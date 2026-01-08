<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Rate limiting
$identifier = $_SERVER['REMOTE_ADDR'] . ':' . $action;
if (!checkRateLimit($identifier)) {
    handleError('Rate limit exceeded', 429);
}

try {
    switch ($action) {
        case 'stats':
            handleStats();
            break;
            
        case 'blocks':
            handleBlocks();
            break;
            
        case 'block':
            handleBlock();
            break;
            
        case 'transactions':
            handleTransactions();
            break;
            
        case 'transaction':
            handleTransaction();
            break;
            
        case 'address':
            handleAddress();
            break;
            
        case 'wallets':
            handleWallets();
            break;
            
        case 'wallet':
            handleWallet();
            break;
            
        case 'contracts':
            handleContracts();
            break;
            
        case 'validators':
            handleValidators();
            break;
            
        case 'tokens':
            handleTokens();
            break;
            
        case 'search':
            handleSearch();
            break;
            
        case 'analytics':
            handleAnalytics();
            break;
            
        default:
            handleError('Invalid action', 400);
    }
} catch (Exception $e) {
    logEvent('api_error', ['action' => $action, 'error' => $e->getMessage()]);
    handleError('Internal server error', 500);
}

function handleStats() {
    $cacheKey = 'network_stats';
    $stats = getCache($cacheKey);
    
    if (!$stats) {
        // Get data from blockchain API
        $status = apiCall('/status');
        $blocks = apiCall('/blocks');
        $transactions = apiCall('/transactions');
        $wallets = apiCall('/wallets');
        
        $stats = [
            'latest_block' => $status['blocks'] ?? 0,
            'total_transactions' => count($transactions['transactions'] ?? []),
            'tps' => calculateTPS($transactions['transactions'] ?? []),
            'avg_block_time' => calculateAvgBlockTime($blocks['blocks'] ?? []),
            'network_load' => calculateNetworkLoad(),
            'validators' => count($wallets['wallets'] ?? []),
            'tx_24h' => calculateTransactions24h($transactions['transactions'] ?? []),
            'gas_price' => GAS_PRICE,
            'market_cap' => calculateMarketCap(),
            'circulating_supply' => calculateCirculatingSupply(),
            'staked_amount' => calculateStakedAmount(),
            'mempool_size' => getMempoolSize()
        ];
        
        setCache($cacheKey, $stats, 30); // Cache for 30 seconds
    }
    
    echo json_encode($stats);
}

function handleBlocks() {
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = min(100, max(10, intval($_GET['limit'] ?? 50)));
    
    $cacheKey = "blocks_page_{$page}_{$limit}";
    $cached = getCache($cacheKey);
    
    if ($cached) {
        echo json_encode($cached);
        return;
    }
    
    $blocks = apiCall('/blocks');
    $blockList = $blocks['blocks'] ?? [];
    
    // Enrich block data
    $enrichedBlocks = array_map(function($block) {
        return [
            'number' => $block['index'],
            'hash' => $block['hash'],
            'timestamp' => $block['timestamp'],
            'transactions' => countTransactionsInBlock($block),
            'miner' => getBlockMiner($block),
            'gas_used' => getBlockGasUsed($block),
            'gas_limit' => 30000000,
            'difficulty' => 'N/A',
            'size' => strlen(json_encode($block)),
            'parent_hash' => $block['previous_hash'] ?? null,
            'reward' => '2 DIO'
        ];
    }, array_reverse($blockList));
    
    $result = paginate($enrichedBlocks, $page, $limit);
    setCache($cacheKey, $result, 60);
    
    echo json_encode($result);
}

