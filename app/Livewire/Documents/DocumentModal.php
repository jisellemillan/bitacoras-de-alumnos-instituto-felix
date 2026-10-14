<?php

namespace App\Livewire\Documents;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentModal extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public bool $show = false;
    public bool $isEditing = false;
    public ?int $documentId = null;
    public ?int $studentId = null;

    // Campos del documento
    public string $title = '';
    public string $description = '';
    public string $category = 'dictamen';
    public $file = null; // uploaded file

    // Categorías disponibles
    public array $categories = [
        'dictamen'      => 'Dictamen médico/psicológico',
        'evaluacion'    => 'Evaluación externa',
        'consentimiento'=> 'Carta de consentimiento',
        'plan_apoyo'    => 'Plan de apoyo educativo',
        'informe'       => 'Informe de seguimiento',
        'otro'          => 'Otro documento',
    ];

    protected $listeners = [
        'open-document-modal' => 'openModal',
        'open-document-edit'  => 'openEditModal',
    ];

    public function rules(): array
    {
        $fileRules = $this->isEditing
            ? ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx']
            : ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'];

        return [
            'title'       => ['required', 'string', 'min:3', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'category'    => ['required', 'string'],
            'file'        => $fileRules,
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'title'    => 'título del documento',
            'category' => 'categoría',
            'file'     => 'archivo',
        ];
    }

    public function openModal(int $studentId): void
    {
        $this->reset();
        $this->studentId = $studentId;
        $this->category  = 'dictamen';
        $this->show      = true;
    }

    public function openEditModal(int $documentId): void
    {
        $doc = Document::findOrFail($documentId);

        $this->reset();
        $this->isEditing   = true;
        $this->documentId  = $doc->id;
        $this->studentId   = $doc->student_id;
        $this->title       = $doc->title;
        $this->description = $doc->description ?? '';
        $this->category    = $doc->category ?? 'dictamen';

        $this->show = true;
    }

    public function save(): void
    {
        $this->validate();
        $student = Student::with('studentFile')->findOrFail($this->studentId);

        if ($this->isEditing) {
            $doc = Document::findOrFail($this->documentId);

            $data = [
                'title'       => $this->title,
                'description' => $this->description ?: null,
                'category'    => $this->category,
            ];

            // Si se subió nuevo archivo, reemplazar el anterior
            if ($this->file) {
                if ($doc->file_path && Storage::disk('private')->exists($doc->file_path)) {
                    Storage::disk('private')->delete($doc->file_path);
                }
                $path = $this->file->storeAs(
                    "documentos/{$student->id}",
                    time() . '_' . $this->file->getClientOriginalName(),
                    'private'
                );
                $data['file_path']  = $path;
                $data['mime_type']  = $this->file->getMimeType();
                $data['file_size']  = $this->file->getSize();
            }

            $doc->update($data);
            $action  = 'updated';
            $label   = "Actualización de documento de {$student->full_name}";
            $message = 'Documento actualizado con éxito.';
        } else {
            $path = $this->file->storeAs(
                "documentos/{$student->id}",
                time() . '_' . $this->file->getClientOriginalName(),
                'private'
            );

            $doc = Document::create([
                'student_file_id' => $student->studentFile->id,
                'student_id'      => $student->id,
                'uploaded_by'     => auth()->id(),
                'title'           => $this->title,
                'description'     => $this->description ?: null,
                'category'        => $this->category,
                'file_path'       => $path,
                'mime_type'       => $this->file->getMimeType(),
                'file_size'       => $this->file->getSize(),
            ]);

            $action  = 'created';
            $label   = "Carga de documento para {$student->full_name}";
            $message = 'Documento subido exitosamente.';
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'action'          => $action,
            'auditable_type'  => Document::class,
            'auditable_id'    => $doc->id,
            'auditable_label' => $label,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);

        $this->show = false;
        $this->dispatch('document-saved');
        $this->dispatch('toast', [
            'type'    => 'success',
            'title'   => 'Documento',
            'message' => $message,
        ]);
    }

    public function render()
    {
        return view('livewire.documents.document-modal');
    }
}
