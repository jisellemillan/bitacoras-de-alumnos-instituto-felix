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

// 1. El administrador técnico tiene acceso a configuración general, usuarios y accesos,
        // PERO NO a módulos confidenciales (acuerdos, calendario, alumnos, reportes).
        if ($user->isAdminTecnico()) {
            $student = $request->route('student');
            $group = $request->route('group');
            
            $reqSections = [];
            foreach ($sections as $s) {
                foreach (explode(',', $s) as $sub) {
                    $reqSections[] = trim($sub);
                }
            }

            // A) Bloqueo por ID: Si la URL intenta acceder al expediente confidencial de un alumno (ej. /alumnos/1)
            if ($student) {
                abort(403, 'Acceso denegado. Los expedientes psicopedagógicos individuales de los alumnos son confidenciales.');
            }

            // B) Bloqueo por Sección: Solo lo dejamos pasar si la ruta está etiquetada 
            // como 'configuracion', 'usuarios' o 'solicitudes_acceso' en web.php
            $allowedForTech = ['configuracion', 'usuarios', 'solicitudes_acceso'];
            $hasAllowedSection = count(array_intersect($reqSections, $allowedForTech)) > 0;

            if (!$hasAllowedSection) {
                abort(403, 'Acceso denegado por confidencialidad. Tu perfil técnico no tiene autorización para ingresar a las áreas de control psicopedagógico.');
            }

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

            // Dirección y Coordinación Académica tienen visibilidad total sobre grupos y alumnos
            $rolesConAccesoTotal = [
                \App\Enums\UserRole::Direccion,
                \App\Enums\UserRole::CoordinacionAcademica,
                \App\Enums\UserRole::Psicopedagogia,
            ];
            if (in_array($user->role, $rolesConAccesoTotal)) {
                return $next($request);
            }

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

            // Si NO se permitió el acceso (no es propietario ni tiene grant), se bloquea definitivamente.
            if (!$accesoPermitido) {
                abort(403, "Acceso denegado. No tienes asignado este registro ni cuentas con una solicitud de acceso aprobada para verlo.");
            }

            return $next($request);
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