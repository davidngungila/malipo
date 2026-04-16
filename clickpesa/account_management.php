<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$balanceData = null;
$statementData = null;
$error = '';
$success = '';

// Get account balance
try {
    $balanceData = $api->getAccountBalance();
    if (isset($balanceData[0])) {
        $success = 'Account balance retrieved successfully!';
    }
} catch (Exception $e) {
    $error = 'Failed to retrieve account balance: ' . $e->getMessage();
}

// Get account statement if requested
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['get_statement']) || isset($_GET['currency']) || isset($_GET['start_date']) || isset($_GET['end_date']))) {
    try {
        $currency = $_GET['currency'] ?? 'TZS';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        
        $statementData = $api->getAccountStatement($currency, $startDate, $endDate);
        $success = 'Account statement retrieved successfully!';
        
    } catch (Exception $e) {
        $error = 'Failed to retrieve account statement: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .account-container {
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
        .balance-card {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            text-align: center;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
            margin-bottom: 30px;
        }
        .balance-amount {
            font-size: 3rem;
            font-weight: 700;
            margin: 20px 0;
        }
        .balance-currency {
            font-size: 1.5rem;
            opacity: 0.9;
        }
        .statement-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
        .transaction-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f1f3f5;
        }
        .transaction-row:last-child {
            border-bottom: none;
        }
        .transaction-type {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 15px;
        }
        .transaction-credit {
            background-color: #d4edda;
            color: #155724;
        }
        .transaction-debit {
            background-color: #f8d7da;
            color: #721c24;
        }
        .amount-positive {
            color: #28a745;
            font-weight: 600;
        }
        .amount-negative {
            color: #dc3545;
            font-weight: 600;
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
                <a class="nav-link" href="account_management.php" class="active">Account</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="account-container">
            <div class="header-section">
                <h1><i class="fas fa-wallet text-primary me-3"></i>Account Management</h1>
                <p>View your account balance and transaction statement</p>
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

            <!-- Balance Section -->
            <?php if ($balanceData && isset($balanceData[0])): ?>
                <div class="balance-card">
                    <h4><i class="fas fa-balance-scale me-2"></i>Account Balance</h4>
                    <div class="balance-amount">
                        <?php echo number_format($balanceData[0]['balance']); ?>
                        <span class="balance-currency"><?php echo htmlspecialchars($balanceData[0]['currency']); ?></span>
                    </div>
                    <p class="mb-0">Available Balance</p>
                </div>
            <?php endif; ?>

            <!-- Statement Filter Form -->
            <div class="statement-card">
                <h5><i class="fas fa-filter text-primary me-2"></i>Account Statement Filters</h5>
                <form method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="currency" class="form-control" id="currency">
                                    <option value="TZS" <?php echo (isset($_GET['currency']) && $_GET['currency'] === 'TZS') ? 'selected' : ''; ?>>TZS</option>
                                    <option value="USD" <?php echo (isset($_GET['currency']) && $_GET['currency'] === 'USD') ? 'selected' : ''; ?>>USD</option>
                                </select>
                                <label for="currency">Currency</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="start_date" class="form-control" id="start_date" 
                                       value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
                                <label for="start_date">Start Date</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="end_date" class="form-control" id="end_date" 
                                       value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">
                                <label for="end_date">End Date</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <button type="submit" name="get_statement" value="1" class="btn btn-primary w-100 h-100">
                                    <i class="fas fa-search me-2"></i>Get Statement
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Account Statement -->
            <?php if ($statementData): ?>
                <?php if (isset($statementData['accountDetails'])): ?>
                    <!-- Account Summary -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3><?php echo number_format($statementData['accountDetails']['openingBalance'] ?? 0); ?></h3>
                            <p>Opening Balance</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo number_format($statementData['accountDetails']['totalCredits'] ?? 0); ?></h3>
                            <p>Total Credits</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo number_format($statementData['accountDetails']['totalDebits'] ?? 0); ?></h3>
                            <p>Total Debits</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo number_format($statementData['accountDetails']['closingBalance'] ?? 0); ?></h3>
                            <p>Closing Balance</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($statementData['transactions']) && !empty($statementData['transactions'])): ?>
                    <!-- Transactions List -->
                    <div class="statement-card">
                        <h5><i class="fas fa-list text-primary me-2"></i>Transaction History</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($statementData['transactions'] as $transaction): ?>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i', strtotime($transaction['date'])); ?></td>
                                            <td><?php echo htmlspecialchars($transaction['description'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="transaction-type <?php echo strtolower($transaction['entry'] ?? 'unknown'); ?>">
                                                    <?php echo htmlspecialchars($transaction['entry'] ?? 'Unknown'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <code><?php echo htmlspecialchars($transaction['orderReference'] ?? $transaction['id'] ?? 'N/A'); ?></code>
                                            </td>
                                            <td class="<?php echo strtolower($transaction['entry'] ?? 'debit') === 'credit' ? 'amount-positive' : 'amount-negative'; ?>">
                                                <?php echo strtolower($transaction['entry'] ?? 'debit') === 'credit' ? '+' : '-'; ?>
                                                <?php echo number_format($transaction['amount'] ?? 0); ?> <?php echo htmlspecialchars($transaction['currency'] ?? 'TZS'); ?>
                                            </td>
                                            <td><?php echo number_format($transaction['balance'] ?? 0); ?> <?php echo htmlspecialchars($transaction['currency'] ?? 'TZS'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No Transactions Found</h5>
                        <p>No transactions found for the selected criteria.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!$statementData && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_statement'])): ?>
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h5>Statement Not Available</h5>
                    <p>Unable to retrieve account statement. Please try again.</p>
                </div>
            <?php endif; ?>

            <?php if (!$statementData && $_SERVER['REQUEST_METHOD'] !== 'GET'): ?>
                <div class="empty-state">
                    <i class="fas fa-file-invoice"></i>
                    <h5>Account Statement</h5>
                    <p>Use the filters above to retrieve your account statement.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
