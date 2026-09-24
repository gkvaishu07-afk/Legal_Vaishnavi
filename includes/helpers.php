<?php
/**
 * LegalEase - Helper Functions & Utilities
 */

if (!defined('LEGALEASE_ACCESS')) {
    define('LEGALEASE_ACCESS', true);
}

// Load configuration
if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';
} elseif (file_exists(__DIR__ . '/../config.example.php')) {
    require_once __DIR__ . '/../config.example.php';
}

/**
 * Check if Gemini API Key is configured and non-empty
 * @return bool
 */
function isGeminiConfigured() {
    return defined('GEMINI_API_KEY') && !empty(trim(GEMINI_API_KEY));
}

/**
 * Determine dynamic base URL for links and assets
 * Works seamlessly whether hosted at root or inside a subfolder like /LegalEase/
 * @return string
 */
function getBaseUrl() {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dirName = dirname($scriptName);
    
    // Normalize Windows backslashes to forward slashes
    $dirName = str_replace('\\', '/', $dirName);
    
    // If inside includes/ or api/, navigate to root
    if (basename($dirName) === 'includes' || basename($dirName) === 'api') {
        $dirName = dirname($dirName);
    }
    
    if ($dirName === '/' || $dirName === '.') {
        return '';
    }
    
    return rtrim($dirName, '/');
}

/**
 * Sanitize plain string input safely
 * @param mixed $input
 * @return string
 */
function sanitizeInput($input) {
    if (!is_string($input)) {
        return '';
    }
    // Trim and normalize newlines
    $input = trim($input);
    $input = str_replace(["\r\n", "\r"], "\n", $input);
    // Strip raw HTML tags for security
    $input = strip_tags($input);
    return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Validate document generation form inputs
 * @param array $data
 * @return array [bool $isValid, array $errors, array $cleaned]
 */
function validateDocumentInput($data) {
    $errors = [];
    $cleaned = [];

    // Document Type
    $docType = isset($data['document_type']) ? trim((string)$data['document_type']) : '';
    if (empty($docType)) {
        $errors['document_type'] = 'Please select a valid document type.';
    } else {
        $cleaned['document_type'] = sanitizeInput($docType);
    }

    // Parties Involved
    $parties = isset($data['parties']) ? trim((string)$data['parties']) : '';
    if (empty($parties)) {
        $errors['parties'] = 'Please specify the parties involved in this agreement.';
    } elseif (mb_strlen($parties) < 4) {
        $errors['parties'] = 'Parties description is too short (minimum 4 characters).';
    } else {
        $cleaned['parties'] = sanitizeInput($parties);
    }

    // Terms & Conditions
    $terms = isset($data['terms']) ? trim((string)$data['terms']) : '';
    if (empty($terms)) {
        $errors['terms'] = 'Please provide the terms and conditions.';
    } elseif (mb_strlen($terms) < 10) {
        $errors['terms'] = 'Please provide more descriptive terms and conditions (minimum 10 characters).';
    } else {
        $cleaned['terms'] = sanitizeInput($terms);
    }

    // Effective Date
    $effectiveDate = isset($data['effective_date']) ? trim((string)$data['effective_date']) : '';
    if (empty($effectiveDate)) {
        $errors['effective_date'] = 'Please specify an effective date.';
    } else {
        $dateObj = DateTime::createFromFormat('Y-m-d', $effectiveDate);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $effectiveDate) {
            $errors['effective_date'] = 'Effective date must be a valid date in YYYY-MM-DD format.';
        } else {
            $cleaned['effective_date'] = $effectiveDate;
        }
    }

    // Optional Additional Instructions
    $notes = isset($data['additional_notes']) ? trim((string)$data['additional_notes']) : '';
    $cleaned['additional_notes'] = sanitizeInput($notes);

    return [
        'is_valid' => empty($errors),
        'errors' => $errors,
        'cleaned' => $cleaned
    ];
}

/**
 * Send JSON response and exit
 * @param array $data
 * @param int $statusCode
 */
function jsonResponse($data, $statusCode = 200) {
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Load template definitions from data/templates.json
 * @return array
 */
function loadTemplates() {
    $filePath = __DIR__ . '/../data/templates.json';
    if (!file_exists($filePath)) {
        return ['templates' => []];
    }
    $raw = file_get_contents($filePath);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : ['templates' => []];
}

/**
 * Format date for official legal documents
 * @param string $dateStr (YYYY-MM-DD)
 * @return string
 */
function formatLegalDate($dateStr) {
    $timestamp = strtotime($dateStr);
    if (!$timestamp) {
        return htmlspecialchars($dateStr);
    }
    return date('F j, Y', $timestamp);
}

/**
 * Split terms entered by user by semicolon or newline
 * @param string $termsRaw
 * @return array
 */
function parseTermsList($termsRaw) {
    // Decode HTML entities temporarily for splitting
    $raw = html_entity_decode($termsRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Split by semicolons or newlines
    $parts = preg_split('/[;\n\r]+/', $raw);
    $results = [];
    
    foreach ($parts as $part) {
        $trimmed = trim($part);
        // Remove trailing period or leading bullet if any
        $trimmed = preg_replace('/^[\d\.\-\*\s]+/', '', $trimmed);
        if (!empty($trimmed)) {
            $results[] = htmlspecialchars($trimmed, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
    
    return $results;
}
