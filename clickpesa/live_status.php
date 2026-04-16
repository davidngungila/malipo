<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$activePayments = [];
$recentPayments = [];
$error = '';

try {
    // Get recent payments for live monitoring
    $response = $api->queryAllPayments(['limit' => 20, 'orderBy' => 'DESC']);
    
    if (isset($response['data'])) {
        $allPayments = $response['data'];
        
        // Separate active/processing payments
        $activePayments = array_filter($allPayments, function($p) {
            return in_array($p['status'], ['PROCESSING', 'PENDING']);
        });
        
        $recentPayments = array_slice($allPayments, 0, 10);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Payment Status - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .live-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 30px auto;
            max-width: 1200px;
            padding: 40px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 40px;
        }
        .header-section h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .live-indicator {
            width: 12px;
            height: 12px;
            background: #28a745;
            border-radius: 50%;
            margin-left: 15px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .status-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        .payment-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
        }
        .payment-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
        .payment-item.processing {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .payment-item.pending {
            border-left-color: #17a2b8;
            background: #d1ecf1;
        }
        .payment-item.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .payment-item.failed {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .status-badge {
            font-size: 11px;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-processing {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-pending {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .refresh-btn {
            background: linear-gradient(45deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .auto-refresh {
            background: linear-gradient(45deg, #17a2b8 0%, #138496 100%);
        }
        .last-updated {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }
        .nav-link {
            color: #666;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #667eea;
        }
        .nav-link.active {
            color: #667eea;
            font-weight: 600;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }
        .stats-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-state i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-credit-card text-primary me-2"></i>ClickPesa
                <span class="badge bg-success ms-2" style="font-size: 10px;">FEEDTAN PAY</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="advanced_dashboard.php">Advanced</a>
                <a class="nav-link active" href="live_status.php">Live Status</a>
                <a class="nav-link" href="initiate_payment.php">Initiate Payment</a>
                <a class="nav-link" href="payment_status.php">Check Status</a>
                <a class="nav-link" href="payment_history.php">History</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="live-container">
            <div class="header-section">
                <h1>
                    <i class="fas fa-satellite-dish text-primary me-3"></i>
                    Live Payment Status
                    <span class="live-indicator"></span>
                </h1>
                <p>Real-time monitoring of payment transactions</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="status-grid">
                <div class="status-card">
                    <div class="text-center">
                        <i class="fas fa-clock text-warning" style="font-size: 2rem; margin-bottom: 15px;"></i>
                        <div class="stats-number"><?php echo count($activePayments); ?></div>
                        <div class="stats-label">Active Payments</div>
                    </div>
                </div>
                <div class="status-card">
                    <div class="text-center">
                        <i class="fas fa-check-circle text-success" style="font-size: 2rem; margin-bottom: 15px;"></i>
                        <div class="stats-number"><?php echo count(array_filter($recentPayments, function($p) { return $p['status'] === 'SUCCESS'; })); ?></div>
                        <div class="stats-label">Successful Today</div>
                    </div>
                </div>
                <div class="status-card">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 2rem; margin-bottom: 15px;"></i>
                        <div class="stats-number"><?php echo count(array_filter($recentPayments, function($p) { return $p['status'] === 'FAILED'; })); ?></div>
                        <div class="stats-label">Failed Today</div>
                    </div>
                </div>
                <div class="status-card">
                    <div class="text-center">
                        <i class="fas fa-money-bill-wave text-primary" style="font-size: 2rem; margin-bottom: 15px;"></i>
                        <div class="stats-number"><?php echo number_format(array_sum(array_column($recentPayments, 'collectedAmount'))); ?></div>
                        <div class="stats-label">Total Volume (TZS)</div>
                    </div>
                </div>
            </div>

            <!-- Active Payments -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3><i class="fas fa-spinner fa-spin text-warning me-2"></i>Active Payments</h3>
                    <button class="btn refresh-btn" onclick="refreshData()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh Now
                    </button>
                </div>

                <?php if (!empty($activePayments)): ?>
                    <?php foreach ($activePayments as $payment): ?>
                        <div class="payment-item <?php echo strtolower($payment['status']); ?>">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-receipt text-muted me-2"></i>
                                        <strong><?php echo htmlspecialchars($payment['orderReference']); ?></strong>
                                        <span class="status-badge status-<?php echo strtolower($payment['status']); ?> ms-2">
                                            <?php echo htmlspecialchars($payment['status']); ?>
                                        </span>
                                    </div>
                                    <div class="text-muted small">
                                        <?php 
                                        $phone = '';
                                        if (isset($payment['paymentPhoneNumber'])) {
                                            $phone = $payment['paymentPhoneNumber'];
                                        } elseif (isset($payment['customer']['customerPhoneNumber'])) {
                                            $phone = $payment['customer']['customerPhoneNumber'];
                                        }
                                        echo $phone ? 'Phone: ' . htmlspecialchars($phone) . ' • ' : '';
                                        echo date('M j, Y H:i:s', strtotime($payment['createdAt'])); 
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="fw-bold"><?php echo number_format($payment['collectedAmount']); ?> TZS</div>
                                    <div class="small text-muted"><?php echo $api->getStatusDescription($payment['status']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h5>No Active Payments</h5>
                        <p>All payments have been completed or no payments are currently processing.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Payments -->
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3><i class="fas fa-history text-primary me-2"></i>Recent Payments</h3>
                    <div>
                        <button class="btn refresh-btn auto-refresh" onclick="toggleAutoRefresh()">
                            <i class="fas fa-play me-2" id="autoRefreshIcon"></i>
                            <span id="autoRefreshText">Start Auto Refresh</span>
                        </button>
                        <button class="btn refresh-btn" onclick="refreshData()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh
                        </button>
                    </div>
                </div>

                <?php if (!empty($recentPayments)): ?>
                    <?php foreach ($recentPayments as $payment): ?>
                        <div class="payment-item <?php echo strtolower($payment['status']); ?>">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-receipt text-muted me-2"></i>
                                        <strong><?php echo htmlspecialchars($payment['orderReference']); ?></strong>
                                        <span class="status-badge status-<?php echo strtolower($payment['status']); ?> ms-2">
                                            <?php echo htmlspecialchars($payment['status']); ?>
                                        </span>
                                    </div>
                                    <div class="text-muted small">
                                        <?php 
                                        $phone = '';
                                        if (isset($payment['paymentPhoneNumber'])) {
                                            $phone = $payment['paymentPhoneNumber'];
                                        } elseif (isset($payment['customer']['customerPhoneNumber'])) {
                                            $phone = $payment['customer']['customerPhoneNumber'];
                                        }
                                        echo $phone ? 'Phone: ' . htmlspecialchars($phone) . ' • ' : '';
                                        echo date('M j, Y H:i:s', strtotime($payment['createdAt'])); 
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="fw-bold"><?php echo number_format($payment['collectedAmount']); ?> TZS</div>
                                    <div class="small text-muted"><?php echo $api->getStatusDescription($payment['status']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No Recent Payments</h5>
                        <p>No payment transactions found in the recent period.</p>
                        <a href="initiate_payment.php" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>Initiate Payment
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="last-updated">
                <i class="fas fa-clock me-2"></i>
                Last updated: <span id="lastUpdated"><?php echo date('Y-m-d H:i:s'); ?></span>
                <span id="autoRefreshStatus" class="ms-2"></span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let autoRefreshInterval = null;
        let isAutoRefreshing = false;

        function refreshData() {
            const refreshBtn = event?.currentTarget;
            if (refreshBtn) {
                refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
                refreshBtn.disabled = true;
            }

            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        function toggleAutoRefresh() {
            const btn = event.currentTarget;
            const icon = document.getElementById('autoRefreshIcon');
            const text = document.getElementById('autoRefreshText');
            const status = document.getElementById('autoRefreshStatus');

            if (isAutoRefreshing) {
                clearInterval(autoRefreshInterval);
                isAutoRefreshing = false;
                icon.className = 'fas fa-play me-2';
                text.textContent = 'Start Auto Refresh';
                status.textContent = '';
                btn.classList.remove('auto-refresh');
            } else {
                autoRefreshInterval = setInterval(() => {
                    location.reload();
                }, 10000); // Refresh every 10 seconds
                isAutoRefreshing = true;
                icon.className = 'fas fa-pause me-2';
                text.textContent = 'Stop Auto Refresh';
                status.textContent = '(Auto-refreshing every 10s)';
                btn.classList.add('auto-refresh');
            }
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Update last updated time
        function updateLastUpdated() {
            document.getElementById('lastUpdated').textContent = new Date().toLocaleString();
        }

        // Update time every second
        setInterval(updateLastUpdated, 1000);
    </script>
</body>
</html>
