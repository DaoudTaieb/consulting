@echo off
cd /d "%~dp0"

echo Demarrage du projet MEUBLE DRISS...
echo.

start "Laravel - php artisan serve" cmd /k "php artisan serve"
start "Vite - npm run dev" cmd /k "npm run dev"
start "Ngrok - tunnel 8000" cmd /k "ngrok http 8000"

echo.
echo Trois fenetres ouvertes :
echo - Laravel (port 8000)
echo - Vite (build front)
echo - Ngrok (tunnel vers port 8000)
echo.
echo Fermez les fenetres pour arreter les serveurs.

timeout /t 3
