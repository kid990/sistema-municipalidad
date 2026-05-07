<?php

namespace App\Livewire\CiudadanoCargo;

use App\Livewire\Concerns\HandlesCrud;
use App\Models\Cargo;
use App\Models\Ciudadano;
use App\Models\CiudadanoCargo;
use App\Models\Gestion;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Asignación de Cargos')]
class Index extends Component
{
    use HandlesCrud;

    public $ciudadanos = [];
    public $cargos = [];
    public $gestiones = [];
    public $searchCiudadano = '';
    public $selectedCiudadanoId = null;
    public $showSearchResults = false;
    public $selectedCiudadanoNombre = '';
    public $isSearching = false;

    public function mount(): void
    {
        $this->form = $this->getFormDefaults();
        $this->loadSelects();
    }

    #[\Livewire\Attributes\On('resetForm')]
    public function resetForm(): void
    {
        $this->selectedCiudadanoId = null;
        $this->selectedCiudadanoNombre = '';
        $this->searchCiudadano = '';
        $this->showSearchResults = false;
        $this->isSearching = false;
    }

    public function buscarCiudadano(): void
    {
        $this->isSearching = true;

        if (empty($this->searchCiudadano)) {
            $this->selectedCiudadanoNombre = '';
            $this->selectedCiudadanoId = null;
            $this->form['ciudadano_id'] = '';
            $this->isSearching = false;
            return;
        }

        // Buscar ciudadano por DNI exacto
        $ciudadano = Ciudadano::where('dni', trim($this->searchCiudadano))
            ->whereHas('comunero', function ($query) {
                $query->where('estado_comunero', 'Activo');
            })
            ->first();

        if ($ciudadano) {
            // Si encuentra al ciudadano, llenar automáticamente
            $this->selectedCiudadanoId = $ciudadano->id;
            $this->form['ciudadano_id'] = $ciudadano->id;
            $this->selectedCiudadanoNombre = $ciudadano->ape_paterno . ' ' . $ciudadano->ape_materno . ', ' . $ciudadano->nombres;
            $this->showSearchResults = false;
        } else {
            // Si no encuentra, limpiar
            $this->selectedCiudadanoId = null;
            $this->selectedCiudadanoNombre = '';
            $this->form['ciudadano_id'] = '';
            $this->showSearchResults = true;
        }

        $this->isSearching = false;
    }

    public function getFilteredCiudadanosProperty()
    {
        // Ya no se usa - la búsqueda se hace directamente en buscarCiudadano()
        return collect();
    }

    public function limpiarCiudadano(): void
    {
        $this->selectedCiudadanoId = null;
        $this->form['ciudadano_id'] = '';
        $this->selectedCiudadanoNombre = '';
        $this->searchCiudadano = '';
        $this->showSearchResults = false;
    }

    public function loadSelects(): void
    {
        $this->ciudadanos = Ciudadano::whereHas('comunero', function ($query) {
            $query->where('estado_comunero', 'Activo');
        })
            ->orderBy('ape_paterno')
            ->get()
            ->mapWithKeys(function ($ciudadano) {
                return [$ciudadano->id => $ciudadano->ape_paterno.' '.$ciudadano->ape_materno.', '.$ciudadano->nombres];
            })
            ->toArray();

        $this->gestiones = Gestion::where('estado_gestion', true)
            ->orderByDesc('fecha_inicio')
            ->get()
            ->mapWithKeys(function ($gestion) {
                return [$gestion->id => $gestion->nombre_gestion];
            })
            ->toArray();

        $this->cargos = Cargo::query()
            ->orderBy('nombre_cargo')
            ->get()
            ->mapWithKeys(function ($cargo) {
                return [$cargo->id => $cargo->nombre];
            })
            ->toArray();
    }

    protected function getModel(): string
    {
        return CiudadanoCargo::class;
    }

    protected function getFormDefaults(): array
    {
        return [
            'ciudadano_id' => '',
            'gestion_id' => '',
            'cargo_id' => '',
            'fecha_inicio' => '',
            'fecha_fin' => '',
            'activo' => true,
        ];
    }

    protected function getFormDataForEdit($record): array
    {
        $ciudadano = $record->ciudadano;
        $this->selectedCiudadanoId = $record->ciudadano_id;
        $this->selectedCiudadanoNombre = $ciudadano->ape_paterno.' '.$ciudadano->ape_materno.', '.$ciudadano->nombres;
        
        return [
            'ciudadano_id' => $record->ciudadano_id,
            'gestion_id' => $record->gestion_id,
            'cargo_id' => $record->cargo_id,
            'fecha_inicio' => $record->fecha_inicio->format('Y-m-d'),
            'fecha_fin' => $record->fecha_fin ? $record->fecha_fin->format('Y-m-d') : '',
            'activo' => $record->activo,
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'form.ciudadano_id' => 'required|exists:ciudadanos,id',
            'form.gestion_id' => 'required|exists:gestiones,id',
            'form.cargo_id' => 'required|exists:cargos,id',
            'form.fecha_inicio' => 'required|date',
            'form.fecha_fin' => 'nullable|date|after_or_equal:form.fecha_inicio',
            'form.activo' => 'required|boolean',
        ];
    }

    protected function saveRecord(array $data): void
    {
        $data['estado_asignacion'] = $data['activo'] ? 'Vigente' : 'Cesado';
        unset($data['activo']);

        if ($this->editingId) {
            $asignacion = CiudadanoCargo::find($this->editingId);
            $asignacion->update($data);
            $this->dispatch('asignacion-updated', message: 'Asignación actualizada exitosamente');
        } else {
            CiudadanoCargo::create($data);
            $this->dispatch('asignacion-created', message: 'Asignación creada exitosamente');
        }
    }

    protected function deleteRecord($id): void
    {
        $ciudadanoCargo = CiudadanoCargo::findOrFail($id);
        abort_unless(auth()->user()->hasAnyRole(['admin', 'registrador']), 403);
        $ciudadanoCargo->delete();
        $this->dispatch('asignacion-deleted', message: 'Asignación eliminada exitosamente');
    }

    protected function getModelIndex()
    {
        return CiudadanoCargo::with(['ciudadano', 'cargo', 'gestion'])
            ->whereHas('ciudadano', function ($query) {
                $query->where('ape_paterno', 'like', '%'.$this->search.'%')
                    ->orWhere('ape_materno', 'like', '%'.$this->search.'%')
                    ->orWhere('nombres', 'like', '%'.$this->search.'%')
                    ->orWhere('dni', 'like', '%'.$this->search.'%');
            })
            ->orWhereHas('cargo', function ($query) {
                $query->where('nombre_cargo', 'like', '%'.$this->search.'%');
            })
            ->orWhereHas('gestion', function ($query) {
                $query->where('nombre_gestion', 'like', '%'.$this->search.'%');
            })
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    protected function getViewName(): string
    {
        return 'livewire.ciudadano-cargo.index';
    }

    public function render()
    {
        return view($this->getViewName(), [
            'asignaciones' => $this->getModelIndex(),
        ]);
    }
}
