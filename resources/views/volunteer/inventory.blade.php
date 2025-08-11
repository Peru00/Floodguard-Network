<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - HelpHub</title>
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

        /* Inventory specific styles */
        .inventory-section {
            margin-bottom: 30px;
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
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 0;
        }
        
        .section-actions {
            display: flex;
            gap: 10px;
        }
        
        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: var(--text-light);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 15px;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(138, 178, 166, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color);
            font-size: 20px;
        }
        
        .stat-info h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stat-change {
            font-size: 12px;
        }
        
        .stat-change.up {
            color: var(--success-color);
        }
        
        .stat-change.down {
            color: var(--danger-color);
        }
        
        /* Table Styles */
        .table-container {
            background-color: var(--text-light);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            margin-bottom: 15px;
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
        
        .badge.good {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
        }
        
        .badge.warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }
        
        .badge.danger {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
        }
        
        /* Action Buttons */
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background-color: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            background-color: #f0f0f0;
        }
        
        .delete-btn {
            color: var(--danger-color);
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
            <li><a href="{{ route('volunteer.inventory') }}" class="active"><i class="fas fa-boxes"></i> <span>Inventory</span></a></li>
            <li><a href="{{ route('volunteer.victims') }}"><i class="fas fa-users"></i> <span>Victims</span></a></li>
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
            <!-- Inventory Management Section -->
            <section class="inventory-section">
                <div class="section-header">
                    <h2><i class="fas fa-boxes"></i> Relief Inventory Management</h2>
                    <div class="section-actions">
                        <button class="btn btn-primary"><i class="fas fa-plus"></i> Add Item</button>
                        <button class="btn btn-secondary"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
                
                <!-- Inventory Summary Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Items</h3>
                            <p>{{ $inventoryItems->total() }}</p>
                            <span class="stat-change up"><i class="fas fa-arrow-up"></i> Active inventory</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Available Items</h3>
                            <p>{{ $inventoryItems->where('quantity', '>', 0)->count() }}</p>
                            <span class="stat-change up"><i class="fas fa-arrow-up"></i> In stock</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Low Stock</h3>
                            <p>{{ $inventoryItems->where('quantity', '>', 0)->where('quantity', '<=', 50)->count() }}</p>
                            <span class="stat-change down"><i class="fas fa-arrow-down"></i> Needs restocking</span>
                        </div>
                    </div>
                </div>
                
                @if($inventoryItems->count() > 0)
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Item ID</th>
                                    <th>Relief Type</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventoryItems as $item)
                                    <tr>
                                        <td>INV-{{ str_pad($item->inventory_id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ ucfirst($item->item_name) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->unit ?? 'units' }}</td>
                                        <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d') : 'N/A' }}</td>
                                        <td>
                                            @if($item->quantity > 50)
                                                <span class="badge good">Good</span>
                                            @elseif($item->quantity > 0)
                                                <span class="badge warning">Low Stock</span>
                                            @else
                                                <span class="badge danger">Out of Stock</span>
                                            @endif
                                        </td>
                                        <td class="actions">
                                            <button class="action-btn delete-btn" title="Delete"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div style="margin-top: 20px;">
                        {{ $inventoryItems->links() }}
                    </div>
                @else
                    <div style="text-align: center; padding: 40px 20px; color: #666;">
                        <i class="fas fa-boxes" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                        <p>No inventory items found.</p>
                        <small>Inventory items will appear here when added by administrators.</small>
                    </div>
                @endif
            </section>
        </main>
    </div>
</body>
</html>
