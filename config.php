<?php
/**
 * LegalEase - AI-Powered Legal Document Generator
 * Active Server-Side Configuration
 *
 * NOTE: Keep this file secure. Never expose this file or keys to client-side assets.
 */

// Prevent direct script access if included in security check
if (!defined('LEGALEASE_ACCESS')) {
    define('LEGALEASE_ACCESS', true);
}

// Google Gemini API Configuration
// Check system environment variable first, or set your API key string here
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'AQ.Ab8RN6LSdi8t2eOmaMsyyEWlUNT7ygbXxGCNHij5ncF5h_-H3g');

// Gemini Model Configuration (recommended: gemini-1.5-flash)
define('GEMINI_MODEL', 'gemini-1.5-flash');

// Gemini API Endpoint (v1beta)
define('GEMINI_API_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent');

// Request Timeout (seconds)
define('API_TIMEOUT', 30);

// Application Metadata
define('APP_NAME', 'LegalEase');
define('APP_VERSION', '1.0.0');
define('APP_TAGLINE', 'AI-Powered Legal Document Generator');
define('DEVELOPER_NAME', 'Vaishnavi G');
define('DEVELOPER_INSTITUTION', 'Government Arts College (Autonomous), Kumbakonam');
define('DEVELOPER_COURSE', 'B.Sc. Computer Science');
define('DEVELOPER_YEAR', '2nd Year');
