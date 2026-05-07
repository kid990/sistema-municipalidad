<?php

namespace App\Livewire\Asistencia;

use App\Models\AsistenciaFaena;
use App\Models\Faena;
use App\Models\FechaFaena;
use Livewire\Component;
use Livewire\WithPagination;

class Asistencia extends Component
{
    use WithPagination;

    public $selectedFaena = null;
    public $selectedFecha = null;
    public $search = '';
    public $perPage = 15;

    public function render()
    {
        $faenas = Faena::whereHas('fechasFaenas')
            ->orderBy('nombre_actividad')
            ->get();

        $fechas = $this->selectedFaena
            ? FechaFaena::where('faena_id', $this->selectedFaena)
                ->orderBy('fecha_realizacion')
                ->pluck('fecha_realizacion')
                ->values()
            : collect();

        $query = AsistenciaFaena::query()
            ->with(['fechaFaena.faena', 'comunero.ciudadano']);

        if ($this->selectedFaena) {
            $query->whereHas('fechaFaena', fn ($q) => $q->where('faena_id', $this->selectedFaena));
        }

        if ($this->selectedFecha) {
            $query->whereHas('fechaFaena', fn ($q) => $q->where('fecha_realizacion', $this->selectedFecha));
        }

        if ($this->search) {
            $query->whereHas('comunero.ciudadano', function ($q) {
                $q->where('nombres', 'like', '%'.$this->search.'%')
                    ->orWhere('ape_paterno', 'like', '%'.$this->search.'%')
                    ->orWhere('ape_materno', 'like', '%'.$this->search.'%');
            });
        }

        return view('livewire.asistencia.asistencia', [
            'asistencias' => $query
                ->join('fechas_faenas', 'asistencia_faenas.fecha_faena_id', '=', 'fechas_faenas.id')
                ->select('asistencia_faenas.*')
                ->orderByDesc('fechas_faenas.fecha_realizacion')
                ->orderByDesc('asistencia_faenas.id')
                ->paginate($this->perPage),
            'faenas' => $faenas,
            'fechas' => $fechas,
        ]);
    }

    public function updatedSelectedFaena(): void
    {
        $this->selectedFecha = null;
        $this->resetPage();
    }

    public function updatedSelectedFecha(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function actualizarAsistencia($asistenciaId, $valor): void
    {
        if (! in_array($valor, ['Asistio', 'Falto', 'Justificado'], true)) {
            session()->flash('error', 'Estado de asistencia no valido');
            return;
        }

        $asistencia = AsistenciaFaena::find($asistenciaId);

        if ($asistencia) {
            $asistencia->update(['estado_asistencia' => $valor]);
            session()->flash('success', 'Asistencia actualizada correctamente');
        }
    }

    public function resetearFiltros(): void
    {
        $this->selectedFaena = null;
        $this->selectedFecha = null;
        $this->search = '';
        $this->resetPage();
    }
}
