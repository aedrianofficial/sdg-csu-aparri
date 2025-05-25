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
    source: str = Field("pdf-file", description="Source type of the document (pdf-file, text-file)")
    
class AnalysisResponse(BaseModel):
    """Response model for document analysis"""
    matched_sdgs: List[MatchedSDG] = Field(default_factory=list, description="List of matched SDGs")
    metadata: Metadata = Field(..., description="Document metadata")
    raw_text_sample: str = Field("", description="Sample of extracted text (for debugging)")

class GenderAnalysisResponse(BaseModel):
    """Response model for gender analysis endpoints"""
    benefits_men: bool = Field(
        default=False, 
        description="Whether the content benefits men/boys"
    )
    benefits_women: bool = Field(
        default=False, 
        description="Whether the content benefits women/girls"
    )
    benefits_all: bool = Field(
        default=False, 
        description="Whether the content benefits all genders"
    )
    addresses_gender_inequality: bool = Field(
        default=False, 
        description="Whether the content addresses gender inequality issues"
    )
    men_count: Optional[int] = Field(
        default=None, 
        description="Estimated count of men mentioned in the content"
    )
    women_count: Optional[int] = Field(
        default=None, 
        description="Estimated count of women mentioned in the content"
    )
    gender_notes: str = Field(
        default="", 
        description="Additional notes about gender impact"
    )
    confidence_score: float = Field(
        default=0.5, 
        description="Confidence level of the gender analysis"
    )
    key_terms: Optional[Dict[str, List[str]]] = Field(
        default=None, 
        description="Key gender-related terms found in the document"
    ) 