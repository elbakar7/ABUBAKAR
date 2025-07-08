<?php
require_once __DIR__ . '/auth.php';

// Get current user if logged in
$currentUser = $auth->getCurrentUser();
$pageTitle = $pageTitle ?? 'Madrassah Management System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="fas fa-mosque me-2"></i>
                Madrassah Management System
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if ($auth->isLoggedIn()): ?>
                    <ul class="navbar-nav me-auto">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link" href="/views/<?php echo $_SESSION['role']; ?>/dashboard.php">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        
                        <?php if ($_SESSION['role'] === 'super_admin'): ?>
                            <!-- Super Admin Menu -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cogs me-1"></i>Management
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/views/super_admin/madrassahs.php">
                                        <i class="fas fa-mosque me-1"></i>Madrassahs
                                    </a></li>
                                    <li><a class="dropdown-item" href="/views/super_admin/admins.php">
                                        <i class="fas fa-user-shield me-1"></i>Madrassah Admins
                                    </a></li>
                                    <li><a class="dropdown-item" href="/views/super_admin/analytics.php">
                                        <i class="fas fa-chart-bar me-1"></i>Analytics
                                    </a></li>
                                </ul>
                            </li>
                            
                        <?php elseif ($_SESSION['role'] === 'madrassah_admin'): ?>
                            <!-- Madrassah Admin Menu -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-users me-1"></i>Students
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/views/madrassah_admin/students.php">
                                        <i class="fas fa-user-graduate me-1"></i>All Students
                                    </a></li>
                                    <li><a class="dropdown-item" href="/views/madrassah_admin/student_progress.php">
                                        <i class="fas fa-chart-line me-1"></i>Progress Tracking
                                    </a></li>
                                    <li><a class="dropdown-item" href="/views/madrassah_admin/attendance.php">
                                        <i class="fas fa-clipboard-check me-1"></i>Attendance
                                    </a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>Teachers
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/views/madrassah_admin/teachers.php">
                                        <i class="fas fa-users me-1"></i>All Teachers
                                    </a></li>
                                    <li><a class="dropdown-item" href="/views/madrassah_admin/schedules.php">
                                        <i class="fas fa-calendar-alt me-1"></i>Schedules
                                    </a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-book me-1"></i>Academic
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/views/madrassah_admin/classes.php">
                                        <i class="fas fa-school me-1"></i>Classes
                                    </a></li>
                                    <li><a class="dropdown-item" href="/views/madrassah_admin/subjects.php">
                                        <i class="fas fa-book-open me-1"></i>Subjects
                                    </a></li>
                                    <li><a class="dropdown-item" href="/views/madrassah_admin/syllabus.php">
                                        <i class="fas fa-file-pdf me-1"></i>Syllabus
                                    </a></li>
                                    <li><a class="dropdown-item" href="/views/madrassah_admin/exams.php">
                                        <i class="fas fa-file-alt me-1"></i>Exams
                                    </a></li>
                                </ul>
                            </li>
                            
                        <?php elseif ($_SESSION['role'] === 'teacher'): ?>
                            <!-- Teacher Menu -->
                            <li class="nav-item">
                                <a class="nav-link" href="/views/teacher/schedule.php">
                                    <i class="fas fa-calendar me-1"></i>My Schedule
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/views/teacher/students.php">
                                    <i class="fas fa-users me-1"></i>My Students
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/views/teacher/assessments.php">
                                    <i class="fas fa-clipboard-check me-1"></i>Assessments
                                </a>
                            </li>
                            
                        <?php elseif ($_SESSION['role'] === 'student'): ?>
                            <!-- Student Menu -->
                            <li class="nav-item">
                                <a class="nav-link" href="/views/student/progress.php">
                                    <i class="fas fa-chart-line me-1"></i>My Progress
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/views/student/schedule.php">
                                    <i class="fas fa-calendar me-1"></i>Class Schedule
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/views/student/syllabus.php">
                                    <i class="fas fa-book me-1"></i>Syllabus
                                </a>
                            </li>
                            
                        <?php elseif ($_SESSION['role'] === 'parent'): ?>
                            <!-- Parent Menu -->
                            <li class="nav-item">
                                <a class="nav-link" href="/views/parent/children.php">
                                    <i class="fas fa-child me-1"></i>My Children
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/views/parent/progress.php">
                                    <i class="fas fa-chart-line me-1"></i>Progress Reports
                                </a>
                            </li>
                            
                        <?php elseif ($_SESSION['role'] === 'donor'): ?>
                            <!-- Donor Menu -->
                            <li class="nav-item">
                                <a class="nav-link" href="/views/donor/madrassahs.php">
                                    <i class="fas fa-mosque me-1"></i>Madrassahs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/views/donor/sponsorships.php">
                                    <i class="fas fa-hand-holding-heart me-1"></i>My Sponsorships
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Common menu items -->
                        <li class="nav-item">
                            <a class="nav-link" href="/views/shared/messages.php">
                                <i class="fas fa-envelope me-1"></i>Messages
                            </a>
                        </li>
                    </ul>
                    
                    <!-- User dropdown -->
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/views/shared/profile.php">
                                    <i class="fas fa-user me-1"></i>Profile
                                </a></li>
                                <li><a class="dropdown-item" href="/views/shared/settings.php">
                                    <i class="fas fa-cog me-1"></i>Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/controllers/logout.php">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <!-- Guest menu -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/views/auth/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/views/auth/register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
         <!-- Main content wrapper -->
     <main class="main-content"><?php
         // Show current user info for development
         if ($auth->isLoggedIn() && isset($_GET['debug'])):
         ?>
         <div class="container-fluid mt-2">
             <div class="alert alert-info alert-dismissible fade show" role="alert">
                 <strong>Debug:</strong> Logged in as <?php echo $_SESSION['full_name']; ?> 
                 (<?php echo $_SESSION['role']; ?>) 
                 <?php if ($_SESSION['madrassah_name']): ?>
                     at <?php echo $_SESSION['madrassah_name']; ?>
                 <?php endif; ?>
                 <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
             </div>
         </div>
     <?php endif; ?>