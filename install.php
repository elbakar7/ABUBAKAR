<?php
/**
 * Installation Script for Madrassah Management System
 * Creates database, tables, and sample data
 */

// Check if already installed
if (file_exists('config/.installed')) {
    die('System is already installed. Delete config/.installed file to reinstall.');
}

$pageTitle = 'Install - Madrassah Management System';
$errors = [];
$success = '';

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'madrassah_management'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbConfig['host'] = $_POST['db_host'] ?? 'localhost';
    $dbConfig['username'] = $_POST['db_username'] ?? 'root';
    $dbConfig['password'] = $_POST['db_password'] ?? '';
    $dbConfig['database'] = $_POST['db_name'] ?? 'madrassah_management';
    
    try {
        // Connect to MySQL server
        $pdo = new PDO("mysql:host={$dbConfig['host']}", $dbConfig['username'], $dbConfig['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Connect to the new database
        $pdo = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['database']}", $dbConfig['username'], $dbConfig['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Read and execute SQL file
        $sql = file_get_contents('database/madrassah_db.sql');
        $pdo->exec($sql);
        
        // Create demo data
        createDemoData($pdo);
        
        // Update database configuration file
        updateDatabaseConfig($dbConfig);
        
        // Mark as installed
        file_put_contents('config/.installed', date('Y-m-d H:i:s'));
        
        $success = 'Installation completed successfully! You can now login with the demo accounts.';
        
    } catch (PDOException $e) {
        $errors[] = 'Database Error: ' . $e->getMessage();
    } catch (Exception $e) {
        $errors[] = 'Error: ' . $e->getMessage();
    }
}

function createDemoData($pdo) {
    // Create demo madrassahs
    $madrassahs = [
        ['Al-Noor Islamic Center', 'New York, USA', '123 Islamic Way, Brooklyn, NY 11201', '+1-555-0101', 'info@alnoor.org'],
        ['Dar Al-Uloom Academy', 'London, UK', '456 Masjid Road, London E1 4NS', '+44-20-7946-0958', 'admin@daruloom.ac.uk'],
        ['Islamic Education Foundation', 'Toronto, Canada', '789 Peace Street, Toronto, ON M5V 3A8', '+1-416-555-0123', 'contact@ief.ca']
    ];
    
    foreach ($madrassahs as $madrassah) {
        $stmt = $pdo->prepare("INSERT INTO madrassahs (name, location, address, phone, email, established_date) VALUES (?, ?, ?, ?, ?, CURDATE())");
        $stmt->execute($madrassah);
    }
    
    // Create demo super admin
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name, role_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'superadmin',
        'superadmin@madrassah.com',
        password_hash('admin123', PASSWORD_DEFAULT),
        'Super',
        'Admin',
        1, // super_admin role
        'active'
    ]);
    
    // Create demo madrassah admin
    $stmt->execute([
        'admin',
        'admin@alnoor.org',
        password_hash('admin123', PASSWORD_DEFAULT),
        'Ahmed',
        'Hassan',
        2, // madrassah_admin role
        'active'
    ]);
    $adminId = $pdo->lastInsertId();
    
    // Assign admin to first madrassah
    $pdo->prepare("UPDATE users SET madrassah_id = 1 WHERE id = ?")->execute([$adminId]);
    
    // Create demo teacher
    $stmt->execute([
        'teacher',
        'teacher@alnoor.org',
        password_hash('teacher123', PASSWORD_DEFAULT),
        'Fatima',
        'Ali',
        3, // teacher role
        'active'
    ]);
    $teacherId = $pdo->lastInsertId();
    $pdo->prepare("UPDATE users SET madrassah_id = 1 WHERE id = ?")->execute([$teacherId]);
    
    // Create demo student
    $stmt->execute([
        'student',
        'student@example.com',
        password_hash('student123', PASSWORD_DEFAULT),
        'Omar',
        'Abdullah',
        4, // student role
        'active'
    ]);
    $studentId = $pdo->lastInsertId();
    $pdo->prepare("UPDATE users SET madrassah_id = 1 WHERE id = ?")->execute([$studentId]);
    
    // Create demo parent
    $stmt->execute([
        'parent',
        'parent@example.com',
        password_hash('parent123', PASSWORD_DEFAULT),
        'Muhammad',
        'Abdullah',
        5, // parent role
        'active'
    ]);
    $parentId = $pdo->lastInsertId();
    
    // Link parent to student
    $pdo->prepare("INSERT INTO parent_student_relations (parent_id, student_id, relationship) VALUES (?, ?, 'father')")->execute([$parentId, $studentId]);
    
    // Create demo donor
    $stmt->execute([
        'donor',
        'donor@example.com',
        password_hash('donor123', PASSWORD_DEFAULT),
        'Aisha',
        'Rahman',
        6, // donor role
        'active'
    ]);
    
    // Create demo classes and subjects
    $pdo->prepare("INSERT INTO classes (madrassah_id, class_name, level, description) VALUES (1, 'Beginner Quran', 1, 'Basic Quran reading and memorization')")->execute();
    $pdo->prepare("INSERT INTO classes (madrassah_id, class_name, level, description) VALUES (1, 'Intermediate Quran', 2, 'Advanced Quran memorization and Tajweed')")->execute();
    
    $pdo->prepare("INSERT INTO subjects (madrassah_id, subject_name, description) VALUES (1, 'Quran Memorization', 'Memorization of Quranic verses')")->execute();
    $pdo->prepare("INSERT INTO subjects (madrassah_id, subject_name, description) VALUES (1, 'Islamic Studies', 'Basic Islamic knowledge and practices')")->execute();
    $pdo->prepare("INSERT INTO subjects (madrassah_id, subject_name, description) VALUES (1, 'Arabic Language', 'Arabic reading and writing')")->execute();
}

