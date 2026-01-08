<?php
$page = max(1, intval($_GET['page'] ?? 1));
$limit = min(100, max(10, intval($_GET['limit'] ?? 50)));

// Sample tokens data
$tokens = [
    [
        'address' => '0x0000000000000000000000000000000000000000',
        'name' => 'Diora',
        'symbol' => 'DIO',
        'decimals' => 18,
        'total_supply' => '1000000000000000000000000000',
        'holders' => 15420,
        'price' => '0.00',
        'market_cap' => '0',
        'volume_24h' => '0',
        'change_24h' => '0.00',
        'logo' => null
    ],
    [
        'address' => '0x1234567890123456789012345678901234567890',
        'name' => 'USD Coin',
        'symbol' => 'USDC',
        'decimals' => 6,
        'total_supply' => '500000000000000',
        'holders' => 8934,
        'price' => '1.00',
        'market_cap' => '500000000',
        'volume_24h' => '25000000',
        'change_24h' => '0.01',
        'logo' => null
    ],
    [
        'address' => '0x2345678901234567890123456789012345678901',
        'name' => 'Wrapped Ether',
        'symbol' => 'WETH',
        'decimals' => 18,
        'total_supply' => '100000000000000000000000',
        'holders' => 5678,
        'price' => '2234.50',
        'market_cap' => '223450000000',
        'volume_24h' => '125000000',
        'change_24h' => '2.34',
        'logo' => null
    ]
];

// Pagination
$total = count($tokens);
$pages = ceil($total / $limit);
$offset = ($page - 1) * $limit;
$pageTokens = array_slice($tokens, $offset, $limit);
?>

<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Tokens</h2>
        <div class="table-actions">
            <div style="display: flex; gap: 1rem; align-items: center;">
                <input type="text" placeholder="Search tokens..." style="padding: 0.5rem 1rem; background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary);">
                <select class="btn btn-secondary">
                    <option>All Tokens</option>
                    <option>ERC-20</option>
                    <option>ERC-721</option>
                    <option>ERC-1155</option>
                </select>
                <select class="btn btn-secondary" onchange="changeLimit(this.value)">
                    <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10 per page</option>
                    <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25 per page</option>
                    <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 per page</option>
                </select>
            </div>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Market Cap</th>
                <th>Volume (24h)</th>
                <th>Change (24h)</th>
                <th>Holders</th>
                <th>Total Supply</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pageTokens)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No tokens found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pageTokens as $token): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-blue), var(--accent-cyan)); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.875rem;">
                                    <?php echo substr($token['symbol'], 0, 2); ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-primary);">
                                        <?php echo $token['name']; ?>
                                    </div>
                                    <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                        <a href="index.php?page=address&address=<?php echo $token['address']; ?>" class="address-link">
                                            <?php echo substr($token['address'], 0, 6); ?>...<?php echo substr($token['address'], -4); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600;">$<?php echo number_format($token['price'], 2); ?></div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);"><?php echo $token['symbol']; ?></div>
                        </td>
                        <td>$<?php echo number_format($token['market_cap']); ?></td>
                        <td>$<?php echo number_format($token['volume_24h']); ?></td>
                        <td>
                            <span class="stat-change <?php echo $token['change_24h'] >= 0 ? 'positive' : 'negative'; ?>">
                                <?php echo $token['change_24h'] >= 0 ? '+' : ''; ?><?php echo $token['change_24h']; ?>%
                            </span>
                        </td>
                        <td><?php echo number_format($token['holders']); ?></td>
                        <td>
                            <?php echo number_format($token['total_supply'] / pow(10, $token['decimals']), 0); ?> <?php echo $token['symbol']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($pages > 1): ?>
        <div style="padding: 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="color: var(--text-secondary);">
                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total); ?> of <?php echo $total; ?> tokens
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <?php if ($page > 1): ?>
                    <a href="index.php?page=tokens&page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-secondary">← Previous</a>
                <?php endif; ?>
                
                <?php
                $start = max(1, $page - 2);
                $end = min($pages, $page + 2);
                
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="index.php?page=tokens&page=<?php echo $i; ?>&limit=<?php echo $limit; ?>" 
                       class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $pages): ?>
                    <a href="index.php?page=tokens&page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Token Categories -->
<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Token Categories</h2>
    </div>
    
    <div style="padding: 2rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                <h4 style="color: var(--text-primary); margin-bottom: 1rem;">DeFi Tokens</h4>
                <div style="color: var(--text-secondary); margin-bottom: 1rem;">Decentralized finance protocol tokens</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="badge badge-info">127 tokens</span>
                    <span style="color: var(--accent-green);">$2.3B market cap</span>
                </div>
            </div>
            
            <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Stablecoins</h4>
                <div style="color: var(--text-secondary); margin-bottom: 1rem;">Price-stable digital assets</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="badge badge-info">8 tokens</span>
                    <span style="color: var(--accent-green);">$850M market cap</span>
                </div>
            </div>
            
            <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Gaming</h4>
                <div style="color: var(--text-secondary); margin-bottom: 1rem;">Gaming and metaverse tokens</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="badge badge-info">45 tokens</span>
                    <span style="color: var(--accent-green);">$120M market cap</span>
                </div>
            </div>
            
            <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                <h4 style="color: var(--text-primary); margin-bottom: 1rem;">NFTs</h4>
                <div style="color: var(--text-secondary); margin-bottom: 1rem;">Non-fungible tokens</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="badge badge-info">234 tokens</span>
                    <span style="color: var(--accent-green);">$45M market cap</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Token Stats -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Tokens</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #1e40af, #3730a3);">🪙</div>
        </div>
        <div class="stat-value"><?php echo number_format($total + 414); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo rand(5, 25); ?> (24h)</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Market Cap</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">$</div>
        </div>
        <div class="stat-value">$<?php echo number_format(rand(3000000000, 5000000000), 0); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo rand(2, 8); ?>% (24h)</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">24h Volume</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">📊</div>
        </div>
        <div class="stat-value">$<?php echo number_format(rand(500000000, 1500000000), 0); ?></div>
        <div class="stat-change negative">
            <span>↓</span> <span>-<?php echo rand(1, 5); ?>% (24h)</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Active Holders</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">👥</div>
        </div>
        <div class="stat-value"><?php echo number_format(rand(50000, 150000)); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo rand(500, 2000); ?> (24h)</span>
        </div>
    </div>
</div>

<script>
function changeLimit(limit) {
    const url = new URL(window.location);
    url.searchParams.set('limit', limit);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}

// Search functionality
document.querySelector('input[placeholder="Search tokens..."]').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    if (query.length > 2) {
        // Implement search
        console.log('Searching for:', query);
    }
});
</script>
