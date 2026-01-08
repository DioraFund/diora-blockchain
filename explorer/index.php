<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once 'config.php';

// Only load API for API requests
if (strpos($_SERVER['REQUEST_URI'], '/api.php') !== false) {
    require_once 'api.php';
    exit;
}

// Load wallet functions if needed
if (isset($_GET['page']) && $_GET['page'] === 'wallet') {
    require_once 'wallet.php';
}

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? '';
$address = $_GET['address'] ?? '';
$hash = $_GET['hash'] ?? '';
$block = $_GET['block'] ?? '';

// Get network stats
$networkStats = getNetworkStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diora Blockchain Explorer - Professional Network Monitor</title>
    <meta name="description" content="Professional blockchain explorer for Diora network with real-time monitoring, wallet integration, and advanced analytics">
    <meta name="keywords" content="diora, blockchain, explorer, ethereum, defi, web3, crypto">
    
    <!-- Professional CSS -->
    <style>
        :root {
            --primary-dark: #0a0e27;
            --secondary-dark: #151932;
            --tertiary-dark: #1e2139;
            --accent-blue: #1e40af;
            --accent-cyan: #06b6d4;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --accent-yellow: #f59e0b;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border-color: #334155;
            --bg-card: #1e293b;
            --bg-hover: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, monospace;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Professional Header */
        .header {
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 72px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            text-decoration: none;
            letter-spacing: -0.02em;
        }

        .nav {
            display: flex;
            gap: 0.5rem;
            list-style: none;
        }

        .nav-link {
            padding: 0.75rem 1.25rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .nav-link.active {
            background: var(--accent-blue);
            color: white;
        }

        .wallet-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-blue), #2563eb);
            color: white;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--bg-hover);
        }

        /* Hero Section */
        .hero {
            padding: 4rem 2rem;
            text-align: center;
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.1) 0%, rgba(6, 182, 212, 0.05) 100%);
            border-bottom: 1px solid var(--border-color);
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--accent-cyan) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.03em;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Search Section */
        .search-container {
            max-width: 800px;
            margin: 0 auto 3rem;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1.25rem 4rem 1.25rem 1.5rem;
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: 16px;
            font-size: 1rem;
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
        }

        .search-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--accent-blue);
            color: white;
            border: none;
            border-radius: 12px;
            width: 48px;
            height: 48px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .search-btn:hover {
            background: #2563eb;
        }

        .search-filters {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            justify-content: center;
        }

        .filter-chip {
            padding: 0.5rem 1rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-chip:hover,
        .filter-chip.active {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: white;
        }

        /* Network Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1400px;
            margin: 0 auto 3rem;
            padding: 0 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-cyan));
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-variant-numeric: tabular-nums;
        }

        .stat-change {
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-change.positive {
            color: var(--accent-green);
        }

        .stat-change.negative {
            color: var(--accent-red);
        }

        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem 4rem;
        }

        /* Tables */
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .table-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .table-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: var(--secondary-dark);
            padding: 1rem 2rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table td {
            padding: 1.25rem 2rem;
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
            font-size: 0.95rem;
        }

        .data-table tbody tr:hover {
            background: var(--bg-hover);
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Links and Addresses */
        .address-link {
            color: var(--accent-blue);
            text-decoration: none;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.875rem;
            transition: color 0.2s ease;
        }

        .address-link:hover {
            color: var(--accent-cyan);
            text-decoration: underline;
        }

        .tx-hash {
            color: var(--accent-blue);
            text-decoration: none;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.875rem;
            transition: color 0.2s ease;
        }

        .tx-hash:hover {
            color: var(--accent-cyan);
            text-decoration: underline;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent-green);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--accent-yellow);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--accent-red);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .badge-info {
            background: rgba(6, 182, 212, 0.1);
            color: var(--accent-cyan);
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 4rem;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 3px solid var(--border-color);
            border-top: 3px solid var(--accent-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                height: auto;
                padding: 1rem;
            }

            .nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }

            .container {
                padding: 0 1rem 2rem;
            }

            .data-table {
                font-size: 0.875rem;
            }

            .data-table th,
            .data-table td {
                padding: 1rem;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body>
    <!-- Professional Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <div class="logo-icon">D</div>
                <span>DIORA</span>
            </div>
            
            <nav class="nav">
                <a href="index.php?page=home" class="nav-link <?php echo $page == 'home' ? 'active' : ''; ?>">Home</a>
                <a href="index.php?page=blocks" class="nav-link <?php echo $page == 'blocks' ? 'active' : ''; ?>">Blocks</a>
                <a href="index.php?page=transactions" class="nav-link <?php echo $page == 'transactions' ? 'active' : ''; ?>">Transactions</a>
                <a href="index.php?page=contracts" class="nav-link <?php echo $page == 'contracts' ? 'active' : ''; ?>">Contracts</a>
                <a href="index.php?page=tokens" class="nav-link <?php echo $page == 'tokens' ? 'active' : ''; ?>">Tokens</a>
                <a href="index.php?page=validators" class="nav-link <?php echo $page == 'validators' ? 'active' : ''; ?>">Validators</a>
                <a href="index.php?page=analytics" class="nav-link <?php echo $page == 'analytics' ? 'active' : ''; ?>">Analytics</a>
            </nav>

            <div class="wallet-section">
                <?php if (isset($_SESSION['wallet_address'])): ?>
                    <span class="address-link"><?php echo substr($_SESSION['wallet_address'], 0, 6) . '...' . substr($_SESSION['wallet_address'], -4); ?></span>
                    <a href="index.php?page=wallet&action=logout" class="btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="index.php?page=wallet" class="btn btn-primary">
                        <span>🔐</span> Connect Wallet
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Hero Section with Search -->
    <section class="hero">
        <h1 class="hero-title">Diora Blockchain Explorer</h1>
        <p class="hero-subtitle">Professional network monitoring and analytics platform for Diora blockchain ecosystem</p>
        
        <div class="search-container">
            <form method="GET" action="index.php">
                <input type="text" name="search" class="search-input" placeholder="Search by address, transaction hash, block number, or token..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="search-btn">🔍</button>
                <input type="hidden" name="page" value="search">
            </form>
            <div class="search-filters">
                <div class="filter-chip">All</div>
                <div class="filter-chip">Addresses</div>
                <div class="filter-chip">Transactions</div>
                <div class="filter-chip">Blocks</div>
                <div class="filter-chip">Tokens</div>
                <div class="filter-chip">Contracts</div>
            </div>
        </div>
    </section>

    <!-- Network Statistics -->
    <div class="stats-grid">
        <div class="stat-card fade-in">
            <div class="stat-header">
                <span class="stat-title">Latest Block</span>
                <div class="stat-icon" style="background: linear-gradient(135deg, #1e40af, #3730a3);">🧱</div>
            </div>
            <div class="stat-value" id="latest-block"><?php echo number_format($networkStats['latest_block']); ?></div>
            <div class="stat-change positive">
                <span>↑</span> <span>+1 every ~2s</span>
            </div>
        </div>

        <div class="stat-card fade-in">
            <div class="stat-header">
                <span class="stat-title">Network TPS</span>
                <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">⚡</div>
            </div>
            <div class="stat-value" id="tps"><?php echo number_format($networkStats['tps'], 2); ?></div>
            <div class="stat-change positive">
                <span>↑</span> <span>+12.5%</span>
            </div>
        </div>

        <div class="stat-card fade-in">
            <div class="stat-header">
                <span class="stat-title">Avg Block Time</span>
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">⏱️</span>
            </div>
            <div class="stat-value" id="block-time"><?php echo $networkStats['avg_block_time']; ?>s</div>
            <div class="stat-change positive">
                <span>↓</span> <span>-0.1s</span>
            </div>
        </div>

        <div class="stat-card fade-in">
            <div class="stat-header">
                <span class="stat-title">Total Transactions</span>
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">📊</div>
            </div>
            <div class="stat-value" id="total-tx"><?php echo number_format($networkStats['total_transactions']); ?></div>
            <div class="stat-change positive">
                <span>↑</span> <span>+<?php echo number_format($networkStats['tx_24h']); ?> (24h)</span>
            </div>
        </div>

        <div class="stat-card fade-in">
            <div class="stat-header">
                <span class="stat-title">Active Validators</span>
                <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">👥</div>
            </div>
            <div class="stat-value" id="validators"><?php echo $networkStats['validators']; ?></div>
            <div class="stat-change positive">
                <span>↑</span> <span>+2 this week</span>
            </div>
        </div>

        <div class="stat-card fade-in">
            <div class="stat-header">
                <span class="stat-title">Network Load</span>
                <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">📈</div>
            </div>
            <div class="stat-value" id="network-load"><?php echo $networkStats['network_load']; ?>%</div>
            <div class="stat-change negative">
                <span>↑</span> <span>+5.2%</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container">
        <?php
        // Include page content based on URL
        switch ($page) {
            case 'blocks':
                include 'pages/blocks.php';
                break;
            case 'transactions':
                include 'pages/transactions.php';
                break;
            case 'address':
                include 'pages/address.php';
                break;
            case 'block':
                include 'pages/block.php';
                break;
            case 'transaction':
                include 'pages/transaction.php';
                break;
            case 'wallet':
                include 'pages/wallet.php';
                break;
            case 'contracts':
                include 'pages/contracts.php';
                break;
            case 'tokens':
                include 'pages/tokens.php';
                break;
            case 'validators':
                include 'pages/validators.php';
                break;
            case 'analytics':
                include 'pages/analytics.php';
                break;
            case 'search':
                include 'pages/search.php';
                break;
            default:
                include 'pages/home.php';
        }
        ?>
    </main>

    <!-- Professional Footer -->
    <footer style="background: var(--secondary-dark); border-top: 1px solid var(--border-color); padding: 3rem 2rem; text-align: center; color: var(--text-secondary);">
        <div style="max-width: 1400px; margin: 0 auto;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                <div>
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Diora Network</h4>
                    <p style="font-size: 0.875rem; line-height: 1.6;">Professional blockchain explorer providing real-time network monitoring and analytics for the Diora ecosystem.</p>
                </div>
                <div>
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">API Access</h4>
                    <p style="font-size: 0.875rem; line-height: 1.6;">Full API compatibility with Etherscan for seamless integration with existing tools and applications.</p>
                </div>
                <div>
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Security</h4>
                    <p style="font-size: 0.875rem; line-height: 1.6;">Enterprise-grade security with rate limiting, audit logs, and comprehensive monitoring systems.</p>
                </div>
            </div>
            <div style="border-top: 1px solid var(--border-color); padding-top: 2rem; font-size: 0.875rem;">
                <p>&copy; 2026 Diora Blockchain Explorer. Professional network monitoring platform.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Real-time updates
        setInterval(() => {
            updateNetworkStats();
        }, 5000);

        function updateNetworkStats() {
            fetch('api.php?action=stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('latest-block').textContent = parseInt(data.latest_block).toLocaleString();
                    document.getElementById('tps').textContent = parseFloat(data.tps).toFixed(2);
                    document.getElementById('block-time').textContent = data.avg_block_time + 's';
                    document.getElementById('total-tx').textContent = parseInt(data.total_transactions).toLocaleString();
                    document.getElementById('validators').textContent = data.validators;
                    document.getElementById('network-load').textContent = data.network_load + '%';
                })
                .catch(error => console.error('Error updating stats:', error));
        }

        // Search functionality
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const value = e.target.value;
            if (value.length > 2) {
                // Implement search suggestions
            }
        });

        // Filter chips
        document.querySelectorAll('.filter-chip').forEach(chip => {
            chip.addEventListener('click', function() {
                document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
