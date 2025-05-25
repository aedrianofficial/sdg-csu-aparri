<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenderAiController extends Controller
{
    /**
     * Base URL for the SDG AI Engine
     */
    protected $baseUrl;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Default to localhost:8003 but allow override from env
        $this->baseUrl = env('SDG_AI_ENGINE_URL', 'http://localhost:8003');
    }
    
    /**
     * Analyze text content for gender impact
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyze(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'text' => 'required|string',
                'target_beneficiaries' => 'nullable|string',
            ]);
            
            // Prepare data for AI Engine
            $data = [
                'text' => $validated['text'],
                'target_beneficiaries' => $validated['target_beneficiaries'] ?? '',
            ];
            
            // Forward to AI Engine
            $response = Http::timeout(60)
                ->post("{$this->baseUrl}/gender/analyze-text", $data);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }
            
            // Log error details
            Log::error('Gender AI Engine text analysis failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze gender impact: ' . ($response->json()['detail'] ?? 'Unknown error')
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Gender analysis error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing gender impact: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Analyze uploaded file for gender impact
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeFile(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB PDF
                'target_beneficiaries' => 'nullable|string',
            ]);
            
            $file = $request->file('file');
            $targetBeneficiaries = $request->input('target_beneficiaries', '');
            
            // Forward file to AI Engine using multipart form
            $response = Http::timeout(90)
                ->attach(
                    'file', 
                    file_get_contents($file->getPathname()), 
                    $file->getClientOriginalName()
                )
                ->post("{$this->baseUrl}/gender/analyze", [
                    'target_beneficiaries' => $targetBeneficiaries
                ]);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }
            
            // Log error details
            Log::error('Gender AI Engine file analysis failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze gender impact: ' . ($response->json()['detail'] ?? 'Unknown error')
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Gender file analysis error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing gender impact: ' . $e->getMessage()
            ], 500);
        }
    }
} 