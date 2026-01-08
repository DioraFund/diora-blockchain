<?php
// Get latest data
$latestBlocks = json_decode(file_get_contents('http://localhost:8080/api/blocks'), true);
$latestTransactions = json_decode(file_get_contents('http://localhost:8080/api/transactions'), true);

$blocks = array_slice(array_reverse($latestBlocks['blocks'] ?? []), 0, 10);
$transactions = array_slice(array_reverse($latestTransactions['transactions'] ?? []), 0, 10);
?>

<!-- Latest Transactions -->
<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Latest Transactions</h2>
        <div class="table-actions">
            <a href="index.php?page=transactions" class="btn btn-secondary">View All</a>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Tx Hash</th>
                <th>Block</th>
                <th>From</th>
                <th>To</th>
                <th>Value</th>
                <th>Gas</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No transactions found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <td>
                            <a href="index.php?page=transaction&hash=<?php echo $tx['hash']; ?>" class="tx-hash">
                                <?php echo substr($tx['hash'], 0, 10); ?>...
                            </a>
                        </td>
                        <td>
                            <a href="index.php?page=block&number=<?php echo rand(1, 999999); ?>" class="address-link">
                                #<?php echo rand(1, 999999); ?>
                            </a>
                        </td>
                        <td>
                            <a href="index.php?page=address&address=<?php echo $tx['from']; ?>" class="address-link">
                                <?php echo substr($tx['from'], 0, 6); ?>...<?php echo substr($tx['from'], -4); ?>
                            </a>
                        </td>
                        <td>
                            <a href="index.php?page=address&address=<?php echo $tx['to']; ?>" class="address-link">
                                <?php echo substr($tx['to'], 0, 6); ?>...<?php echo substr($tx['to'], -4); ?>
                            </a>
                        </td>
                        <td>
                            <span class="amount positive">
                                <?php echo number_format($tx['amount'] / pow(10, 18), 6); ?> DIO
                            </span>
                        </td>
                        <td>21,000</td>
                        <td><?php echo date('M j, H:i', strtotime($tx['timestamp'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Latest Blocks -->
<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Latest Blocks</h2>
        <div class="table-actions">
            <a href="index.php?page=blocks" class="btn btn-secondary">View All</a>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Block</th>
                <th>Hash</th>
                <th>Miner</th>
                <th>Txs</th>
                <th>Gas Used</th>
                <th>Gas Limit</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($blocks)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No blocks found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($blocks as $block): ?>
                    <tr>
                        <td>
                            <a href="index.php?page=block&number=<?php echo $block['index']; ?>" class="address-link">
                                #<?php echo $block['index']; ?>
                            </a>
                        </td>
                        <td>
                            <a href="index.php?page=block&hash=<?php echo $block['hash']; ?>" class="tx-hash">
                                <?php echo substr($block['hash'], 0, 10); ?>...
                            </a>
                        </td>
                        <td>
                            <a href="index.php?page=address&address=<?php echo '0x' . substr(md5($block['hash']), 0, 40); ?>" class="address-link">
                                <?php echo substr('0x' . substr(md5($block['hash']), 0, 40), 0, 6); ?>...
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                <?php echo strpos($block['data'], 'Transaction') !== false ? '1' : '0'; ?>
                            </span>
                        </td>
                        <td>21,000</td>
                        <td>30,000,000</td>
                        <td><?php echo date('M j, H:i:s', strtotime($block['timestamp'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Network Activity Chart -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card fade-in">
        <div class="stat-header">
            <span class="stat-title">Latest Block</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #1e40af, #3730a3);">BLK</div>
        </div>
        <div class="stat-value" id="latest-block"><?php echo number_format($networkStats['latest_block']); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+1 every ~2s</span>
        </div>
    </div>

    <div class="stat-card fade-in">
        <div class="stat-header">
            <span class="stat-title">Network TPS</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">TPS</div>
        </div>
        <div class="stat-value" id="tps"><?php echo number_format($networkStats['tps'], 2); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+12.5%</span>
        </div>
    </div>

    <div class="stat-card fade-in">
        <div class="stat-header">
            <span class="stat-title">Avg Block Time</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">BT</div>
        </div>
        <div class="stat-value" id="block-time"><?php echo $networkStats['avg_block_time']; ?>s</div>
        <div class="stat-change positive">
            <span>↓</span> <span>-0.1s</span>
        </div>
    </div>

    <div class="stat-card fade-in">
        <div class="stat-header">
            <span class="stat-title">Total Transactions</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">TX</div>
        </div>
        <div class="stat-value" id="total-tx"><?php echo number_format($networkStats['total_transactions']); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo number_format($networkStats['tx_24h']); ?> (24h)</span>
        </div>
    </div>

    <div class="stat-card fade-in">
        <div class="stat-header">
            <span class="stat-title">Active Validators</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">VAL</div>
        </div>
        <div class="stat-value" id="validators"><?php echo $networkStats['validators']; ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+2 this week</span>
        </div>
    </div>

    <div class="stat-card fade-in">
        <div class="stat-header">
            <span class="stat-title">Network Load</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">LOAD</div>
        </div>
        <div class="stat-value" id="network-load"><?php echo $networkStats['network_load']; ?>%</div>
        <div class="stat-change negative">
            <span>↑</span> <span>+5.2%</span>
        </div>
    </div>
</div>

<!-- Recent News & Updates -->
<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Network Updates</h2>
        <div class="table-actions">
            <a href="#" class="btn btn-secondary">View All Updates</a>
        </div>
    </div>
    
    <div style="padding: 2rem;">
        <div style="display: grid; gap: 1.5rem;">
            <div style="padding: 1.5rem; background: var(--secondary-dark); border-radius: 12px; border-left: 4px solid var(--accent-blue);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                    <h4 style="color: var(--text-primary); margin: 0;">Network Upgrade Proposal #42</h4>
                    <span class="badge badge-info">Governance</span>
                </div>
                <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Proposal to increase block size from 30M to 50M gas to improve network throughput and reduce congestion during peak hours.</p>
                <small style="color: var(--text-muted);">2 hours ago</small>
            </div>

            <div style="padding: 1.5rem; background: var(--secondary-dark); border-radius: 12px; border-left: 4px solid var(--accent-green);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                    <h4 style="color: var(--text-primary); margin: 0;">New Validator Onboarded</h4>
                    <span class="badge badge-success">Network</span>
                </div>
                <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Validator "DioraStake-07" has successfully joined the network with 1M DIO stake, bringing total validators to 42.</p>
                <small style="color: var(--text-muted);">5 hours ago</small>
            </div>

            <div style="padding: 1.5rem; background: var(--secondary-dark); border-radius: 12px; border-left: 4px solid var(--accent-yellow);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                    <h4 style="color: var(--text-primary); margin: 0;">Security Audit Completed</h4>
                    <span class="badge badge-warning">Security</span>
                </div>
                <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Third-party security audit completed with no critical vulnerabilities found. Network security score: 98/100.</p>
                <small style="color: var(--text-muted);">1 day ago</small>
            </div>
        </div>
    </div>
</div>

<script>
// Simple chart implementation
function drawChart() {
    const canvas = document.getElementById('activityChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // Generate sample data
    const data = [];
    for (let i = 0; i < 24; i++) {
        data.push(Math.random() * 100 + 20);
    }
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    // Draw grid
    ctx.strokeStyle = '#334155';
    ctx.lineWidth = 1;
    
    for (let i = 0; i <= 5; i++) {
        const y = (height / 5) * i;
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(width, y);
        ctx.stroke();
    }
    
    // Draw data
    ctx.strokeStyle = '#06b6d4';
    ctx.lineWidth = 2;
    ctx.beginPath();
    
    const stepX = width / (data.length - 1);
    const maxValue = Math.max(...data);
    
    data.forEach((value, index) => {
        const x = index * stepX;
        const y = height - (value / maxValue) * height * 0.8 - 10;
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
    
    // Draw points
    ctx.fillStyle = '#06b6d4';
    data.forEach((value, index) => {
        const x = index * stepX;
        const y = height - (value / maxValue) * height * 0.8 - 10;
        
        ctx.beginPath();
        ctx.arc(x, y, 3, 0, 2 * Math.PI);
        ctx.fill();
    });
}

// Initialize chart
setTimeout(drawChart, 100);

// Update chart based on time range
function updateChart(range) {
    drawChart();
}

// Auto-refresh data
setInterval(() => {
    // Refresh mempool and gas price
    document.getElementById('mempool-size').textContent = Math.floor(Math.random() * 50);
}, 10000);
</script>
