<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Symfony\Component\Mime\Part\DataPart;

class EmbedLogoInEmails
{
    /**
     * Identificador único (CID) de la imagen embebida.
     * Debe contener un "@" según los requisitos de Symfony Mime.
     */
    protected const CID = 'logo-pnfi@sgai';

    /**
     * Maneja el evento MessageSending: incrusta el logo como imagen embebida
     * para que se muestre en cualquier cliente de correo, sin depender de
     * que el servidor sea accesible públicamente.
     */
    public function handle(MessageSending $event): void
    {
        $logoPath = $this->optimizedLogoPath();

        if (! file_exists($logoPath)) {
            return;
        }

        $html = $event->message->getHtmlBody();

        if ($html === null || ! str_contains($html, 'images/logo-pnfi.png')) {
            return;
        }

        // Reemplaza la URL generada por asset() (ej: http://127.0.0.1:8000/images/logo-pnfi.png)
        // por la referencia CID de la imagen embebida.
        $event->message->html(
            preg_replace(
                '#(?:https?://[^"\']*)?/images/logo-pnfi\.png#',
                'cid:'.self::CID,
                $html
            )
        );

        // Incrusta la imagen como parte inline con el CID correspondiente.
        $event->message->addPart(
            DataPart::fromPath($logoPath, 'logo-pnfi.png', 'image/png')
                ->asInline()
                ->setContentId(self::CID)
        );
    }

    /**
     * Devuelve la ruta de la versión optimizada del logo (200x200),
     * generándola a partir de la original si aún no existe.
     */
    protected function optimizedLogoPath(): string
    {
        $path = storage_path('app/logo-pnfi-email.png');

        if (! file_exists($path)) {
            $this->generateOptimizedLogo($path);
        }

        return $path;
    }

    /**
     * Redimensiona el logo original (1024x1024, ~1.5MB) a 200x200 PNG
     * para que los correos no pesen demasiado.
     */
    protected function generateOptimizedLogo(string $destination): void
    {
        $source = public_path('images/logo-pnfi.png');

        if (! file_exists($source)) {
            return;
        }

        [$width, $height] = getimagesize($source);

        $size = 200;
        $src = imagecreatefrompng($source);
        $dst = imagecreatetruecolor($size, $size);

        // Preservar la transparencia del PNG
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $size, $size, $width, $height);

        imagepng($dst, $destination);
        imagedestroy($src);
        imagedestroy($dst);
    }
}
