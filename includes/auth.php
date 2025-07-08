<?php
/**
 * Authentication and Security Functions
 * Handles user login, session management, and security
 */

session_start();
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Hash password securely
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Login user
     */
    public function login($username, $password) {
        $query = "SELECT u.*, r.role_name, m.name as madrassah_name 
                  FROM users u 
                  LEFT JOIN user_roles r ON u.role_id = r.id 
                  LEFT JOIN madrassahs m ON u.madrassah_id = m.id 
                  WHERE (u.username = ? OR u.email = ?) AND u.status = 'active'";
        
        $user = $this->db->fetchSingle($query, [$username, $username]);
        
        if ($user && $this->verifyPassword($password, $user['password'])) {
            // Update last login
            $this->db->execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
            
            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['madrassah_id'] = $user['madrassah_id'];
            $_SESSION['madrassah_name'] = $user['madrassah_name'];
            $_SESSION['last_activity'] = time();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        header('Location: /views/auth/login.php');
        exit();
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['last_activity']);
    }
    
    /**
     * Check session timeout (30 minutes)
     */
    public function checkSessionTimeout() {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            $this->logout();
        }
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole($roles) {
        return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
    }
    
    /**
     * Require login
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /views/auth/login.php');
            exit();
        }
        $this->checkSessionTimeout();
    }
    
    /**
     * Require specific role
     */
    public function requireRole($role) {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            header('Location: /views/shared/unauthorized.php');
            exit();
        }
    }
    
    /**
     * Register new user
     */
    public function register($userData) {
        // Check if username or email already exists
        $checkQuery = "SELECT id FROM users WHERE username = ? OR email = ?";
        $existing = $this->db->fetchSingle($checkQuery, [$userData['username'], $userData['email']]);
        
        if ($existing) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        // Hash password
        $userData['password'] = $this->hashPassword($userData['password']);
        
        // Insert user
        $query = "INSERT INTO users (username, email, password, first_name, last_name, phone, 
                  date_of_birth, gender, role_id, madrassah_id, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $userData['username'],
            $userData['email'],
            $userData['password'],
            $userData['first_name'],
            $userData['last_name'],
            $userData['phone'] ?? null,
            $userData['date_of_birth'] ?? null,
            $userData['gender'] ?? 'male',
            $userData['role_id'],
            $userData['madrassah_id'] ?? null,
            $userData['status'] ?? 'active'
        ];
        
        if ($this->db->execute($query, $params)) {
            return ['success' => true, 'message' => 'User registered successfully', 'user_id' => $this->db->lastInsertId()];
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }
    
    /**
     * Get current user data
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $query = "SELECT u.*, r.role_name, m.name as madrassah_name 
                  FROM users u 
                  LEFT JOIN user_roles r ON u.role_id = r.id 
                  LEFT JOIN madrassahs m ON u.madrassah_id = m.id 
                  WHERE u.id = ?";
        
        return $this->db->fetchSingle($query, [$_SESSION['user_id']]);
    }
    
    /**
     * Generate CSRF token
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generate random password
 */
function generateRandomPassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Calculate age from date of birth
 */
function calculateAge($dateOfBirth) {
    return date_diff(date_create($dateOfBirth), date_create('today'))->y;
}

// Initialize Auth class
$auth = new Auth();
?>