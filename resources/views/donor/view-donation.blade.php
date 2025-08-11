<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Receipt - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Arial', sans-serif;
            padding: 40px 20px;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .receipt-header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 2px dashed #e0e0e0;
            margin-bottom: 30px;
        }
        
        .receipt-logo {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }
        
        .receipt-title {
            font-size: 2rem;
            color: var(--primary-color);
            margin: 0;
            font-weight: 600;
        }
        
        .receipt-subtitle {
            color: var(--text-secondary);
            margin-top: 10px;
        }
        
        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-section h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }
        
        .info-section h3 i {
            margin-right: 10px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .info-label {
            font-weight: 500;
            color: var(--text-secondary);
        }
        
        .info-value {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .donation-details {
            background: rgba(0, 123, 255, 0.05);
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid var(--secondary-color);
            margin-bottom: 30px;
        }
        
        .amount-highlight {
            text-align: center;
            padding: 20px;
            background: var(--secondary-color);
            color: white;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .amount-value {
            font-size: 2.5rem;
            font-weight: bold;
        }
        
        .amount-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .status-section {
            text-align: center;
            margin: 30px 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .receipt-footer {
            text-align: center;
            padding-top: 30px;
            border-top: 2px dashed #e0e0e0;
            color: var(--text-secondary);
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 30px 0;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--accent-color);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .thank-you {
            background: linear-gradient(135deg, #2ed573, #17a2b8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            
            .action-buttons {
                display: none !important;
            }
        }
        
        @media (max-width: 768px) {
            .receipt-info {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="receipt-logo">
                <i class="fas fa-hands-helping"></i>
            </div>
            <h1 class="receipt-title">Donation Receipt</h1>
            <p class="receipt-subtitle">Floodguard Network - Flood Relief Organization</p>
        </div>

        <!-- Donation Information -->
        <div class="receipt-info">
            <div class="info-section">
                <h3><i class="fas fa-user"></i> Donor Information</h3>
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $donation->first_name }} {{ $donation->last_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Donation ID:</span>
                    <span class="info-value">{{ $donation->donation_id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y h:i A') }}</span>
                </div>
            </div>

            <div class="info-section">
                <h3><i class="fas fa-info-circle"></i> Donation Details</h3>
                <div class="info-item">
                    <span class="info-label">Type:</span>
                    <span class="info-value">{{ ucfirst($donation->donation_type) }}</span>
                </div>
                @if($donation->payment_method)
                    <div class="info-item">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value">{{ ucfirst($donation->payment_method) }}</span>
                    </div>
                @endif
                @if($donation->transaction_id)
                    <div class="info-item">
                        <span class="info-label">Transaction ID:</span>
                        <span class="info-value">{{ $donation->transaction_id }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Donation Details Section -->
        <div class="donation-details">
            @if($donation->donation_type === 'money')
                <div class="amount-highlight">
                    <div class="amount-value">৳{{ number_format($donation->amount, 2) }}</div>
                    <div class="amount-label">Monetary Donation</div>
                </div>
            @else
                <h4><i class="fas fa-box"></i> Items Donated</h4>
                <p><strong>Description:</strong> {{ $donation->items }}</p>
                @if($donation->quantity)
                    <p><strong>Quantity:</strong> {{ $donation->quantity }} units</p>
                @endif
            @endif
        </div>

        <!-- Status Section -->
        <div class="status-section">
            <p><strong>Current Status:</strong></p>
            <span class="status-badge status-{{ $donation->status }}">
                <i class="fas fa-{{ $donation->status === 'approved' ? 'check-circle' : ($donation->status === 'pending' ? 'clock' : 'times-circle') }}"></i>
                {{ ucfirst($donation->status) }}
            </span>
            
            @if($donation->status === 'approved')
                <div class="thank-you">
                    <h4><i class="fas fa-heart"></i> Thank You!</h4>
                    <p>Your donation has been approved and will be used to help flood victims in need. Your generosity makes a real difference in people's lives.</p>
                </div>
            @elseif($donation->status === 'pending')
                <p style="margin-top: 15px; color: var(--text-secondary);">
                    <i class="fas fa-info-circle"></i> Your donation is currently being reviewed by our team. You will be notified once it's processed.
                </p>
            @elseif($donation->status === 'rejected')
                <p style="margin-top: 15px; color: #dc3545;">
                    <i class="fas fa-exclamation-triangle"></i> This donation was rejected. Please contact our support team for more information.
                </p>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="{{ route('donor.donations') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Donations
            </a>
            <a href="{{ route('donor.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <p><strong>Floodguard Network</strong></p>
            <p>Dedicated to providing relief and support to flood victims</p>
            <p><i class="fas fa-envelope"></i> contact@floodguard.org | <i class="fas fa-phone"></i> +880-XXX-XXXXXX</p>
            <p><small>This is a computer-generated receipt. No signature required.</small></p>
        </div>
    </div>
</body>
</html>
