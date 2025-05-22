import uvicorn
import os
import sys

# Add the current directory to the path to import the app module
sys.path.insert(0, os.path.abspath(os.path.dirname(__file__)))

# Import the FastAPI app from the app package
from app.main import app

if __name__ == "__main__":
    # Run the server on port 8003
    uvicorn.run(app, host="0.0.0.0", port=8003) 