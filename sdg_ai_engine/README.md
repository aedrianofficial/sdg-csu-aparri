# SDG AI Engine

This is an AI-powered engine that analyzes PDF documents to detect Sustainable Development Goals (SDGs) and their targets that are relevant to the content.

## Features

- Analyzes PDF research papers and documents to identify relevant SDGs
- Detects specific SDG targets (subcategories) for more precise classification
- Returns confidence scores for each detected SDG and target
- Provides metadata about the document (word count, page count, etc.)
- Built with FastAPI for high performance and easy integration

## Requirements

- Python 3.8 or higher
- pip (Python package manager)
- Virtual environment (recommended)

## Directory Structure

```
sdg_ai_engine/
├── app/
│   ├── __init__.py
│   ├── main.py
│   ├── models.py
│   ├── sdg_analyzer.py
│   └── data/
│       ├── __init__.py
│       └── sdg_data.json
├── requirements.txt
├── setup.py
└── README.md
```

## Installation

1. Make sure you have Python 3.8+ installed:
   ```
   python --version
   ```

2. Run the setup script to create necessary directories:
   ```
   python setup.py
   ```

3. Create a virtual environment:
   ```
   python -m venv venv
   ```

4. Activate the virtual environment:
   - Windows:
     ```
     venv\Scripts\activate
     ```
   - Linux/Mac:
     ```
     source venv/bin/activate
     ```

5. Install dependencies:
   ```
   pip install -r requirements.txt
   ```

6. Download the spaCy language model:
   ```
   python -m spacy download en_core_web_md
   ```

## Running the Server

Start the FastAPI server:
```
uvicorn app.main:app --reload --host 0.0.0.0 --port 8000
```

The server will be available at http://localhost:8000

## API Documentation

Once the server is running, you can access the automatic API documentation at:
- http://localhost:8000/docs (Swagger UI)
- http://localhost:8000/redoc (ReDoc)

## API Endpoints

### Health Check
```
GET /
```
Returns the status of the API.

### Analyze PDF
```
POST /sdg/analyze
```
Analyzes a PDF document to detect relevant SDGs and targets.

**Parameters**:
- `file`: PDF file to analyze (form-data)

**Response**:
```json
{
  "matched_sdgs": [
    {
      "sdg_number": "05",
      "sdg_name": "Gender Equality",
      "confidence": 0.85,
      "force_match": false,
      "matched_keywords": ["gender equality", "women", "girls"],
      "subcategories": [
        {
          "subcategory": "5.1",
          "confidence": 0.76
        },
        {
          "subcategory": "5.5",
          "confidence": 0.68
        }
      ]
    },
    {
      "sdg_number": "04",
      "sdg_name": "Quality Education",
      "confidence": 0.72,
      "force_match": false,
      "matched_keywords": ["education", "learning", "school"],
      "subcategories": [
        {
          "subcategory": "4.5",
          "confidence": 0.65
        }
      ]
    }
  ],
  "metadata": {
    "word_count": 1250,
    "page_count": 5,
    "processing_time_ms": 980
  },
  "raw_text_sample": "This research examines the impact of gender-based policies..."
}
```

## Automatic Startup with Laravel

This AI engine is designed to work with the Laravel application. You can start it automatically using:
```
php artisan sdg:start-engine
``` 