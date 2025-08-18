<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Relief Camp - Volunteer Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Relief Camp Specific Styles */
        .relief-camp-container {
            padding: 2rem;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        /* Camp Overview Card */
        .camp-overview {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .camp-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .camp-title {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .camp-title i {
            color: var(--secondary-color);
        }

        .camp-status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .camp-status-badge.full {
            background: #fee2e2;
            color: #dc2626;
        }

        .camp-status-badge.almost-full {
            background: #fef3c7;
            color: #d97706;
        }

        .camp-status-badge.available {
            background: #d1fae5;
            color: #059669;
        }

        /* Camp Info Grid */
        .camp-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid var(--secondary-color);
        }

        .info-card h4 {
            color: var(--primary-color);
            margin: 0 0 1rem 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-card p {
            margin: 0.5rem 0;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-card i {
            color: var(--secondary-color);
            width: 16px;
        }

        /* Occupancy Section */
        .occupancy-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .section-title {
            color: var(--primary-color);
            margin: 0 0 1.5rem 0;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .occupancy-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--secondary-color);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stat-icon {
            font-size: 1.2rem;
            color: var(--secondary-color);
        }

        /* Occupancy Progress Bar */
        .occupancy-progress {
            margin: 2rem 0;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .progress-text {
            font-weight: 600;
            color: var(--primary-color);
        }

        .progress-percentage {
            font-size: 1.1rem;
            font-weight: 700;
            color: #666;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
            position: relative;
        }

        .progress-fill.full { 
            background: linear-gradient(90deg, #dc2626, #ef4444); 
        }
        .progress-fill.almost-full { 
            background: linear-gradient(90deg, #d97706, #f59e0b); 
        }
        .progress-fill.available { 
            background: linear-gradient(90deg, #059669, #10b981); 
        }

        .progress-details {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #666;
        }

        /* Management Actions */
        .management-actions {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .action-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            border-color: var(--secondary-color);
        }

        .action-icon {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .action-card h4 {
            color: var(--primary-color);
            margin: 0 0 1rem 0;
            font-size: 1.1rem;
        }

        .action-card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            line-height: 1.4;
        }

        .btn-action {
            background: var(--secondary-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action:hover {
            background: #7aa396;
            transform: translateY(-1px);
        }

        /* Update Forms */
        .update-form {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(138, 178, 166, 0.1);
        }

        .btn-update {
            background: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-update:hover {
            background: #3a4a5c;
            transform: translateY(-1px);
        }

        /* No Camp Assigned */
        .no-camp {
            background: white;
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .no-camp i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1.5rem;
        }

        .no-camp h3 {
            color: var(--primary-color);
            margin: 0 0 1rem 0;
        }

        .no-camp p {
            color: #666;
            margin: 0;
        }

        /* Alert Styles */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
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

        .alert i {
            font-size: 1.2rem;
        }

        /* Custom Navbar Styling */
        .navbar {
            background: #525470 !important;
            padding: 0.75rem 2rem !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15) !important;
        }

        .navbar .logo h1 {
            font-size: 1.8rem !important;
            font-weight: 600 !important;
            color: white !important;
        }

        .navbar .logo i {
            color: #8AB2A6 !important;
            font-size: 1.8rem !important;
        }

        .nav-links {
            display: flex !important;
            align-items: center !important;
            gap: 2.5rem !important;
        }

        .nav-links li a {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            padding: 8px 16px !important;
            border-radius: 6px !important;
            transition: all 0.3s ease !important;
            color: #E8E9ED !important;
            text-decoration: none !important;
            font-weight: 500 !important;
        }

        .nav-links li a:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }

        .nav-links li a.active {
            background-color: #8AB2A6 !important;
            color: white !important;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .relief-camp-container {
                padding: 1rem;
            }

            .camp-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .camp-info-grid {
                grid-template-columns: 1fr;
            }

            .occupancy-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 1rem !important;
            }

            .nav-links {
                gap: 1rem !important;
            }

            .nav-links li a span {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-hands-helping"></i>
            <h1>FloodGuard Volunteer</h1>
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="{{ route('volunteer.dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="{{ route('volunteer.relief-camps') }}" class="active"><i class="fas fa-campground"></i> <span>My Relief Camp</span></a></li>
            <li><a href="{{ route('volunteer.inventory') }}"><i class="fas fa-boxes"></i> <span>Inventory</span></a></li>
            <li><a href="{{ route('volunteer.victims') }}"><i class="fas fa-users"></i> <span>Victims</span></a></li>
            <li><a href="{{ route('volunteer.distribution-repository') }}"><i class="fas fa-truck"></i> <span>Distribution Repo</span></a></li>
            <li>
                <div class="admin-profile" style="cursor: pointer;" onclick="window.location.href='{{ route('volunteer.edit-profile') }}'">
                    @if($volunteerInfo->profile_picture)
                        <img src="{{ asset('storage/' . $volunteerInfo->profile_picture) }}" alt="Volunteer Profile">
                    @else
                        <img src="{{ asset('assets/profile-user.png') }}" alt="Volunteer Profile">
                    @endif
                    <div>
                        <p>{{ $volunteerInfo->first_name }} {{ $volunteerInfo->last_name }}</p>
                        <small>Volunteer</small>
                    </div>
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
        <div class="relief-camp-container">
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

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($assignedCamp)
                <!-- Camp Overview -->
                <div class="camp-overview">
                    <div class="camp-header">
                        <h1 class="camp-title">
                            <i class="fas fa-campground"></i>
                            {{ $assignedCamp->camp_name }}
                        </h1>
                        <span class="camp-status-badge {{ $assignedCamp->occupancy_status }}">
                            @if($assignedCamp->occupancy_status === 'full')
                                <i class="fas fa-exclamation-triangle"></i> Full
                            @elseif($assignedCamp->occupancy_status === 'almost-full')
                                <i class="fas fa-exclamation"></i> Almost Full
                            @else
                                <i class="fas fa-check-circle"></i> Available
                            @endif
                        </span>
                    </div>

                    <div class="camp-info-grid">
                        <div class="info-card">
                            <h4><i class="fas fa-map-marker-alt"></i> Location Details</h4>
                            <p><i class="fas fa-location-arrow"></i> {{ $assignedCamp->location }}</p>
                            <p><i class="fas fa-calendar"></i> Established: {{ $assignedCamp->created_at->format('F j, Y') }}</p>
                            <p><i class="fas fa-clock"></i> Last updated: {{ $assignedCamp->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                        
                        <div class="info-card">
                            <h4><i class="fas fa-users"></i> Capacity Information</h4>
                            <p><i class="fas fa-home"></i> Total Capacity: {{ $assignedCamp->capacity }} people</p>
                            <p><i class="fas fa-user-friends"></i> Current Occupancy: {{ $assignedCamp->current_occupancy }} people</p>
                            <p><i class="fas fa-bed"></i> Available Spaces: {{ $assignedCamp->available_spaces }} spaces</p>
                        </div>
                        
                        <div class="info-card">
                            <h4><i class="fas fa-user-tie"></i> Management Info</h4>
                            <p><i class="fas fa-id-badge"></i> Manager: {{ $volunteerInfo->first_name }} {{ $volunteerInfo->last_name }}</p>
                            <p><i class="fas fa-phone"></i> Contact: {{ $volunteerInfo->phone ?? 'Not provided' }}</p>
                            <p><i class="fas fa-envelope"></i> Email: {{ $volunteerInfo->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Occupancy Statistics -->
                <div class="occupancy-section">
                    <h2 class="section-title">
                        <i class="fas fa-chart-bar"></i> Occupancy Statistics
                    </h2>

                    <div class="occupancy-stats">
                        <div class="stat-card">
                            <span class="stat-number">{{ $assignedCamp->capacity }}</span>
                            <div class="stat-label">Total Capacity</div>
                            <i class="stat-icon fas fa-home"></i>
                        </div>
                        
                        <div class="stat-card">
                            <span class="stat-number">{{ $assignedCamp->current_occupancy }}</span>
                            <div class="stat-label">Current Occupancy</div>
                            <i class="stat-icon fas fa-users"></i>
                        </div>
                        
                        <div class="stat-card">
                            <span class="stat-number">{{ $assignedCamp->available_spaces }}</span>
                            <div class="stat-label">Available Spaces</div>
                            <i class="stat-icon fas fa-bed"></i>
                        </div>
                        
                        <div class="stat-card">
                            <span class="stat-number">{{ number_format($assignedCamp->occupancy_percentage, 1) }}%</span>
                            <div class="stat-label">Occupancy Rate</div>
                            <i class="stat-icon fas fa-percentage"></i>
                        </div>
                    </div>

                    <div class="occupancy-progress">
                        <div class="progress-header">
                            <span class="progress-text">Current Occupancy Level</span>
                            <span class="progress-percentage">{{ number_format($assignedCamp->occupancy_percentage, 1) }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill {{ $assignedCamp->occupancy_status }}" 
                                 style="width: {{ $assignedCamp->occupancy_percentage }}%"></div>
                        </div>
                        <div class="progress-details">
                            <span>{{ $assignedCamp->current_occupancy }} / {{ $assignedCamp->capacity }} occupied</span>
                            <span>{{ $assignedCamp->available_spaces }} spaces remaining</span>
                        </div>
                    </div>
                </div>

                <!-- Update Occupancy Form -->
                <div class="update-form">
                    <h2 class="section-title">
                        <i class="fas fa-edit"></i> Update Camp Occupancy
                    </h2>

                    <form method="POST" action="{{ route('volunteer.update-camp-occupancy') }}">
                        @csrf
                        <input type="hidden" name="camp_id" value="{{ $assignedCamp->camp_id }}">
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="current_occupancy" class="form-label">
                                    <i class="fas fa-users"></i> Current Occupancy
                                </label>
                                <input type="number" 
                                       id="current_occupancy" 
                                       name="current_occupancy" 
                                       class="form-input"
                                       value="{{ $assignedCamp->current_occupancy }}"
                                       min="0" 
                                       max="{{ $assignedCamp->capacity }}"
                                       required>
                                <small style="color: #666; font-size: 0.85rem;">Maximum capacity: {{ $assignedCamp->capacity }} people</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="update_notes" class="form-label">
                                    <i class="fas fa-sticky-note"></i> Update Notes (Optional)
                                </label>
                                <input type="text" 
                                       id="update_notes" 
                                       name="update_notes" 
                                       class="form-input"
                                       placeholder="Brief note about this update...">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-update">
                            <i class="fas fa-save"></i> Update Occupancy
                        </button>
                    </form>
                </div>

                <!-- Management Actions -->
                <div class="management-actions">
                    <h2 class="section-title">
                        <i class="fas fa-tools"></i> Camp Management Actions
                    </h2>

                    <div class="actions-grid">
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h4>Check-in Victims</h4>
                            <p>Register new victims arriving at the relief camp and update occupancy records.</p>
                            <button class="btn-action" onclick="window.location.href='{{ route('volunteer.victims') }}'">
                                <i class="fas fa-arrow-right"></i> Manage Victims
                            </button>
                        </div>
                        
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h4>Inventory Management</h4>
                            <p>Track and manage supplies, food, and resources available at the camp.</p>
                            <button class="btn-action" onclick="window.location.href='{{ route('volunteer.inventory') }}'">
                                <i class="fas fa-arrow-right"></i> View Inventory
                            </button>
                        </div>
                        
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h4>Distribution Tasks</h4>
                            <p>Coordinate relief distribution activities and track completed deliveries.</p>
                            <button class="btn-action" onclick="window.location.href='{{ route('volunteer.distribution-repository') }}'">
                                <i class="fas fa-arrow-right"></i> Distribution Repo
                            </button>
                        </div>
                        
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4>Generate Reports</h4>
                            <p>Create detailed reports on camp status, occupancy trends, and activities.</p>
                            <button class="btn-action" onclick="generateCampReport()">
                                <i class="fas fa-arrow-right"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>

            @else
                <!-- No Camp Assigned -->
                <div class="no-camp">
                    <i class="fas fa-campground"></i>
                    <h3>No Relief Camp Assigned</h3>
                    <p>You are not currently assigned to manage any relief camp. Please contact your administrator to get assigned to a camp.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Set up CSRF token for AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Form validation
            const occupancyInput = document.getElementById('current_occupancy');
            if (occupancyInput) {
                occupancyInput.addEventListener('input', function() {
                    const value = parseInt(this.value);
                    const max = parseInt(this.max);
                    
                    if (value > max) {
                        this.setCustomValidity(`Occupancy cannot exceed camp capacity of ${max} people`);
                    } else if (value < 0) {
                        this.setCustomValidity('Occupancy cannot be negative');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        });

        // Generate Camp Report Function
        function generateCampReport() {
            @if($assignedCamp)
                // Show loading state
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
                button.disabled = true;
                
                // Make request to generate report
                fetch('{{ route('volunteer.generate-camp-report') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        camp_id: '{{ $assignedCamp->camp_id }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Open report in new window for viewing
                        const reportWindow = window.open('', '_blank');
                        reportWindow.document.write(data.report_html);
                        reportWindow.document.close();
                        
                        // Show download option
                        showAlert(
                            `Report generated successfully! <a href="${data.download_url}" style="color: white; text-decoration: underline;" target="_blank">Click here to download</a>`, 
                            'success'
                        );
                    } else {
                        showAlert(data.message || 'Failed to generate report', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error generating report:', error);
                    showAlert('Error generating report. Please try again.', 'danger');
                })
                .finally(() => {
                    // Restore button state
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            @else
                showAlert('No camp assigned to generate report for.', 'danger');
            @endif
        }
        
        // Helper function to show alerts
        function showAlert(message, type) {
            const alertContainer = document.querySelector('.relief-camp-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            alertContainer.insertBefore(alert, alertContainer.firstChild);
            
            // Auto-hide after 8 seconds for success (longer for download links), 5 for others
            const hideTimeout = type === 'success' ? 8000 : 5000;
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => alert.remove(), 500);
            }, hideTimeout);
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
