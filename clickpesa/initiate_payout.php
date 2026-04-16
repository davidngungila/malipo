<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$success = '';
$error = '';
$payoutData = null;
$banksList = [];

// Get banks list for bank payouts
try {
    $banksResponse = $api->getBanksList();
    if (isset($banksResponse['data'])) {
        $banksList = $banksResponse['data'];
    }
} catch (Exception $e) {
    // Banks list not critical, continue without it
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $payoutType = $_POST['payout_type'];
        $amount = $api->formatAmount($_POST['amount']);
        $currency = $_POST['currency'];
        $reference = $api->generateOrderReference('PY');
        $narrative = $_POST['narrative'] ?? '';
        
        if ($payoutType === 'mobile_money') {
            $phoneNumber = $api->validatePhoneNumber($_POST['phone_number']);
            
            if (!$phoneNumber) {
                throw new Exception('Invalid phone number. Please use format: 255712345678');
            }
            
            // Preview mobile money payout
            $preview = $api->previewMobileMoneyPayout($amount, $phoneNumber, $currency, $reference);
            
            if (isset($preview['amount']) || isset($preview['balance'])) {
                // Create mobile money payout
                $payout = $api->createMobileMoneyPayout($amount, $phoneNumber, $currency, $reference);
                $payoutData = $payout;
                $success = 'Mobile Money payout initiated successfully! Order Reference: ' . $reference;
            } else {
                throw new Exception('Mobile money payout preview failed: ' . ($preview['message'] ?? 'Unknown error'));
            }
            
        } elseif ($payoutType === 'bank') {
            $bankAccount = $_POST['bank_account'];
            $bankCode = $_POST['bank_code'];
            $accountName = $_POST['account_name'];
            
            if (empty($bankAccount) || empty($bankCode) || empty($accountName)) {
                throw new Exception('All bank details are required');
            }
            
            // Preview bank payout
            $preview = $api->previewBankPayout($amount, $currency, $bankAccount, $bankCode, $accountName, $reference);
            
            if (isset($preview['amount']) || isset($preview['balance'])) {
                // Create bank payout
                $payout = $api->createBankPayout($amount, $currency, $bankAccount, $bankCode, $accountName, $reference);
                $payoutData = $payout;
                $success = 'Bank payout initiated successfully! Order Reference: ' . $reference;
            } else {
                throw new Exception('Bank payout preview failed: ' . ($preview['message'] ?? 'Unknown error'));
            }
        } else {
            throw new Exception('Invalid payout type selected');
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initiate Payout - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payout-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 50px auto;
            max-width: 800px;
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
        .payout-type-selector {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .payout-type-btn {
            flex: 1;
            padding: 20px;
            border: 2px solid #dee2e6;
            border-radius: 15px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .payout-type-btn:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        .payout-type-btn.active {
            border-color: #667eea;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .payout-type-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        .form-section {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .form-section.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
        .payout-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .payout-info h5 {
            color: #333;
            margin-bottom: 15px;
        }
        .payout-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .payout-detail:last-child {
            border-bottom: none;
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
                <a class="nav-link active" href="initiate_payout.php">Payouts</a>
                <a class="nav-link" href="payment_status.php">Check Status</a>
                <a class="nav-link" href="payment_history.php">History</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="payout-container">
            <div class="header-section">
                <h1><i class="fas fa-hand-holding-usd text-primary me-3"></i>Initiate Payout</h1>
                <p>Send money to mobile money or bank accounts</p>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Payout Type Selection -->
            <div class="payout-type-selector">
                <div class="payout-type-btn active" onclick="selectPayoutType('mobile_money')">
                    <i class="fas fa-mobile-alt"></i>
                    <strong>Mobile Money</strong>
                    <small class="d-block mt-1">Send to mobile wallets</small>
                </div>
                <div class="payout-type-btn" onclick="selectPayoutType('bank')">
                    <i class="fas fa-university"></i>
                    <strong>Bank Transfer</strong>
                    <small class="d-block mt-1">Send to bank accounts</small>
                </div>
            </div>

            <form method="POST" id="payoutForm">
                <input type="hidden" name="payout_type" id="payoutType" value="mobile_money">
                
                <!-- Common Fields -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="amount" class="form-control" id="amount" 
                                   placeholder="Amount" required min="100" max="10000000" 
                                   value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : '1000'; ?>">
                            <label for="amount">Amount (TZS)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="currency" class="form-control" id="currency" required>
                                <option value="TZS" <?php echo (!isset($_POST['currency']) || $_POST['currency'] === 'TZS') ? 'selected' : ''; ?>>TZS</option>
                                <option value="USD" <?php echo (isset($_POST['currency']) && $_POST['currency'] === 'USD') ? 'selected' : ''; ?>>USD</option>
                            </select>
                            <label for="currency">Currency</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="narrative" class="form-control" id="narrative" 
                           placeholder="Narrative" 
                           value="<?php echo isset($_POST['narrative']) ? htmlspecialchars($_POST['narrative']) : ''; ?>">
                    <label for="narrative">Narrative (Optional)</label>
                </div>

                <!-- Mobile Money Section -->
                <div id="mobileMoneySection" class="form-section active">
                    <h5><i class="fas fa-mobile-alt text-primary me-2"></i>Mobile Money Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="tel" name="phone_number" class="form-control" id="phone_number" 
                                       placeholder="Phone Number" required 
                                       pattern="255[67][0-9]{8}" 
                                       value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
                                <label for="phone_number">Phone Number</label>
                                <div class="form-text">Format: 255712345678 (Tanzania mobile money)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Transfer Section -->
                <div id="bankSection" class="form-section">
                    <h5><i class="fas fa-university text-primary me-2"></i>Bank Transfer Details</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="bank_code" class="form-control" id="bank_code" 
                                       placeholder="Bank BIC" required 
                                       value="<?php echo isset($_POST['bank_code']) ? htmlspecialchars($_POST['bank_code']) : ''; ?>">
                                <label for="bank_code">Bank BIC</label>
                                <div class="form-text">Bank Business Identifier Code (BIC)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="bank_account" class="form-control" id="bank_account" 
                                       placeholder="Account Number" required 
                                       value="<?php echo isset($_POST['bank_account']) ? htmlspecialchars($_POST['bank_account']) : ''; ?>">
                                <label for="bank_account">Account Number</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="account_name" class="form-control" id="account_name" 
                               placeholder="Account Name" required 
                               value="<?php echo isset($_POST['account_name']) ? htmlspecialchars($_POST['account_name']) : ''; ?>">
                        <label for="account_name">Account Name</label>
                    </div>
                    
                    <?php if (!empty($banksList)): ?>
                        <div class="alert alert-info">
                            <strong>Available Banks:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($banksList as $bank): ?>
                                    <li><?php echo htmlspecialchars($bank['name'] ?? 'Unknown'); ?> (<?php echo htmlspecialchars($bank['bic'] ?? 'N/A'); ?>)</li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Initiate Payout
                    </button>
                </div>
            </form>

            <?php if ($payoutData): ?>
                <div class="payout-info">
                    <h5><i class="fas fa-info-circle text-primary me-2"></i>Payout Details</h5>
                    <div class="payout-detail">
                        <span>Payout ID:</span>
                        <strong><?php echo htmlspecialchars($payoutData['id'] ?? 'N/A'); ?></strong>
                    </div>
                    <div class="payout-detail">
                        <span>Status:</span>
                        <strong><?php echo htmlspecialchars($payoutData['status'] ?? 'PROCESSING'); ?></strong>
                    </div>
                    <div class="payout-detail">
                        <span>Amount:</span>
                        <strong><?php echo htmlspecialchars($payoutData['amount'] ?? $_POST['amount']); ?> <?php echo htmlspecialchars($_POST['currency']); ?></strong>
                    </div>
                    <div class="payout-detail">
                        <span>Reference:</span>
                        <strong><?php echo htmlspecialchars($payoutData['reference'] ?? 'N/A'); ?></strong>
                    </div>
                    <div class="payout-detail">
                        <span>Created:</span>
                        <strong><?php echo date('Y-m-d H:i:s'); ?></strong>
                    </div>
                    <div class="mt-3">
                        <a href="payout_status.php?reference=<?php echo urlencode($payoutData['reference'] ?? ''); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search me-2"></i>Check Status
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Secured by ClickPesa Payout Gateway
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPayoutType(type) {
            // Update buttons
            document.querySelectorAll('.payout-type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.payout-type-btn').classList.add('active');
            
            // Update hidden field
            document.getElementById('payoutType').value = type;
            
            // Show/hide sections
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });
            
            if (type === 'mobile_money') {
                document.getElementById('mobileMoneySection').classList.add('active');
            } else if (type === 'bank') {
                document.getElementById('bankSection').classList.add('active');
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

        // Format phone number input
        document.getElementById('phone_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0 && !value.startsWith('255')) {
                value = '255' + value;
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
