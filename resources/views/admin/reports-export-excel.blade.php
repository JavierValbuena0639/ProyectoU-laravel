<table border="1">
    <tr>
        <th colspan="4">{{ __('admin.reports_export_title') }}</th>
    </tr>
    <tr>
        <th>{{ __('admin.reports_invoices_generated') }}</th>
        <th>{{ __('admin.reports_total_revenue') }}</th>
        <th>{{ __('admin.reports_growth') }}</th>
        <th>{{ __('admin.reports_period') }}</th>
    </tr>
    <tr>
        <td>{{ $invoicesCount }}</td>
        <td>{{ $revenueSum }}</td>
        <td>{{ $growthPct }}%</td>
        <td>{{ $period }} ({{ $start->toDateString() }} - {{ $end->toDateString() }})</td>
    </tr>

    <tr>
        <th colspan="4">{{ __('admin.reports_monthly_revenue') }}</th>
    </tr>
    <tr>
        <th>Mes</th>
        <th colspan="3">Monto</th>
    </tr>
    @foreach($labels as $i => $label)
    <tr>
        <td>{{ $label }}</td>
        <td colspan="3">{{ $series[$i] }}</td>
    </tr>
    @endforeach

    <tr>
        <th colspan="4">{{ __('admin.reports_module_activity') }}</th>
    </tr>
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

    <tr>
        <th colspan="4">{{ __('admin.reports_recent_activity') }}</th>
    </tr>
    <tr>
        <th>Evento</th>
        <th>Usuario</th>
        <th colspan="2">Fecha</th>
    </tr>
    @foreach($recentAudits as $audit)
    <tr>
        <td>{{ $audit->description ?? $audit->event }}</td>
        <td>{{ optional($audit->user)->name ?? 'Sistema' }}</td>
        <td colspan="2">{{ $audit->created_at->toDateTimeString() }}</td>
    </tr>
    @endforeach
</table>