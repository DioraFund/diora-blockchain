<?php
$page = max(1, intval($_GET['page'] ?? 1));
$limit = min(100, max(10, intval($_GET['limit'] ?? 50)));

// Get validators from API
$wallets = json_decode(file_get_contents('http://localhost:8080/api/wallets'), true);
$walletList = $wallets['wallets'] ?? [];

// Create validators from wallets
$validators = array_map(function($wallet, $index) {
    $status = ['active', 'active', 'active', 'pending', 'slashing'][rand(0, 4)];
    $staked = rand(1000000, 10000000) * pow(10, 18);
    
    return [
        'address' => $wallet['address'],
        'name' => "Validator #" . ($index + 1),
        'status' => $status,
        'commission' => (rand(1, 10) / 10) . '%',
        'staked_amount' => $staked,
        'reward_rate' => (rand(5, 15) / 10) . '%',
        'uptime' => (rand(950, 1000) / 10) . '%',
        'last_active' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' minutes')),
        'total_rewards' => $staked * 0.08 * 0.5, // ~8% APR for 6 months
        'performance_score' => rand(75, 100),
        'delegators' => rand(10, 500)
    ];
}, $walletList, array_keys($walletList));

// Sort by staked amount
usort($validators, function($a, $b) {
    return $b['staked_amount'] - $a['staked_amount'];
});

// Pagination
$total = count($validators);
$pages = ceil($total / $limit);
$offset = ($page - 1) * $limit;
$pageValidators = array_slice($validators, $offset, $limit);
?>

