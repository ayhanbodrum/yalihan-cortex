<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\AICoreSystem;
use App\Services\AISystemIntegration;
use App\Services\FlexibleStorageManager;

class AICoreTestController extends AdminController
{
    private $aiCore;
    private $aiIntegration;
    private $storageManager;

    public function __construct()
    {
        $this->aiCore = new AICoreSystem();
        $this->aiIntegration = new AISystemIntegration();
        $this->storageManager = new FlexibleStorageManager();
    }

    /**
     * AI Core Test sayfası
     */
    public function index()
    {
        return view('admin.ai-core-test.index');
    }

    /**
     * AI'yi test et
     */
    public function testAI(Request $request)
    {
        $request->validate([
            'context' => 'required|string',
            'input' => 'required|string'
        ]);

        $context = $request->context;
        $input = $request->input;

        try {
            // AI'yi test et
            $response = $this->aiCore->testAI($context, $input);

            return response()->json([
                'success' => true,
                'response' => $response,
                'context' => $context,
                'input' => $input
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI'yi öğret
     */
    public function teachAI(Request $request)
    {
        $request->validate([
            'context' => 'required|string',
            'task' => 'required|string',
            'expected_output' => 'required|string'
        ]);

        $context = $request->context;
        $task = $request->task;
        $expectedOutput = $request->expected_output;

        try {
            // AI'yi öğret
            $result = $this->aiCore->teachAI($context, $task, $expectedOutput);

            return response()->json([
                'success' => true,
                'result' => $result,
                'message' => 'AI başarıyla öğretildi!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Storage test
     */
    public function testStorage(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'data' => 'required|string'
        ]);

        $key = $request->key;
        $data = $request->data;

        try {
            // Veri kaydet
            $this->storageManager->store($key, $data);

            // Veri getir
            $retrieved = $this->storageManager->get($key);

            return response()->json([
                'success' => true,
                'stored' => $data,
                'retrieved' => $retrieved,
                'key' => $key
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI sistem durumu
     */
    public function getSystemStatus()
    {
        try {
            $status = [
                'ai_core' => $this->testAICore(),
                'storage' => $this->testStorageInternal(),
                'integration' => $this->testIntegration()
            ];

            return response()->json([
                'success' => true,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI Core test
     */
    private function testAICore()
    {
        try {
            $response = $this->aiCore->testAI('test', 'Merhaba');
            return ['status' => 'active', 'response' => $response];
        } catch (\Exception $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    /**
     * Storage test (private)
     */
    private function testStorageInternal()
    {
        try {
            $testKey = 'test_' . time();
            $testData = ['test' => 'data', 'timestamp' => now()];

            $this->storageManager->store($testKey, $testData);
            $retrieved = $this->storageManager->get($testKey);

            return ['status' => 'active', 'test' => $retrieved];
        } catch (\Exception $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    /**
     * Integration test
     */
    private function testIntegration()
    {
        try {
            $suggestions = $this->aiIntegration->generateSuggestions('test', 'test input');
            return ['status' => 'active', 'suggestions' => $suggestions];
        } catch (\Exception $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }
}
