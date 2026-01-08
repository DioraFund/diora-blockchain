<?php
// Get network data
$transactions = json_decode(file_get_contents('http://localhost:8080/api/transactions'), true);
$blocks = json_decode(file_get_contents('http://localhost:8080/api/blocks'), true);
$wallets = json_decode(file_get_contents('http://localhost:8080/api/wallets'), true);

// Generate analytics data
$dailyStats = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dailyStats[] = [
        'date' => $date,
        'transactions' => rand(1000, 5000),
        'blocks' => rand(40000, 45000),
        'gas_used' => rand(800000000, 1200000000),
        'active_addresses' => rand(500, 2000),
        'new_addresses' => rand(50, 200)
    ];
}

$hourlyStats = [];
for ($i = 23; $i >= 0; $i--) {
    $hour = date('H:00', strtotime("-$i hours"));
    $hourlyStats[] = [
        'hour' => $hour,
        'tps' => rand(10, 100),
        'gas_price' => rand(15, 35),
        'block_time' => rand(1800, 2400) / 1000
    ];
}

$gasStats = [
    'current_gas_price' => 20,
    'avg_gas_price_24h' => rand(18, 25),
    'gas_limit' => 30000000,
    'gas_used_24h' => rand(2000000000000, 2800000000000),
    'gas_efficiency' => rand(85, 95)
];

$networkStats = [
    'total_blocks' => count($blocks['blocks'] ?? []),
    'total_transactions' => count($transactions['transactions'] ?? []),
    'active_addresses' => count($wallets['wallets'] ?? []),
    'network_hashrate' => '1.2 TH/s',
    'difficulty' => '2.5T',
    'avg_block_time' => 2.1,
    'network_utilization' => rand(15, 85)
];
?>

<div class="analytics-container">
    <!-- Network Overview -->
    <div class="table-container fade-in">
        <div class="table-header">
            <h2 class="table-title">Network Overview</h2>
            <div class="table-actions">
                <select class="btn btn-secondary" onchange="updateTimeRange(this.value)">
                    <option value="24h">Last 24 Hours</option>
                    <option value="7d">Last 7 Days</option>
                    <option value="30d">Last 30 Days</option>
                    <option value="90d">Last 90 Days</option>
                </select>
            </div>
        </div>
        
        <div style="padding: 2rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Total Blocks</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-blue);"><?php echo number_format($networkStats['total_blocks']); ?></div>
                    <div style="color: var(--text-secondary); margin-top: 0.5rem;">Avg time: <?php echo $networkStats['avg_block_time']; ?>s</div>
                </div>
                
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Total Transactions</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-green);"><?php echo number_format($networkStats['total_transactions']); ?></div>
                    <div style="color: var(--text-secondary); margin-top: 0.5rem;">All time</div>
                </div>
                
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Active Addresses</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-cyan);"><?php echo number_format($networkStats['active_addresses']); ?></div>
                    <div style="color: var(--text-secondary); margin-top: 0.5rem;">Unique addresses</div>
                </div>
                
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Network Hashrate</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-yellow);"><?php echo $networkStats['network_hashrate']; ?></div>
                    <div style="color: var(--text-secondary); margin-top: 0.5rem;">Current</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <!-- Transaction Volume Chart -->
        <div class="table-container fade-in">
            <div class="table-header">
                <h2 class="table-title">Transaction Volume (30 Days)</h2>
            </div>
            <div style="padding: 2rem;">
                <canvas id="volumeChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Gas Price Chart -->
        <div class="table-container fade-in">
            <div class="table-header">
                <h2 class="table-title">Gas Price Trends (24 Hours)</h2>
            </div>
            <div style="padding: 2rem;">
                <canvas id="gasChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Network Activity -->
    <div class="table-container fade-in">
        <div class="table-header">
            <h2 class="table-title">Network Activity</h2>
        </div>
        
        <div style="padding: 2rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <!-- TPS Chart -->
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Transactions Per Second</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-green); margin-bottom: 1rem;">
                        <?php echo array_sum(array_column($hourlyStats, 'tps')) / count($hourlyStats); ?>
                    </div>
                    <canvas id="tpsChart" width="300" height="100"></canvas>
                </div>

                <!-- Block Time -->
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Block Time</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-blue); margin-bottom: 1rem;">
                        <?php echo $networkStats['avg_block_time']; ?>s
                    </div>
                    <canvas id="blockTimeChart" width="300" height="100"></canvas>
                </div>

                <!-- Network Utilization -->
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Network Utilization</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-yellow); margin-bottom: 1rem;">
                        <?php echo $networkStats['network_utilization']; ?>%
                    </div>
                    <div style="width: 100%; height: 20px; background: var(--border-color); border-radius: 10px; overflow: hidden; margin-bottom: 1rem;">
                        <div style="width: <?php echo $networkStats['network_utilization']; ?>%; height: 100%; background: linear-gradient(90deg, var(--accent-green), var(--accent-yellow), var(--accent-red));"></div>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">
                        <?php echo $networkStats['network_utilization'] < 50 ? 'Low Load' : ($networkStats['network_utilization'] < 80 ? 'Medium Load' : 'High Load'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gas Statistics -->
    <div class="table-container fade-in">
        <div class="table-header">
            <h2 class="table-title">Gas Statistics</h2>
        </div>
        
        <div style="padding: 2rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Current Gas Price</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-cyan);"><?php echo $gasStats['current_gas_price']; ?> Gwei</div>
                    <div style="color: var(--text-secondary); margin-top: 0.5rem;">Real-time</div>
                </div>
                
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">24h Average</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-blue);"><?php echo $gasStats['avg_gas_price_24h']; ?> Gwei</div>
                    <div style="color: var(--text-secondary); margin-top: 0.5rem;">Past 24 hours</div>
                </div>
                
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Gas Used (24h)</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-green);"><?php echo number_format($gasStats['gas_used_24h'] / 1000000000, 0); ?>B</div>
                    <div style="color: var(--text-secondary); margin-top: 0.5rem;">Total gas consumed</div>
                </div>
                
                <div style="background: var(--secondary-dark); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: var(--text-primary); margin-bottom: 1rem;">Gas Efficiency</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-yellow);"><?php echo $gasStats['gas_efficiency']; ?>%</div>
                    <div style="color: var(--text-secondary); margin-top: 0.5rem;">Optimization score</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Statistics Table -->
    <div class="table-container fade-in">
        <div class="table-header">
            <h2 class="table-title">Daily Statistics (Last 7 Days)</h2>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transactions</th>
                    <th>Blocks</th>
                    <th>Gas Used</th>
                    <th>Active Addresses</th>
                    <th>New Addresses</th>
                    <th>Avg Gas Price</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $recentDailyStats = array_slice($dailyStats, -7);
                foreach (array_reverse($recentDailyStats) as $stat): 
                ?>
                    <tr>
                        <td><?php echo date('M j, Y', strtotime($stat['date'])); ?></td>
                        <td><?php echo number_format($stat['transactions']); ?></td>
                        <td><?php echo number_format($stat['blocks']); ?></td>
                        <td><?php echo number_format($stat['gas_used'] / 1000000000, 2); ?>B</td>
                        <td><?php echo number_format($stat['active_addresses']); ?></td>
                        <td><?php echo number_format($stat['new_addresses']); ?></td>
                        <td><?php echo rand(15, 25); ?> Gwei</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Chart drawing functions
