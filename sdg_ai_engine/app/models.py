from pydantic import BaseModel, Field
from typing import List, Dict, Optional, Any

class SubCategory(BaseModel):
    """Represents an SDG subcategory/target"""
    subcategory: str = Field(..., description="Subcategory code (e.g., 1.1, 5.A)")
    confidence: float = Field(..., description="Confidence score (0-1)")
    
class MatchedSDG(BaseModel):
    """Represents a matched SDG with details"""
    sdg_number: str = Field(..., description="SDG number (01-17)")
    sdg_name: str = Field(..., description="Full name of the SDG")
    confidence: float = Field(..., description="Confidence score (0-1)")
    force_match: bool = Field(False, description="Whether this match should override others")
    matched_keywords: List[str] = Field(default_factory=list, description="Keywords that triggered this match")
    subcategories: List[SubCategory] = Field(default_factory=list, description="Matched subcategories/targets")

class Metadata(BaseModel):
    """Metadata about the analyzed document"""
    word_count: int = Field(..., description="Word count of the document")
    page_count: int = Field(..., description="Number of pages in the document")
    processing_time_ms: int = Field(..., description="Processing time in milliseconds")
    
class AnalysisResponse(BaseModel):
    """Response model for document analysis"""
    matched_sdgs: List[MatchedSDG] = Field(default_factory=list, description="List of matched SDGs")
    metadata: Metadata = Field(..., description="Document metadata")
    raw_text_sample: str = Field("", description="Sample of extracted text (for debugging)") 