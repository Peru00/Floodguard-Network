<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Dashboard specific additions aligned to provided static HTML & screenshot */
        .profile-header {display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;}
        .profile-header h2 {font-weight:600;margin:0;}
        .volunteer-id {font-size:12px;color:#666;margin-top:4px;}
        .profile-stats {display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;}
        .profile-stats .stat-item {background:#eef2f3;border:1px solid #e5e7eb;border-radius:6px;padding:14px;display:flex;align-items:center;gap:12px;}
        .profile-stats .stat-item i {font-size:18px;color:var(--secondary-color);}
        .profile-stats .stat-item span {display:block;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#555;margin-bottom:4px;}
        .profile-stats .stat-item strong {font-size:13px;font-weight:600;color:#222;}
        /* Form */
        .form-header {display:flex;align-items:center;gap:8px;margin-bottom:18px;}
        .form-header h2 {font-size:18px;margin:0;font-weight:600;display:flex;align-items:center;gap:8px;}
        .donation-type-row {display:flex;flex-wrap:wrap;align-items:center;gap:38px;padding:10px 5px 5px 5px;}
        .donation-type-row .option {display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;}
        .donation-type-row input {width:16px;height:16px;}
        .conditional-block {display:none;margin-top:18px;}
        .conditional-block.active {display:block;}
        .inline-fields {display:flex;flex-wrap:wrap;gap:20px;margin-bottom:14px;}
        .inline-fields .form-group {flex:1 min-width:200px;}
        .form-actions {display:flex;justify-content:flex-end;gap:10px;margin-top:10px;}
        .alert {font-size:14px;}
        /* Notifications */
        .notification-container {margin-top:30px;}
        .notification-item {background:#fff;border-radius:6px;padding:18px 20px;margin-bottom:14px;border:1px solid #eee;position:relative;border-left:4px solid #ddd;}
        .notification-item.approved{border-left-color:#2ed573;}
        .notification-item.rejected{border-left-color:#ff4757;}
        .notification-item.pending{border-left-color:#ffa502;}
        .notification-title {display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;}
        .notification-title h3 {margin:0;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;}
        .status-badge {font-size:10px;padding:4px 8px;border-radius:20px;font-weight:600;letter-spacing:.5px;}
        .status-approved{background:#d1fae5;color:#065f46;}
        .status-rejected{background:#fee2e2;color:#991b1b;}
        .status-pending{background:#fef3c7;color:#92400e;}
        .notification-time {font-size:11px;color:#6b7280;}
        .notification-message {font-size:13px;color:#222;margin-bottom:4px;}
        .view-link {font-size:12px;color:#2563eb;font-weight:500;text-decoration:none;}
        .view-link:hover{text-decoration:underline;}
        @media (max-width: 768px){
            .donation-type-row {gap:16px;}
            .profile-stats {grid-template-columns:repeat(auto-fit,minmax(140px,1fr));}
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
            <a href="{{ route('donor.dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
            <a href="{{ route('donor.distribution-repository') }}"><i class="fas fa-archive"></i> <span>Distribution Repo</span></a>
            <div class="admin-profile" style="cursor: pointer;" onclick="window.location.href='{{ route('donor.edit-profile') }}'">
                <img src="{{ Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : 'https://randomuser.me/api/portraits/men/32.jpg' }}" alt="Donor">
                <div>
                    <p>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <small>Donor</small>
                </div>
            </div>
            <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <main class="main-content">
        <!-- Profile & Stats -->
        <section class="volunteer-hero" style="margin-bottom:25px;">
            <div class="volunteer-profile" style="background:#fff;border-radius:8px;padding:24px;border:1px solid #e5e7eb;">
                <div class="profile-header">
                    <div>
                        <h2>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h2>
                        <p class="volunteer-id">Donor ID: {{ Auth::user()->user_id ?? 'USER-'.time() }}</p>
                    </div>
                </div>
                <div class="profile-stats">
                    <div class="stat-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <span>Phone No</span>
                            <strong>{{ Auth::user()->phone ?? 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <span>Email</span>
                            <strong>{{ Auth::user()->email }}</strong>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-hand-holding-usd"></i>
                        <div>
                            <span>Total Donations</span>
                            <strong>{{ $totalDonations ?? 0 }} Contributions</strong>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-money-bill-wave"></i>
                        <div>
                            <span>Total Amount</span>
                            <strong>${{ number_format($totalAmount ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-calendar-check"></i>
                        <div>
                            <span>Last Donation</span>
                            <strong>
                                @if(isset($donations) && $donations->count())
                                    {{ optional($donations->first()->created_at)->format('M d, Y') }}
                                @else
                                    ---
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add Donation -->
        <section class="table-container" style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:24px;margin-bottom:28px;">
            <div class="form-header">
                <h2><i class="fas fa-plus-circle"></i> Add New Donation</h2>
            </div>

            @if(session('success'))
                <div class="alert alert-success" style="background:#d1fae5;border:1px solid #10b981;color:#065f46;padding:10px 14px;border-radius:6px;margin-bottom:16px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" style="background:#fee2e2;border:1px solid #ef4444;color:#991b1b;padding:10px 14px;border-radius:6px;margin-bottom:16px;">{{ session('error') }}</div>
            @endif

            <form id="donationForm" class="report-form compact" method="POST" action="{{ route('donor.submit-donation') }}">
                @csrf
                <div class="donation-type-row">
                    <label class="option"><input type="radio" name="donation_type" value="money" required> Money</label>
                    <label class="option"><input type="radio" name="donation_type" value="food"> Food</label>
                    <label class="option"><input type="radio" name="donation_type" value="clothing"> Clothing</label>
                    <label class="option"><input type="radio" name="donation_type" value="medicine"> Medicine</label>
                    <label class="option"><input type="radio" name="donation_type" value="other"> Other</label>
                </div>

                <div id="moneyFields" class="conditional-block">
                    <div class="inline-fields">
                        <div class="form-group">
                            <label>Amount (USD) *</label>
                            <input type="number" step="0.01" name="money_amount" placeholder="e.g. 150" />
                        </div>
                        <div class="form-group">
                            <label>Payment Method *</label>
                            <select name="payment_method">
                                <option value="">Select method</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="mobile">Mobile Payment</option>
                                <option value="credit">Credit Card</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Transaction ID *</label>
                            <input type="text" name="transaction_id" placeholder="Enter transaction reference" />
                        </div>
                    </div>
                </div>

                <div id="itemFields" class="conditional-block">
                    <div class="inline-fields">
                        <div class="form-group">
                            <label>Quantity *</label>
                            <input type="text" name="item_quantity" placeholder="e.g. 25 boxes" />
                        </div>
                        <div class="form-group">
                            <label>Expiry Date <small>(Food/Medicine)</small></label>
                            <input type="date" name="expiry_date" />
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:10px;">
                        <label>Description *</label>
                        <textarea name="description" rows="3" placeholder="Describe the items you wish to donate"></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary" id="resetBtn">Clear Form</button>
                    <button type="submit" class="btn btn-primary">Submit Donation</button>
                </div>
            </form>
        </section>

        <!-- Notifications -->
        <section class="table-container notification-container" style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:24px;">
            <div class="notification-header" style="margin-bottom:14px;">
                <h2 style="font-size:18px;margin:0;display:flex;align-items:center;gap:8px;"><i class="fas fa-bell"></i> Notifications</h2>
            </div>
            @php $recent = isset($donations)? $donations->take(5) : collect(); @endphp
            @if($recent->isEmpty())
                <div class="notification-item pending">
                    <div class="notification-title">
                        <h3>No Notifications</h3>
                        <span class="notification-time">—</span>
                    </div>
                    <div class="notification-message">You don't have any donation updates yet.</div>
                </div>
            @else
                @foreach($recent as $donation)
                    <div class="notification-item {{ strtolower($donation->status) }}">
                        <div class="notification-title">
                            <h3>
                                Donation {{ ucfirst($donation->status) }}
                                <span class="status-badge status-{{ strtolower($donation->status) }}">{{ strtoupper($donation->status) }}</span>
                            </h3>
                            <span class="notification-time">{{ $donation->created_at ? $donation->created_at->diffForHumans() : $donation->donation_date->diffForHumans() }}</span>
                        </div>
                        <div class="notification-message">
                            @if($donation->donation_type === 'money')
                                Monetary donation of ${{ number_format($donation->amount,2) }}.
                            @else
                                {{ ucfirst($donation->donation_type) }} donation: {{ $donation->items ?? $donation->description }} (Qty: {{ $donation->quantity ?? '1' }}).
                            @endif
                        </div>
                        <a class="view-link" href="{{ route('donor.view-donation', $donation->donation_id) }}">View Details</a>
                    </div>
                @endforeach
            @endif
        </section>
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

    <script>
        (function(){
            const moneyFields = document.getElementById('moneyFields');
            const itemFields = document.getElementById('itemFields');
            const radios = document.querySelectorAll('input[name="donation_type"]');
            const resetBtn = document.getElementById('resetBtn');
            const form = document.getElementById('donationForm');
            
            function updateBlocks(){
                let val = document.querySelector('input[name="donation_type"]:checked')?.value;
                
                // Hide both sections first
                moneyFields.classList.remove('active');
                itemFields.classList.remove('active');
                
                // Clear all required attributes
                moneyFields.querySelectorAll('input,select,textarea').forEach(el => {
                    el.required = false;
                    el.disabled = false; // Ensure fields are not disabled
                });
                itemFields.querySelectorAll('input,select,textarea').forEach(el => {
                    el.required = false;
                    el.disabled = false; // Ensure fields are not disabled
                });
                
                if(val === 'money'){
                    moneyFields.classList.add('active');
                    // Set required fields for money donation
                    const amountField = moneyFields.querySelector('input[name="money_amount"]');
                    const methodField = moneyFields.querySelector('select[name="payment_method"]');
                    const txField = moneyFields.querySelector('input[name="transaction_id"]');
                    
                    if(amountField) amountField.required = true;
                    if(methodField) methodField.required = true;
                    if(txField) txField.required = true;
                } else if(val) {
                    itemFields.classList.add('active');
                    itemFields.querySelector('input[name="item_quantity"]').required = true;
                    itemFields.querySelector('textarea[name="description"]').required = true;
                }
                }
            }
            
            radios.forEach(r => r.addEventListener('change', updateBlocks));
            resetBtn.addEventListener('click', () => {
                moneyFields.classList.remove('active');
                itemFields.classList.remove('active');
            });
        })();
    </script>
</body>
</html>