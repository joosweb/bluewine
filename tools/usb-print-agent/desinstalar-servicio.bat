@echo off
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo.
    echo  ERROR: Debes ejecutar este archivo como Administrador.
    pause
    exit /b 1
)

set SERVICE_NAME=BluewineAgent
set INSTALL_DIR=%~dp0

echo.
echo  Deteniendo y eliminando el servicio %SERVICE_NAME%...
echo.

"%INSTALL_DIR%nssm.exe" stop "%SERVICE_NAME%" >nul 2>&1
"%INSTALL_DIR%nssm.exe" remove "%SERVICE_NAME%" confirm

echo.
echo  Servicio eliminado.
echo.
pause
