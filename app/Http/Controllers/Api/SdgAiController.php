<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Sdg;
use App\Models\SdgSubCategory;

class SdgAiController extends Controller
{
    /**
     * Analyze a research document and identify relevant SDGs and targets
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyze(Request $request)
    {
        // Add CORS headers
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
        ];
        
        // Handle preflight OPTIONS request
        if ($request->isMethod('OPTIONS')) {
            return response()->json(['status' => 'success'], 200, $headers);
        }
        
        // Check if this is a text analysis request
        if ($request->has('text')) {
            return $this->analyzeText($request);
        }
        
        // Validate the request for file upload
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB PDF file
        ]);

        // Save the uploaded file temporarily
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $tempPath = $file->getPathname();
            
            // Check if file is too small (possibly empty or corrupted)
            if ($file->getSize() < 100) { // Less than 100 bytes
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded PDF file appears to be empty or corrupted.'
                ], 400, $headers);
            }
            
            try {
                // Get file contents first to validate it's readable
                $fileContents = file_get_contents($tempPath);
                
                if ($fileContents === false || empty($fileContents)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not read the uploaded PDF file.'
                    ], 400, $headers);
                }
                
                // Create a unique temporary filename to avoid conflicts
                $tempFileName = 'sdg_analysis_' . time() . '_' . $file->getClientOriginalName();
                
                // Call the SDG AI Engine API (running locally)
                $response = Http::timeout(60)->attach(
                    'file', 
                    $fileContents, 
                    $tempFileName
                )->post('http://localhost:8003/sdg/analyze');

                // Check if the request was successful
                if ($response->successful()) {
                    $aiResults = $response->json();
                    
                    // Transform AI response to match the expected format in the frontend
                    $transformedResults = $this->transformAiResults($aiResults);
                    
                    // Add headers to the response
                    $corsHeaders = [
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
                    ];
                    
                    return response()->json([
                        'success' => true,
                        'data' => $transformedResults
                    ], 200, $corsHeaders);
                } else {
                    // Get the specific error message from the AI engine
                    $errorBody = $response->body();
                    $errorJson = json_decode($errorBody, true);
                    $errorMessage = isset($errorJson['detail']) ? $errorJson['detail'] : 'Unknown error from SDG AI service';
                    
                    // Log the error for debugging
                    Log::error('SDG AI Engine returned an error', [
                        'status' => $response->status(),
                        'response' => $errorBody
                    ]);
                    
                    // Return a user-friendly message based on the error
                    $userMessage = $this->getUserFriendlyErrorMessage($errorMessage);
                    
                    // Add CORS headers
                    $corsHeaders = [
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
                    ];
                    
                    return response()->json([
                        'success' => false,
                        'message' => $userMessage
                    ], 500, $corsHeaders);
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                
                Log::error('Failed to connect to SDG AI Engine', [
                    'error' => $errorMessage,
                    'file' => $file->getClientOriginalName(),
                    'size' => $file->getSize()
                ]);
                
                // Check for specific connection errors
                if (strpos($errorMessage, 'Connection refused') !== false || 
                    strpos($errorMessage, 'Connection timed out') !== false ||
                    strpos($errorMessage, 'cURL error 28') !== false) {
                    // Add CORS headers
                    $corsHeaders = [
                        'Access-Control-Allow-Origin' => '*',
                        'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
                    ];
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not connect to SDG AI service. Please make sure the AI engine is running (start_ai_engine.bat) and try again.'
                    ], 500, $corsHeaders);
                }
                
                // Add CORS headers
                $corsHeaders = [
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
                ];
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error analyzing document. Please try again or select SDGs manually. Details: ' . $this->sanitizeErrorMessage($errorMessage)
                ], 500, $corsHeaders);
            }
        }
        
        // Add CORS headers
        $corsHeaders = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
        ];
        
        return response()->json([
            'success' => false,
            'message' => 'No file uploaded'
        ], 400, $corsHeaders);
    }
    
    /**
     * Analyze text input for SDG relevance
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function analyzeText(Request $request)
    {
        // Add CORS headers
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
        ];
        
        // Validate the request
        $request->validate([
            'text' => 'required|string|min:10',
        ]);
        
        $text = $request->input('text');
        
        try {
            // Call the SDG AI Engine API's text analysis endpoint
            $response = Http::timeout(60)
                ->post('http://localhost:8003/sdg/analyze-text', [
                    'text' => $text
                ]);
            
            // Check if the request was successful
            if ($response->successful()) {
                $aiResults = $response->json();
                
                // Transform AI response to match the expected format in the frontend
                $transformedResults = $this->transformAiResults($aiResults);
                
                return response()->json([
                    'success' => true,
                    'data' => $transformedResults
                ], 200, $headers);
            } else {
                // Get the specific error message from the AI engine
                $errorBody = $response->body();
                $errorJson = json_decode($errorBody, true);
                $errorMessage = isset($errorJson['detail']) ? $errorJson['detail'] : 'Unknown error from SDG AI service';
                
                // Log the error for debugging
                Log::error('SDG AI Engine returned an error for text analysis', [
                    'status' => $response->status(),
                    'response' => $errorBody
                ]);
                
                // Return a user-friendly message
                $userMessage = $this->getUserFriendlyErrorMessage($errorMessage);
                
                return response()->json([
                    'success' => false,
                    'message' => $userMessage
                ], 500, $headers);
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            Log::error('Failed to connect to SDG AI Engine for text analysis', [
                'error' => $errorMessage,
                'text_length' => strlen($text)
            ]);
            
            // Check for specific connection errors
            if (strpos($errorMessage, 'Connection refused') !== false || 
                strpos($errorMessage, 'Connection timed out') !== false ||
                strpos($errorMessage, 'cURL error 28') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not connect to SDG AI service. Please make sure the AI engine is running and try again.'
                ], 500, $headers);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing text. Please try again or select SDGs manually. Details: ' . $this->sanitizeErrorMessage($errorMessage)
            ], 500, $headers);
        }
    }
    
    /**
     * Transform the AI results to match the expected format in the frontend
     *
     * @param array $aiResults
     * @return array
     */
    private function transformAiResults($aiResults)
    {
        $sdgs = [];
        $subcategories = [];
        
        // Validate that we have valid AI results before processing
        if (!isset($aiResults['matched_sdgs']) || !is_array($aiResults['matched_sdgs'])) {
            Log::warning('Invalid AI results format received', [
                'results' => $aiResults
            ]);
            return [
                'sdgs' => [],
                'subcategories' => []
            ];
        }
        
        // Process matched SDGs
        foreach ($aiResults['matched_sdgs'] as $matchedSdg) {
            // Validate required fields
            if (!isset($matchedSdg['sdg_number'])) {
                continue;
            }
            
            // Find the corresponding SDG in the database
            $sdgNumber = (int)$matchedSdg['sdg_number'];
            
            // Try to find the SDG by its number in the name (e.g., "SDG 1: No Poverty")
            $sdgModel = Sdg::where('name', 'like', "SDG $sdgNumber%")
                ->orWhere('name', 'like', "%SDG $sdgNumber%")
                ->first();
                
            // If not found by number, try to find by ID as fallback
            if (!$sdgModel && $sdgNumber > 0 && $sdgNumber <= 17) {
                $sdgModel = Sdg::find($sdgNumber);
            }
            
            if ($sdgModel) {
                $sdgs[] = [
                    'id' => $sdgModel->id,
                    'name' => $sdgModel->name,
                    'confidence' => $matchedSdg['confidence'] ?? 0.5 // Default if missing
                ];
                
                // Process subcategories for this SDG
                if (isset($matchedSdg['subcategories']) && is_array($matchedSdg['subcategories'])) {
                    foreach ($matchedSdg['subcategories'] as $sub) {
                        // Validate required fields
                        if (!isset($sub['subcategory'])) {
                            continue;
                        }
                        
                        // Find the corresponding subcategory in the database
                        $subCode = $sub['subcategory'];
                        $subModel = SdgSubCategory::where('sub_category_name', 'like', "%{$subCode}%")
                            ->where('sdg_id', $sdgModel->id)
                            ->first();
                        
                        if ($subModel) {
                            $subcategories[] = [
                                'id' => $subModel->id,
                                'name' => $subCode,
                                'description' => $subModel->sub_category_description,
                                'confidence' => $sub['confidence'] ?? 0.5 // Default if missing
                            ];
                        } else {
                            // Log warning about missing subcategory
                            Log::warning("Subcategory not found in database", [
                                'subcategory' => $subCode,
                                'sdg_id' => $sdgModel->id
                            ]);
                        }
                    }
                }
            } else {
                // Log warning about missing SDG
                Log::warning("SDG not found in database", [
                    'sdg_number' => $sdgNumber
                ]);
            }
            
            // Limit to top 3 SDGs for consistency between research and projects
            if (count($sdgs) >= 3) {
                break;
            }
        }
        
        return [
            'sdgs' => $sdgs,
            'subcategories' => $subcategories
        ];
    }
    
