<?php
/**
 * Registration Page for Madrassah Management System
 * Allows users to register for different roles
 */

require_once '../../includes/auth.php';

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    $role = $_SESSION['role'];
    header("Location: /views/$role/dashboard.php");
    exit();
}

$pageTitle = 'Register - Madrassah Management System';
$error = '';
$success = '';

// Get available roles (excluding super_admin)
$rolesQuery = "SELECT * FROM user_roles WHERE role_name != 'super_admin' ORDER BY id";
$roles = $db->fetchAll($rolesQuery);

// Get madrassahs for selection
$madrassahsQuery = "SELECT * FROM madrassahs WHERE status = 'active' ORDER BY name";
$madrassahs = $db->fetchAll($madrassahsQuery);

// Pre-select role from URL parameter
$selectedRole = sanitizeInput($_GET['role'] ?? '');

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $formData = [
        'username' => sanitizeInput($_POST['username'] ?? ''),
        'email' => sanitizeInput($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
        'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
        'phone' => sanitizeInput($_POST['phone'] ?? ''),
        'date_of_birth' => sanitizeInput($_POST['date_of_birth'] ?? ''),
        'gender' => sanitizeInput($_POST['gender'] ?? 'male'),
        'role_id' => (int)($_POST['role_id'] ?? 0),
        'madrassah_id' => !empty($_POST['madrassah_id']) ? (int)$_POST['madrassah_id'] : null,
        'terms' => isset($_POST['terms'])
    ];
    
    // Validation
    $errors = [];
    
    if (empty($formData['username']) || strlen($formData['username']) < 3) {
        $errors[] = 'Username must be at least 3 characters long.';
    }
    
    if (!validateEmail($formData['email'])) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (strlen($formData['password']) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    
    if ($formData['password'] !== $formData['confirm_password']) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (empty($formData['first_name']) || empty($formData['last_name'])) {
        $errors[] = 'First name and last name are required.';
    }
    
    if ($formData['role_id'] <= 0) {
        $errors[] = 'Please select a valid role.';
    }
    
    // Check if role requires madrassah association
    $selectedRoleData = array_filter($roles, function($role) use ($formData) {
        return $role['id'] == $formData['role_id'];
    });
    $selectedRoleData = reset($selectedRoleData);
    
    if ($selectedRoleData && in_array($selectedRoleData['role_name'], ['teacher', 'student', 'madrassah_admin']) && !$formData['madrassah_id']) {
        $errors[] = 'Please select a madrassah for this role.';
    }
    
    if (!$formData['terms']) {
        $errors[] = 'You must agree to the terms and conditions.';
    }
    
    // If no validation errors, attempt registration
    if (empty($errors)) {
        $result = $auth->register($formData);
        
        if ($result['success']) {
            header('Location: /views/auth/login.php?registered=1');
            exit();
        } else {
            $error = $result['message'];
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Create Your Account
                    </h4>
                </div>
                <div class="card-body p-4">
                    <!-- Display error message -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Registration Form -->
                    <form method="POST" class="needs-validation" novalidate>
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo $auth->generateCSRFToken(); ?>">
                        
                        <!-- Personal Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>First Name *
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="first_name" 
                                    name="first_name" 
                                    value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>"
                                    required
                                >
                                <div class="invalid-feedback">Please enter your first name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="last_name" 
                                    name="last_name" 
                                    value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>"
                                    required
                                >
                                <div class="invalid-feedback">Please enter your last name.</div>
                            </div>
                        </div>
                        
                        <!-- Account Information -->
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-at me-1"></i>Username *
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="username" 
                                name="username" 
                                value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>"
                                pattern="[a-zA-Z0-9_]{3,}"
                                title="Username must be at least 3 characters and contain only letters, numbers, and underscores"
                                required
                            >
                            <div class="invalid-feedback">Username must be at least 3 characters long.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email Address *
                            </label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                                required
                            >
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        
                        <!-- Password Fields -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Password *
                                </label>
                                <div class="input-group">
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        id="password" 
                                        name="password" 
                                        minlength="6"
                                        required
                                    >
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Password must be at least 6 characters long.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password *</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    required
                                >
                                <div class="invalid-feedback">Please confirm your password.</div>
                            </div>
                        </div>
                        
                        <!-- Additional Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Phone Number
                                </label>
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="phone" 
                                    name="phone" 
                                    value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>"
                                >
                            </div>
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date of Birth
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control" 
                                    id="date_of_birth" 
                                    name="date_of_birth" 
                                    value="<?php echo htmlspecialchars($formData['date_of_birth'] ?? ''); ?>"
                                >
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gender" class="form-label">
                                <i class="fas fa-venus-mars me-1"></i>Gender
                            </label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="male" <?php echo ($formData['gender'] ?? 'male') === 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($formData['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        
                        <!-- Role Selection -->
                        <div class="mb-3">
                            <label for="role_id" class="form-label">
                                <i class="fas fa-user-tag me-1"></i>Register as *
                            </label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">Select your role...</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>" 
                                            <?php echo ($selectedRole === $role['role_name'] || ($formData['role_id'] ?? 0) == $role['id']) ? 'selected' : ''; ?>
                                            data-role="<?php echo $role['role_name']; ?>">
                                        <?php echo ucwords(str_replace('_', ' ', $role['role_name'])); ?>
                                        <?php if ($role['description']): ?>
                                            - <?php echo htmlspecialchars($role['description']); ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select your role.</div>
                        </div>
                        
                        <!-- Madrassah Selection (conditional) -->
                        <div class="mb-3" id="madrassah-selection" style="display: none;">
                            <label for="madrassah_id" class="form-label">
                                <i class="fas fa-mosque me-1"></i>Select Madrassah *
                            </label>
                            <select class="form-select" id="madrassah_id" name="madrassah_id">
                                <option value="">Select a madrassah...</option>
                                <?php foreach ($madrassahs as $madrassah): ?>
                                    <option value="<?php echo $madrassah['id']; ?>" 
                                            <?php echo ($formData['madrassah_id'] ?? 0) == $madrassah['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($madrassah['name']); ?>
                                        <?php if ($madrassah['location']): ?>
                                            - <?php echo htmlspecialchars($madrassah['location']); ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a madrassah.</div>
                        </div>
                        
                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="terms" 
                                    name="terms" 
                                    required
                                    <?php echo ($formData['terms'] ?? false) ? 'checked' : ''; ?>
                                >
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> *
                                </label>
                                <div class="invalid-feedback">You must agree to the terms and conditions.</div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>
                                Create Account
                            </button>
                        </div>
                        
                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="mb-0">
                                Already have an account? 
                                <a href="/views/auth/login.php" class="text-decoration-none fw-bold">
                                    Login here
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Acceptance of Terms</h6>
                <p>By using the Madrassah Management System, you agree to these terms and conditions.</p>
                
                <h6>2. User Responsibilities</h6>
                <p>Users are responsible for maintaining the confidentiality of their account information and for all activities that occur under their account.</p>
                
                <h6>3. Data Privacy</h6>
                <p>We are committed to protecting your privacy and personal information in accordance with applicable data protection laws.</p>
                
                <h6>4. Educational Purpose</h6>
                <p>This system is designed to support Islamic education and should be used in accordance with Islamic principles and values.</p>
                
                <h6>5. Code of Conduct</h6>
                <p>All users must maintain respectful communication and conduct themselves in accordance with Islamic ethics.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="acceptTerms">Accept Terms</button>
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
    
    // Show/hide madrassah selection based on role
    function toggleMadrassahSelection() {
        const selectedRole = $('#role_id option:selected').data('role');
        const madrassahSection = $('#madrassah-selection');
        const madrassahSelect = $('#madrassah_id');
        
        if (['teacher', 'student', 'madrassah_admin'].includes(selectedRole)) {
            madrassahSection.show();
            madrassahSelect.prop('required', true);
        } else {
            madrassahSection.hide();
            madrassahSelect.prop('required', false).val('');
        }
    }
    
    $('#role_id').on('change', toggleMadrassahSelection);
    
    // Initialize on page load
    toggleMadrassahSelection();
    
    // Password confirmation validation
    $('#confirm_password').on('blur', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        
        if (password !== confirmPassword) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('Passwords do not match.');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });
    
    // Accept terms from modal
    $('#acceptTerms').on('click', function() {
        $('#terms').prop('checked', true);
        $('#termsModal').modal('hide');
    });
    
    // Form submission loading state
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Creating Account...'
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