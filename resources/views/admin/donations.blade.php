<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Donations Management - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

       .stat-icon.total { background: #8ab2a6; }
        .stat-icon.pending { background: #8ab2a6; }
        .stat-icon.approved { background: #8ab2a6; }
        .stat-icon.rejected { background: #8ab2a6; }
        .stat-icon.amount { background: #8ab2a6; }

        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            color: var(--primary-color);
        }

        .stat-info p {
            margin: 0;
            color: #666;
            font-weight: 500;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .form-input, .form-select {
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: var(--primary-color);
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 0.75em;
            font-weight: 600;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
        }

        .badge.pending {
            background-color: #ffc107;
            color: #000000;
            font-weight: 700;
        }

        .badge.approved {
            background-color: #28a745;
            color: #ffffff;
        }

        .badge.rejected {
            background-color: #dc3545;
            color: #ffffff;
        }

        .badge.monetary {
            background-color: #28a745;
            color: #ffffff;
        }

        .badge.money {
            background-color: #28a745;
            color: #ffffff;
        }

        .badge.food {
            background-color: #ffc107;
            color: #000000;
            font-weight: 700;
        }

        .badge.medical {
            background-color: #17a2b8;
            color: #ffffff;
        }

        .badge.medicine {
            background-color: #17a2b8;
            color: #ffffff;
        }

        .badge.clothing {
            background-color: #6f42c1;
            color: #ffffff;
        }

        .badge.other {
            background-color: #6c757d;
            color: #ffffff;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            transition: all 0.2s ease;
        }

        .approve-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
        }

        .approve-btn:hover {
            background: linear-gradient(135deg, #218838, #1ea080);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
        }

        .reject-btn {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }

        .reject-btn:hover {
            background: linear-gradient(135deg, #c82333, #dc2626);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
        }

        .view-btn {
            background: linear-gradient(135deg, #007bff, #0056b3);
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        }

        .view-btn:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem;
            background: white;
            border-top: 1px solid #e9ecef;
        }

        .pagination-info {
            color: #666;
            font-size: 0.9rem;
        }

        .pagination-links {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .page-link {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            text-decoration: none;
            color: #495057;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background-color: #f8f9fa;
            border-color: #8ab2a6;
            color: #8ab2a6;
        }

        .page-link.active {
            background-color: #8ab2a6;
            border-color: #8ab2a6;
            color: white;
        }

        .page-link.disabled {
            color: #6c757d;
            border: none;
            background: none;
            cursor: default;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            color: var(--primary-color);
            margin: 0;
        }

        .close {
            font-size: 1.5rem;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            border: none;
            background: none;
        }

        .close:hover {
            color: #000;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-label {
            font-weight: 600;
            color: var(--primary-color);
        }

        .detail-value {
            color: #666;
        }

        .form-textarea {
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 14px;
            resize: vertical;
            min-height: 80px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .amount-cell {
            font-weight: bold;
            color: var(--primary-color);
        }

        .urgent-row {
            border-left: 4px solid #ff4757;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-hands-helping"></i>
            <h1>Floodguard Admin</h1>
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.user-management') }}"><i class="fas fa-users-cog"></i> User Management</a></li>
            <li><a href="{{ route('admin.relief-camps') }}"><i class="fas fa-campground"></i> Relief Camps</a></li>
            <li><a href="{{ route('admin.inventory') }}"><i class="fas fa-box-open"></i> Inventory</a></li>
            <li class="active"><a href="{{ route('admin.donations') }}"><i class="fas fa-donate"></i> Donations</a></li>
            <li><a href="{{ route('admin.distribution-repo') }}"><i class="fas fa-truck"></i> Distribution Repo</a></li>
            <li>
                <div class="admin-profile">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Admin Profile">
                    @else
                        <img src="{{ asset('assets/profile-user.png') }}" alt="Admin Profile">
                    @endif
                </div>
            </li>
            <li>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn" style="background: none; border: none; color: var(--text-light); cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="admin-container">
        <main class="main-content">
            <!-- Page Header -->
            <div class="section-header">
                <h2><i class="fas fa-donate"></i> Donations Management</h2>
                <div class="section-actions">
                    <button class="btn btn-secondary" onclick="toggleFilters()">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.donations', ['export' => 'csv']) }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Export CSV
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Donations Summary Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['total_donations']) }}</h3>
                        <p>Total Donations</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['pending_count']) }}</h3>
                        <p>Pending Review</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon approved">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['monetary_count']) }}</h3>
                        <p>Money Donations</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rejected">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['physical_count']) }}</h3>
                        <p>Physical Donations</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amount">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>৳{{ number_format($stats['monetary_amount'], 2) }}</h3>
                        <p>Money Amount</p>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filters" id="filterSection" style="display: none;">
                <form method="GET" action="{{ route('admin.donations') }}">
                    <div class="filter-row">
                        <div class="form-group">
                            <label class="form-label">Search Donations</label>
                            <input type="text" name="search" class="form-input" placeholder="Search by donor name or description..." value="{{ request('search') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Donation Type</label>
                            <select name="donation_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="money" {{ request('donation_type') == 'money' ? 'selected' : '' }}>Money</option>
                                <option value="food" {{ request('donation_type') == 'food' ? 'selected' : '' }}>Food</option>
                                <option value="medical" {{ request('donation_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                                <option value="medicine" {{ request('donation_type') == 'medicine' ? 'selected' : '' }}>Medicine</option>
                                <option value="clothing" {{ request('donation_type') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                                <option value="other" {{ request('donation_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date Range</label>
                            <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Donations Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Donation ID</th>
                            <th>Donor Name</th>
                            <th>Type</th>
                            <th>Amount/Description</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $donation)
                            <tr>
                                <td><strong>{{ $donation->donation_id }}</strong></td>
                                <td>
                                    <div>
                                        @if($donation->user)
                                            <strong>{{ $donation->user->first_name }} {{ $donation->user->last_name }}</strong>
                                            @if($donation->user->phone)
                                                <br><small style="color: #666;">{{ $donation->user->phone }}</small>
                                            @endif
                                        @else
                                            <strong>Anonymous Donor</strong>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $typeClass = strtolower($donation->donation_type);
                                        // Map different type names to consistent classes  
                                        if (in_array($typeClass, ['money', 'monetary'])) $typeClass = 'money';
                                        if (in_array($typeClass, ['medicine', 'medical'])) $typeClass = 'medical';
                                    @endphp
                                    <span class="badge {{ $typeClass }}">
                                        {{ $donation->donation_type == 'money' ? 'Money' : ucfirst($donation->donation_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if(in_array($donation->donation_type, ['monetary', 'money']))
                                        <span class="amount-cell">৳{{ number_format($donation->amount, 2) }}</span>
                                    @else
                                        <div>
                                            <strong>{{ $donation->item_description ?? $donation->description ?? 'Physical Donation' }}</strong>
                                            @if($donation->quantity)
                                                <br><small>Qty: {{ $donation->quantity }}</small>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $donation->status }}">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                                <td>{{ $donation->donation_date ? \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn view-btn" onclick="viewDonation({
                                            id: '{{ $donation->donation_id }}',
                                            donor_name: '{{ $donation->user ? addslashes($donation->user->first_name . " " . $donation->user->last_name) : "Anonymous Donor" }}',
                                            phone: '{{ $donation->user && $donation->user->phone ? addslashes($donation->user->phone) : "" }}',
                                            email: '{{ $donation->user && $donation->user->email ? addslashes($donation->user->email) : "" }}',
                                            type: '{{ ucfirst($donation->donation_type) }}',
                                            amount: '{{ in_array($donation->donation_type, ["monetary", "money"]) ? "৳" . number_format($donation->amount, 2) : addslashes($donation->item_description ?? $donation->description ?? "Physical Donation") }}',
                                            quantity: '{{ $donation->quantity ?? "" }}',
                                            message: '{{ addslashes($donation->message ?? "No message provided") }}',
                                            date: '{{ $donation->donation_date ? \Carbon\Carbon::parse($donation->donation_date)->format("M d, Y") : "N/A" }}',
                                            status: '{{ ucfirst($donation->status) }}'
                                        })" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($donation->status == 'pending')
                                            <form method="POST" action="{{ route('admin.donations.approve', $donation->donation_id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="action-btn approve-btn" onclick="return confirm('Are you sure you want to approve this donation?')" title="Approve Donation">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button class="action-btn reject-btn" onclick="openRejectModal('{{ $donation->donation_id }}')" title="Reject Donation">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: #666;">
                                    <i class="fas fa-hand-holding-heart" style="font-size: 3rem; color: #ddd; display: block; margin-bottom: 1rem;"></i>
                                    No donations found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if($donations->hasPages())
                    <div class="pagination">
                        <div class="pagination-info">
                            Showing {{ $donations->firstItem() }} to {{ $donations->lastItem() }} of {{ $donations->total() }} results
                        </div>
                        <div class="pagination-links">
                            @for ($i = 1; $i <= $donations->lastPage(); $i++)
                                @if ($i == $donations->currentPage())
                                    <span class="page-link active">{{ $i }}</span>
                                @else
                                    <a href="{{ $donations->url($i) }}" class="page-link">{{ $i }}</a>
                                @endif
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- View Donation Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-eye"></i> Donation Details</h3>
                <button class="close" onclick="closeViewModal()">&times;</button>
            </div>
            <div class="modal-body" id="donationDetails">
                <!-- Donation details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Reject Donation Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-times-circle"></i> Reject Donation</h3>
                <button class="close" onclick="closeRejectModal()">&times;</button>
            </div>
            <form method="POST" action="" id="rejectForm">
                @csrf
                <div class="modal-body">
                    <p>Please provide a reason for rejecting this donation:</p>
                    <div class="form-group">
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-textarea" required placeholder="Explain why this donation is being rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Donation</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleFilters() {
            const filterSection = document.getElementById('filterSection');
            filterSection.style.display = filterSection.style.display === 'none' ? 'block' : 'none';
        }

        function viewDonation(donation) {
            const modal = document.getElementById('viewModal');
            const detailsDiv = document.getElementById('donationDetails');
            
            // Show the actual donation details immediately
            detailsDiv.innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Donation ID:</span>
                    <span class="detail-value">${donation.id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Donor Name:</span>
                    <span class="detail-value">${donation.donor_name}</span>
                </div>
                ${donation.phone ? `
                <div class="detail-row">
                    <span class="detail-label">Contact:</span>
                    <span class="detail-value">${donation.phone}</span>
                </div>
                ` : ''}
                ${donation.email ? `
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">${donation.email}</span>
                </div>
                ` : ''}
                <div class="detail-row">
                    <span class="detail-label">Donation Type:</span>
                    <span class="detail-value">${donation.type}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">${['monetary', 'money'].includes(donation.type.toLowerCase()) ? 'Amount' : 'Description'}:</span>
                    <span class="detail-value">${donation.amount}</span>
                </div>
                ${donation.quantity ? `
                <div class="detail-row">
                    <span class="detail-label">Quantity:</span>
                    <span class="detail-value">${donation.quantity}</span>
                </div>
                ` : ''}
                <div class="detail-row">
                    <span class="detail-label">Message:</span>
                    <span class="detail-value">${donation.message}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date Submitted:</span>
                    <span class="detail-value">${donation.date}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="badge ${donation.status.toLowerCase()}">${donation.status}</span></span>
                </div>
            `;
            
            modal.style.display = 'block';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        function openRejectModal(donationId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            
            form.action = `/admin/donations/${donationId}/reject`;
            modal.style.display = 'block';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const viewModal = document.getElementById('viewModal');
            const rejectModal = document.getElementById('rejectModal');
            
            if (event.target == viewModal) {
                closeViewModal();
            }
            if (event.target == rejectModal) {
                closeRejectModal();
            }
        }
    </script>
</body>
</html>
