<?php

namespace App\Livewire\SchoolCycles;

use App\Models\SchoolCycle;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SchoolCyclesIndex extends Component
{
    // ─── Modal / formulario ──────────────────────────────────────────────────

    public bool   $showModal     = false;
    public bool   $isEditing     = false;
    public ?int   $editingId     = null;

    public string $year          = '';
    public string $period_number = '1';
    public string $starts_at     = '';
    public string $ends_at       = '';
    public bool   $is_active     = false;

    /** Nombre compuesto en tiempo real, ej. "2026-1" */
    public function getComputedNameProperty(): string
    {
        if ($this->year === '') {
            return '';
        }
        return $this->year . '-' . $this->period_number;
    }

    // ─── Reglas de validación ────────────────────────────────────────────────

    protected function rules(): array
    {
        $uniqueRule = 'unique:ciclos_escolares,name';
        if ($this->isEditing && $this->editingId) {
            $uniqueRule .= ',' . $this->editingId;
        }

        return [
            'year'          => ['required', 'digits:4', 'integer', 'min:2000', 'max:2100'],
            'period_number' => ['required', 'in:1,2,3'],
            'starts_at'     => ['nullable', 'date'],
            'ends_at'       => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'     => ['boolean'],
        ];
    }

    protected array $messages = [
        'year.required'          => 'El año es obligatorio.',
        'year.digits'            => 'El año debe tener 4 dígitos.',
        'year.min'               => 'El año debe ser 2000 o posterior.',
        'year.max'               => 'El año no puede superar 2100.',
        'period_number.required' => 'Selecciona el número de periodo.',
        'period_number.in'       => 'El periodo debe ser 1, 2 o 3.',
        'ends_at.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la de inicio.',
    ];

    // ─── Abrir modal de creación ─────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->year = (string) now()->year;
        $this->isEditing = false;
        $this->showModal = true;
    }

    // ─── Abrir modal de edición ──────────────────────────────────────────────

    public function openEditModal(int $id): void
    {
        $cycle = SchoolCycle::findOrFail($id);

        $this->resetForm();
        $this->isEditing = true;
        $this->editingId = $id;

        // Descomponer "2026-1" en año y número
        $parts = explode('-', $cycle->name, 2);
        $this->year          = $parts[0] ?? (string) now()->year;
        $this->period_number = $parts[1] ?? '1';

        $this->starts_at = $cycle->starts_at?->format('Y-m-d') ?? '';
        $this->ends_at   = $cycle->ends_at?->format('Y-m-d') ?? '';
        $this->is_active = $cycle->is_active;
        $this->showModal = true;
    }

    // ─── Guardar (crear o editar) ────────────────────────────────────────────

    public function save(): void
    {
        $this->validate();

        $composedName = $this->computedName; // ej. "2026-1"

        // Verificar unicidad del nombre compuesto manualmente
        $exists = \App\Models\SchoolCycle::where('name', $composedName)
            ->when($this->isEditing, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($exists) {
            $this->addError('year', "Ya existe el periodo {$composedName}.");
            return;
        }

        $data = [
            'name'      => $composedName,
            'starts_at' => $this->starts_at ?: null,
            'ends_at'   => $this->ends_at   ?: null,
            'is_active' => $this->is_active,
        ];

        DB::transaction(function () use ($data) {
            if ($this->isEditing) {
                $cycle = SchoolCycle::findOrFail($this->editingId);

                // Si se activa este ciclo, desactivar los demás primero
                if ($data['is_active'] && ! $cycle->is_active) {
                    SchoolCycle::where('id', '!=', $cycle->id)->update(['is_active' => false]);
                }

                $cycle->update($data);
            } else {
                // Si el nuevo ciclo se crea como activo, desactivar los demás
                if (! empty($data['is_active'])) {
                    SchoolCycle::query()->update(['is_active' => false]);
                }

                SchoolCycle::create($data);
            }
        });

        $wasEditing = $this->isEditing;
        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', [
            'type'    => 'success',
            'title'   => 'Gestión de Periodos',
            'message' => $wasEditing ? 'Periodo actualizado correctamente.' : 'Periodo creado correctamente.',
        ]);
    }

    // ─── Activar un periodo ──────────────────────────────────────────────────

    public function activateCycle(int $id): void
    {
        DB::transaction(function () use ($id) {
            SchoolCycle::query()->update(['is_active' => false]);
            SchoolCycle::findOrFail($id)->update(['is_active' => true]);
        });

        $this->dispatch('toast', [
            'type'    => 'success',
            'title'   => 'Gestión de Periodos',
            'message' => 'Periodo activado. Ahora es el ciclo escolar activo.',
        ]);
    }

    // ─── Desactivar un periodo ───────────────────────────────────────────────

    public function deactivateCycle(int $id): void
    {
        $cycle = SchoolCycle::findOrFail($id);

        if (! $cycle->is_active) {
            return;
        }

        $cycle->update(['is_active' => false]);

        $this->dispatch('toast', [
            'type'    => 'info',
            'title'   => 'Gestión de Periodos',
            'message' => 'El periodo fue desactivado.',
        ]);
    }

    // ─── Eliminar un periodo ─────────────────────────────────────────────────

    public function deleteCycle(int $id): void
    {
        $cycle = SchoolCycle::findOrFail($id);

        // Bloquear si está activo
        if ($cycle->is_active) {
            $this->dispatch('toast', [
                'type'    => 'error',
                'title'   => 'No se puede eliminar',
                'message' => 'No puedes eliminar el periodo activo. Activa otro periodo primero.',
            ]);
            return;
        }

        // Bloquear si tiene registros asociados
        $hasRecords = $cycle->groups()->exists()
            || $cycle->enrollments()->exists()
            || $this->cycleHasObservations($id)
            || $this->cycleHasInterviews($id);

        if ($hasRecords) {
            $this->dispatch('toast', [
                'type'    => 'error',
                'title'   => 'No se puede eliminar',
                'message' => 'Existen grupos, inscripciones u otros registros asociados a este periodo.',
            ]);
            return;
        }

        try {
            $cycle->delete();
            $this->dispatch('toast', [
                'type'    => 'warning',
                'title'   => 'Periodo eliminado',
                'message' => 'El periodo fue eliminado correctamente.',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('toast', [
                'type'    => 'error',
                'title'   => 'No se pudo eliminar',
                'message' => 'Existen registros vinculados a este periodo.',
            ]);
        }
    }

    // ─── Helpers para verificar registros ───────────────────────────────────

    private function cycleHasObservations(int $id): bool
    {
        return DB::table('observaciones')->where('school_cycle_id', $id)->exists();
    }

    private function cycleHasInterviews(int $id): bool
    {
        return DB::table('entrevistas')->where('school_cycle_id', $id)->exists();
    }

    // ─── Reset ───────────────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->year          = '';
        $this->period_number = '1';
        $this->starts_at     = '';
        $this->ends_at       = '';
        $this->is_active     = false;
        $this->editingId     = null;
        $this->resetValidation();
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    public function render()
    {
        $cycles = SchoolCycle::orderByDesc('is_active')
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->get();

        return view('livewire.school-cycles.school-cycles-index', compact('cycles'))
            ->title('Gestión de Periodos');
    }
}
