<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$billPayNumbers = [];
$error = '';
$success = '';

// Get account statement to find BillPay transactions
try {
    $statementData = $api->getAccountStatement('TZS');
    
    if (isset($statementData['transactions'])) {
        // Enhanced filtering for BillPay related transactions
        foreach ($statementData['transactions'] as $transaction) {
            // Look for BillPay related transactions with multiple keywords
            $description = strtolower($transaction['description'] ?? '');
            $isBillPayRelated = false;
            
            // Check for various BillPay indicators
            $billPayKeywords = ['billpay', 'bill', 'control', 'invoice', 'payment', 'fee'];
            foreach ($billPayKeywords as $keyword) {
                if (strpos($description, $keyword) !== false) {
                    $isBillPayRelated = true;
                    break;
                }
            }
            
            // Also check if it's a control number format (FEEDTANPAY or similar)
            if (isset($transaction['orderReference']) && 
                (strpos($transaction['orderReference'], 'FEEDTANPAY') !== false ||
                 preg_match('/^[0-9]{10,}$/', $transaction['orderReference']))) {
                $isBillPayRelated = true;
            }
            
            if ($isBillPayRelated) {
                $billPayNumbers[] = [
                    'billPayNumber' => $transaction['orderReference'] ?? 'Unknown',
                    'description' => $transaction['description'],
                    'amount' => $transaction['amount'],
                    'currency' => $transaction['currency'],
                    'date' => $transaction['date'],
                    'type' => $transaction['type'] ?? 'Transaction',
                    'entry' => $transaction['entry'] ?? 'Unknown',
                    'balance' => $transaction['balance'] ?? 0,
                    'status' => 'ACTIVE',
                    'fullTransaction' => $transaction // Store full transaction data
                ];
            }
        }
        
        $success = 'Found ' . count($billPayNumbers) . ' BillPay control numbers from live system';
        
    } else {
        throw new Exception('No transaction data available');
    }
    
} catch (Exception $e) {
    $error = 'Failed to retrieve BillPay numbers: ' . $e->getMessage();
    
    // Don't add sample data - show real error instead
    $billPayNumbers = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BillPay Control Numbers - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .billpay-list-container {
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
        .billpay-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
            transition: all 0.3s ease;
        }
        .billpay-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        .billpay-number {
            font-size: 1.4rem;
            font-weight: 700;
            color: #667eea;
            font-family: monospace;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid #667eea;
            text-align: center;
            margin-bottom: 15px;
        }
        .status-badge {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .amount-display {
            font-size: 1.2rem;
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
        .stat-card h3 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stat-card p {
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
        .filter-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
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
                <a class="nav-link" href="billpay_create.php">BillPay</a>
                <a class="nav-link" href="billpay_list.php" class="active">BillPay List</a>
                <a class="nav-link" href="billpay_manage.php">BillPay Manage</a>
                <a class="nav-link" href="account_management.php">Account</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="billpay-list-container">
            <div class="header-section">
                <h1><i class="fas fa-list text-primary me-3"></i>BillPay Control Numbers</h1>
                <p>View all active BillPay control numbers awaiting payment</p>
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

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo count($billPayNumbers); ?></h3>
                    <p>Total Control Numbers</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($billPayNumbers, function($b) { return $b['status'] === 'ACTIVE'; })); ?></h3>
                    <p>Active</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($billPayNumbers, function($b) { return $b['entry'] === 'Pending'; })); ?></h3>
                    <p>Awaiting Payment</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo array_sum(array_column($billPayNumbers, 'amount')); ?></h3>
                    <p>Total Amount (TZS)</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <h5><i class="fas fa-filter text-primary me-2"></i>Filter Control Numbers</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="ACTIVE">Active</option>
                                <option value="PENDING">Pending</option>
                                <option value="INACTIVE">Inactive</option>
                            </select>
                            <label for="statusFilter">Status</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select class="form-select" id="entryFilter">
                                <option value="">All Types</option>
                                <option value="Credit">Credit</option>
                                <option value="Debit">Debit</option>
                                <option value="Pending">Pending</option>
                            </select>
                            <label for="entryFilter">Entry Type</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search...">
                            <label for="searchFilter">Search</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BillPay Numbers List -->
            <?php if (!empty($billPayNumbers)): ?>
                <div id="billpayList">
                    <?php foreach ($billPayNumbers as $index => $billPay): ?>
                        <div class="billpay-card" data-status="<?php echo $billPay['status']; ?>" data-entry="<?php echo $billPay['entry']; ?>" data-search="<?php echo strtolower($billPay['billPayNumber'] . ' ' . $billPay['description']); ?>">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="billpay-number"><?php echo htmlspecialchars($billPay['billPayNumber']); ?></div>
                                    <small class="text-muted">Created: <?php echo date('Y-m-d H:i', strtotime($billPay['date'])); ?></small>
                                </div>
                                <div class="col-md-3">
                                    <div class="amount-display">
                                        <?php echo number_format($billPay['amount']); ?> <?php echo htmlspecialchars($billPay['currency']); ?>
                                    </div>
                                    <small class="text-muted"><?php echo htmlspecialchars($billPay['description']); ?></small>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted">Balance:</small><br>
                                    <strong><?php echo number_format($billPay['balance']); ?> TZS</strong>
                                </div>
                                <div class="col-md-3">
                                    <div class="btn-group" role="group">
                                        <a href="billpay_manage.php?billPayNumber=<?php echo urlencode($billPay['billPayNumber']); ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Manage">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="copyToClipboard('<?php echo htmlspecialchars($billPay['billPayNumber']); ?>')" 
                                                title="Copy Number">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="shareBillPay('<?php echo htmlspecialchars($billPay['billPayNumber']); ?>', '<?php echo htmlspecialchars($billPay['description']); ?>')" 
                                                title="Share">
                                            <i class="fas fa-share"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" 
                                                onclick="showDetails(<?php echo $index; ?>)" 
                                                title="Full Details">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Full Details Section (Hidden by default) -->
                            <div class="row mt-3" id="details-<?php echo $index; ?>" style="display: none;">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Full Transaction Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Transaction ID:</strong> <?php echo htmlspecialchars($billPay['fullTransaction']['id'] ?? 'N/A'); ?><br>
                                                    <strong>Transaction Type:</strong> <?php echo htmlspecialchars($billPay['type']); ?><br>
                                                    <strong>Entry Type:</strong> <?php echo htmlspecialchars($billPay['entry']); ?><br>
                                                    <strong>Currency:</strong> <?php echo htmlspecialchars($billPay['currency']); ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Order Reference:</strong> <?php echo htmlspecialchars($billPay['fullTransaction']['orderReference'] ?? 'N/A'); ?><br>
                                                    <strong>Balance After:</strong> <?php echo number_format($billPay['balance']); ?> TZS<br>
                                                    <strong>Status:</strong> <?php echo htmlspecialchars($billPay['status']); ?><br>
                                                    <strong>Created:</strong> <?php echo date('Y-m-d H:i:s', strtotime($billPay['date'])); ?>
                                                </div>
                                            </div>
                                            <?php if (isset($billPay['fullTransaction']['description'])): ?>
                                                <div class="mt-3">
                                                    <strong>Full Description:</strong><br>
                                                    <code><?php echo htmlspecialchars($billPay['fullTransaction']['description']); ?></code>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>No BillPay Numbers Found</h5>
                    <p>No BillPay control numbers found in your account statement. Create some control numbers to get started.</p>
                    <div class="mt-3">
                        <a href="billpay_create.php" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-2"></i>Create Control Number
                        </a>
                        <a href="account_management.php" class="btn btn-outline-primary">
                            <i class="fas fa-wallet me-2"></i>View Account Statement
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterBillPayNumbers() {
            const statusFilter = document.getElementById('statusFilter').value;
            const entryFilter = document.getElementById('entryFilter').value;
            const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
            
            const cards = document.querySelectorAll('.billpay-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const status = card.dataset.status;
                const entry = card.dataset.entry;
                const search = card.dataset.search;
                
                const matchesStatus = !statusFilter || status === statusFilter;
                const matchesEntry = !entryFilter || entry === entryFilter;
                const matchesSearch = !searchFilter || search.includes(searchFilter);
                
                if (matchesStatus && matchesEntry && matchesSearch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show message if no results
            const noResultsMsg = document.getElementById('noResults');
            if (visibleCount === 0 && !noResultsMsg) {
                const msg = document.createElement('div');
                msg.id = 'noResults';
                msg.className = 'empty-state';
                msg.innerHTML = '<i class="fas fa-search"></i><h5>No Results Found</h5><p>No control numbers match your filters.</p>';
                document.getElementById('billpayList').appendChild(msg);
            } else if (visibleCount > 0 && noResultsMsg) {
                noResultsMsg.remove();
            }
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'position-fixed top-0 end-0 p-3';
                toast.style.zIndex = '1050';
                toast.innerHTML = '<div class="toast show" role="alert"><div class="toast-header"><strong class="me-auto">Copied!</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div><div class="toast-body">Control number copied to clipboard</div></div>';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            });
        }
        
        function shareBillPay(billPayNumber, description) {
            if (navigator.share) {
                navigator.share({
                    title: 'BillPay Control Number',
                    text: `Pay using BillPay number: ${billPayNumber}\n${description}`,
                    url: window.location.href
                });
            } else {
                // Fallback - copy to clipboard
                copyToClipboard(billPayNumber);
            }
        }
        
        function showDetails(index) {
            const detailsSection = document.getElementById('details-' + index);
            if (detailsSection.style.display === 'none') {
                detailsSection.style.display = 'block';
            } else {
                detailsSection.style.display = 'none';
            }
        }
        
        // Add event listeners
        document.getElementById('statusFilter').addEventListener('change', filterBillPayNumbers);
        document.getElementById('entryFilter').addEventListener('change', filterBillPayNumbers);
        document.getElementById('searchFilter').addEventListener('input', filterBillPayNumbers);
        
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
