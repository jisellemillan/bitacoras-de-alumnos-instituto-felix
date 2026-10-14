<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| web.php — Solo incluye los archivos de rutas por módulo
|--------------------------------------------------------------------------
*/

// Autenticación (Breeze)
require __DIR__ . '/auth.php';

// Módulos de la aplicación
require __DIR__ . '/modules/dashboard.php';
require __DIR__ . '/modules/students.php';
require __DIR__ . '/modules/agreements.php';
require __DIR__ . '/modules/calendar.php';
require __DIR__ . '/modules/reports.php';
require __DIR__ . '/modules/access-requests.php';
require __DIR__ . '/modules/admin.php';
