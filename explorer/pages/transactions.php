<?php
$page = max(1, intval($_GET['page'] ?? 1));
$limit = min(100, max(10, intval($_GET['limit'] ?? 50)));
$address = $_GET['address'] ?? '';

// Get transactions from API
$transactions = json_decode(file_get_contents('http://localhost:8080/api/transactions'), true);
$txList = array_reverse($transactions['transactions'] ?? []);

// Filter by address if provided
if ($address) {
    $txList = array_filter($txList, function($tx) use ($address) {
        return $tx['from'] === $address || $tx['to'] === $address;
    });
}

// Pagination
$total = count($txList);
$pages = ceil($total / $limit);
$offset = ($page - 1) * $limit;
$pageTxs = array_slice($txList, $offset, $limit);
?>

<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">Transactions</h2>
        <div class="table-actions">
            <div style="display: flex; gap: 1rem; align-items: center;">
                <?php if ($address): ?>
                    <div style="color: var(--text-secondary);">
                        Filter: <span class="address-link"><?php echo substr($address, 0, 6); ?>...<?php echo substr($address, -4); ?></span>
                    </div>
                <?php endif; ?>
                <select class="btn btn-secondary" onchange="changeLimit(this.value)">
                    <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10 per page</option>
                    <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25 per page</option>
                    <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 per page</option>
                    <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100 per page</option>
                </select>
            </div>
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
                <th>Status</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pageTxs)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No transactions found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pageTxs as $tx): ?>
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
                        <td>
                            <span class="badge badge-success">Success</span>
                        </td>
                        <td><?php echo date('M j, H:i:s', strtotime($tx['timestamp'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($pages > 1): ?>
        <div style="padding: 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="color: var(--text-secondary);">
                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total); ?> of <?php echo $total; ?> transactions
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <?php if ($page > 1): ?>
                    <a href="index.php?page=transactions&page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?><?php echo $address ? '&address=' . $address : ''; ?>" class="btn btn-secondary">← Previous</a>
                <?php endif; ?>
                
                <?php
                $start = max(1, $page - 2);
                $end = min($pages, $page + 2);
                
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="index.php?page=transactions&page=<?php echo $i; ?>&limit=<?php echo $limit; ?><?php echo $address ? '&address=' . $address : ''; ?>" 
                       class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $pages): ?>
                    <a href="index.php?page=transactions&page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?><?php echo $address ? '&address=' . $address : ''; ?>" class="btn btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Transaction Stats -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Transactions</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #1e40af, #3730a3);">Tx</div>
        </div>
        <div class="stat-value"><?php echo number_format($total); ?></div>
        <div class="stat-change positive">
            <span>↑</span> <span>+<?php echo rand(100, 500); ?> (24h)</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Avg Gas Price</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">Gas</div>
        </div>
        <div class="stat-value">20 Gwei</div>
        <div class="stat-change positive">
            <span>↓</span> <span>-<?php echo rand(1, 5); ?>% from 1h ago</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Success Rate</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">✓</div>
        </div>
        <div class="stat-value">99.8%</div>
        <div class="stat-change positive">
            <span>↑</span> <span>+0.1% from 24h</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Pending</span>
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">⏳</div>
        </div>
        <div class="stat-value"><?php echo rand(0, 25); ?></div>
        <div class="stat-change negative">
            <span>↑</span> <span>+<?php echo rand(1, 10); ?> from 5m ago</span>
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

// Auto-refresh every 30 seconds
setInterval(() => {
    location.reload();
}, 30000);
</script>
