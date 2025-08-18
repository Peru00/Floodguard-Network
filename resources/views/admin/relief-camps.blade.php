<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Relief Camps Management - FloodGuard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Relief Camps Specific Styles */
        .relief-camps-container {
            padding: 2rem;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .camps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .camp-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .camp-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .camp-card.selected {
            border: 3px solid var(--secondary-color);
            box-shadow: 0 8px 30px rgba(138, 178, 166, 0.3);
        }

        .camp-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .camp-header h3 {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .camp-status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
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

        .camp-info {
            margin-bottom: 1.5rem;
        }

        .camp-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.8rem;
            color: #666;
        }

        .camp-info-item i {
            color: var(--secondary-color);
            width: 20px;
            text-align: center;
        }

        .occupancy-section {
            margin: 1.5rem 0;
        }

        .occupancy-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .occupancy-text {
            font-weight: 600;
            color: var(--primary-color);
        }

        .occupancy-percentage {
            font-size: 0.9rem;
            color: #666;
        }

        .occupancy-bar {
            width: 100%;
            height: 12px;
            background: #f0f0f0;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .occupancy-fill {
            height: 100%;
            border-radius: 6px;
            transition: width 0.3s ease;
        }

        .occupancy-fill.full { 
            background: linear-gradient(90deg, #dc2626, #ef4444); 
        }
        .occupancy-fill.almost-full { 
            background: linear-gradient(90deg, #d97706, #f59e0b); 
        }
        .occupancy-fill.available { 
            background: linear-gradient(90deg, #059669, #10b981); 
        }

        .occupancy-numbers {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: #666;
        }

        .camp-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .stat-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.2rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 2px solid #f0f0f0;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            flex: 1;
            justify-content: center;
        }

        .edit-btn {
            background: #3498db;
            color: white;
        }

        .edit-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .delete-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* Summary Stats */
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
        }

        .summary-icon {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }

        .summary-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .summary-label {
            color: #666;
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }

        /* Section Header */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-title {
            color: var(--primary-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.8rem;
        }

        .section-title i {
            color: var(--secondary-color);
        }

        .btn-primary {
            background: var(--secondary-color);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #7aa396;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(138, 178, 166, 0.4);
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
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 1.5rem 2rem;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.3rem;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #666;
        }

        .modal-body {
            padding: 2rem;
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

        .form-input, .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(138, 178, 166, 0.1);
        }

        .form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #f0f0f0;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            margin: 1rem 0 0.5rem 0;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .relief-camps-container {
                padding: 1rem;
                background-color: #f8f9fa;
            }

            .camps-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .summary-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.8rem;
            }

            .summary-card {
                padding: 0.8rem;
            }

            .summary-icon {
                font-size: 1.4rem;
                margin-bottom: 0.3rem;
            }

            .summary-number {
                font-size: 1.2rem;
            }

            .section-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .camp-stats {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }
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
            <li class="active"><a href="{{ route('admin.relief-camps') }}"><i class="fas fa-campground"></i> Relief Camps</a></li>
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
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="admin-container" style="background-color: #f8f9fa;">
        <div class="relief-camps-container">
            <!-- Page Header -->
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-campground"></i>
                    Relief Camps Management
                </h2>
                <button class="btn-primary" onclick="openAddCampModal()">
                    <i class="fas fa-plus"></i>
                    Add New Camp
                </button>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul style="margin: 0.5rem 0 0 0; padding-left: 1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Summary Statistics -->
            <div class="summary-stats">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-campground"></i>
                    </div>
                    <span class="summary-number">{{ $stats['total_camps'] }}</span>
                    <div class="summary-label">Total Camps</div>
                </div>
                
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="summary-number">{{ number_format($stats['total_capacity']) }}</span>
                    <div class="summary-label">Total Capacity</div>
                </div>
                
                <div class="summary-card" id="current-occupancy-card">
                    <div class="summary-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <span class="summary-number" id="current-occupancy-number">{{ number_format($stats['total_occupancy']) }}</span>
                    <div class="summary-label">Current Occupancy</div>
                </div>
                
                <div class="summary-card" id="available-spaces-card">
                    <div class="summary-icon">
                        <i class="fas fa-bed"></i>
                    </div>
                    <span class="summary-number" id="available-spaces-number">{{ number_format($stats['available_spaces']) }}</span>
                    <div class="summary-label">Available Spaces</div>
                </div>
            </div>

            <!-- Relief Camps Grid -->
            @if($camps->count() > 0)
                <div class="camps-grid">
                    @foreach($camps as $camp)
                        <div class="camp-card" onclick="selectCamp('{{ $camp->camp_id }}', {{ $camp->current_occupancy }}, {{ $camp->available_spaces }})">
                            <div class="camp-header">
                                <h3>{{ $camp->camp_name }}</h3>
                                <span class="camp-status-badge {{ $camp->occupancy_status }}">
                                    @if($camp->occupancy_status === 'full')
                                        <i class="fas fa-exclamation-triangle"></i> Full
                                    @elseif($camp->occupancy_status === 'almost-full')
                                        <i class="fas fa-exclamation"></i> Almost Full
                                    @else
                                        <i class="fas fa-check-circle"></i> Available
                                    @endif
                                </span>
                            </div>

                            <div class="camp-info">
                                <div class="camp-info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $camp->location }}</span>
                                </div>
                                
                                <div class="camp-info-item">
                                    <i class="fas fa-user-tie"></i>
                                    <span>
                                        @if($camp->manager)
                                            Managed by {{ $camp->manager->first_name }} {{ $camp->manager->last_name }}
                                        @else
                                            <em>No manager assigned</em>
                                        @endif
                                    </span>
                                </div>

                                <div class="camp-info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Last updated: {{ $camp->last_updated->format('M d, Y g:i A') }}</span>
                                </div>
                            </div>

                            <div class="occupancy-section">
                                <div class="occupancy-header">
                                    <span class="occupancy-text">Occupancy</span>
                                    <span class="occupancy-percentage">{{ number_format($camp->occupancy_percentage, 1) }}%</span>
                                </div>
                                
                                <div class="occupancy-bar">
                                    <div class="occupancy-fill {{ $camp->occupancy_status }}" 
                                         style="width: {{ $camp->occupancy_percentage }}%"></div>
                                </div>
                                
                                <div class="occupancy-numbers">
                                    <span>{{ $camp->current_occupancy }} / {{ $camp->capacity }}</span>
                                    <span>{{ $camp->available_spaces }} available</span>
                                </div>
                            </div>

                            <div class="camp-stats">
                                <div class="stat-item">
                                    <span class="stat-number">{{ $camp->capacity }}</span>
                                    <div class="stat-label">Max Capacity</div>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number">{{ $camp->current_occupancy }}</span>
                                    <div class="stat-label">Current</div>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button class="action-btn edit-btn" onclick="editCamp('{{ $camp->camp_id }}')">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteCamp('{{ $camp->camp_id }}', '{{ addslashes($camp->camp_name) }}')">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-campground"></i>
                    <h3>No Relief Camps Found</h3>
                    <p>Start by creating your first relief camp to manage flood victims accommodation.</p>
                    <button class="btn-primary" onclick="openAddCampModal()" style="margin-top: 1rem;">
                        <i class="fas fa-plus"></i>
                        Create First Camp
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Camp Modal -->
    <div id="addCampModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Add New Relief Camp</h3>
                <span class="close" onclick="closeAddCampModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.relief-camps.create') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="camp_name">
                            <i class="fas fa-campground"></i> Camp Name *
                        </label>
                        <input type="text" id="camp_name" name="camp_name" class="form-input" 
                               placeholder="Enter camp name" required maxlength="100">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="location">
                            <i class="fas fa-map-marker-alt"></i> Location *
                        </label>
                        <input type="text" id="location" name="location" class="form-input" 
                               placeholder="Enter camp location" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="capacity">
                            <i class="fas fa-users"></i> Capacity *
                        </label>
                        <input type="number" id="capacity" name="capacity" class="form-input" 
                               placeholder="Maximum number of people" required min="1" max="10000">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="managed_by">
                            <i class="fas fa-user-tie"></i> Camp Manager (Optional)
                        </label>
                        <select id="managed_by" name="managed_by" class="form-select">
                            <option value="">Select a volunteer manager</option>
                            @foreach($volunteers as $volunteer)
                                <option value="{{ $volunteer->user_id }}">
                                    {{ $volunteer->first_name }} {{ $volunteer->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-buttons">
                        <button type="button" class="btn-secondary" onclick="closeAddCampModal()">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Create Camp
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Camp Modal -->
    <div id="editCampModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit Relief Camp</h3>
                <span class="close" onclick="closeEditCampModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" id="editCampForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label class="form-label" for="edit_camp_name">
                            <i class="fas fa-campground"></i> Camp Name *
                        </label>
                        <input type="text" id="edit_camp_name" name="camp_name" class="form-input" 
                               required maxlength="100">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_location">
                            <i class="fas fa-map-marker-alt"></i> Location *
                        </label>
                        <input type="text" id="edit_location" name="location" class="form-input" 
                               required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_capacity">
                            <i class="fas fa-users"></i> Capacity *
                        </label>
                        <input type="number" id="edit_capacity" name="capacity" class="form-input" 
                               required min="1" max="10000">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_current_occupancy">
                            <i class="fas fa-user-friends"></i> Current Occupancy *
                        </label>
                        <input type="number" id="edit_current_occupancy" name="current_occupancy" class="form-input" 
                               required min="0">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_managed_by">
                            <i class="fas fa-user-tie"></i> Camp Manager (Optional)
                        </label>
                        <select id="edit_managed_by" name="managed_by" class="form-select">
                            <option value="">Select a volunteer manager</option>
                            @foreach($volunteers as $volunteer)
                                <option value="{{ $volunteer->user_id }}">
                                    {{ $volunteer->first_name }} {{ $volunteer->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-buttons">
                        <button type="button" class="btn-secondary" onclick="closeEditCampModal()">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Update Camp
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Store camp data for editing
        const campData = {
            @foreach($camps as $camp)
            '{{ $camp->camp_id }}': {
                camp_id: '{{ $camp->camp_id }}',
                camp_name: '{{ addslashes($camp->camp_name) }}',
                location: '{{ addslashes($camp->location) }}',
                capacity: {{ $camp->capacity }},
                current_occupancy: {{ $camp->current_occupancy }},
                managed_by: '{{ $camp->managed_by ?? "" }}'
            }@if(!$loop->last),@endif
            @endforeach
        };

        // Camp Selection Function
        function selectCamp(campId, currentOccupancy, availableSpaces) {
            // Remove previous selection
            document.querySelectorAll('.camp-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selection to clicked card
            event.currentTarget.classList.add('selected');
            
            // Update summary cards with selected camp data
            document.getElementById('current-occupancy-number').textContent = currentOccupancy;
            document.getElementById('available-spaces-number').textContent = availableSpaces;
            
            // Add visual feedback to the cards
            const occupancyCard = document.getElementById('current-occupancy-card');
            const spacesCard = document.getElementById('available-spaces-card');
            
            // Reset previous highlights
            document.querySelectorAll('.summary-card').forEach(card => {
                card.style.background = 'white';
            });
            
            // Highlight the updated cards
            occupancyCard.style.background = 'linear-gradient(135deg, #e3f2fd, #f8f9fa)';
            spacesCard.style.background = 'linear-gradient(135deg, #e8f5e8, #f8f9fa)';
            
            setTimeout(() => {
                occupancyCard.style.background = 'white';
                spacesCard.style.background = 'white';
            }, 2000);
        }

        // Add Camp Modal Functions
        function openAddCampModal() {
            document.getElementById('addCampModal').style.display = 'block';
        }

        function closeAddCampModal() {
            document.getElementById('addCampModal').style.display = 'none';
        }

        // Edit Camp Functions
        function editCamp(campId) {
            const camp = campData[campId];
            if (!camp) {
                alert('Camp data not found');
                return;
            }

            // Populate form fields
            document.getElementById('edit_camp_name').value = camp.camp_name;
            document.getElementById('edit_location').value = camp.location;
            document.getElementById('edit_capacity').value = camp.capacity;
            document.getElementById('edit_current_occupancy').value = camp.current_occupancy;
            document.getElementById('edit_managed_by').value = camp.managed_by;

            // Set form action
            document.getElementById('editCampForm').action = `/admin/relief-camps/${campId}`;

            // Show modal
            document.getElementById('editCampModal').style.display = 'block';
        }

        function closeEditCampModal() {
            document.getElementById('editCampModal').style.display = 'none';
        }

        // Delete Camp Function
        function deleteCamp(campId, campName) {
            if (confirm(`Are you sure you want to delete "${campName}"? This action cannot be undone.`)) {
                // Create a form and submit DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/relief-camps/${campId}`;
                
                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
                
                // Add method override for DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                // Submit form
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addCampModal');
            const editModal = document.getElementById('editCampModal');
            
            if (event.target == addModal) {
                closeAddCampModal();
            }
            if (event.target == editModal) {
                closeEditCampModal();
            }
        }

        // Form validation for capacity vs occupancy
        document.getElementById('edit_capacity').addEventListener('input', function() {
            const capacity = parseInt(this.value);
            const occupancy = parseInt(document.getElementById('edit_current_occupancy').value);
            
            if (occupancy > capacity) {
                document.getElementById('edit_current_occupancy').value = capacity;
            }
            document.getElementById('edit_current_occupancy').max = capacity;
        });

        document.getElementById('edit_current_occupancy').addEventListener('input', function() {
            const capacity = parseInt(document.getElementById('edit_capacity').value);
            const occupancy = parseInt(this.value);
            
            if (occupancy > capacity) {
                this.value = capacity;
                alert('Current occupancy cannot exceed camp capacity');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
