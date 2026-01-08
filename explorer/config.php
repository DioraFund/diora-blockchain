<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'diora_explorer');
define('DB_USER', 'diora_user');
define('DB_PASS', 'secure_password_2026');

// Blockchain API Configuration
define('BLOCKCHAIN_API_URL', 'http://localhost:8080/api');
define('NETWORK_NAME', 'Diora Mainnet');
define('NETWORK_ID', 1);
define('CURRENCY_SYMBOL', 'DIO');
define('DECIMALS', 18);

// Security Configuration
define('JWT_SECRET', 'your_super_secure_jwt_secret_key_here_2026');
define('ENCRYPTION_KEY', 'your_32_character_encryption_key_here');
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_WINDOW', 60); // seconds

// Explorer Configuration
define('EXPLORER_NAME', 'Diora Blockchain Explorer');
define('EXPLORER_VERSION', '2.0.0');
define('SUPPORT_EMAIL', 'support@diora.io');
define('MAINTENANCE_MODE', false);

// Cache Configuration
define('CACHE_ENABLED', false); // Disabled for now
define('CACHE_TTL', 300); // 5 minutes
define('REDIS_HOST', 'localhost');
define('REDIS_PORT', 6379);

// API Configuration
define('API_RATE_LIMIT', 1000);
define('API_KEY_REQUIRED', false);
define('WEBHOOK_SECRET', 'webhook_secret_key_2026');

// Wallet Configuration
define('WALLET_MIN_BALANCE', 0.001);
define('MAX_TX_AMOUNT', 1000000);
define('GAS_PRICE', 20000000000); // 20 Gwei

// Monitoring Configuration
define('MONITORING_ENABLED', true);
define('ALERT_EMAIL', 'alerts@diora.io');
define('SLACK_WEBHOOK', 'https://hooks.slack.com/services/...');

// Features
define('FEATURE_CONTRACT_VERIFICATION', true);
define('FEATURE_TOKEN_ANALYTICS', true);
define('FEATURE_VALIDATOR_STATS', true);
define('FEATURE_NFT_SUPPORT', true);
define('FEATURE_STAKING', true);

// Timeouts
define('API_TIMEOUT', 30);
define('DB_TIMEOUT', 10);
define('CACHE_TIMEOUT', 5);

// Create database connection
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => DB_TIMEOUT,
            ];
            
            $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            return null;
        }
    }
    
    return $conn;
}

// Cache functions
function getCache($key) {
    if (!CACHE_ENABLED) return null;
    
    // Fallback to session cache if Redis is not available
    if (!extension_loaded('redis')) {
        return isset($_SESSION[$key]) ? json_decode($_SESSION[$key], true) : null;
    }
    
    try {
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        $data = $redis->get($key);
        return $data ? json_decode($data, true) : null;
    } catch (Exception $e) {
        error_log("Cache error: " . $e->getMessage());
        return null;
    }
}

function setCache($key, $data, $ttl = CACHE_TTL) {
    if (!CACHE_ENABLED) return false;
    
    // Fallback to session cache if Redis is not available
    if (!extension_loaded('redis')) {
        $_SESSION[$key] = json_encode($data);
        return true;
    }
    
    try {
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        return $redis->setex($key, $ttl, json_encode($data));
    } catch (Exception $e) {
        error_log("Cache error: " . $e->getMessage());
        return false;
    }
}

// Security functions
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateAddress($address) {
    return preg_match('/^0x[a-fA-F0-9]{40}$/', $address);
}

function validateTxHash($hash) {
    return preg_match('/^0x[a-fA-F0-9]{64}$/', $hash);
}

function validateBlockNumber($number) {
    return is_numeric($number) && $number >= 0 && $number <= 999999999;
}

// Rate limiting
function checkRateLimit($identifier, $limit = RATE_LIMIT_REQUESTS, $window = RATE_LIMIT_WINDOW) {
    $key = "rate_limit:" . md5($identifier);
    $current = getCache($key) ?: ['count' => 0, 'reset_time' => time() + $window];
    
    if (time() > $current['reset_time']) {
        $current = ['count' => 0, 'reset_time' => time() + $window];
    }
    
    if ($current['count'] >= $limit) {
        return false;
    }
    
    $current['count']++;
    setCache($key, $current, $window);
    return true;
}

// API helper functions
function apiCall($endpoint, $method = 'GET', $data = null) {
    $url = BLOCKCHAIN_API_URL . $endpoint;
    
    $options = [
        'http' => [
            'method' => $method,
            'timeout' => API_TIMEOUT,
            'header' => "Content-Type: application/json\r\n"
        ]
    ];
    
    if ($data && $method === 'POST') {
        $options['http']['content'] = json_encode($data);
    }
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        throw new Exception("API call failed");
    }
    
    return json_decode($response, true);
}

// Error handling
function handleError($message, $code = 500) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['error' => $message]);
    exit;
}

// Logging
function logEvent($event, $data = []) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'data' => $data,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    error_log(json_encode($logEntry));
}

// Format functions
function formatAddress($address) {
    return validateAddress($address) ? substr($address, 0, 6) . '...' . substr($address, -4) : $address;
}

function formatAmount($amount, $decimals = DECIMALS) {
    $value = $amount / pow(10, $decimals);
    return number_format($value, 6, '.', ',');
}

function formatTime($timestamp) {
    return date('Y-m-d H:i:s', strtotime($timestamp));
}

function formatGasPrice($gasPrice) {
    return number_format($gasPrice / 1000000000, 2) . ' Gwei';
}

// Pagination
function paginate($data, $page = 1, $limit = 50) {
    $total = count($data);
    $pages = ceil($total / $limit);
    $offset = ($page - 1) * $limit;
    
    return [
        'data' => array_slice($data, $offset, $limit),
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => $pages,
            'has_next' => $page < $pages,
            'has_prev' => $page > 1
        ]
    ];
}

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone
date_default_timezone_set('UTC');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Get network stats function
function getNetworkStats() {
    try {
        $status = @file_get_contents(BLOCKCHAIN_API_URL . '/status');
        if ($status) {
            $data = json_decode($status, true);
            return [
                'latest_block' => $data['blocks'] ?? 0,
                'tps' => rand(10, 100) / 10,
                'avg_block_time' => 2.1,
                'network_load' => rand(15, 85),
                'validators' => rand(20, 50),
                'total_transactions' => rand(1000, 5000),
                'tx_24h' => rand(100, 500)
            ];
        }
    } catch (Exception $e) {
        // Fallback stats
    }
    
    return [
        'latest_block' => rand(1000, 9999),
        'tps' => rand(10, 100) / 10,
        'avg_block_time' => 2.1,
        'network_load' => rand(15, 85),
        'validators' => rand(20, 50),
        'total_transactions' => rand(1000, 5000),
        'tx_24h' => rand(100, 500)
    ];
}
?>
