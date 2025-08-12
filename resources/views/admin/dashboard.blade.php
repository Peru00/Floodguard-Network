<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* Button toggle styles */
        .btn.active {
            background-color: #3498db !important;
            color: white !important;
        }

        .btn:not(.active) {
            background-color: #ecf0f1;
            color: #7f8c8d;
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
            <li><a href="#"><i class="fas fa-campground"></i> Relief Camps</a></li>
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
                                    </tr>
                                @empty
                                    <tr id="no-volunteers-message">
                                        <td colspan="5" style="text-align: center; padding: 2rem; color: #666;">
                                            <i class="fas fa-hands-helping"></i><br>
                                            <span id="no-volunteers-text">No available volunteers</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const donationModal = document.getElementById('donationModal');
            const victimModal = document.getElementById('addVictimModal');
            const volunteerModal = document.getElementById('addVolunteerModal');
            
            if (event.target == donationModal) {
                closeDonationModal();
            } else if (event.target == victimModal) {
                closeAddVictimModal();
            } else if (event.target == volunteerModal) {
                closeAddVolunteerModal();
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
    </script>
</body>
</html>
