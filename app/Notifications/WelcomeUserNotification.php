<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class WelcomeUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $password;

    // Pasamos el usuario y la contraseña temporal al constructor
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('¡Bienvenido a la plataforma!')
                    ->greeting('¡Hola, ' . $this->user->name . '!')
                    ->line('Tu cuenta ha sido creada exitosamente por el administrador.')
                    ->line('Aquí tienes tus credenciales de acceso:')
                    ->line('**Correo:** ' . $this->user->email)
                    ->line('**Contraseña:** ' . $this->password)
                    ->action('Iniciar Sesión', 'http://localhost:5173/login')
                    ->line('Te recomendamos cambiar tu contraseña una vez que ingreses por primera vez.');
    }
}
