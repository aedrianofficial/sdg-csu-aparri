@echo off
echo Starting SDG AI Engine...
cd %~dp0
cd sdg_ai_engine
start /B python -m uvicorn app.main:app --host 0.0.0.0 --port 8003
echo SDG AI Engine started on http://localhost:8003
echo Press any key to stop the server...
pause > nul
taskkill /FI "WINDOWTITLE eq C:*python -m uvicorn app.main:app --host 0.0.0.0 --port 8003*" /F
echo SDG AI Engine stopped. 