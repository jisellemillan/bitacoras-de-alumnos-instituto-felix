<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class PdfStyles
{
    public static function get(string $sheet = 'pdf-base'): string
    {
        $path = resource_path("css/pdf/{$sheet}.css");

        if (File::exists($path)) {
            return File::get($path);
        }

        return '';
    }
}
