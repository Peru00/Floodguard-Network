<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution Repository - Volunteer Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <li><a href="{{ route('volunteer.distribution-repository') }}" class="active"><i class="fas fa-archive"></i> <span>Distribution Repo</span></a></li>
            <li>
                <div class="admin-profile" style="cursor: pointer;" onclick="window.location.href='{{ route('volunteer.edit-profile') }}'">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Volunteer Profile">
                    @else
                        <img src="{{ asset('assets/profile-user.png') }}" alt="Volunteer Profile">
                    @endif
                    <div>
                        <p>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
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
            <!-- Page Header -->
            <section class="stats-section">
                <h2><i class="fas fa-history"></i> My Distribution History</h2>
                <p style="color: #666; margin-bottom: 20px;">Track all relief distributions you have completed</p>
            </section>

            <!-- Distribution Records -->
            <section class="distribution-section">
                <div class="table-container">
                    <div class="table-header">
                        <h3>Distribution Records</h3>
                        <div class="table-summary">
                            <strong>Total: {{ $distributions->total() }} distributions</strong>
                        </div>
                    </div>
                    
                    @if($distributions->count() > 0)
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Distribution ID</th>
                                    <th>Date & Time</th>
                                    <th>Beneficiary</th>
                                    <th>Relief Type</th>
                                    <th>Quantity</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($distributions as $distribution)
                                <tr>
                                    <td>
                                        <strong>{{ $distribution->distribution_id }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ \Carbon\Carbon::parse($distribution->distribution_date)->format('M d, Y') }}</strong><br>
                                            <small style="color: #666;">{{ \Carbon\Carbon::parse($distribution->distribution_date)->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-user-injured" style="color: var(--primary-color);"></i>
                                            <strong>{{ $distribution->victim_name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-box" style="color: var(--secondary-color);"></i>
                                            {{ $distribution->relief_type }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: rgba(76, 175, 80, 0.1); color: var(--success-color);">
                                            {{ $distribution->quantity }} units
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-map-marker-alt" style="color: var(--warning-color);"></i>
                                            {{ $distribution->location }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge completed">
                                            <i class="fas fa-check"></i> Completed
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        <div class="table-footer">
                            <div class="table-info">
                                Showing {{ $distributions->firstItem() }} to {{ $distributions->lastItem() }} of {{ $distributions->total() }} distributions
                            </div>
                            <div class="pagination">
                                {{ $distributions->links() }}
                            </div>
                        </div>
                    @else
                        <div style="text-align: center; padding: 60px 20px; color: #666;">
                            <i class="fas fa-box" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                            <h3 style="margin-bottom: 10px;">No Distributions Yet</h3>
                            <p>Your completed distributions will appear here once you start completing assigned tasks.</p>
                            <a href="{{ route('volunteer.dashboard') }}" class="btn btn-primary" style="margin-top: 20px;">
                                <i class="fas fa-tachometer-alt"></i> View Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </section>
        </main>
    </div>
</body>
</html>
