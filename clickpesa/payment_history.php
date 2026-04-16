<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$payments = null;
$error = '';
$success = '';
$totalCount = 0;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$skip = ($currentPage - 1) * $limit;

// Build query parameters
$params = [
    'limit' => $limit,
    'skip' => $skip,
    'orderBy' => 'DESC',
    'sortBy' => 'createdAt'
];

// Apply filters
if (!empty($_GET['status'])) {
    $params['status'] = $_GET['status'];
}
if (!empty($_GET['currency'])) {
    $params['collectedCurrency'] = $_GET['currency'];
}
if (!empty($_GET['channel'])) {
    $params['channel'] = $_GET['channel'];
}
if (!empty($_GET['order_reference'])) {
    $params['orderReference'] = $_GET['order_reference'];
}
if (!empty($_GET['start_date'])) {
    $params['startDate'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $params['endDate'] = $_GET['end_date'];
}

try {
    $response = $api->queryAllPayments($params);
    
    if (isset($response['data'])) {
        $payments = $response['data'];
        $totalCount = $response['totalCount'];
        $success = 'Retrieved ' . count($payments) . ' payment records';
    } else {
        $payments = [];
        $success = 'No payment records found matching your criteria';
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
    <title>Payment History - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .history-container {
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
        }
        .header-section p {
            color: #666;
            font-size: 16px;
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .payment-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
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
        .amount-cell {
            font-weight: 600;
            color: #333;
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-outline-primary {
            border-color: #667eea;
            color: #667eea;
            font-weight: 600;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        .btn-outline-primary:hover {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
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
        .pagination {
            justify-content: center;
            margin-top: 30px;
        }
        .page-link {
            color: #667eea;
            border: 1px solid #dee2e6;
            margin: 0 2px;
            border-radius: 5px;
        }
        .page-link:hover {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }
        .page-item.active .page-link {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
        .stats-card h3 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stats-card p {
            color: #666;
            margin: 0;
            font-size: 14px;
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
                <a class="nav-link" href="live_status.php">Live Status</a>
                <a class="nav-link" href="initiate_payment.php">Initiate Payment</a>
                <a class="nav-link" href="payment_status.php">Check Status</a>
                <a class="nav-link active" href="payment_history.php">History</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="history-container">
            <div class="header-section">
                <h1><i class="fas fa-history text-primary me-3"></i>Payment History</h1>
                <p>View and filter all your payment transactions</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <?php if ($payments && count($payments) > 0): ?>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo $totalCount; ?></h3>
                            <p>Total Transactions</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo count(array_filter($payments, function($p) { return $p['status'] === 'SUCCESS' || $p['status'] === 'SETTLED'; })); ?></h3>
                            <p>Successful</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo count(array_filter($payments, function($p) { return $p['status'] === 'PROCESSING' || $p['status'] === 'PENDING'; })); ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo array_sum(array_column($payments, 'collectedAmount')); ?></h3>
                            <p>Total Amount (TZS)</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="SUCCESS" <?php echo isset($_GET['status']) && $_GET['status'] === 'SUCCESS' ? 'selected' : ''; ?>>Success</option>
                            <option value="SETTLED" <?php echo isset($_GET['status']) && $_GET['status'] === 'SETTLED' ? 'selected' : ''; ?>>Settled</option>
                            <option value="PROCESSING" <?php echo isset($_GET['status']) && $_GET['status'] === 'PROCESSING' ? 'selected' : ''; ?>>Processing</option>
                            <option value="PENDING" <?php echo isset($_GET['status']) && $_GET['status'] === 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                            <option value="FAILED" <?php echo isset($_GET['status']) && $_GET['status'] === 'FAILED' ? 'selected' : ''; ?>>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-select">
                            <option value="">All Currencies</option>
                            <option value="TZS" <?php echo isset($_GET['currency']) && $_GET['currency'] === 'TZS' ? 'selected' : ''; ?>>TZS</option>
                            <option value="USD" <?php echo isset($_GET['currency']) && $_GET['currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Order Reference</label>
                        <input type="text" name="order_reference" class="form-control" 
                               placeholder="Order Reference" 
                               value="<?php echo isset($_GET['order_reference']) ? htmlspecialchars($_GET['order_reference']) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Channel</label>
                        <input type="text" name="channel" class="form-control" 
                               placeholder="Payment Channel" 
                               value="<?php echo isset($_GET['channel']) ? htmlspecialchars($_GET['channel']) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <a href="payment_history.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Payment Table -->
            <?php if ($payments && count($payments) > 0): ?>
                <div class="payment-table">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Order Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Phone Number</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td>
                                            <code><?php echo substr(htmlspecialchars($payment['id']), 0, 12); ?>...</code>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($payment['orderReference']); ?></strong>
                                        </td>
                                        <td class="amount-cell">
                                            <?php echo number_format($payment['collectedAmount']); ?> <?php echo htmlspecialchars($payment['collectedCurrency']); ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($payment['status']); ?>">
                                                <?php echo htmlspecialchars($payment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            if (isset($payment['paymentPhoneNumber'])) {
                                                echo htmlspecialchars($payment['paymentPhoneNumber']);
                                            } elseif (isset($payment['customer']['customerPhoneNumber'])) {
                                                echo htmlspecialchars($payment['customer']['customerPhoneNumber']);
                                            } else {
                                                echo '<span class="text-muted">N/A</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo date('Y-m-d H:i', strtotime($payment['createdAt'])); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="payment_status.php?reference=<?php echo urlencode($payment['orderReference']); ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Check Status">
                                                    <i class="fas fa-search"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="showPaymentDetails('<?php echo htmlspecialchars(json_encode($payment)); ?>')" 
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
                </div>

                <!-- Pagination -->
                <?php if ($totalCount > $limit): ?>
                    <nav>
                        <ul class="pagination">
                            <?php
                            $totalPages = ceil($totalCount / $limit);
                            $currentUrl = $_SERVER['REQUEST_URI'];
                            $urlParts = parse_url($currentUrl);
                            parse_str($urlParts['query'] ?? '', $queryParams);
                            unset($queryParams['page']);
                            $baseUrl = $urlParts['path'] . '?' . http_build_query($queryParams);
                            
                            // Previous button
                            if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>&page=<?php echo $currentPage - 1; ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            // Page numbers
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php
                            // Next button
                            if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>&page=<?php echo $currentPage + 1; ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>No Payment Records Found</h5>
                    <p>There are no payment records matching your criteria.</p>
                    <a href="initiate_payment.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Initiate New Payment
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt text-primary me-2"></i>Payment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="paymentDetailsContent">
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
        function showPaymentDetails(paymentData) {
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
            
            if (payment.exchange) {
                detailsHtml += `
                    <h6>Exchange Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Source Currency:</strong></td><td>${payment.exchange.sourceCurrency}</td></tr>
                        <tr><td><strong>Target Currency:</strong></td><td>${payment.exchange.targetCurrency}</td></tr>
                        <tr><td><strong>Source Amount:</strong></td><td>${payment.exchange.sourceAmount}</td></tr>
                        <tr><td><strong>Exchange Rate:</strong></td><td>${payment.exchange.rate}</td></tr>
                    </table>
                `;
            }
            
            detailsHtml += `
                    </div>
                </div>
            `;
            
            document.getElementById('paymentDetailsContent').innerHTML = detailsHtml;
            new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
        }

        // Auto-dismiss alerts after 5 seconds
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
