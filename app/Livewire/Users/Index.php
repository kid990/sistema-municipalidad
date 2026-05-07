<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Enums\UserRole;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Usuarios')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $editingId = null;

    public $form = ['name' => '', 'email' => '', 'rol' => '', 'password' => '', 'password_confirmation' => ''];

    public $showPassword = false;

    public $showPasswordConfirmation = false;

    public $perPage = 10;

    protected $queryString = ['search'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getUsersProperty()
    {
        return User::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('email', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate($this->perPage);
    }

    public function openNew(): void
    {
        $this->editingId = null;
        $this->form = ['name' => '', 'email' => '', 'rol' => '', 'password' => '', 'password_confirmation' => ''];
        $this->showPassword = false;
        $this->showPasswordConfirmation = false;
        $this->showModal = true;
    }

    public function openEdit($id): void
    {
        $user = User::find($id);
        $this->editingId = $id;
        $this->form = [
            'name' => $user->name,
            'email' => $user->email,
            'rol' => $user->rol->value,
            'password' => '',
            'password_confirmation' => '',
        ];
        $this->showPassword = false;
        $this->showPasswordConfirmation = false;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'form.name' => 'required|string|max:255',
            'form.email' => 'required|email|max:255|unique:users,email,'.$this->editingId,
            'form.rol' => 'required|in:admin,registrador,tesorero',
        ];

        if (!$this->editingId) {
            $rules['form.password'] = 'required|string|min:8|confirmed';
            $rules['form.password_confirmation'] = 'required';
        }

        $this->validate($rules);

        if ($this->editingId) {
            $user = User::find($this->editingId);
            $user->update([
                'name' => $this->form['name'],
                'email' => $this->form['email'],
                'rol' => $this->form['rol'],
            ]);
            $this->dispatch('user-updated', message: 'Usuario actualizado exitosamente');
        } else {
            User::create([
                'name' => $this->form['name'],
                'email' => $this->form['email'],
                'rol' => $this->form['rol'],
                'password' => $this->form['password'],
            ]);
            $this->dispatch('user-created', message: 'Usuario creado exitosamente');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->form = ['name' => '', 'email' => '', 'rol' => '', 'password' => '', 'password_confirmation' => ''];
        $this->showPassword = false;
        $this->showPasswordConfirmation = false;
    }

    public function toggleShowPassword(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function toggleShowPasswordConfirmation(): void
    {
        $this->showPasswordConfirmation = ! $this->showPasswordConfirmation;
    }

    public function delete($id): void
    {
        // Prevent deletion of current user
        if ($id === auth()->id()) {
            $this->dispatch('error', message: 'No puedes eliminar tu propio usuario');

            return;
        }

        $user = User::findOrFail($id);
        
        abort_unless(auth()->user()->hasAnyRole('admin'), 403);
        
        $user->delete();
        $this->dispatch('user-deleted', message: 'Usuario eliminado exitosamente');
    }

    public function render()
    {
        return view('livewire.users.index', [
            'users' => $this->users,
            'roles' => UserRole::cases(),
        ]);
    }
}
