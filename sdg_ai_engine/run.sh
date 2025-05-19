#!/bin/bash
echo "Starting SDG AI Engine..."

# Check if venv exists, if not create it
if [ ! -d "venv" ]; then
    echo "Creating virtual environment..."
    python3 -m venv venv
    source venv/bin/activate
    pip install --upgrade pip
    pip install -r requirements.txt
    python -m spacy download en_core_web_md
else
    source venv/bin/activate
fi

# Run the setup script
python setup.py

# Start the FastAPI server
echo "Starting FastAPI server at http://localhost:8000"
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000 