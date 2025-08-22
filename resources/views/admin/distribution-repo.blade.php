<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Distribution Repository - Floodguard Network</title>
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
        .stat-icon.completed { background: #8ab2a6; }
        .stat-icon.ongoing { background: #8ab2a6; }
        .stat-icon.scheduled { background: #8ab2a6; }
        .stat-icon.beneficiaries { background: #8ab2a6; }

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

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-info {
            background: #17a2b8;
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

        .badge.scheduled {
            background-color: #9C27B0;
            color: #ffffff;
        }

        .badge.ongoing {
            background-color: #ffc107;
            color: #000000;
            font-weight: 700;
        }

        .badge.completed {
            background-color: #28a745;
            color: #ffffff;
        }

        .badge.cancelled {
            background-color: #dc3545;
            color: #ffffff;
        }

        .badge.high {
            background-color: #dc3545;
            color: #ffffff;
        }

        .badge.medium {
            background-color: #ffc107;
            color: #000000;
            font-weight: 700;
        }

        .badge.low {
            background-color: #6c757d;
            color: #ffffff;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.5rem 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .view-btn {
            background-color: #007bff;
        }

        .action-btn:hover {
            opacity: 0.8;
        }

        .pagination {
            display: flex;
            justify-content: center;
            padding: 2rem;
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
            max-width: 800px;
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
            max-height: 60vh;
            overflow-y: auto;
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

        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-top: 0.5rem;
        }

        .item-list {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-top: 1rem;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .urgent-row {
            border-left: 4px solid #ff4757;
        }

        .location-cell {
            max-width: 200px;
            word-wrap: break-word;
        }

        .date-cell {
            white-space: nowrap;
        }

        .map-container {
            height: 300px;
            background: #f0f0f0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            margin: 1rem 0;
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
            <li><a href="{{ route('admin.donations') }}"><i class="fas fa-donate"></i> Donations</a></li>
            <li class="active"><a href="{{ route('admin.distribution-repo') }}"><i class="fas fa-truck"></i> Distribution Repo</a></li>
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
                <h2><i class="fas fa-truck"></i> Distribution Repository</h2>
                <div class="section-actions">
                    <button class="btn btn-secondary" onclick="toggleFilters()">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.distribution-repo', ['export' => 'csv']) }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Export Report
                    </a>
                    <button class="btn btn-success" onclick="openAddDistributionModal()">
                        <i class="fas fa-plus"></i> Schedule Distribution
                    </button>
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

            <!-- Distribution Summary Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['total_distributions']) }}</h3>
                        <p>Total Distributions</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon completed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['completed_distributions']) }}</h3>
                        <p>Completed</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon ongoing">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['total_items_distributed']) }}</h3>
                        <p>Items Distributed</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon scheduled">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['unique_beneficiaries']) }}</h3>
                        <p>Unique Beneficiaries</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon beneficiaries">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['total_distributions']) }}</h3>
                        <p>Total Records</p>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filters" id="filterSection" style="display: none;">
                <form method="GET" action="{{ route('admin.distribution-repo') }}">
                    <div class="filter-row">
                        <div class="form-group">
                            <label class="form-label">Search Distributions</label>
                            <input type="text" name="search" class="form-input" placeholder="Search by location or task ID..." value="{{ request('search') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="">All Priorities</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Distribution Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Distribution ID</th>
                            <th>Location</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Victim</th>
                            <th>Volunteer</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distributions as $distribution)
                            <tr>
                                <td><strong>{{ $distribution->distribution_id }}</strong></td>
                                <td class="location-cell">
                                    <div>
                                        <strong>{{ $distribution->location }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $distribution->item_name ?? 'N/A' }}</strong>
                                </td>
                                <td><strong>{{ number_format($distribution->quantity) }}</strong></td>
                                <td>{{ $distribution->victim_name ?? 'N/A' }}</td>
                                <td>
                                    <div>
                                        @if($distribution->volunteer_first_name)
                                            <strong>{{ $distribution->volunteer_first_name }} {{ $distribution->volunteer_last_name }}</strong>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td class="date-cell">{{ $distribution->distribution_date ? \Carbon\Carbon::parse($distribution->distribution_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn view-btn" onclick="viewDistribution('{{ $distribution->distribution_id }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: #666;">
                                    <i class="fas fa-truck" style="font-size: 3rem; color: #ddd; display: block; margin-bottom: 1rem;"></i>
                                    No distribution records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if($distributions->hasPages())
                    <div class="pagination">
                        {{ $distributions->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- View Distribution Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-eye"></i> Distribution Details</h3>
                <button class="close" onclick="closeViewModal()">&times;</button>
            </div>
            <div class="modal-body" id="distributionDetails">
                <!-- Distribution details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Track Distribution Modal -->
    <div id="trackModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-route"></i> Track Distribution</h3>
                <button class="close" onclick="closeTrackModal()">&times;</button>
            </div>
            <div class="modal-body" id="trackingDetails">
                <!-- Tracking details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeTrackModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Add Distribution Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Schedule New Distribution</h3>
                <button class="close" onclick="closeAddModal()">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.distribution-repo.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Distribution Location *</label>
                        <input type="text" name="distribution_location" class="form-input" required placeholder="Enter distribution location">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Barangay</label>
                        <input type="text" name="barangay" class="form-input" placeholder="Enter barangay">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Team Leader *</label>
                        <input type="text" name="team_leader" class="form-input" required placeholder="Enter team leader name">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="form-input" placeholder="Enter contact number">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Beneficiaries Count *</label>
                        <input type="number" name="beneficiaries_count" class="form-input" min="1" required placeholder="Number of beneficiaries">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Priority Level *</label>
                        <select name="priority" class="form-select" required>
                            <option value="">Select Priority</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Scheduled Date *</label>
                        <input type="date" name="scheduled_date" class="form-input" required min="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Special Instructions</label>
                        <textarea name="special_instructions" class="form-input" rows="3" placeholder="Any special instructions or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule Distribution</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleFilters() {
            const filterSection = document.getElementById('filterSection');
            filterSection.style.display = filterSection.style.display === 'none' ? 'block' : 'none';
        }

        function viewDistribution(taskId) {
            const modal = document.getElementById('viewModal');
            const detailsDiv = document.getElementById('distributionDetails');
            
            detailsDiv.innerHTML = `
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
                    <p>Loading distribution details...</p>
                </div>
            `;
            
            modal.style.display = 'block';
            
            // Simulate API call
            setTimeout(() => {
                detailsDiv.innerHTML = `
                    <div class="detail-row">
                        <span class="detail-label">Task ID:</span>
                        <span class="detail-value">${taskId}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Location:</span>
                        <span class="detail-value">Barangay Sample, Sample City</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Team Leader:</span>
                        <span class="detail-value">Juan Dela Cruz</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Contact:</span>
                        <span class="detail-value">+63 912 345 6789</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Beneficiaries:</span>
                        <span class="detail-value">150 families</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value"><span class="badge ongoing">Ongoing</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Priority:</span>
                        <span class="detail-value"><span class="badge high">High</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Scheduled Date:</span>
                        <span class="detail-value">December 20, 2024</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Progress:</span>
                        <span class="detail-value">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 65%"></div>
                            </div>
                            <div class="progress-text">65% Complete</div>
                        </span>
                    </div>
                    <div class="item-list">
                        <h4 style="margin-bottom: 1rem; color: var(--primary-color);">Relief Items Distributed:</h4>
                        <div class="item-row">
                            <span>Rice Packs (5kg)</span>
                            <span>150 packs</span>
                        </div>
                        <div class="item-row">
                            <span>Canned Goods</span>
                            <span>300 cans</span>
                        </div>
                        <div class="item-row">
                            <span>Drinking Water (1L)</span>
                            <span>450 bottles</span>
                        </div>
                        <div class="item-row">
                            <span>Hygiene Kits</span>
                            <span>150 kits</span>
                        </div>
                    </div>
                `;
            }, 1000);
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        function trackDistribution(taskId) {
            const modal = document.getElementById('trackModal');
            const detailsDiv = document.getElementById('trackingDetails');
            
            detailsDiv.innerHTML = `
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
                    <p>Loading tracking information...</p>
                </div>
            `;
            
            modal.style.display = 'block';
            
            // Simulate API call
            setTimeout(() => {
                detailsDiv.innerHTML = `
                    <div class="detail-row">
                        <span class="detail-label">Current Status:</span>
                        <span class="detail-value"><span class="badge ongoing">En Route</span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Last Update:</span>
                        <span class="detail-value">5 minutes ago</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Team Location:</span>
                        <span class="detail-value">Approaching destination (2.3 km away)</span>
                    </div>
                    <div class="map-container">
                        <div style="text-align: center;">
                            <i class="fas fa-map-marked-alt" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                            <p>Live Tracking Map</p>
                            <p style="font-size: 0.9rem; color: #999;">GPS coordinates would be displayed here</p>
                        </div>
                    </div>
                    <div style="margin-top: 1rem;">
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Distribution Timeline:</h4>
                        <div style="border-left: 3px solid var(--primary-color); padding-left: 1rem;">
                            <div style="margin-bottom: 1rem;">
                                <strong>08:00 AM</strong> - Team departed from warehouse
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <strong>09:30 AM</strong> - Arrived at first checkpoint
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <strong>11:15 AM</strong> - Currently en route to destination
                            </div>
                            <div style="color: #999;">
                                <strong>12:30 PM</strong> - Expected arrival (ETA)
                            </div>
                        </div>
                    </div>
                `;
            }, 1000);
        }

        function closeTrackModal() {
            document.getElementById('trackModal').style.display = 'none';
        }

        function openAddDistributionModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function updateStatus(taskId, newStatus) {
            if (confirm(`Are you sure you want to mark this distribution as ${newStatus}?`)) {
                // In a real application, you would make an AJAX call here
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/distribution-repo/${taskId}/status`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = newStatus;
                
                form.appendChild(csrfToken);
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const viewModal = document.getElementById('viewModal');
            const trackModal = document.getElementById('trackModal');
            const addModal = document.getElementById('addModal');
            
            if (event.target == viewModal) {
                closeViewModal();
            }
            if (event.target == trackModal) {
                closeTrackModal();
            }
            if (event.target == addModal) {
                closeAddModal();
            }
        }
    </script>
</body>
</html>
