<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dashboard-container {
            padding: 5rem 5%;
            min-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .success-card {
            max-width: 600px;
            width: 100%;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1.5rem;
        }
        
        .success-title {
            font-size: 2.5rem;
            color: #0077cc;
            margin-bottom: 1rem;
            font-weight: bold;
        }
        
        .role-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background-color: #0077cc;
            color: white;
            border-radius: 25px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 1rem 0;
        }
        
        .welcome-message {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .user-info {
            background: rgba(0, 119, 204, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        
        .user-info h3 {
            color: #0077cc;
            margin-bottom: 1rem;
        }
        
        .user-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0, 119, 204, 0.2);
        }
        
        .user-detail:last-child {
            border-bottom: none;
        }
        
        .action-buttons {
            margin-top: 2rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            margin: 0.5rem;
            background-color: #0077cc;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #005fa3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar glass">
            <div class="logo">
                <i class="fas fa-hands-helping"></i>
                <h1>Floodguard Network</h1>
            </div>
            <ul class="nav-links">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="dashboard-container">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="success-title">Login Successful!</h1>
                <div class="role-badge">{{ strtoupper(auth()->user()->role) }}</div>
                
                <p class="welcome-message">
                    Welcome to your {{ ucfirst(auth()->user()->role) }} Dashboard, {{ auth()->user()->first_name }}!
                    You have successfully logged into the Floodguard Network system.
                </p>
                
                <div class="user-info">
                    <h3><i class="fas fa-user"></i> Your Information</h3>
                    <div class="user-detail">
                        <strong>Name:</strong>
                        <span>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                    </div>
                    <div class="user-detail">
                        <strong>Email:</strong>
                        <span>{{ auth()->user()->email }}</span>
                    </div>
                    <div class="user-detail">
                        <strong>Phone:</strong>
                        <span>{{ auth()->user()->phone }}</span>
                    </div>
                    <div class="user-detail">
                        <strong>Role:</strong>
                        <span>{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                    <div class="user-detail">
                        <strong>Status:</strong>
                        <span>{{ ucfirst(auth()->user()->status) }}</span>
                    </div>
                    <div class="user-detail">
                        <strong>Member Since:</strong>
                        <span>{{ auth()->user()->registration_date->format('F j, Y') }}</span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="{{ route('home') }}" class="btn">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                    <a href="{{ route('logout') }}" class="btn btn-secondary">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
                
                @if(auth()->user()->role === 'admin')
                <div style="margin-top: 2rem; padding: 1rem; background-color: #d4edda; border-radius: 5px; color: #155724;">
                    <strong>Admin Features Coming Soon:</strong>
                    <ul style="text-align: left; margin-top: 1rem;">
                        <li>User Management</li>
                        <li>Donation Approval</li>
                        <li>Inventory Management</li>
                        <li>Reports & Analytics</li>
                    </ul>
                </div>
                @elseif(auth()->user()->role === 'volunteer')
                <div style="margin-top: 2rem; padding: 1rem; background-color: #d1ecf1; border-radius: 5px; color: #0c5460;">
                    <strong>Volunteer Features Coming Soon:</strong>
                    <ul style="text-align: left; margin-top: 1rem;">
                        <li>Task Assignments</li>
                        <li>Distribution Tracking</li>
                        <li>Victim Registration</li>
                        <li>Communication Hub</li>
                    </ul>
                </div>
                @elseif(auth()->user()->role === 'donor')
                <div style="margin-top: 2rem; padding: 1rem; background-color: #fff3cd; border-radius: 5px; color: #856404;">
                    <strong>Donor Features Coming Soon:</strong>
                    <ul style="text-align: left; margin-top: 1rem;">
                        <li>Make Donations</li>
                        <li>Track Donations</li>
                        <li>View Impact Reports</li>
                        <li>Donation History</li>
                    </ul>
                </div>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
