<?php

namespace App\Livewire\Concerns;

use Livewire\WithPagination;

/**
 * Trait para manejar operaciones CRUD básicas en componentes Livewire.
 * 
 * Propiedades requeridas que deben definirse en el componente:
 * - $search (string) - Búsqueda
 * - $showModal (bool) - Control del modal
 * - $editingId (int|null) - ID del registro en edición
 * - $form (array) - Datos del formulario
 * - $perPage (int) - Registros por página
 * 
 * Métodos que deben implementarse en el componente:
 * - getFormDefaults(): array - Valores por defecto del formulario
 * - getFormDataForEdit($record): array - Datos formateados para edición
 * - getValidationRules(): array - Reglas de validación
 * - saveRecord(array $data): void - Lógica de guardado (crear/actualizar)
 * - deleteRecord($id): void - Lógica de eliminación con autorización
 * - getModelIndex(): mixed - Obtener registros paginados
 * - getViewName(): string - Nombre de la vista para render()
 */
trait HandlesCrud
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $form = [];
    public $perPage = 10;

    protected $queryString = ['search'];

    public function mount(): void
    {
        $this->form = $this->getFormDefaults();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Abrir modal para crear un nuevo registro.
     */
    public function openNew(): void
    {
        $this->editingId = null;
        $this->form = $this->getFormDefaults();
        $this->showModal = true;
    }

    /**
     * Abrir modal para editar un registro existente.
     */
    public function openEdit($id): void
    {
        $record = $this->getModel()::findOrFail($id);
        $this->editingId = $id;
        $this->form = $this->getFormDataForEdit($record);
        $this->showModal = true;
    }

    /**
     * Validar y guardar el registro (crear o actualizar).
     */
    public function save(): void
    {
        $this->validate($this->getValidationRules());
        $this->saveRecord($this->form);
        $this->closeModal();
    }

    /**
     * Cerrar el modal y limpiar el estado.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->form = $this->getFormDefaults();
    }

    /**
     * Eliminar un registro con verificación de autorización.
     */
    public function delete($id): void
    {
        $this->deleteRecord($id);
    }

    /**
     * Obtener el modelo asociado (debe implementarse en el componente).
     */
    abstract protected function getModel(): string;

    /**
     * Obtener valores por defecto del formulario.
     */
    abstract protected function getFormDefaults(): array;

    /**
     * Obtener datos del formulario para edición.
     */
    abstract protected function getFormDataForEdit($record): array;

    /**
     * Obtener reglas de validación.
     */
    abstract protected function getValidationRules(): array;

    /**
     * Guardar el registro (crear o actualizar).
     */
    abstract protected function saveRecord(array $data): void;

    /**
     * Eliminar un registro con autorización.
     */
    abstract protected function deleteRecord($id): void;

    /**
     * Obtener registros paginados (debe implementarse en el componente).
     */
    abstract protected function getModelIndex();
}
