<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$payouts = null;
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
    $params['currency'] = $_GET['currency'];
}
if (!empty($_GET['payout_type'])) {
    $params['channel'] = $_GET['payout_type'];
}
if (!empty($_GET['reference'])) {
    $params['orderReference'] = $_GET['reference'];
}
if (!empty($_GET['start_date'])) {
    $params['startDate'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $params['endDate'] = $_GET['end_date'];
}

try {
    $response = $api->queryAllPayouts($params);
    
    if (isset($response['data'])) {
        $payouts = $response['data'];
        $totalCount = $response['totalCount'];
        $success = 'Retrieved ' . count($payouts) . ' payout records';
    } else {
        $payouts = [];
        $success = 'No payout records found matching your criteria';
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
    <title>Payout History - ClickPesa</title>
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
        .payout-table {
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
        .payout-type-badge {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .payout-mobile {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .payout-bank {
            background-color: #f3e5f5;
            color: #7b1fa2;
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
                <a class="nav-link" href="initiate_payment.php">Payments</a>
                <a class="nav-link" href="initiate_payout.php">Payouts</a>
                <a class="nav-link" href="payout_status.php">Payout Status</a>
                <a class="nav-link active" href="payout_history.php">Payout History</a>
                <a class="nav-link" href="payment_status.php">Payment Status</a>
                <a class="nav-link" href="payment_history.php">Payment History</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="history-container">
            <div class="header-section">
                <h1><i class="fas fa-history text-primary me-3"></i>Payout History</h1>
                <p>View and filter all your payout transactions</p>
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
            <?php if ($payouts && count($payouts) > 0): ?>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo $totalCount; ?></h3>
                            <p>Total Payouts</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo count(array_filter($payouts, function($p) { return $p['status'] === 'SUCCESS' || $p['status'] === 'SETTLED'; })); ?></h3>
                            <p>Successful</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo count(array_filter($payouts, function($p) { return $p['status'] === 'PROCESSING' || $p['status'] === 'PENDING'; })); ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h3><?php echo array_sum(array_column($payouts, 'amount')); ?></h3>
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
                        <label class="form-label">Payout Type</label>
                        <select name="payout_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="MOBILE MONEY" <?php echo isset($_GET['payout_type']) && $_GET['payout_type'] === 'MOBILE MONEY' ? 'selected' : ''; ?>>Mobile Money</option>
                            <option value="BANK TRANSFER" <?php echo isset($_GET['payout_type']) && $_GET['payout_type'] === 'BANK TRANSFER' ? 'selected' : ''; ?>>Bank Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Reference</label>
                        <input type="text" name="reference" class="form-control" 
                               placeholder="Reference" 
                               value="<?php echo isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : ''; ?>">
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
                            <a href="payout_history.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Payout Table -->
            <?php if ($payouts && count($payouts) > 0): ?>
                <div class="payout-table">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Payout ID</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Recipient</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payouts as $payout): ?>
                                    <tr>
                                        <td>
                                            <code><?php echo substr(htmlspecialchars($payout['id']), 0, 12); ?>...</code>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($payout['orderReference'] ?? $payout['reference'] ?? 'N/A'); ?></strong>
                                        </td>
                                        <td class="amount-cell">
                                            <?php echo number_format($payout['amount']); ?> <?php echo htmlspecialchars($payout['currency']); ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($payout['status']); ?>">
                                                <?php echo htmlspecialchars($payout['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $payoutType = $payout['channel'] ?? 'MOBILE MONEY';
                                            $typeLabel = $payoutType === 'BANK TRANSFER' ? 'Bank' : 'Mobile';
                                            $typeClass = $payoutType === 'BANK TRANSFER' ? 'payout-bank' : 'payout-mobile';
                                            echo "<span class='payout-type-badge {$typeClass}'>{$typeLabel}</span>";
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if (isset($payout['beneficiary']['accountName']) && $payout['beneficiary']['accountName']) {
                                                echo htmlspecialchars($payout['beneficiary']['accountName']);
                                            } elseif (isset($payout['beneficiary']['beneficiaryMobileNumber']) && $payout['beneficiary']['beneficiaryMobileNumber']) {
                                                echo htmlspecialchars($payout['beneficiary']['beneficiaryMobileNumber']);
                                            } elseif (isset($payout['beneficiary']['accountNumber']) && $payout['beneficiary']['accountNumber']) {
                                                echo '****' . substr(htmlspecialchars($payout['beneficiary']['accountNumber']), -4);
                                            } else {
                                                echo '<span class="text-muted">N/A</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo date('Y-m-d H:i', strtotime($payout['createdAt'])); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="payout_status.php?reference=<?php echo urlencode($payout['orderReference'] ?? $payout['reference'] ?? ''); ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Check Status">
                                                    <i class="fas fa-search"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="showPayoutDetails('<?php echo htmlspecialchars(json_encode($payout)); ?>')" 
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
                    <h5>No Payout Records Found</h5>
                    <p>There are no payout records matching your criteria.</p>
                    <a href="initiate_payout.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Initiate Payout
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payout Details Modal -->
    <div class="modal fade" id="payoutDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-hand-holding-usd text-primary me-2"></i>Payout Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="payoutDetailsContent">
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
        function showPayoutDetails(payoutData) {
            const payout = typeof payoutData === 'string' ? JSON.parse(payoutData) : payoutData;
            
            let detailsHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Payout Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Payout ID:</strong></td><td>${payout.id || 'N/A'}</td></tr>
                            <tr><td><strong>Reference:</strong></td><td>${payout.orderReference || payout.reference || 'N/A'}</td></tr>
                            <tr><td><strong>Amount:</strong></td><td>${new Intl.NumberFormat().format(payout.amount || 0)} ${payout.currency || 'N/A'}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="status-badge status-${(payout.status || '').toLowerCase()}">${payout.status || 'N/A'}</span></td></tr>
                            <tr><td><strong>Type:</strong></td><td>${payout.channel === 'BANK TRANSFER' ? 'Bank Transfer' : 'Mobile Money'}</td></tr>
                            <tr><td><strong>Created:</strong></td><td>${payout.createdAt ? new Date(payout.createdAt).toLocaleString() : 'N/A'}</td></tr>
            `;
            
            if (payout.payoutReference) {
                detailsHtml += `<tr><td><strong>Payout Reference:</strong></td><td>${payout.payoutReference}</td></tr>`;
            }
            
            if (payout.message) {
                detailsHtml += `<tr><td><strong>Message:</strong></td><td>${payout.message}</td></tr>`;
            }
            
            detailsHtml += `
                        </table>
                    </div>
                    <div class="col-md-6">
            `;
            
            if (payout.beneficiary) {
                detailsHtml += `
                    <h6>Beneficiary Information</h6>
                    <table class="table table-sm">
                `;
                
                if (payout.beneficiary.accountName) {
                    detailsHtml += `<tr><td><strong>Name:</strong></td><td>${payout.beneficiary.accountName}</td></tr>`;
                }
                
                if (payout.beneficiary.beneficiaryMobileNumber) {
                    detailsHtml += `<tr><td><strong>Phone:</strong></td><td>${payout.beneficiary.beneficiaryMobileNumber}</td></tr>`;
                }
                
                if (payout.beneficiary.accountNumber) {
                    detailsHtml += `<tr><td><strong>Bank Account:</strong></td><td>${payout.beneficiary.accountNumber}</td></tr>`;
                }
                
                if (payout.beneficiary.bankName) {
                    detailsHtml += `<tr><td><strong>Bank:</strong></td><td>${payout.beneficiary.bankName}</td></tr>`;
                }
                
                if (payout.beneficiary && payout.beneficiary.email) {
                    detailsHtml += `<tr><td><strong>Email:</strong></td><td>${payout.beneficiary.email}</td></tr>`;
                }
                
                detailsHtml += `
                    </table>
                `;
            }
            
            detailsHtml += `
                    </div>
                </div>
            `;
            
            document.getElementById('payoutDetailsContent').innerHTML = detailsHtml;
            new bootstrap.Modal(document.getElementById('payoutDetailsModal')).show();
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
