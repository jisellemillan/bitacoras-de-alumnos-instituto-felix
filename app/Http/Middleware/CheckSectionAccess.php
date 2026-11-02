<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AccessGrant; // Necesario para consultar los permisos aprobados

class CheckSectionAccess
{
    public function handle(Request $request, Closure $next, string ...$sections): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // 1. El administrador siempre tiene acceso completo
        if ($user->isAdminTecnico()) {
            return $next($request);
        }

        // Preparar las secciones requeridas
        $requiredSections = [];
        foreach ($sections as $s) {
            foreach (explode(',', $s) as $sub) {
                $trimmed = trim($sub);
                if ($trimmed !== '') {
                    $requiredSections[] = $trimmed;
                }
            }
        }

        // 2. Verificar si el usuario tiene permiso en al menos una de las secciones requeridas
        foreach ($requiredSections as $sec) {
            if ($user->hasSectionPermission($sec)) {
                return $next($request);
            }
        }

        // 3. NUEVA REVISIÓN: Verificar permiso individual aprobado (AccessGrant)
        $student = $request->route('student');
        $group = $request->route('group');

        // Extraer el ID dependiendo si Laravel inyectó el modelo completo o solo el número
        $studentId = is_object($student) ? $student->id : $student;
        $groupId = is_object($group) ? $group->id : $group;

        if ($studentId) {
            $hasStudentGrant = AccessGrant::where('user_id', $user->id)
                ->where('student_id', $studentId)
                ->where('is_active', true)
                ->exists();
            
            if ($hasStudentGrant) {
                return $next($request);
            }
        }

        if ($groupId) {
            $hasGroupGrant = AccessGrant::where('user_id', $user->id)
                ->where('group_id', $groupId)
                ->where('is_active', true)
                ->exists();
            
            if ($hasGroupGrant) {
                return $next($request);
            }
        }

        // 4. Si no tiene permiso global ni permiso individual aprobado, se bloquea.
        $sectionLabels = implode(' o ', $requiredSections);
        abort(403, "No cuentas con autorización para acceder a la sección: {$sectionLabels}. Solicita acceso al administrador.");
    }
}