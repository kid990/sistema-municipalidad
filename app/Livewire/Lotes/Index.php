<?php

namespace App\Livewire\Lotes;

use App\Livewire\Concerns\HandlesCrud;
use App\Models\Lote;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Lotes')]
class Index extends Component
{
    use HandlesCrud;

    protected function getModel(): string
    {
        return Lote::class;
    }

    protected function getFormDefaults(): array
    {
        return [
            'numero_lote' => '',
            'manzana' => '',
            'area_m2' => '',
            'referencia_ubicacion' => '',
            'estado' => 'Habitado',
        ];
    }

    protected function getFormDataForEdit($record): array
    {
        return [
            'numero_lote' => $record->numero_lote,
            'manzana' => $record->manzana ?? '',
            'area_m2' => $record->area_m2 ?? '',
            'referencia_ubicacion' => $record->referencia_ubicacion ?? '',
            'estado' => $record->estado ?? 'Habitado',
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'form.numero_lote' => 'required|string|max:20',
            'form.manzana' => 'nullable|string|max:10',
            'form.area_m2' => 'nullable|numeric|min:0',
            'form.referencia_ubicacion' => 'nullable|string',
            'form.estado' => 'required|in:Habitado,Desocupado,En Litigio',
        ];
    }

    protected function saveRecord(array $data): void
    {
        if ($this->editingId) {
            $lote = Lote::find($this->editingId);
            $lote->update($data);
            $this->dispatch('lote-updated', message: 'Lote actualizado exitosamente');
        } else {
            Lote::create($data);
            $this->dispatch('lote-created', message: 'Lote creado exitosamente');
        }
    }

    protected function deleteRecord($id): void
    {
        $lote = Lote::findOrFail($id);
        abort_unless(auth()->user()->hasAnyRole(['admin']), 403);
        $lote->delete();
        $this->dispatch('lote-deleted', message: 'Lote eliminado exitosamente');
    }

    protected function getModelIndex()
    {
        return Lote::where('numero_lote', 'like', '%'.$this->search.'%')
            ->orWhere('manzana', 'like', '%'.$this->search.'%')
            ->orWhere('referencia_ubicacion', 'like', '%'.$this->search.'%')
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    protected function getViewName(): string
    {
        return 'livewire.lotes.index';
    }

    public function render()
    {
        return view($this->getViewName(), [
            'lotes' => $this->getModelIndex(),
        ]);
    }
}
