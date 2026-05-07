<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiReniecService
{
    private $baseUrl;

    private $token;

    private $timeout;

    public function __construct()
    {
        // Usar config() para evitar problemas de caché de configuración
        $this->baseUrl = config('reniec.api_url', 'https://dniruc.apisperu.com/api/v1');
        $this->token = config('reniec.token');
        $this->timeout = config('reniec.timeout', 10);
    }

    /**
     * Consultar DNI en RENIEC
     *
     * @param  string  $dni  - DNI de 8 dígitos
     * @return array|null - Datos del ciudadano o null si hay error
     */
    public function consultarDni(string $dni)
    {
        try {
            // Validar que el DNI sea de 8 dígitos
            if (! preg_match('/^\d{8}$/', $dni)) {
                return [
                    'success' => false,
                    'message' => 'El DNI debe ser de 8 dígitos',
                    'data' => null,
                ];
            }

            $url = "{$this->baseUrl}/dni/{$dni}?token={$this->token}";

            // Log para debugging
            Log::debug('RENIEC API - Consultando DNI', [
                'url' => $url,
                'dni' => $dni,
                'baseUrl' => $this->baseUrl,
                'timeout' => $this->timeout,
            ]);

            $response = Http::timeout($this->timeout)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                // Verificar si la respuesta es exitosa
                if (isset($data['success']) && $data['success']) {
                    return [
                        'success' => true,
                        'message' => 'DNI validado correctamente',
                        'data' => [
                            'dni' => $data['dni'] ?? $dni,
                            'nombres' => $data['nombres'] ?? $data['nombre'] ?? null,
                            'ape_paterno' => $data['apellidoPaterno'] ?? $data['ape_paternoaterno'] ?? $data['apell_paterno'] ?? null,
                            'ape_materno' => $data['apellidoMaterno'] ?? $data['ape_maternoaterno'] ?? $data['apell_materno'] ?? null,
                            'fecha_nacimiento' => $data['fechaNacimiento'] ?? $data['fecha_nacimiento'] ?? $data['fechaNac'] ?? null,
                        ],
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $data['message'] ?? 'No se encontró el DNI en RENIEC',
                        'data' => null,
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Error en la consulta a RENIEC',
                    'data' => null,
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Consultar RUC en SUNAT
     *
     * @param  string  $ruc  - RUC de 11 dígitos
     * @return array|null - Datos de la empresa o null si hay error
     */
    public function consultarRuc(string $ruc)
    {
        try {
            // Validar que el RUC sea de 11 dígitos
            if (! preg_match('/^\d{11}$/', $ruc)) {
                return [
                    'success' => false,
                    'message' => 'El RUC debe ser de 11 dígitos',
                    'data' => null,
                ];
            }

            $url = "{$this->baseUrl}/ruc/{$ruc}?token={$this->token}";

            $response = Http::timeout($this->timeout)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['success']) && $data['success']) {
                    return [
                        'success' => true,
                        'message' => 'RUC validado correctamente',
                        'data' => $data,
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $data['message'] ?? 'No se encontró el RUC en SUNAT',
                        'data' => null,
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Error en la consulta a SUNAT',
                    'data' => null,
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
                'data' => null,
            ];
        }
    }
}