function handleBlock() {
    $blockNumber = $_GET['number'] ?? '';
    $blockHash = $_GET['hash'] ?? '';
    
    if (!$blockNumber && !$blockHash) {
        handleError('Block number or hash required', 400);
    }
    
    $cacheKey = "block_" . ($blockNumber ?: $blockHash);
    $block = getCache($cacheKey);
    
    if (!$block) {
        $blocks = apiCall('/blocks');
        $blockList = $blocks['blocks'] ?? [];
        
        $targetBlock = null;
        foreach ($blockList as $b) {
            if (($blockNumber && $b['index'] == $blockNumber) || 
                ($blockHash && $b['hash'] === $blockHash)) {
                $targetBlock = $b;
                break;
            }
        }
        
        if (!$targetBlock) {
            handleError('Block not found', 404);
        }
        
        $block = [
            'number' => $targetBlock['index'],
            'hash' => $targetBlock['hash'],
            'parent_hash' => $targetBlock['previous_hash'] ?? null,
            'timestamp' => $targetBlock['timestamp'],
            'miner' => getBlockMiner($targetBlock),
            'transactions' => getBlockTransactions($targetBlock),
            'gas_used' => getBlockGasUsed($targetBlock),
            'gas_limit' => 30000000,
            'difficulty' => 'N/A',
            'size' => strlen(json_encode($targetBlock)),
            'reward' => '2 DIO',
            'uncles' => [],
            'extra_data' => '0x'
        ];
        
        setCache($cacheKey, $block, 300);
    }
    
    echo json_encode($block);
}

function handleTransactions() {
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = min(100, max(10, intval($_GET['limit'] ?? 50)));
    $address = $_GET['address'] ?? '';
    
    $cacheKey = "txs_page_{$page}_{$limit}_" . md5($address);
    $cached = getCache($cacheKey);
    
    if ($cached) {
        echo json_encode($cached);
        return;
    }
    
    $transactions = apiCall('/transactions');
    $txList = $transactions['transactions'] ?? [];
    
    // Filter by address if provided
    if ($address) {
        $txList = array_filter($txList, function($tx) use ($address) {
            return $tx['from'] === $address || $tx['to'] === $address;
        });
    }
    
    // Enrich transaction data
    $enrichedTxs = array_map(function($tx) {
        return [
            'hash' => $tx['hash'],
            'block_number' => getTransactionBlock($tx),
            'timestamp' => $tx['timestamp'],
            'from' => $tx['from'],
            'to' => $tx['to'],
            'value' => formatAmount($tx['amount']),
            'gas' => 21000,
            'gas_price' => GAS_PRICE,
            'gas_used' => 21000,
            'status' => 'success',
            'method' => 'Transfer',
            'confirmations' => getConfirmations($tx)
        ];
    }, array_reverse($txList));
    
    $result = paginate($enrichedTxs, $page, $limit);
    setCache($cacheKey, $result, 60);
    
    echo json_encode($result);
}

function handleTransaction() {
    $txHash = $_GET['hash'] ?? '';
    
    if (!$txHash) {
        handleError('Transaction hash required', 400);
    }
    
    if (!validateTxHash($txHash)) {
        handleError('Invalid transaction hash', 400);
    }
    
    $cacheKey = "tx_" . $txHash;
    $transaction = getCache($cacheKey);
    
    if (!$transaction) {
        $transactions = apiCall('/transactions');
        $txList = $transactions['transactions'] ?? [];
        
        $targetTx = null;
        foreach ($txList as $tx) {
            if ($tx['hash'] === $txHash) {
                $targetTx = $tx;
                break;
            }
        }
        
        if (!$targetTx) {
            handleError('Transaction not found', 404);
        }
        
        $transaction = [
            'hash' => $targetTx['hash'],
            'block_number' => getTransactionBlock($targetTx),
            'timestamp' => $targetTx['timestamp'],
            'from' => $targetTx['from'],
            'to' => $targetTx['to'],
            'value' => formatAmount($targetTx['amount']),
            'gas' => 21000,
            'gas_price' => GAS_PRICE,
            'gas_used' => 21000,
            'status' => 'success',
            'method' => 'Transfer',
            'confirmations' => getConfirmations($targetTx),
            'logs' => [],
            'events' => [],
            'internal_txs' => [],
            'trace' => []
        ];
        
        setCache($cacheKey, $transaction, 300);
    }
    
    echo json_encode($transaction);
}

