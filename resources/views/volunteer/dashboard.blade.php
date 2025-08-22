<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Additional volunteer-specific styles */
        .volunteer-hero {
            margin-bottom: 30px;
            padding: 25px;
            background: white;
            color: var(--text-dark);
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        }
        
        .volunteer-profile {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        
        .profile-header {
            display: flex;
            gap: 20px;
            align-items: center;
            flex: 1;
        }
        
        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
        
        .profile-header h2 {
            margin-bottom: 5px;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .volunteer-id {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        
        .availability-toggle {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .toggle-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .toggle-label {
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ff4757;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--success-color);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .toggle-status {
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .toggle-status.available {
            color: var(--success-color);
        }
        
        .toggle-status.unavailable {
            color: var(--danger-color);
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            flex: 2;
            margin-left: -40px;
        }
        
        .stat-item {
            background: rgba(138, 178, 166, 0.1);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }
        
        .stat-item i {
            font-size: 1.5rem;
            color: var(--secondary-color);
            min-width: 24px;
            flex-shrink: 0;
        }
        
        .stat-item div {
            flex: 1;
            min-width: 0;
        }
        
        .stat-item div span {
            display: block;
            font-size: 0.8rem;
            opacity: 0.8;
            margin-bottom: 2px;
        }
        
        .stat-item div strong {
            font-size: 1rem;
            font-weight: 600;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        /* Task Cards */
        .assigned-tasks {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .section-header h2 {
            color: var(--primary-color);
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tasks-container {
            display: grid;
            gap: 20px;
        }
        
        .task-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .task-card.high-priority {
            border-left: 4px solid var(--danger-color);
        }
        
        .task-card.medium-priority {
            border-left: 4px solid var(--warning-color);
        }
        
        .task-card.low-priority {
            border-left: 4px solid var(--success-color);
        }
        
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .task-header h3 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin: 0;
        }
        
        .task-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .detail-item i {
            color: var(--secondary-color);
            min-width: 16px;
        }
        
        .task-description {
            margin-bottom: 20px;
        }
        
        .task-description p {
            color: #555;
            line-height: 1.5;
        }
        
        .task-actions {
            text-align: right;
        }
        
        .no-tasks {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        
        .no-tasks i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        .btn-edit {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 0.9rem;
            padding: 5px;
            margin-left: 10px;
            transition: color 0.3s;
        }
        
        .btn-edit:hover {
            color: var(--primary-color);
        }
        
        /* Alert Styles */
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert.success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        
        .alert.error {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(244, 67, 54, 0.3);
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

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .volunteer-profile {
                flex-direction: column;
                gap: 20px;
            }
            
            .profile-stats {
                grid-template-columns: 1fr;
            }
            
            .task-details {
                grid-template-columns: 1fr;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
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

        /* Chat Styles */
        .chat-container {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 12px;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .chat-layout {
            display: flex;
            height: 500px;
        }

        /* Admin List Styles */
        .admin-list {
            width: 280px;
            background: #f8f9fa;
            border-right: 1px solid #e1e8ed;
            display: flex;
            flex-direction: column;
        }

        .admin-list h3 {
            padding: 20px;
            margin: 0;
            font-size: 16px;
            color: #1a2b47;
            background: #1a2b47;
            color: white;
            border-bottom: 1px solid #e1e8ed;
        }

        .admin-items {
            flex: 1;
            overflow-y: auto;
        }

        .admin-item {
            padding: 15px 20px;
            border-bottom: 1px solid #e1e8ed;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-item:hover {
            background: #e8f4f8;
        }

        .admin-item.active {
            background: #e8f4f8;
            border-left: 4px solid #3498db;
        }

        .admin-info {
            flex: 1;
        }

        .admin-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .admin-status {
            font-size: 12px;
            color: #7f8c8d;
        }

        .admin-status.online {
            color: #27ae60;
        }

        .admin-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #bdc3c7;
        }

        .admin-indicator.online {
            background: #27ae60;
        }

        /* Chat Interface Styles */
        .chat-interface {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 20px;
            background: #1a2b47;
            color: white;
            font-weight: 600;
            border-bottom: 1px solid #e1e8ed;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #fafbfc;
            max-height: 350px;
        }

        .welcome-message {
            text-align: center;
            color: #7f8c8d;
            padding: 40px 20px;
        }

        .welcome-message i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .message.sent {
            justify-content: flex-end; /* Sent messages on the right like admin */
        }

        .message.received {
            justify-content: flex-start; /* Received messages on the left like admin */
        }

        .message-content {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
        }

        .message-text {
            margin: 0;
            line-height: 1.4;
        }

        .message.received .message-content {
            background: #f1f3f4; /* Admin chat style for received messages */
            color: #333;
            border-bottom-left-radius: 4px;
        }

        .message.sent .message-content {
            background: #3498db; /* Admin chat style for sent messages */
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 4px;
        }

        .message.received .message-time {
            text-align: left;
        }

        .message.sent .message-time {
            text-align: right;
        }

        .message-image {
            max-width: 200px;
            border-radius: 8px;
            margin-top: 8px;
            cursor: pointer;
        }

        /* Chat Input Styles */
        .chat-input-container {
            border-top: 1px solid #e1e8ed;
            background: white;
        }

        .message-input-area {
            display: flex;
            align-items: flex-end;
            padding: 15px;
            gap: 10px;
        }

        #messageInput {
            flex: 1;
            border: 1px solid #e1e8ed;
            border-radius: 20px;
            padding: 10px 15px;
            resize: none;
            outline: none;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        #messageInput:focus {
            border-color: #3498db;
        }

        .chat-actions {
            display: flex;
            gap: 8px;
        }

        .image-btn, .send-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .image-btn {
            background: #ecf0f1;
            color: #7f8c8d;
        }

        .image-btn:hover {
            background: #bdc3c7;
            color: #2c3e50;
        }

        .send-btn {
            background: #3498db;
            color: white;
        }

        .send-btn:hover {
            background: #2980b9;
            transform: scale(1.05);
        }

        .send-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            transform: none;
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
            <li><a href="{{ route('volunteer.dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="{{ route('volunteer.relief-camps') }}"><i class="fas fa-campground"></i> <span>My Relief Camp</span></a></li>
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

            <!-- Volunteer Hero Section -->
            <section class="volunteer-hero">
                <div class="volunteer-profile">
                    <div class="profile-header">
                        @if($volunteerInfo->profile_picture)
                            <img src="{{ asset('storage/' . $volunteerInfo->profile_picture) }}" alt="Volunteer" class="profile-img">
                        @else
                            <img src="{{ asset('assets/profile-user.png') }}" alt="Volunteer" class="profile-img">
                        @endif
                        <div>
                            <h2>
                                {{ $volunteerInfo->first_name }} {{ $volunteerInfo->last_name }}
                                <button onclick="window.location.href='{{ route('volunteer.edit-profile') }}'" class="btn-edit" title="Edit Profile">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </h2>
                            <p class="volunteer-id">Volunteer ID: {{ $volunteerInfo->volunteer_id }}</p>
                            
                            <div class="availability-toggle">
                                <div class="toggle-container">
                                    <span class="toggle-label">Availability:</span>
                                    <label class="switch">
                                        <input type="checkbox" id="availabilityToggle" {{ $volunteerInfo->is_available ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <span id="availabilityStatus" class="toggle-status {{ $volunteerInfo->is_available ? 'available' : 'unavailable' }}">
                                    {{ $volunteerInfo->is_available ? 'Available' : 'Not Available' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <span>Phone No</span>
                                <strong>{{ $volunteerInfo->phone ?? 'Not provided' }}</strong>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <span>Email</span>
                                <strong>{{ $volunteerInfo->email }}</strong>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-tools"></i>
                            <div>
                                <span>Skill</span>
                                <strong>{{ $volunteerInfo->skill_type ?? 'General Volunteer' }}</strong>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-hand-holding-heart"></i>
                            <div>
                                <span>Total Helped</span>
                                <strong>{{ $volunteerInfo->people_helped ?? 0 }} People</strong>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clipboard-list"></i>
                            <div>
                                <span>Active Tasks</span>
                                <strong>{{ count($tasks) }} Tasks</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Assigned Tasks Section -->
            <section class="assigned-tasks">
                <div class="section-header">
                    <h2><i class="fas fa-tasks"></i> Assigned Tasks</h2>
                </div>

                <div class="tasks-container">
                    @if(count($tasks) === 0 && count($volunteerTasks) === 0)
                        <div class="no-tasks">
                            <i class="fas fa-tasks"></i>
                            <p>You currently have no assigned tasks.</p>
                            <small>New tasks will appear here when assigned by administrators.</small>
                        </div>
                    @else
                        {{-- Display Volunteer Tasks assigned by Admin --}}
                        @foreach($volunteerTasks as $task)
                            <div class="task-card {{ strtolower($task->priority) }}-priority">
                                <div class="task-header">
                                    <h3>{{ $task->title }}</h3>
                                    <span class="badge {{ strtolower($task->priority) }}">{{ ucfirst($task->priority) }} Priority</span>
                                </div>
                                <div class="task-details">
                                    <div class="detail-item">
                                        <i class="fas fa-calendar-day"></i>
                                        <span>Assigned: {{ $task->assigned_date ? \Carbon\Carbon::parse($task->assigned_date)->format('l, F j, Y') : 'Not specified' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <span>Due: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M j, Y g:i A') : 'No due date' }}</span>
                                    </div>
                                    @if($task->location)
                                    <div class="detail-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $task->location }}</span>
                                    </div>
                                    @endif
                                    <div class="detail-item">
                                        <i class="fas fa-tag"></i>
                                        <span>{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</span>
                                    </div>
                                    @if($task->assignedBy)
                                    <div class="detail-item">
                                        <i class="fas fa-user-shield"></i>
                                        <span>Assigned by: {{ $task->assignedBy->first_name }} {{ $task->assignedBy->last_name }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="task-description">
                                    <p><strong>Description:</strong> {{ $task->description }}</p>
                                    @if($task->notes)
                                        <p><strong>Additional Notes:</strong> {{ $task->notes }}</p>
                                    @endif
                                </div>
                                <div class="task-actions">
                                    <form method="POST" action="{{ route('volunteer.complete-volunteer-task') }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to mark this task as completed? This action cannot be undone.')">
                                        @csrf
                                        <input type="hidden" name="task_id" value="{{ $task->task_id }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check"></i> Mark as Completed
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        {{-- Display Distribution Tasks (Legacy) --}}
                        @foreach($tasks as $task)
                            <div class="task-card {{ strtolower($task->priority) }}-priority">
                                <div class="task-header">
                                    <h3>Relief Distribution Task</h3>
                                    <span class="badge {{ strtolower($task->priority) }}">{{ ucfirst($task->priority) }} Priority</span>
                                </div>
                                <div class="task-details">
                                    <div class="detail-item">
                                        <i class="fas fa-calendar-day"></i>
                                        <span>Assigned: {{ \Carbon\Carbon::parse($task->assigned_date)->format('l, F j, Y') }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $task->location }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-user"></i>
                                        <span>Beneficiary: {{ $task->victim_name }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-box"></i>
                                        <span>{{ $task->quantity }} units of {{ $task->item_name }}</span>
                                    </div>
                                </div>
                                <div class="task-description">
                                    <p><strong>Task:</strong> Distribute {{ $task->quantity }} units of {{ $task->item_name }} to {{ $task->victim_name }} at {{ $task->location }}. This is a {{ strtolower($task->priority) }} priority relief distribution task.</p>
                                </div>
                                <div class="task-actions">
                                    <form method="POST" action="{{ route('volunteer.complete-task') }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to mark this task as completed? This action cannot be undone.')">
                                        @csrf
                                        <input type="hidden" name="task_id" value="{{ $task->task_id }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check"></i> Mark as Completed
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>

            <!-- Admin Communication Section -->
            <section class="assigned-tasks">
                <div class="section-header">
                    <h2><i class="fas fa-comments"></i> Admin Communication</h2>
                    <button id="refreshChatBtn" class="btn btn-secondary" onclick="refreshChatMessages()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                
                <div class="chat-container">
                    <div class="chat-layout">
                        <!-- Admin List (Static since volunteers only chat with admins) -->
                        <div class="admin-list">
                            <h3>Administrators</h3>
                            <div class="admin-items">
                                <div class="admin-item active" onclick="selectAdmin()">
                                    <div class="admin-info">
                                        <div class="admin-name">FloodGuard Admin</div>
                                        <div class="admin-status online">Online</div>
                                    </div>
                                    <div class="admin-indicator online"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Interface -->
                        <div class="chat-interface">
                            <div class="chat-header">
                                <div id="selectedAdminName">Chat with FloodGuard Admin</div>
                            </div>
                            <div class="chat-messages" id="chatMessages">
                                <div class="welcome-message">
                                    <i class="fas fa-comments"></i>
                                    <p>Messages from administrators will appear here</p>
                                </div>
                            </div>
                            <div class="chat-input-container">
                                <form id="chatForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="message-input-area">
                                        <textarea id="messageInput" name="message" placeholder="Type your message to admin..." rows="2"></textarea>
                                        <div class="chat-actions">
                                            <label for="imageInput" class="image-btn" title="Send Image">
                                                <i class="fas fa-image"></i>
                                                <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">
                                            </label>
                                            <button type="submit" class="send-btn" title="Send Message">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Set up CSRF token for AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Availability toggle functionality with AJAX
            const availabilityToggle = document.getElementById('availabilityToggle');
            const statusElement = document.getElementById('availabilityStatus');
            
            availabilityToggle.addEventListener('change', function() {
                fetch('{{ route('volunteer.toggle-availability') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusElement.textContent = data.status;
                        statusElement.className = 'toggle-status ' + (data.is_available ? 'available' : 'unavailable');
                    } else {
                        // Revert toggle if update failed
                        availabilityToggle.checked = !availabilityToggle.checked;
                        alert('Error updating availability: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revert toggle if request failed
                    availabilityToggle.checked = !availabilityToggle.checked;
                    alert('Error updating availability. Please try again.');
                });
            });

            // Load chat messages on page load
            loadChatMessages();
        });

        // Chat functionality
        function loadChatMessages() {
            const chatMessages = document.getElementById('chatMessages');
            
            // Show loading state
            chatMessages.innerHTML = `
                <div class="welcome-message">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading messages...</p>
                </div>
            `;

            // Make AJAX call to load messages
            fetch('/volunteer/chat/messages')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.messages.length === 0) {
                            chatMessages.innerHTML = `
                                <div class="welcome-message">
                                    <i class="fas fa-comments"></i>
                                    <p>No messages yet. Administrators can send you messages here.</p>
                                </div>
                            `;
                        } else {
                            chatMessages.innerHTML = '';
                            data.messages.forEach(message => {
                                if (message.image_path) {
                                    addMessageToChat(
                                        message.sender_type === 'volunteer' ? 'sent' : 'received',
                                        null,
                                        false,
                                        message.created_at,
                                        message.image_path
                                    );
                                } else {
                                    addMessageToChat(
                                        message.sender_type === 'volunteer' ? 'sent' : 'received',
                                        message.message,
                                        true,
                                        message.created_at
                                    );
                                }
                            });
                        }
                        // Scroll to bottom
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    } else {
                        chatMessages.innerHTML = `
                            <div class="welcome-message">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Error loading messages: ${data.message}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    chatMessages.innerHTML = `
                        <div class="welcome-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Error loading messages. Please try again.</p>
                        </div>
                    `;
                });
        }

        // Handle chat form submission
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageInput = document.getElementById('messageInput');
            const imageInput = document.getElementById('imageInput');
            const message = messageInput.value.trim();
            
            if (!message && !imageInput.files[0]) {
                return;
            }

            // Disable form while sending
            const sendBtn = document.querySelector('.send-btn');
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Create FormData for file upload
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            if (message) formData.append('message', message);
            if (imageInput.files[0]) formData.append('image', imageInput.files[0]);

            // Send message via AJAX
            fetch('/volunteer/chat/send', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add message to chat immediately
                    if (data.chat_message.message) {
                        addMessageToChat('sent', data.chat_message.message, true, data.chat_message.created_at);
                    }
                    if (data.chat_message.image_path) {
                        addMessageToChat('sent', data.chat_message.image_url, false, data.chat_message.created_at);
                    }
                    
                    // Clear inputs
                    messageInput.value = '';
                    imageInput.value = '';
                    document.querySelector('label[for="imageInput"]').style.background = '#6c757d';
                    document.querySelector('label[for="imageInput"]').title = 'Send Image';
                } else {
                    alert('Error sending message: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Error sending message. Please try again.');
            })
            .finally(() => {
                // Re-enable form
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            });
        });

        function addMessageToChat(type, content, isText = true, time = null, imagePath = null) {
            const chatMessages = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            
            const currentTime = time || new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            if (isText) {
                messageDiv.innerHTML = `
                    <div class="message-content">
                        <div class="message-text">${content}</div>
                        <div class="message-time">${currentTime}</div>
                    </div>
                `;
            } else {
                const imageUrl = imagePath ? `/storage/${imagePath}` : content;
                messageDiv.innerHTML = `
                    <div class="message-content">
                        <img src="${imageUrl}" alt="Sent image" class="message-image">
                        <div class="message-time">${currentTime}</div>
                    </div>
                `;
            }
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function refreshChatMessages() {
            const refreshBtn = document.getElementById('refreshChatBtn');
            const icon = refreshBtn.querySelector('i');
            
            icon.classList.add('fa-spin');
            
            setTimeout(() => {
                loadChatMessages();
                icon.classList.remove('fa-spin');
            }, 500);
        }

        // Handle image input change
        document.getElementById('imageInput').addEventListener('change', function(e) {
            if (e.target.files[0]) {
                const fileName = e.target.files[0].name;
                const label = document.querySelector('label[for="imageInput"]');
                label.title = `Selected: ${fileName}`;
                label.style.background = '#7a9b8f';
            }
        });

        // Select admin function (for volunteer side, there's only one admin to talk to)
        function selectAdmin() {
            // Admin is already selected by default - this function exists for consistency
            // but volunteers only communicate with administrators
            console.log('Admin selected');
        }

        // Auto-resize textarea
        document.getElementById('messageInput').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Allow Enter to send message (Shift+Enter for new line)
        document.getElementById('messageInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('chatForm').dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>
