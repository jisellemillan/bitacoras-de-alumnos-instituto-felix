<?php


namespace App\Livewire\Forms;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserForm extends Form
{
    public ?int $id = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'docente_titular';
    public array $section_permissions = [];
    public bool $is_active = true;
    public array $group_ids = []; //Agregamos esto 1


    public function rules(): array
    {
        $passwordRules = $this->id
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($this->id),
            ],
            'password' => $passwordRules,
            'role' => ['required', Rule::enum(UserRole::class)],
            'section_permissions' => ['array'],
            'is_active' => ['boolean'],
            'group_ids' => ['array'],##
            'group_ids.*' => ['exists:grupos,id'], //agregamos esto 2
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'first_name' => 'nombres',
            'last_name' => 'apellidos',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
            'role' => 'rol del sistema',
            'section_permissions' => 'permisos de sección',
        ];
    }
}
