<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Victims - HelpHub</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        
        .nav-links li a i {
            font-size: 1rem !important;
        }
        
        .admin-profile {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
            transition: background-color 0.3s ease !important;
        }
        
        .admin-profile:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        .admin-profile img {
            width: 35px !important;
            height: 35px !important;
            border-radius: 50% !important;
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
        }
        
        .admin-profile div p {
            color: white !important;
            font-weight: 600 !important;
            margin: 0 !important;
            font-size: 0.9rem !important;
        }
        
        .admin-profile div small {
            color: #B8BCC8 !important;
            font-size: 0.75rem !important;
        }
        
        .logout-btn {
            margin-left: 10px !important;
            padding: 8px 10px !important;
            border-radius: 6px !important;
            transition: background-color 0.3s ease !important;
        }
        
        .logout-btn:hover {
            background-color: rgba(244, 67, 54, 0.2) !important;
        }
        
        .logout-btn i {
            font-size: 1.1rem !important;
        }

        /* Victims specific styles */
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            margin: 0;
        }
        
        /* Form Container */
        .form-container {
            max-width: 100%;
            margin: 0 auto 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .form-header {
            text-align: left;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }
        
        .form-header h2 {
            color: var(--primary-color);
            margin-bottom: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Form Grid Layout */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group.full-width {
            grid-column: span 3;
        }
        
        .form-group.double-width {
            grid-column: span 2;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--primary-color);
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary-color);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 15px;
            grid-column: span 3;
        }
        
        textarea {
            resize: vertical;
            min-height: 60px;
            max-height: 120px;
        }
        
        /* Table Container */
        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            margin-bottom: 15px;
        }
        
        .table-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .table-header h2 {
            color: var(--primary-color);
            font-size: 1.4rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .data-table th {
            background-color: #f9f9f9;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .data-table tr:hover {
            background-color: #f5f5f5;
        }
        
        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge.high {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
        }
        
        .badge.medium {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }
        
        .badge.low {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }
        
        /* Table Footer */
        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .table-summary {
            font-size: 14px;
            color: #666;
        }
        
        .table-summary strong {
            color: var(--primary-color);
        }
        
        .pagination {
            display: flex;
            gap: 5px;
        }
        
        .page-btn {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            background-color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .page-btn.active {
            background-color: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
        }
        
        .page-btn:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-hands-helping"></i>
            <h1>HelpHub</h1>
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="{{ route('volunteer.dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="{{ route('volunteer.inventory') }}"><i class="fas fa-boxes"></i> <span>Inventory</span></a></li>
            <li><a href="{{ route('volunteer.victims') }}" class="active"><i class="fas fa-users"></i> <span>Victims</span></a></li>
            <li><a href="{{ route('volunteer.distribution-repository') }}"><i class="fas fa-truck"></i> <span>Distribution Repo</span></a></li>
            <li>
                <div class="admin-profile" style="cursor: pointer;" onclick="window.location.href='{{ route('volunteer.edit-profile') }}'">
                    @if(auth()->user() && auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Volunteer Profile">
                    @else
                        <img src="{{ asset('assets/profile-user.png') }}" alt="Volunteer Profile">
                    @endif
                    <div>
                        <p>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
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
        <main class="main-content">
            <div class="page-header">
                <h1>Victim Management</h1>
            </div>
            
            <!-- Add New Victim Form -->
            <div class="form-container">
                <div class="form-header">
                    <h2><i class="fas fa-plus-circle"></i> Add New Victim</h2>
                </div>
                
                <form id="victimForm">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="victimName">Full Name</label>
                            <input type="text" id="victimName" name="name" placeholder="Enter victim's name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="familySize">Family Size</label>
                            <input type="number" id="familySize" name="family_size" min="1" placeholder="Number of members">
                        </div>
                        
                        <div class="form-group">
                            <label for="phoneNumber">Contact Number</label>
                            <input type="tel" id="phoneNumber" name="contact_info" placeholder="Phone number">
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" placeholder="Enter location">
                        </div>
                        
                        <div class="form-group">
                            <label for="priority">Priority Level</label>
                            <select id="priority" name="priority">
                                <option value="">Select priority</option>
                                <option value="high">High (Immediate)</option>
                                <option value="medium">Medium (24hrs)</option>
                                <option value="low">Low (Stable)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="needs">Needs</label>
                            <input type="text" id="needs" name="special_needs" placeholder="Food, Water, Shelter, Medical">
                        </div>
                        
                        <div class="form-actions">
                            <button type="reset" class="btn btn-secondary">Clear</button>
                            <button type="submit" class="btn btn-primary">Save Victim</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Victims List Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2><i class="fas fa-list"></i> Registered Victims</h2>
                </div>
                
                @if($victims->count() > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Victim ID</th>
                                <th>Name</th>
                                <th>Family Size</th>
                                <th>Location</th>
                                <th>Priority</th>
                                <th>Needs</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($victims as $victim)
                                <tr>
                                    <td>VIC-{{ date('Y') }}-{{ str_pad($victim->victim_id, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $victim->name }}</td>
                                    <td>{{ $victim->family_size ?? 'N/A' }}</td>
                                    <td>{{ $victim->location ?? 'Location not specified' }}</td>
                                    <td>
                                        <span class="badge {{ strtolower($victim->priority ?? 'low') }}">
                                            {{ ucfirst($victim->priority ?? 'Low') }}
                                        </span>
                                    </td>
                                    <td>{{ $victim->special_needs ?? $victim->medical_condition ?? 'General assistance' }}</td>
                                    <td>{{ $victim->contact_info ?? 'No contact info' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="table-footer">
                        <div class="table-summary">
                            <p>Total victims: <strong>{{ $victims->total() }}</strong></p>
                        </div>
                        <div class="pagination">
                            @if ($victims->onFirstPage())
                                <button class="page-btn" disabled><i class="fas fa-angle-double-left"></i></button>
                                <button class="page-btn" disabled><i class="fas fa-angle-left"></i></button>
                            @else
                                <a href="{{ $victims->url(1) }}" class="page-btn"><i class="fas fa-angle-double-left"></i></a>
                                <a href="{{ $victims->previousPageUrl() }}" class="page-btn"><i class="fas fa-angle-left"></i></a>
                            @endif
                            
                            @foreach ($victims->getUrlRange(1, $victims->lastPage()) as $page => $url)
                                @if ($page == $victims->currentPage())
                                    <button class="page-btn active">{{ $page }}</button>
                                @else
                                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                                @endif
                            @endforeach
                            
                            @if ($victims->hasMorePages())
                                <a href="{{ $victims->nextPageUrl() }}" class="page-btn"><i class="fas fa-angle-right"></i></a>
                                <a href="{{ $victims->url($victims->lastPage()) }}" class="page-btn"><i class="fas fa-angle-double-right"></i></a>
                            @else
                                <button class="page-btn" disabled><i class="fas fa-angle-right"></i></button>
                                <button class="page-btn" disabled><i class="fas fa-angle-double-right"></i></button>
                            @endif
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 40px 20px; color: #666;">
                        <i class="fas fa-users" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                        <p>No victims registered.</p>
                        <small>Use the form above to add new victim records.</small>
                    </div>
                @endif
            </div>
        </main>
    </div>
    
    <script>
        // Form submission
        document.getElementById('victimForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would normally submit the form data to the server
            alert('Victim record saved successfully!');
            this.reset();
            
            // In a real application, you would refresh the victims list or add the new victim to the table
        });
    </script>
</body>
</html>