<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Validators</h2>
        <div class="table-actions">
            <div style="display: flex; gap: 1rem; align-items: center;">
                <select class="btn btn-secondary">
                    <option>All Validators</option>
                    <option>Active Only</option>
                    <option>Pending</option>
                    <option>Slashing</option>
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
                <th>Validator</th>
                <th>Status</th>
                <th>Staked</th>
                <th>Commission</th>
                <th>APR</th>
                <th>Uptime</th>
                <th>Delegators</th>
                <th>Performance</th>
                <th>Last Active</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pageValidators)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No validators found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pageValidators as $validator): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-blue), var(--accent-cyan)); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.875rem;">
                                    V
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-primary);">
                                        <?php echo $validator['name']; ?>
                                    </div>
                                    <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                        <a href="index.php?page=address&address=<?php echo $validator['address']; ?>" class="address-link">
                                            <?php echo substr($validator['address'], 0, 6); ?>...<?php echo substr($validator['address'], -4); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($validator['status'] === 'active'): ?>
                                <span class="badge badge-success">Active</span>
                            <?php elseif ($validator['status'] === 'pending'): ?>
                                <span class="badge badge-warning">Pending</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Slashing</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight: 600;"><?php echo number_format($validator['staked_amount'] / pow(10, 18), 0); ?> DIO</div>
                            <div style="font-size: 0.875rem; color: var(--text-secondary);">$<?php echo number_format($validator['staked_amount'] / pow(10, 18) * 0.01, 0); ?></div>
                        </td>
                        <td><?php echo $validator['commission']; ?></td>
                        <td>
                            <div style="font-weight: 600; color: var(--accent-green);"><?php echo $validator['reward_rate']; ?></div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 40px; height: 4px; background: var(--border-color); border-radius: 2px; overflow: hidden;">
                                    <div style="width: <?php echo $validator['uptime']; ?>%; height: 100%; background: var(--accent-green);"></div>
                                </div>
                                <span style="font-size: 0.875rem;"><?php echo $validator['uptime']; ?></span>
                            </div>
                        </td>
                        <td><?php echo $validator['delegators']; ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 40px; height: 4px; background: var(--border-color); border-radius: 2px; overflow: hidden;">
                                    <div style="width: <?php echo $validator['performance_score']; ?>%; height: 100%; background: <?php echo $validator['performance_score'] >= 90 ? 'var(--accent-green)' : ($validator['performance_score'] >= 75 ? 'var(--accent-yellow)' : 'var(--accent-red)'); ?>;"></div>
                                </div>
                                <span style="font-size: 0.875rem;"><?php echo $validator['performance_score']; ?>%</span>
                            </div>
                        </td>
                        <td><?php echo date('M j, H:i', strtotime($validator['last_active'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($pages > 1): ?>
        <div style="padding: 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="color: var(--text-secondary);">
                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total); ?> of <?php echo $total; ?> validators
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <?php if ($page > 1): ?>
                    <a href="index.php?page=validators&page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-secondary">← Previous</a>
                <?php endif; ?>
                
                <?php
                $start = max(1, $page - 2);
                $end = min($pages, $page + 2);
                
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="index.php?page=validators&page=<?php echo $i; ?>&limit=<?php echo $limit; ?>" 
                       class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $pages): ?>
                    <a href="index.php?page=validators&page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Staking Overview -->
<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Staking Overview</h2>
    </div>
    
    <div style="padding: 2rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Total Staked</h4>
                <div style="font-size: 2rem; font-weight: 700; color: var(--accent-green); margin-bottom: 0.5rem;">
                    <?php 
                    $totalStaked = array_sum(array_column($validators, 'staked_amount'));
                    echo number_format($totalStaked / pow(10, 18), 0); 
                    ?> DIO
                </div>
                <div style="color: var(--text-secondary);">$<?php echo number_format($totalStaked / pow(10, 18) * 0.01, 0); ?></div>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: var(--text-secondary);">Stake Ratio</span>
                        <span style="color: var(--text-primary);"><?php echo round($totalStaked / 1000000000000000000000000000 * 100, 1); ?>%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Avg APR</span>
                        <span style="color: var(--accent-green);">8.5%</span>
                    </div>
                </div>
            </div>
            
            <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Validator Performance</h4>
                <div style="display: grid; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Active Validators</span>
                        <span class="badge badge-success"><?php echo count(array_filter($validators, fn($v) => $v['status'] === 'active')); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Pending</span>
                        <span class="badge badge-warning"><?php echo count(array_filter($validators, fn($v) => $v['status'] === 'pending')); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Slashing</span>
                        <span class="badge badge-danger"><?php echo count(array_filter($validators, fn($v) => $v['status'] === 'slashing')); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Avg Uptime</span>
                        <span style="color: var(--accent-green);">99.2%</span>
                    </div>
                </div>
            </div>
            
            <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Delegation Stats</h4>
                <div style="display: grid; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Total Delegators</span>
                        <span style="color: var(--text-primary);"><?php echo number_format(array_sum(array_column($validators, 'delegators'))); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Avg Delegation</span>
                        <span style="color: var(--text-primary);"><?php echo number_format($totalStaked / pow(10, 18) / array_sum(array_column($validators, 'delegators')), 0); ?> DIO</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Rewards Distributed</span>
                        <span style="color: var(--accent-green);"><?php echo number_format(array_sum(array_column($validators, 'total_rewards')) / pow(10, 18), 0); ?> DIO</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-secondary);">Next Epoch</span>
                        <span style="color: var(--text-primary);"><?php echo (32 - (date('H') % 32)); ?>h <?php echo (60 - date('i')); ?>m</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validator Stats -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Validators</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #1e40af, #3730a3);">V</div>
        </div>
        <div class="stat-value"><?php echo count($validators); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo rand(1, 5); ?> this week</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Staked</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">DIO</div>
        </div>
        <div class="stat-value"><?php echo number_format($totalStaked / pow(10, 18), 0); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo rand(5, 15); ?>% (30d)</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Average APR</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">%</div>
        </div>
        <div class="stat-value">8.5%</div>
        <div class="stat-change negative">
            <span>↓</span> <span>-0.2% (30d)</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Network Security</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">🔒</div>
        </div>
        <div class="stat-value">99.9%</div>
        <div class="stat-change positive">
            <span>↑</span> <span>+0.1% (30d)</span>
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

// Auto-refresh validator status
setInterval(() => {
    location.reload();
}, 60000); // Refresh every minute
</script>
