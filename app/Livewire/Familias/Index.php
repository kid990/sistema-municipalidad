<?php

namespace App\Livewire\Familias;

use App\Livewire\Concerns\HandlesCrud;
use App\Models\Ciudadano;
use App\Models\Familia;
use App\Models\Lote;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Familias')]
class Index extends Component
{
    use HandlesCrud;

    public $lotes = [];
    public $searchCiudadano = '';
    public $selectedJefeNombre = '';
    public $showSearchResults = false;
    public $isSearching = false;

    public function mount(): void
    {
        $this->form = $this->getFormDefaults();
        $this->loadSelects();
    }

    #[\Livewire\Attributes\On('resetForm')]
    public function resetForm(): void
    {
        $this->selectedJefeNombre = '';
        $this->searchCiudadano = '';
        $this->showSearchResults = false;
        $this->isSearching = false;
    }

    protected function getModel(): string
    {
        return Familia::class;
    }

    public function loadSelects(): void
    {
        $this->lotes = Lote::orderBy('numero_lote')
            ->get()
            ->mapWithKeys(function ($lote) {
                $label = $lote->numero_lote;
                if (! empty($lote->manzana)) {
                    $label .= ' - Mz ' . $lote->manzana;
                }
                return [$lote->id => $label];
            })
            ->toArray();

    }

    public function buscarCiudadano(): void
    {
        $this->isSearching = true;

        if (empty($this->searchCiudadano)) {
            $this->selectedJefeNombre = '';
            $this->form['jefe_familia_id'] = '';
            $this->isSearching = false;
            return;
        }

        $ciudadano = Ciudadano::where('dni', trim($this->searchCiudadano))
            ->first();

        if ($ciudadano) {
            $this->form['jefe_familia_id'] = $ciudadano->id;
            $this->selectedJefeNombre = $ciudadano->ape_paterno.' '.$ciudadano->ape_materno.', '.$ciudadano->nombres;
            $this->showSearchResults = false;
        } else {
            $this->form['jefe_familia_id'] = '';
            $this->selectedJefeNombre = '';
            $this->showSearchResults = true;
        }

        $this->isSearching = false;
    }

    public function limpiarJefeFamilia(): void
    {
        $this->form['jefe_familia_id'] = '';
        $this->selectedJefeNombre = '';
        $this->searchCiudadano = '';
        $this->showSearchResults = false;
    }

    protected function getFormDefaults(): array
    {
        return [
            'nombre_familia' => '',
            'lote_id' => '',
            'jefe_familia_id' => '',
        ];
    }

    protected function getFormDataForEdit($record): array
    {
        return [
            'nombre_familia' => $record->nombre_familia ?? '',
            'lote_id' => $record->lote_id,
            'jefe_familia_id' => $record->jefe_familia_id,
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'form.nombre_familia' => 'required|string|max:100',
            'form.lote_id' => 'nullable|exists:lotes,id',
            'form.jefe_familia_id' => 'required|exists:ciudadanos,id',
        ];
    }

    protected function saveRecord(array $data): void
    {
        $data['lote_id'] = $data['lote_id'] ?: null;

        if ($this->editingId) {
            $familia = Familia::find($this->editingId);
            $familia->update($data);
            $this->dispatch('familia-updated', message: 'Familia actualizada exitosamente');
        } else {
            Familia::create($data);
            $this->dispatch('familia-created', message: 'Familia creada exitosamente');
        }
    }

    protected function deleteRecord($id): void
    {
        $familia = Familia::findOrFail($id);
        abort_unless(auth()->user()->hasAnyRole(['admin']), 403);
        $familia->delete();
        $this->dispatch('familia-deleted', message: 'Familia eliminada exitosamente');
    }

    protected function getModelIndex()
    {
        return Familia::with(['lote', 'jefeFamilia'])
            ->whereHas('jefeFamilia', function ($query) {
                $query->where('ape_paterno', 'like', '%'.$this->search.'%')
                    ->orWhere('ape_materno', 'like', '%'.$this->search.'%')
                    ->orWhere('nombres', 'like', '%'.$this->search.'%')
                    ->orWhere('dni', 'like', '%'.$this->search.'%');
            })
            ->orWhereHas('lote', function ($query) {
                $query->where('numero_lote', 'like', '%'.$this->search.'%')
                    ->orWhere('manzana', 'like', '%'.$this->search.'%');
            })
            ->orWhere('nombre_familia', 'like', '%'.$this->search.'%')
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    protected function getViewName(): string
    {
        return 'livewire.familias.index';
    }

    public function render()
    {
        if ($this->form['jefe_familia_id'] && empty($this->selectedJefeNombre)) {
            $jefe = Ciudadano::find($this->form['jefe_familia_id']);
            if ($jefe) {
                $this->selectedJefeNombre = $jefe->ape_paterno.' '.$jefe->ape_materno.', '.$jefe->nombres;
            }
        }

        return view($this->getViewName(), [
            'familias' => $this->getModelIndex(),
        ]);
    }
}
