<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue; 
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Str; 

class UsuarioCreadoParaCoordinador extends Notification
{
    use Queueable;

    protected $coordinador;
    protected $usuario;

    // Recibimos los modelos de Coordinador y Usuario creados
    public function __construct($coordinador, $usuario)
    {
        $this->coordinador = $coordinador->loadMissing(['docente.persona', 'docente.pnf']);
        $this->usuario = $usuario;
    }

    // Le decimos a Laravel que guarde esta notificación en la base de datos
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Construye el mensaje personalizado extrayendo los datos de las relaciones
     */
    protected function construirMensaje()
    {
        $docente = $this->coordinador->docente;
        $persona = $docente?->persona;
        $pnf = $docente?->pnf;

        // Extraemos nombre y apellido o colocamos un respaldo
        $nombreCompleto = $persona 
            ? Str::title("{$persona->nombre} {$persona->apellido}") 
            : "Coordinador N° {$this->coordinador->id}";

        // Extraemos el nombre del PNF (ajusta 'nombre' si tu columna se llama de otra forma, ej. 'nombre_pnf')
        $nombrePnf = Str::title($pnf->nombre) ?? 'PNF no asignado';

        return "Se ha creado automáticamente el usuario con el correo '{$this->usuario->email}' para el coordinador {$nombreCompleto} del {$nombrePnf}.";
    }

    public function toArray($notifiable)
    {
        return [
            'mensaje' => $this->construirMensaje(),
            'tipo' => 'registro_usuario',
            'coordinador_id' => $this->coordinador->id,
        ];
    }

    // Estructura del JSON que se guardará en la columna 'data'
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'data' => [
                'mensaje' => $this->construirMensaje(),
                'tipo' => 'registro_usuario',
                'coordinador_id' => $this->coordinador->id,
            ],
            'read_at' => null,
            'created_at' => now()->toIso8601String(),
        ]);
    }
}