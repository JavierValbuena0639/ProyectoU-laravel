<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        .section { margin-top: 16px; }
    </style>
    <title>{{ __('admin.reports_export_title') }}</title>
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
    </head>
<body>
    <h1>{{ __('admin.reports_title') }}</h1>
    <p>{{ __('admin.reports_period') }}: {{ $period }} ({{ $start->toDateString() }} - {{ $end->toDateString() }})</p>

    <div class="section">
        <h2>Métricas</h2>
        <table>
            <tr>
                <th>{{ __('admin.reports_invoices_generated') }}</th>
                <th>{{ __('admin.reports_total_revenue') }}</th>
                <th>{{ __('admin.reports_growth') }}</th>
            </tr>
            <tr>
                <td>{{ $invoicesCount }}</td>
                <td>{{ $revenueSum }}</td>
                <td>{{ $growthPct }}%</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>{{ __('admin.reports_monthly_revenue') }}</h2>
        <table>
            <tr>
                <th>Mes</th>
                <th>Monto</th>
            </tr>
            @foreach($labels as $i => $label)
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $series[$i] }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Actividad por Módulo</h2>
        <table>
            <tr>
                <th>Invoicing</th>
                <th>Accounting</th>
                <th>Payroll</th>
                <th>Quotes</th>
            </tr>
            <tr>
                <td>{{ $invoicesCount }}</td>
                <td>{{ $accountingCount }}</td>
                <td>{{ $payrollCount }}</td>
                <td>{{ $quotesCount }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>{{ __('admin.reports_recent_activity') }}</h2>
        <table>
            <tr>
                <th>Evento</th>
                <th>Usuario</th>
                <th>Fecha</th>
            </tr>
            @foreach($recentAudits as $audit)
            <tr>
                <td>{{ $audit->description ?? $audit->event }}</td>
                <td>{{ optional($audit->user)->name ?? 'Sistema' }}</td>
                <td>{{ $audit->created_at->toDateTimeString() }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>