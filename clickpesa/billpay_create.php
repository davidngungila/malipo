<?php
require_once 'config.php';
require_once 'ClickPesaAPI.php';

$config = include 'config.php';
$api = new ClickPesaAPI($config);

$success = '';
$error = '';
$billPayData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $billType = $_POST['bill_type'];
        
        if ($billType === 'order') {
            // Create Order Control Number
            $billDescription = $_POST['bill_description'];
            $billAmount = !empty($_POST['bill_amount']) ? $api->formatAmount($_POST['bill_amount']) : null;
            $billReference = !empty($_POST['bill_reference']) ? $_POST['bill_reference'] : $api->generateFeedtanPayControlNumber();
            $billPaymentMode = $_POST['bill_payment_mode'] ?? 'ALLOW_PARTIAL_AND_OVER_PAYMENT';
            
            $result = $api->createOrderControlNumber($billDescription, $billAmount, $billReference, $billPaymentMode);
            $billPayData = $result;
            $success = 'Order Control Number created successfully!';
            
        } elseif ($billType === 'customer') {
            // Create Customer Control Number
            $customerName = $_POST['customer_name'];
            $customerEmail = !empty($_POST['customer_email']) ? $_POST['customer_email'] : null;
            $customerPhone = !empty($_POST['customer_phone']) ? $api->validatePhoneNumber($_POST['customer_phone']) : null;
            $billDescription = !empty($_POST['bill_description']) ? $_POST['bill_description'] : null;
            $billAmount = !empty($_POST['bill_amount']) ? $api->formatAmount($_POST['bill_amount']) : null;
            $billReference = !empty($_POST['bill_reference']) ? $_POST['bill_reference'] : $api->generateFeedtanPayControlNumber();
            $billPaymentMode = $_POST['bill_payment_mode'] ?? 'ALLOW_PARTIAL_AND_OVER_PAYMENT';
            
            if (!$customerEmail && !$customerPhone) {
                throw new Exception('Either customer email or phone number is required');
            }
            
            $result = $api->createCustomerControlNumber($customerName, $customerEmail, $customerPhone, $billDescription, $billAmount, $billReference, $billPaymentMode);
            $billPayData = $result;
            $success = 'Customer Control Number created successfully!';
            
        } elseif ($billType === 'bulk_order') {
            // Bulk Create Order Control Numbers
            $controlNumbers = [];
            $bulkData = json_decode($_POST['bulk_data'], true);
            
            if (!$bulkData || !is_array($bulkData)) {
                throw new Exception('Invalid bulk data format');
            }
            
            foreach ($bulkData as $item) {
                $controlItem = [
                    'billDescription' => $item['billDescription'] ?? 'Bulk Bill'
                ];
                
                if (isset($item['billAmount']) && $item['billAmount'] > 0) {
                    $controlItem['billAmount'] = $api->formatAmount($item['billAmount']);
                }
                
                // Use FEEDTANPAY format if no reference provided
                if (!empty($item['billReference'])) {
                    $controlItem['billReference'] = $item['billReference'];
                } else {
                    $controlItem['billReference'] = $api->generateFeedtanPayControlNumber();
                }
                
                $controlNumbers[] = $controlItem;
            }
            
            $result = $api->bulkCreateOrderControlNumbers($controlNumbers);
            $billPayData = $result;
            $success = 'Bulk Order Control Numbers created successfully!';
            
        } elseif ($billType === 'bulk_customer') {
            // Bulk Create Customer Control Numbers
            $controlNumbers = [];
            $bulkData = json_decode($_POST['bulk_data'], true);
            
            if (!$bulkData || !is_array($bulkData)) {
                throw new Exception('Invalid bulk data format');
            }
            
            foreach ($bulkData as $item) {
                if (empty($item['customerName'])) {
                    continue;
                }
                
                $controlItem = [
                    'customerName' => $item['customerName']
                ];
                
                if (!empty($item['customerEmail'])) {
                    $controlItem['customerEmail'] = $item['customerEmail'];
                }
                
                if (!empty($item['customerPhone'])) {
                    $controlItem['customerPhone'] = $api->validatePhoneNumber($item['customerPhone']);
                }
                
                if (!empty($item['billDescription'])) {
                    $controlItem['billDescription'] = $item['billDescription'];
                }
                
                if (isset($item['billAmount']) && $item['billAmount'] > 0) {
                    $controlItem['billAmount'] = $api->formatAmount($item['billAmount']);
                }
                
                // Use FEEDTANPAY format if no reference provided
                if (!empty($item['billReference'])) {
                    $controlItem['billReference'] = $item['billReference'];
                } else {
                    $controlItem['billReference'] = $api->generateFeedtanPayControlNumber();
                }
                
                $controlNumbers[] = $controlItem;
            }
            
            $result = $api->bulkCreateCustomerControlNumbers($controlNumbers);
            $billPayData = $result;
            $success = 'Bulk Customer Control Numbers created successfully!';
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
    <title>BillPay Control Number - ClickPesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .billpay-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 30px auto;
            max-width: 1000px;
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
        .bill-type-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .bill-type-btn {
            flex: 1;
            min-width: 200px;
            padding: 20px;
            border: 2px solid #dee2e6;
            border-radius: 15px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .bill-type-btn:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.1);
        }
        .bill-type-btn.active {
            border-color: #667eea;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .bill-type-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        .form-section {
            display: none;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
        }
        .form-section.active {
            display: block;
        }
        .result-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            margin-top: 30px;
        }
        .billpay-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            font-family: monospace;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid #667eea;
            text-align: center;
            margin: 15px 0;
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
        .bulk-textarea {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            min-height: 200px;
        }
        .success-badge {
            background: #d4edda;
            color: #155724;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }
        .error-badge {
            background: #f8d7da;
            color: #721c24;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
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
                <a class="nav-link" href="billpay_create.php" class="active">BillPay</a>
                <a class="nav-link" href="billpay_list.php">BillPay List</a>
                <a class="nav-link" href="account_management.php">Account</a>
                <a class="nav-link text-warning" href="fix_authentication.php">
                    <i class="fas fa-wrench me-1"></i>Fix Auth
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="billpay-container">
            <div class="header-section">
                <h1><i class="fas fa-file-invoice-dollar text-primary me-3"></i>BillPay Control Numbers</h1>
                <p>Create BillPay control numbers for orders, customers, or bulk operations</p>
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

            <form method="POST">
                <!-- Bill Type Selection -->
                <div class="bill-type-selector">
                    <div class="bill-type-btn active" onclick="showBillType('order')">
                        <i class="fas fa-shopping-cart"></i>
                        <h5>Order Control</h5>
                        <p class="mb-0">Create control number for an order</p>
                    </div>
                    <div class="bill-type-btn" onclick="showBillType('customer')">
                        <i class="fas fa-user"></i>
                        <h5>Customer Control</h5>
                        <p class="mb-0">Create control number for a customer</p>
                    </div>
                    <div class="bill-type-btn" onclick="showBillType('bulk_order')">
                        <i class="fas fa-layer-group"></i>
                        <h5>Bulk Order</h5>
                        <p class="mb-0">Create multiple order controls</p>
                    </div>
                    <div class="bill-type-btn" onclick="showBillType('bulk_customer')">
                        <i class="fas fa-users"></i>
                        <h5>Bulk Customer</h5>
                        <p class="mb-0">Create multiple customer controls</p>
                    </div>
                </div>

                <input type="hidden" name="bill_type" id="bill_type" value="order">

                <!-- Order Control Number Section -->
                <div id="orderSection" class="form-section active">
                    <h5><i class="fas fa-shopping-cart text-primary me-2"></i>Order Control Number</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <input type="text" name="bill_description" class="form-control" id="order_description" 
                                       placeholder="Bill Description" 
                                       value="<?php echo isset($_POST['bill_description']) ? htmlspecialchars($_POST['bill_description']) : ''; ?>">
                                <label for="order_description">Bill Description *</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" name="bill_amount" class="form-control" id="order_amount" 
                                       placeholder="Amount" step="0.01" min="0"
                                       value="<?php echo isset($_POST['bill_amount']) ? htmlspecialchars($_POST['bill_amount']) : ''; ?>">
                                <label for="order_amount">Bill Amount (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" name="bill_reference" class="form-control" id="order_reference" 
                                       placeholder="FEEDTANPAY12" maxlength="20" pattern="FEEDTANPAY[0-9]{2}"
                                       value="<?php echo isset($_POST['bill_reference']) ? htmlspecialchars($_POST['bill_reference']) : ''; ?>">
                                <label for="order_reference">Control Number (Optional)</label>
                                <div class="form-text">Format: FEEDTANPAY12 (Leave blank for auto-generation)</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <select name="bill_payment_mode" class="form-control" id="order_payment_mode" onchange="updatePaymentModeInfo()">
                                    <option value="ALLOW_PARTIAL_AND_OVER_PAYMENT" <?php echo (isset($_POST['bill_payment_mode']) && $_POST['bill_payment_mode'] === 'ALLOW_PARTIAL_AND_OVER_PAYMENT') ? 'selected' : ''; ?>>Allow Partial & Over Payment</option>
                                    <option value="EXACT" <?php echo (isset($_POST['bill_payment_mode']) && $_POST['bill_payment_mode'] === 'EXACT') ? 'selected' : ''; ?>>Exact Amount Only</option>
                                </select>
                                <label for="order_payment_mode">Payment Mode</label>
                                <div class="form-text" id="paymentModeInfo">
                                    <strong>Allow Partial & Over:</strong> Customers can pay partial amounts or overpay
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Control Number Section -->
                <div id="customerSection" class="form-section">
                    <h5><i class="fas fa-user text-primary me-2"></i>Customer Control Number</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <input type="text" name="customer_name" class="form-control" id="customer_name" 
                                       placeholder="Customer Name" 
                                       value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>">
                                <label for="customer_name">Customer Name *</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="email" name="customer_email" class="form-control" id="customer_email" 
                                       placeholder="Email Address" 
                                       value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>">
                                <label for="customer_email">Email Address</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="tel" name="customer_phone" class="form-control" id="customer_phone" 
                                       placeholder="Phone Number" pattern="255[67][0-9]{8}"
                                       value="<?php echo isset($_POST['customer_phone']) ? htmlspecialchars($_POST['customer_phone']) : ''; ?>">
                                <label for="customer_phone">Phone Number (255712345678)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <input type="text" name="bill_description" class="form-control" id="customer_description" 
                                       placeholder="Bill Description" 
                                       value="<?php echo isset($_POST['bill_description']) ? htmlspecialchars($_POST['bill_description']) : ''; ?>">
                                <label for="customer_description">Bill Description</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="number" name="bill_amount" class="form-control" id="customer_amount" 
                                       placeholder="Amount" step="0.01" min="0"
                                       value="<?php echo isset($_POST['bill_amount']) ? htmlspecialchars($_POST['bill_amount']) : ''; ?>">
                                <label for="customer_amount">Bill Amount (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" name="bill_reference" class="form-control" id="customer_reference" 
                                       placeholder="FEEDTANPAY12" maxlength="20" pattern="FEEDTANPAY[0-9]{2}"
                                       value="<?php echo isset($_POST['bill_reference']) ? htmlspecialchars($_POST['bill_reference']) : ''; ?>">
                                <label for="customer_reference">Control Number (Optional)</label>
                                <div class="form-text">Format: FEEDTANPAY12 (Leave blank for auto-generation)</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select name="bill_payment_mode" class="form-control" id="customer_payment_mode" onchange="updatePaymentModeInfo('customer')">
                                    <option value="ALLOW_PARTIAL_AND_OVER_PAYMENT" <?php echo (isset($_POST['bill_payment_mode']) && $_POST['bill_payment_mode'] === 'ALLOW_PARTIAL_AND_OVER_PAYMENT') ? 'selected' : ''; ?>>Allow Partial & Over Payment</option>
                                    <option value="EXACT" <?php echo (isset($_POST['bill_payment_mode']) && $_POST['bill_payment_mode'] === 'EXACT') ? 'selected' : ''; ?>>Exact Amount Only</option>
                                </select>
                                <label for="customer_payment_mode">Payment Mode</label>
                                <div class="form-text" id="customerPaymentModeInfo">
                                    <strong>Allow Partial & Over:</strong> Customers can pay partial amounts or overpay
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Order Control Numbers Section -->
                <div id="bulkOrderSection" class="form-section">
                    <h5><i class="fas fa-layer-group text-primary me-2"></i>Bulk Order Control Numbers</h5>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6><i class="fas fa-table text-info me-2"></i>Order Data Table</h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBulkOrderRow()">
                                        <i class="fas fa-plus me-1"></i>Add Row
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="clearBulkOrders()">
                                        <i class="fas fa-trash me-1"></i>Clear All
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="bulkOrderTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40%">Description</th>
                                            <th width="20%">Amount (TZS)</th>
                                            <th width="25%">Reference (Optional)</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" class="form-control form-control-sm bulk-order-desc" placeholder="Order description"></td>
                                            <td><input type="number" class="form-control form-control-sm bulk-order-amount" placeholder="0" step="0.01" min="0"></td>
                                            <td><input type="text" class="form-control form-control-sm bulk-order-ref" placeholder="Optional" maxlength="20"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBulkOrderRow(this)"><i class="fas fa-times"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle text-info me-2"></i>Bulk Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="bulkOrderPaymentMode">
                                            <option value="ALLOW_PARTIAL_AND_OVER_PAYMENT">Allow Partial & Over Payment</option>
                                            <option value="EXACT">Exact Amount Only</option>
                                        </select>
                                        <label for="bulkOrderPaymentMode">Payment Mode</label>
                                    </div>
                                    <div class="alert alert-info mb-3">
                                        <small><strong>Note:</strong> Maximum 50 orders per request</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="loadSampleBulkOrders()">
                                        <i class="fas fa-file-import me-1"></i>Load Sample Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Pro Tip:</strong> You can also paste JSON data directly in the textarea below for advanced users
                    </div>
                    <div class="form-floating mb-3">
                        <textarea name="bulk_data" class="form-control bulk-textarea" id="bulk_order_data" 
                                  placeholder='[
  {
    "billDescription": "Order 1 - Product A",
    "billAmount": 25000,
    "billReference": "ORD001"
  }
]'><?php echo isset($_POST['bulk_data']) ? htmlspecialchars($_POST['bulk_data']) : ''; ?></textarea>
                        <label for="bulk_order_data">Advanced JSON Input (Optional)</label>
                    </div>
                </div>

                <!-- Bulk Customer Control Numbers Section -->
                <div id="bulkCustomerSection" class="form-section">
                    <h5><i class="fas fa-users text-primary me-2"></i>Bulk Customer Control Numbers</h5>
                    <div class="row mb-3">
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6><i class="fas fa-users text-info me-2"></i>Customer Data Table</h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBulkCustomerRow()">
                                        <i class="fas fa-user-plus me-1"></i>Add Customer
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="clearBulkCustomers()">
                                        <i class="fas fa-trash me-1"></i>Clear All
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="bulkCustomerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="20%">Customer Name</th>
                                            <th width="15%">Email</th>
                                            <th width="15%">Phone</th>
                                            <th width="20%">Description</th>
                                            <th width="10%">Amount</th>
                                            <th width="10%">Reference</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" class="form-control form-control-sm bulk-customer-name" placeholder="Customer name"></td>
                                            <td><input type="email" class="form-control form-control-sm bulk-customer-email" placeholder="email@example.com"></td>
                                            <td><input type="tel" class="form-control form-control-sm bulk-customer-phone" placeholder="255712345678"></td>
                                            <td><input type="text" class="form-control form-control-sm bulk-customer-desc" placeholder="Bill description"></td>
                                            <td><input type="number" class="form-control form-control-sm bulk-customer-amount" placeholder="0" step="0.01" min="0"></td>
                                            <td><input type="text" class="form-control form-control-sm bulk-customer-ref" placeholder="Optional" maxlength="20"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBulkCustomerRow(this)"><i class="fas fa-times"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-cog text-info me-2"></i>Bulk Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="bulkCustomerPaymentMode">
                                            <option value="ALLOW_PARTIAL_AND_OVER_PAYMENT">Allow Partial & Over Payment</option>
                                            <option value="EXACT">Exact Amount Only</option>
                                        </select>
                                        <label for="bulkCustomerPaymentMode">Payment Mode</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="requireContact" checked>
                                        <label class="form-check-label" for="requireContact">
                                            Require Email or Phone
                                        </label>
                                    </div>
                                    <div class="alert alert-info mb-3">
                                        <small><strong>Note:</strong> Maximum 50 customers per request</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="loadSampleBulkCustomers()">
                                        <i class="fas fa-file-import me-1"></i>Load Sample Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Pro Tip:</strong> You can also paste JSON data directly in the textarea below for advanced users
                    </div>
                    <div class="form-floating mb-3">
                        <textarea name="bulk_data" class="form-control bulk-textarea" id="bulk_customer_data" 
                                  placeholder='[
  {
    "customerName": "John Doe",
    "customerEmail": "john@example.com",
    "billDescription": "Monthly Subscription",
    "billAmount": 25000
  }
]'><?php echo isset($_POST['bulk_data']) ? htmlspecialchars($_POST['bulk_data']) : ''; ?></textarea>
                        <label for="bulk_customer_data">Advanced JSON Input (Optional)</label>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>Create Control Number(s)
                    </button>
                </div>
            </form>

            <!-- Results Section -->
            <?php if ($billPayData): ?>
                <div class="result-card">
                    <h5><i class="fas fa-check-circle text-success me-2"></i>BillPay Control Number(s) Created</h5>
                    
                    <?php if (isset($billPayData['billPayNumber'])): ?>
                        <!-- Single Control Number Result -->
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Control Number:</h6>
                                <div class="billpay-number"><?php echo htmlspecialchars($billPayData['billPayNumber']); ?></div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Description:</strong> 
                                    <?php 
                                    // Use the data that was sent in the request, not API response
                                    $description = $billDescription ?? null;
                                    if ($description && $description !== 'N/A') {
                                        echo '<span class="text-primary">' . htmlspecialchars($description) . '</span>';
                                    } else {
                                        echo '<span class="text-muted fst-italic">No description provided</span>';
                                    }
                                    ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Amount:</strong> 
                                    <?php 
                                    // Use the data that was sent in the request
                                    if ($billAmount !== null && $billAmount > 0) {
                                        echo '<span class="text-success">' . number_format($billAmount) . ' TZS</span>';
                                    } else {
                                        echo '<span class="text-muted fst-italic">Not specified</span>';
                                    }
                                    ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Payment Mode:</strong> 
                                    <?php 
                                    // Use the data that was sent in the request
                                    $paymentModeText = $billPaymentMode === 'ALLOW_PARTIAL_AND_OVER_PAYMENT' ? 'Allow Partial & Over Payment' : 'Exact Amount Only';
                                    echo htmlspecialchars($paymentModeText); 
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if (isset($billPayData['billCustomerName'])): ?>
                                    <div class="mb-2">
                                        <strong>Customer:</strong> <?php echo htmlspecialchars($billPayData['billCustomerName']); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="mb-2">
                                    <strong>Status:</strong> <span class="success-badge">Active</span>
                                </div>
                            </div>
                        </div>
                        
                    <?php elseif (isset($billPayData['billPayNumbers'])): ?>
                        <!-- Bulk Control Numbers Result -->
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Bulk Creation Results:</h6>
                                <div class="alert alert-info">
                                    <strong>Created:</strong> <?php echo $billPayData['created']; ?> control numbers<br>
                                    <?php if (isset($billPayData['failed']) && $billPayData['failed'] > 0): ?>
                                        <strong>Failed:</strong> <?php echo $billPayData['failed']; ?> control numbers
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($billPayData['billPayNumbers'])): ?>
                                    <h6>Successfully Created Control Numbers:</h6>
                                    <div class="row">
                                        <?php foreach ($billPayData['billPayNumbers'] as $number): ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="billpay-number" style="font-size: 1rem; padding: 10px;">
                                                    <?php echo htmlspecialchars($number); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($billPayData['errors']) && !empty($billPayData['errors'])): ?>
                                    <h6 class="text-danger mt-3">Errors:</h6>
                                    <?php foreach ($billPayData['errors'] as $error): ?>
                                        <div class="alert alert-warning">
                                            <strong>Index <?php echo $error['index']; ?>:</strong> <?php echo htmlspecialchars($error['reason']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <a href="billpay_manage.php" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Manage BillPay Numbers
                        </a>
                        <a href="billpay_create.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Another
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showBillType(type) {
            // Update button states
            document.querySelectorAll('.bill-type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.bill-type-btn').classList.add('active');
            
            // Update hidden input
            document.getElementById('bill_type').value = type;
            
            // Hide all sections
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(type + 'Section').classList.add('active');
        }

        // Bulk Order Table Functions
        function addBulkOrderRow() {
            const table = document.getElementById('bulkOrderTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td><input type="text" class="form-control form-control-sm bulk-order-desc" placeholder="Order description"></td>
                <td><input type="number" class="form-control form-control-sm bulk-order-amount" placeholder="0" step="0.01" min="0"></td>
                <td><input type="text" class="form-control form-control-sm bulk-order-ref" placeholder="Optional" maxlength="20"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBulkOrderRow(this)"><i class="fas fa-times"></i></button></td>
            `;
            
            // Check if we've reached the limit
            const rows = table.getElementsByTagName('tr').length;
            if (rows >= 50) {
                document.querySelector('#bulkOrderSection .alert-info').innerHTML = '<small><strong>Warning:</strong> Maximum 50 orders per request reached</small>';
                document.querySelector('#bulkOrderSection .alert-info').classList.add('alert-warning');
            }
        }

        function removeBulkOrderRow(button) {
            const row = button.closest('tr');
            row.remove();
            
            // Update warning message
            const table = document.getElementById('bulkOrderTable').getElementsByTagName('tbody')[0];
            const rows = table.getElementsByTagName('tr').length;
            if (rows < 50) {
                document.querySelector('#bulkOrderSection .alert-info').innerHTML = '<small><strong>Note:</strong> Maximum 50 orders per request</small>';
                document.querySelector('#bulkOrderSection .alert-info').classList.remove('alert-warning');
            }
        }

        function clearBulkOrders() {
            const table = document.getElementById('bulkOrderTable').getElementsByTagName('tbody')[0];
            table.innerHTML = `
                <tr>
                    <td><input type="text" class="form-control form-control-sm bulk-order-desc" placeholder="Order description"></td>
                    <td><input type="number" class="form-control form-control-sm bulk-order-amount" placeholder="0" step="0.01" min="0"></td>
                    <td><input type="text" class="form-control form-control-sm bulk-order-ref" placeholder="Optional" maxlength="20"></td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBulkOrderRow(this)"><i class="fas fa-times"></i></button></td>
                </tr>
            `;
        }

        function loadSampleBulkOrders() {
            const sampleData = [
                { description: "Web Hosting Plan - Basic", amount: 25000, reference: "WEB001" },
                { description: "Domain Registration - .com", amount: 15000, reference: "DOM001" },
                { description: "SSL Certificate - Standard", amount: 35000, reference: "SSL001" },
                { description: "Email Marketing Service", amount: 45000, reference: "EMAIL001" },
                { description: "Cloud Storage - 100GB", amount: 20000, reference: "CLOUD001" }
            ];
            
            const table = document.getElementById('bulkOrderTable').getElementsByTagName('tbody')[0];
            table.innerHTML = '';
            
            sampleData.forEach(item => {
                const newRow = table.insertRow();
                newRow.innerHTML = `
                    <td><input type="text" class="form-control form-control-sm bulk-order-desc" value="${item.description}"></td>
                    <td><input type="number" class="form-control form-control-sm bulk-order-amount" value="${item.amount}" step="0.01" min="0"></td>
                    <td><input type="text" class="form-control form-control-sm bulk-order-ref" value="${item.reference}" maxlength="20"></td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBulkOrderRow(this)"><i class="fas fa-times"></i></button></td>
                `;
            });
        }

        // Bulk Customer Table Functions
        function addBulkCustomerRow() {
            const table = document.getElementById('bulkCustomerTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td><input type="text" class="form-control form-control-sm bulk-customer-name" placeholder="Customer name"></td>
                <td><input type="email" class="form-control form-control-sm bulk-customer-email" placeholder="email@example.com"></td>
                <td><input type="tel" class="form-control form-control-sm bulk-customer-phone" placeholder="255712345678"></td>
                <td><input type="text" class="form-control form-control-sm bulk-customer-desc" placeholder="Bill description"></td>
                <td><input type="number" class="form-control form-control-sm bulk-customer-amount" placeholder="0" step="0.01" min="0"></td>
                <td><input type="text" class="form-control form-control-sm bulk-customer-ref" placeholder="Optional" maxlength="20"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBulkCustomerRow(this)"><i class="fas fa-times"></i></button></td>
            `;
            
            // Check if we've reached the limit
            const rows = table.getElementsByTagName('tr').length;
            if (rows >= 50) {
                document.querySelector('#bulkCustomerSection .alert-info').innerHTML = '<small><strong>Warning:</strong> Maximum 50 customers per request reached</small>';
                document.querySelector('#bulkCustomerSection .alert-info').classList.add('alert-warning');
            }
        }

        function removeBulkCustomerRow(button) {
            const row = button.closest('tr');
            row.remove();
            
            // Update warning message
            const table = document.getElementById('bulkCustomerTable').getElementsByTagName('tbody')[0];
            const rows = table.getElementsByTagName('tr').length;
            if (rows < 50) {
                document.querySelector('#bulkCustomerSection .alert-info').innerHTML = '<small><strong>Note:</strong> Maximum 50 customers per request</small>';
                document.querySelector('#bulkCustomerSection .alert-info').classList.remove('alert-warning');
            }
        }

        function clearBulkCustomers() {
            const table = document.getElementById('bulkCustomerTable').getElementsByTagName('tbody')[0];
            table.innerHTML = `
                <tr>
                    <td><input type="text" class="form-control form-control-sm bulk-customer-name" placeholder="Customer name"></td>
                    <td><input type="email" class="form-control form-control-sm bulk-customer-email" placeholder="email@example.com"></td>
                    <td><input type="tel" class="form-control form-control-sm bulk-customer-phone" placeholder="255712345678"></td>
                    <td><input type="text" class="form-control form-control-sm bulk-customer-desc" placeholder="Bill description"></td>
                    <td><input type="number" class="form-control form-control-sm bulk-customer-amount" placeholder="0" step="0.01" min="0"></td>
                    <td><input type="text" class="form-control form-control-sm bulk-customer-ref" placeholder="Optional" maxlength="20"></td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBulkCustomerRow(this)"><i class="fas fa-times"></i></button></td>
                </tr>
            `;
        }

        function loadSampleBulkCustomers() {
            const sampleData = [
                { name: "John Doe", email: "john@example.com", phone: "255712345678", description: "Monthly Subscription", amount: 25000, reference: "SUB001" },
                { name: "Jane Smith", email: "jane@example.com", phone: "255713456789", description: "Annual Membership", amount: 120000, reference: "MEM001" },
                { name: "Robert Johnson", email: "robert@company.com", phone: "", description: "Professional Services", amount: 75000, reference: "PRO001" },
                { name: "Sarah Williams", email: "", phone: "255714567890", description: "Consulting Fee", amount: 50000, reference: "CON001" },
                { name: "Michael Brown", email: "michael@business.com", phone: "255715678901", description: "Product Purchase", amount: 35000, reference: "PROD001" }
            ];
            
            const table = document.getElementById('bulkCustomerTable').getElementsByTagName('tbody')[0];
            table.innerHTML = '';
            
            sampleData.forEach(item => {
                const newRow = table.insertRow();
                newRow.innerHTML = `
                    <td><input type="text" class="form-control form-control-sm bulk-customer-name" value="${item.name}"></td>
                    <td><input type="email" class="form-control form-control-sm bulk-customer-email" value="${item.email}"></td>
                    <td><input type="tel" class="form-control form-control-sm bulk-customer-phone" value="${item.phone}"></td>
                    <td><input type="text" class="form-control form-control-sm bulk-customer-desc" value="${item.description}"></td>
                    <td><input type="number" class="form-control form-control-sm bulk-customer-amount" value="${item.amount}" step="0.01" min="0"></td>
                    <td><input type="text" class="form-control form-control-sm bulk-customer-ref" value="${item.reference}" maxlength="20"></td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeBulkCustomerRow(this)"><i class="fas fa-times"></i></button></td>
                `;
            });
        }

        // Update payment mode information dynamically
        function updatePaymentModeInfo(type = 'order') {
            const selectId = type === 'customer' ? 'customer_payment_mode' : 'order_payment_mode';
            const infoId = type === 'customer' ? 'customerPaymentModeInfo' : 'paymentModeInfo';
            const select = document.getElementById(selectId);
            const info = document.getElementById(infoId);
            
            const descriptions = {
                'ALLOW_PARTIAL_AND_OVER_PAYMENT': '<strong>Allow Partial & Over:</strong> Customers can pay partial amounts or overpay',
                'EXACT': '<strong>Exact Amount Only:</strong> Customers must pay exact amount only'
            };
            
            info.innerHTML = descriptions[select.value] || descriptions['ALLOW_PARTIAL_AND_OVER_PAYMENT'];
        }

        // Convert table data to JSON for form submission
        function prepareBulkData() {
            const billType = document.getElementById('bill_type').value;
            
            if (billType === 'bulk_order') {
                const rows = document.querySelectorAll('#bulkOrderTable tbody tr');
                const orders = [];
                
                rows.forEach(row => {
                    const desc = row.querySelector('.bulk-order-desc').value.trim();
                    const amount = row.querySelector('.bulk-order-amount').value;
                    const ref = row.querySelector('.bulk-order-ref').value.trim();
                    
                    if (desc) {
                        const order = { billDescription: desc };
                        if (amount) order.billAmount = parseFloat(amount);
                        // Use FEEDTANPAY format if no reference provided
                        if (ref && ref.match(/^FEEDTANPAY[0-9]{2}$/)) {
                            order.billReference = ref;
                        } else {
                            order.billReference = 'FEEDTANPAY' + String(Math.floor(Math.random() * 100)).padStart(2, '0');
                        }
                        orders.push(order);
                    }
                });
                
                document.getElementById('bulk_order_data').value = JSON.stringify(orders, null, 2);
                
            } else if (billType === 'bulk_customer') {
                const rows = document.querySelectorAll('#bulkCustomerTable tbody tr');
                const customers = [];
                const requireContact = document.getElementById('requireContact').checked;
                
                rows.forEach(row => {
                    const name = row.querySelector('.bulk-customer-name').value.trim();
                    const email = row.querySelector('.bulk-customer-email').value.trim();
                    const phone = row.querySelector('.bulk-customer-phone').value.trim();
                    const desc = row.querySelector('.bulk-customer-desc').value.trim();
                    const amount = row.querySelector('.bulk-customer-amount').value;
                    const ref = row.querySelector('.bulk-customer-ref').value.trim();
                    
                    if (name) {
                        if (!requireContact || email || phone) {
                            const customer = { customerName: name };
                            if (email) customer.customerEmail = email;
                            if (phone) customer.customerPhone = phone;
                            if (desc) customer.billDescription = desc;
                            if (amount) customer.billAmount = parseFloat(amount);
                            // Use FEEDTANPAY format if no reference provided
                            if (ref && ref.match(/^FEEDTANPAY[0-9]{2}$/)) {
                                customer.billReference = ref;
                            } else {
                                customer.billReference = 'FEEDTANPAY' + String(Math.floor(Math.random() * 100)).padStart(2, '0');
                            }
                            customers.push(customer);
                        }
                    }
                });
                
                document.getElementById('bulk_customer_data').value = JSON.stringify(customers, null, 2);
            }
        }

        function validateForm() {
            const billType = document.getElementById('bill_type').value;
            let isValid = true;
            
            // Clear previous error states
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            if (billType === 'order') {
                const description = document.getElementById('order_description');
                if (!description.value.trim()) {
                    description.classList.add('is-invalid');
                    isValid = false;
                }
            } else if (billType === 'customer') {
                const customerName = document.getElementById('customer_name');
                const customerEmail = document.getElementById('customer_email');
                const customerPhone = document.getElementById('customer_phone');
                
                if (!customerName.value.trim()) {
                    customerName.classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!customerEmail.value.trim() && !customerPhone.value.trim()) {
                    customerEmail.classList.add('is-invalid');
                    customerPhone.classList.add('is-invalid');
                    isValid = false;
                }
                
                // Validate phone format if provided
                if (customerPhone.value.trim() && !/^255[67]\d{8}$/.test(customerPhone.value.trim())) {
                    customerPhone.classList.add('is-invalid');
                    isValid = false;
                }
            } else if (billType === 'bulk_order' || billType === 'bulk_customer') {
                const bulkData = document.getElementById('bulk_' + billType + '_data');
                try {
                    const data = JSON.parse(bulkData.value);
                    if (!Array.isArray(data) || data.length === 0) {
                        bulkData.classList.add('is-invalid');
                        isValid = false;
                    }
                } catch (e) {
                    bulkData.classList.add('is-invalid');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Please fill in all required fields correctly.';
                errorDiv.innerHTML += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                
                const container = document.querySelector('.billpay-container');
                container.insertBefore(errorDiv, container.firstChild);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    if (errorDiv.parentNode) {
                        errorDiv.parentNode.removeChild(errorDiv);
                    }
                }, 5000);
            }
            
            return isValid;
        }

        // Add form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const billType = document.getElementById('bill_type').value;
            
            // Prepare bulk data from tables if using bulk operations
            if (billType === 'bulk_order' || billType === 'bulk_customer') {
                prepareBulkData();
            }
            
            if (!validateForm()) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

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
