<?php

namespace App\Enums;

// Define los roles del sistema y su valor en la base de datos
enum UserRole: string
{

    // Los tres roles posibles del sistema
    case ADMIN       = 'admin';
    case REGISTRADOR = 'registrador';
    case TESORERO    = 'tesorero';

    // Convierte el valor técnico en un nombre legible para la interfaz
    // Ejemplo: UserRole::ADMIN->label() → 'Administrador'
    public function label(): string
    {
        return match($this) {
            self::ADMIN       => 'Administrador',
            self::REGISTRADOR => 'Registrador',
            self::TESORERO    => 'Tesorero',
        };

        //Uso: $user->role->label() → 'Administrador'
    }
}
