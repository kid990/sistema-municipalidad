<?php

namespace App\Livewire\Gestiones;

use App\Livewire\Concerns\HandlesCrud;
use App\Models\Gestion;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gestiones')]
class Index extends Component
{
    use HandlesCrud;

    public $years = [];

    public function mount(): void
    {
        $this->form = $this->getFormDefaults();
        $currentYear = (int) now()->format('Y');
        $this->years = range($currentYear - 10, $currentYear + 10);
    }

    protected function getModel(): string
    {
        return Gestion::class;
    }

    protected function getFormDefaults(): array
    {
        $currentYear = (int) now()->format('Y');

        return [
            'nombre_gestion' => '',
            'anio_inicio' => $currentYear,
            'anio_fin' => $currentYear,
            'estado_gestion' => true,
        ];
    }

    protected function getFormDataForEdit($record): array
    {
        return [
            'nombre_gestion' => $record->nombre_gestion,
            'anio_inicio' => $record->fecha_inicio ? (int) $record->fecha_inicio->format('Y') : (int) now()->format('Y'),
            'anio_fin' => $record->fecha_fin ? (int) $record->fecha_fin->format('Y') : null,
            'estado_gestion' => (bool) $record->estado_gestion,
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'form.nombre_gestion' => 'required|string|max:100',
            'form.anio_inicio' => 'required|integer|min:1900|max:2100',
            'form.anio_fin' => 'nullable|integer|min:1900|max:2100|gte:form.anio_inicio',
            'form.estado_gestion' => 'required|boolean',
        ];
    }

    protected function saveRecord(array $data): void
    {
        $data['fecha_inicio'] = $data['anio_inicio'].'-01-01';
        $data['fecha_fin'] = $data['anio_fin'] ? $data['anio_fin'].'-12-31' : null;
        unset($data['anio_inicio'], $data['anio_fin']);

        if ($this->editingId) {
            $gestion = Gestion::find($this->editingId);
            $gestion->update($data);
            $this->dispatch('gestion-updated', message: 'Gestión actualizada exitosamente');
        } else {
            Gestion::create($data);
            $this->dispatch('gestion-created', message: 'Gestión creada exitosamente');
        }
    }

    protected function deleteRecord($id): void
    {
        $gestion = Gestion::findOrFail($id);
        abort_unless(auth()->user()->hasAnyRole(['admin']), 403);
        $gestion->delete();
        $this->dispatch('gestion-deleted', message: 'Gestión eliminada exitosamente');
    }

    protected function getModelIndex()
    {
        return Gestion::where('nombre_gestion', 'like', '%'.$this->search.'%')
            ->orderByDesc('fecha_inicio')
            ->paginate($this->perPage);
    }

    protected function getViewName(): string
    {
        return 'livewire.gestiones.index';
    }

    public function render()
    {
        return view($this->getViewName(), [
            'gestiones' => $this->getModelIndex(),
        ]);
    }
}
