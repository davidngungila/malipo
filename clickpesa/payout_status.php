<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$payoutData = null;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['reference'])) {
    try {
        $payoutReference = $_POST['payout_reference'] ?? $_GET['reference'];
        
        if (empty($payoutReference)) {
            throw new Exception('Payout reference is required');
        }
        
        $payoutData = $api->queryPayoutStatus($payoutReference);
        
        if ($payoutData && isset($payoutData['id'])) {
            $success = 'Payout status retrieved successfully!';
        } else {
            throw new Exception('No payout found with this reference');
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
    <title>Payout Status - ClickPesa</title>
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
        .payout-type-badge {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 15px;
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
                <a class="nav-link active" href="payout_status.php">Payout Status</a>
                <a class="nav-link" href="payment_status.php">Payment Status</a>
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
                <h1><i class="fas fa-search text-primary me-3"></i>Payout Status</h1>
                <p>Check the status of your payout transactions</p>
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
                            <input type="text" name="payout_reference" class="form-control" id="payout_reference" 
                                   placeholder="Payout Reference" required 
                                   value="<?php echo isset($_POST['payout_reference']) ? htmlspecialchars($_POST['payout_reference']) : (isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : ''); ?>">
                            <label for="payout_reference">Payout Reference</label>
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

            <?php if ($payoutData): ?>
                <div class="status-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-hand-holding-usd text-primary me-2"></i>
                            Payout: <?php echo htmlspecialchars($payoutData['id']); ?>
                        </h5>
                        <span class="status-badge status-<?php echo strtolower($payoutData['status']); ?>">
                            <?php echo htmlspecialchars($payoutData['status']); ?>
                        </span>
                    </div>

                    <div class="payment-detail">
                        <span class="detail-label">
                            <i class="fas fa-money-bill-wave"></i>Amount
                        </span>
                        <span class="detail-value">
                            <strong><?php echo number_format($payoutData['amount']); ?> <?php echo htmlspecialchars($payoutData['currency']); ?></strong>
                        </span>
                    </div>

                    <div class="payment-detail">
                        <span class="detail-label">
                            <i class="fas fa-hashtag"></i>Reference
                        </span>
                        <span class="detail-value"><?php echo htmlspecialchars($payoutData['reference']); ?></span>
                    </div>

                    <?php if (isset($payoutData['payoutReference'])): ?>
                    <div class="payment-detail">
                        <span class="detail-label">
                            <i class="fas fa-barcode"></i>Payout Reference
                        </span>
                        <span class="detail-value"><?php echo htmlspecialchars($payoutData['payoutReference']); ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="payment-detail">
                        <span class="detail-label">
                            <i class="fas fa-info-circle"></i>Status Description
                        </span>
                        <span class="detail-value"><?php echo $api->getStatusDescription($payoutData['status']); ?></span>
                    </div>

                    <?php if (isset($payoutData['message'])): ?>
                    <div class="payment-detail">
                        <span class="detail-label">
                            <i class="fas fa-comment"></i>Message
                        </span>
                        <span class="detail-value"><?php echo htmlspecialchars($payoutData['message']); ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="payment-detail">
                        <span class="detail-label">
                            <i class="fas fa-tag"></i>Payout Type
                        </span>
                        <span class="detail-value">
                            <?php 
                            $payoutType = $payoutData['type'] ?? 'mobile_money';
                            $typeLabel = $payoutType === 'bank' ? 'Bank Transfer' : 'Mobile Money';
                            $typeClass = $payoutType === 'bank' ? 'payout-bank' : 'payout-mobile';
                            echo "<span class='payout-type-badge {$typeClass}'>{$typeLabel}</span>";
                            ?>
                        </span>
                    </div>

                    <div class="timeline mt-4">
                        <div class="timeline-item">
                            <strong>Created:</strong> <?php echo date('Y-m-d H:i:s', strtotime($payoutData['createdAt'])); ?>
                        </div>
                        <?php if (isset($payoutData['updatedAt']) && $payoutData['updatedAt'] !== $payoutData['createdAt']): ?>
                        <div class="timeline-item">
                            <strong>Last Updated:</strong> <?php echo date('Y-m-d H:i:s', strtotime($payoutData['updatedAt'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($payoutData['recipient'])): ?>
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="fas fa-user text-primary me-2"></i>Recipient Information</h6>
                        <div class="row">
                            <?php if (!empty($payoutData['recipient']['name'])): ?>
                            <div class="col-md-4">
                                <small class="text-muted">Name:</small><br>
                                <strong><?php echo htmlspecialchars($payoutData['recipient']['name']); ?></strong>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($payoutData['recipient']['phoneNumber'])): ?>
                            <div class="col-md-4">
                                <small class="text-muted">Phone:</small><br>
                                <strong><?php echo htmlspecialchars($payoutData['recipient']['phoneNumber']); ?></strong>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($payoutData['recipient']['bankAccount'])): ?>
                            <div class="col-md-4">
                                <small class="text-muted">Bank Account:</small><br>
                                <strong><?php echo htmlspecialchars($payoutData['recipient']['bankAccount']); ?></strong>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($payoutData['recipient']['bankName'])): ?>
                            <div class="col-md-4">
                                <small class="text-muted">Bank:</small><br>
                                <strong><?php echo htmlspecialchars($payoutData['recipient']['bankName']); ?></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mt-3 d-flex gap-2">
                        <button class="refresh-btn" onclick="refreshStatus('<?php echo htmlspecialchars($payoutData['reference']); ?>')">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Status
                        </button>
                        <a href="payout_history.php?reference=<?php echo urlencode($payoutData['reference']); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-history me-2"></i>View History
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$payoutData && ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['reference']))): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search text-muted" style="font-size: 48px;"></i>
                    <h5 class="mt-3 text-muted">No Payout Found</h5>
                    <p class="text-muted">No payout records found for the provided reference.</p>
                </div>
            <?php endif; ?>

            <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['reference'])): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search text-muted" style="font-size: 48px;"></i>
                    <h5 class="mt-3 text-muted">Check Payout Status</h5>
                    <p class="text-muted">Enter a payout reference to check the status of your payout.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshStatus(payoutReference) {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
            btn.disabled = true;
            
            setTimeout(() => {
                window.location.href = 'payout_status.php?reference=' + encodeURIComponent(payoutReference);
            }, 1000);
        }

        // Auto-refresh for processing payouts
        <?php if (isset($payoutData) && isset($payoutData['status'])): ?>
            <?php if ($payoutData['status'] === 'PROCESSING'): ?>
                setTimeout(() => {
                    window.location.href = 'payout_status.php?reference=<?php echo urlencode($payoutData['reference']); ?>';
                }, 30000); // Refresh every 30 seconds
            <?php endif; ?>
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
