import os
import re
import string
import nltk
import PyPDF2
from typing import List, Dict, Any, Optional, Set, Tuple
from .models import GenderAnalysisResponse

# Ensure NLTK data is available
nltk_data_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), "data", "nltk_data")
if not os.path.exists(nltk_data_path):
    os.makedirs(nltk_data_path, exist_ok=True)
nltk.data.path.append(nltk_data_path)

# Download required NLTK data if not already present
try:
    nltk.data.find('tokenizers/punkt')
except LookupError:
    nltk.download('punkt', download_dir=nltk_data_path)

class GenderAnalyzer:
    """
    Analyzes documents for gender impacts, based on mentions of men, women,
    and gender equality concepts.
    """
    
    def __init__(self):
        # Expanded gender-related keywords for more comprehensive analysis
        self.women_keywords = {
            'women', 'woman', 'female', 'females', 'girl', 'girls', 'mother', 'mothers',
            'maternal', 'maternity', 'feminine', 'pregnant', 'pregnancy', 'breastfeeding',
            'gynecological', 'uterine', 'ovarian', 'daughter', 'daughters', 'sisterhood',
            'businesswomen', 'breast cancer', 'menopause', 'menstruation', 'menstrual', 
            'ladies', 'lady', 'aunt', 'aunts', 'widow', 'widows', 'lesbian', 'lesbians', 
            'grandma', 'grandmother', 'granddaughters', 'sorority', 'wife', 'wives',
            'women entrepreneurs', 'women farmers', 'women workers', 'women leaders',
            'miss', 'ms', 'mrs', 'madam', 'she', 'her', 'hers'
        }
        
        self.men_keywords = {
            'men', 'man', 'male', 'males', 'boy', 'boys', 'father', 'fathers',
            'paternal', 'paternity', 'masculine', 'prostate', 'testicular', 'son', 'sons',
            'brotherhood', 'businessman', 'businessmen', 'grandfather', 'grandfathers', 
            'grandsons', 'gentleman', 'gentlemen', 'uncle', 'uncles', 'widower', 'widowers',
            'gay', 'gays', 'fraternity', 'husband', 'husbands', 'men entrepreneurs', 
            'men farmers', 'men workers', 'men leaders', 'mr', 'sir', 'he', 'him', 'his'
        }
        
        self.gender_equality_keywords = {
            'gender equality', 'gender inequality', 'gender gap', 'gender disparity',
            'gender discrimination', 'gender bias', 'gender sensitive', 'gender responsive',
            'gender mainstreaming', 'women empowerment', 'women\'s rights', 'gender parity',
            'gender-based', 'gender lens', 'gender analysis', 'gender balanced', 'feminism',
            'feminist', 'sexism', 'sexist', 'patriarchy', 'matriarchy', 'gender neutrality',
            'gender stereotype', 'gender role', 'gender pay gap', 'gender identity', 
            'women in leadership', 'glass ceiling', 'gender quota', 'women\'s movement',
            'gender perspective', 'equal opportunity', 'equal pay', 'gender equity', 
            'maternal rights', 'paternity leave', 'maternity leave', 'gender violence',
            'gender-inclusive', 'gender-neutral', 'gender-transformative', 'women\'s health',
            'reproductive rights', 'reproductive health', 'sexual harassment', 'women in stem'
        }
        
        # Context modifiers - words that might change the meaning when near gender terms
        self.negation_terms = {
            'not', 'no', 'none', 'never', 'neither', 'nor', 'doesn\'t', 'don\'t', 
            'cannot', 'can\'t', 'excluding', 'except', 'without'
        }
        
        # Terms indicating strong positive impact for gender equality
        self.positive_terms = {
            'empower', 'advance', 'promote', 'improve', 'enhance', 'strengthen', 
            'support', 'increase', 'benefit', 'help', 'prioritize', 'focus on'
        }
    
    def analyze_document(self, file_path: str, target_beneficiaries: str = "", is_text: bool = False) -> GenderAnalysisResponse:
        """
        Analyze a document for gender impacts.
        
        Args:
            file_path: Path to the file to analyze
            target_beneficiaries: Additional text about target beneficiaries
            is_text: If True, treat file_path as containing a text file instead of PDF
            
        Returns:
            GenderAnalysisResponse object with analysis results
        """
        try:
            # Extract text from the file
            text = self._extract_text_from_file(file_path, is_text)
            
            # Add target beneficiaries text if provided
            full_text = text
            if target_beneficiaries:
                full_text += ' ' + target_beneficiaries
            
            # Convert to lowercase for case-insensitive matching
            full_text = full_text.lower()
            
            # Create an array of sentences for context analysis
            sentences = self._split_into_sentences(full_text)
            
            # Initialize results with default values
            results = GenderAnalysisResponse(
                benefits_men=False,
                benefits_women=False,
                benefits_all=False,
                addresses_gender_inequality=False,
                men_count=None,
                women_count=None,
                gender_notes="",
                confidence_score=0.5,
                key_terms={"women": [], "men": [], "equality": []}
            )
            
            # Analyze for women beneficiaries with context
            women_analysis = self._analyze_keywords_with_context(full_text, sentences, self.women_keywords)
            results.benefits_women = women_analysis['positive_mentions'] > 0
            results.key_terms["women"] = women_analysis['key_terms'][:5]  # Limit to top 5 terms
            
            # Analyze for men beneficiaries with context
            men_analysis = self._analyze_keywords_with_context(full_text, sentences, self.men_keywords)
            results.benefits_men = men_analysis['positive_mentions'] > 0
            results.key_terms["men"] = men_analysis['key_terms'][:5]  # Limit to top 5 terms
            
            # Analyze for gender equality focus with context
            equality_analysis = self._analyze_keywords_with_context(full_text, sentences, self.gender_equality_keywords)
            results.addresses_gender_inequality = equality_analysis['positive_mentions'] > 0
            results.key_terms["equality"] = equality_analysis['key_terms'][:5]  # Limit to top 5 terms
            
            # Calculate confidence score based on analysis results
            results.confidence_score = self._calculate_confidence_score(women_analysis, men_analysis, equality_analysis)
            
            # If both men and women are mentioned, mark as benefiting all
            results.benefits_all = (results.benefits_men and results.benefits_women)
            
            # Extract counts if available
            results.women_count = self._extract_count(full_text, self.women_keywords)
            results.men_count = self._extract_count(full_text, self.men_keywords)
            
            # Prepare gender notes
            notes = []
            
            if results.benefits_women and not results.benefits_men:
                notes.append("This primarily focuses on women/girls as beneficiaries.")
                if women_analysis['positive_mentions'] > 5:
                    notes.append(f"There is a strong emphasis on women's involvement (mentioned {women_analysis['total_mentions']} times).")
            elif results.benefits_men and not results.benefits_women:
                notes.append("This primarily focuses on men/boys as beneficiaries.")
                if men_analysis['positive_mentions'] > 5:
                    notes.append(f"There is a strong emphasis on men's involvement (mentioned {men_analysis['total_mentions']} times).")
            elif results.benefits_men and results.benefits_women:
                notes.append("This considers both men/boys and women/girls as beneficiaries.")
                
                # Compare mentions to see if there's a gender balance
                women_mentions = women_analysis['total_mentions']
                men_mentions = men_analysis['total_mentions']
                
                if women_mentions > men_mentions * 2:
                    notes.append(f"However, there appears to be a stronger focus on women (mentioned {women_mentions} times vs. men mentioned {men_mentions} times).")
                elif men_mentions > women_mentions * 2:
                    notes.append(f"However, there appears to be a stronger focus on men (mentioned {men_mentions} times vs. women mentioned {women_mentions} times).")
                else:
                    notes.append("There appears to be a relatively balanced focus on both genders.")
            else:
                notes.append("This does not specifically mention any gender groups. Consider if the project/research is truly gender-neutral or if gender aspects should be addressed.")
            
            if results.addresses_gender_inequality:
                if equality_analysis['positive_mentions'] > 3:
                    notes.append("This strongly addresses gender inequality issues with multiple references.")
                else:
                    notes.append("This addresses gender inequality issues.")
                
                if equality_analysis['key_terms']:
                    notes.append("Gender equality terms found: " + ", ".join(equality_analysis['key_terms'][:3]) + ".")
                
                # Add sample sentences mentioning gender equality
                if equality_analysis['sample_sentences'] and len(equality_analysis['sample_sentences']) > 0:
                    notes.append(f"Example: \"{equality_analysis['sample_sentences'][0]}\"")
            
            if results.women_count is not None:
                notes.append(f"Approximately {results.women_count} women/girls mentioned.")
            
            if results.men_count is not None:
                notes.append(f"Approximately {results.men_count} men/boys mentioned.")
            
            # Add confidence level to notes
            if results.confidence_score < 0.4:
                notes.append("Note: Low confidence in this gender analysis. Consider manual review.")
            elif results.confidence_score > 0.8:
                notes.append("Note: High confidence in this gender analysis.")
            
            results.gender_notes = " ".join(notes)
            
            return results
            
        except Exception as e:
            # In case of errors, return basic results with an error note
            return GenderAnalysisResponse(
                benefits_men=False,
                benefits_women=False,
                benefits_all=False,
                addresses_gender_inequality=False,
                men_count=None,
                women_count=None,
                gender_notes=f"Error analyzing gender impacts: {str(e)}",
                confidence_score=0.2,
                key_terms={"women": [], "men": [], "equality": []}
            )
    
    def _extract_text_from_file(self, file_path: str, is_text: bool = False) -> str:
        """Extract text from a file (PDF or text)"""
        if is_text:
            with open(file_path, 'r', encoding='utf-8') as f:
                return f.read()
        else:
            # Parse PDF
            with open(file_path, 'rb') as f:
                pdf_reader = PyPDF2.PdfReader(f)
                text = ""
                for page in pdf_reader.pages:
                    text += page.extract_text() or ""
                return text
    
    def _split_into_sentences(self, text: str) -> List[str]:
        """Split text into sentences for context analysis"""
        return nltk.sent_tokenize(text)
    
    def _contains_negation(self, sentence: str) -> bool:
        """Check if a sentence contains negation terms"""
        return any(neg_term in sentence.split() for neg_term in self.negation_terms)
    
    def _contains_positive_terms(self, sentence: str) -> bool:
        """Check if a sentence contains positive impact terms"""
        return any(pos_term in sentence for pos_term in self.positive_terms)
    
    def _analyze_keywords_with_context(self, full_text: str, sentences: List[str], keywords: Set[str]) -> Dict[str, Any]:
        """
        Analyze text for keywords with context analysis
        
        Args:
            full_text: The full text to analyze
            sentences: List of sentences for context analysis
            keywords: Set of keywords to search for
            
        Returns:
            Dictionary with analysis results
        """
        results = {
            'total_mentions': 0,
            'positive_mentions': 0,
            'negative_mentions': 0,
            'neutral_mentions': 0,
            'key_terms': [],
            'sample_sentences': []
        }
        
        # Find matches in the full text first (for efficiency)
        matches = []
        for keyword in keywords:
            if keyword in full_text:
                matches.append(keyword)
                
                # Only add unique keywords to key_terms (avoid duplicates like "women" and "woman")
                if keyword not in results['key_terms']:
                    base_word = keyword.split()[0]  # Get base word (e.g., "women" from "women entrepreneurs")
                    if not any(base_word in term for term in results['key_terms']):
                        results['key_terms'].append(keyword)
        
        # If no matches, return early
        if not matches:
            return results
        
        # Track total mentions
        for match in matches:
            # Count occurrences of each match, considering word boundaries
            pattern = r'\b' + re.escape(match) + r'\b'
            count = len(re.findall(pattern, full_text))
            results['total_mentions'] += count
        
        # Initialize positive mentions to 0
        results['positive_mentions'] = 0
        
        # Analyze sentences containing matches for context
        for sentence in sentences:
            has_match = any(keyword in sentence.lower() for keyword in keywords)
            if has_match:
                # Add to sample sentences (limit to 3)
                if len(results['sample_sentences']) < 3:
                    results['sample_sentences'].append(sentence)
                
                # Check for negation and positive terms
                has_negation = self._contains_negation(sentence.lower())
                has_positive = self._contains_positive_terms(sentence.lower())
                
                if has_negation:
                    results['negative_mentions'] += 1
                elif has_positive:
                    results['positive_mentions'] += 1
                else:
                    results['neutral_mentions'] += 1
                    # Neutral mentions still count partially towards positive
                    results['positive_mentions'] += 0.5

        # Ensure positive_mentions is an integer
        results['positive_mentions'] = int(results['positive_mentions'])
        
        return results
    
    def _extract_count(self, text: str, keywords: Set[str]) -> Optional[int]:
        """
        Extract numeric counts associated with keywords
        e.g., "100 women" or "affecting 50 men"
        
        Args:
            text: Text to search in
            keywords: Keywords to find counts for
            
        Returns:
            Extracted count or None if not found
        """
        count_patterns = [
            r'(\d+)\s+(' + '|'.join(keywords) + r')',  # e.g., "50 women"
            r'(' + '|'.join(keywords) + r')\s+of\s+(\d+)',  # e.g., "women of 50"
            r'affecting\s+(\d+)\s+(' + '|'.join(keywords) + r')',  # e.g., "affecting 50 women"
            r'benefiting\s+(\d+)\s+(' + '|'.join(keywords) + r')',  # e.g., "benefiting 50 women"
            r'serving\s+(\d+)\s+(' + '|'.join(keywords) + r')',  # e.g., "serving 50 women"
        ]
        
        for pattern in count_patterns:
            matches = re.findall(pattern, text)
            if matches:
                for match in matches:
                    # Extract the numeric part
                    numeric_parts = [part for part in match if part.isdigit()]
                    if numeric_parts:
                        return int(numeric_parts[0])
        
        return None
    
    def _calculate_confidence_score(self, women_analysis: Dict[str, Any], men_analysis: Dict[str, Any], 
                                    equality_analysis: Dict[str, Any]) -> float:
        """
        Calculate confidence score based on analysis results
        
        Args:
            women_analysis: Results of women keywords analysis
            men_analysis: Results of men keywords analysis
            equality_analysis: Results of gender equality keywords analysis
            
        Returns:
            Confidence score between 0.0 and 1.0
        """
        # Base confidence starts at 0.5 (medium)
        confidence = 0.5
        
        # Increase confidence based on number of mentions
        total_mentions = women_analysis['total_mentions'] + men_analysis['total_mentions'] + equality_analysis['total_mentions']
        if total_mentions > 20:
            confidence += 0.2
        elif total_mentions > 10:
            confidence += 0.1
        elif total_mentions < 3:
            confidence -= 0.2
        
        # Increase confidence if we have clear positive mentions
        positive_mentions = women_analysis['positive_mentions'] + men_analysis['positive_mentions'] + equality_analysis['positive_mentions']
        if positive_mentions > 5:
            confidence += 0.1
        
        # Increase confidence if we have gender equality terms (these are more specific)
        if equality_analysis['total_mentions'] > 0:
            confidence += 0.1
        
        # Adjust for balance between men and women mentions
        if women_analysis['total_mentions'] > 0 and men_analysis['total_mentions'] > 0:
            # More balanced = more confident
            ratio = min(women_analysis['total_mentions'], men_analysis['total_mentions']) / max(1, max(women_analysis['total_mentions'], men_analysis['total_mentions']))
            if ratio > 0.3:  # Reasonably balanced
                confidence += 0.1
        
        # Cap confidence between 0.1 and 1.0
        return max(0.1, min(1.0, confidence))