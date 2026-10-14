<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSectionAccess
{
    public function handle(Request $request, Closure $next, string ...$sections): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // El administrador siempre tiene acceso completo
        if ($user->isAdminTecnico()) {
            return $next($request);
        }

        // Verificar si el usuario tiene permiso en al menos una de las secciones requeridas
        $requiredSections = [];
        foreach ($sections as $s) {
            foreach (explode(',', $s) as $sub) {
                $trimmed = trim($sub);
                if ($trimmed !== '') {
                    $requiredSections[] = $trimmed;
                }
            }
        }

        foreach ($requiredSections as $sec) {
            if ($user->hasSectionPermission($sec)) {
                return $next($request);
            }
        }

        $sectionLabels = implode(' o ', $requiredSections);
        abort(403, "No cuentas con autorización para acceder a la sección: {$sectionLabels}. Solicita acceso al administrador.");
    }
}
