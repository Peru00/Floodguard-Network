<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .donor-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .donor-header {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            border: var(--glass-border);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .donor-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            border: var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .stat-label {
            color: var(--text-secondary);
            margin-top: 5px;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .dashboard-section {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            border: var(--glass-border);
            border-radius: 15px;
            padding: 25px;
        }
        
        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .section-title i {
            margin-right: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }
        
        .btn-submit {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-submit:hover {
            background: var(--accent-color);
            transform: translateY(-2px);
        }
        
        .recent-donations {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .donation-item {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid;
        }
        
        .donation-item.pending {
            border-left-color: #ffa502;
        }
        
        .donation-item.approved {
            border-left-color: #2ed573;
        }
        
        .donation-item.rejected {
            border-left-color: #ff4757;
        }
        
        .donation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .donation-id {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .donation-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #ffa502;
            color: white;
        }
        
        .status-approved {
            background: #2ed573;
            color: white;
        }
        
        .status-rejected {
            background: #ff4757;
            color: white;
        }
        
        .donation-details {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .monetary-fields {
            display: none;
        }
        
        .goods-fields {
            display: none;
        }
        
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            border: var(--glass-border);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar .logo {
            display: flex;
            align-items: center;
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .navbar .logo i {
            margin-right: 10px;
        }
        
        .navbar .nav-links {
            display: flex;
            list-style: none;
            gap: 20px;
            margin: 0;
            padding: 0;
        }
        
        .navbar .nav-links a {
            color: var(--primary-color);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .navbar .nav-links a:hover,
        .navbar .nav-links a.active {
            background: var(--secondary-color);
            color: white;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .donor-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <i class="fas fa-hands-helping"></i>
                <span>Floodguard Network</span>
            </div>
            <ul class="nav-links">
                <li><a href="{{ route('donor.dashboard') }}" class="active">Dashboard</a></li>
                <li><a href="{{ route('donor.donations') }}">My Donations</a></li>
                <li><a href="{{ route('donor.distribution-repository') }}">Distribution</a></li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="donor-container">
        <!-- Welcome Header -->
        <div class="donor-header">
            <h1>Welcome, {{ $donorInfo->first_name ?? 'Donor' }} {{ $donorInfo->last_name ?? '' }}</h1>
            <p>Thank you for your generous support in helping flood victims. Your contributions make a real difference.</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="donor-stats">
            <div class="stat-card">
                <i class="fas fa-hand-holding-heart"></i>
                <div class="stat-value">{{ $donorInfo->total_donations ?? 0 }}</div>
                <div class="stat-label">Total Donations</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-coins"></i>
                <div class="stat-value">৳{{ number_format($donorInfo->total_amount ?? 0, 2) }}</div>
                <div class="stat-label">Total Amount</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar-alt"></i>
                <div class="stat-value">{{ $donorInfo->last_donation_date ? \Carbon\Carbon::parse($donorInfo->last_donation_date)->format('M d') : 'N/A' }}</div>
                <div class="stat-label">Last Donation</div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Make New Donation -->
            <div class="dashboard-section">
                <h2 class="section-title">
                    <i class="fas fa-plus-circle"></i>
                    Make a New Donation
                </h2>
                
                <form action="{{ route('donor.submit-donation') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="donation_type">Donation Type</label>
                        <select id="donation_type" name="donation_type" required onchange="toggleDonationFields()">
                            <option value="">Select Donation Type</option>
                            <option value="monetary">Monetary Donation</option>
                            <option value="goods">Goods/Items</option>
                            <option value="services">Services</option>
                        </select>
                    </div>

                    <div id="monetary-fields" class="monetary-fields">
                        <div class="form-group">
                            <label for="amount">Amount (৳)</label>
                            <input type="number" id="amount" name="amount" min="0" step="0.01" placeholder="Enter amount">
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select id="payment_method" name="payment_method">
                                <option value="">Select Payment Method</option>
                                <option value="bkash">bKash</option>
                                <option value="nogad">Nagad</option>
                                <option value="rocket">Rocket</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="transaction_id">Transaction ID</label>
                            <input type="text" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID">
                        </div>
                    </div>

                    <div id="goods-fields" class="goods-fields">
                        <div class="form-group">
                            <label for="items">Items Description</label>
                            <textarea id="items" name="items" rows="3" placeholder="Describe the items you're donating"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity/Units</label>
                            <input type="number" id="quantity" name="quantity" min="1" placeholder="Enter quantity">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-heart"></i> Submit Donation
                    </button>
                </form>
            </div>

            <!-- Recent Donations -->
            <div class="dashboard-section">
                <h2 class="section-title">
                    <i class="fas fa-history"></i>
                    Recent Donations
                </h2>
                
                <div class="recent-donations">
                    @forelse($recentDonations as $donation)
                        <div class="donation-item {{ $donation->status }}">
                            <div class="donation-header">
                                <span class="donation-id">{{ $donation->donation_id }}</span>
                                <span class="donation-status status-{{ $donation->status }}">{{ ucfirst($donation->status) }}</span>
                            </div>
                            <div class="donation-details">
                                <div>Type: {{ ucfirst($donation->donation_type) }}</div>
                                @if($donation->amount)
                                    <div>Amount: ৳{{ number_format($donation->amount, 2) }}</div>
                                @endif
                                @if($donation->items)
                                    <div>Items: {{ $donation->items }}</div>
                                @endif
                                <div>Date: {{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; color: var(--text-secondary); padding: 40px 20px;">
                            <i class="fas fa-heart" style="font-size: 3rem; margin-bottom: 15px; color: var(--secondary-color);"></i><br>
                            No donations yet. Start making a difference today!
                        </p>
                    @endforelse
                </div>
                
                @if(count($recentDonations) > 0)
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="{{ route('donor.donations') }}" class="btn-submit" style="display: inline-block; width: auto; padding: 8px 20px;">
                            View All Donations
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleDonationFields() {
            const donationType = document.getElementById('donation_type').value;
            const monetaryFields = document.getElementById('monetary-fields');
            const goodsFields = document.getElementById('goods-fields');
            
            // Hide all fields first
            monetaryFields.style.display = 'none';
            goodsFields.style.display = 'none';
            
            // Show relevant fields
            if (donationType === 'monetary') {
                monetaryFields.style.display = 'block';
            } else if (donationType === 'goods' || donationType === 'services') {
                goodsFields.style.display = 'block';
            }
        }
    </script>
</body>
</html>
