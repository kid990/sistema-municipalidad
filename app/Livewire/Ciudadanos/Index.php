<?php

namespace App\Livewire\Ciudadanos;

use App\Exports\CiudadanosExport;
use App\Livewire\Concerns\HandlesCrud;
use App\Models\Ciudadano;
use App\Services\ApiReniecService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Ciudadanos')]
class Index extends Component
{
    use HandlesCrud;

    public $validatingDni = false;
    public $dniValidationMessage = '';
    public $dniValidationStatus = null;
    public $dniValidated = false;

    protected function getModel(): string
    {
        return Ciudadano::class;
    }

    protected function getFormDefaults(): array
    {
        return [
            'dni' => '',
            'nombres' => '',
            'ape_paterno' => '',
            'ape_materno' => '',
            'fecha_nacimiento' => '',
            'genero' => '',
            'telefono' => '',
            'email' => '',
            'direccion_referencia' => '',
            'estado' => 'Activo',
        ];
    }

    protected function getFormDataForEdit($record): array
    {
        return [
            'dni' => $record->dni,
            'nombres' => $record->nombres,
            'ape_paterno' => $record->ape_paterno,
            'ape_materno' => $record->ape_materno,
            'fecha_nacimiento' => $record->fecha_nacimiento ? $record->fecha_nacimiento->format('Y-m-d') : '',
            'genero' => $record->genero ?? '',
            'telefono' => $record->telefono ?? '',
            'email' => $record->email ?? '',
            'direccion_referencia' => $record->direccion_referencia ?? '',
            'estado' => $record->estado ?? 'Activo',
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'form.dni' => $this->editingId
                ? 'required|digits:8|unique:ciudadanos,dni,'.$this->editingId
                : 'required|digits:8|unique:ciudadanos,dni',
            'form.nombres' => 'required|string|max:100',
            'form.ape_paterno' => 'required|string|max:100',
            'form.ape_materno' => 'required|string|max:100',
            'form.fecha_nacimiento' => 'nullable|date',
            'form.genero' => 'nullable|in:M,F,Otro',
            'form.telefono' => 'nullable|string|max:15',
            'form.email' => 'nullable|email|max:150',
            'form.direccion_referencia' => 'nullable|string',
            'form.estado' => 'required|in:Activo,Inactivo',
        ];
    }

    protected function saveRecord(array $data): void
    {
        if ($this->editingId) {
            $ciudadano = Ciudadano::find($this->editingId);
            $ciudadano->update($data);
            $this->dispatch('ciudadano-updated', message: 'Ciudadano actualizado exitosamente');
        } else {
            Ciudadano::create($data);
            $this->dispatch('ciudadano-created', message: 'Ciudadano creado exitosamente');
        }
    }

    protected function deleteRecord($id): void
    {
        $ciudadano = Ciudadano::findOrFail($id);
        abort_unless(auth()->user()->hasAnyRole(['admin', 'registrador']), 403);
        $ciudadano->delete();
        $this->dispatch('ciudadano-deleted', message: 'Ciudadano eliminado exitosamente');
    }

    protected function getModelIndex()
    {
        return Ciudadano::where('dni', 'like', '%'.$this->search.'%')
            ->orWhere('nombres', 'like', '%'.$this->search.'%')
            ->orWhere('ape_paterno', 'like', '%'.$this->search.'%')
            ->orWhere('ape_materno', 'like', '%'.$this->search.'%')
            ->orWhere('email', 'like', '%'.$this->search.'%')
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    protected function getViewName(): string
    {
        return 'livewire.ciudadanos.index';
    }

    public function render()
    {
        return view($this->getViewName(), [
            'ciudadanos' => $this->getModelIndex(),
        ]);
    }

    public function descargarExcel()
    {
        return Excel::download(new CiudadanosExport, 'ciudadanos_'.now()->format('Y-m-d_H-i-s').'.xlsx');
    }

    public function validarDniConReniec(): void
    {
        if (empty($this->form['dni'])) {
            $this->dniValidationMessage = 'Por favor ingresa un DNI';
            $this->dniValidationStatus = 'error';
            return;
        }

        if (! preg_match('/^\d{8}$/', $this->form['dni'])) {
            $this->dniValidationMessage = 'El DNI debe ser de 8 dígitos';
            $this->dniValidationStatus = 'error';
            return;
        }

        $this->validatingDni = true;
        $this->dniValidationMessage = 'Validando DNI...';
        $this->dniValidationStatus = null;

        try {
            $service = new ApiReniecService;
            $result = $service->consultarDni($this->form['dni']);

            if ($result['success'] && $result['data']) {
                $nombres = $result['data']['nombres'] ?? '';
                $ape_paterno = $result['data']['ape_paterno'] ?? '';
                $ape_materno = $result['data']['ape_materno'] ?? '';
                $fecha_nacimiento = !empty($result['data']['fecha_nacimiento']) ? $result['data']['fecha_nacimiento'] : $this->form['fecha_nacimiento'] ?? '';

                $this->form = array_merge($this->form, [
                    'nombres' => $nombres,
                    'ape_paterno' => $ape_paterno,
                    'ape_materno' => $ape_materno,
                    'fecha_nacimiento' => $fecha_nacimiento,
                ]);

                $this->dniValidationMessage = 'DNI validado correctamente. Datos completados automáticamente.';
                $this->dniValidationStatus = 'success';
                $this->dniValidated = true;
            } else {
                $this->dniValidationMessage = $result['message'] ?? 'No se encontró el DNI en RENIEC';
                $this->dniValidationStatus = 'error';
                $this->dniValidated = false;
            }
        } catch (\Exception $e) {
            $this->dniValidationMessage = 'Error al validar DNI: '.$e->getMessage();
            $this->dniValidationStatus = 'error';
            $this->dniValidated = false;
        } finally {
            $this->validatingDni = false;
        }
    }
}
