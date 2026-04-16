<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$recentPayments = [];
$stats = [
    'total_transactions' => 0,
    'successful' => 0,
    'pending' => 0,
    'failed' => 0,
    'total_amount' => 0
];

try {
    // Get recent payments (last 10)
    $response = $api->queryAllPayments(['limit' => 10, 'orderBy' => 'DESC']);
    
    if (isset($response['data'])) {
        $recentPayments = $response['data'];
        
        // Calculate statistics
        $stats['total_transactions'] = $response['totalCount'];
        $stats['successful'] = count(array_filter($recentPayments, function($p) { 
            return $p['status'] === 'SUCCESS' || $p['status'] === 'SETTLED'; 
        }));
        $stats['pending'] = count(array_filter($recentPayments, function($p) { 
            return $p['status'] === 'PROCESSING' || $p['status'] === 'PENDING'; 
        }));
        $stats['failed'] = count(array_filter($recentPayments, function($p) { 
            return $p['status'] === 'FAILED'; 
        }));
        $stats['total_amount'] = array_sum(array_column($recentPayments, 'collectedAmount'));
        
        // Get payout statistics
        $stats['payouts'] = [
            'total' => 0,
            'successful' => 0,
            'pending' => 0,
            'failed' => 0,
            'total_amount' => 0
        ];
        
        try {
            $payoutResponse = $api->queryAllPayouts(['limit' => 20]);
            if (isset($payoutResponse['data'])) {
                $payouts = $payoutResponse['data'];
                $stats['payouts']['total'] = $payoutResponse['totalCount'];
                $stats['payouts']['successful'] = count(array_filter($payouts, function($p) { 
                    return $p['status'] === 'SUCCESS' || $p['status'] === 'SETTLED'; 
                }));
                $stats['payouts']['pending'] = count(array_filter($payouts, function($p) { 
                    return $p['status'] === 'PROCESSING' || $p['status'] === 'PENDING'; 
                }));
                $stats['payouts']['failed'] = count(array_filter($payouts, function($p) { 
                    return $p['status'] === 'FAILED'; 
                }));
                $stats['payouts']['total_amount'] = array_sum(array_column($payouts, 'amount'));
            }
        } catch (Exception $e) {
            // Payout stats not critical, continue without them
        }
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
    <title>Dashboard - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 30px auto;
            max-width: 1400px;
            padding: 40px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 50px;
        }
        .header-section h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 2.5rem;
        }
        .header-section p {
            color: #666;
            font-size: 18px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        .stat-card.primary {
            border-left-color: #667eea;
        }
        .stat-card.success {
            border-left-color: #28a745;
        }
        .stat-card.warning {
            border-left-color: #ffc107;
        }
        .stat-card.danger {
            border-left-color: #dc3545;
        }
        .stat-card .icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        .stat-card.primary .icon { color: #667eea; }
        .stat-card.success .icon { color: #28a745; }
        .stat-card.warning .icon { color: #ffc107; }
        .stat-card.danger .icon { color: #dc3545; }
        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #333;
        }
        .stat-card p {
            color: #666;
            margin: 0;
            font-weight: 500;
        }
        .quick-actions {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 40px;
            color: white;
            margin-bottom: 40px;
        }
        .quick-actions h2 {
            margin-bottom: 25px;
            font-weight: 600;
        }
        .action-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin: 5px;
        }
        .action-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-2px);
        }
        .action-btn i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .recent-transactions {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
        .recent-transactions h3 {
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }
        .transaction-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #f1f3f5;
            transition: background-color 0.3s ease;
        }
        .transaction-item:hover {
            background-color: #f8f9fa;
        }
        .transaction-item:last-child {
            border-bottom: none;
        }
        .transaction-info {
            flex: 1;
        }
        .transaction-id {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .transaction-meta {
            color: #666;
            font-size: 14px;
        }
        .transaction-amount {
            text-align: right;
        }
        .amount {
            font-weight: 700;
            font-size: 1.1rem;
            color: #333;
        }
        .status-badge {
            font-size: 11px;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 5px;
            display: inline-block;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        .status-processing {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-pending {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-settled {
            background-color: #d1ecf1;
            color: #0c5460;
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
                <a class="nav-link active" href="index.php">Dashboard</a>
                <a class="nav-link" href="advanced_dashboard.php">Advanced</a>
                <a class="nav-link" href="live_status.php">Live Status</a>
                <a class="nav-link" href="initiate_payment.php">Payments</a>
                <a class="nav-link" href="initiate_payout.php">Payouts</a>
                <a class="nav-link" href="billpay_create.php">BillPay</a>
                <a class="nav-link" href="billpay_list.php">BillPay List</a>
                <a class="nav-link" href="account_management.php">Account</a>
                <a class="nav-link" href="payment_status.php">Payment Status</a>
                <a class="nav-link" href="payout_status.php">Payout Status</a>
                <a class="nav-link" href="payment_history.php">Payment History</a>
                <a class="nav-link" href="payout_history.php">Payout History</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-container">
            <div class="header-section">
                <h1><i class="fas fa-tachometer-alt text-primary me-3"></i>Payment Dashboard</h1>
                <p>Manage and monitor your ClickPesa payment transactions</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Authentication Issue Detected!</strong> Your IP whitelist may have been removed.
                    <div class="mt-2">
                        <strong>Quick Fix:</strong>
                        <ul class="mb-0">
                            <li>Re-whitelist IPs: 102.208.186.66 and 192.168.3.163</li>
                            <li><a href="fix_authentication.php" class="btn btn-sm btn-warning mt-2">Fix Authentication</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3><?php echo number_format($stats['total_transactions']); ?></h3>
                    <p>Payments</p>
                </div>
                <div class="stat-card success">
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3><?php echo number_format($stats['successful']); ?></h3>
                    <p>Successful</p>
                </div>
                <div class="stat-card warning">
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3><?php echo number_format($stats['pending']); ?></h3>
                    <p>Pending</p>
                </div>
                <div class="stat-card danger">
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3><?php echo number_format($stats['failed']); ?></h3>
                    <p>Failed</p>
                </div>
                <div class="stat-card info">
                    <div class="icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h3><?php echo number_format($stats['payouts']['total']); ?></h3>
                    <p>Payouts</p>
                </div>
                <div class="stat-card" style="border-left-color: #9c27b0;">
                    <div class="icon" style="color: #9c27b0;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3><?php echo number_format($stats['total_amount'] + $stats['payouts']['total_amount']); ?></h3>
                    <p>Total Volume (TZS)</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="fas fa-bolt me-3"></i>Quick Actions</h2>
                        <p class="mb-0">Get started with common payment operations</p>
                    </div>
                    <div>
                        <button class="refresh-btn me-2" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="window.location.href='test_api_direct.php'">
                            <i class="fas fa-plug me-2"></i>Test API
                        </button>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="initiate_payment.php" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        New Payment
                    </a>
                    <a href="initiate_payout.php" class="action-btn">
                        <i class="fas fa-hand-holding-usd"></i>
                        New Payout
                    </a>
                    <a href="billpay_create.php" class="action-btn">
                        <i class="fas fa-file-invoice-dollar"></i>
                        BillPay
                    </a>
                    <a href="account_management.php" class="action-btn">
                        <i class="fas fa-wallet"></i>
                        Account
                    </a>
                    <a href="payment_status.php" class="action-btn">
                        <i class="fas fa-search"></i>
                        Check Status
                    </a>
                    <a href="payout_status.php" class="action-btn">
                        <i class="fas fa-search-dollar"></i>
                        Payout Status
                    </a>
                    <a href="billpay_manage.php" class="action-btn">
                        <i class="fas fa-cog"></i>
                        BillPay Mgmt
                    </a>
                    <a href="payment_history.php" class="action-btn">
                        <i class="fas fa-history"></i>
                        Payment History
                    </a>
                    <a href="payout_history.php" class="action-btn">
                        <i class="fas fa-history-alt"></i>
                        Payout History
                    </a>
                    <a href="live_status.php" class="action-btn">
                        <i class="fas fa-satellite-dish"></i>
                        Live Status
                    </a>
                    <a href="advanced_dashboard.php" class="action-btn">
                        <i class="fas fa-chart-line"></i>
                        Advanced
                    </a>
                    <a href="fix_authentication.php" class="action-btn" style="background: rgba(255, 193, 7, 0.3); border-color: #ffc107;">
                        <i class="fas fa-wrench"></i>
                        Fix Auth
                    </a>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="recent-transactions">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="fas fa-list-alt text-primary me-2"></i>Recent Transactions</h3>
                    <a href="payment_history.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>View All
                    </a>
                </div>

                <?php if (!empty($recentPayments)): ?>
                    <?php foreach ($recentPayments as $payment): ?>
                        <div class="transaction-item">
                            <div class="transaction-info">
                                <div class="transaction-id">
                                    <i class="fas fa-receipt text-muted me-2"></i>
                                    <?php echo htmlspecialchars($payment['orderReference']); ?>
                                </div>
                                <div class="transaction-meta">
                                    <?php 
                                    $phone = '';
                                    if (isset($payment['paymentPhoneNumber'])) {
                                        $phone = $payment['paymentPhoneNumber'];
                                    } elseif (isset($payment['customer']['customerPhoneNumber'])) {
                                        $phone = $payment['customer']['customerPhoneNumber'];
                                    }
                                    echo $phone ? 'Phone: ' . htmlspecialchars($phone) . ' • ' : '';
                                    echo date('M j, Y H:i', strtotime($payment['createdAt'])); 
                                    ?>
                                </div>
                            </div>
                            <div class="transaction-amount">
                                <div class="amount">
                                    <?php echo number_format($payment['collectedAmount']); ?> <?php echo htmlspecialchars($payment['collectedCurrency']); ?>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($payment['status']); ?>">
                                    <?php echo htmlspecialchars($payment['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No Recent Transactions</h5>
                        <p>Start by initiating your first payment transaction.</p>
                        <a href="initiate_payment.php" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>Initiate Payment
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- System Information -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-info-circle text-primary me-2"></i>System Information
                            </h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-server text-muted me-2"></i>
                                    <strong>API Base URL:</strong> <?php echo $config['clickpesa']['api_base_url']; ?>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-money-bill text-muted me-2"></i>
                                    <strong>Default Currency:</strong> <?php echo $config['clickpesa']['currency']; ?>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <strong>Token Validity:</strong> 1 hour
                                </li>
                                <li>
                                    <i class="fas fa-shield-alt text-muted me-2"></i>
                                    <strong>Connection:</strong> Secure (HTTPS)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-chart-line text-primary me-2"></i>Performance Summary
                            </h5>
                            <?php if ($stats['total_transactions'] > 0): ?>
                                <div class="mb-2">
                                    <strong>Success Rate:</strong> 
                                    <?php echo round(($stats['successful'] / $stats['total_transactions']) * 100, 1); ?>%
                                </div>
                                <div class="mb-2">
                                    <strong>Total Volume:</strong> 
                                    <?php echo number_format($stats['total_amount']); ?> TZS
                                </div>
                                <div class="mb-2">
                                    <strong>Average Transaction:</strong> 
                                    <?php echo number_format($stats['total_amount'] / max(1, $stats['total_transactions'])); ?> TZS
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No transaction data available yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh dashboard every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'n':
                        e.preventDefault();
                        window.location.href = 'initiate_payment.php';
                        break;
                    case 's':
                        e.preventDefault();
                        window.location.href = 'payment_status.php';
                        break;
                    case 'h':
                        e.preventDefault();
                        window.location.href = 'payment_history.php';
                        break;
                }
            }
        });
    </script>
</body>
</html>
