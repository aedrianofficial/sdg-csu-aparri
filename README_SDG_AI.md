# SDG AI Analysis System

## Overview

The SDG AI Analysis system uses a consistent pipeline for analyzing both research documents (PDF files) and project descriptions (text) to identify relevant Sustainable Development Goals (SDGs).

## Architecture

### Python-based AI Engine (Port 8003)

- **API Endpoints:**
  - `/sdg/analyze`: Accepts PDF uploads for research
  - `/sdg/analyze-text`: Accepts plain text for projects/programs

- **Analysis Process:**
  - Uses natural language processing to identify SDG relevance
  - Limits results to the top 3 most relevant SDGs
  - Identifies specific SDG targets within each goal
  - Returns confidence scores for each match

### Laravel Backend

- **API Endpoints:**
  - `/api/sdg-ai/analyze`: Accepts both PDF uploads and text input
  - Automatically routes to the appropriate AI engine endpoint

- **Controllers:**
  - `SdgAiController`: Handles API requests and normalizes results
  - `Auth/ProjectController` and `Contributor/ProjectController`: Both use the same `/api/sdg-ai/analyze` endpoint

## Key Features

1. **Unified Analysis:** The same AI engine and algorithm is used for all SDG analysis
2. **Consistent Results:** Research and project analysis return identically structured results
3. **Fallback Mechanism:** If the AI service is unavailable, a keyword-based fallback mechanism ensures functionality

## Usage

1. **For Research Documents:**
   - Upload a PDF file to `/api/sdg-ai/analyze`
   - The document is processed by the AI engine
   - The top 3 most relevant SDGs are returned with targets

2. **For Projects/Programs:**
   - Send the project title and description as text to `/api/sdg-ai/analyze`
   - The same AI engine processes the text
   - Identical format of results as with research documents

## Starting the System

1. Start the SDG AI Engine: `cd sdg_ai_engine && python -m app.main`
2. Start Laravel development server: `php artisan serve` 