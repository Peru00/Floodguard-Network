<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management - FloodGuard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Match dashboard styling exactly */
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

        .edit-btn {
            background-color: #ffa502;
        }

        .delete-btn {
            background-color: #ff4757;
        }

        .action-btn:hover {
            opacity: 0.8;
        }

        .btn:not(.active) {
            background-color: #ecf0f1;
            color: #7f8c8d;
        }

        /* Make stat cards match dashboard layout exactly */
        .stat-card {
            min-width: 280px;
            min-height: 120px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .stat-card .stat-icon {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #e8f5e8;
            transition: transform 0.3s ease;
        }

        .stat-card .stat-icon i {
            font-size: 24px;
            color: var(--secondary-color);
        }

        .stat-card .stat-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .stat-card .stat-info h3 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-color);
            margin: 0 0 8px 0;
            line-height: 1.2;
        }

        .stat-card .stat-main-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
            line-height: 1;
            transition: color 0.3s ease;
        }

        .stat-card .stat-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-change.up {
            color: #10b981;
        }

        .stat-change.neutral {
            color: #6b7280;
        }

        .stat-change.down {
            color: #ef4444;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-left: 4px solid var(--secondary-color);
        }

        .stat-card:hover .stat-main-number {
            color: var(--secondary-color);
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }

        .stat-card:hover .stat-icon i {
            color: var(--accent-color);
        }

        /* Content grid for side-by-side cards */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }

        .content-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .content-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        /* Create User Card - Light Blue Theme */
        .content-card:first-child .card-header {
            background: #e3f2fd;
            color: #1565c0;
            border-bottom: 3px solid #42a5f5;
        }

        .content-card:first-child .card-header i {
            color: #1976d2;
        }

        /* Transfer Admin Card - Light Orange Theme */
        .content-card:last-child .card-header {
            background: #fff3e0;
            color: #e65100;
            border-bottom: 3px solid #ff9800;
        }

        .content-card:last-child .card-header i {
            color: #f57c00;
        }

        /* Button Accent Colors */
        .content-card:first-child .btn-primary {
            background: #42a5f5;
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .content-card:first-child .btn-primary:hover {
            background: #1976d2;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 165, 245, 0.4);
        }

        .content-card:last-child .btn-warning {
            background: #ff9800;
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .content-card:last-child .btn-warning:hover {
            background: #f57c00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 152, 0, 0.4);
        }

        /* Table Styling */
        .table-header {
            background: #f8f9fa;
            color: var(--text-color);
            padding: 16px 20px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .table-header i {
            color: var(--text-color);
        }

        .table-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .data-table thead th {
            background: #f8f9fa;
            color: var(--text-color);
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 1px solid #e9ecef;
        }

        /* Role-based color coding */
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-admin {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        .role-donor {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #86efac;
        }

        .role-volunteer {
            background: #dbeafe;
            color: #2563eb;
            border: 1px solid #93c5fd;
        }

        .role-victim {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fcd34d;
        }

        /* Alert Styles */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
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
            font-size: 1.1rem;
        }

        /* Action buttons styling */
        .actions {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: #3b82f6;
            color: white;
        }

        .edit-btn:hover {
            background: #2563eb;
            transform: scale(1.1);
        }

        .delete-btn {
            background: #ef4444;
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 24px;
            font-weight: 600;
        }

        .card-header i {
            font-size: 20px;
        }

        .card-header h3 {
            font-size: 16px;
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            padding: 24px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 20px;
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
    </nav>

    <!-- Main Content -->
    <div class="admin-container">
        <main class="main-content">
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
                <h2>User Management Overview</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Administrators</h3>
                        <div class="stat-main-number">{{ $roleStats['admin'] ?? 1 }}</div>
                        <small class="stat-change neutral"><i class="fas fa-shield-alt"></i> System Access</small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Donors</h3>
                        <div class="stat-main-number">{{ $roleStats['donor'] ?? 3 }}</div>
                        <small class="stat-change up"><i class="fas fa-check-circle"></i> Active Contributors</small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Volunteers</h3>
                        <div class="stat-main-number">{{ $roleStats['volunteer'] ?? 1 }}</div>
                        <small class="stat-change up"><i class="fas fa-users"></i> 1 Available Now</small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Recent Victims</h3>
                        <div class="stat-main-number">{{ isset($victims) ? $victims->count() : 4 }}</div>
                        <small class="stat-change neutral"><i class="fas fa-exclamation-triangle"></i> 2 High Priority</small>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content Grid -->
        <div class="content-grid">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success" style="grid-column: 1 / -1; margin-bottom: 1.5rem;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="grid-column: 1 / -1; margin-bottom: 1.5rem;">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="grid-column: 1 / -1; margin-bottom: 1.5rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul style="margin: 0.5rem 0 0 0; padding-left: 1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Role *</label>
                                <select name="role" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Administrator</option>
                                    <option value="donor">Donor</option>
                                    <option value="volunteer">Volunteer</option>
                                </select>
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
                    
                    <p style="margin-bottom: 1.5rem; color: #718096;">
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
                <h3>All Users ({{ isset($users) ? $users->count() : 0 }})</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($users))
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
                            <td>
                                <div class="actions">
                                    <button class="action-btn edit-btn" title="Edit User (ID: {{ $user->user_id }})" onclick="editUser('{{ $user->user_id }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete-btn" title="Delete User (ID: {{ $user->user_id }})" onclick="deleteUser('{{ $user->user_id }}', '{{ addslashes($user->first_name) }} {{ addslashes($user->last_name) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #718096; padding: 2rem;">
                                <i class="fas fa-users"></i> No users found
                            </td>
                        </tr>
                        @endforelse
                    @else
                        <tr>
                            <td colspan="6" style="text-align: center; color: #718096; padding: 2rem;">
                                <i class="fas fa-users"></i> No users data available
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        </main>
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
                            <option value="">Choose a user...</option>
                            @if(isset($users))
                                @foreach($users->where('role', '!=', 'admin') as $user)
                                    <option value="{{ $user->user_id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
                                @endforeach
                            @endif
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

        // Edit user function
        function editUser(userId) {
            console.log('Edit user clicked, userId:', userId);
            try {
                // Create edit form dynamically or redirect to edit page
                const editUrl = '/admin/users/' + userId + '/edit';
                console.log('Redirecting to:', editUrl);
                window.location.href = editUrl;
            } catch (error) {
                console.error('Error in editUser function:', error);
                alert('Error: Unable to edit user');
            }
        }

        // Delete user function
        function deleteUser(userId, userName) {
            console.log('Delete user clicked, userId:', userId, 'userName:', userName);
            try {
                if (confirm('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.')) {
                    // Create a form and submit DELETE request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/users/' + userId;
                    
                    console.log('Form action:', form.action);
                    
                    // Add CSRF token
                    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfTokenInput = document.querySelector('input[name="_token"]');
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    
                    if (csrfTokenMeta) {
                        csrfInput.value = csrfTokenMeta.getAttribute('content');
                        console.log('Using CSRF token from meta tag');
                    } else if (csrfTokenInput) {
                        csrfInput.value = csrfTokenInput.value;
                        console.log('Using CSRF token from input');
                    } else {
                        csrfInput.value = '{{ csrf_token() }}';
                        console.log('Using Laravel CSRF token');
                    }
                    
                    console.log('CSRF token:', csrfInput.value);
                    form.appendChild(csrfInput);
                    
                    // Add method override for DELETE
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    // Submit form
                    document.body.appendChild(form);
                    console.log('Submitting form');
                    form.submit();
                } else {
                    console.log('User cancelled deletion');
                }
            } catch (error) {
                console.error('Error in deleteUser function:', error);
                alert('Error: Unable to delete user');
            }
        }
    </script>
</body>
</html>
