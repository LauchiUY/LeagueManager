<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Aplazamiento;

class AplazamientoResueltoNotification extends Notification
{
    use Queueable;

    protected $aplazamiento;
    protected $partido;

    /**
     * Create a new notification instance.
     */
    public function __construct(Aplazamiento $aplazamiento)
    {
        $this->aplazamiento = $aplazamiento;
        $this->partido = $aplazamiento->partido;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $esLocal = $notifiable->id === $this->partido->equipoLocal->id_capitan;
        $rival = $esLocal ? $this->partido->equipoVisitante->nombre : $this->partido->equipoLocal->nombre;
        
        $estadoText = $this->aplazamiento->estado === 'aprobado' ? 'APROBADA' : 'RECHAZADA';

        return [
            'titulo' => "Aplazamiento {$estadoText}",
            'mensaje' => "Tu solicitud para aplazar el partido contra {$rival} ha sido {$this->aplazamiento->estado} por el administrador.",
            'estado' => $this->aplazamiento->estado,
            'id_partido' => $this->partido->id,
            'id_aplazamiento' => $this->aplazamiento->id
        ];
    }
}
