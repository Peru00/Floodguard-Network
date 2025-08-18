<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add these new styles */
        .actions {
            display: flex;
            gap: 5px;
            align-items: center;
            justify-content: flex-start;
        }

        .action-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            color: white;
        }

        .view-btn {
            background-color: #3498db;
        }

        .approve-btn {
            background-color: #2ed573;
        }

        .reject-btn {
            background-color: #ff4757;
        }

        form[method="POST"] {
            display: inline-flex;
            gap: 5px;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            padding: 20px 30px;
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
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .modal-body {
            padding: 20px 30px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: 600;
            width: 150px;
            color: var(--primary-color);
        }

        .detail-value {
            flex: 1;
            color: #333;
        }

        /* Form specific styles for modals */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-info {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
            font-size: 14px;
            border-left: 4px solid #3498db;
        }

        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }

        /* Enhanced badge styles for availability */
        .badge i {
            margin-right: 4px;
            font-size: 0.8em;
        }

        .badge.approved {
            background-color: #e8f5e8;
            color: #2d5a27;
            border: 1px solid #c3e6cb;
        }

        .badge.rejected {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            color: #8b1538;
        }

        @keyframes pulse-available {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Real-time indicator */
        .availability-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .availability-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .availability-dot.available {
            background: #2ed573;
            animation: blink-green 1.5s infinite;
        }

        .availability-dot.unavailable {
            background: #ff4757;
        }

        @keyframes blink-green {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Enhanced button styles for modal */
        .btn.btn-secondary {
            transition: all 0.3s ease;
        }

        .btn.btn-secondary:hover {
            background: #ffcdd2 !important;
            color: #c62828 !important;
            border-color: #ef9a9a !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(211, 47, 47, 0.2);
        }

        .btn.btn-primary:hover {
            background: #c8e6c8 !important;
            color: #1b5e20 !important;
            border-color: #a5d6a5 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
        }

        /* Button toggle styles */
        .btn.active {
            background-color: #3498db !important;
            color: white !important;
        }

        .btn:not(.active) {
            background-color: #ecf0f1;
            color: #7f8c8d;
        }

        /* Chat Communication Styles */
        .chat-container {
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
            height: 600px;
        }

        .chat-layout {
            display: flex;
            height: 100%;
        }

        .volunteer-list {
            width: 300px;
            background: white;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
        }

        .volunteer-list h3 {
            padding: 15px;
            margin: 0;
            background: #3498db;
            color: white;
            font-size: 16px;
            font-weight: 600;
        }

        .volunteer-search {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .volunteer-items {
            flex: 1;
            overflow-y: auto;
        }

        .volunteer-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .volunteer-item:hover {
            background-color: #f8f9fa;
        }

        .volunteer-item.selected {
            background-color: #e3f2fd;
            border-left: 3px solid #3498db;
        }

        .volunteer-info {
            flex: 1;
        }

        .volunteer-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .volunteer-status {
            font-size: 12px;
            color: #666;
        }

        .volunteer-status.online {
            color: #28a745;
        }

        .volunteer-status.offline {
            color: #dc3545;
        }

        .volunteer-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-left: 10px;
        }

        .volunteer-indicator.online {
            background-color: #28a745;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.3);
        }

        .volunteer-indicator.offline {
            background-color: #dc3545;
        }

        .no-volunteers {
            text-align: center;
            padding: 30px 15px;
            color: #666;
            font-style: italic;
        }

        .chat-interface {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-header {
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: #333;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #fff;
        }

        .welcome-message {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .welcome-message i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message-content {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
        }

        .message.received .message-content {
            background: #f1f3f4;
            color: #333;
            border-bottom-left-radius: 4px;
        }

        .message.sent .message-content {
            background: #3498db;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-text {
            margin: 0;
            line-height: 1.4;
        }

        .message-image {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 5px;
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 5px;
        }

        .chat-input-container {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .message-input-area {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        #messageInput {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 20px;
            resize: none;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.4;
            max-height: 100px;
        }

        #messageInput:focus {
            outline: none;
            border-color: #3498db;
        }

        .chat-actions {
            display: flex;
            gap: 5px;
        }

        .image-btn, .send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .image-btn {
            background: #6c757d;
            color: white;
        }

        .image-btn:hover {
            background: #5a6268;
        }

        .send-btn {
            background: #3498db;
            color: white;
        }

        .send-btn:hover {
            background: #2980b9;
        }

        .send-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
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
            <li class="active"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.user-management') }}"><i class="fas fa-users-cog"></i> User Management</a></li>
            <li><a href="{{ route('admin.relief-camps') }}"><i class="fas fa-campground"></i> Relief Camps</a></li>
            <li><a href="#"><i class="fas fa-box-open"></i> Inventory</a></li>
            <li><a href="#"><i class="fas fa-donate"></i> Donations</a></li>
            <li><a href="#"><i class="fas fa-box-open"></i> Distribution Repo</a></li>
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
            <!-- Stats Overview Section -->
            <section class="stats-section">
                <h2>Dashboard Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Volunteers</h3>
                            <p>{{ $stats['volunteers'] }}</p>
                            <span class="stat-change {{ $stats['volunteers'] > 0 ? 'up' : 'neutral' }}">
                                <i class="fas fa-hands-helping"></i> 
                                {{ $volunteers->where('is_available', true)->count() }} Available Now
                            </span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Donors</h3>
                            <p>{{ $stats['donors'] }}</p>
                            <span class="stat-change {{ $stats['pending_donations'] > 0 ? 'up' : 'neutral' }}">
                                <i class="fas fa-clock"></i> {{ $stats['pending_donations'] }} Pending Requests
                            </span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-donate"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Donations</h3>
                            <p>${{ number_format($stats['total_donations'], 2) }}</p>
                            <span class="stat-change up"><i class="fas fa-check-circle"></i> Approved donations</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Victims</h3>
                            <p>{{ $stats['victims'] }}</p>
                            <span class="stat-change {{ $stats['high_priority_victims'] > 0 ? 'down' : 'neutral' }}">
                                <i class="fas fa-exclamation-triangle"></i> {{ $stats['high_priority_victims'] }} High Priority
                            </span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Affected Locations</h3>
                            <p>{{ $stats['locations'] }}</p>
                            <span class="stat-change {{ $stats['pending_victims'] > 0 ? 'up' : 'neutral' }}">
                                <i class="fas fa-users"></i> {{ $stats['pending_victims'] }} Need Help
                            </span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Relief Items</h3>
                            <p>{{ number_format($stats['distributions']) }}</p>
                            <span class="stat-change {{ $stats['assisted_victims'] > 0 ? 'up' : 'neutral' }}">
                                <i class="fas fa-heart"></i> {{ $stats['assisted_victims'] }} Assisted
                            </span>
                        </div>
                    </div>
                </div>
            </section>

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

            <!-- Donations Management Section -->
            <section class="donations-section">
                <div class="section-header">
                    <h2>Pending Donation Requests</h2>
                    <div class="section-actions">
                        <button class="btn btn-secondary"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Donation ID</th>
                                <th>Donor Name</th>
                                <th>Type</th>
                                <th>Amount/Items</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingDonations as $donation)
                                <tr>
                                    <td>{{ $donation->donation_id }}</td>
                                    <td>{{ $donation->user->first_name }} {{ $donation->user->last_name }}</td>
                                    <td>
                                        <span class="badge @if($donation->donation_type === 'monetary') info @else warning @endif">
                                            {{ ucfirst($donation->donation_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($donation->donation_type === 'monetary')
                                            ${{ number_format($donation->amount, 2) }}
                                        @else
                                            {{ $donation->quantity }} {{ $donation->items }}
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge pending">{{ ucfirst($donation->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="action-btn view-btn" onclick="viewDonation('{{ $donation->donation_id }}')" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.donation.update-status') }}" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="donation_id" value="{{ $donation->donation_id }}">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="action-btn approve-btn" onclick="return confirm('Are you sure you want to approve this donation?')" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.donation.update-status') }}" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="donation_id" value="{{ $donation->donation_id }}">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="action-btn reject-btn" onclick="return confirm('Are you sure you want to reject this donation?')" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: #666;">
                                        <i class="fas fa-inbox"></i><br>
                                        No pending donations to review
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Recent Donations Section -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
                <!-- Victim List Section -->
                <section class="donations-section">
                    <div class="section-header">
                        <h2>Registered Victims</h2>
                        <div class="section-actions">
                            <span style="margin-right: 15px; color: #666; font-size: 0.9rem;">
                                {{ $victims->where('status', 'pending')->count() }} Pending
                            </span>
                            <span style="margin-right: 15px; color: #666; font-size: 0.9rem;">
                                {{ $victims->where('status', 'assisted')->count() }} Assisted
                            </span>
                            <a href="{{ route('admin.user-management') }}" class="btn btn-secondary"><i class="fas fa-plus"></i> Add Victim</a>
                        </div>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Victim ID</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Family Size</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Needs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($victims as $victim)
                                    <tr>
                                        <td>{{ $victim->victim_id }}</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <i class="fas fa-user-injured" style="color: #e74c3c;"></i>
                                                {{ $victim->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <i class="fas fa-map-marker-alt" style="color: #e74c3c;"></i>
                                                {{ $victim->location }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge info">{{ $victim->family_size }} people</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $victim->priority === 'high' ? 'rejected' : ($victim->priority === 'medium' ? 'warning' : 'approved') }}">
                                                {{ ucfirst($victim->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $victim->status === 'assisted' ? 'approved' : ($victim->status === 'relocated' ? 'info' : 'pending') }}">
                                                {{ ucfirst($victim->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $victim->needs }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2rem; color: #666;">
                                            <i class="fas fa-users"></i><br>
                                            No victims registered yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Available Volunteers Section -->
                <section class="donations-section">
                    <div class="section-header">
                        <h2>Available Volunteers</h2>
                        <div class="section-actions">
                            <button id="availableBtn" class="btn btn-primary active" onclick="showAvailable()">
                                {{ $volunteers->where('is_available', true)->count() }} Available
                            </button>
                            <button id="unavailableBtn" class="btn btn-secondary" onclick="showUnavailable()">
                                {{ $volunteers->where('is_available', false)->count() }} Unavailable
                            </button>
                            <a href="{{ route('admin.user-management') }}" class="btn btn-secondary"><i class="fas fa-user-plus"></i> Add Volunteer</a>
                        </div>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Volunteer ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Availability</th>
                                    <th>Assigned Tasks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($volunteers as $volunteer)
                                    <tr class="volunteer-row {{ $volunteer->is_available ? 'available-volunteer' : 'unavailable-volunteer' }}" style="{{ !$volunteer->is_available ? 'display: none;' : '' }}">
                                        <td>{{ $volunteer->user_id }}</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <i class="fas fa-user" style="color: #3498db;"></i>
                                                {{ $volunteer->first_name }} {{ $volunteer->last_name }}
                                            </div>
                                        </td>
                                        <td>{{ $volunteer->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if($volunteer->is_available)
                                                <span class="badge approved">Available</span>
                                            @else
                                                <span class="badge rejected">Unavailable</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span style="color: #7f8c8d;">No tasks</span>
                                        </td>
                                        <td>
                                            <button class="action-btn" style="background-color: #28a745; color: white;" onclick="openAssignTaskModal('{{ $volunteer->user_id }}', '{{ $volunteer->first_name }} {{ $volunteer->last_name }}')" title="Assign New Task">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-volunteers-message">
                                        <td colspan="6" style="text-align: center; padding: 2rem; color: #666;">
                                            <i class="fas fa-hands-helping"></i><br>
                                            <span id="no-volunteers-text">No available volunteers</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Chat Communication Section -->
                <section class="donations-section">
                    <div class="section-header">
                        <h2><i class="fas fa-comments"></i> Volunteer Communication</h2>
                        <div class="section-actions">
                            <button id="refreshChatBtn" class="btn btn-secondary" onclick="refreshChatList()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="chat-container">
                        <div class="chat-layout">
                            <!-- Volunteer List -->
                            <div class="volunteer-list">
                                <h3>Select Volunteer</h3>
                                <div class="volunteer-search">
                                    <input type="text" id="volunteerSearch" placeholder="Search volunteers..." class="search-input">
                                </div>
                                <div class="volunteer-items">
                                    @forelse($volunteers as $volunteer)
                                        <div class="volunteer-item" onclick="selectVolunteer('{{ $volunteer->user_id }}', '{{ $volunteer->first_name }} {{ $volunteer->last_name }}')">
                                            <div class="volunteer-info">
                                                <div class="volunteer-name">{{ $volunteer->first_name }} {{ $volunteer->last_name }}</div>
                                                <div class="volunteer-status {{ $volunteer->is_available ? 'online' : 'offline' }}">
                                                    {{ $volunteer->is_available ? 'Available' : 'Unavailable' }}
                                                </div>
                                            </div>
                                            <div class="volunteer-indicator {{ $volunteer->is_available ? 'online' : 'offline' }}"></div>
                                        </div>
                                    @empty
                                        <div class="no-volunteers">No volunteers available</div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Chat Interface -->
                            <div class="chat-interface">
                                <div class="chat-header">
                                    <div id="selectedVolunteerName">Select a volunteer to start chatting</div>
                                </div>
                                <div class="chat-messages" id="chatMessages">
                                    <div class="welcome-message">
                                        <i class="fas fa-comments"></i>
                                        <p>Select a volunteer from the list to start a conversation</p>
                                    </div>
                                </div>
                                <div class="chat-input-container">
                                    <form id="chatForm" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" id="selectedVolunteerId" name="volunteer_id">
                                        <div class="message-input-area">
                                            <textarea id="messageInput" name="message" placeholder="Type your message..." rows="2"></textarea>
                                            <div class="chat-actions">
                                                <label for="imageInput" class="image-btn" title="Send Image">
                                                    <i class="fas fa-image"></i>
                                                    <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">
                                                </label>
                                                <button type="submit" class="send-btn" title="Send Message">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Donation Details Modal -->
    <div id="donationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Donation Details</h3>
                <span class="close" onclick="closeDonationModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="detail-label">Donation ID:</div>
                    <div class="detail-value" id="modal-donation-id">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Donor Name:</div>
                    <div class="detail-value" id="modal-donor-name">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Type:</div>
                    <div class="detail-value" id="modal-donation-type">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Amount/Items:</div>
                    <div class="detail-value" id="modal-amount-items">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div class="detail-value" id="modal-date">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value" id="modal-status">-</div>
                </div>
                <div class="detail-row" id="payment-info" style="display: none;">
                    <div class="detail-label">Payment Method:</div>
                    <div class="detail-value" id="modal-payment-method">-</div>
                </div>
                <div class="detail-row" id="transaction-info" style="display: none;">
                    <div class="detail-label">Transaction ID:</div>
                    <div class="detail-value" id="modal-transaction-id">-</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Victim Modal -->
    <div id="addVictimModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Victim</h3>
                <span class="close" onclick="closeAddVictimModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.add-victim') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-input" required placeholder="Enter victim's full name">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Family Size *</label>
                        <input type="number" name="family_size" class="form-input" min="1" required placeholder="Number of family members">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-input" placeholder="Contact phone number">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Location *</label>
                        <input type="text" name="location" class="form-input" required placeholder="Current location or address">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Priority Level *</label>
                        <select name="priority" class="form-select" required>
                            <option value="">Select priority level</option>
                            <option value="high">High Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="low">Low Priority</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Needs Description *</label>
                        <textarea name="needs" class="form-textarea" required placeholder="Describe what assistance is needed (food, shelter, medical aid, etc.)"></textarea>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn btn-secondary" onclick="closeAddVictimModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Victim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Volunteer Modal -->
    <div id="addVolunteerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Volunteer</h3>
                <span class="close" onclick="closeAddVolunteerModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.add-volunteer') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-input" required placeholder="Enter first name">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" class="form-input" required placeholder="Enter last name">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-input" required placeholder="volunteer@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number *</label>
                        <input type="text" name="phone" class="form-input" required placeholder="Contact phone number">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Location *</label>
                        <input type="text" name="location" class="form-input" required placeholder="City or area of operation">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Skill Type *</label>
                        <select name="skill_type" class="form-select" required>
                            <option value="">Select skill type</option>
                            <option value="Medical Aid">Medical Aid</option>
                            <option value="Transportation">Transportation</option>
                            <option value="Food Distribution">Food Distribution</option>
                            <option value="Rescue Operations">Rescue Operations</option>
                            <option value="Communication">Communication</option>
                            <option value="General Support">General Support</option>
                        </select>
                    </div>
                    
                    <div class="form-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Login Details:</strong> The volunteer will receive an account with their email as username and default password <strong>"volunteer123"</strong>. They can change this password after first login.
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn btn-secondary" onclick="closeAddVolunteerModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Volunteer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Task Modal -->
    <div id="assignTaskModal" class="modal">
        <div class="modal-content" style="max-width: 1200px; height: 85vh; margin: 2% auto;">
            <div class="modal-header">
                <h3>Assign Task to <span id="selectedVolunteerName">Volunteer</span></h3>
                <span class="close" onclick="closeAssignTaskModal()">&times;</span>
            </div>
            <div class="modal-body" style="height: calc(85vh - 180px); overflow-y: auto; padding: 20px;">
                <form method="POST" action="{{ route('admin.assign-task') }}" id="assignTaskForm">
                    @csrf
                    <input type="hidden" name="volunteer_id" id="assignVolunteerId">
                    
                    <!-- Horizontal Card Layout -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        
                        <!-- Task Details Card (Blue) -->
                        <div style="background: #e3f2fd; border-left: 4px solid #2196f3; border-radius: 8px; padding: 20px; min-height: 300px;">
                            <h4 style="margin: 0 0 15px 0; color: #1976d2; text-align: center;"><i class="fas fa-clipboard-list"></i> Task Details</h4>
                            
                            <div class="form-group">
                                <label class="form-label" style="font-size: 14px; font-weight: bold;">Task Title Type *</label>
                                <select name="task_title_type" id="taskTitleType" class="form-select" required onchange="toggleTaskTitleInput()" style="font-size: 14px; padding: 8px;">
                                    <option value="">Select type</option>
                                    <option value="victim_related">👥 Victim Related</option>
                                    <option value="other">🔧 Custom Task</option>
                                </select>
                            </div>
                            
                            <div class="form-group" id="victimSelectionGroup" style="display: none;">
                                <label class="form-label" style="font-size: 14px; font-weight: bold;">Select Victim *</label>
                                <select name="victim_id" id="victimSelect" class="form-select" style="font-size: 14px; padding: 8px;" onchange="updateVictimDetails()">
                                    <option value="">Choose victim...</option>
                                    @if(isset($victims))
                                        @foreach($victims as $victim)
                                            <option value="{{ $victim->victim_id }}" 
                                                    data-priority="{{ $victim->priority }}" 
                                                    data-needs="{{ $victim->needs }}"
                                                    data-location="{{ $victim->location }}"
                                                    data-family-size="{{ $victim->family_size }}">
                                                {{ $victim->name }} - {{ $victim->location }} (Priority: {{ ucfirst($victim->priority) }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div class="form-group" id="customTaskTitleGroup" style="display: none;">
                                <label class="form-label" style="font-size: 14px; font-weight: bold;">Custom Title *</label>
                                <input type="text" name="custom_task_title" id="customTaskTitle" class="form-input" placeholder="Enter title" style="font-size: 14px; padding: 8px;">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" style="font-size: 14px; font-weight: bold;">Description *</label>
                                <textarea name="task_description" class="form-textarea" required placeholder="Task instructions..." style="min-height: 80px; font-size: 14px; padding: 8px;"></textarea>
                            </div>
                        </div>
                        
                        <!-- Task Description Card (Green) -->
                        <div style="background: #e8f5e8; border-left: 4px solid #4caf50; border-radius: 8px; padding: 20px; min-height: 300px;">
                            <h4 style="margin: 0 0 15px 0; color: #388e3c; text-align: center;"><i class="fas fa-tasks"></i> Task Category</h4>
                            
                            <div class="form-group">
                                <label class="form-label" style="font-size: 14px; font-weight: bold;">Task Type *</label>
                                <select name="task_type" class="form-select" required style="font-size: 14px; padding: 8px;">
                                    <option value="">Select category</option>
                                    <option value="relief_distribution">📦 Relief Distribution</option>
                                    <option value="medical_assistance">🏥 Medical Assistance</option>
                                    <option value="evacuation_support">🚁 Evacuation Support</option>
                                    <option value="communication">📞 Communication</option>
                                    <option value="logistics">🚛 Logistics</option>
                                    <option value="data_collection">📊 Data Collection</option>
                                    <option value="other">🔧 Other Tasks</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" style="font-size: 14px; font-weight: bold;">Location</label>
                                <input type="text" name="location" class="form-input" placeholder="Task location (optional)" style="font-size: 14px; padding: 8px;">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" style="font-size: 14px; font-weight: bold;">Additional Notes</label>
                                <textarea name="notes" class="form-textarea" placeholder="Special instructions..." style="min-height: 100px; font-size: 14px; padding: 8px;"></textarea>
                            </div>
                        </div>
                        
                        <!-- Priority Level Card (Red) -->
                        <div style="background: #ffebee; border-left: 4px solid #f44336; border-radius: 8px; padding: 20px; min-height: 300px;">
                            <h4 style="margin: 0 0 15px 0; color: #d32f2f; text-align: center;"><i class="fas fa-exclamation-triangle"></i> Priority Level</h4>
                            
                            <div class="form-group">
                                <label class="form-label" style="font-size: 14px; font-weight: bold;">Priority *</label>
                                <select name="priority" class="form-select" required style="font-size: 14px; padding: 8px;">
                                    <option value="">Select priority</option>
                                    <option value="high">🔴 High Priority</option>
                                    <option value="medium">🟡 Medium Priority</option>
                                    <option value="low">🟢 Low Priority</option>
                                </select>
                            </div>
                            
                            <div style="background: #fff; border-radius: 6px; padding: 15px; margin-top: 20px; border: 1px solid #ffcdd2;">
                                <h5 style="margin: 0 0 10px 0; color: #d32f2f;">Priority Guidelines:</h5>
                                <ul style="margin: 0; padding-left: 20px; font-size: 12px; color: #666;">
                                    <li><strong>High:</strong> Urgent, life-threatening situations</li>
                                    <li><strong>Medium:</strong> Important but not critical</li>
                                    <li><strong>Low:</strong> Can be done when available</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Due Date Card (Orange) -->
                        <div style="background: #fff3e0; border-left: 4px solid #ff9800; border-radius: 8px; padding: 20px; min-height: 300px;">
                            <h4 style="margin: 0 0 15px 0; color: #f57c00; text-align: center;"><i class="fas fa-calendar-alt"></i> Due Date</h4>
                            
                            <div class="form-group">
                                <label class="form-label" style="font-size: 14px; font-weight: bold;">Due Date & Time *</label>
                                <input type="datetime-local" name="due_date" class="form-input" required style="font-size: 14px; padding: 8px;">
                            </div>
                            
                            <div style="background: #fff; border-radius: 6px; padding: 15px; margin-top: 20px; border: 1px solid #ffcc02;">
                                <div style="display: flex; align-items: flex-start; gap: 10px;">
                                    <i class="fas fa-info-circle" style="color: #ff9800; margin-top: 2px;"></i>
                                    <div>
                                        <strong style="color: #f57c00; font-size: 13px;">Notice:</strong>
                                        <p style="margin: 5px 0 0 0; font-size: 12px; line-height: 1.4; color: #666;">
                                            The volunteer will be notified immediately and can track this task in their dashboard.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Action Buttons at Bottom -->
            <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 25px; background: #f8f9fa; border-top: 2px solid #e9ecef; border-radius: 0 0 8px 8px;">
                <div style="display: flex; justify-content: center; gap: 25px;">
                    <button type="button" class="btn btn-secondary" onclick="closeAssignTaskModal()" style="padding: 15px 40px; font-size: 16px; min-width: 180px; background: #ffebee; color: #d32f2f; border: 2px solid #ffcdd2;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" form="assignTaskForm" style="padding: 15px 40px; font-size: 16px; min-width: 180px; background: #e8f5e8; color: #2e7d32; border: 2px solid #c8e6c8;">
                        <i class="fas fa-paper-plane"></i> Assign Task
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Store donation data for modal display
        const donationData = {
            @foreach($pendingDonations as $donation)
            '{{ $donation->donation_id }}': {
                id: '{{ $donation->donation_id }}',
                donorName: '{{ $donation->user->first_name }} {{ $donation->user->last_name }}',
                type: '{{ ucfirst($donation->donation_type) }}',
                amount: '@if($donation->donation_type === "monetary")${{ number_format($donation->amount ?? 0, 2) }}@else{{ $donation->quantity ?? 0 }} {{ $donation->items ?? "items" }}@endif',
                date: '{{ \Carbon\Carbon::parse($donation->donation_date)->format("M d, Y") }}',
                status: '{{ ucfirst($donation->status) }}',
                paymentMethod: '{{ $donation->payment_method ?? "N/A" }}',
                transactionId: '{{ $donation->transaction_id ?? "N/A" }}'
            }@if(!$loop->last),@endif
            @endforeach
        };

        function viewDonation(donationId) {
            const donation = donationData[donationId];
            if (!donation) {
                alert('Donation details not found');
                return;
            }

            // Populate modal with donation data
            document.getElementById('modal-donation-id').textContent = donation.id;
            document.getElementById('modal-donor-name').textContent = donation.donorName;
            document.getElementById('modal-donation-type').textContent = donation.type;
            document.getElementById('modal-amount-items').textContent = donation.amount;
            document.getElementById('modal-date').textContent = donation.date;
            document.getElementById('modal-status').textContent = donation.status;
            document.getElementById('modal-payment-method').textContent = donation.paymentMethod;
            document.getElementById('modal-transaction-id').textContent = donation.transactionId;

            // Show/hide payment info based on donation type
            const paymentInfo = document.getElementById('payment-info');
            const transactionInfo = document.getElementById('transaction-info');
            
            if (donation.type === 'Monetary') {
                paymentInfo.style.display = 'flex';
                transactionInfo.style.display = 'flex';
            } else {
                paymentInfo.style.display = 'none';
                transactionInfo.style.display = 'none';
            }

            // Show modal
            document.getElementById('donationModal').style.display = 'block';
        }

        function closeDonationModal() {
            document.getElementById('donationModal').style.display = 'none';
        }

        // Add Victim Modal functions
        function openAddVictimModal() {
            document.getElementById('addVictimModal').style.display = 'block';
        }

        function closeAddVictimModal() {
            document.getElementById('addVictimModal').style.display = 'none';
        }

        // Add Volunteer Modal functions
        function openAddVolunteerModal() {
            document.getElementById('addVolunteerModal').style.display = 'block';
        }

        function closeAddVolunteerModal() {
            document.getElementById('addVolunteerModal').style.display = 'none';
        }

        // Assign Task Modal functions
        function openAssignTaskModal(volunteerId, volunteerName) {
            document.getElementById('assignTaskModal').style.display = 'block';
            document.getElementById('selectedVolunteerName').textContent = volunteerName;
            document.getElementById('assignVolunteerId').value = volunteerId;
            
            // Set minimum date to today
            const today = new Date();
            const dateString = today.toISOString().slice(0, 16);
            document.querySelector('input[name="due_date"]').min = dateString;
        }

        function closeAssignTaskModal() {
            document.getElementById('assignTaskModal').style.display = 'none';
            document.getElementById('assignTaskForm').reset();
            // Reset visibility of conditional fields
            document.getElementById('victimSelectionGroup').style.display = 'none';
            document.getElementById('customTaskTitleGroup').style.display = 'none';
        }

        // Toggle between victim selection and custom task title
        function toggleTaskTitleInput() {
            const taskTitleType = document.getElementById('taskTitleType').value;
            const victimGroup = document.getElementById('victimSelectionGroup');
            const customGroup = document.getElementById('customTaskTitleGroup');
            const victimSelect = document.getElementById('victimSelect');
            const customTitle = document.getElementById('customTaskTitle');
            
            if (taskTitleType === 'victim_related') {
                victimGroup.style.display = 'block';
                customGroup.style.display = 'none';
                victimSelect.required = true;
                customTitle.required = false;
                customTitle.value = '';
            } else if (taskTitleType === 'other') {
                victimGroup.style.display = 'none';
                customGroup.style.display = 'block';
                victimSelect.required = false;
                customTitle.required = true;
                victimSelect.value = '';
                // Reset auto-filled fields when switching to custom
                document.querySelector('select[name="priority"]').value = '';
                document.querySelector('select[name="task_type"]').value = '';
                document.querySelector('input[name="location"]').value = '';
                document.querySelector('textarea[name="task_description"]').value = '';
            } else {
                victimGroup.style.display = 'none';
                customGroup.style.display = 'none';
                victimSelect.required = false;
                customTitle.required = false;
                victimSelect.value = '';
                customTitle.value = '';
            }
        }

        // Update victim details automatically when a victim is selected
        function updateVictimDetails() {
            const victimSelect = document.getElementById('victimSelect');
            const selectedOption = victimSelect.options[victimSelect.selectedIndex];
            
            if (selectedOption.value) {
                const priority = selectedOption.getAttribute('data-priority');
                const needs = selectedOption.getAttribute('data-needs');
                const location = selectedOption.getAttribute('data-location');
                const familySize = selectedOption.getAttribute('data-family-size');
                
                // Update priority dropdown
                const prioritySelect = document.querySelector('select[name="priority"]');
                prioritySelect.value = priority;
                
                // Auto-suggest task type based on victim needs
                const taskTypeSelect = document.querySelector('select[name="task_type"]');
                const needsLower = needs.toLowerCase();
                
                if (needsLower.includes('food') || needsLower.includes('water') || needsLower.includes('supplies')) {
                    taskTypeSelect.value = 'relief_distribution';
                } else if (needsLower.includes('medical') || needsLower.includes('health') || needsLower.includes('medicine')) {
                    taskTypeSelect.value = 'medical_assistance';
                } else if (needsLower.includes('evacuation') || needsLower.includes('rescue') || needsLower.includes('transport')) {
                    taskTypeSelect.value = 'evacuation_support';
                } else if (needsLower.includes('communication') || needsLower.includes('contact') || needsLower.includes('phone')) {
                    taskTypeSelect.value = 'communication';
                } else {
                    taskTypeSelect.value = 'other';
                }
                
                // Auto-fill location
                document.querySelector('input[name="location"]').value = location;
                
                // Auto-generate task description based on victim details
                const descriptionTextarea = document.querySelector('textarea[name="task_description"]');
                const victimName = selectedOption.text.split(' - ')[0];
                descriptionTextarea.value = `Assist ${victimName} and family (${familySize} members) at ${location}. Priority: ${priority.toUpperCase()}. Specific needs: ${needs}`;
            } else {
                // Clear fields when no victim is selected
                document.querySelector('select[name="priority"]').value = '';
                document.querySelector('select[name="task_type"]').value = '';
                document.querySelector('input[name="location"]').value = '';
                document.querySelector('textarea[name="task_description"]').value = '';
            }
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const donationModal = document.getElementById('donationModal');
            const victimModal = document.getElementById('addVictimModal');
            const volunteerModal = document.getElementById('addVolunteerModal');
            const assignTaskModal = document.getElementById('assignTaskModal');
            
            if (event.target == donationModal) {
                closeDonationModal();
            } else if (event.target == victimModal) {
                closeAddVictimModal();
            } else if (event.target == volunteerModal) {
                closeAddVolunteerModal();
            } else if (event.target == assignTaskModal) {
                closeAssignTaskModal();
            }
        }

        // Volunteer availability toggle functions
        function showAvailable() {
            // Hide unavailable volunteers
            document.querySelectorAll('.unavailable-volunteer').forEach(function(row) {
                row.style.display = 'none';
            });
            
            // Show available volunteers
            document.querySelectorAll('.available-volunteer').forEach(function(row) {
                row.style.display = '';
            });
            
            // Update button states
            document.getElementById('availableBtn').classList.add('active');
            document.getElementById('unavailableBtn').classList.remove('active');
            
            // Update no volunteers message
            const availableCount = document.querySelectorAll('.available-volunteer').length;
            const noVolunteersMessage = document.getElementById('no-volunteers-message');
            if (noVolunteersMessage) {
                document.getElementById('no-volunteers-text').textContent = availableCount === 0 ? 'No available volunteers' : '';
                noVolunteersMessage.style.display = availableCount === 0 ? '' : 'none';
            }
        }

        function showUnavailable() {
            // Hide available volunteers
            document.querySelectorAll('.available-volunteer').forEach(function(row) {
                row.style.display = 'none';
            });
            
            // Show unavailable volunteers
            document.querySelectorAll('.unavailable-volunteer').forEach(function(row) {
                row.style.display = '';
            });
            
            // Update button states
            document.getElementById('unavailableBtn').classList.add('active');
            document.getElementById('availableBtn').classList.remove('active');
            
            // Update no volunteers message
            const unavailableCount = document.querySelectorAll('.unavailable-volunteer').length;
            const noVolunteersMessage = document.getElementById('no-volunteers-message');
            if (noVolunteersMessage) {
                document.getElementById('no-volunteers-text').textContent = unavailableCount === 0 ? 'No unavailable volunteers' : '';
                noVolunteersMessage.style.display = unavailableCount === 0 ? '' : 'none';
            }
        }

        // Chat functionality
        let selectedVolunteerId = null;
        let selectedVolunteerName = '';

        function selectVolunteer(volunteerId, volunteerName) {
            // Remove previous selection
            document.querySelectorAll('.volunteer-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Add selection to clicked item
            event.currentTarget.classList.add('selected');

            selectedVolunteerId = volunteerId;
            selectedVolunteerName = volunteerName;

            // Update chat header
            document.getElementById('selectedVolunteerName').textContent = `Chat with ${volunteerName}`;
            document.getElementById('selectedVolunteerId').value = volunteerId;

            // Load chat messages
            loadChatMessages(volunteerId);
        }

        function loadChatMessages(volunteerId) {
            const chatMessages = document.getElementById('chatMessages');
            
            // Show loading state
            chatMessages.innerHTML = `
                <div class="welcome-message">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading messages...</p>
                </div>
            `;

            // Make AJAX call to load messages
            fetch(`/admin/chat/messages/${volunteerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.messages.length === 0) {
                            chatMessages.innerHTML = `
                                <div class="welcome-message">
                                    <i class="fas fa-comments"></i>
                                    <p>No messages yet. Start the conversation!</p>
                                </div>
                            `;
                        } else {
                            chatMessages.innerHTML = '';
                            data.messages.forEach(message => {
                                if (message.image_path) {
                                    addMessageToChat(
                                        message.sender_type === 'admin' ? 'sent' : 'received',
                                        null,
                                        false,
                                        message.created_at,
                                        message.image_path
                                    );
                                } else {
                                    addMessageToChat(
                                        message.sender_type === 'admin' ? 'sent' : 'received',
                                        message.message,
                                        true,
                                        message.created_at
                                    );
                                }
                            });
                        }
                        // Scroll to bottom
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    } else {
                        chatMessages.innerHTML = `
                            <div class="welcome-message">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Error loading messages: ${data.message}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    chatMessages.innerHTML = `
                        <div class="welcome-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Error loading messages. Please try again.</p>
                        </div>
                    `;
                });
        }

        // Handle chat form submission
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!selectedVolunteerId) {
                alert('Please select a volunteer first');
                return;
            }

            const messageInput = document.getElementById('messageInput');
            const imageInput = document.getElementById('imageInput');
            const message = messageInput.value.trim();
            
            if (!message && !imageInput.files[0]) {
                return;
            }

            // Disable form while sending
            const sendBtn = document.querySelector('.send-btn');
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Create FormData for file upload
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('volunteer_id', selectedVolunteerId);
            if (message) formData.append('message', message);
            if (imageInput.files[0]) formData.append('image', imageInput.files[0]);

            // Send message via AJAX
            fetch('/admin/chat/send', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add message to chat immediately
                    if (data.chat_message.message) {
                        addMessageToChat('sent', data.chat_message.message, true, data.chat_message.created_at);
                    }
                    if (data.chat_message.image_path) {
                        addMessageToChat('sent', data.chat_message.image_url, false, data.chat_message.created_at);
                    }
                    
                    // Clear inputs
                    messageInput.value = '';
                    imageInput.value = '';
                    document.querySelector('label[for="imageInput"]').style.background = '#6c757d';
                    document.querySelector('label[for="imageInput"]').title = 'Send Image';
                } else {
                    alert('Error sending message: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Error sending message. Please try again.');
            })
            .finally(() => {
                // Re-enable form
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            });
        });

        function addMessageToChat(type, content, isText = true, time = null, imagePath = null) {
            const chatMessages = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            
            const currentTime = time || new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            if (isText) {
                messageDiv.innerHTML = `
                    <div class="message-content">
                        <div class="message-text">${content}</div>
                        <div class="message-time">${currentTime}</div>
                    </div>
                `;
            } else {
                const imageUrl = imagePath ? `/storage/${imagePath}` : content;
                messageDiv.innerHTML = `
                    <div class="message-content">
                        <img src="${imageUrl}" alt="Sent image" class="message-image">
                        <div class="message-time">${currentTime}</div>
                    </div>
                `;
            }
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function addImageToChat(type, imageSrc, scrollToBottom = true) {
            addMessageToChat(type, imageSrc, false);
        }

        // Handle image input change
        document.getElementById('imageInput').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                const fileName = e.target.files[0].name;
                const label = document.querySelector('label[for="imageInput"]');
                label.title = `Selected: ${fileName}`;
                label.style.background = '#2980b9';
            }
        });

        // Auto-resize textarea
        document.getElementById('messageInput').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Search volunteers
        document.getElementById('volunteerSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const volunteerItems = document.querySelectorAll('.volunteer-item');
            
            volunteerItems.forEach(item => {
                const volunteerName = item.querySelector('.volunteer-name').textContent.toLowerCase();
                if (volunteerName.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        function refreshChatList() {
            // Make AJAX call to refresh volunteer list
            const refreshBtn = document.getElementById('refreshChatBtn');
            const icon = refreshBtn.querySelector('i');
            
            icon.classList.add('fa-spin');
            
            fetch('/admin/chat/volunteers')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update volunteer list
                        const volunteerItems = document.querySelector('.volunteer-items');
                        volunteerItems.innerHTML = '';
                        
                        if (data.volunteers.length === 0) {
                            volunteerItems.innerHTML = '<div class="no-volunteers">No volunteers available</div>';
                        } else {
                            data.volunteers.forEach(volunteer => {
                                const volunteerDiv = document.createElement('div');
                                volunteerDiv.className = 'volunteer-item';
                                volunteerDiv.onclick = () => selectVolunteer(volunteer.user_id, `${volunteer.first_name} ${volunteer.last_name}`);
                                
                                volunteerDiv.innerHTML = `
                                    <div class="volunteer-info">
                                        <div class="volunteer-name">${volunteer.first_name} ${volunteer.last_name}</div>
                                        <div class="volunteer-status ${volunteer.is_available ? 'online' : 'offline'}">
                                            ${volunteer.is_available ? 'Available' : 'Unavailable'}
                                        </div>
                                    </div>
                                    <div class="volunteer-indicator ${volunteer.is_available ? 'online' : 'offline'}"></div>
                                `;
                                
                                volunteerItems.appendChild(volunteerDiv);
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error refreshing chat list:', error);
                })
                .finally(() => {
                    icon.classList.remove('fa-spin');
                });
        }

        // Allow Enter to send message (Shift+Enter for new line)
        document.getElementById('messageInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('chatForm').dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>
