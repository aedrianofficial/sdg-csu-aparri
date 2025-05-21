<?php

namespace App\Services;

use App\Models\GenderImpact;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;

class GenderAnalysisService
{
    // Expanded gender-related keywords for more comprehensive analysis
    protected $womenKeywords = [
        'women', 'woman', 'female', 'females', 'girl', 'girls', 'mother', 'mothers',
        'maternal', 'maternity', 'feminine', 'pregnant', 'pregnancy', 'breastfeeding',
        'gynecological', 'uterine', 'ovarian', 'daughter', 'daughters', 'sisterhood',
        'businesswomen', 'breast cancer', 'menopause', 'menstruation', 'menstrual', 
        'ladies', 'lady', 'aunt', 'aunts', 'widow', 'widows', 'lesbian', 'lesbians', 
        'grandma', 'grandmother', 'granddaughters', 'sorority', 'wife', 'wives',
        'women entrepreneurs', 'women farmers', 'women workers', 'women leaders',
        'miss', 'ms', 'mrs', 'madam', 'she', 'her', 'hers'
    ];
    
    protected $menKeywords = [
        'men', 'man', 'male', 'males', 'boy', 'boys', 'father', 'fathers',
        'paternal', 'paternity', 'masculine', 'prostate', 'testicular', 'son', 'sons',
        'brotherhood', 'businessman', 'businessmen', 'grandfather', 'grandfathers', 
        'grandsons', 'gentleman', 'gentlemen', 'uncle', 'uncles', 'widower', 'widowers',
        'gay', 'gays', 'fraternity', 'husband', 'husbands', 'men entrepreneurs', 
        'men farmers', 'men workers', 'men leaders', 'mr', 'sir', 'he', 'him', 'his'
    ];
    
    protected $genderEqualityKeywords = [
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
    ];

    // Context modifiers - words that might change the meaning when near gender terms
    protected $negationTerms = [
        'not', 'no', 'none', 'never', 'neither', 'nor', 'doesn\'t', 'don\'t', 
        'cannot', 'can\'t', 'excluding', 'except', 'without'
    ];
    
    // Terms indicating strong positive impact for gender equality
    protected $strongPositiveTerms = [
        'empower', 'advance', 'promote', 'improve', 'enhance', 'strengthen', 
        'support', 'increase', 'benefit', 'help', 'prioritize', 'focus on'
    ];

