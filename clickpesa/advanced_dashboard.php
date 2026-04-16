<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$dashboardData = [
    'recentPayments' => [],
    'stats' => [
        'total_transactions' => 0,
        'successful' => 0,
        'pending' => 0,
        'failed' => 0,
        'total_amount' => 0,
        'success_rate' => 0
    ],
    'error' => null
];

try {
    // Get comprehensive dashboard data
    $response = $api->queryAllPayments(['limit' => 50, 'orderBy' => 'DESC']);
    
    if (isset($response['data'])) {
        $allPayments = $response['data'];
        $dashboardData['recentPayments'] = array_slice($allPayments, 0, 10);
        
        // Calculate statistics
        $dashboardData['stats']['total_transactions'] = $response['totalCount'];
        $dashboardData['stats']['successful'] = count(array_filter($allPayments, function($p) { 
            return $p['status'] === 'SUCCESS' || $p['status'] === 'SETTLED'; 
        }));
        $dashboardData['stats']['pending'] = count(array_filter($allPayments, function($p) { 
            return $p['status'] === 'PROCESSING' || $p['status'] === 'PENDING'; 
        }));
        $dashboardData['stats']['failed'] = count(array_filter($allPayments, function($p) { 
            return $p['status'] === 'FAILED'; 
        }));
        $dashboardData['stats']['total_amount'] = array_sum(array_column($allPayments, 'collectedAmount'));
        
        if ($dashboardData['stats']['total_transactions'] > 0) {
            $dashboardData['stats']['success_rate'] = round(
                ($dashboardData['stats']['successful'] / $dashboardData['stats']['total_transactions']) * 100, 1
            );
        } else {
            $dashboardData['stats']['success_rate'] = 0;
        }
        
        // Ensure all values are numeric to prevent chart errors
        $dashboardData['stats']['total_transactions'] = max(0, $dashboardData['stats']['total_transactions']);
        $dashboardData['stats']['successful'] = max(0, $dashboardData['stats']['successful']);
        $dashboardData['stats']['pending'] = max(0, $dashboardData['stats']['pending']);
        $dashboardData['stats']['failed'] = max(0, $dashboardData['stats']['failed']);
        $dashboardData['stats']['total_amount'] = max(0, $dashboardData['stats']['total_amount']);
        $dashboardData['stats']['success_rate'] = max(0, min(100, $dashboardData['stats']['success_rate']));
    }
} catch (Exception $e) {
    $dashboardData['error'] = $e->getMessage();
}

// Ensure dashboard data exists even if API fails
if (!isset($dashboardData['stats'])) {
    $dashboardData['stats'] = [
        'total_transactions' => 0,
        'successful' => 0,
        'pending' => 0,
        'failed' => 0,
        'total_amount' => 0,
        'success_rate' => 0
    ];
}

