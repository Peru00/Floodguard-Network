<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventory Management - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.food { background: #8ab2a6; }
        .stat-icon.medical { background: #8ab2a6; }
        .stat-icon.clothing { background:#8ab2a6; }
        .stat-icon.total { background: #8ab2a6;}
        .stat-icon.available { background: #8ab2a6; }

        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            color: var(--primary-color);
        }

        .stat-info p {
            margin: 0;
            color: #666;
            font-weight: 500;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .form-input, .form-select {
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: var(--primary-color);
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 0.75em;
            font-weight: 600;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
        }

        .badge.available {
            background-color: #28a745;
            color: #ffffff;
        }

        .badge.reserved {
            background-color: #ffc107;
            color: #000000;
        }

        .badge.distributed {
            background-color: #6c757d;
            color: #ffffff;
        }

        .badge.food {
            background-color: #4CAF50;
            color: #ffffff;
        }

        .badge.medical {
            background-color: #f44336;
            color: #ffffff;
        }

        .badge.clothing {
            background-color: #9C27B0;
            color: #ffffff;
        }

        .badge.other {
            background-color: #607D8B;
            color: #ffffff;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            font-size: 0.875rem;
        }

        .edit-btn {
            background-color: #3498db;
        }

        .delete-btn {
            background-color: #e74c3c;
        }

        .action-btn:hover {
            opacity: 0.8;
        }

        .pagination {
            display: flex;
            justify-content: center;
            padding: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            color: var(--primary-color);
            margin: 0;
        }

        .close {
            font-size: 1.5rem;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            border: none;
            background: none;
        }

        .close:hover {
            color: #000;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .form-textarea {
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 14px;
            resize: vertical;
            min-height: 80px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
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
            <li><a href="{{ route('admin.user-management') }}"><i class="fas fa-users-cog"></i> User Management</a></li>
            <li><a href="{{ route('admin.relief-camps') }}"><i class="fas fa-campground"></i> Relief Camps</a></li>
            <li class="active"><a href="{{ route('admin.inventory') }}"><i class="fas fa-box-open"></i> Inventory</a></li>
            <li><a href="{{ route('admin.donations') }}"><i class="fas fa-donate"></i> Donations</a></li>
            <li><a href="{{ route('admin.distribution-repo') }}"><i class="fas fa-truck"></i> Distribution Repo</a></li>
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
            <!-- Page Header -->
            <div class="section-header">
                <h2><i class="fas fa-box-open"></i> Relief Inventory Management</h2>
                <div class="section-actions">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                    <button class="btn btn-secondary" onclick="toggleFilters()">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>

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

            <!-- Inventory Summary Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['total_items']) }}</h3>
                        <p>Total Items</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon food">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['food_items']) }}</h3>
                        <p>Food Items</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon medical">
                        <i class="fas fa-medkit"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['medical_items']) }}</h3>
                        <p>Medical Supplies</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon clothing">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['clothing_items']) }}</h3>
                        <p>Clothing Items</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon available">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['available_items']) }}</h3>
                        <p>Available Items</p>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filters" id="filterSection" style="display: none;">
                <form method="GET" action="{{ route('admin.inventory') }}">
                    <div class="filter-row">
                        <div class="form-group">
                            <label class="form-label">Search Items</label>
                            <input type="text" name="search" class="form-input" placeholder="Search by item name..." value="{{ request('search') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Item Type</label>
                            <select name="item_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="food" {{ request('item_type') == 'food' ? 'selected' : '' }}>Food</option>
                                <option value="medical" {{ request('item_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                                <option value="clothing" {{ request('item_type') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                                <option value="other" {{ request('item_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                <option value="distributed" {{ request('status') == 'distributed' ? 'selected' : '' }}>Distributed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Inventory Table -->
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Added Date</th>
                            <th>Expiry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $item)
                            <tr>
                                <td><strong>{{ $item->inventory_id }}</strong></td>
                                <td>{{ $item->item_name }}</td>
                                <td>
                                    <span class="badge {{ $item->item_type }}">
                                        {{ ucfirst($item->item_type) }}
                                    </span>
                                </td>
                                <td><strong>{{ number_format($item->quantity) }}</strong></td>
                                <td>
                                    <span class="badge {{ $item->status }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>{{ $item->added_date ? \Carbon\Carbon::parse($item->added_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn edit-btn" onclick="openEditModal('{{ $item->inventory_id }}', '{{ $item->item_name }}', '{{ $item->item_type }}', '{{ $item->quantity }}', '{{ $item->status }}', '{{ $item->item_description }}', '{{ $item->expiry_date }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.inventory.delete', $item->inventory_id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: #666;">
                                    <i class="fas fa-box-open" style="font-size: 3rem; color: #ddd; display: block; margin-bottom: 1rem;"></i>
                                    No inventory items found. Click "Add Item" to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if($inventory->hasPages())
                    <div class="pagination">
                        {{ $inventory->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Add Item Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Add New Inventory Item</h3>
                <button class="close" onclick="closeAddModal()">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.inventory.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Item Name *</label>
                        <input type="text" name="item_name" class="form-input" required placeholder="Enter item name">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Item Type *</label>
                        <select name="item_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="food">Food & Water</option>
                            <option value="medical">Medical Supplies</option>
                            <option value="clothing">Clothing</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="quantity" class="form-input" min="1" required placeholder="Enter quantity">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="item_description" class="form-textarea" placeholder="Enter item description (optional)"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-input" min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit Inventory Item</h3>
                <button class="close" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" action="" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Item Name *</label>
                        <input type="text" name="item_name" id="edit_item_name" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Item Type *</label>
                        <select name="item_type" id="edit_item_type" class="form-select" required>
                            <option value="food">Food & Water</option>
                            <option value="medical">Medical Supplies</option>
                            <option value="clothing">Clothing</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="quantity" id="edit_quantity" class="form-input" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="available">Available</option>
                            <option value="reserved">Reserved</option>
                            <option value="distributed">Distributed</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="item_description" id="edit_description" class="form-textarea"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" id="edit_expiry_date" class="form-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleFilters() {
            const filterSection = document.getElementById('filterSection');
            filterSection.style.display = filterSection.style.display === 'none' ? 'block' : 'none';
        }

        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal(id, name, type, quantity, status, description, expiryDate) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            
            form.action = `/admin/inventory/${id}`;
            document.getElementById('edit_item_name').value = name;
            document.getElementById('edit_item_type').value = type;
            document.getElementById('edit_quantity').value = quantity;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_description').value = description || '';
            document.getElementById('edit_expiry_date').value = expiryDate || '';
            
            modal.style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            
            if (event.target == addModal) {
                closeAddModal();
            }
            if (event.target == editModal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
