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
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid;
            font-size: 0.95rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        
        .alert-error,
        .alert-danger {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        
        .text-danger {
            color: #dc3545 !important;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
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
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-header h3 {
            margin: 0;
            color: var(--primary-color);
        }
        
        .close {
            font-size: 24px;
            cursor: pointer;
            color: #aaa;
        }
        
        .close:hover {
            color: #000;
        }
        
        /* Filter Dropdown */
        .filter-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .filter-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 6px;
            z-index: 1;
            border: 1px solid #ddd;
        }
        
        .filter-content.show {
            display: block;
        }
        
        .filter-content a {
            color: #333;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .filter-content a:hover {
            background-color: #f5f5f5;
        }
        
        .filter-content a:last-child {
            border-bottom: none;
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
            <h1>FloodGuard Volunteer</h1>
        </div>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <li><a href="{{ route('volunteer.dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="{{ route('volunteer.relief-camps') }}"><i class="fas fa-campground"></i> <span>My Relief Camp</span></a></li>
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
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            
            <!-- Inventory Management Section -->
            <section class="inventory-section">
                <div class="section-header">
                    <h2><i class="fas fa-boxes"></i> Relief Inventory Management</h2>
                    <div class="section-actions">
                        <button class="btn btn-primary" onclick="openAddItemModal()"><i class="fas fa-plus"></i> Add Item</button>
                        <div class="filter-dropdown">
                            <button class="btn btn-secondary" onclick="toggleFilterDropdown()"><i class="fas fa-filter"></i> Filter</button>
                            <div id="filterDropdown" class="filter-content">
                                <a href="{{ route('volunteer.inventory') }}">All Items</a>
                                <a href="{{ route('volunteer.inventory', ['filter' => 'good_stock']) }}">Good Stock (>50)</a>
                                <a href="{{ route('volunteer.inventory', ['filter' => 'low_stock']) }}">Low Stock (1-50)</a>
                                <a href="{{ route('volunteer.inventory', ['filter' => 'out_of_stock']) }}">Out of Stock</a>
                                <hr style="margin: 5px 0; border: none; border-top: 1px solid #eee;">
                                <a href="{{ route('volunteer.inventory', ['filter' => 'food']) }}">Food Items</a>
                                <a href="{{ route('volunteer.inventory', ['filter' => 'clothing']) }}">Clothing</a>
                                <a href="{{ route('volunteer.inventory', ['filter' => 'medical']) }}">Medical Supplies</a>
                            </div>
                        </div>
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
                                            <form method="POST" action="{{ route('volunteer.inventory.delete', $item->inventory_id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this item?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn delete-btn" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
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
    
    <!-- Add Item Modal -->
    <div id="addItemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus-circle"></i> Add New Inventory Item</h3>
                <span class="close" onclick="closeAddItemModal()">&times;</span>
            </div>
            
            <form action="{{ route('volunteer.inventory.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="itemName">Item Name</label>
                        <input type="text" id="itemName" name="item_name" placeholder="Enter item name" required value="{{ old('item_name') }}">
                        @error('item_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="itemType">Item Type</label>
                        <select id="itemType" name="item_type" required>
                            <option value="">Select item type</option>
                            <option value="food" {{ old('item_type') == 'food' ? 'selected' : '' }}>Food</option>
                            <option value="clothing" {{ old('item_type') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                            <option value="medical" {{ old('item_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                            <option value="other" {{ old('item_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('item_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" min="1" placeholder="Enter quantity" required value="{{ old('quantity') }}">
                        @error('quantity')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <input type="text" id="unit" name="unit" placeholder="e.g., pieces, kg, liters" value="{{ old('unit', 'units') }}">
                        @error('unit')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="itemDescription">Description (Optional)</label>
                        <textarea id="itemDescription" name="item_description" placeholder="Enter item description" rows="3">{{ old('item_description') }}</textarea>
                        @error('item_description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="expiryDate">Expiry Date (Optional)</label>
                        <input type="date" id="expiryDate" name="expiry_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('expiry_date') }}">
                        @error('expiry_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                        <button type="button" class="btn btn-secondary" onclick="closeAddItemModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        .form-group {
            margin-bottom: 15px;
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
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary-color);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }
    </style>
    
    <script>
        // Modal functions
        function openAddItemModal() {
            document.getElementById('addItemModal').style.display = 'block';
        }
        
        function closeAddItemModal() {
            document.getElementById('addItemModal').style.display = 'none';
        }
        
        // Filter dropdown
        function toggleFilterDropdown() {
            document.getElementById("filterDropdown").classList.toggle("show");
        }
        
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.btn')) {
                var dropdowns = document.getElementsByClassName("filter-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
            
            // Close modal when clicking outside
            if (event.target.classList.contains('modal')) {
                closeAddItemModal();
            }
        }
        
        // Auto-open modal if there are validation errors
        @if ($errors->any())
            window.onload = function() {
                openAddItemModal();
            };
        @endif
    </script>
</body>
</html>
