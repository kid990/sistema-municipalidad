<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'rol'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Define los atributos que deben convertirse a otros tipos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'rol' => UserRole::class,
        ];
    }

    /**
     * Obtiene las iniciales del usuario.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Verifica si el usuario es administrador.
     */
    public function isAdmin(): bool
    {
        return $this->rol === UserRole::ADMIN;
    }

    /**
     * Verifica si el usuario es registrador.
     */
    public function isRegistrador(): bool
    {
        return $this->rol === UserRole::REGISTRADOR;
    }

    /**
     * Verifica si el usuario es tesorero.
     */
    public function isTesorero(): bool
    {
        return $this->rol === UserRole::TESORERO;
    }

    /**
     * Verifica si el usuario tiene alguno de los roles indicados.
     */
    public function hasAnyRole(UserRole|array|string $roles): bool
    {
        if (is_string($roles)) {
            return $this->rol->value === $roles;
        }
        if (is_array($roles)) {
            $roles = array_map(fn($r) => $r instanceof UserRole ? $r->value : $r, $roles);
            return in_array($this->rol->value, $roles);
        }

        return $this->rol === $roles;
    }

}
