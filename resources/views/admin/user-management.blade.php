<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - FloodGuard Network</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --background: #ecf0f1;
            --text-dark: #2c3e50;
            --text-light: #ffffff;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Navbar Styles (Same as Dashboard) */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2980b9 100%);
            padding: 1rem 2rem;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-light);
            font-size: 1.5rem;
            font-weight: bold;
        }

        .logo i {
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 0;
            align-items: center;
        }

        .nav-links li a {
            color: var(--text-light);
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links li a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-links li.active a {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .admin-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--text-light);
        }

        .logout-btn {
            background: none !important;
            border: none !important;
            color: var(--text-light) !important;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            color: var(--danger-color) !important;
            transform: scale(1.1);
        }

        /* Main Content Container */
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Stats Section */
        .stats-section {
            margin-bottom: 2rem;
        }

        .stats-section h2 {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            text-align: center;
            transition: all 0.3s ease;
            border-top: 4px solid var(--primary-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Content Sections */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .content-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--secondary-color), #34495e);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.25rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e8ed;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: white;
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

        /* Button Styles */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #e67e22);
            color: white;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        /* Data Table */
        .table-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .table-header {
            background: linear-gradient(135deg, var(--secondary-color), #34495e);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-header h3 {
            margin: 0;
            font-size: 1.25rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th, .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }

        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .data-table tbody tr {
            transition: all 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
        }

        /* Badges */
        .role-badge, .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-admin {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: white;
        }

        .role-donor {
            background: linear-gradient(135deg, var(--success-color), #229954);
            color: white;
        }

        .role-volunteer {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
        }

        .status-high {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: white;
        }

        .status-medium {
            background: linear-gradient(135deg, var(--warning-color), #e67e22);
            color: white;
        }

        .status-low {
            background: linear-gradient(135deg, var(--success-color), #229954);
            color: white;
        }

        .status-pending {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }

        .status-assisted {
            background: linear-gradient(135deg, var(--success-color), #229954);
            color: white;
        }

        .status-relocated {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: var(--success-color);
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: var(--danger-color);
        }

        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border-color: var(--primary-color);
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-color: var(--warning-color);
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
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
        }

        .modal-body {
            padding: 2rem;
        }

        .close {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close:hover {
            transform: scale(1.2);
        }

        /* Role-specific fields toggle */
        .role-fields {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .admin-container {
                padding: 1rem;
            }
            
            .nav-links {
                gap: 0.5rem;
            }
            
            .nav-links li a {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .navbar .container {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <i class="fas fa-hands-helping"></i>
                <h1>Floodguard Admin</h1>
            </div>
            <ul class="nav-links">
                <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="active"><a href="{{ route('admin.user-management') }}"><i class="fas fa-users-cog"></i> User Management</a></li>
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
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="admin-container">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Stats Overview Section -->
        <section class="stats-section">
            <h2><i class="fas fa-users-cog"></i> User Management System</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-number">{{ $roleStats['admin'] }}</div>
                    <div class="stat-label">Administrators</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="stat-number">{{ $roleStats['donor'] }}</div>
                    <div class="stat-label">Donors</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="stat-number">{{ $roleStats['volunteer'] }}</div>
                    <div class="stat-label">Volunteers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-number">{{ $victims->count() }}</div>
                    <div class="stat-label">Recent Victims</div>
                </div>
            </div>
        </section>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Create User Form -->
            <div class="content-card">
                <div class="card-header">
                    <i class="fas fa-user-plus"></i>
                    <h3>Create New User</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Admin Privilege:</strong> Only administrators can create new user accounts and assign roles.
                    </div>
                    
                    <form method="POST" action="{{ route('admin.create-user') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-input" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Role *</label>
                                <select name="role" class="form-select" required id="userRole" onchange="toggleRoleFields()">
                                    <option value="">Select Role</option>
                                    <option value="admin">Administrator</option>
                                    <option value="donor">Donor</option>
                                    <option value="volunteer">Volunteer</option>
                                </select>
                            </div>
                        </div>

                        <div id="volunteerFields" class="role-fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Location</label>
                                    <input type="text" name="location" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Skill Type</label>
                                    <select name="skill_type" class="form-select">
                                        <option value="General Support">General Support</option>
                                        <option value="Medical Aid">Medical Aid</option>
                                        <option value="Transportation">Transportation</option>
                                        <option value="Food Distribution">Food Distribution</option>
                                        <option value="Rescue Operations">Rescue Operations</option>
                                        <option value="Communication">Communication</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="donorFields" class="role-fields" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-input" required minlength="6">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create User Account
                        </button>
                    </form>
                </div>
            </div>

            <!-- Transfer Admin Rights -->
            <div class="content-card">
                <div class="card-header">
                    <i class="fas fa-crown"></i>
                    <h3>Transfer Admin Rights</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Critical Action:</strong> This will permanently transfer all administrative privileges to another user.
                    </div>
                    
                    <p style="margin-bottom: 1.5rem; color: #666;">
                        Transfer administrative privileges to another user. <strong>Warning:</strong> You will lose admin access and become a regular donor.
                    </p>

                    <button type="button" class="btn btn-warning" onclick="openTransferModal()">
                        <i class="fas fa-exchange-alt"></i> Transfer Admin Rights
                    </button>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="table-container">
            <div class="table-header">
                <i class="fas fa-list"></i>
                <h3>All Users ({{ $users->count() }})</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            <span class="role-badge role-{{ $user->role }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #666; padding: 2rem;">
                            <i class="fas fa-users"></i> No users found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Recent Victims -->
        <div class="table-container">
            <div class="table-header">
                <i class="fas fa-heart"></i>
                <h3>Recent Victims ({{ $victims->count() }})</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Victim ID</th>
                        <th>Name</th>
                        <th>Family Size</th>
                        <th>Location</th>
                        <th>Priority</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($victims as $victim)
                    <tr>
                        <td>{{ $victim->victim_id }}</td>
                        <td>{{ $victim->name }}</td>
                        <td>{{ $victim->family_size }}</td>
                        <td>{{ $victim->location }}</td>
                        <td>
                            <span class="status-badge status-{{ $victim->priority }}">
                                {{ ucfirst($victim->priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $victim->status }}">
                                {{ ucfirst($victim->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #666; padding: 2rem;">
                            <i class="fas fa-heart"></i> No victims found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transfer Admin Modal -->
    <div id="transferModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Transfer Admin Rights</h3>
                <span class="close" onclick="closeTransferModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.transfer-admin') }}">
                    @csrf
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>WARNING:</strong> This action cannot be undone. You will lose admin privileges and become a regular donor account.
                    </div>

                    <div class="form-group">
                        <label class="form-label">Select New Admin *</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Choose user to make admin</option>
                            @foreach($users as $user)
                                @if($user->user_id !== auth()->user()->user_id && $user->role !== 'admin')
                                    <option value="{{ $user->user_id }}">
                                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm Your Password *</label>
                        <input type="password" name="current_password" class="form-input" required placeholder="Enter your current password">
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 2rem;">
                        <button type="button" class="btn btn-secondary" onclick="closeTransferModal()">Cancel</button>
                        <button type="submit" class="btn btn-danger">Transfer Admin Rights</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleRoleFields() {
            const role = document.getElementById('userRole').value;
            const volunteerFields = document.getElementById('volunteerFields');
            const donorFields = document.getElementById('donorFields');
            
            volunteerFields.style.display = role === 'volunteer' ? 'block' : 'none';
            donorFields.style.display = role === 'donor' ? 'block' : 'none';
        }

        function openTransferModal() {
            document.getElementById('transferModal').style.display = 'block';
        }

        function closeTransferModal() {
            document.getElementById('transferModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('transferModal');
            if (event.target === modal) {
                closeTransferModal();
            }
        }

        // Auto-hide success/error messages after 5 seconds
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