function drawVolumeChart() {
    const canvas = document.getElementById('volumeChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // Sample data
    const data = <?php echo json_encode(array_column($dailyStats, 'transactions')); ?>;
    const labels = <?php echo json_encode(array_column($dailyStats, 'date')); ?>;
    
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

function drawGasChart() {
    const canvas = document.getElementById('gasChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    const data = <?php echo json_encode(array_column($hourlyStats, 'gas_price')); ?>;
    
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
    ctx.strokeStyle = '#f59e0b';
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
}

function drawTPSChart() {
    const canvas = document.getElementById('tpsChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    const data = <?php echo json_encode(array_column($hourlyStats, 'tps')); ?>;
    
    ctx.clearRect(0, 0, width, height);
    
    ctx.strokeStyle = '#10b981';
    ctx.lineWidth = 2;
    ctx.beginPath();
    
    const stepX = width / (data.length - 1);
    const maxValue = Math.max(...data);
    
    data.forEach((value, index) => {
        const x = index * stepX;
        const y = height - (value / maxValue) * height * 0.8 - 5;
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
}

function drawBlockTimeChart() {
    const canvas = document.getElementById('blockTimeChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    const data = <?php echo json_encode(array_column($hourlyStats, 'block_time')); ?>;
    
    ctx.clearRect(0, 0, width, height);
    
    ctx.strokeStyle = '#1e40af';
    ctx.lineWidth = 2;
    ctx.beginPath();
    
    const stepX = width / (data.length - 1);
    const maxValue = Math.max(...data);
    const minValue = Math.min(...data);
    const range = maxValue - minValue;
    
    data.forEach((value, index) => {
        const x = index * stepX;
        const y = height - ((value - minValue) / range) * height * 0.8 - 5;
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
}

function updateTimeRange(range) {
    // Update charts based on time range
    drawVolumeChart();
    drawGasChart();
    drawTPSChart();
    drawBlockTimeChart();
}

// Initialize charts
setTimeout(() => {
    drawVolumeChart();
    drawGasChart();
    drawTPSChart();
    drawBlockTimeChart();
}, 100);

// Auto-refresh
setInterval(() => {
    location.reload();
}, 60000); // Refresh every minute
</script>

<style>
.analytics-container {
    max-width: 1400px;
    margin: 0 auto;
}

canvas {
    width: 100%;
    height: auto;
}
</style>
