<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit User - FloodGuard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    /* Use the same navbar styling as other admin pages */
    .admin-container {
        margin-top: 0;
        padding: 2rem;
        min-height: calc(100vh - 70px);
    }

    .edit-user-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .page-header {
        background: var(--primary-color);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        text-align: center;
    }

    .page-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 600;
    }

    .edit-form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-grid.full-width {
        grid-template-columns: 1fr;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-group input,
    .form-group select {
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(138, 178, 166, 0.1);
        background: white;
    }

    .password-section {
        border-top: 2px solid #f0f0f0;
        padding-top: 1.5rem;
        margin-top: 1.5rem;
    }

    .password-section h3 {
        color: var(--primary-color);
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }

    .role-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-admin { background: #fee; color: #dc2626; }
    .role-donor { background: #eff6ff; color: #2563eb; }
    .role-volunteer { background: #f0f9f0; color: #16a34a; }
    .role-victim { background: #fef3c7; color: #d97706; }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #f0f0f0;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .btn-primary {
        background: var(--secondary-color);
        color: white;
    }

    .btn-primary:hover {
        background: #6b9688;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(138, 178, 166, 0.4);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .edit-user-container {
            margin: 1rem auto;
        }
    }
</style>
</head>

<body class="admin-panel">
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
        <div class="edit-user-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-user-edit"></i> Edit User</h1>
        <p>Update user information and settings</p>
    </div>

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

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <ul style="margin: 0.5rem 0 0 0; padding-left: 1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="edit-form-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <h2 style="margin: 0; color: var(--primary-color);">
                <i class="fas fa-user"></i> {{ $userToEdit->first_name }} {{ $userToEdit->last_name }}
            </h2>
            <span class="role-badge role-{{ $userToEdit->role }}">
                {{ ucfirst($userToEdit->role) }}
            </span>
        </div>

        <form action="{{ route('admin.users.update', $userToEdit->user_id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name"><i class="fas fa-user"></i> First Name</label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           value="{{ old('first_name', $userToEdit->first_name) }}" 
                           required>
                </div>

                <div class="form-group">
                    <label for="last_name"><i class="fas fa-user"></i> Last Name</label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           value="{{ old('last_name', $userToEdit->last_name) }}" 
                           required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $userToEdit->email) }}" 
                           required>
                </div>

                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $userToEdit->phone) }}">
                </div>
            </div>

            <div class="form-grid full-width">
                <div class="form-group">
                    <label for="role"><i class="fas fa-shield-alt"></i> Role</label>
                    <select id="role" name="role" required>
                        <option value="donor" {{ old('role', $userToEdit->role) == 'donor' ? 'selected' : '' }}>Donor</option>
                        <option value="volunteer" {{ old('role', $userToEdit->role) == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                        <option value="victim" {{ old('role', $userToEdit->role) == 'victim' ? 'selected' : '' }}>Victim</option>
                        <option value="admin" {{ old('role', $userToEdit->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>

            <!-- Password Section -->
            <div class="password-section">
                <h3><i class="fas fa-lock"></i> Change Password (Optional)</h3>
                <p style="color: #6c757d; margin-bottom: 1rem;">Leave blank to keep current password</p>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="password"><i class="fas fa-key"></i> New Password</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               minlength="8">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation"><i class="fas fa-key"></i> Confirm Password</label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               minlength="8">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('admin.user-management') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to User Management
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update User
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }
    });
</script>
</body>
</html>
