<?php
$page = max(1, intval($_GET['page'] ?? 1));
$limit = min(100, max(10, intval($_GET['limit'] ?? 50)));

// Get blocks from API
$blocks = json_decode(file_get_contents('http://localhost:8080/api/blocks'), true);
$blockList = array_reverse($blocks['blocks'] ?? []);

// Pagination
$total = count($blockList);
$pages = ceil($total / $limit);
$offset = ($page - 1) * $limit;
$pageBlocks = array_slice($blockList, $offset, $limit);
?>

<div class="table-container fade-in">
    <div class="table-header">
        <h2 class="table-title">🧱 Blocks</h2>
        <div class="table-actions">
            <div style="display: flex; gap: 1rem; align-items: center;">
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
                <th>Block</th>
                <th>Hash</th>
                <th>Miner</th>
                <th>Txs</th>
                <th>Gas Used</th>
                <th>Gas Limit</th>
                <th>Reward</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pageBlocks)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No blocks found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pageBlocks as $block): ?>
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
                        <td>2 DIO</td>
                        <td><?php echo date('M j, H:i:s', strtotime($block['timestamp'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($pages > 1): ?>
        <div style="padding: 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="color: var(--text-secondary);">
                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total); ?> of <?php echo $total; ?> blocks
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <?php if ($page > 1): ?>
                    <a href="index.php?page=blocks&page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-secondary">← Previous</a>
                <?php endif; ?>
                
                <?php
                $start = max(1, $page - 2);
                $end = min($pages, $page + 2);
                
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="index.php?page=blocks&page=<?php echo $i; ?>&limit=<?php echo $limit; ?>" 
                       class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $pages): ?>
                    <a href="index.php?page=blocks&page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>" class="btn btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
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
