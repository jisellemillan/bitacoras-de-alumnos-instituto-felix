<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AccessGrant;

class CheckSectionAccess
{
    public function handle(Request $request, Closure $next, string ...$sections): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // 1. El administrador siempre tiene acceso ilimitado
        if ($user->isAdminTecnico()) {
            return $next($request);
        }

        // 2. Extraer los IDs si la URL tiene un registro específico (ej. /alumnos/1 o /grupos/5)
        $student = $request->route('student');
        $group = $request->route('group');
        $studentId = is_object($student) ? $student->id : $student;
        $groupId = is_object($group) ? $group->id : $group;

        // 3. CANDADO DE PROPIEDAD ESTRICTA (Protege las URL con ID)
        if ($groupId || $studentId) {
            $accesoPermitido = false;

            // Verificar si tiene derecho a ver este GRUPO
            if ($groupId) {
                $isOwner = $user->groups()->where('grupos.id', $groupId)->exists();
                $hasGrant = AccessGrant::where('user_id', $user->id)
                    ->where('group_id', $groupId)->where('is_active', true)->exists();
                
                if ($isOwner || $hasGrant) {
                    $accesoPermitido = true;
                }
            }

            // Verificar si tiene derecho a ver este ALUMNO
            if ($studentId) {
                $isMyStudent = $user->groups()->whereHas('students', function ($q) use ($studentId) {
                    $q->where('alumnos.id', $studentId)->where('inscripciones.is_current', true);
                })->exists();
                $hasGrant = AccessGrant::where('user_id', $user->id)
                    ->where('student_id', $studentId)->where('is_active', true)->exists();
                
                if ($isMyStudent || $hasGrant) {
                    $accesoPermitido = true;
                }
            }

            // EXCEPCIÓN: Psicopedagogía tiene acceso libre para ver expedientes porque ellos aprueban y gestionan.
            // Si NO se permitió el acceso y NO es Psicopedagogía, se bloquea definitivamente.
            if (!$accesoPermitido && !$user->isPsicopedagogia()) {
                abort(403, "Acceso denegado. No tienes asignado este registro ni cuentas con una solicitud de acceso aprobada para verlo.");
            }

            // Si pasó las pruebas de propiedad/permiso, lo dejamos entrar sin necesidad de revisar sus casillas estáticas.
            if ($accesoPermitido || $user->isPsicopedagogia()) {
                return $next($request);
            }
        }

        // 4. EVALUACIÓN DE PERMISOS ESTÁTICOS (Protege las URL generales como el menú principal /alumnos)
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