@echo off
:: ============================================================
::  Instalador del servicio BluewineAgent
::  Ejecutar como Administrador
:: ============================================================

:: Verificar que se ejecuta como Administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo.
    echo  ERROR: Debes ejecutar este archivo como Administrador.
    echo  Clic derecho sobre el archivo ^> "Ejecutar como administrador"
    echo.
    pause
    exit /b 1
)

:: Verificar que nssm.exe existe en esta carpeta
if not exist "%~dp0nssm.exe" (
    echo.
    echo  ERROR: No se encontro nssm.exe en esta carpeta.
    echo  Descargalo desde https://nssm.cc/download ^(version 64-bit^)
    echo  y colocalo junto a este archivo.
    echo.
    pause
    exit /b 1
)

:: Verificar que el agente existe
if not exist "%~dp0usb-print-agent.exe" (
    echo.
    echo  ERROR: No se encontro usb-print-agent.exe en esta carpeta.
    echo.
    pause
    exit /b 1
)

set SERVICE_NAME=BluewineAgent
set INSTALL_DIR=%~dp0

echo.
echo  ============================================================
echo   Instalando servicio: %SERVICE_NAME%
echo   Carpeta: %INSTALL_DIR%
echo  ============================================================
echo.

:: Detener y eliminar servicio anterior si existe
sc query "%SERVICE_NAME%" >nul 2>&1
if %errorLevel% equ 0 (
    echo  Eliminando instalacion anterior...
    "%INSTALL_DIR%nssm.exe" stop "%SERVICE_NAME%" >nul 2>&1
    "%INSTALL_DIR%nssm.exe" remove "%SERVICE_NAME%" confirm >nul 2>&1
    timeout /t 2 /nobreak >nul
)

:: Instalar el servicio
echo  Instalando servicio...
"%INSTALL_DIR%nssm.exe" install "%SERVICE_NAME%" "%INSTALL_DIR%usb-print-agent.exe"
if %errorLevel% neq 0 (
    echo.
    echo  ERROR: No se pudo instalar el servicio.
    pause
    exit /b 1
)

:: Configurar directorio de trabajo (para que lea el .env)
"%INSTALL_DIR%nssm.exe" set "%SERVICE_NAME%" AppDirectory "%INSTALL_DIR%"

:: Configurar reinicio automatico si falla
"%INSTALL_DIR%nssm.exe" set "%SERVICE_NAME%" AppExit Default Restart
"%INSTALL_DIR%nssm.exe" set "%SERVICE_NAME%" AppRestartDelay 3000

:: Guardar logs en la misma carpeta
"%INSTALL_DIR%nssm.exe" set "%SERVICE_NAME%" AppStdout "%INSTALL_DIR%agente.log"
"%INSTALL_DIR%nssm.exe" set "%SERVICE_NAME%" AppStderr "%INSTALL_DIR%agente.log"
"%INSTALL_DIR%nssm.exe" set "%SERVICE_NAME%" AppRotateFiles 1
"%INSTALL_DIR%nssm.exe" set "%SERVICE_NAME%" AppRotateBytes 1048576

:: Iniciar el servicio
echo  Iniciando servicio...
"%INSTALL_DIR%nssm.exe" start "%SERVICE_NAME%"
if %errorLevel% neq 0 (
    echo.
    echo  ERROR: El servicio se instalo pero no pudo iniciarse.
    echo  Revisa el archivo agente.log para mas detalles.
    pause
    exit /b 1
)

echo.
echo  ============================================================
echo   Servicio instalado e iniciado correctamente.
echo.
echo   El agente arrancara automaticamente con Windows.
echo   Los logs se guardan en: %INSTALL_DIR%agente.log
echo  ============================================================
echo.
pause
