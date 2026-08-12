<?php

namespace App\Notifications\Concerns;

use Symfony\Component\Mime\Email;

trait WithEmailMetadata
{
    /**
     * Define los metadatos que se agregarán como headers personalizados al correo.
     * Sobrescribe este método en tu notificación para definir metadatos específicos.
     *
     * @return array<string, string> Array de metadatos donde la key es el nombre del header (sin el prefijo X-)
     */
    protected function emailMetadata(): array
    {
        return [];
    }

    /**
     * Agrega los metadatos como headers personalizados al mensaje Symfony.
     * Se aplica automáticamente en el método toMail() usando withSymfonyMessage()
     */
    protected function applyEmailMetadata(Email $message): void
    {
        $metadata = $this->emailMetadata();

        if (empty($metadata)) {
            return;
        }

        $headers = $message->getHeaders();

        foreach ($metadata as $key => $value) {
            // Asegurar que el valor no esté vacío
            if (empty($value)) {
                continue;
            }

            // Convertir el valor a string
            $value = (string) $value;

            // Agregar prefijo X- si no lo tiene
            $headerName = str_starts_with($key, 'X-') ? $key : 'X-'.$key;

            // Agregar header personalizado
            $headers->addTextHeader($headerName, $value);
        }
    }
}
