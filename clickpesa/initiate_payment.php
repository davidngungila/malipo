<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$success = '';
$error = '';
$paymentData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $amount = $api->formatAmount($_POST['amount']);
        $phoneNumber = $api->validatePhoneNumber($_POST['phone_number']);
        $orderReference = $api->generateOrderReference();
        
        if (!$phoneNumber) {
            throw new Exception('Invalid phone number. Please use format: 255712345678');
        }
        
        if ($amount < $config['clickpesa']['payment']['min_amount'] || $amount > $config['clickpesa']['payment']['max_amount']) {
            throw new Exception('Amount must be between ' . $config['clickpesa']['payment']['min_amount'] . ' and ' . $config['clickpesa']['payment']['max_amount'] . ' TZS');
        }
        
        // Preview the payment first
        $preview = $api->previewUSSDPush($amount, $orderReference, $phoneNumber, true);
        
        if (isset($preview['activeMethods']) && !empty($preview['activeMethods'])) {
            // Initiate the payment
            $payment = $api->initiateUSSDPush($amount, $orderReference, $phoneNumber);
            $paymentData = $payment;
            $success = 'Payment initiated successfully! USSD Push sent to ' . $phoneNumber . '. Transaction ID: ' . $payment['id'];
        } else {
            throw new Exception('No active payment methods available for this phone number');
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
    <title>Initiate Payment - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payment-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 50px auto;
            max-width: 600px;
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
        .form-floating label {
            color: #666;
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
        .payment-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .payment-info h5 {
            color: #333;
            margin-bottom: 15px;
        }
        .payment-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .payment-detail:last-child {
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
                <a class="nav-link active" href="initiate_payment.php">Initiate Payment</a>
                <a class="nav-link" href="payment_status.php">Check Status</a>
                <a class="nav-link" href="payment_history.php">History</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="payment-container">
            <div class="header-section">
                <h1><i class="fas fa-mobile-alt text-primary me-3"></i>Initiate USSD Payment</h1>
                <p>Send a USSD Push request to collect payment from mobile money</p>
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

            <?php if ($paymentData): ?>
                <div class="payment-info">
                    <h5><i class="fas fa-info-circle text-primary me-2"></i>Payment Details</h5>
                    <div class="payment-detail">
                        <span>Transaction ID:</span>
                        <strong><?php echo htmlspecialchars($paymentData['id']); ?></strong>
                    </div>
                    <div class="payment-detail">
                        <span>Status:</span>
                        <strong><?php echo htmlspecialchars($paymentData['status']); ?></strong>
                    </div>
                    <div class="payment-detail">
                        <span>Amount:</span>
                        <strong><?php echo htmlspecialchars($paymentData['collectedAmount']); ?> <?php echo htmlspecialchars($paymentData['collectedCurrency']); ?></strong>
                    </div>
                    <div class="payment-detail">
                        <span>Order Reference:</span>
                        <strong><?php echo htmlspecialchars($paymentData['orderReference']); ?></strong>
                    </div>
                    <div class="payment-detail">
                        <span>Created:</span>
                        <strong><?php echo date('Y-m-d H:i:s', strtotime($paymentData['createdAt'])); ?></strong>
                    </div>
                    <div class="mt-3">
                        <a href="payment_status.php?reference=<?php echo urlencode($paymentData['orderReference']); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search me-2"></i>Check Status
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="mt-4">
                <div class="form-floating mb-3">
                    <input type="number" name="amount" class="form-control" id="amount" 
                           placeholder="Amount" required min="<?php echo $config['clickpesa']['payment']['min_amount']; ?>" 
                           max="<?php echo $config['clickpesa']['payment']['max_amount']; ?>" 
                           value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : $config['clickpesa']['payment']['default_amount']; ?>">
                    <label for="amount">Amount (TZS)</label>
                    <div class="form-text">Min: <?php echo $config['clickpesa']['payment']['min_amount']; ?>, Max: <?php echo $config['clickpesa']['payment']['max_amount']; ?></div>
                </div>

                <div class="form-floating mb-3">
                    <input type="tel" name="phone_number" class="form-control" id="phone_number" 
                           placeholder="Phone Number" required 
                           pattern="255[67][0-9]{8}" 
                           value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
                    <label for="phone_number">Phone Number</label>
                    <div class="form-text">Format: 255712345678 (Tanzania numbers only)</div>
                </div>

                <div class="form-floating mb-4">
                    <input type="text" name="description" class="form-control" id="description" 
                           placeholder="Description" 
                           value="<?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?>">
                    <label for="description">Description (Optional)</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Send USSD Push
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Secured by ClickPesa Payment Gateway
                </small>
            </div>
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
