<?php

namespace App\Livewire\Cargos;

use App\Livewire\Concerns\HandlesCrud;
use App\Models\Cargo;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cargos')]
class Index extends Component
{
    use HandlesCrud;

    protected function getModel(): string
    {
        return Cargo::class;
    }

    protected function getFormDefaults(): array
    {
        return ['nombre_cargo' => ''];
    }

    protected function getFormDataForEdit($record): array
    {
        return [
            'nombre_cargo' => $record->nombre_cargo,
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'form.nombre_cargo' => 'required|string|max:50',
        ];
    }

    protected function saveRecord(array $data): void
    {
        if ($this->editingId) {
            $cargo = Cargo::find($this->editingId);
            $cargo->update($data);
            $this->dispatch('cargo-updated', message: 'Cargo actualizado exitosamente');
        } else {
            Cargo::create($data);
            $this->dispatch('cargo-created', message: 'Cargo creado exitosamente');
        }
    }

    protected function deleteRecord($id): void
    {
        $cargo = Cargo::findOrFail($id);
        abort_unless(auth()->user()->hasAnyRole(['admin']), 403);
        $cargo->delete();
        $this->dispatch('cargo-deleted', message: 'Cargo eliminado exitosamente');
    }

    protected function getModelIndex()
    {
        return Cargo::where('nombre_cargo', 'like', '%'.$this->search.'%')
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    protected function getViewName(): string
    {
        return 'livewire.cargos.index';
    }

    public function render()
    {
        return view($this->getViewName(), [
            'cargos' => $this->getModelIndex(),
        ]);
    }
}
