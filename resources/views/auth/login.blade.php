<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Login/Signup Section */
        .auth-section {
            padding: 5rem 5%;
            min-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .auth-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            position: relative;
        }
        
        .auth-form {
            padding: 3rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .auth-form h2 {
            color: #0077cc;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #0077cc;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0077cc;
        }
        
        .submit-container {
            display: flex;
            justify-content: center;
            margin: 2rem 0 1rem;
        }
        
        .btn-submit {
            padding: 0.8rem 2rem;
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 200px;
            text-align: center;
        }
        
        .btn-submit:hover {
            background-color: #005fa3;
        }
        
        .toggle-auth {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .toggle-auth a {
            color: #0077cc;
            text-decoration: none;
            font-weight: 500;
        }
        
        .toggle-auth a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-message {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        #location-group {
            display: none;
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
                <li><a href="{{ route('login') }}" class="active">Login/Signup</a></li>
                <li><a href="#">Admin Dashboard</a></li>
                <li><a href="#">Volunteer Dashboard</a></li>
                <li><a href="#">Donor Dashboard</a></li>
                <li><a href="{{ route('home') }}#emergency-contact">Emergency Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="auth-section">
            <div class="auth-container">
                <!-- Login Form -->
                <div id="login-form" class="auth-form">
                    <h2>Login</h2>
                    <form method="POST" action="{{ route('login.submit') }}">
                        @csrf
                        <div class="form-group">
                            <label for="login-email">Email:</label>
                            <input type="email" id="login-email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="login-password">Password:</label>
                            <input type="password" id="login-password" name="password" required>
                            @error('password')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="submit-container">
                            <button type="submit" class="btn-submit">Login</button>
                        </div>
                    </form>
                    <div class="toggle-auth">
                        <p>Don't have an account? <a href="#" onclick="toggleForms()">Sign up here</a></p>
                    </div>
                </div>

                <!-- Signup Form -->
                <div id="signup-form" class="auth-form" style="display: none;">
                    <h2>Sign Up</h2>
                    <form method="POST" action="{{ route('signup.submit') }}">
                        @csrf
                        <div class="form-group">
                            <label for="signup-first-name">First Name:</label>
                            <input type="text" id="signup-first-name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="signup-last-name">Last Name:</label>
                            <input type="text" id="signup-last-name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="signup-email">Email:</label>
                            <input type="email" id="signup-email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="signup-phone">Phone:</label>
                            <input type="tel" id="signup-phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="signup-password">Password:</label>
                            <input type="password" id="signup-password" name="password" required>
                            @error('password')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="signup-confirm-password">Confirm Password:</label>
                            <input type="password" id="signup-confirm-password" name="password_confirmation" required>
                        </div>
                        <div class="form-group">
                            <label for="signup-role">Role:</label>
                            <select id="signup-role" name="role" onchange="toggleLocationInput()" required>
                                <option value="">Select Role</option>
                                <option value="volunteer" {{ old('role') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                                <option value="donor" {{ old('role') == 'donor' ? 'selected' : '' }}>Donor</option>
                            </select>
                            @error('role')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group" id="location-group">
                            <label for="signup-location">Location:</label>
                            <input type="text" id="signup-location" name="location" value="{{ old('location') }}">
                            @error('location')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="submit-container">
                            <button type="submit" class="btn-submit">Sign Up</button>
                        </div>
                    </form>
                    <div class="toggle-auth">
                        <p>Already have an account? <a href="#" onclick="toggleForms()">Login here</a></p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const signupForm = document.getElementById('signup-form');
            
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                signupForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                signupForm.style.display = 'block';
            }
        }

        function toggleLocationInput() {
            const roleSelect = document.getElementById('signup-role');
            const locationGroup = document.getElementById('location-group');
            locationGroup.style.display = roleSelect.value === 'volunteer' ? 'block' : 'none';
        }

        // Show signup form if there are signup errors
        @if($errors->any() && old('_token') && request()->route()->getName() === 'signup.submit')
            toggleForms();
            @if(old('role') === 'volunteer')
                toggleLocationInput();
            @endif
        @endif
    </script>
</body>
</html>
