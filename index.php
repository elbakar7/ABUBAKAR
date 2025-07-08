<?php
/**
 * Main Landing Page for Madrassah Management System
 * Shows welcome content and login/registration options for guests
 * Redirects logged-in users to their appropriate dashboard
 */

require_once 'includes/auth.php';

// Redirect logged-in users to their dashboard
if ($auth->isLoggedIn()) {
    $role = $_SESSION['role'];
    header("Location: /views/$role/dashboard.php");
    exit();
}

$pageTitle = 'Welcome - Madrassah Management System';
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <!-- Hero Section -->
    <section class="hero-section py-5 mb-5" style="background: linear-gradient(135deg, #2c5aa0 0%, #1e3d72 100%); color: white;">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="fas fa-mosque me-3"></i>
                        Madrassah Management System
                    </h1>
                    <p class="lead mb-4">
                        Empowering Islamic Education through modern technology. 
                        Manage students, teachers, classes, and track Qur'an memorization progress 
                        in one comprehensive platform.
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="/views/auth/login.php" class="btn btn-light btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Login
                        </a>
                        <a href="/views/auth/register.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>
                            Register
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <div class="text-center">
                        <i class="fas fa-users display-1 mb-3" style="opacity: 0.8;"></i>
                        <h4>Multi-Madrassah Platform</h4>
                        <p>Support for multiple Islamic schools and institutions</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-primary mb-3">Comprehensive Features</h2>
                    <p class="lead text-muted">Everything you need to manage your Islamic educational institution</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Student Management -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="widget-icon icon-primary mx-auto mb-3">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h5 class="card-title">Student Management</h5>
                            <p class="card-text">
                                Register and manage students, track their progress, 
                                attendance, and Qur'an memorization journey.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Teacher Management -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="widget-icon icon-success mx-auto mb-3">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h5 class="card-title">Teacher Management</h5>
                            <p class="card-text">
                                Assign teachers to classes, manage schedules, 
                                and facilitate communication with students and parents.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Class Scheduling -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="widget-icon icon-info mx-auto mb-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h5 class="card-title">Class Scheduling</h5>
                            <p class="card-text">
                                Create and manage class schedules, assign subjects, 
                                and organize academic calendars efficiently.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Qur'an Progress -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="widget-icon mx-auto mb-3" style="background: linear-gradient(135deg, #009639, #4caf50);">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h5 class="card-title">Qur'an Memorization</h5>
                            <p class="card-text">
                                Track student progress in Qur'an memorization, 
                                assess Tajweed, and generate achievement certificates.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Exam System -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="widget-icon icon-warning mx-auto mb-3">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h5 class="card-title">Exam Management</h5>
                            <p class="card-text">
                                Conduct exams, record results, generate report cards, 
                                and issue printable certificates for achievements.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Donation System -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="widget-icon mx-auto mb-3" style="background: linear-gradient(135deg, #ffd700, #ff9800);">
                                <i class="fas fa-hand-holding-heart"></i>
                            </div>
                            <h5 class="card-title">Sponsorship System</h5>
                            <p class="card-text">
                                Connect donors with madrassahs, enable student and 
                                teacher sponsorships, and manage donations transparently.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Role-based Access Section -->
    <section class="roles-section py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-primary mb-3">Role-Based Access</h2>
                    <p class="lead text-muted">Different interfaces for different user types</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Super Admin -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white text-center">
                            <i class="fas fa-user-shield fa-2x mb-2"></i>
                            <h5 class="mb-0">Super Admin</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Manage multiple madrassahs</li>
                                <li><i class="fas fa-check text-success me-2"></i>Assign madrassah admins</li>
                                <li><i class="fas fa-check text-success me-2"></i>System-wide analytics</li>
                                <li><i class="fas fa-check text-success me-2"></i>Global settings management</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Madrassah Admin -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white text-center">
                            <i class="fas fa-user-cog fa-2x mb-2"></i>
                            <h5 class="mb-0">Madrassah Admin</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Manage students & teachers</li>
                                <li><i class="fas fa-check text-success me-2"></i>Create class schedules</li>
                                <li><i class="fas fa-check text-success me-2"></i>Upload syllabus content</li>
                                <li><i class="fas fa-check text-success me-2"></i>Conduct exams</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Teacher -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white text-center">
                            <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                            <h5 class="mb-0">Teacher</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>View teaching schedule</li>
                                <li><i class="fas fa-check text-success me-2"></i>Mark attendance</li>
                                <li><i class="fas fa-check text-success me-2"></i>Input assessments</li>
                                <li><i class="fas fa-check text-success me-2"></i>Communicate with parents</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Student -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-white text-center">
                            <i class="fas fa-user-graduate fa-2x mb-2"></i>
                            <h5 class="mb-0">Student</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>View progress reports</li>
                                <li><i class="fas fa-check text-success me-2"></i>Check class schedule</li>
                                <li><i class="fas fa-check text-success me-2"></i>Access syllabus materials</li>
                                <li><i class="fas fa-check text-success me-2"></i>View certificates</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Parent -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-secondary">
                        <div class="card-header bg-secondary text-white text-center">
                            <i class="fas fa-user-friends fa-2x mb-2"></i>
                            <h5 class="mb-0">Parent</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Monitor child's progress</li>
                                <li><i class="fas fa-check text-success me-2"></i>View attendance records</li>
                                <li><i class="fas fa-check text-success me-2"></i>Receive notifications</li>
                                <li><i class="fas fa-check text-success me-2"></i>Communicate with teachers</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Donor -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white text-center">
                            <i class="fas fa-hand-holding-heart fa-2x mb-2"></i>
                            <h5 class="mb-0">Donor</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Browse madrassahs</li>
                                <li><i class="fas fa-check text-success me-2"></i>Sponsor students/teachers</li>
                                <li><i class="fas fa-check text-success me-2"></i>View impact reports</li>
                                <li><i class="fas fa-check text-success me-2"></i>Track donations</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Islamic Quote Section -->
    <section class="quote-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="quran-verse text-center">
                        <p class="fs-4 mb-3">
                            "وَقُل رَّبِّ زِدْنِي عِلْمًا"
                        </p>
                        <p class="fs-5 mb-3">
                            "And say: My Lord, increase me in knowledge."
                        </p>
                        <small class="text-muted">— Qur'an 20:114</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Getting Started Section -->
    <section class="getting-started py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Ready to Get Started?</h2>
            <p class="lead mb-4">
                Join thousands of Islamic institutions already using our platform to enhance their educational management.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="/views/auth/register.php?role=donor" class="btn btn-light btn-lg">
                    <i class="fas fa-hand-holding-heart me-2"></i>
                    Register as Donor
                </a>
                <a href="/views/auth/register.php?role=madrassah_admin" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-mosque me-2"></i>
                    Register Your Madrassah
                </a>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>