@echo off
echo Starting SDG AI-Powered Research Platform...

REM Start the SDG AI Engine in a new window
start "SDG AI Engine" cmd /k php artisan sdg:start-engine

echo Waiting for AI Engine to initialize...
timeout /t 5 /nobreak > nul

REM Start the Laravel application 
echo Starting Laravel application...
php artisan serve

echo Both services have been started. 