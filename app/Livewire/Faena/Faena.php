<?php

namespace App\Livewire\Faena;

use App\Models\AsistenciaFaena;
use App\Models\Comunero;
use App\Models\Faena as FaenaModel;
use App\Models\FechaFaena;
use Livewire\Component;
use Livewire\WithPagination;

class Faena extends Component
{
    use WithPagination;

    public $activeModal = null;
    public $selectedFaenaId = null;
    public $nombre = '';
    public $costo_multa_inasistencia = '50.00';
    public $fechasTemp = [];
    public $nuevaFecha = '';
    public $search = '';
    public $perPage = 10;

    public function render()
    {
        $query = FaenaModel::query()->withCount('fechasFaenas');

        if ($this->search) {
            $query->where('nombre_actividad', 'like', '%'.$this->search.'%');
        }

        $selectedFaena = $this->selectedFaenaId
            ? FaenaModel::with('fechasFaenas')->find($this->selectedFaenaId)
            : null;

        return view('livewire.faena.faena', [
            'faenas' => $query->orderByDesc('id')->paginate($this->perPage),
            'selectedFaena' => $selectedFaena,
            'estadisticasPorFecha' => $this->activeModal === 'view' && $this->selectedFaenaId
                ? $this->obtenerEstadisticasPorFecha($this->selectedFaenaId)
                : [],
            'estadisticasGenerales' => $this->activeModal === 'view' && $this->selectedFaenaId
                ? $this->obtenerEstadisticas($this->selectedFaenaId)
                : [],
        ]);
    }

    public function openModal($type, $faenaId = null): void
    {
        $this->activeModal = $type;
        $this->selectedFaenaId = $faenaId;
        $this->resetFormularios();
    }

    public function agregarUnaFecha(): void
    {
        $this->validate([
            'nuevaFecha' => 'required|date',
        ], [
            'nuevaFecha.required' => 'Selecciona una fecha',
        ]);

        if (in_array($this->nuevaFecha, $this->fechasTemp, true)) {
            $this->addError('nuevaFecha', 'Esa fecha ya fue agregada');
            return;
        }

        $existe = FechaFaena::where('faena_id', $this->selectedFaenaId)
            ->where('fecha_realizacion', $this->nuevaFecha)
            ->exists();

        if ($existe) {
            $this->addError('nuevaFecha', 'Esa fecha ya esta registrada');
            return;
        }

        $this->fechasTemp[] = $this->nuevaFecha;
        $this->nuevaFecha = '';
        $this->resetValidation();
    }

    public function quitarFechaTemporal($index): void
    {
        unset($this->fechasTemp[$index]);
        $this->fechasTemp = array_values($this->fechasTemp);
    }

    public function closeModal(): void
    {
        $this->activeModal = null;
        $this->selectedFaenaId = null;
        $this->resetFormularios();
    }

    public function resetFormularios(): void
    {
        $this->nombre = '';
        $this->costo_multa_inasistencia = '50.00';
        $this->fechasTemp = [];
        $this->nuevaFecha = '';
    }

    public function crearFaena(): void
    {
        $this->validate([
            'nombre' => 'required|string|min:3|max:150',
            'costo_multa_inasistencia' => 'required|numeric|min:0|max:99999999.99',
        ]);

        FaenaModel::create([
            'nombre_actividad' => $this->nombre,
            'costo_multa_inasistencia' => $this->costo_multa_inasistencia,
        ]);

        session()->flash('success', "Faena '{$this->nombre}' creada exitosamente");
        $this->closeModal();
    }

    public function agregarFechas(): void
    {
        if (empty($this->fechasTemp)) {
            $this->addError('newFechas', 'Agrega al menos una fecha');
            return;
        }

        $comuneros = Comunero::where('estado_comunero', 'Activo')->get();

        if ($comuneros->isEmpty()) {
            session()->flash('error', 'No hay comuneros activos para generar registros');
            return;
        }

        $count = 0;

        foreach ($this->fechasTemp as $fecha) {
            $fechaFaena = FechaFaena::firstOrCreate([
                'faena_id' => $this->selectedFaenaId,
                'fecha_realizacion' => $fecha,
            ]);

            foreach ($comuneros as $comunero) {
                $asistencia = AsistenciaFaena::firstOrCreate([
                    'fecha_faena_id' => $fechaFaena->id,
                    'comunero_id' => $comunero->id,
                ], [
                    'estado_asistencia' => 'Falto',
                ]);

                if ($asistencia->wasRecentlyCreated) {
                    $count++;
                }
            }
        }

        session()->flash('success', 'Se agregaron '.count($this->fechasTemp).' fechas con '.$count.' registros');
        $this->closeModal();
    }

    public function obtenerEstadisticas($faenaId): array
    {
        $query = AsistenciaFaena::whereHas('fechaFaena', fn ($q) => $q->where('faena_id', $faenaId));
        $estadisticas = (clone $query)
            ->selectRaw('estado_asistencia, COUNT(*) as count')
            ->groupBy('estado_asistencia')
            ->pluck('count', 'estado_asistencia');

        return [
            'asistio' => $estadisticas->get('Asistio', 0),
            'falto' => $estadisticas->get('Falto', 0),
            'justificado' => $estadisticas->get('Justificado', 0),
            'total' => $query->count(),
        ];
    }

    public function obtenerEstadisticasPorFecha($faenaId)
    {
        return FechaFaena::where('faena_id', $faenaId)
            ->with('asistencias')
            ->orderBy('fecha_realizacion')
            ->get()
            ->map(fn ($fecha) => [
                'fecha' => $fecha->fecha_realizacion,
                'asistio' => $fecha->asistencias->where('estado_asistencia', 'Asistio')->count(),
                'falto' => $fecha->asistencias->where('estado_asistencia', 'Falto')->count(),
                'justificado' => $fecha->asistencias->where('estado_asistencia', 'Justificado')->count(),
                'total' => $fecha->asistencias->count(),
            ]);
    }

    public function completarRegistros($faenaId): void
    {
        $fechas = FechaFaena::where('faena_id', $faenaId)->get();
        $comuneros = Comunero::where('estado_comunero', 'Activo')->get();

        if ($fechas->isEmpty() || $comuneros->isEmpty()) {
            session()->flash('error', 'Debe existir al menos una fecha y un comunero activo');
            return;
        }

        $creados = 0;

        foreach ($fechas as $fecha) {
            foreach ($comuneros as $comunero) {
                $asistencia = AsistenciaFaena::firstOrCreate([
                    'fecha_faena_id' => $fecha->id,
                    'comunero_id' => $comunero->id,
                ], [
                    'estado_asistencia' => 'Falto',
                ]);

                if ($asistencia->wasRecentlyCreated) {
                    $creados++;
                }
            }
        }

        session()->flash('success', "Registros completados. {$creados} registro(s) nuevo(s).");
        $this->closeModal();
    }

    public function eliminarFaena($faenaId): void
    {
        $marcados = AsistenciaFaena::whereHas('fechaFaena', fn ($q) => $q->where('faena_id', $faenaId))
            ->whereIn('estado_asistencia', ['Asistio', 'Justificado'])
            ->exists();

        if ($marcados) {
            $this->dispatch('error', message: 'No se puede eliminar la faena porque hay registros de asistencia marcados.');
            return;
        }

        $faena = FaenaModel::findOrFail($faenaId);
        $nombre = $faena->nombre_actividad;
        $faena->delete();

        $this->dispatch('faena-deleted', message: "Faena '{$nombre}' eliminada exitosamente");
    }
}
