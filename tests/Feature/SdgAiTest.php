<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sdg;
use App\Models\SdgSubCategory;
use App\Services\SdgAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Mockery;

class SdgAiTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Setup before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create some sample SDGs and subcategories for testing
        $sdg1 = Sdg::factory()->create([
            'id' => 1,
            'name' => 'No Poverty'
        ]);
        
        $sdg4 = Sdg::factory()->create([
            'id' => 4,
            'name' => 'Quality Education'
        ]);
        
        $sdg5 = Sdg::factory()->create([
            'id' => 5,
            'name' => 'Gender Equality'
        ]);
        
        // Create some subcategories
        SdgSubCategory::factory()->create([
            'id' => 1,
            'sdg_id' => 1,
            'sub_category_name' => '1.1',
            'sub_category_description' => 'By 2030, eradicate extreme poverty for all people everywhere'
        ]);
        
        SdgSubCategory::factory()->create([
            'id' => 2,
            'sdg_id' => 4,
            'sub_category_name' => '4.1',
            'sub_category_description' => 'Ensure all girls and boys complete free, equitable and quality primary and secondary education'
        ]);
        
        SdgSubCategory::factory()->create([
            'id' => 3,
            'sdg_id' => 5,
            'sub_category_name' => '5.1',
            'sub_category_description' => 'End all forms of discrimination against all women and girls everywhere'
        ]);
    }

    /**
     * Test the SDG AI Engine integration for file analysis.
     */
    public function test_sdg_ai_engine_file_analysis(): void
    {
        // Create a fake user
        $user = User::factory()->create([
            'role' => 'contributor',
        ]);

        // Create a fake PDF file with SDG-related content
        Storage::fake('local');
        $content = "This research focuses on sustainable development, climate change, and poverty reduction strategies. 
                   It addresses gender equality issues and promotes quality education for all.";
        $file = UploadedFile::fake()->createWithContent('research.pdf', $content);

        // Mock the SdgAiService to return a test response
        $this->mock(SdgAiService::class, function ($mock) {
            $mock->shouldReceive('analyzeResearchFile')
                ->andReturn([
                    'matched_sdgs' => [
                        [
                            'sdg_number' => '05',
                            'sdg_name' => 'Gender Equality',
                            'confidence' => 0.85,
                            'force_match' => false,
                            'matched_keywords' => ['gender equality', 'women', 'girls'],
                            'subcategories' => [
                                [
                                    'subcategory' => '5.1',
                                    'confidence' => 0.76
                                ]
                            ]
                        ],
                        [
                            'sdg_number' => '04',
                            'sdg_name' => 'Quality Education',
                            'confidence' => 0.72,
                            'matched_keywords' => ['education', 'learning'],
                            'subcategories' => [
                                [
                                    'subcategory' => '4.1',
                                    'confidence' => 0.65
                                ]
                            ]
                        ],
                        [
                            'sdg_number' => '01',
                            'sdg_name' => 'No Poverty',
                            'confidence' => 0.65,
                            'matched_keywords' => ['poverty', 'reduction'],
                            'subcategories' => [
                                [
                                    'subcategory' => '1.1',
                                    'confidence' => 0.60
                                ]
                            ]
                        ]
                    ],
                    'metadata' => [
                        'word_count' => 120,
                        'page_count' => 1,
                        'processing_time_ms' => 500
                    ]
                ]);
                
            $mock->shouldReceive('convertResultsToIds')
                ->andReturn([
                    'sdgIds' => [5, 4, 1],
                    'subCategoryIds' => [3, 2, 1]
                ]);
        });

        // Send the file to the API endpoint
        $response = $this->actingAs($user)
                        ->post(route('api.sdg-ai.analyze'), [
                            'file' => $file,
                        ]);

        // Assert the response structure
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'sdgs',
                        'subcategories',
                        'raw_ai_results',
                    ]
                ]);

        // Assert the specific data from our mock
        $response->assertJson([
            'success' => true,
            'data' => [
                'sdgs' => [
                    ['id' => 5, 'name' => 'Gender Equality'],
                    ['id' => 4, 'name' => 'Quality Education'],
                    ['id' => 1, 'name' => 'No Poverty']
                ],
                'subcategories' => [
                    ['id' => 3, 'name' => '5.1'],
                    ['id' => 2, 'name' => '4.1'],
                    ['id' => 1, 'name' => '1.1']
                ]
            ]
        ]);
    }
    
    /**
     * Test the SdgAiService directly.
     */
    public function test_sdg_ai_service_convert_results(): void
    {
        // Create the service
        $service = new SdgAiService();
        
        // Test data
        $aiResults = [
            'matched_sdgs' => [
                [
                    'sdg_number' => '01',
                    'sdg_name' => 'No Poverty',
                    'confidence' => 0.85,
                    'matched_keywords' => ['poverty', 'poor'],
                    'subcategories' => [
                        [
                            'subcategory' => '1.1',
                            'confidence' => 0.76
                        ]
                    ]
                ],
                [
                    'sdg_number' => '05',
                    'sdg_name' => 'Gender Equality',
                    'confidence' => 0.65,
                    'matched_keywords' => ['gender', 'women'],
                    'subcategories' => [
                        [
                            'subcategory' => '5.1',
                            'confidence' => 0.60
                        ]
                    ]
                ]
            ],
            'metadata' => [
                'word_count' => 100,
                'page_count' => 1
            ]
        ];
        
        $result = $service->convertResultsToIds($aiResults);
        
        // Test that conversion works correctly
        $this->assertIsArray($result);
        $this->assertArrayHasKey('sdgIds', $result);
        $this->assertArrayHasKey('subCategoryIds', $result);
        
        // Should contain the SDG IDs in the right order (gender equality first due to special handling)
        $this->assertEquals([5, 1], $result['sdgIds']);
        
        // Should contain subcategory IDs
        $this->assertContains(3, $result['subCategoryIds']); // 5.1
        $this->assertContains(1, $result['subCategoryIds']); // 1.1
    }
} 