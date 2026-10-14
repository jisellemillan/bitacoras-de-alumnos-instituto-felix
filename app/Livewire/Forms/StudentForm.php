<?php

namespace App\Livewire\Forms;

use App\Enums\IntakeRoute;
use App\Enums\NeedArea;
use App\Enums\StudentStatus;
use Livewire\Form;

class StudentForm extends Form
{
    public ?int $id = null;
    public string $first_name = '';
    public string $last_name = '';
    public ?string $birthdate = null;
    public ?string $sex = null;
    public ?string $curp = null;
    public ?int $group_id = null;

    // Campos del expediente digital (Rutas A, B, C y clínicas)
    public ?string $intake_route = 'A';
    public ?string $status = 'activo';
    public array $need_areas = [];
    public ?string $primary_diagnosis = '';
    public ?string $strengths = '';
    public ?string $support_areas = '';
    public ?string $external_specialist = '';
    public ?string $treatment_medication = '';

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birthdate' => ['nullable', 'date'],
            'sex' => ['nullable', 'in:masculino,femenino,otro'],
            'curp' => ['nullable', 'string', 'max:18', 'regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}$/i'],
            'group_id' => ['required', 'exists:grupos,id'],
            'intake_route' => ['nullable', 'in:A,B,C'],
            'status' => ['nullable', 'in:activo,en_seguimiento,cerrado'],
            'need_areas' => ['array'],
            'primary_diagnosis' => ['nullable', 'string', 'max:500'],
            'strengths' => ['nullable', 'string'],
            'support_areas' => ['nullable', 'string'],
            'external_specialist' => ['nullable', 'string', 'max:255'],
            'treatment_medication' => ['nullable', 'string'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'first_name' => 'nombre(s)',
            'last_name' => 'apellidos',
            'birthdate' => 'fecha de nacimiento',
            'sex' => 'sexo',
            'curp' => 'CURP',
            'group_id' => 'grado y grupo',
            'intake_route' => 'ruta de ingreso',
            'primary_diagnosis' => 'diagnóstico principal',
            'need_areas' => 'áreas de necesidad',
        ];
    }
}
