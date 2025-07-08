<?php
/**
 * Login Page for Madrassah Management System
 * Handles user authentication and redirects to appropriate dashboard
 */

require_once '../../includes/auth.php';

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    $role = $_SESSION['role'];
    header("Location: /views/$role/dashboard.php");
    exit();
}

$pageTitle = 'Login - Madrassah Management System';
$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username/email and password.';
    } else {
        // Attempt login
        if ($auth->login($username, $password)) {
            // Set remember me cookie if requested
            if ($remember) {
                setcookie('remember_user', $username, time() + (30 * 24 * 60 * 60), '/'); // 30 days
            }
            
            // Redirect to appropriate dashboard
            $role = $_SESSION['role'];
            header("Location: /views/$role/dashboard.php");
            exit();
        } else {
            $error = 'Invalid username/email or password. Please try again.';
        }
    }
}

// Check for registration success message
if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $success = 'Registration successful! You can now login with your credentials.';
}

// Check for logout message
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    $success = 'You have been successfully logged out.';
}

// Pre-fill username if remember me cookie exists
$rememberedUser = $_COOKIE['remember_user'] ?? '';
?>

<?php include '../../includes/header.php'; ?>

<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login to Your Account
                    </h4>
                </div>
                <div class="card-body p-5">
                    <!-- Display success message -->
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Display error message -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Login Form -->
                    <form method="POST" class="needs-validation" novalidate>
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                        
                        <!-- Username/Email Field -->
                        <div class="mb-4">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-1"></i>
                                Username or Email
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg" 
                                id="username" 
                                name="username" 
                                value="<?php echo htmlspecialchars($rememberedUser); ?>"
                                placeholder="Enter your username or email"
                                required
                                autocomplete="username"
                            >
                            <div class="invalid-feedback">
                                Please enter your username or email.
                            </div>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>
                                Password
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control form-control-lg" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Enter your password"
                                    required
                                    autocomplete="current-password"
                                >
                                <button 
                                    class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="togglePassword"
                                    data-bs-toggle="tooltip"
                                    title="Show/Hide Password"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Please enter your password.
                            </div>
                        </div>
                        
                        <!-- Remember Me & Forgot Password -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        id="remember" 
                                        name="remember"
                                        <?php echo $rememberedUser ? 'checked' : ''; ?>
                                    >
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <a href="/views/auth/forgot_password.php" class="text-decoration-none">
                                    Forgot Password?
                                </a>
                            </div>
                        </div>
                        
                        <!-- Login Button -->
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Login
                            </button>
                        </div>
                        
                        <!-- Demo Users -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-center mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Demo Accounts
                                </h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <small class="d-block text-muted">Super Admin</small>
                                        <button type="button" class="btn btn-sm btn-outline-primary demo-login" 
                                                data-username="superadmin" data-password="admin123">
                                            Try Demo
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <small class="d-block text-muted">Teacher</small>
                                        <button type="button" class="btn btn-sm btn-outline-success demo-login" 
                                                data-username="teacher" data-password="teacher123">
                                            Try Demo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="mb-0">
                                Don't have an account? 
                                <a href="/views/auth/register.php" class="text-decoration-none fw-bold">
                                    Register here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Islamic Quote -->
            <div class="text-center mt-4">
                <div class="quran-verse">
                    <p class="mb-2">"وَمَن يَتَّقِ اللَّهَ يَجْعَل لَّهُ مَخْرَجًا"</p>
                    <small class="text-muted">"And whoever fears Allah, He will make for him a way out." - Qur'an 65:2</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page-specific JavaScript -->
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').on('click', function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Demo login functionality
    $('.demo-login').on('click', function() {
        const username = $(this).data('username');
        const password = $(this).data('password');
        
        $('#username').val(username);
        $('#password').val(password);
        
        // Optional: Auto-submit after a short delay for better UX
        setTimeout(() => {
            $('form').submit();
        }, 500);
    });
    
    // Auto-focus on username field if empty
    if ($('#username').val() === '') {
        $('#username').focus();
    } else {
        $('#password').focus();
    }
    
    // Form submission loading state
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Logging in...'
        );
        
        // Re-enable if form validation fails
        setTimeout(() => {
            if (!this.checkValidity()) {
                submitBtn.prop('disabled', false).html(originalText);
            }
        }, 100);
    });
});
</script>

<?php include '../../includes/footer.php'; ?>