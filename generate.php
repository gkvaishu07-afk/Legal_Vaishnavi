<?php
/**
 * LegalEase - AI & Template Document Generation Endpoint
 * Receives AJAX requests from frontend, validates inputs, coordinates Gemini AI
 * or falls back gracefully to structured JSON templates.
 */

define('LEGALEASE_ACCESS', true);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/gemini.php';
require_once __DIR__ . '/includes/template_generator.php';

// Accept only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse([
        'success' => false,
        'error' => 'METHOD_NOT_ALLOWED',
        'message' => 'Only POST requests are supported.'
    ], 405);
}

// Support both JSON body and standard Form Data
$rawInput = file_get_contents('php://input');
$data = [];

if (!empty($rawInput)) {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $data = $decoded;
    }
}

// Fallback to $_POST if JSON body was empty
if (empty($data) && !empty($_POST)) {
    $data = $_POST;
}

// Validate inputs
$validation = validateDocumentInput($data);

if (!$validation['is_valid']) {
    jsonResponse([
        'success' => false,
        'error' => 'VALIDATION_FAILED',
        'errors' => $validation['errors'],
        'message' => 'Please fill in all required fields accurately.'
    ], 422);
}

$cleaned = $validation['cleaned'];

// Attempt generation
$isAiMode = isGeminiConfigured();
$response = null;

if ($isAiMode) {
    // Attempt Gemini AI generation
    $aiResult = generateDocumentWithGemini($cleaned);
    
    if ($aiResult['success']) {
        $response = $aiResult;
    } else {
        // Graceful fallback to template generator if Gemini API fails
        $templateResult = generateDocumentFromTemplate($cleaned);
        $templateResult['fallback_triggered'] = true;
        $templateResult['fallback_reason'] = 'AI service temporarily unavailable (' . ($aiResult['message'] ?? 'API error') . '). Automatically generated using our verified legal templates.';
        $response = $templateResult;
    }
} else {
    // Demo Mode: Generate from JSON templates directly
    $response = generateDocumentFromTemplate($cleaned);
    $response['demo_mode'] = true;
    $response['notice'] = 'Generated in Demo Mode using built-in legal templates. To enable real-time Gemini AI drafting, configure your GEMINI_API_KEY in config.php.';
}

// Return final JSON
jsonResponse($response, 200);
