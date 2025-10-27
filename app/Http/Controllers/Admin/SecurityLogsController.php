<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Audit;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecurityLogsController extends Controller
{
    /**
     * Mostrar vista de logs con filtros.
     */
    public function index(Request $request)
    {
        $query = Audit::with('user')->latest();

        if ($event = $request->get('event')) {
            $query->where('event', $event);
        }
        if ($user = $request->get('user')) {
            // filtrar por email del actor o del auditable
            $query->whereHas('user', function($q) use ($user) {
                $q->where('email', 'like', '%'.$user.'%');
            });
        }
        if ($ip = $request->get('ip')) {
            $query->where('ip_address', 'like', '%'.$ip.'%');
        }
        if ($start = $request->get('start_date')) {
            $query->where('created_at', '>=', $start.' 00:00:00');
        }
        if ($end = $request->get('end_date')) {
            $query->where('created_at', '<=', $end.' 23:59:59');
        }

        $logs = $query->paginate(50)->appends($request->query());
        $events = [
            'login_success','login_failed','login_blocked','email_verification_success','twofa_success','role_changed'
        ];
        return view('admin.security-logs', compact('logs','events'));
    }

    /**
     * Exportar CSV con filtros aplicados.
     */
    public function exportCsv(Request $request)
    {
        $query = Audit::with('user')->latest();

        if ($event = $request->get('event')) {
            $query->where('event', $event);
        }
        if ($user = $request->get('user')) {
            $query->whereHas('user', function($q) use ($user) {
                $q->where('email', 'like', '%'.$user.'%');
            });
        }
        if ($ip = $request->get('ip')) {
            $query->where('ip_address', 'like', '%'.$ip.'%');
        }
        if ($start = $request->get('start_date')) {
            $query->where('created_at', '>=', $start.' 00:00:00');
        }
        if ($end = $request->get('end_date')) {
            $query->where('created_at', '<=', $end.' 23:59:59');
        }

        $response = new StreamedResponse(function() use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Event','Description','User','IP','User Agent','URL','Fecha']);
            $query->chunk(200, function($chunk) use ($out) {
                foreach ($chunk as $audit) {
                    fputcsv($out, [
                        $audit->event,
                        $audit->description,
                        optional($audit->user)->email ?? 'Sistema',
                        $audit->ip_address,
                        $audit->user_agent,
                        $audit->url,
                        $audit->created_at->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        });
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="security-logs.csv"');
        return $response;
    }
}