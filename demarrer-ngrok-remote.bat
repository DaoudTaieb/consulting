@echo off
cd /d "%~dp0"

echo ============================================
echo   MEUBLE DRISS - Acces via autre WiFi/Ngrok
echo ============================================
echo.
echo Les assets sont compiles (npm run build) pour que
echo le site fonctionne via ngrok depuis un autre appareil.
echo.

echo Construction des assets (JS/CSS)...
call npm run build
if errorlevel 1 (
    echo ERREUR: npm run build a echoue.
    pause
    exit /b 1
)
echo.
echo Demarrage de Laravel et Ngrok (pas de Vite en dev)...
echo.

start "Laravel - php artisan serve" cmd /k "php artisan serve"
start "Ngrok - tunnel 8000" cmd /k "ngrok http 8000"

echo.
echo Deux fenetres ouvertes :
echo - Laravel (port 8000)
echo - Ngrok (tunnel)
echo.
echo Ouvre l URL https affichee par ngrok depuis ton telephone ou autre PC.
echo Fermez les fenetres pour arreter.
echo.

timeout /t 3
