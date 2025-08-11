<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Volunteer Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .edit-profile-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .section-header h2 {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .section-header p {
            color: #666;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-grid.single {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-hands-helping"></i>
            <h1>Floodguard Network</h1>
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="{{ route('volunteer.dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="{{ route('volunteer.distribution-repository') }}"><i class="fas fa-archive"></i> <span>Distribution Repo</span></a></li>
            <li>
                <div class="admin-profile" style="cursor: pointer;" onclick="window.location.href='{{ route('volunteer.edit-profile') }}'">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Volunteer Profile">
                    @else
                        <img src="{{ asset('assets/profile-user.png') }}" alt="Volunteer Profile">
                    @endif
                    <div>
                        <p>{{ $user->first_name }} {{ $user->last_name }}</p>
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

            @if ($errors->any())
                <div class="alert error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Edit Profile Section -->
            <section class="edit-profile-section">
                <div class="section-header">
                    <h2><i class="fas fa-user-edit"></i> Edit Volunteer Profile</h2>
                    <p>Update your personal information and volunteer details</p>
                </div>

                <form method="POST" action="{{ route('volunteer.update-profile') }}">
                    @csrf
                    
                    <!-- Personal Information -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name"><i class="fas fa-user"></i> First Name</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name"><i class="fas fa-user"></i> Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        </div>
                    </div>

                    <!-- Volunteer Information -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="skill_type"><i class="fas fa-tools"></i> Skills/Specialization</label>
                            <select id="skill_type" name="skill_type" required>
                                <option value="">Select your primary skill</option>
                                <option value="Medical Assistance" {{ old('skill_type', $volunteerProfile->skill_type ?? '') === 'Medical Assistance' ? 'selected' : '' }}>Medical Assistance</option>
                                <option value="Food Distribution" {{ old('skill_type', $volunteerProfile->skill_type ?? '') === 'Food Distribution' ? 'selected' : '' }}>Food Distribution</option>
                                <option value="Construction" {{ old('skill_type', $volunteerProfile->skill_type ?? '') === 'Construction' ? 'selected' : '' }}>Construction</option>
                                <option value="Transportation" {{ old('skill_type', $volunteerProfile->skill_type ?? '') === 'Transportation' ? 'selected' : '' }}>Transportation</option>
                                <option value="Communication" {{ old('skill_type', $volunteerProfile->skill_type ?? '') === 'Communication' ? 'selected' : '' }}>Communication</option>
                                <option value="Logistics" {{ old('skill_type', $volunteerProfile->skill_type ?? '') === 'Logistics' ? 'selected' : '' }}>Logistics</option>
                                <option value="Search & Rescue" {{ old('skill_type', $volunteerProfile->skill_type ?? '') === 'Search & Rescue' ? 'selected' : '' }}>Search & Rescue</option>
                                <option value="Counseling" {{ old('skill_type', $volunteerProfile->skill_type ?? '') === 'Counseling' ? 'selected' : '' }}>Counseling</option>
                                <option value="General Volunteer" {{ old('skill_type', $volunteerProfile->skill_type ?? '') === 'General Volunteer' ? 'selected' : '' }}>General Volunteer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="location"><i class="fas fa-map-marker-alt"></i> Preferred Location</label>
                            <input type="text" id="location" name="location" value="{{ old('location', $volunteerProfile->location ?? '') }}" placeholder="e.g., Downtown, North District">
                        </div>
                    </div>

                    <!-- Emergency Contact Information -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="emergency_contact_name"><i class="fas fa-user-friends"></i> Emergency Contact Name</label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $volunteerProfile->emergency_contact_name ?? '') }}" placeholder="Full name of emergency contact">
                        </div>
                        <div class="form-group">
                            <label for="emergency_contact_phone"><i class="fas fa-phone-alt"></i> Emergency Contact Phone</label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $volunteerProfile->emergency_contact_phone ?? '') }}" placeholder="+1 (555) 123-4567">
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('volunteer.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
