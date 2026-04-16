<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fanya Malipo - Payment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --success-color: #1a7437;
            --light-green: #d4edda;
            --dark-green: #155724;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--success-color);
            min-height: 100vh;
            padding: 10px;
            margin: 0;
        }

        .payment-container {
            max-width: 500px;
            margin: 20px auto;
            background: white;
            border: 2px solid var(--success-color);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(26, 116, 55, 0.15);
            overflow: hidden;
        }

        .payment-header {
            background: var(--success-color);
            color: white;
            padding: 25px 20px;
            text-align: center;
            position: relative;
        }

        .payment-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="50" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="30" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .payment-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .payment-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 1rem;
            position: relative;
            z-index: 1;
        }

        .payment-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--success-color);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--success-color);
            box-shadow: 0 0 0 0.2rem rgba(26, 116, 55, 0.25);
            background: white;
        }

        
        .btn-primary {
            background: var(--success-color);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 116, 55, 0.3);
        }

        .result-card {
            background: var(--light-green);
            color: var(--success-color);
            border: 2px solid var(--success-color);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-card h5 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .payment-details {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .payment-details .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
        }

        .payment-details .row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .payment-details strong {
            color: var(--success-color);
            font-weight: 600;
        }

        .alert {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        footer {
            background: var(--success-color);
            color: white;
            margin-top: 30px;
            padding: 25px 0;
            text-align: center;
        }

        footer p {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        footer small {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .countdown-timer {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid var(--success-color);
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 20px;
            text-align: center;
        }

        .countdown-timer i {
            color: var(--success-color);
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .countdown-text {
            font-weight: 600;
            color: var(--success-color);
            font-size: 0.95rem;
        }

        .btn-success {
            background: var(--success-color);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
        }

        .btn-light {
            background: #f8f9fa;
            color: var(--success-color);
            border: 2px solid var(--success-color);
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            background: var(--success-color);
            color: white;
            transform: translateY(-2px);
        }

        .text-warning {
            color: #856404;
        }

        .fa-hourglass-end {
            color: #ffc107;
        }

        .security-badge {
            background: rgba(26, 116, 55, 0.1);
            border: 1px solid var(--success-color);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.85rem;
            color: var(--success-color);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading-spinner.active {
            display: block;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--success-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 600px) {
            .payment-container {
                margin: 10px;
                max-width: 100%;
            }

            
            .payment-header h1 {
                font-size: 1.4rem;
            }

            .payment-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <div class="payment-header">
                <h1><i class="fas fa-shield-alt me-2"></i>Payment System</h1>
                <p>Fanya Malipo Salama</p>
            </div>

            <div class="payment-body">
                @if ($success)
                    <div class="result-card">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h5>{!! $success !!}</h5>
                        @if ($paymentData)
                            <div class="payment-details">
                                <div class="row">
                                    <div class="col-6">
                                        <strong>ID:</strong> {{ $paymentData['id'] ?? $orderReference }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Kiasi:</strong> {{ number_format($amount ?? 0) }} TZS
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Simu:</strong> {{ $phoneNumber }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Hali:</strong> {{ $paymentData['status'] ?? 'PROCESSING' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="mt-4">
                            <div class="countdown-timer" id="countdown" style="display: none;">
                                <i class="fas fa-clock mb-2 d-block"></i>
                                <div class="countdown-text">
                                    Subiri <span id="countdown-seconds">300</span> sekunde kuthibitisha malipo...
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="{{ url('/public/payment') }}" class="btn btn-light">
                                    <i class="fas fa-plus me-1"></i>Mpya
                                </a>
                                <button class="btn btn-success" onclick="downloadReceipt()" id="downloadBtn" style="display: none;">
                                    <i class="fas fa-download me-1"></i>Pakua Risiti
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    @if ($error)
                        <div class="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{!! $error !!}
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/public/payment') }}" id="paymentForm">
                        @csrf
                        <div class="form-group">
                            <label><i class="fas fa-user me-1"></i>Jina la Mwanachama</label>
                            <input type="text" name="member_name" class="form-control" id="member_name" 
                                   placeholder="Jina lako kamili" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-comment me-1"></i>Madhumuni ya Malipo</label>
                            <textarea name="payment_purpose" class="form-control" id="payment_purpose" 
                                      placeholder="Eleza madhumuni ya malipo" rows="2" required></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-money-bill me-1"></i>Kiasi (TZS)</label>
                            <input type="number" name="amount" class="form-control" id="amount" 
                                   placeholder="Andika kiasi" step="100" min="100" max="1000000" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-phone me-1"></i>Namba ya Simu</label>
                            <input type="tel" name="phone_number" class="form-control" id="phone_number" 
                                   placeholder="255712345678" pattern="255[67]\d{8}" required>
                            <small><i class="fas fa-info-circle me-1"></i>Format: 255712345678 (Tanzania)</small>
                        </div>

                        <div class="loading-spinner" id="loadingSpinner">
                            <div class="spinner"></div>
                            <p>Inasubiri kuthibitisha malipo...</p>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-lock me-2"></i>LIPA SASA
                        </button>

                        <div class="mt-3 text-center">
                            <div class="security-badge">
                                <i class="fas fa-shield-alt"></i>
                                Malipo yako yanalindwa na kisasa
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let countdownInterval;
        
        
        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            const countdownText = document.getElementById('countdown-seconds');
            const downloadBtn = document.getElementById('downloadBtn');
            
            countdownElement.style.display = 'block';
            downloadBtn.style.display = 'none';
            
            let seconds = 300; // 5 minutes
            countdownText.textContent = seconds;
            
            countdownInterval = setInterval(() => {
                seconds--;
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                
                if (minutes > 0) {
                    countdownText.textContent = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
                } else {
                    countdownText.textContent = `${remainingSeconds} sekunde`;
                }
                
                if (seconds <= 0) {
                    clearInterval(countdownInterval);
                    expirePage();
                }
            }, 1000);
        }

        function expirePage() {
            document.body.innerHTML = `
                <div class="container">
                    <div class="payment-container">
                        <div class="payment-header">
                            <h1><i class="fas fa-clock me-3"></i>Muda Umekuisha</h1>
                            <p>Ukumbuka wa malipo umekuisha. Tafadhali anza upya.</p>
                        </div>
                        <div class="payment-body text-center">
                            <i class="fas fa-hourglass-end fa-4x mb-3 text-warning"></i>
                            <h5 class="text-warning">Muda wa Malipo Umekuisha</h5>
                            <p class="mt-3">Ukumbuka wa malipo umekuisha kwa sababu za usalama.</p>
                            <a href="{{ url('/public/payment') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-redo me-2"></i>Anza Upya
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        function downloadReceipt() {
            const receiptData = {
                transactionId: '{{ $paymentData['id'] ?? $orderReference ?? '' }}',
                amount: '{{ $amount ?? 0 }}',
                currency: 'TZS',
                phoneNumber: '{{ $phoneNumber ?? '' }}',
                memberName: '{{ old('member_name') ?? '' }}',
                paymentPurpose: '{{ old('payment_purpose') ?? '' }}',
                date: new Date().toISOString(),
                status: 'SUCCESS'
            };
            
            const receiptText = `
Payment System
P.O.Box 7744, Dar es Salaam, Tanzania

===============================================
RECEIPT YA MALIPO / PAYMENT RECEIPT
===============================================

Transaction ID: ${receiptData.transactionId}
Tarehe: ${new Date().toLocaleDateString('sw-TZ')}
Saa: ${new Date().toLocaleTimeString('sw-TZ')}

Maelezo ya Mwanachama:
${receiptData.memberName}

Namba ya Simu: ${receiptData.phoneNumber}

Kiasi: ${receiptData.amount} TZS

Madhumuni ya Malipo:
${receiptData.paymentPurpose}

Hali ya Malipo: ${receiptData.status}

===============================================
Asante kwa kutumia huduma zetu za malipo.
Thank you for using our payment services.
===============================================
            `;
            
            const blob = new Blob([receiptText], { type: 'text/plain;charset=utf-8' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `receipt_${receiptData.transactionId}_${new Date().getTime()}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        // Form validation and submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const amount = document.getElementById('amount').value;
            const phone = document.getElementById('phone_number').value;
            const memberName = document.getElementById('member_name').value;
            const paymentPurpose = document.getElementById('payment_purpose').value;
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');

            if (amount < 100 || amount > 1000000) {
                e.preventDefault();
                alert('Kiasi lazima kuwa kati ya 100 na 1,000,000 TZS');
                return false;
            }

            if (!/^255[67]\d{8}$/.test(phone)) {
                e.preventDefault();
                alert('Tafadhali weka namba ya simu sahihi ya Tanzania');
                return false;
            }

            if (memberName.trim().length < 2) {
                e.preventDefault();
                alert('Tafadhali weka jina kamili');
                return false;
            }

            if (paymentPurpose.trim().length < 5) {
                e.preventDefault();
                alert('Tafadhali eleza madhumuni ya malipo');
                return false;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Inasubiri...';
            loadingSpinner.classList.add('active');
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Start countdown on page load if payment was successful
        @if ($success)
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(startCountdown, 1000);
            });
        @endif
    </script>
</body>
</html>
