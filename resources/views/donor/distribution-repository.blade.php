<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution Repository - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Match dashboard styling */
        .donor-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .page-header {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 25px;
            margin-top: 100px;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
        }
        
        .page-header p {
            color: #666;
            margin: 12px 0 0 0;
            font-size: 16px;
            font-weight: 500;
        }
        
        .distributions-container {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table-header {
            background: var(--secondary-color);
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .impact-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin-bottom: 28px;
        }
        
        .stat-card {
            background: #eef2f3;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 14px;
            text-align: center;
        }
        
        .stat-card i {
            font-size: 18px;
            color: var(--secondary-color);
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 13px;
            font-weight: 600;
            color: #222;
        }
        
        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #555;
            margin-top: 4px;
        }
        
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            border: var(--glass-border);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar .logo {
            display: flex;
            align-items: center;
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .navbar .logo i {
            margin-right: 10px;
        }
        
        .navbar .nav-links {
            display: flex;
            list-style: none;
            gap: 20px;
            margin: 0;
            padding: 0;
        }
        
        .navbar .nav-links a {
            color: var(--primary-color);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .navbar .nav-links a:hover,
        .navbar .nav-links a.active {
            background: var(--secondary-color);
            color: white;
        }
        
        .distribution-item {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--secondary-color);
            transition: all 0.3s ease;
        }
        
        .distribution-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .distribution-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .distribution-id {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .distribution-date {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .distribution-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-icon {
            color: var(--secondary-color);
            width: 20px;
        }
        
        .detail-label {
            font-weight: 500;
            color: var(--text-secondary);
        }
        
        .detail-value {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #222;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .empty-state p {
            color: #666;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .distribution-details {
                grid-template-columns: 1fr;
            }
            
            .distribution-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .impact-stats {
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <i class="fas fa-hands-helping"></i>
                <span>Floodguard Network</span>
            </div>
            <ul class="nav-links">
                <li><a href="{{ route('donor.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('donor.donations') }}">My Donations</a></li>
                <li><a href="{{ route('donor.distribution-repository') }}" class="active">Distribution</a></li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="donor-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-truck"></i> Distribution Repository</h1>
            <p>See how your donations are making a real impact on flood victims' lives</p>
        </div>

        <!-- Impact Statistics -->
        <div class="impact-stats">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-value">{{ count($distributions) }}</div>
                <div class="stat-label">Total Distributions</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-map-marker-alt"></i>
                <div class="stat-value">{{ collect($distributions)->unique('location')->count() }}</div>
                <div class="stat-label">Locations Served</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-heart"></i>
                <div class="stat-value">{{ collect($distributions)->unique('victim_name')->count() }}</div>
                <div class="stat-label">People Helped</div>
            </div>
        </div>

        <!-- Distributions Container -->
        <div class="distributions-container">
            <div class="table-header">
                <i class="fas fa-list"></i>
                Recent Distribution Activities
            </div>
            
            <div style="padding: 20px;">
                @if(count($distributions) > 0)
                    @foreach($distributions as $distribution)
                        <div class="distribution-item">
                            <div class="distribution-header">
                                <div class="distribution-id">
                                    <i class="fas fa-box"></i> {{ $distribution->distribution_id }}
                                </div>
                                <div class="distribution-date">
                                    {{ \Carbon\Carbon::parse($distribution->distribution_date)->format('M d, Y h:i A') }}
                                </div>
                            </div>
                            
                            <div class="distribution-details">
                                <div class="detail-item">
                                    <i class="fas fa-user-friends detail-icon"></i>
                                    <span class="detail-label">Volunteer:</span>
                                    <span class="detail-value">{{ $distribution->volunteer_name }}</span>
                                </div>
                                
                                <div class="detail-item">
                                    <i class="fas fa-user detail-icon"></i>
                                    <span class="detail-label">Victim:</span>
                                    <span class="detail-value">{{ $distribution->victim_name }}</span>
                                </div>
                                
                                <div class="detail-item">
                                    <i class="fas fa-box-open detail-icon"></i>
                                    <span class="detail-label">Relief Type:</span>
                                    <span class="detail-value">{{ $distribution->relief_type }}</span>
                                </div>
                                
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt detail-icon"></i>
                                    <span class="detail-label">Location:</span>
                                    <span class="detail-value">{{ $distribution->location }}</span>
                                </div>
                                
                                <div class="detail-item">
                                    <i class="fas fa-sort-numeric-up detail-icon"></i>
                                    <span class="detail-label">Quantity:</span>
                                    <span class="detail-value">{{ $distribution->quantity }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-truck"></i>
                        <h3>No distribution records found</h3>
                        <p>Distribution activities will appear here once relief operations begin.</p>
                        <p style="margin-top: 20px;">
                            <a href="{{ route('donor.dashboard') }}" style="color: var(--secondary-color); text-decoration: none;">
                                <i class="fas fa-heart"></i> Make a donation to support relief efforts
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        @if(count($distributions) > 0)
            <div style="text-align: center; margin-top: 30px; padding: 20px; background: rgba(46, 213, 115, 0.1); border-radius: 10px;">
                <h3 style="color: var(--primary-color); margin-bottom: 10px;">
                    <i class="fas fa-heart"></i> Thank You for Your Support!
                </h3>
                <p style="color: var(--text-secondary); margin: 0;">
                    Your generous donations enable our volunteers to provide direct relief to flood victims in need. 
                    Every contribution, no matter the size, makes a meaningful difference in someone's life.
                </p>
            </div>
        @endif
    </div>
</body>
</html>
