<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$billPayData = null;
$billPayList = [];
$error = '';
$success = '';
$apiStatus = 'connected';

// Test API connectivity first
try {
    // Test token generation
    $testToken = $api->getValidToken();
    if (!$testToken) {
        throw new Exception('API authentication failed - unable to generate token');
    }
    
    // Test basic API call
    $testCall = $api->getAccountStatement('TZS');
    if (!isset($testCall)) {
        throw new Exception('API connection test failed');
    }
    
    $apiStatus = 'connected';
    
    // Get account statement to find BillPay transactions for listing
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
                $billPayList[] = [
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
        
        $success = 'Found ' . count($billPayList) . ' BillPay control numbers from live system';
        
    } else {
        throw new Exception('No transaction data available');
    }
    
} catch (Exception $e) {
    $error = 'API Connection Error: ' . $e->getMessage();
    $apiStatus = 'disconnected';
    
    // Don't add sample data - show real error instead
    $billPayList = [];
}

// Handle POST requests for BillPay operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['billPayNumber'])) {
    try {
        $billPayNumber = $_POST['billPayNumber'] ?? $_GET['billPayNumber'];
        
        if (empty($billPayNumber)) {
            throw new Exception('BillPay number is required');
        }
        
        // Re-check API connectivity before operations
        $tokenCheck = $api->getValidToken();
        if (!$tokenCheck) {
            throw new Exception('API connection lost - please refresh and try again');
        }
        
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            // Update BillPay reference with enhanced error handling
            $billAmount = !empty($_POST['billAmount']) ? $api->formatAmount($_POST['billAmount']) : null;
            $billDescription = !empty($_POST['billDescription']) ? $_POST['billDescription'] : null;
            $billPaymentMode = !empty($_POST['billPaymentMode']) ? $_POST['billPaymentMode'] : null;
            $billStatus = !empty($_POST['billStatus']) ? $_POST['billStatus'] : null;
            
            error_log("Updating BillPay Number: $billPayNumber with data: " . json_encode([
                'billAmount' => $billAmount,
                'billDescription' => $billDescription,
                'billPaymentMode' => $billPaymentMode,
                'billStatus' => $billStatus
            ]));
            
            $result = $api->updateBillPayReference($billPayNumber, $billAmount, $billDescription, $billPaymentMode, $billStatus);
            
            if ($result && isset($result['billPayNumber'])) {
                $billPayData = $result;
                $success = 'BillPay number updated successfully!';
                error_log("BillPay Update Success: " . json_encode($result));
            } else {
                throw new Exception('Failed to update BillPay number - API returned invalid response');
            }
            
        } else {
            // Query BillPay number details with enhanced error handling
            error_log("Querying BillPay Number: $billPayNumber");
            
            $billPayData = $api->queryBillPayNumber($billPayNumber);
            
            if (isset($billPayData['billPayNumber'])) {
                $success = 'BillPay number details retrieved successfully!';
                error_log("BillPay Query Success: " . json_encode($billPayData));
            } else {
                // Check if it's a sample FEEDTANPAY number
                if (strpos($billPayNumber, 'FEEDTANPAY') === 0) {
                    throw new Exception('This appears to be a sample FEEDTANPAY control number. Please create a real BillPay control number first.');
                } else {
                    throw new Exception('No BillPay number found with the provided number: ' . htmlspecialchars($billPayNumber));
                }
            }
        }
        
    } catch (Exception $e) {
        $error = 'Operation Failed: ' . $e->getMessage();
        error_log("BillPay Operation Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BillPay Management - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .manage-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 30px auto;
            max-width: 900px;
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
        .api-status {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .api-status.connected {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .api-status.disconnected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .api-status i {
            margin-right: 8px;
        }
        .billpay-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
        }
        .billpay-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
            font-family: monospace;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #667eea;
            text-align: center;
            margin: 15px 0;
        }
        .status-badge {
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
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
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f5;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .edit-form {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
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
                <a class="nav-link" href="billpay_manage.php" class="active">BillPay Manage</a>
                <a class="nav-link" href="account_management.php">Account</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="manage-container">
            <div class="header-section">
                <h1><i class="fas fa-cog text-primary me-3"></i>BillPay Management</h1>
                <p>View and manage BillPay control numbers</p>
                
                <!-- API Connectivity Status -->
                <div class="api-status <?php echo $apiStatus; ?>">
                    <i class="fas <?php echo $apiStatus === 'connected' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                    API Status: <?php echo ucfirst($apiStatus); ?> - Direct ClickPesa Connection
                </div>
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

            <!-- BillPay List Section -->
            <?php if (!empty($billPayList)): ?>
                <div class="billpay-card">
                    <h5><i class="fas fa-list text-primary me-2"></i>All BillPay Control Numbers</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Control Number</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($billPayList as $billPay): ?>
                                    <tr>
                                        <td>
                                            <span class="billpay-number" style="font-size: 0.9rem; padding: 8px;">
                                                <?php echo htmlspecialchars($billPay['billPayNumber']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($billPay['description']); ?></td>
                                        <td><?php echo number_format($billPay['amount']); ?> <?php echo htmlspecialchars($billPay['currency']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($billPay['status']); ?>">
                                                <?php echo htmlspecialchars($billPay['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($billPay['date'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?billPayNumber=<?php echo urlencode($billPay['billPayNumber']); ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="copyToClipboard('<?php echo htmlspecialchars($billPay['billPayNumber']); ?>')" 
                                                        title="Copy Number">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>No BillPay Numbers Found</h5>
                    <p>No BillPay control numbers have been created yet. Create your first BillPay control number to get started.</p>
                    <div class="mt-3">
                        <a href="billpay_create.php" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-2"></i>Create Control Number
                        </a>
                        <a href="billpay_list.php" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>View All Control Numbers
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Search Form for Specific BillPay -->
            <div class="billpay-card mt-4">
                <h5><i class="fas fa-search text-primary me-2"></i>Search Specific BillPay Number</h5>
                <form method="GET">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-floating">
                                <input type="text" name="billPayNumber" class="form-control" id="billPayNumber" 
                                       placeholder="Enter BillPay Number for detailed management" 
                                       value="<?php echo isset($_GET['billPayNumber']) ? htmlspecialchars($_GET['billPayNumber']) : ''; ?>">
                                <label for="billPayNumber">BillPay Number</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <button type="submit" class="btn btn-primary w-100 h-100">
                                    <i class="fas fa-search me-2"></i>Search & Manage
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <?php if ($billPayData): ?>
                <div class="billpay-card">
                    <div class="text-center mb-4">
                        <h5><i class="fas fa-file-invoice-dollar text-primary me-2"></i>BillPay Control Number</h5>
                        <div class="billpay-number"><?php echo htmlspecialchars($billPayData['billPayNumber']); ?></div>
                        
                        <?php 
                        $status = $billPayData['billStatus'] ?? 'ACTIVE';
                        $statusClass = $status === 'ACTIVE' ? 'status-active' : 'status-inactive';
                        ?>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-row">
                                <span class="detail-label">Description:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($billPayData['billDescription'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Amount:</span>
                                <span class="detail-value">
                                    <?php echo $billPayData['billAmount'] ? number_format($billPayData['billAmount']) . ' TZS' : 'Not specified'; ?>
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Payment Mode:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($billPayData['billPaymentMode'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php if (isset($billPayData['billCustomerName'])): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Customer:</span>
                                    <span class="detail-value"><?php echo htmlspecialchars($billPayData['billCustomerName']); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Type:</span>
                                <span class="detail-value">
                                    <?php echo isset($billPayData['billCustomerName']) ? 'Customer Control' : 'Order Control'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div class="edit-form">
                        <h6><i class="fas fa-edit text-primary me-2"></i>Update BillPay Number</h6>
                        <form method="POST">
                            <input type="hidden" name="billPayNumber" value="<?php echo htmlspecialchars($billPayData['billPayNumber']); ?>">
                            <input type="hidden" name="action" value="update">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="billDescription" class="form-control" id="editDescription" 
                                               placeholder="Description" 
                                               value="<?php echo htmlspecialchars($billPayData['billDescription'] ?? ''); ?>">
                                        <label for="editDescription">Description</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" name="billAmount" class="form-control" id="editAmount" 
                                               placeholder="Amount" step="0.01" min="0"
                                               value="<?php echo $billPayData['billAmount'] ?? ''; ?>">
                                        <label for="editAmount">Amount (TZS)</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select name="billPaymentMode" class="form-control" id="editPaymentMode">
                                            <option value="ALLOW_PARTIAL_AND_OVER_PAYMENT" <?php echo (isset($billPayData['billPaymentMode']) && $billPayData['billPaymentMode'] === 'ALLOW_PARTIAL_AND_OVER_PAYMENT') ? 'selected' : ''; ?>>Allow Partial & Over Payment</option>
                                            <option value="EXACT" <?php echo (isset($billPayData['billPaymentMode']) && $billPayData['billPaymentMode'] === 'EXACT') ? 'selected' : ''; ?>>Exact Amount Only</option>
                                        </select>
                                        <label for="editPaymentMode">Payment Mode</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select name="billStatus" class="form-control" id="editStatus">
                                            <option value="ACTIVE" <?php echo ($status === 'ACTIVE') ? 'selected' : ''; ?>>Active</option>
                                            <option value="INACTIVE" <?php echo ($status === 'INACTIVE') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                        <label for="editStatus">Status</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update BillPay
                                </button>
                                <a href="billpay_create.php" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i>Create New
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$billPayData && ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['billPayNumber']))): ?>
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h5>No BillPay Number Found</h5>
                    <p>No BillPay number found with the provided number.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