function handleAddress() {
    $address = $_GET['address'] ?? '';
    
    if (!$address) {
        handleError('Address required', 400);
    }
    
    if (!validateAddress($address)) {
        handleError('Invalid address', 400);
    }
    
    $cacheKey = "address_" . $address;
    $data = getCache($cacheKey);
    
    if (!$data) {
        $wallets = apiCall('/wallets');
        $walletList = $wallets['wallets'] ?? [];
        
        $wallet = null;
        foreach ($walletList as $w) {
            if ($w['address'] === $address) {
                $wallet = $w;
                break;
            }
        }
        
        $transactions = apiCall('/transactions');
        $txList = $transactions['transactions'] ?? [];
        
        $addressTxs = array_filter($txList, function($tx) use ($address) {
            return $tx['from'] === $address || $tx['to'] === $address;
        });
        
        $sent = array_sum(array_map(function($tx) use ($address) {
            return $tx['from'] === $address ? $tx['amount'] : 0;
        }, $addressTxs));
        
        $received = array_sum(array_map(function($tx) use ($address) {
            return $tx['to'] === $address ? $tx['amount'] : 0;
        }, $addressTxs));
        
        $data = [
            'address' => $address,
            'balance' => $wallet ? formatAmount($wallet['balance']) : '0',
            'nonce' => $wallet ? $wallet['nonce'] : 0,
            'transaction_count' => count($addressTxs),
            'value_sent' => formatAmount($sent),
            'value_received' => formatAmount($received),
            'is_contract' => false,
            'is_validator' => false,
            'tokens' => [],
            'nfts' => [],
            'staking_info' => [],
            'first_seen' => count($addressTxs) > 0 ? min(array_column($addressTxs, 'timestamp')) : null,
            'last_seen' => count($addressTxs) > 0 ? max(array_column($addressTxs, 'timestamp')) : null
        ];
        
        setCache($cacheKey, $data, 120);
    }
    
    echo json_encode($data);
}

function handleWallets() {
    $wallets = apiCall('/wallets');
    $walletList = $wallets['wallets'] ?? [];
    
    $enrichedWallets = array_map(function($wallet) {
        return [
            'address' => $wallet['address'],
            'name' => $wallet['name'],
            'balance' => formatAmount($wallet['balance']),
            'nonce' => $wallet['nonce'],
            'created_at' => $wallet['created_at'],
            'transaction_count' => getWalletTransactionCount($wallet['address']),
            'is_validator' => false,
            'staked_amount' => '0'
        ];
    }, $walletList);
    
    echo json_encode([
        'wallets' => $enrichedWallets,
        'count' => count($enrichedWallets)
    ]);
}

function handleWallet() {
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'POST':
            createWallet();
            break;
        case 'GET':
            getWallet();
            break;
        default:
            handleError('Method not allowed', 405);
    }
}

function createWallet() {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = sanitizeInput($input['name'] ?? '');
    
    if (!$name) {
        handleError('Wallet name required', 400);
    }
    
    $result = apiCall('/wallets', 'POST', ['name' => $name]);
    
    echo json_encode([
        'success' => true,
        'wallet' => [
            'address' => $result['wallet']['address'],
            'name' => $result['wallet']['name'],
            'private_key' => $result['wallet']['private_key'], // Only for demo
            'public_key' => $result['wallet']['public_key'],
            'created_at' => $result['wallet']['created_at']
        ]
    ]);
}

