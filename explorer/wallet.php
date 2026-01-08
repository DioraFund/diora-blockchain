<?php
// Wallet functions for Diora Explorer

// Wallet management functions
function createWallet($name, $password) {
    // Generate secure random private key
    $privateKey = '0x' . bin2hex(random_bytes(32));
    
    // Generate public key (simplified)
    $publicKey = '0x' . substr($privateKey, 2) . 'public';
    
    // Generate address (simplified)
    $address = '0x' . substr(md5($publicKey), 0, 40);
    
    // Store wallet data (in production, use encrypted database)
    $wallet = [
        'name' => $name,
        'address' => $address,
        'private_key' => $privateKey,
        'public_key' => $publicKey,
        'balance' => '0',
        'nonce' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return $wallet;
}

function importWallet($privateKey) {
    // Validate private key format
    if (!preg_match('/^0x[a-fA-F0-9]{64}$/', $privateKey)) {
        throw new Exception('Invalid private key format');
    }
    
    // Generate public key (simplified)
    $publicKey = '0x' . substr($privateKey, 2) . 'public';
    
    // Generate address (simplified)
    $address = '0x' . substr(md5($publicKey), 0, 40);
    
    return [
        'address' => $address,
        'private_key' => $privateKey,
        'public_key' => $publicKey,
        'name' => 'Imported Wallet',
        'balance' => '0',
        'nonce' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ];
}

function getWalletBalance($address) {
    try {
        $response = @file_get_contents(BLOCKCHAIN_API_URL . "/wallets/$address/balance");
        if ($response) {
            $data = json_decode($response, true);
            return $data['balance'] ?? '0';
        }
    } catch (Exception $e) {
        error_log("Error getting wallet balance: " . $e->getMessage());
    }
    
    return '0';
}

function sendTransaction($from, $to, $amount) {
    try {
        $postData = json_encode([
            'to' => $to,
            'amount' => $amount
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => $postData,
                'timeout' => 30
            ]
        ]);
        
        $response = @file_get_contents(BLOCKCHAIN_API_URL . "/wallets/$from/send", false, $context);
        
        if ($response) {
            return json_decode($response, true);
        }
    } catch (Exception $e) {
        error_log("Error sending transaction: " . $e->getMessage());
    }
    
    return ['error' => 'Transaction failed'];
}

function getWalletTransactions($address) {
    try {
        $response = @file_get_contents(BLOCKCHAIN_API_URL . '/transactions');
        if ($response) {
            $data = json_decode($response, true);
            $transactions = $data['transactions'] ?? [];
            
            // Filter transactions for this address
            return array_filter($transactions, function($tx) use ($address) {
                return $tx['from'] === $address || $tx['to'] === $address;
            });
        }
    } catch (Exception $e) {
        error_log("Error getting wallet transactions: " . $e->getMessage());
    }
    
    return [];
}

function validateAddress($address) {
    return preg_match('/^0x[a-fA-F0-9]{40}$/', $address);
}

function validatePrivateKey($privateKey) {
    return preg_match('/^0x[a-fA-F0-9]{64}$/', $privateKey);
}

function formatAmount($amount, $decimals = 18) {
    $value = $amount / pow(10, $decimals);
    return number_format($value, 6, '.', ',');
}

function formatAddress($address) {
    return validateAddress($address) ? substr($address, 0, 6) . '...' . substr($address, -4) : $address;
}

function generateQRCode($address) {
    // Simple QR code placeholder - in production use a proper QR library
    return "QR: " . substr($address, 0, 20) . "...";
}

function validateTransaction($to, $amount, $balance) {
    if (!validateAddress($to)) {
        return ['valid' => false, 'error' => 'Invalid recipient address'];
    }
    
    if ($amount <= 0) {
        return ['valid' => false, 'error' => 'Amount must be greater than 0'];
    }
    
    if ($amount > $balance) {
        return ['valid' => false, 'error' => 'Insufficient balance'];
    }
    
    return ['valid' => true];
}

function estimateGasFee($gasPrice = 20000000000, $gasLimit = 21000) {
    $feeWei = $gasPrice * $gasLimit;
    return formatAmount($feeWei);
}

function getNetworkGasPrice() {
    try {
        $response = @file_get_contents(BLOCKCHAIN_API_URL . '/status');
        if ($response) {
            $data = json_decode($response, true);
            return 20000000000; // 20 Gwei default
        }
    } catch (Exception $e) {
        // Fallback
    }
    
    return 20000000000; // 20 Gwei
}

