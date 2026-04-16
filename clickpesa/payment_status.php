<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$paymentData = null;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['reference'])) {
    try {
        $orderReference = $_POST['order_reference'] ?? $_GET['reference'];
        
        if (empty($orderReference)) {
            throw new Exception('Order reference is required');
        }
        
        $paymentData = $api->queryPaymentStatus($orderReference);
        
        if (is_array($paymentData) && count($paymentData) > 0) {
            $success = 'Payment status retrieved successfully!';
        } else {
            throw new Exception('No payment found with this order reference');
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
    <title>Payment Status - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .status-container {
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
        .status-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
        }
        .status-badge {
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
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
        .payment-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .payment-detail:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
        }
        .detail-label i {
            margin-right: 8px;
            color: #667eea;
        }
        .detail-value {
            color: #333;
            font-weight: 500;
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
        .refresh-btn {
            background: linear-gradient(45deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            border: 2px solid white;
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
                <a class="nav-link active" href="payment_status.php">Check Status</a>
                <a class="nav-link" href="payment_history.php">History</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="status-container">
            <div class="header-section">
                <h1><i class="fas fa-search text-primary me-3"></i>Payment Status</h1>
                <p>Check the status of your payment transactions</p>
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

            <form method="POST" class="mb-4">
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-floating">
                            <input type="text" name="order_reference" class="form-control" id="order_reference" 
                                   placeholder="Order Reference" required 
                                   value="<?php echo isset($_POST['order_reference']) ? htmlspecialchars($_POST['order_reference']) : (isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : ''); ?>">
                            <label for="order_reference">Order Reference</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating">
                            <button type="submit" class="btn btn-primary w-100 h-100">
                                <i class="fas fa-search me-2"></i>Check Status
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <?php if ($paymentData && is_array($paymentData)): ?>
                <?php foreach ($paymentData as $payment): ?>
                    <div class="status-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-receipt text-primary me-2"></i>
                                Transaction: <?php echo htmlspecialchars($payment['id']); ?>
                            </h5>
                            <span class="status-badge status-<?php echo strtolower($payment['status']); ?>">
                                <?php echo htmlspecialchars($payment['status']); ?>
                            </span>
                        </div>

                        <div class="payment-detail">
                            <span class="detail-label">
                                <i class="fas fa-money-bill-wave"></i>Amount
                            </span>
                            <span class="detail-value">
                                <strong><?php echo number_format($payment['collectedAmount']); ?> <?php echo htmlspecialchars($payment['collectedCurrency']); ?></strong>
                            </span>
                        </div>

                        <div class="payment-detail">
                            <span class="detail-label">
                                <i class="fas fa-hashtag"></i>Order Reference
                            </span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment['orderReference']); ?></span>
                        </div>

                        <?php if (isset($payment['paymentReference'])): ?>
                        <div class="payment-detail">
                            <span class="detail-label">
                                <i class="fas fa-barcode"></i>Payment Reference
                            </span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment['paymentReference']); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($payment['paymentPhoneNumber'])): ?>
                        <div class="payment-detail">
                            <span class="detail-label">
                                <i class="fas fa-phone"></i>Phone Number
                            </span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment['paymentPhoneNumber']); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="payment-detail">
                            <span class="detail-label">
                                <i class="fas fa-info-circle"></i>Status Description
                            </span>
                            <span class="detail-value"><?php echo $api->getStatusDescription($payment['status']); ?></span>
                        </div>

                        <?php if (isset($payment['message'])): ?>
                        <div class="payment-detail">
                            <span class="detail-label">
                                <i class="fas fa-comment"></i>Message
                            </span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment['message']); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="timeline mt-4">
                            <div class="timeline-item">
                                <strong>Created:</strong> <?php echo date('Y-m-d H:i:s', strtotime($payment['createdAt'])); ?>
                            </div>
                            <?php if (isset($payment['updatedAt']) && $payment['updatedAt'] !== $payment['createdAt']): ?>
                            <div class="timeline-item">
                                <strong>Last Updated:</strong> <?php echo date('Y-m-d H:i:s', strtotime($payment['updatedAt'])); ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($payment['customer'])): ?>
                        <div class="mt-3 p-3 bg-light rounded">
                            <h6 class="mb-3"><i class="fas fa-user text-primary me-2"></i>Customer Information</h6>
                            <div class="row">
                                <?php if (!empty($payment['customer']['customerName'])): ?>
                                <div class="col-md-4">
                                    <small class="text-muted">Name:</small><br>
                                    <strong><?php echo htmlspecialchars($payment['customer']['customerName']); ?></strong>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($payment['customer']['customerPhoneNumber'])): ?>
                                <div class="col-md-4">
                                    <small class="text-muted">Phone:</small><br>
                                    <strong><?php echo htmlspecialchars($payment['customer']['customerPhoneNumber']); ?></strong>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($payment['customer']['customerEmail'])): ?>
                                <div class="col-md-4">
                                    <small class="text-muted">Email:</small><br>
                                    <strong><?php echo htmlspecialchars($payment['customer']['customerEmail']); ?></strong>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="mt-3 d-flex gap-2">
                            <button class="refresh-btn" onclick="refreshStatus('<?php echo htmlspecialchars($payment['orderReference']); ?>')">
                                <i class="fas fa-sync-alt me-2"></i>Refresh Status
                            </button>
                            <a href="payment_history.php?reference=<?php echo urlencode($payment['orderReference']); ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-history me-2"></i>View History
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!$paymentData && ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['reference']))): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search text-muted" style="font-size: 48px;"></i>
                    <h5 class="mt-3 text-muted">No Payment Found</h5>
                    <p class="text-muted">No payment records found for the provided order reference.</p>
                </div>
            <?php endif; ?>

            <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['reference'])): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search text-muted" style="font-size: 48px;"></i>
                    <h5 class="mt-3 text-muted">Check Payment Status</h5>
                    <p class="text-muted">Enter an order reference to check the status of your payment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshStatus(orderReference) {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
            btn.disabled = true;
            
            setTimeout(() => {
                window.location.href = 'payment_status.php?reference=' + encodeURIComponent(orderReference);
            }, 1000);
        }

        // Auto-refresh for processing payments
        <?php if (isset($paymentData) && is_array($paymentData)): ?>
            <?php foreach ($paymentData as $payment): ?>
                <?php if ($payment['status'] === 'PROCESSING'): ?>
                    setTimeout(() => {
                        window.location.href = 'payment_status.php?reference=<?php echo urlencode($payment['orderReference']); ?>';
                    }, 30000); // Refresh every 30 seconds
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

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
