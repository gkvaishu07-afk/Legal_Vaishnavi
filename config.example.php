<?php
/**
 * LegalEase - AI-Powered Legal Document Generator
 * Configuration Example File
 *
 * INSTRUCTIONS:
 * 1. Duplicate or rename this file to 'config.php' in the same directory.
 * 2. Enter your Google Gemini API Key below in the GEMINI_API_KEY constant,
 *    OR set an environment variable named GEMINI_API_KEY in your system/web server.
 * 3. Never commit 'config.php' with your live secret key to public version control!
 *
 * HOW TO OBTAIN A FREE GEMINI API KEY:
 * 1. Visit Google AI Studio: https://aistudio.google.com/
 * 2. Sign in with your Google Account.
 * 3. Click "Get API Key" and generate a key for Gemini 1.5 Flash.
 * 4. Paste the key below between the quotes.
 *
 * DEMO MODE:
 * If no key is provided, LegalEase automatically runs in Demo Mode using the built-in
 * JSON template system (data/templates.json) with full functionality!
 */

// Prevent direct script access
if (!defined('LEGALEASE_ACCESS')) {
    define('LEGALEASE_ACCESS', true);
}

// Google Gemini API Configuration
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: '');

// Gemini Model: 'gemini-1.5-flash' or 'gemini-2.5-flash'
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