if (!isset($dashboardData['recentPayments'])) {
    $dashboardData['recentPayments'] = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Dashboard - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .advanced-container {
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
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
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
        .stat-card.info .icon { color: #17a2b8; }
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
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        .chart-container h3 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .transaction-table {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
        .transaction-table h3 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f5;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .status-badge {
            font-size: 11px;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        .metric-card h4 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .metric-card p {
            margin: 0;
            opacity: 0.9;
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
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
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
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link active" href="advanced_dashboard.php">Advanced</a>
                <a class="nav-link" href="live_status.php">Live Status</a>
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
        <div class="advanced-container">
            <div class="header-section">
                <h1><i class="fas fa-chart-line text-primary me-3"></i>Advanced Dashboard</h1>
                <p>Comprehensive analytics and real-time monitoring</p>
            </div>

            <?php if ($dashboardData['error']): ?>
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>API Error:</strong> <?php echo $dashboardData['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div class="alert alert-warning alert-custom">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Troubleshooting:</strong> Your IP whitelist may have been removed. Please re-add your IPs to ClickPesa dashboard:
                    <ul class="mb-0 mt-2">
                        <li><strong>Public IP:</strong> 102.208.186.66</li>
                        <li><strong>Local IP:</strong> 192.168.3.163</li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Key Metrics -->
            <div class="metric-grid">
                <div class="metric-card">
                    <h4><?php echo number_format($dashboardData['stats']['total_amount']); ?></h4>
                    <p>Total Volume (TZS)</p>
                </div>
                <div class="metric-card">
                    <h4><?php echo $dashboardData['stats']['success_rate']; ?>%</h4>
                    <p>Success Rate</p>
                </div>
                <div class="metric-card">
                    <h4><?php echo number_format($dashboardData['stats']['total_transactions']); ?></h4>
                    <p>Total Transactions</p>
                </div>
                <div class="metric-card">
                    <h4><?php echo number_format($dashboardData['stats']['pending']); ?></h4>
                    <p>Active/Pending</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3><?php echo number_format($dashboardData['stats']['total_transactions']); ?></h3>
                    <p>Total Transactions</p>
                </div>
                <div class="stat-card success">
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3><?php echo number_format($dashboardData['stats']['successful']); ?></h3>
                    <p>Successful</p>
                </div>
                <div class="stat-card warning">
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3><?php echo number_format($dashboardData['stats']['pending']); ?></h3>
                    <p>Pending</p>
                </div>
                <div class="stat-card danger">
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3><?php echo number_format($dashboardData['stats']['failed']); ?></h3>
                    <p>Failed</p>
                </div>
                <div class="stat-card info">
                    <div class="icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <h3><?php echo $dashboardData['stats']['success_rate']; ?>%</h3>
                    <p>Success Rate</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-container">
                        <h3><i class="fas fa-chart-pie text-primary me-2"></i>Transaction Status Distribution</h3>
                        <canvas id="statusChart" width="400" height="300"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-container">
                        <h3><i class="fas fa-chart-bar text-primary me-2"></i>Performance Overview</h3>
                        <canvas id="performanceChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="transaction-table">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3><i class="fas fa-list-alt text-primary me-2"></i>Recent Transactions</h3>
                    <button class="refresh-btn" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh Data
                    </button>
                </div>

                <?php if (!empty($dashboardData['recentPayments'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboardData['recentPayments'] as $payment): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($payment['orderReference']); ?></strong>
                                        </td>
                                        <td class="fw-bold">
                                            <?php echo number_format($payment['collectedAmount']); ?> TZS
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($payment['status']); ?>">
                                                <?php echo htmlspecialchars($payment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $phone = '';
                                            if (isset($payment['paymentPhoneNumber'])) {
                                                $phone = $payment['paymentPhoneNumber'];
                                            } elseif (isset($payment['customer']['customerPhoneNumber'])) {
                                                $phone = $payment['customer']['customerPhoneNumber'];
                                            }
                                            echo $phone ? htmlspecialchars($phone) : '<span class="text-muted">N/A</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo date('M j, H:i', strtotime($payment['createdAt'])); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="payment_status.php?reference=<?php echo urlencode($payment['orderReference']); ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Check Status">
                                                    <i class="fas fa-search"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="showDetails('<?php echo htmlspecialchars(json_encode($payment)); ?>')" 
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3 text-muted">No Transactions Found</h5>
                        <p class="text-muted">Start by initiating your first payment transaction.</p>
                        <a href="initiate_payment.php" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>Initiate Payment
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt text-primary me-2"></i>Transaction Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Chart.js configurations - Ensure all data is valid to prevent infinity
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Successful', 'Pending', 'Failed'],
                datasets: [{
                    data: [
                        <?php echo isset($dashboardData['stats']['successful']) ? $dashboardData['stats']['successful'] : 0; ?>,
                        <?php echo isset($dashboardData['stats']['pending']) ? $dashboardData['stats']['pending'] : 0; ?>,
                        <?php echo isset($dashboardData['stats']['failed']) ? $dashboardData['stats']['failed'] : 0; ?>
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(performanceCtx, {
            type: 'bar',
            data: {
                labels: ['Total', 'Success', 'Pending', 'Failed'],
                datasets: [{
                    label: 'Transactions',
                    data: [
                        <?php echo isset($dashboardData['stats']['total_transactions']) ? $dashboardData['stats']['total_transactions'] : 0; ?>,
                        <?php echo isset($dashboardData['stats']['successful']) ? $dashboardData['stats']['successful'] : 0; ?>,
                        <?php echo isset($dashboardData['stats']['pending']) ? $dashboardData['stats']['pending'] : 0; ?>,
                        <?php echo isset($dashboardData['stats']['failed']) ? $dashboardData['stats']['failed'] : 0; ?>
                    ],
                    backgroundColor: ['#667eea', '#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value === Infinity || value === -Infinity || isNaN(value)) {
                                    return 0;
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });

        function showDetails(paymentData) {
            const payment = typeof paymentData === 'string' ? JSON.parse(paymentData) : paymentData;
            
            let detailsHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Transaction Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Transaction ID:</strong></td><td>${payment.id}</td></tr>
                            <tr><td><strong>Order Reference:</strong></td><td>${payment.orderReference}</td></tr>
                            <tr><td><strong>Amount:</strong></td><td>${new Intl.NumberFormat().format(payment.collectedAmount)} ${payment.collectedCurrency}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="status-badge status-${payment.status.toLowerCase()}">${payment.status}</span></td></tr>
                            <tr><td><strong>Created:</strong></td><td>${new Date(payment.createdAt).toLocaleString()}</td></tr>
            `;
            
            if (payment.paymentReference) {
                detailsHtml += `<tr><td><strong>Payment Reference:</strong></td><td>${payment.paymentReference}</td></tr>`;
            }
            
            if (payment.message) {
                detailsHtml += `<tr><td><strong>Message:</strong></td><td>${payment.message}</td></tr>`;
            }
            
            detailsHtml += `
                        </table>
                    </div>
                    <div class="col-md-6">
            `;
            
            if (payment.customer) {
                detailsHtml += `
                    <h6>Customer Information</h6>
                    <table class="table table-sm">
                `;
                
                if (payment.customer.customerName) {
                    detailsHtml += `<tr><td><strong>Name:</strong></td><td>${payment.customer.customerName}</td></tr>`;
                }
                
                if (payment.customer.customerPhoneNumber) {
                    detailsHtml += `<tr><td><strong>Phone:</strong></td><td>${payment.customer.customerPhoneNumber}</td></tr>`;
                }
                
                if (payment.customer.customerEmail) {
                    detailsHtml += `<tr><td><strong>Email:</strong></td><td>${payment.customer.customerEmail}</td></tr>`;
                }
                
                detailsHtml += `
                    </table>
                `;
            }
            
            detailsHtml += `
                    </div>
                </div>
            `;
            
            document.getElementById('detailsContent').innerHTML = detailsHtml;
            new bootstrap.Modal(document.getElementById('detailsModal')).show();
        }

        // Auto-refresh every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);

        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
