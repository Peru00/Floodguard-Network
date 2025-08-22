<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Floodguard Network</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Override body background for login page */
        body {
            background: white !important;
            min-height: 100vh;
        }
        
        /* Login/Signup Section */
        .auth-section {
            padding: 0 5%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .auth-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            position: relative;
        }
        
        .auth-form {
            padding: 3rem;
            transition: all 0.3s ease;
        }
        
        .auth-form h2 {
            color: var(--primary-color);
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
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
        }
        
        .submit-container {
            display: flex;
            justify-content: center;
            margin: 2rem 0 1rem;
        }
        
        .btn-submit {
            padding: 0.8rem 2rem;
            background-color: var(--secondary-color);
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
            background-color: var(--accent-color);
        }
        
        .toggle-auth {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .toggle-auth a {
            color: var(--secondary-color);
        .toggle-auth a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .toggle-auth a:hover {
            text-decoration: underline;
        }
        
        /* Animation */
        .fade-in {
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Error message styling */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Login Section -->
    <section id="login-section" class="auth-section">
        <div class="auth-container">
            <div class="auth-form glass fade-in">
                <h2>Login</h2>
                
                @if(session('error') || $errors->any())
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> 
                        @if(session('error'))
                            {{ session('error') }}<br>
                        @endif
                        @if($errors->any())
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        @endif
                    </div>
                @endif

                @if(session('status'))
                    <div style="background-color: #d4edda; color: #155724; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                        <i class="fas fa-check-circle"></i> {{ session('status') }}
                    </div>
                @endif

                @if(session('success'))
                    <div style="background-color: #d4edda; color: #155724; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('message'))
                    <div style="background-color: #d1ecf1; color: #0c5460; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                        <i class="fas fa-info-circle"></i> {{ session('message') }}
                    </div>
                @endif
                
                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    
                    <div class="submit-container">
                        <button type="submit" class="btn-submit">Login</button>
                    </div>
                    
                    <div class="toggle-auth">
                        <a href="{{ route('forgot-password') }}">Forgot Password?</a>
                    </div>
                    
                    <div class="toggle-auth">
                        Don't have an account? <a href="{{ route('signup') }}">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