function getWallet() {
    $address = $_GET['address'] ?? '';
    
    if (!$address) {
        handleError('Address required', 400);
    }
    
    $wallets = apiCall('/wallets');
    $walletList = $wallets['wallets'] ?? [];
    
    foreach ($walletList as $wallet) {
        if ($wallet['address'] === $address) {
            echo json_encode([
                'address' => $wallet['address'],
                'name' => $wallet['name'],
                'balance' => formatAmount($wallet['balance']),
                'nonce' => $wallet['nonce'],
                'created_at' => $wallet['created_at']
            ]);
            return;
        }
    }
    
    handleError('Wallet not found', 404);
}

function handleContracts() {
    echo json_encode([
        'contracts' => [],
        'count' => 0,
        'message' => 'Smart contracts feature coming soon'
    ]);
}

function handleValidators() {
    $wallets = apiCall('/wallets');
    $walletList = $wallets['wallets'] ?? [];
    
    $validators = array_map(function($wallet, $index) {
        return [
            'address' => $wallet['address'],
            'name' => "Validator #" . ($index + 1),
            'status' => 'active',
            'commission' => '5%',
            'staked_amount' => formatAmount(rand(1000000, 10000000) * pow(10, 18)),
            'reward_rate' => '8.5%',
            'uptime' => '99.9%',
            'last_active' => date('Y-m-d H:i:s')
        ];
    }, $walletList, array_keys($walletList));
    
    echo json_encode([
        'validators' => $validators,
        'count' => count($validators),
        'total_staked' => formatAmount(array_sum(array_column($validators, 'staked_amount')) * pow(10, 18))
    ]);
}

function handleTokens() {
    echo json_encode([
        'tokens' => [
            [
                'address' => '0x0000000000000000000000000000000000000000',
                'symbol' => 'DIO',
                'name' => 'Diora Token',
                'decimals' => 18,
                'total_supply' => '1000000000000000000000000000',
                'holders' => count(apiCall('/wallets')['wallets'] ?? []),
                'price' => '0.00',
                'market_cap' => '0',
                'volume_24h' => '0'
            ]
        ],
        'count' => 1
    ]);
}

function handleSearch() {
    $query = sanitizeInput($_GET['q'] ?? '');
    
    if (!$query) {
        handleError('Search query required', 400);
    }
    
    $results = [];
    
    // Search by address
    if (validateAddress($query)) {
        $wallets = apiCall('/wallets');
        foreach ($wallets['wallets'] ?? [] as $wallet) {
            if ($wallet['address'] === $query) {
                $results[] = [
                    'type' => 'address',
                    'address' => $wallet['address'],
                    'name' => $wallet['name'],
                    'balance' => formatAmount($wallet['balance'])
                ];
                break;
            }
        }
    }
    
    // Search by transaction hash
    if (validateTxHash($query)) {
        $transactions = apiCall('/transactions');
        foreach ($transactions['transactions'] ?? [] as $tx) {
            if ($tx['hash'] === $query) {
                $results[] = [
                    'type' => 'transaction',
                    'hash' => $tx['hash'],
                    'from' => $tx['from'],
                    'to' => $tx['to'],
                    'value' => formatAmount($tx['amount'])
                ];
                break;
            }
        }
    }
    
    // Search by block number
    if (is_numeric($query)) {
        $blocks = apiCall('/blocks');
        foreach ($blocks['blocks'] ?? [] as $block) {
            if ($block['index'] == $query) {
                $results[] = [
                    'type' => 'block',
                    'number' => $block['index'],
                    'hash' => $block['hash'],
                    'timestamp' => $block['timestamp']
                ];
                break;
            }
        }
    }
    
    echo json_encode([
        'query' => $query,
        'results' => $results,
        'count' => count($results)
    ]);
}

function handleAnalytics() {
    echo json_encode([
        'network_stats' => getNetworkStats(),
        'daily_stats' => getDailyStats(),
        'hourly_stats' => getHourlyStats(),
        'gas_stats' => getGasStats(),
        'validator_stats' => getValidatorStats()
    ]);
}

