<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class SdgAiService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SDG_AI_ENGINE_URL', 'http://localhost:8000');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30.0,
            'connect_timeout' => 5.0,
            'http_errors' => false, // Don't throw exceptions for HTTP errors
        ]);
        
        Log::info('SdgAiService initialized with base URL: ' . $this->baseUrl);
    }

    /**
     * Analyze a research file to detect relevant SDGs and subcategories
     *
     * @param UploadedFile $file The uploaded research file (PDF only)
     * @return array|null Results from the AI engine or null if an error occurred
     */
    public function analyzeResearchFile(UploadedFile $file)
    {
        try {
            // Verify file is PDF
            $fileExtension = strtolower($file->getClientOriginalExtension());
            if ($fileExtension !== 'pdf') {
                Log::error('Only PDF files are supported for SDG analysis', [
                    'provided_extension' => $fileExtension,
                    'file_name' => $file->getClientOriginalName()
                ]);
                return null;
            }
            
            Log::info('Sending PDF file to SDG AI Engine for analysis', [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'endpoint' => $this->baseUrl . '/sdg/analyze'
            ]);
            
            // Check if AI engine is responding
            try {
                $healthResponse = $this->client->request('GET', '/', [
                    'timeout' => 2.0,
                ]);
                
                if ($healthResponse->getStatusCode() != 200) {
                    Log::warning('AI Engine health check failed with status code: ' . $healthResponse->getStatusCode());
                }
            } catch (\Exception $e) {
                Log::error('AI Engine health check failed: ' . $e->getMessage());
                return null;
            }
            
            // Send the file to the AI engine
            $response = $this->client->request('POST', '/sdg/analyze', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName(),
                    ],
                ],
                'timeout' => 60.0, // Increased timeout for larger PDF files
            ]);
            
            $statusCode = $response->getStatusCode();
            
            // Log response status
            Log::info('Received response from SDG AI Engine', [
                'status_code' => $statusCode
            ]);
            
            if ($statusCode != 200) {
                Log::error('AI Engine returned non-200 status code', [
                    'status_code' => $statusCode,
                    'response' => $response->getBody()->getContents()
                ]);
                return null;
            }

            $result = json_decode($response->getBody()->getContents(), true);
            
            if (!$result || !is_array($result)) {
                Log::error('AI Engine returned invalid JSON response', [
                    'raw_response' => $response->getBody()->getContents()
                ]);
                return null;
            }
            
            // Check for error in the response
            if (isset($result['error'])) {
                Log::error('AI Engine reported an error processing the PDF', [
                    'error' => $result['error'],
                    'file_name' => $file->getClientOriginalName()
                ]);
                return null;
            }
            
            return $result;
        } catch (ConnectException $e) {
            Log::error('Connection error with SDG AI Engine: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        } catch (RequestException $e) {
            Log::error('HTTP request error with SDG AI Engine: ' . $e->getMessage(), [
                'status_code' => $e->getResponse() ? $e->getResponse()->getStatusCode() : 'unknown',
                'response_body' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'none',
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error communicating with SDG AI Engine: ' . $e->getMessage(), [
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Convert AI engine results to database-compatible format
     *
     * @param array $aiResults Results from the AI engine
     * @return array [sdgIds, subCategoryIds]
     */
    public function convertResultsToIds(array $aiResults)
    {
        $sdgIds = [];
        $subCategoryIds = [];

        // Check if there's a short document with forced matches
        $isShortDocument = false;
        $hasForceMatch = false;
        
        if (isset($aiResults['metadata']) && isset($aiResults['metadata']['word_count'])) {
            $isShortDocument = $aiResults['metadata']['word_count'] <= 50; // Increased from 10 to 50 for better accuracy
            
            Log::info('Document word count detected', [
                'word_count' => $aiResults['metadata']['word_count'],
                'is_short_document' => $isShortDocument
            ]);
        }
        
        // Check for the matched_sdgs array
        if (isset($aiResults['matched_sdgs']) && is_array($aiResults['matched_sdgs'])) {
            // Process each matched SDG
            foreach ($aiResults['matched_sdgs'] as $match) {
                // Check if this is a forced match (priority match)
                if (isset($match['force_match']) && $match['force_match']) {
                    $hasForceMatch = true;
                    
                    // For forced matches, only use this SDG and ignore others
                    $sdgId = (int)ltrim($match['sdg_number'], '0');
                    $sdgIds = [$sdgId];
                    
                    Log::info('Forced match detected', [
                        'sdg_number' => $match['sdg_number'],
                        'sdg_id' => $sdgId,
                        'confidence' => $match['confidence'] ?? 'unknown'
                    ]);
                    
                    // For a forced match, still get its subcategories
                    if (isset($match['subcategories']) && is_array($match['subcategories'])) {
                        foreach ($match['subcategories'] as $subCategory) {
                            // Get the subcategory code (e.g., 5.1)
                            if (!isset($subCategory['subcategory'])) {
                                continue;
                            }
                            
                            $subcategoryCode = $subCategory['subcategory'];
                            $subCategoryId = $this->findSubCategoryId($match['sdg_number'], $subcategoryCode);
                            
                            if ($subCategoryId) {
                                $subCategoryIds[] = $subCategoryId;
                            }
                        }
                    }
                    
                    break; // Stop processing other SDGs
                }
                
                // Convert SDG number (01, 02, etc.) to database ID (1, 2, etc.)
                $sdgId = (int)ltrim($match['sdg_number'], '0');
                $sdgIds[] = $sdgId;

                // Process subcategories if available and not a short document without subcategories
                if (!$isShortDocument && isset($match['subcategories']) && is_array($match['subcategories'])) {
                    foreach ($match['subcategories'] as $subCategory) {
                        // Skip if we don't have subcategory information
                        if (!isset($subCategory['subcategory'])) {
                            continue;
                        }
                        
                        // Get the subcategory code (e.g., 1.1 or 5.A)
                        $subcategoryCode = $subCategory['subcategory'];
                        
                        // Find the subcategory ID in the database
                        $subCategoryId = $this->findSubCategoryId(
                            $match['sdg_number'], 
                            $subcategoryCode
                        );
                        
                        if ($subCategoryId) {
                            $subCategoryIds[] = $subCategoryId;
                            
                            Log::debug('Subcategory found', [
                                'sdg' => $match['sdg_number'],
                                'subcategory_code' => $subcategoryCode,
                                'subcategory_id' => $subCategoryId,
                                'confidence' => $subCategory['confidence'] ?? 'unknown'
                            ]);
                        }
                    }
                }
            }
        }

        // Special handling for gender equality terms
        $hasGenderEquality = false;
        
        // Check if any matched keywords contain gender equality related terms
        if (isset($aiResults['matched_sdgs'])) {
            foreach ($aiResults['matched_sdgs'] as $match) {
                if (isset($match['matched_keywords'])) {
                    foreach ($match['matched_keywords'] as $keyword) {
                        if (stripos($keyword, 'gender') !== false || 
                            stripos($keyword, 'women') !== false ||
                            stripos($keyword, 'girls') !== false ||
                            stripos($keyword, 'female') !== false ||
                            $keyword === 'equality') {
                            $hasGenderEquality = true;
                            break 2;
                        }
                    }
                }
            }
        }
        
        // If "gender equality" is detected, ensure SDG 5 is included
        if ($hasGenderEquality && !$hasForceMatch) {
            if (!in_array(5, $sdgIds)) {
                $sdgIds[] = 5;
                Log::info('Gender equality terms detected, adding SDG 5');
            }
            
            // Reorder to put SDG 5 first if it's not a forced match
            if (!$hasForceMatch) {
                $sdgIds = array_unique($sdgIds);
                if (in_array(5, $sdgIds)) {
                    $sdgIds = array_diff($sdgIds, [5]);
                    array_unshift($sdgIds, 5);
                }
            }
        }

        // Filter out any null values and ensure IDs are integers
        $sdgIds = array_map('intval', array_filter($sdgIds));
        $subCategoryIds = array_map('intval', array_filter($subCategoryIds));
        
        // Remove duplicates
        $sdgIds = array_values(array_unique($sdgIds));
        $subCategoryIds = array_values(array_unique($subCategoryIds));
        
        // Limit subcategories for better precision (only if we have a lot)
        if (count($subCategoryIds) > 8) {
            $subCategoryIds = array_slice($subCategoryIds, 0, 8);
        }

        // Log the final results
        Log::info('Converted AI results to database IDs', [
            'sdg_count' => count($sdgIds),
            'subcategory_count' => count($subCategoryIds),
            'is_short_document' => $isShortDocument,
            'has_force_match' => $hasForceMatch
        ]);
        
        return [
            'sdgIds' => $sdgIds,
            'subCategoryIds' => $subCategoryIds,
        ];
    }

    /**
     * Find the database ID for an SDG subcategory
     *
     * @param string $sdgNumber SDG number (01, 02, etc.)
     * @param string $subCategoryCode Subcategory code (1.1, 1.2, etc.)
     * @return int|null Database ID for the subcategory or null if not found
     */
    protected function findSubCategoryId($sdgNumber, $subCategoryCode)
    {
        // Convert SDG number to numeric ID for database lookup
        $sdgId = (int)ltrim($sdgNumber, '0');
        
        // Clean up subcategory code to ensure consistent formatting
        // Some subcategory codes might come as "1.1" when the database has them as "1.1"
        // or the database might use just the name portion without the SDG number prefix
        
        // First, try exact match
        $subCategory = \App\Models\SdgSubCategory::where('sdg_id', $sdgId)
            ->where('sub_category_name', $subCategoryCode)
            ->first();
            
        if ($subCategory) {
            return $subCategory->id;
        }
        
        // If not found, try with cleaned code (removing SDG number if it's redundant)
        // For example, if subcategory is "5.1" for SDG 5, try searching for just "1"
        if (strpos($subCategoryCode, "$sdgId.") === 0) {
            $cleanedCode = substr($subCategoryCode, strlen("$sdgId."));
            $subCategory = \App\Models\SdgSubCategory::where('sdg_id', $sdgId)
                ->where('sub_category_name', $cleanedCode)
                ->first();
                
            if ($subCategory) {
                return $subCategory->id;
            }
        }
        
        // Try by searching in description as a fallback
        $subCategory = \App\Models\SdgSubCategory::where('sdg_id', $sdgId)
            ->where('sub_category_description', 'like', '%' . $subCategoryCode . '%')
            ->first();
            
        return $subCategory ? $subCategory->id : null;
    }
} 
