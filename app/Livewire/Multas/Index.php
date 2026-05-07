<?php

namespace App\Livewire\Multas;

use App\Livewire\Concerns\HandlesCrud;
use App\Models\Comunero;
use App\Models\Multa;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Multas')]
class Index extends Component
{
    use HandlesCrud;

    public $searchCiudadano = '';
    public $selectedComuneroNombre = '';
    public $showSearchResults = false;
    public $isSearching = false;

    public function mount(): void
    {
        $this->form = $this->getFormDefaults();
    }

    #[\Livewire\Attributes\On('resetForm')]
    public function resetForm(): void
    {
        $this->selectedComuneroNombre = '';
        $this->searchCiudadano = '';
        $this->showSearchResults = false;
        $this->isSearching = false;
    }

    protected function getModel(): string
    {
        return Multa::class;
    }

    protected function getFormDefaults(): array
    {
        return [
            'comunero_id' => '',
            'monto' => '',
            'motivo' => '',
            'fecha_emision' => now()->toDateString(),
            'estado_pago' => false,
        ];
    }

    protected function getFormDataForEdit($record): array
    {
        return [
            'comunero_id' => $record->comunero_id,
            'monto' => $record->monto,
            'motivo' => $record->motivo ?? '',
            'fecha_emision' => $record->fecha_emision ? $record->fecha_emision->format('Y-m-d') : now()->toDateString(),
            'estado_pago' => (bool) $record->estado_pago,
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'form.comunero_id' => 'required|exists:comuneros,id',
            'form.monto' => 'required|numeric|min:0',
            'form.motivo' => 'nullable|string|max:255',
            'form.fecha_emision' => 'required|date',
            'form.estado_pago' => 'required|boolean',
        ];
    }

    public function buscarComunero(): void
    {
        $this->isSearching = true;

        if (empty($this->searchCiudadano)) {
            $this->selectedComuneroNombre = '';
            $this->form['comunero_id'] = '';
            $this->isSearching = false;
            return;
        }

        $comunero = Comunero::whereHas('ciudadano', function ($query) {
            $query->where('dni', trim($this->searchCiudadano));
        })->first();

        if ($comunero) {
            $this->form['comunero_id'] = $comunero->id;
            $ciudadano = $comunero->ciudadano;
            $this->selectedComuneroNombre = $ciudadano->ape_paterno.' '.$ciudadano->ape_materno.', '.$ciudadano->nombres;
            $this->showSearchResults = false;
        } else {
            $this->form['comunero_id'] = '';
            $this->selectedComuneroNombre = '';
            $this->showSearchResults = true;
        }

        $this->isSearching = false;
    }

    public function limpiarComunero(): void
    {
        $this->form['comunero_id'] = '';
        $this->selectedComuneroNombre = '';
        $this->searchCiudadano = '';
        $this->showSearchResults = false;
    }

    protected function saveRecord(array $data): void
    {
        if ($this->editingId) {
            $multa = Multa::find($this->editingId);
            $multa->update($data);
            $this->dispatch('multa-updated', message: 'Multa actualizada exitosamente');
        } else {
            Multa::create($data);
            $this->dispatch('multa-created', message: 'Multa creada exitosamente');
        }
    }

    protected function deleteRecord($id): void
    {
        $multa = Multa::findOrFail($id);
        abort_unless(auth()->user()->hasAnyRole(['admin', 'tesorero']), 403);
        $multa->delete();
        $this->dispatch('multa-deleted', message: 'Multa eliminada exitosamente');
    }

    protected function getModelIndex()
    {
        return Multa::with('comunero.ciudadano')
            ->whereHas('comunero.ciudadano', function ($query) {
                $query->where('ape_paterno', 'like', '%'.$this->search.'%')
                    ->orWhere('ape_materno', 'like', '%'.$this->search.'%')
                    ->orWhere('nombres', 'like', '%'.$this->search.'%')
                    ->orWhere('dni', 'like', '%'.$this->search.'%');
            })
            ->orWhere('motivo', 'like', '%'.$this->search.'%')
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    protected function getViewName(): string
    {
        return 'livewire.multas.index';
    }

    public function render()
    {
        if ($this->form['comunero_id'] && empty($this->selectedComuneroNombre)) {
            $comunero = Comunero::with('ciudadano')->find($this->form['comunero_id']);
            if ($comunero?->ciudadano) {
                $ciudadano = $comunero->ciudadano;
                $this->selectedComuneroNombre = $ciudadano->ape_paterno.' '.$ciudadano->ape_materno.', '.$ciudadano->nombres;
            }
        }

        return view($this->getViewName(), [
            'multas' => $this->getModelIndex(),
        ]);
    }
}