    /**
     * Convert technical error messages to user-friendly versions
     * 
     * @param string $errorMessage
     * @return string
     */
    private function getUserFriendlyErrorMessage($errorMessage)
    {
        // PDF handling errors
        if (strpos($errorMessage, 'Empty PDF') !== false || 
            strpos($errorMessage, 'Could not extract readable text') !== false) {
            return 'The PDF appears to contain no readable text. It may be scanned or contain only images. Please select SDGs manually.';
        }
        
        if (strpos($errorMessage, 'Invalid or corrupted PDF') !== false) {
            return 'The uploaded PDF file appears to be corrupted or invalid. Please try another file or select SDGs manually.';
        }
        
        if (strpos($errorMessage, 'Error processing PDF') !== false) {
            return 'There was a problem processing your PDF. Please try another document or select SDGs manually.';
        }
        
        // General errors
        if (strpos($errorMessage, 'Document contains very little text') !== false) {
            return 'The document contains very little text for accurate analysis. The AI has made a best guess, but please review the results.';
        }
        
        // Default case - return a sanitized version of the original error
        return 'Error analyzing document: ' . $this->sanitizeErrorMessage($errorMessage);
    }
    
    /**
     * Sanitize error messages to remove paths and technical details
     * 
     * @param string $errorMessage
     * @return string
     */
    private function sanitizeErrorMessage($errorMessage)
    {
        // Remove file paths
        $sanitized = preg_replace('/at [A-Z]:\\\\.*\\\\/', 'at ', $errorMessage);
        
        // Remove technical details that aren't helpful to users
        $sanitized = preg_replace('/PyPDF2\.errors\.[a-zA-Z]+Error: /', '', $sanitized);
        
        return $sanitized;
    }
} 
