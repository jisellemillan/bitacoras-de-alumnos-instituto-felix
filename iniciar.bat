@echo off
setlocal
title Bitacoras Instituto Felix - Servidor Laravel
echo ===================================================
echo   Iniciando Sistema de Bitacoras - Instituto Felix
echo ===================================================
echo.

set "PHP84=C:\php84\php-8.4.26-nts-Win32-vs17-x64"
set "HERD_BIN=C:\Users\%USERNAME%\.config\herd-lite\bin"

if exist "%PHP84%\php.exe" (
    set "PATH=%PHP84%;%HERD_BIN%;C:\Users\roble\.config\herd-lite\bin;%PATH%"
) else (
    set "PATH=%HERD_BIN%;C:\Users\roble\.config\herd-lite\bin;%PATH%"
)

netstat -ano | findstr /R /C:":3306 .*LISTENING" >nul
if errorlevel 1 (
    echo [INFO] Iniciando servidor MySQL de XAMPP...
    if exist "C:\xampp\mysql\bin\mysqld.exe" (
        start "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file=C:\xampp\mysql\bin\my.ini --standalone
        timeout /t 3 /nobreak >nul
    ) else (
        echo [AVISO] Inicia MySQL desde el panel de XAMPP.
    )
) else (
    echo [OK] Base de datos MySQL activa en el puerto 3306.
)

:: Verificar que las tablas esten creadas
php artisan migrate --force >nul 2>&1

start "" cmd /c "timeout /t 2 /nobreak >nul & start http://127.0.0.1:8000"

echo.
echo [OK] Servidor iniciado. Accesible en:
echo   - Local:        http://127.0.0.1:8000
echo   - Red local:    http://0.0.0.0:8000 (usa la IP de tu PC)
echo.
echo Presiona Ctrl+C para detener el servidor.
echo.
php artisan serve --host=0.0.0.0 --port=8000