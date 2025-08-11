<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .profile-edit-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .profile-picture-section {
            text-align: center;
        }
        
        .profile-picture-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e5e7eb;
            margin-bottom: 15px;
        }
        
        .profile-picture-upload {
            position: relative;
            display: inline-block;
        }
        
        .upload-btn {
            background: var(--secondary-color);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            border: none;
            transition: background 0.3s;
        }
        
        .upload-btn:hover {
            background: var(--accent-color);
        }
        
        .profile-picture-input {
            display: none;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group-full {
            grid-column: 1 / -1;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        
        .alert-danger {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="admin-container">
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-hands-helping"></i>
            <h1>HelpHub</h1>
        </div>
        <div class="nav-links">
            <a href="{{ route('home') }}"><i class="fas fa-home"></i> <span>Home</span></a>
            <a href="{{ route('donor.dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
            <a href="{{ route('donor.distribution') }}"><i class="fas fa-archive"></i> <span>Distribution Repo</span></a>
            <div class="admin-profile">
                <img src="{{ $user->profile_picture ? asset($user->profile_picture) : 'https://randomuser.me/api/portraits/men/32.jpg' }}" alt="Donor">
                <div>
                    <p>{{ $user->first_name }} {{ $user->last_name }}</p>
                    <small>Donor</small>
                </div>
            </div>
            <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <main class="main-content">
        <div class="profile-edit-container">
            <div class="profile-header">
                <div>
                    <h2 style="margin: 0; font-size: 24px; color: #333;">
                        <i class="fas fa-user-edit" style="margin-right: 10px; color: var(--secondary-color);"></i>
                        Edit Profile
                    </h2>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Update your personal information and profile picture</p>
                </div>
                <a href="{{ route('donor.dashboard') }}" class="btn btn-secondary" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('donor.update-profile') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="profile-picture-section">
                    <img src="{{ $user->profile_picture ? asset($user->profile_picture) : 'https://randomuser.me/api/portraits/men/32.jpg' }}" 
                         alt="Profile Picture" 
                         class="profile-picture-preview"
                         id="profilePreview">
                    
                    <div class="profile-picture-upload">
                        <label for="profile_picture" class="upload-btn">
                            <i class="fas fa-camera"></i> Change Picture
                        </label>
                        <input type="file" 
                               id="profile_picture" 
                               name="profile_picture" 
                               class="profile-picture-input"
                               accept="image/*"
                               onchange="previewImage(this)">
                    </div>
                    <p style="font-size: 12px; color: #666; margin-top: 8px;">
                        Supported formats: JPG, PNG, GIF (Max: 2MB)
                    </p>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" 
                               name="first_name" 
                               value="{{ old('first_name', $user->first_name) }}" 
                               required 
                               placeholder="Enter your first name">
                    </div>
                    
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" 
                               name="last_name" 
                               value="{{ old('last_name', $user->last_name) }}" 
                               required 
                               placeholder="Enter your last name">
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               placeholder="Enter your email">
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}" 
                               placeholder="Enter your phone number">
                    </div>
                    
                    <div class="form-group form-group-full">
                        <label>Address</label>
                        <textarea name="address" 
                                  rows="3" 
                                  placeholder="Enter your full address">{{ old('address', $user->address) }}</textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('donor.dashboard') }}" class="btn btn-secondary" style="text-decoration: none;">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
