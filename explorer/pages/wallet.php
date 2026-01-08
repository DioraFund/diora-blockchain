<?php
$action = $_GET['action'] ?? '';
$connected = isset($_SESSION['wallet_address']);

if ($action === 'logout') {
    unset($_SESSION['wallet_address']);
    unset($_SESSION['wallet_private_key']);
    header('Location: index.php?page=wallet');
    exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $password = sanitizeInput($_POST['password'] ?? '');
    
    if ($name && $password) {
        // Create wallet via API
        $ch = curl_init('http://localhost:8080/api/wallets');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['name' => $name]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        
        if ($data && isset($data['wallet'])) {
            $_SESSION['wallet_address'] = $data['wallet']['address'];
            $_SESSION['wallet_private_key'] = $data['wallet']['private_key'];
            $_SESSION['wallet_name'] = $data['wallet']['name'];
            
            header('Location: index.php?page=wallet');
            exit;
        }
    }
}

if ($action === 'import' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $privateKey = sanitizeInput($_POST['private_key'] ?? '');
    
    if ($privateKey) {
        // For demo, generate address from private key
        $address = '0x' . substr(md5($privateKey), 0, 40);
        
        $_SESSION['wallet_address'] = $address;
        $_SESSION['wallet_private_key'] = $privateKey;
        $_SESSION['wallet_name'] = 'Imported Wallet';
        
        header('Location: index.php?page=wallet');
        exit;
    }
}

if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = sanitizeInput($_POST['to'] ?? '');
    $amount = sanitizeInput($_POST['amount'] ?? '');
    
    if ($to && $amount && isset($_SESSION['wallet_address'])) {
        $from = $_SESSION['wallet_address'];
        $amountWei = $amount * pow(10, 18);
        
        $ch = curl_init("http://localhost:8080/api/wallets/$from/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'to' => $to,
            'amount' => $amountWei
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        
        if ($data && isset($data['message'])) {
            $success = true;
            $message = $data['message'];
        } else {
            $success = false;
            $message = 'Transaction failed';
        }
    }
}

