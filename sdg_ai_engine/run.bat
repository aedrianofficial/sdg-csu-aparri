@echo off
echo Starting SDG AI Engine...

REM Check if venv exists, if not create it
if not exist venv (
    echo Creating virtual environment...
    python -m venv venv
    call venv\Scripts\activate
    pip install --upgrade pip
    pip install -r requirements.txt
    python -m spacy download en_core_web_md
) else (
    call venv\Scripts\activate
)

REM Run the setup script
python setup.py

REM Start the FastAPI server
echo Starting FastAPI server at http://localhost:8003
uvicorn app.main:app --reload --host 0.0.0.0 --port 8003 