// Helper functions
function calculateTPS($transactions) {
    if (empty($transactions)) return 0;
    
    $now = time();
    $recentTxs = array_filter($transactions, function($tx) use ($now) {
        $txTime = strtotime($tx['timestamp']);
        return ($now - $txTime) < 60;
    });
    
    return count($recentTxs);
}

function calculateAvgBlockTime($blocks) {
    if (count($blocks) < 2) return 2.0;
    
    $times = array_column($blocks, 'timestamp');
    $intervals = [];
    
    for ($i = 1; $i < count($times); $i++) {
        $intervals[] = strtotime($times[$i]) - strtotime($times[$i-1]);
    }
    
    return count($intervals) > 0 ? round(array_sum($intervals) / count($intervals), 1) : 2.0;
}

function calculateNetworkLoad() {
    return rand(15, 85); // Simulated
}

function calculateTransactions24h($transactions) {
    $yesterday = time() - 86400;
    return count(array_filter($transactions, function($tx) use ($yesterday) {
        return strtotime($tx['timestamp']) > $yesterday;
    }));
}

function calculateMarketCap() {
    return 0; // Will be implemented with real price data
}

function calculateCirculatingSupply() {
    return 1000000000; // 1 billion DIO
}

function calculateStakedAmount() {
    return 250000000; // 250 million DIO staked
}

function getMempoolSize() {
    return rand(0, 50); // Simulated
}

function countTransactionsInBlock($block) {
    return strpos($block['data'], 'Transaction') !== false ? 1 : 0;
}

function getBlockMiner($block) {
    return '0x' . substr(md5($block['hash']), 0, 40);
}

function getBlockGasUsed($block) {
    return 21000; // Simplified
}

function getBlockTransactions($block) {
    return [];
}

function getTransactionBlock($tx) {
    return rand(1, 999999); // Simplified
}

function getConfirmations($tx) {
    $stats = apiCall('/status');
    return ($stats['blocks'] ?? 0) - getTransactionBlock($tx);
}

function getWalletTransactionCount($address) {
    $transactions = apiCall('/transactions');
    $txList = $transactions['transactions'] ?? [];
    
    return count(array_filter($txList, function($tx) use ($address) {
        return $tx['from'] === $address || $tx['to'] === $address;
    }));
}

function getNetworkStats() {
    return [
        'total_blocks' => apiCall('/status')['blocks'] ?? 0,
        'total_transactions' => count(apiCall('/transactions')['transactions'] ?? []),
        'active_addresses' => count(apiCall('/wallets')['wallets'] ?? []),
        'gas_price' => GAS_PRICE,
        'network_hashrate' => '1.2 TH/s',
        'difficulty' => '2.5T'
    ];
}

function getDailyStats() {
    $stats = [];
    for ($i = 29; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stats[] = [
            'date' => $date,
            'transactions' => rand(1000, 5000),
            'blocks' => rand(40000, 45000),
            'gas_used' => rand(800000000, 1200000000)
        ];
    }
    return $stats;
}

function getHourlyStats() {
    $stats = [];
    for ($i = 23; $i >= 0; $i--) {
        $hour = date('H:00', strtotime("-$i hours"));
        $stats[] = [
            'hour' => $hour,
            'tps' => rand(10, 100),
            'gas_price' => rand(15, 35)
        ];
    }
    return $stats;
}

function getGasStats() {
    return [
        'current_gas_price' => GAS_PRICE,
        'avg_gas_price_24h' => rand(18000000000, 25000000000),
        'gas_limit' => 30000000,
        'gas_used_24h' => rand(2000000000000, 2800000000000)
    ];
}

function getValidatorStats() {
    return [
        'total_validators' => count(apiCall('/wallets')['wallets'] ?? []),
        'active_validators' => count(apiCall('/wallets')['wallets'] ?? []),
        'total_staked' => calculateStakedAmount(),
        'avg_apr' => '8.5%'
    ];
}
?>
