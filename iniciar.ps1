Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "  Iniciando Sistema de Bitacoras - Instituto Felix" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan

$php84 = "C:\php84\php-8.4.26-nts-Win32-vs17-x64"
$herdPath = "C:\Users\$env:USERNAME\.config\herd-lite\bin"

if (Test-Path "$php84\php.exe") {
    $env:PATH = "$php84;$herdPath;$env:PATH"
} elseif (Test-Path "$herdPath\php.exe") {
    $env:PATH = "$herdPath;$env:PATH"
}

$mysqlListening = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -InformationLevel Quiet
if (-not $mysqlListening) {
    Write-Host "[INFO] Iniciando servidor MySQL de XAMPP..." -ForegroundColor Yellow
    if (Test-Path "C:\xampp\mysql\bin\mysqld.exe") {
        Start-Process "C:\xampp\mysql\bin\mysqld.exe" -ArgumentList "--defaults-file=C:\xampp\mysql\bin\my.ini --standalone" -WindowStyle Hidden
        Start-Sleep -Seconds 3
    } else {
        Write-Host "[AVISO] Por favor inicia MySQL desde el panel de XAMPP." -ForegroundColor Red
    }
} else {
    Write-Host "[OK] Base de datos MySQL activa en el puerto 3306." -ForegroundColor Green
}

Start-Process "http://127.0.0.1:8000"

Write-Host "[OK] Servidor iniciado en http://127.0.0.1:8000" -ForegroundColor Green
Write-Host "Presiona Ctrl+C para detener el servidor." -ForegroundColor Yellow
php artisan serve