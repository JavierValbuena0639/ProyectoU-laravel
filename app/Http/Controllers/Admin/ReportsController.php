<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Invoice;
use App\Models\Account;
use App\Models\Payroll;
use App\Models\Audit;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function export(string $format, Request $request)
    {
        $admin = $request->user();
        $domain = $admin ? $admin->emailDomain() : '';

        $period = $request->get('period', 'month');
        $startDateReq = $request->get('start_date');
        $endDateReq = $request->get('end_date');

        $now = Carbon::now();
        if ($period === 'today') {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
        } elseif ($period === 'week') {
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
        } elseif ($period === 'month') {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
        } elseif ($period === 'quarter') {
            $start = $now->copy()->startOfQuarter();
            $end = $now->copy()->endOfQuarter();
        } elseif ($period === 'year') {
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfYear();
        } else { // custom
            $start = $startDateReq ? Carbon::parse($startDateReq)->startOfDay() : $now->copy()->startOfMonth();
            $end = $endDateReq ? Carbon::parse($endDateReq)->endOfDay() : $now->copy()->endOfMonth();
        }

        $diffDays = $start->diffInDays($end) + 1;
        $prevEnd = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($diffDays - 1);

        $invoicesQuery = Invoice::whereHas('user', function($q) use ($domain){
            $q->where('email', 'like', "%@{$domain}");
        });
        $invoicesCount = (clone $invoicesQuery)
            ->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])
            ->count();
        $revenueSum = (clone $invoicesQuery)
            ->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])
            ->sum('total_amount');
        $prevRevenueSum = (clone $invoicesQuery)
            ->whereBetween('invoice_date', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->sum('total_amount');
        $growthPct = $prevRevenueSum > 0 ? round((($revenueSum - $prevRevenueSum) / $prevRevenueSum) * 100, 1) : ($revenueSum > 0 ? 100 : 0);

        $labels = [];
        $series = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthEnd = $end->copy()->subMonths($i)->endOfMonth();
            $monthStart = $monthEnd->copy()->startOfMonth();
            $labels[] = $monthStart->isoFormat('MMM');
            $series[] = (clone $invoicesQuery)
                ->whereBetween('invoice_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('total_amount');
        }

        $accountingCount = Account::forDomain($domain)->count();
        $payrollCount = Payroll::whereHas('user', function($q) use ($domain){
                $q->where('email', 'like', "%@{$domain}");
            })
            ->whereBetween('payroll_period_start', [$start->toDateString(), $end->toDateString()])
            ->count();
        $quotesCount = 0; // No quotes model

        $recentAudits = Audit::with('user')
            ->whereHas('user', function($q) use ($domain) {
                $q->where('email', 'like', '%@' . $domain);
            })
            ->latest()
            ->take(20)
            ->get();

        $data = compact(
            'period','start','end','invoicesCount','revenueSum','growthPct','labels','series',
            'accountingCount','payrollCount','quotesCount','recentAudits'
        );

        if ($format === 'csv') {
            return $this->exportCsv($data);
        } elseif ($format === 'excel') {
            return $this->exportExcel($data);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($data);
        }

        abort(404);
    }

    protected function exportCsv(array $data)
    {
        $response = new StreamedResponse(function() use ($data) {
            $out = fopen('php://output', 'w');
            // Section: Metrics
            fputcsv($out, ['Metrics']);
            // Active users estimation via recent audits unique user emails
            $activeUsers = $data['recentAudits']->pluck('user.email')->filter()->unique()->count();
            fputcsv($out, ['Active Users', $activeUsers]);
            fputcsv($out, ['Invoices', 'Revenue', 'Growth %']);
            fputcsv($out, [$data['invoicesCount'], $data['revenueSum'], $data['growthPct']]);

            fputcsv($out, []);
            fputcsv($out, ['Monthly Revenue']);
            fputcsv($out, ['Month', 'Amount']);
            foreach ($data['labels'] as $i => $label) {
                fputcsv($out, [$label, $data['series'][$i]]);
            }

            fputcsv($out, []);
            fputcsv($out, ['Module Activity Counts']);
            fputcsv($out, ['Invoicing', 'Accounting', 'Payroll', 'Quotes']);
            fputcsv($out, [$data['invoicesCount'], $data['accountingCount'], $data['payrollCount'], $data['quotesCount']]);

            fputcsv($out, []);
            fputcsv($out, ['Recent Activity']);
            fputcsv($out, ['Event', 'User', 'Date']);
            foreach ($data['recentAudits'] as $audit) {
                fputcsv($out, [
                    $audit->description ?? $audit->event,
                    optional($audit->user)->name ?? 'Sistema',
                    $audit->created_at->toDateTimeString(),
                ]);
            }
            fclose($out);
        });
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="reports.csv"');
        return $response;
    }

    protected function exportExcel(array $data)
    {
        // Use HTML table with Excel MIME so Excel opens it
        $html = view('admin.reports-export-excel', $data)->render();
        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="reports.xls"');
    }

    protected function exportPdf(array $data)
    {
        // Prefer Dompdf if available
        if (class_exists('Dompdf\\Dompdf')) {
            $html = view('admin.reports-export-pdf', $data)->render();
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            return response($dompdf->output())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="reports.pdf"');
        }

        // Fallback: Simple text-based PDF
        $lines = [
            __('admin.reports_title'),
            __('admin.reports_period') . ': ' . $data['period'] . ' (' . $data['start']->toDateString() . ' - ' . $data['end']->toDateString() . ')',
            '',
            'Métricas:',
            __('admin.reports_invoices_generated') . ': ' . $data['invoicesCount'],
            __('admin.reports_total_revenue') . ': ' . $data['revenueSum'],
            __('admin.reports_growth') . ': ' . $data['growthPct'] . '%',
            '',
            __('admin.reports_monthly_revenue') . ':',
        ];
        foreach ($data['labels'] as $i => $label) {
            $lines[] = $label . ': ' . $data['series'][$i];
        }
        $lines[] = '';
        $lines[] = 'Actividad por módulo:';
        $lines[] = 'Invoicing: ' . $data['invoicesCount'];
        $lines[] = 'Accounting: ' . $data['accountingCount'];
        $lines[] = 'Payroll: ' . $data['payrollCount'];
        $lines[] = 'Quotes: ' . $data['quotesCount'];
        $lines[] = '';
        $lines[] = __('admin.reports_recent_activity') . ':';
        foreach ($data['recentAudits'] as $audit) {
            $lines[] = ($audit->description ?? $audit->event) . ' - ' . (optional($audit->user)->name ?? 'Sistema') . ' - ' . $audit->created_at->toDateTimeString();
        }

        $pdf = (new \App\Support\SimplePdf())->render($lines);
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="reports.pdf"');
    }
}