<?php
$page = max(1, intval($_GET['page'] ?? 1));
$limit = min(100, max(10, intval($_GET['limit'] ?? 50)));

// Sample contracts data (in production, this would come from blockchain)
$contracts = [
    [
        'address' => '0x1234567890123456789012345678901234567890',
        'name' => 'Diora Token',
        'symbol' => 'DIO',
        'creator' => '0xabcdefabcdefabcdefabcdefabcdefabcdefabcd',
        'tx_count' => 15420,
        'verified' => true,
        'created_at' => '2026-01-01 12:00:00'
    ],
    [
        'address' => '0x2345678901234567890123456789012345678901',
        'name' => 'Uniswap V2 Router',
        'symbol' => 'UNI-V2',
        'creator' => '0xbcdefabcdefabcdefabcdefabcdefabcdefabcde',
        'tx_count' => 8934,
        'verified' => true,
        'created_at' => '2026-01-02 15:30:00'
    ],
    [
        'address' => '0x3456789012345678901234567890123456789012',
        'name' => 'Multi-Sig Wallet',
        'symbol' => 'MSIG',
        'creator' => '0xcdefabcdefabcdefabcdefabcdefabcdefabcdef',
        'tx_count' => 3421,
        'verified' => false,
        'created_at' => '2026-01-03 09:15:00'
    ]
];

// Pagination
$total = count($contracts);
$pages = ceil($total / $limit);
$offset = ($page - 1) * $limit;
$pageContracts = array_slice($contracts, $offset, $limit);
?>

<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Smart Contracts</h2>
        <div class="table-actions">
            <a href="#" class="btn btn-primary">Verify Contract</a>
            <select class="btn btn-secondary" onchange="changeLimit(this.value)">
                <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10 per page</option>
                <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25 per page</option>
                <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 per page</option>
            </select>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Address</th>
                <th>Name</th>
                <th>Symbol</th>
                <th>Creator</th>
                <th>Txs</th>
                <th>Verified</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pageContracts)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No contracts found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pageContracts as $contract): ?>
                    <tr>
                        <td>
                            <a href="index.php?page=address&address=<?php echo $contract['address']; ?>" class="address-link">
                                <?php echo substr($contract['address'], 0, 6); ?>...<?php echo substr($contract['address'], -4); ?>
                            </a>
                        </td>
                        <td>
                            <a href="#" style="color: var(--text-primary); text-decoration: none; font-weight: 600;">
                                <?php echo $contract['name']; ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-info"><?php echo $contract['symbol']; ?></span>
                        </td>
                        <td>
                            <a href="index.php?page=address&address=<?php echo $contract['creator']; ?>" class="address-link">
                                <?php echo substr($contract['creator'], 0, 6); ?>...<?php echo substr($contract['creator'], -4); ?>
                            </a>
                        </td>
                        <td><?php echo number_format($contract['tx_count']); ?></td>
                        <td>
                            <?php if ($contract['verified']): ?>
                                <span class="badge badge-success">Verified</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Not Verified</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($contract['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($pages > 1): ?>
        <div style="padding: 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="color: var(--text-secondary);">
                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total); ?> of <?php echo $total; ?> contracts
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <?php if ($page > 1): ?>
                    <a href="index.php?page=contracts&page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-secondary">← Previous</a>
                <?php endif; ?>
                
                <?php
                $start = max(1, $page - 2);
                $end = min($pages, $page + 2);
                
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="index.php?page=contracts&page=<?php echo $i; ?>&limit=<?php echo $limit; ?>" 
                       class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $pages): ?>
                    <a href="index.php?page=contracts&page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Contract Verification -->
<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Contract Verification</h2>
    </div>
    
    <div style="padding: 2rem;">
        <form style="display: grid; gap: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Contract Address</label>
                <input type="text" placeholder="0x..." style="width: 100%; padding: 1rem; background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary);">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Compiler Type</label>
                <select style="width: 100%; padding: 1rem; background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary);">
                    <option>Solidity (Single File)</option>
                    <option>Solidity (Multi-Part Files)</option>
                    <option>Vyper</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Compiler Version</label>
                <select style="width: 100%; padding: 1rem; background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary);">
                    <option>v0.8.26+commit.8999732</option>
                    <option>v0.8.25+commit.b61c2a91</option>
                    <option>v0.8.24+commit.e11b9ed9</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">License</label>
                <select style="width: 100%; padding: 1rem; background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary);">
                    <option>MIT License (MIT)</option>
                    <option>GNU General Public License (GPL)</option>
                    <option>Apache License 2.0</option>
                    <option>No License (None)</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);">Source Code</label>
                <textarea placeholder="pragma solidity ^0.8.0;..." style="width: 100%; min-height: 200px; padding: 1rem; background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-family: 'Monaco', 'Menlo', monospace; resize: vertical;"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Verify and Publish</button>
        </form>
    </div>
</div>

<!-- Contract Stats -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Contracts</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #1e40af, #3730a3);">SC</div>
        </div>
        <div class="stat-value"><?php echo number_format($total); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo rand(5, 20); ?> (24h)</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Verified</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">✓</div>
        </div>
        <div class="stat-value"><?php echo count(array_filter($contracts, fn($c) => $c['verified'])); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo rand(1, 5); ?> (24h)</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Avg Contract Size</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">KB</div>
        </div>
        <div class="stat-value"><?php echo rand(15, 45); ?> KB</div>
        <div class="stat-change negative">
            <span>↑</span> <span>+<?php echo rand(1, 5); ?>% from 24h</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Gas Optimization</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">⚡</div>
        </div>
        <div class="stat-value"><?php echo rand(70, 95); ?>%</div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo rand(1, 3); ?>% from 24h</span>
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
</script>