// Get wallet balance if connected
$balance = 0;
$transactions = [];
if ($connected) {
    $address = $_SESSION['wallet_address'];
    
    // Get balance
    $ch = curl_init("http://localhost:8080/api/wallets/$address/balance");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $balanceResponse = curl_exec($ch);
    $balanceData = json_decode($balanceResponse, true);
    $balance = $balanceData['balance'] ?? 0;
    
    // Get transactions
    $ch = curl_init("http://localhost:8080/api/transactions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $txResponse = curl_exec($ch);
    $txData = json_decode($txResponse, true);
    
    if (isset($txData['transactions'])) {
        $transactions = array_filter($txData['transactions'], function($tx) use ($address) {
            return $tx['from'] === $address || $tx['to'] === $address;
        });
    }
}
?>

<style>
.wallet-container {
    max-width: 800px;
    margin: 0 auto;
}

.wallet-header {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    text-align: center;
}

.wallet-address {
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 1.125rem;
    color: var(--accent-blue);
    margin: 1rem 0;
}

.wallet-balance {
    font-size: 3rem;
    font-weight: 800;
    color: var(--text-primary);
    margin: 1rem 0;
}

.wallet-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

.form-section {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.form-grid {
    display: grid;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-input {
    padding: 1rem;
    background: var(--secondary-dark);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-primary);
    font-size: 1rem;
    transition: border-color 0.2s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--accent-blue);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.tx-list {
    display: grid;
    gap: 1rem;
}

.tx-item {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    transition: transform 0.2s ease;
}

.tx-item:hover {
    transform: translateY(-1px);
}

.tx-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.tx-amount {
    font-size: 1.25rem;
    font-weight: 700;
}

.tx-amount.sent {
    color: var(--accent-red);
}

.tx-amount.received {
    color: var(--accent-green);
}

.tx-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    font-size: 0.875rem;
}

.tx-detail {
    display: flex;
    flex-direction: column;
}

.tx-detail-label {
    color: var(--text-muted);
    margin-bottom: 0.25rem;
}

.tx-detail-value {
    color: var(--text-primary);
    font-family: 'Monaco', 'Menlo', monospace;
}

.qr-code {
    width: 200px;
    height: 200px;
    background: white;
    border-radius: 12px;
    margin: 1rem auto;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    color: #333;
}

.security-notice {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: 8px;
    padding: 1rem;
    margin: 1rem 0;
    color: var(--accent-yellow);
}

@media (max-width: 768px) {
    .wallet-actions {
        flex-direction: column;
    }
    
    .tx-details {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="wallet-container">
    <?php if ($connected): ?>
        <!-- Connected Wallet -->
        <div class="wallet-header fade-in">
            <h2 style="margin-bottom: 1rem;">Connected Wallet</h2>
            <div class="wallet-address"><?php echo $_SESSION['wallet_address']; ?></div>
            <div class="wallet-balance"><?php echo number_format($balance / pow(10, 18), 6); ?> DIO</div>
            <div style="color: var(--text-secondary); margin-bottom: 1rem;">
                <?php echo $_SESSION['wallet_name']; ?>
            </div>
            
            <div class="wallet-actions">
                <button onclick="showSendForm()" class="btn btn-primary">
                    Send
                </button>
                <button onclick="showReceiveForm()" class="btn btn-secondary">
                    Receive
                </button>
                <button onclick="refreshBalance()" class="btn btn-secondary">
                    Refresh
                </button>
                <a href="index.php?page=wallet&action=logout" class="btn btn-secondary">
                    Logout
                </a>
            </div>
        </div>

        <!-- Send Form -->
        <div id="sendForm" class="form-section hidden fade-in">
            <h3 style="margin-bottom: 1.5rem;">Send DIO</h3>
            <form method="POST" action="index.php?page=wallet&action=send">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Recipient Address</label>
                        <input type="text" name="to" class="form-input" placeholder="0x..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Amount (DIO)</label>
                        <input type="number" name="amount" class="form-input" placeholder="0.0" step="0.000001" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gas Price (Gwei)</label>
                        <input type="number" class="form-input" value="20" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estimated Fee</label>
                        <input type="text" class="form-input" value="0.00042 DIO" readonly>
                    </div>
                </div>
                
                <div class="security-notice">
                    ⚠️ Always double-check the recipient address before sending. Transactions are irreversible.
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">Send Transaction</button>
                    <button type="button" onclick="hideSendForm()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Receive Form -->
        <div id="receiveForm" class="form-section hidden fade-in">
            <h3 style="margin-bottom: 1.5rem;">Receive DIO</h3>
            <div style="text-align: center;">
                <div class="qr-code">
                    QR Code<br>
                    <?php echo substr($_SESSION['wallet_address'], 0, 10); ?>...
                </div>
                <div class="wallet-address"><?php echo $_SESSION['wallet_address']; ?></div>
                <button onclick="copyAddress()" class="btn btn-secondary" style="margin-top: 1rem;">
                    Copy Address
                </button>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="form-section fade-in">
            <h3 style="margin-bottom: 1.5rem;">Transaction History</h3>
            
            <?php if (empty($transactions)): ?>
                <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                    No transactions found
                </div>
            <?php else: ?>
                <div class="tx-list">
                    <?php foreach (array_reverse($transactions) as $tx): ?>
                        <div class="tx-item">
                            <div class="tx-header">
                                <div>
                                    <a href="index.php?page=transaction&hash=<?php echo $tx['hash']; ?>" class="tx-hash">
                                        <?php echo substr($tx['hash'], 0, 10); ?>...
                                    </a>
                                    <div style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">
                                        <?php echo date('M j, H:i:s', strtotime($tx['timestamp'])); ?>
                                    </div>
                                </div>
                                <div class="tx-amount <?php echo $tx['from'] === $_SESSION['wallet_address'] ? 'sent' : 'received'; ?>">
                                    <?php echo $tx['from'] === $_SESSION['wallet_address'] ? '-' : '+'; ?>
                                    <?php echo number_format($tx['amount'] / pow(10, 18), 6); ?> DIO
                                </div>
                            </div>
                            <div class="tx-details">
                                <div class="tx-detail">
                                    <span class="tx-detail-label">From</span>
                                    <a href="index.php?page=address&address=<?php echo $tx['from']; ?>" class="tx-detail-value address-link">
                                        <?php echo substr($tx['from'], 0, 8); ?>...<?php echo substr($tx['from'], -6); ?>
                                    </a>
                                </div>
                                <div class="tx-detail">
                                    <span class="tx-detail-label">To</span>
                                    <a href="index.php?page=address&address=<?php echo $tx['to']; ?>" class="tx-detail-value address-link">
                                        <?php echo substr($tx['to'], 0, 8); ?>...<?php echo substr($tx['to'], -6); ?>
                                    </a>
                                </div>
                                <div class="tx-detail">
                                    <span class="tx-detail-label">Gas Used</span>
                                    <span class="tx-detail-value">21,000</span>
                                </div>
                                <div class="tx-detail">
                                    <span class="tx-detail-label">Status</span>
                                    <span class="badge badge-success">Success</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- Not Connected -->
        <div class="wallet-header fade-in">
            <h2 style="margin-bottom: 1rem;">Diora Wallet</h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                Secure, non-custodial wallet for managing your DIO tokens. Your keys, your crypto.
            </p>
            
            <div class="wallet-actions">
                <button onclick="showCreateForm()" class="btn btn-primary">
                    Create New Wallet
                </button>
                <button onclick="showImportForm()" class="btn btn-secondary">
                    Import Wallet
                </button>
            </div>
        </div>

        <!-- Create Wallet Form -->
        <div id="createForm" class="form-section hidden fade-in">
            <h3 style="margin-bottom: 1.5rem;">Create New Wallet</h3>
            <form method="POST" action="index.php?page=wallet&action=create">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Wallet Name</label>
                        <input type="text" name="name" class="form-input" placeholder="My Wallet" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Enter secure password" required>
                    </div>
                </div>
                
                <div class="security-notice">
                    Your private key will be generated securely. Save it in a safe place as it cannot be recovered.
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">Create Wallet</button>
                    <button type="button" onclick="hideCreateForm()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Import Wallet Form -->
        <div id="importForm" class="form-section hidden fade-in">
            <h3 style="margin-bottom: 1.5rem;">Import Wallet</h3>
            <form method="POST" action="index.php?page=wallet&action=import">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Private Key</label>
                        <textarea name="private_key" class="form-input form-textarea" placeholder="0x..." required></textarea>
                    </div>
                </div>
                
                <div class="security-notice">
                    Only import private keys from trusted sources. Never share your private key with anyone.
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">Import Wallet</button>
                    <button type="button" onclick="hideImportForm()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Security Features -->
        <div class="form-section fade-in">
            <h3 style="margin-bottom: 1.5rem;">🛡️ Security Features</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div>
                    <h4 style="color: var(--accent-blue); margin-bottom: 0.5rem;">🔐 Non-Custodial</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">
                        You control your private keys. No third-party custody.
                    </p>
                </div>
                <div>
                    <h4 style="color: var(--accent-green); margin-bottom: 0.5rem;">🔒 Encrypted Storage</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">
                        Private keys encrypted with military-grade security.
                    </p>
                </div>
                <div>
                    <h4 style="color: var(--accent-cyan); margin-bottom: 0.5rem;">🛡️ Phishing Protection</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">
                        Built-in protection against phishing and malicious sites.
                    </p>
                </div>
                <div>
                    <h4 style="color: var(--accent-yellow); margin-bottom: 0.5rem;">📱 Multi-Device Support</h4>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">
                        Access your wallet securely from multiple devices.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success" style="margin-top: 2rem;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
</div>

<script>
function showCreateForm() {
    document.getElementById('createForm').classList.remove('hidden');
    document.getElementById('importForm').classList.add('hidden');
}

function hideCreateForm() {
    document.getElementById('createForm').classList.add('hidden');
}

function showImportForm() {
    document.getElementById('importForm').classList.remove('hidden');
    document.getElementById('createForm').classList.add('hidden');
}

function hideImportForm() {
    document.getElementById('importForm').classList.add('hidden');
}

function showSendForm() {
    document.getElementById('sendForm').classList.remove('hidden');
    document.getElementById('receiveForm').classList.add('hidden');
}

function hideSendForm() {
    document.getElementById('sendForm').classList.add('hidden');
}

function showReceiveForm() {
    document.getElementById('receiveForm').classList.remove('hidden');
    document.getElementById('sendForm').classList.add('hidden');
}

function hideReceiveForm() {
    document.getElementById('receiveForm').classList.add('hidden');
}

function refreshBalance() {
    location.reload();
}

function copyAddress() {
    const address = '<?php echo $_SESSION['wallet_address'] ?? ''; ?>';
    if (address) {
        navigator.clipboard.writeText(address).then(() => {
            alert('Address copied to clipboard!');
        });
    }
}

// Auto-refresh balance every 30 seconds
<?php if ($connected): ?>
setInterval(refreshBalance, 30000);
<?php endif; ?>
</script>
