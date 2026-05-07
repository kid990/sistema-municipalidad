<?php

namespace App\Livewire\Comuneros;

use App\Exports\ComunerosExport;
use App\Models\Ciudadano;
use App\Models\Comunero;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Comuneros')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $searchCiudadano = '';

    public $selectedCiudadanos = [];

    public $estado_comunero = 'Activo';

    public $perPage = 10;

    protected $queryString = ['search'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getComunerosProperty()
    {
        return Comunero::with('ciudadano')
            ->whereHas('ciudadano', function ($query) {
                $query->where('ape_paterno', 'like', '%'.$this->search.'%')
                    ->orWhere('ape_materno', 'like', '%'.$this->search.'%')
                    ->orWhere('nombres', 'like', '%'.$this->search.'%')
                    ->orWhere('dni', 'like', '%'.$this->search.'%');
            })
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    public function getCiudadanosDisponiblesProperty()
    {
        $comuneroIds = Comunero::pluck('ciudadano_id')->toArray();

        return Ciudadano::whereNotIn('id', $comuneroIds)
            ->orderBy('ape_paterno')
            ->get()
            ->mapWithKeys(function ($ciudadano) {
                return [$ciudadano->id => $ciudadano->ape_paterno.' '.$ciudadano->ape_materno.', '.$ciudadano->nombres.' - DNI: '.$ciudadano->dni];
            })
            ->toArray();
    }

    public function getFiltredCiudadanosProperty()
    {
        if (empty($this->searchCiudadano)) {
            return $this->ciudadanosDisponibles;
        }

        $search = strtolower($this->searchCiudadano);

        return array_filter(
            $this->ciudadanosDisponibles,
            function ($text) use ($search) {
                return strpos(strtolower($text), $search) !== false;
            }
        );
    }

    public function openNew(): void
    {
        $this->selectedCiudadanos = [];
        $this->searchCiudadano = '';
        $this->estado_comunero = 'Activo';
        $this->showModal = true;
    }

    public function toggleCiudadano($id): void
    {
        $key = array_search($id, $this->selectedCiudadanos);

        if ($key !== false) {
            unset($this->selectedCiudadanos[$key]);
            $this->selectedCiudadanos = array_values($this->selectedCiudadanos);
        } else {
            $this->selectedCiudadanos[] = $id;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedCiudadanos = [];
        $this->searchCiudadano = '';
        $this->estado_comunero = 'Activo';
    }

    public function save(): void
    {
        $this->validate([
            'selectedCiudadanos' => 'required|array|min:1',
            'selectedCiudadanos.*' => 'required|exists:ciudadanos,id',
            'estado_comunero' => 'required|in:Activo,Suspendido,Retirado',
        ], [
            'selectedCiudadanos.required' => 'Debes seleccionar al menos un ciudadano',
            'selectedCiudadanos.min' => 'Debes seleccionar al menos un ciudadano',
        ]);

        foreach ($this->selectedCiudadanos as $ciudadanoId) {
            Comunero::firstOrCreate(
                ['ciudadano_id' => $ciudadanoId],
                [
                    'fecha_empadronamiento' => now()->toDateString(),
                    'estado_comunero' => $this->estado_comunero,
                ]
            );
        }

        $this->dispatch('comunero-created', message: 'Comunero(s) agregado(s) exitosamente');
        $this->closeModal();
    }

    public function delete($id): void
    {
        $comunero = Comunero::findOrFail($id);
        
        abort_unless(auth()->user()->hasAnyRole(['admin', 'registrador']), 403);
        
        $comunero->delete();
        $this->dispatch('comunero-deleted', message: 'Comunero eliminado exitosamente');
    }

    public function descargarExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new ComunerosExport, 'comuneros_'.now()->format('Y-m-d_H-i-s').'.xlsx');
    }

    public function render()
    {
        return view('livewire.comuneros.index', [
            'comuneros' => $this->comuneros,
        ]);
    }
}