    /**
     * Analyze a research file for gender impacts
     *
     * @param UploadedFile $file The uploaded research file
     * @param string|null $targetBeneficiaries Additional text about target beneficiaries
     * @return array Gender impact analysis results
     */
    public function analyzeGenderImpacts(UploadedFile $file, ?string $targetBeneficiaries = null)
    {
        // Initialize results
        $results = [
            'benefits_men' => false,
            'benefits_women' => false,
            'benefits_all' => false,
            'addresses_gender_inequality' => false,
            'men_count' => null,
            'women_count' => null,
            'gender_notes' => '',
            'extracted_text' => '',
            'confidence_score' => 0.5, // Default medium confidence
            'context_analysis' => [] // Store context analysis results
        ];

        try {
            // Extract text from the file
            $fileText = $this->extractTextFromFile($file);
            $results['extracted_text'] = $fileText;
            
            // Add target beneficiaries text if provided
            $fullText = $fileText;
            if ($targetBeneficiaries) {
                $fullText .= ' ' . $targetBeneficiaries;
            }
            
            // Convert to lowercase for case-insensitive matching
            $fullText = mb_strtolower($fullText);
            
            // Create an array of sentences for context analysis
            $sentences = $this->splitIntoSentences($fullText);
            
            // Analyze for women beneficiaries with context
            $womenAnalysis = $this->analyzeKeywordsWithContext($fullText, $sentences, $this->womenKeywords);
            $results['benefits_women'] = $womenAnalysis['positive_mentions'] > 0;
            $results['context_analysis']['women'] = $womenAnalysis;
            
            // Analyze for men beneficiaries with context
            $menAnalysis = $this->analyzeKeywordsWithContext($fullText, $sentences, $this->menKeywords);
            $results['benefits_men'] = $menAnalysis['positive_mentions'] > 0;
            $results['context_analysis']['men'] = $menAnalysis;
            
            // Analyze for gender equality focus with context
            $equalityAnalysis = $this->analyzeKeywordsWithContext($fullText, $sentences, $this->genderEqualityKeywords);
            $results['addresses_gender_inequality'] = $equalityAnalysis['positive_mentions'] > 0;
            $results['context_analysis']['equality'] = $equalityAnalysis;
            
            // Calculate confidence score based on analysis results
            $results['confidence_score'] = $this->calculateConfidenceScore($womenAnalysis, $menAnalysis, $equalityAnalysis);
            
            // If both men and women are mentioned, mark as benefiting all
            $results['benefits_all'] = ($results['benefits_men'] && $results['benefits_women']);
            
            // Extract counts if available
            $results['women_count'] = $this->extractCount($fullText, $this->womenKeywords);
            $results['men_count'] = $this->extractCount($fullText, $this->menKeywords);
            
            // Prepare gender notes
            $notes = [];
            
            if ($results['benefits_women'] && !$results['benefits_men']) {
                $notes[] = "This primarily focuses on women/girls as beneficiaries.";
                if ($womenAnalysis['positive_mentions'] > 5) {
                    $notes[] = "There is a strong emphasis on women's involvement (mentioned " . $womenAnalysis['total_mentions'] . " times).";
                }
            } elseif ($results['benefits_men'] && !$results['benefits_women']) {
                $notes[] = "This primarily focuses on men/boys as beneficiaries.";
                if ($menAnalysis['positive_mentions'] > 5) {
                    $notes[] = "There is a strong emphasis on men's involvement (mentioned " . $menAnalysis['total_mentions'] . " times).";
                }
            } elseif ($results['benefits_men'] && $results['benefits_women']) {
                $notes[] = "This considers both men/boys and women/girls as beneficiaries.";
                
                // Compare mentions to see if there's a gender balance
                $womenMentions = $womenAnalysis['total_mentions'];
                $menMentions = $menAnalysis['total_mentions'];
                
                if ($womenMentions > $menMentions * 2) {
                    $notes[] = "However, there appears to be a stronger focus on women (mentioned $womenMentions times vs. men mentioned $menMentions times).";
                } elseif ($menMentions > $womenMentions * 2) {
                    $notes[] = "However, there appears to be a stronger focus on men (mentioned $menMentions times vs. women mentioned $womenMentions times).";
                } else {
                    $notes[] = "There appears to be a relatively balanced focus on both genders.";
                }
            } else {
                $notes[] = "This does not specifically mention any gender groups. Consider if the project/research is truly gender-neutral or if gender aspects should be addressed.";
            }
            
            if ($results['addresses_gender_inequality']) {
                if ($equalityAnalysis['positive_mentions'] > 3) {
                    $notes[] = "This strongly addresses gender inequality issues with multiple references.";
                } else {
                    $notes[] = "This addresses gender inequality issues.";
                }
                
                if (!empty($equalityAnalysis['key_terms'])) {
                    $notes[] = "Gender equality terms found: " . implode(", ", array_slice($equalityAnalysis['key_terms'], 0, 3)) . ".";
                }
                
                // Add sample sentences mentioning gender equality
                if (!empty($equalityAnalysis['sample_sentences']) && count($equalityAnalysis['sample_sentences']) > 0) {
                    $notes[] = "Example: \"" . $equalityAnalysis['sample_sentences'][0] . "\"";
                }
            }
            
            if ($results['women_count'] !== null) {
                $notes[] = "Approximately {$results['women_count']} women/girls mentioned.";
            }
            
            if ($results['men_count'] !== null) {
                $notes[] = "Approximately {$results['men_count']} men/boys mentioned.";
            }
            
            // Add confidence level to notes
            if ($results['confidence_score'] < 0.4) {
                $notes[] = "Note: Low confidence in this gender analysis. Consider manual review.";
            } elseif ($results['confidence_score'] > 0.8) {
                $notes[] = "Note: High confidence in this gender analysis.";
            }
            
            $results['gender_notes'] = implode(" ", $notes);
            
            return $results;
            
        } catch (\Exception $e) {
            Log::error('Gender analysis failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'exception' => $e
            ]);
            
            $results['gender_notes'] = "Error analyzing gender impacts: " . $e->getMessage();
            return $results;
        }
    }
    
    /**
     * Extract text from uploaded file
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function extractTextFromFile(UploadedFile $file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if ($extension === 'pdf') {
            // Parse PDF
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getPathname());
            return $pdf->getText();
        } elseif ($extension === 'docx') {
            // For DOCX files we'd need a library like PhpWord
            // This is a simple implementation and might need additional dependencies
            // For now, we'll throw an exception
            throw new \Exception("DOCX parsing not implemented yet.");
        } else {
            throw new \Exception("Unsupported file type: {$extension}. Only PDF and DOCX are supported.");
        }
    }
    
    /**
     * Find keyword matches in text
     *
     * @param string $text Text to search in
     * @param array $keywords Keywords to search for
     * @return array Matching keywords found
     */
    protected function findKeywordMatches($text, $keywords)
    {
        $matches = [];
        foreach ($keywords as $keyword) {
            if (mb_strpos($text, $keyword) !== false) {
                $matches[] = $keyword;
            }
        }
        return $matches;
    }
    
    /**
     * Extract numeric counts associated with keywords
     * e.g., "100 women" or "affecting 50 men"
     *
     * @param string $text Text to search in
     * @param array $keywords Keywords to find counts for
     * @return int|null Extracted count or null if not found
     */
    protected function extractCount($text, $keywords)
    {
        $count = null;
        $maxCount = 0;
        
        foreach ($keywords as $keyword) {
            // Look for patterns like "X keyword" where X is a number
            preg_match_all('/(\d+)\s+' . preg_quote($keyword, '/') . '/', $text, $matches);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    $currentCount = (int)$match;
                    if ($currentCount > $maxCount) {
                        $maxCount = $currentCount;
                    }
                }
            }
        }
        
        if ($maxCount > 0) {
            $count = $maxCount;
        }
        
        return $count;
    }

    /**
     * Split text into sentences for better context analysis
     * 
     * @param string $text The text to split into sentences
     * @return array Array of sentences
     */
    protected function splitIntoSentences($text) 
    {
        // Basic sentence splitting (could be improved with more complex NLP)
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return $sentences;
    }
    
    /**
     * Check if a sentence contains any negation terms
     * 
     * @param string $sentence The sentence to check
     * @return bool True if the sentence contains negation
     */
    protected function containsNegation($sentence) 
    {
        foreach ($this->negationTerms as $term) {
            if (mb_strpos($sentence, $term) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if a sentence contains strong positive terms
     * 
     * @param string $sentence The sentence to check
     * @return bool True if the sentence contains positive terms
     */
    protected function containsPositiveTerms($sentence) 
    {
        foreach ($this->strongPositiveTerms as $term) {
            if (mb_strpos($sentence, $term) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Analyze keywords with context in sentences
     * 
     * @param string $fullText Full text content
     * @param array $sentences Array of sentences from the text
     * @param array $keywords Keywords to look for
     * @return array Analysis results including positive/negative mentions and sample sentences
     */
    protected function analyzeKeywordsWithContext($fullText, $sentences, $keywords) 
    {
        $result = [
            'total_mentions' => 0,
            'positive_mentions' => 0,
            'negative_mentions' => 0,
            'key_terms' => [],
            'sample_sentences' => []
        ];
        
        // Count total mentions in the full text
        foreach ($keywords as $keyword) {
            $count = substr_count($fullText, $keyword);
            if ($count > 0) {
                $result['total_mentions'] += $count;
                $result['key_terms'][] = $keyword;
            }
        }
        
        // Reset key terms to unique values
        $result['key_terms'] = array_unique($result['key_terms']);
        
        // Analyze each sentence for context
        foreach ($sentences as $sentence) {
            $containsKeyword = false;
            foreach ($keywords as $keyword) {
                if (mb_strpos($sentence, $keyword) !== false) {
                    $containsKeyword = true;
                    break;
                }
            }
            
            if ($containsKeyword) {
                // Check for negations that might change meaning
                if ($this->containsNegation($sentence)) {
                    $result['negative_mentions']++;
                } else {
                    $result['positive_mentions']++;
                    
                    // If this sentence has strong positive language, give it priority for samples
                    if ($this->containsPositiveTerms($sentence) && count($result['sample_sentences']) < 3) {
                        $result['sample_sentences'][] = $sentence;
                    } elseif (count($result['sample_sentences']) < 3) {
                        $result['sample_sentences'][] = $sentence;
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Calculate confidence score based on analysis results
     * 
     * @param array $womenAnalysis Analysis results for women keywords
     * @param array $menAnalysis Analysis results for men keywords
     * @param array $equalityAnalysis Analysis results for gender equality keywords
     * @return float Confidence score between 0 and 1
     */
    protected function calculateConfidenceScore($womenAnalysis, $menAnalysis, $equalityAnalysis) 
    {
        // Start with medium confidence
        $score = 0.5;
        
        // More mentions increase confidence
        $totalMentions = $womenAnalysis['total_mentions'] + $menAnalysis['total_mentions'] + $equalityAnalysis['total_mentions'];
        
        if ($totalMentions > 20) {
            $score += 0.3; // High number of mentions
        } elseif ($totalMentions > 10) {
            $score += 0.2; // Moderate number of mentions
        } elseif ($totalMentions > 5) {
            $score += 0.1; // Few mentions
        } elseif ($totalMentions < 2) {
            $score -= 0.2; // Very few mentions, lower confidence
        }
        
        // If there are contradictory signals (both positive and negative mentions), reduce confidence
        $negativeMentions = $womenAnalysis['negative_mentions'] + $menAnalysis['negative_mentions'] + $equalityAnalysis['negative_mentions'];
        if ($negativeMentions > 0 && $totalMentions > 0) {
            $negativeRatio = $negativeMentions / $totalMentions;
            if ($negativeRatio > 0.3) {
                $score -= 0.2; // Many negative mentions, could be confusing
            }
        }
        
        // Strong gender equality focus increases confidence in the analysis
        if ($equalityAnalysis['positive_mentions'] > 5) {
            $score += 0.1;
        }
        
        // Ensure score stays within bounds
        return max(0.1, min(0.99, $score));
    }

    /**
     * Analyze gender impacts from text content directly
     *
     * @param string $text Text to analyze
     * @param string|null $targetBeneficiaries Additional text about target beneficiaries
     * @return array Gender impact analysis results
     */
    public function analyzeGenderFromText(string $text, ?string $targetBeneficiaries = null)
    {
        // Initialize results
        $results = [
            'benefits_men' => false,
            'benefits_women' => false,
            'benefits_all' => false,
            'addresses_gender_inequality' => false,
            'men_count' => null,
            'women_count' => null,
            'gender_notes' => '',
            'extracted_text' => '',
            'confidence_score' => 0.5, // Default medium confidence
            'context_analysis' => [] // Store context analysis results
        ];

        try {
            // Store the original text
            $results['extracted_text'] = $text;
            
            // Add target beneficiaries text if provided
            $fullText = $text;
            if ($targetBeneficiaries) {
                $fullText .= ' ' . $targetBeneficiaries;
            }
            
            // Convert to lowercase for case-insensitive matching
            $fullText = mb_strtolower($fullText);
            
            // Create an array of sentences for context analysis
            $sentences = $this->splitIntoSentences($fullText);
            
            // Analyze for women beneficiaries with context
            $womenAnalysis = $this->analyzeKeywordsWithContext($fullText, $sentences, $this->womenKeywords);
            $results['benefits_women'] = $womenAnalysis['positive_mentions'] > 0;
            $results['context_analysis']['women'] = $womenAnalysis;
            
            // Analyze for men beneficiaries with context
            $menAnalysis = $this->analyzeKeywordsWithContext($fullText, $sentences, $this->menKeywords);
            $results['benefits_men'] = $menAnalysis['positive_mentions'] > 0;
            $results['context_analysis']['men'] = $menAnalysis;
            
            // Analyze for gender equality focus with context
            $equalityAnalysis = $this->analyzeKeywordsWithContext($fullText, $sentences, $this->genderEqualityKeywords);
            $results['addresses_gender_inequality'] = $equalityAnalysis['positive_mentions'] > 0;
            $results['context_analysis']['equality'] = $equalityAnalysis;
            
            // Calculate confidence score based on analysis results
            $results['confidence_score'] = $this->calculateConfidenceScore($womenAnalysis, $menAnalysis, $equalityAnalysis);
            
            // If both men and women are mentioned, mark as benefiting all
            $results['benefits_all'] = ($results['benefits_men'] && $results['benefits_women']);
            
            // Extract counts if available
            $results['women_count'] = $this->extractCount($fullText, $this->womenKeywords);
            $results['men_count'] = $this->extractCount($fullText, $this->menKeywords);
            
            // Prepare gender notes
            $notes = [];
            
            if ($results['benefits_women'] && !$results['benefits_men']) {
                $notes[] = "This project primarily focuses on women/girls as beneficiaries.";
                if ($womenAnalysis['positive_mentions'] > 5) {
                    $notes[] = "There is a strong emphasis on women's involvement (mentioned " . $womenAnalysis['total_mentions'] . " times).";
                }
            } elseif ($results['benefits_men'] && !$results['benefits_women']) {
                $notes[] = "This project primarily focuses on men/boys as beneficiaries.";
                if ($menAnalysis['positive_mentions'] > 5) {
                    $notes[] = "There is a strong emphasis on men's involvement (mentioned " . $menAnalysis['total_mentions'] . " times).";
                }
            } elseif ($results['benefits_men'] && $results['benefits_women']) {
                $notes[] = "This project considers both men/boys and women/girls as beneficiaries.";
                
                // Compare mentions to see if there's a gender balance
                $womenMentions = $womenAnalysis['total_mentions'];
                $menMentions = $menAnalysis['total_mentions'];
                
                if ($womenMentions > $menMentions * 2) {
                    $notes[] = "However, there appears to be a stronger focus on women (mentioned $womenMentions times vs. men mentioned $menMentions times).";
                } elseif ($menMentions > $womenMentions * 2) {
                    $notes[] = "However, there appears to be a stronger focus on men (mentioned $menMentions times vs. women mentioned $womenMentions times).";
                } else {
                    $notes[] = "There appears to be a relatively balanced focus on both genders.";
                }
            } else {
                $notes[] = "This project does not specifically mention any gender groups. Consider if it is truly gender-neutral or if gender aspects should be addressed.";
            }
            
            if ($results['addresses_gender_inequality']) {
                if ($equalityAnalysis['positive_mentions'] > 3) {
                    $notes[] = "This project strongly addresses gender inequality issues with multiple references.";
                } else {
                    $notes[] = "This project addresses gender inequality issues.";
                }
                
                if (!empty($equalityAnalysis['key_terms'])) {
                    $notes[] = "Gender equality terms found: " . implode(", ", array_slice($equalityAnalysis['key_terms'], 0, 3)) . ".";
                }
                
                // Add sample sentences mentioning gender equality
                if (!empty($equalityAnalysis['sample_sentences']) && count($equalityAnalysis['sample_sentences']) > 0) {
                    $notes[] = "Example: \"" . $equalityAnalysis['sample_sentences'][0] . "\"";
                }
            }
            
            if ($results['women_count'] !== null) {
                $notes[] = "Approximately {$results['women_count']} women/girls mentioned.";
            }
            
            if ($results['men_count'] !== null) {
                $notes[] = "Approximately {$results['men_count']} men/boys mentioned.";
            }
            
            // Add confidence level to notes
            if ($results['confidence_score'] < 0.4) {
                $notes[] = "Note: Low confidence in this gender analysis. Consider manual review.";
            } elseif ($results['confidence_score'] > 0.8) {
                $notes[] = "Note: High confidence in this gender analysis.";
            }
            
            $results['gender_notes'] = implode(" ", $notes);
            
            return $results;
            
        } catch (\Exception $e) {
            Log::error('Gender text analysis failed: ' . $e->getMessage(), [
                'text_length' => strlen($text),
                'exception' => $e
            ]);
            
            $results['gender_notes'] = "Error analyzing gender impacts: " . $e->getMessage();
            return $results;
        }
    }
} 