function backupWallet($wallet) {
    $backup = [
        'address' => $wallet['address'],
        'private_key' => $wallet['private_key'],
        'name' => $wallet['name'],
        'created_at' => $wallet['created_at'],
        'backup_date' => date('Y-m-d H:i:s')
    ];
    
    return json_encode($backup, JSON_PRETTY_PRINT);
}

function validateSeedPhrase($phrase) {
    $words = explode(' ', $phrase);
    return count($words) === 12 || count($words) === 24;
}

function generateSeedPhrase() {
    $wordlist = [
        'abandon', 'ability', 'able', 'about', 'above', 'absent', 'absorb', 'abstract',
        'absurd', 'abuse', 'access', 'accident', 'account', 'accuse', 'achieve', 'acid',
        'acoustic', 'acquire', 'across', 'act', 'action', 'actor', 'actress', 'actual'
    ];
    
    $phrase = [];
    for ($i = 0; $i < 12; $i++) {
        $phrase[] = $wordlist[array_rand($wordlist)];
    }
    
    return implode(' ', $phrase);
}

function getWalletInfo($address) {
    $balance = getWalletBalance($address);
    $transactions = getWalletTransactions($address);
    
    $sent = 0;
    $received = 0;
    
    foreach ($transactions as $tx) {
        if ($tx['from'] === $address) {
            $sent += $tx['amount'];
        } else {
            $received += $tx['amount'];
        }
    }
    
    return [
        'address' => $address,
        'balance' => formatAmount($balance),
        'transaction_count' => count($transactions),
        'total_sent' => formatAmount($sent),
        'total_received' => formatAmount($received),
        'first_transaction' => !empty($transactions) ? min(array_column($transactions, 'timestamp')) : null,
        'last_transaction' => !empty($transactions) ? max(array_column($transactions, 'timestamp')) : null
    ];
}

function isContractAddress($address) {
    // Simplified check - in production, verify if address has code
    return false;
}

function getTokenBalances($address) {
    // Placeholder for token balances
    return [
        [
            'token' => 'DIO',
            'symbol' => 'DIO',
            'balance' => getWalletBalance($address),
            'decimals' => 18,
            'contract_address' => '0x0000000000000000000000000000000000000000'
        ]
    ];
}

function getNFTCollection($address) {
    // Placeholder for NFT collection
    return [];
}

function getStakingInfo($address) {
    // Placeholder for staking information
    return [
        'staked_amount' => '0',
        'rewards' => '0',
        'apr' => '8.5%',
        'unlock_time' => null
    ];
}

function signMessage($message, $privateKey) {
    // Simplified message signing
    $hash = hash('sha256', $message);
    return '0x' . bin2hex(hex2bin($hash) ^ hex2bin(substr($privateKey, 2)));
}

function verifySignature($message, $signature, $address) {
    // Simplified signature verification
    return true; // Placeholder
}

function encryptPrivateKey($privateKey, $password) {
    // Simplified encryption - use proper encryption in production
    return base64_encode($privateKey . ':' . $password);
}

function decryptPrivateKey($encryptedKey, $password) {
    // Simplified decryption - use proper decryption in production
    $decoded = base64_decode($encryptedKey);
    $parts = explode(':', $decoded);
    
    if (count($parts) === 2 && $parts[1] === $password) {
        return $parts[0];
    }
    
    return false;
}

function getWalletSecurityScore($wallet) {
    $score = 0;
    
    // Check if wallet has transactions
    if ($wallet['transaction_count'] > 0) {
        $score += 20;
    }
    
    // Check balance diversity
    if ($wallet['balance'] > 0) {
        $score += 10;
    }
    
    // Check activity frequency
    if ($wallet['transaction_count'] > 10) {
        $score += 20;
    }
    
    return min(100, $score + 50); // Base score of 50
}

function exportWalletData($address) {
    $info = getWalletInfo($address);
    $transactions = getWalletTransactions($address);
    
    return [
        'wallet_info' => $info,
        'transactions' => $transactions,
        'tokens' => getTokenBalances($address),
        'nfts' => getNFTCollection($address),
        'staking' => getStakingInfo($address),
        'export_date' => date('Y-m-d H:i:s')
    ];
}
?>
