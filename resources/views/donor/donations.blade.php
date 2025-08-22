<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Donations - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Match dashboard styling */
        .donor-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .page-header {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 25px;
            margin-top: 100px;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
        }
        
        .page-header p {
            color: #666;
            margin: 12px 0 0 0;
            font-size: 16px;
            font-weight: 500;
        }
        
        .donations-table {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table-header {
            background: var(--secondary-color);
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        
        .table th {
            background: #eef2f3;
            font-weight: 600;
            color: #222;
        }
        
        .table tbody tr:hover {
            background: #f9fafb;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .btn-view {
            background: var(--secondary-color);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-view:hover {
            background: var(--accent-color);
            transform: translateY(-1px);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #222;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .empty-state p {
            color: #666;
            line-height: 1.6;
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
        
        .alert {
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        
        .alert-danger {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
        }
        
        @media (max-width: 768px) {
            .table {
                font-size: 12px;
            }
            
            .table th,
            .table td {
                padding: 8px;
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
                <li><a href="{{ route('donor.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('donor.donations') }}" class="active">My Donations</a></li>
                <li><a href="{{ route('donor.distribution-repository') }}">Distribution</a></li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="donor-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-heart"></i> My Donations</h1>
            <p>Track all your contributions and their impact on flood relief efforts</p>
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

        <!-- Donations Table -->
        <div class="donations-table">
            <div class="table-header">
                <i class="fas fa-list"></i> Donation History
            </div>
            
            @if(count($donations) > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Donation ID</th>
                            <th>Type</th>
                            <th>Amount/Items</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donations as $donation)
                            <tr>
                                <td>
                                    <strong>{{ $donation->donation_id }}</strong>
                                </td>
                                <td>
                                    <i class="fas fa-{{ $donation->donation_type === 'money' ? 'coins' : ($donation->donation_type === 'food' ? 'utensils' : ($donation->donation_type === 'clothing' ? 'tshirt' : ($donation->donation_type === 'medicine' ? 'pills' : 'hand-helping'))) }}"></i>
                                    {{ ucfirst($donation->donation_type) }}
                                </td>
                                <td>
                                    @if($donation->donation_type === 'money')
                                        <strong>৳{{ number_format($donation->amount, 2) }}</strong>
                                        @if($donation->payment_method)
                                            <br><small>via {{ ucfirst($donation->payment_method) }}</small>
                                        @endif
                                    @else
                                        {{ $donation->items ?? $donation->description }}
                                        @if($donation->quantity)
                                            <br><small>Qty: {{ $donation->quantity }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}<br>
                                    <small>{{ \Carbon\Carbon::parse($donation->donation_date)->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $donation->status }}">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('donor.view-donation', $donation->donation_id) }}" class="btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="fas fa-heart-broken"></i>
                    <h3>No donations found</h3>
                    <p>You haven't made any donations yet. Start making a difference today!</p>
                    <a href="{{ route('donor.dashboard') }}" class="btn-view" style="display: inline-block; margin-top: 15px; padding: 10px 20px;">
                        <i class="fas fa-plus"></i> Make a Donation
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
