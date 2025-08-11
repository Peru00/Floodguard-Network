<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add these new styles */
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

        form[method="POST"] {
            display: inline-flex;
            gap: 5px;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            padding: 20px 30px;
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
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .modal-body {
            padding: 20px 30px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: 600;
            width: 150px;
            color: var(--primary-color);
        }

        .detail-value {
            flex: 1;
            color: #333;
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
            <li class="active"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Volunteers</a></li>
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
            <!-- Stats Overview Section -->
            <section class="stats-section">
                <h2>Dashboard Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Volunteers</h3>
                            <p>{{ $stats['volunteers'] }}</p>
                            <span class="stat-change neutral"><i class="fas fa-users"></i> Active volunteers</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Donors</h3>
                            <p>{{ $stats['donors'] }}</p>
                            <span class="stat-change neutral"><i class="fas fa-heart"></i> Registered donors</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-donate"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Donations</h3>
                            <p>${{ number_format($stats['total_donations'], 2) }}</p>
                            <span class="stat-change up"><i class="fas fa-dollar-sign"></i> Approved donations</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Distributed Items</h3>
                            <p>{{ $stats['distributions'] }}</p>
                            <span class="stat-change neutral"><i class="fas fa-truck"></i> Total distributed</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Affected Locations</h3>
                            <p>{{ $stats['locations'] }}</p>
                            <span class="stat-change neutral"><i class="fas fa-globe"></i> Relief locations</span>
                        </div>
                    </div>
                </div>
            </section>

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

            <!-- Donations Management Section -->
            <section class="donations-section">
                <div class="section-header">
                    <h2>Pending Donation Requests</h2>
                    <div class="section-actions">
                        <button class="btn btn-secondary"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Donation ID</th>
                                <th>Donor Name</th>
                                <th>Type</th>
                                <th>Amount/Items</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingDonations as $donation)
                                <tr>
                                    <td>{{ $donation->donation_id }}</td>
                                    <td>{{ $donation->user->first_name }} {{ $donation->user->last_name }}</td>
                                    <td>
                                        <span class="badge @if($donation->donation_type === 'monetary') info @else warning @endif">
                                            {{ ucfirst($donation->donation_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($donation->donation_type === 'monetary')
                                            ${{ number_format($donation->amount, 2) }}
                                        @else
                                            {{ $donation->quantity }} {{ $donation->items }}
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge pending">{{ ucfirst($donation->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="action-btn view-btn" onclick="viewDonation('{{ $donation->donation_id }}')" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.donation.update-status') }}" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="donation_id" value="{{ $donation->donation_id }}">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="action-btn approve-btn" onclick="return confirm('Are you sure you want to approve this donation?')" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.donation.update-status') }}" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="donation_id" value="{{ $donation->donation_id }}">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="action-btn reject-btn" onclick="return confirm('Are you sure you want to reject this donation?')" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: #666;">
                                        <i class="fas fa-inbox"></i><br>
                                        No pending donations to review
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Recent Donations Section -->
            <section class="donations-section">
                <div class="section-header">
                    <h2>Recent Donations</h2>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Donation ID</th>
                                <th>Donor Name</th>
                                <th>Type</th>
                                <th>Amount/Items</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDonations as $donation)
                                <tr>
                                    <td>{{ $donation->donation_id }}</td>
                                    <td>{{ $donation->user->first_name }} {{ $donation->user->last_name }}</td>
                                    <td>
                                        <span class="badge @if($donation->donation_type === 'monetary') info @else warning @endif">
                                            {{ ucfirst($donation->donation_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($donation->donation_type === 'monetary')
                                            ${{ number_format($donation->amount, 2) }}
                                        @else
                                            {{ $donation->quantity }} {{ $donation->items }}
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge {{ $donation->status === 'approved' ? 'approved' : ($donation->status === 'rejected' ? 'rejected' : 'pending') }}">
                                            {{ ucfirst($donation->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2rem; color: #666;">
                                        <i class="fas fa-inbox"></i><br>
                                        No donations found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Donation Details Modal -->
    <div id="donationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Donation Details</h3>
                <span class="close" onclick="closeDonationModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="detail-label">Donation ID:</div>
                    <div class="detail-value" id="modal-donation-id">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Donor Name:</div>
                    <div class="detail-value" id="modal-donor-name">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Type:</div>
                    <div class="detail-value" id="modal-donation-type">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Amount/Items:</div>
                    <div class="detail-value" id="modal-amount-items">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div class="detail-value" id="modal-date">-</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value" id="modal-status">-</div>
                </div>
                <div class="detail-row" id="payment-info" style="display: none;">
                    <div class="detail-label">Payment Method:</div>
                    <div class="detail-value" id="modal-payment-method">-</div>
                </div>
                <div class="detail-row" id="transaction-info" style="display: none;">
                    <div class="detail-label">Transaction ID:</div>
                    <div class="detail-value" id="modal-transaction-id">-</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Store donation data for modal display
        const donationData = {
            @foreach($pendingDonations->concat($recentDonations) as $donation)
            '{{ $donation->donation_id }}': {
                id: '{{ $donation->donation_id }}',
                donorName: '{{ $donation->user->first_name }} {{ $donation->user->last_name }}',
                type: '{{ ucfirst($donation->donation_type) }}',
                amount: @if($donation->donation_type === 'monetary') '{{ "$" . number_format($donation->amount, 2) }}' @else '{{ $donation->quantity }} {{ $donation->items }}' @endif,
                date: '{{ \Carbon\Carbon::parse($donation->donation_date)->format("M d, Y") }}',
                status: '{{ ucfirst($donation->status) }}',
                paymentMethod: '{{ $donation->payment_method ?? "N/A" }}',
                transactionId: '{{ $donation->transaction_id ?? "N/A" }}'
            },
            @endforeach
        };

        function viewDonation(donationId) {
            const donation = donationData[donationId];
            if (!donation) {
                alert('Donation details not found');
                return;
            }

            // Populate modal with donation data
            document.getElementById('modal-donation-id').textContent = donation.id;
            document.getElementById('modal-donor-name').textContent = donation.donorName;
            document.getElementById('modal-donation-type').textContent = donation.type;
            document.getElementById('modal-amount-items').textContent = donation.amount;
            document.getElementById('modal-date').textContent = donation.date;
            document.getElementById('modal-status').textContent = donation.status;
            document.getElementById('modal-payment-method').textContent = donation.paymentMethod;
            document.getElementById('modal-transaction-id').textContent = donation.transactionId;

            // Show/hide payment info based on donation type
            const paymentInfo = document.getElementById('payment-info');
            const transactionInfo = document.getElementById('transaction-info');
            
            if (donation.type === 'Monetary') {
                paymentInfo.style.display = 'flex';
                transactionInfo.style.display = 'flex';
            } else {
                paymentInfo.style.display = 'none';
                transactionInfo.style.display = 'none';
            }

            // Show modal
            document.getElementById('donationModal').style.display = 'block';
        }

        function closeDonationModal() {
            document.getElementById('donationModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('donationModal');
            if (event.target == modal) {
                closeDonationModal();
            }
        }
    </script>
</body>
</html>
