# AI-Powered SDG and Target Detection

This document outlines the implementation of automated Sustainable Development Goal (SDG) and SDG Target detection in the Research Management System.

## Overview

The system uses a Python-based AI engine to automatically analyze research abstracts and identify relevant SDGs and their targets (subcategories). This automation simplifies the submission process for researchers and ensures more accurate SDG classification.

## Components

### 1. AI Engine (Python FastAPI)

- **Location**: `sdg_ai_engine/` directory
- **Main Components**:
  - `app/main.py`: FastAPI application entry point
  - `app/routers/sdg.py`: API endpoints for SDG analysis
  - `app/services/sdg_classifier.py`: Top-level SDG detection
  - `app/services/sdg_subcategory_classifier.py`: Detailed target/subcategory detection

### 2. Laravel Integration

- **SdgAiService**: `app/Services/SdgAiService.php` - Service to communicate with the AI engine
- **API Controller**: `app/Http/Controllers/Api/SdgAiController.php` - Handles API requests
- **Frontend Integration**: Enhanced UI in create/edit forms for research submission

## How It Works

1. When a user uploads a research abstract file, it's sent to the `/api/sdg-ai/analyze` endpoint
2. The Laravel controller forwards the file to the Python AI engine
3. The AI engine extracts text from the file and performs keyword matching for both SDGs and targets
4. Detected SDGs and targets are returned to the frontend
5. The UI displays the results and auto-selects the detected items
6. Users can modify the AI selection if needed

## Key Features

- **Automatic SDG Detection**: Identifies relevant SDGs from research content
- **Automatic Target Detection**: Identifies specific SDG targets within each SDG
- **Enhanced UI**: Modern, user-friendly interface for viewing AI results
- **Fallback Mechanism**: Gracefully handles situations when the AI engine is unavailable
- **Manual Override**: Users can still manually select SDGs and targets if needed

## Implementation Details

### Frontend Changes

1. Added a prominent section for displaying AI-detected SDGs and targets
2. Improved the visual presentation of detected items using Bootstrap components
3. Hidden manual selection fields by default, showing them only when needed
4. Added a "Modify AI Selection" button for users who want to adjust the results

### Backend Changes

1. Enhanced error handling and logging throughout the system
2. Added CORS support to the AI engine for better browser compatibility
3. Implemented a fallback mechanism for when the AI engine is unavailable
4. Added comprehensive data validation and error handling

## Getting Started

To use the AI-powered SDG detection:

1. Start both the Laravel application and the AI engine using `start-app.bat`
2. Upload a research abstract file through the research submission form
3. The system will automatically detect and display relevant SDGs and targets
4. Review the AI-detected items and make any necessary adjustments
5. Submit the form to save the research with the detected SDGs and targets

## Future Improvements

- Enhance the AI model with more sophisticated NLP techniques
- Add support for more file formats
- Implement a training mechanism to improve detection accuracy over time
- Add a confidence score for each detected SDG and target 