function updateDatabaseConfig($config) {
    $configContent = "<?php
/**
 * Database Configuration for Madrassah Management System
 * Auto-generated during installation
 */

// Database configuration
define('DB_HOST', '{$config['host']}');
define('DB_NAME', '{$config['database']}');
define('DB_USER', '{$config['username']}');
define('DB_PASS', '{$config['password']}');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private \$connection;
    private static \$instance;
    
    private function __construct() {
        try {
            \$dsn = \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=\" . DB_CHARSET;
            \$this->connection = new PDO(\$dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException \$e) {
            die(\"Database connection failed: \" . \$e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (!self::\$instance) {
            self::\$instance = new Database();
        }
        return self::\$instance;
    }
    
    public function getConnection() {
        return \$this->connection;
    }
    
    public function execute(\$query, \$params = []) {
        try {
            \$stmt = \$this->connection->prepare(\$query);
            \$stmt->execute(\$params);
            return \$stmt;
        } catch (PDOException \$e) {
            error_log(\"Database error: \" . \$e->getMessage());
            return false;
        }
    }
    
    public function fetchSingle(\$query, \$params = []) {
        \$stmt = \$this->execute(\$query, \$params);
        return \$stmt ? \$stmt->fetch() : false;
    }
    
    public function fetchAll(\$query, \$params = []) {
        \$stmt = \$this->execute(\$query, \$params);
        return \$stmt ? \$stmt->fetchAll() : false;
    }
    
    public function lastInsertId() {
        return \$this->connection->lastInsertId();
    }
}

// Initialize database connection
\$db = Database::getInstance();
?>";
    
    file_put_contents('config/database.php', $configContent);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .install-card { box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card install-card border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">
                            <i class="fas fa-mosque me-2"></i>
                            Madrassah Management System
                        </h3>
                        <p class="mb-0">Installation Setup</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo $success; ?>
                            </div>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Demo Login Accounts:</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Super Admin:</strong><br>
                                            Username: <code>superadmin</code><br>
                                            Password: <code>admin123</code>
                                        </div>
                                        <div class="col-6">
                                            <strong>Teacher:</strong><br>
                                            Username: <code>teacher</code><br>
                                            Password: <code>teacher123</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="/" class="btn btn-primary btn-lg">
                                    <i class="fas fa-home me-2"></i>
                                    Go to Homepage
                                </a>
                            </div>
                            
                        <?php else: ?>
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Welcome!</strong> This installer will set up your Madrassah Management System database and create demo accounts for testing.
                            </div>
                            
                            <form method="POST">
                                <h5 class="mb-3">Database Configuration</h5>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="db_host" class="form-label">Database Host</label>
                                        <input type="text" class="form-control" id="db_host" name="db_host" 
                                               value="<?php echo htmlspecialchars($dbConfig['host']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="db_name" class="form-label">Database Name</label>
                                        <input type="text" class="form-control" id="db_name" name="db_name" 
                                               value="<?php echo htmlspecialchars($dbConfig['database']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="db_username" class="form-label">Database Username</label>
                                        <input type="text" class="form-control" id="db_username" name="db_username" 
                                               value="<?php echo htmlspecialchars($dbConfig['username']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="db_password" class="form-label">Database Password</label>
                                        <input type="password" class="form-control" id="db_password" name="db_password" 
                                               value="<?php echo htmlspecialchars($dbConfig['password']); ?>">
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <small>
                                        <strong>Note:</strong> This will create the database and install sample data including demo user accounts.
                                        Make sure your MySQL server is running and the provided credentials have sufficient privileges.
                                    </small>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-download me-2"></i>
                                        Install System
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>