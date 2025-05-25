@echo off
echo SDG AI Engine Launcher
echo ======================
echo.
echo This script will start the SDG AI Engine FastAPI server
echo API will be available at http://localhost:8003
echo.
echo Available endpoints:
echo - GET / : Health check
echo - POST /sdg/analyze : Analyze a PDF document for SDGs and targets
echo - POST /sdg/analyze-text : Analyze text content for SDGs and targets
echo - POST /gender/analyze : Analyze a PDF document for gender impacts
echo - POST /gender/analyze-text : Analyze text content for gender impacts
echo.
echo Press Ctrl+C to stop the server
echo.

set PYTHONPATH=%PYTHONPATH%;%CD%

rem Try to run from venv if it exists, otherwise use system Python
if exist "venv\Scripts\python.exe" (
    venv\Scripts\python app.py
) else if exist "venv_new\Scripts\python.exe" (
    venv_new\Scripts\python app.py
) else (
    python app.py
) 