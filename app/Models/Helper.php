<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    public static function array_contains($array, $needle)
    {
        foreach ($array as $item) {

            if (str_contains($item, $needle)) {

                return true;
            }
        }

        return false;
    }

    public static function urlIsImage($url): bool
    {
        // Validar que $url no esté vacío y sea una cadena
        if (empty($url) || ! is_string($url)) {
            return false;
        }

        $requestUri = request()->getRequestUri() ?? ''; // <= compatible con Swoole

        if (strpos($requestUri, $url) && request()->get('page') != 1) {
            // Validar que el archivo exista antes de intentar abrirlo
            if (! file_exists($url)) {
                return false;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE); // Abre la información de tipo MIME
            $tipo = finfo_file($finfo, $url);
            finfo_close($finfo);

            if (is_string($tipo) && strpos($tipo, 'image/') === 0) {
                return true;
            }
        }

        return false;
    }
}
