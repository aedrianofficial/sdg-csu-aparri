from fastapi import FastAPI, UploadFile, File, HTTPException
from fastapi.middleware.cors import CORSMiddleware
import os
import sys
from .models import AnalysisResponse
from .sdg_analyzer import SdgAnalyzer
import PyPDF2
import time

# Initialize FastAPI app
app = FastAPI(
    title="SDG AI Engine",
    description="AI engine for analyzing research documents and detecting relevant SDGs and targets",
    version="1.0.0"
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Allow all origins in development
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Initialize the SDG analyzer
sdg_analyzer = SdgAnalyzer()

@app.get("/")
async def read_root():
    """Health check endpoint"""
    return {
        "status": "ok",
        "message": "SDG AI Engine is running",
        "version": "1.0.0"
    }

@app.post("/sdg/analyze", response_model=AnalysisResponse)
async def analyze_document(file: UploadFile = File(...)):
    """
    Analyze a PDF document to detect relevant SDGs and targets
    """
    # Check if file is PDF
    if not file.filename.lower().endswith('.pdf'):
        raise HTTPException(status_code=400, detail="Only PDF files are supported")
    
    # Check file content type for additional validation
    if not file.content_type or 'application/pdf' not in file.content_type.lower():
        print(f"Warning: File has non-PDF content type: {file.content_type}", file=sys.stderr)
    
    # Generate a unique temp file path to avoid conflicts
    temp_file_path = f"temp_{int(time.time())}_{file.filename.replace(' ', '_')}"
    
    try:
        # Save file temporarily
        with open(temp_file_path, "wb") as f:
            try:
                # Read content in chunks to handle large files
                content = await file.read()
                if not content:
                    raise HTTPException(status_code=400, detail="Uploaded file is empty")
                f.write(content)
            except Exception as e:
                raise HTTPException(
                    status_code=400, 
                    detail=f"Failed to read uploaded file: {str(e)}"
                )
        
        # Check if file was written successfully
        if not os.path.exists(temp_file_path) or os.path.getsize(temp_file_path) == 0:
            raise HTTPException(
                status_code=500, 
                detail="Failed to save uploaded file"
            )
        
        try:
            # Analyze the document using our SDG analyzer
            results = sdg_analyzer.analyze_document(temp_file_path)
            
            # Remove temporary file
            if os.path.exists(temp_file_path):
                try:
                    os.remove(temp_file_path)
                except Exception as e:
                    print(f"Warning: Failed to delete temp file {temp_file_path}: {str(e)}", file=sys.stderr)
            
            # Check if no SDGs were matched
            if 'matched_sdgs' not in results.dict() or not results.dict()['matched_sdgs']:
                print("Warning: No SDGs matched for the document", file=sys.stderr)
            
            return results
        
        except (PyPDF2.errors.PdfReadError, PyPDF2.errors.EmptyFileError) as pdf_err:
            # Handle specific PDF-related errors
            error_message = f"Error reading PDF file: {str(pdf_err)}"
            print(error_message, file=sys.stderr)
            if os.path.exists(temp_file_path):
                try:
                    os.remove(temp_file_path)
                except:
                    pass
            raise HTTPException(status_code=400, detail=error_message)
            
        except ValueError as val_err:
            # Handle validation errors from our analyzer
            error_message = str(val_err)
            print(f"Validation error: {error_message}", file=sys.stderr)
            if os.path.exists(temp_file_path):
                try:
                    os.remove(temp_file_path)
                except:
                    pass
            raise HTTPException(status_code=400, detail=error_message)
            
        except Exception as e:
            # Handle other analysis errors
            error_message = f"Error analyzing document: {str(e)}"
            print(error_message, file=sys.stderr)
            if os.path.exists(temp_file_path):
                try:
                    os.remove(temp_file_path)
                except:
                    pass
            raise HTTPException(status_code=500, detail=error_message)
    
    except HTTPException:
        # Re-raise HTTP exceptions
        raise
    
    except Exception as e:
        # Clean up temp file if it exists
        if 'temp_file_path' in locals() and os.path.exists(temp_file_path):
            try:
                os.remove(temp_file_path)
            except:
                pass
        
        # Log the error
        error_message = f"Error processing uploaded file: {str(e)}"
        print(error_message, file=sys.stderr)
        
        # Return error response
        raise HTTPException(
            status_code=500,
            detail=error_message
        )

@app.post("/sdg/analyze-text", response_model=AnalysisResponse)
async def analyze_text_content(request: dict):
    """
    Analyze text content directly to detect relevant SDGs and targets
    """
    # Validate input
    if not request.get("text"):
        raise HTTPException(status_code=400, detail="Text content is required")
    
    text_content = request.get("text")
    
    # Generate a unique temp file path to avoid conflicts
    temp_file_path = f"temp_text_{int(time.time())}.txt"
    
    try:
        # Save text to a temporary file
        with open(temp_file_path, "w", encoding="utf-8") as f:
            f.write(text_content)
        
        # Check if file was written successfully
        if not os.path.exists(temp_file_path) or os.path.getsize(temp_file_path) == 0:
            raise HTTPException(
                status_code=500, 
                detail="Failed to save text content for analysis"
            )
        
        try:
            # Analyze the text using our SDG analyzer
            results = sdg_analyzer.analyze_document(temp_file_path, is_text=True)
            
            # Remove temporary file
            if os.path.exists(temp_file_path):
                try:
                    os.remove(temp_file_path)
                except Exception as e:
                    print(f"Warning: Failed to delete temp file {temp_file_path}: {str(e)}", file=sys.stderr)
            
            # Check if no SDGs were matched
            if 'matched_sdgs' not in results.dict() or not results.dict()['matched_sdgs']:
                print("Warning: No SDGs matched for the text content", file=sys.stderr)
            
            return results
            
        except Exception as e:
            # Handle analysis errors
            error_message = f"Error analyzing text content: {str(e)}"
            print(error_message, file=sys.stderr)
            if os.path.exists(temp_file_path):
                try:
                    os.remove(temp_file_path)
                except:
                    pass
            raise HTTPException(status_code=500, detail=error_message)
    
    except HTTPException:
        # Re-raise HTTP exceptions
        raise
    
    except Exception as e:
        # Clean up temp file if it exists
        if 'temp_file_path' in locals() and os.path.exists(temp_file_path):
            try:
                os.remove(temp_file_path)
            except:
                pass
        
        # Log the error
        error_message = f"Error processing text content: {str(e)}"
        print(error_message, file=sys.stderr)
        
        # Return error response
        raise HTTPException(
            status_code=500,
            detail=error_message
        ) 