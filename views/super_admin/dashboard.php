<?php
/**
 * Super Admin Dashboard
 * Shows system-wide statistics and management options
 */

require_once '../../includes/auth.php';

// Require super admin role
$auth->requireRole('super_admin');

$pageTitle = 'Super Admin Dashboard - Madrassah Management System';

// Get system statistics
$stats = [];

// Total madrassahs
$stats['madrassahs'] = $db->fetchSingle("SELECT COUNT(*) as count FROM madrassahs WHERE status = 'active'")['count'] ?? 0;

// Total users by role
$userStats = $db->fetchAll("
    SELECT r.role_name, COUNT(u.id) as count 
    FROM user_roles r 
    LEFT JOIN users u ON r.id = u.role_id AND u.status = 'active'
    GROUP BY r.id, r.role_name
    ORDER BY r.id
");

// Total students across all madrassahs
$stats['students'] = 0;
$stats['teachers'] = 0;
$stats['admins'] = 0;
$stats['parents'] = 0;
$stats['donors'] = 0;

foreach ($userStats as $userStat) {
    switch ($userStat['role_name']) {
        case 'student':
            $stats['students'] = $userStat['count'];
            break;
        case 'teacher':
            $stats['teachers'] = $userStat['count'];
            break;
        case 'madrassah_admin':
            $stats['admins'] = $userStat['count'];
            break;
        case 'parent':
            $stats['parents'] = $userStat['count'];
            break;
        case 'donor':
            $stats['donors'] = $userStat['count'];
            break;
    }
}

// Recent activities (last 10)
$recentActivities = $db->fetchAll("
    SELECT 'user_registration' as type, CONCAT(first_name, ' ', last_name) as description, 
           created_at, 'info' as badge_class
    FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY created_at DESC 
    LIMIT 10
");

// Top madrassahs by student count
$topMadrassahs = $db->fetchAll("
    SELECT m.name, m.location, COUNT(u.id) as student_count,
           m.created_at
    FROM madrassahs m
    LEFT JOIN users u ON m.id = u.madrassah_id AND u.role_id = (
        SELECT id FROM user_roles WHERE role_name = 'student'
    )
    WHERE m.status = 'active'
    GROUP BY m.id
    ORDER BY student_count DESC, m.name
    LIMIT 5
");

// Monthly registration trends (last 6 months)
$monthlyTrends = $db->fetchAll("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as registrations
    FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
");

?>

<?php include '../../includes/header.php'; ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                        Super Admin Dashboard
                    </h1>
                    <p class="text-muted mb-0">System-wide overview and management</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMadrassahModal">
                        <i class="fas fa-plus me-2"></i>Add Madrassah
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stats-icon">
                    <i class="fas fa-mosque"></i>
                </div>
                <h3><?php echo number_format($stats['madrassahs']); ?></h3>
                <p>Active Madrassahs</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stats-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3><?php echo number_format($stats['students']); ?></h3>
                <p>Total Students</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stats-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3><?php echo number_format($stats['teachers']); ?></h3>
                <p>Total Teachers</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="stats-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3><?php echo number_format($stats['donors']); ?></h3>
                <p>Active Donors</p>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Monthly Trends Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Registration Trends (Last 6 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="100"></canvas>
                </div>
            </div>

            <!-- Top Madrassahs -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Top Madrassahs by Student Count
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Madrassah Name</th>
                                    <th>Location</th>
                                    <th>Students</th>
                                    <th>Established</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topMadrassahs as $index => $madrassah): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">#<?php echo $index + 1; ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($madrassah['name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($madrassah['location'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-success">
                                            <?php echo number_format($madrassah['student_count']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDate($madrassah['created_at']); ?></td>
                                    <td>
                                        <a href="/views/super_admin/madrassah_details.php?id=<?php echo $madrassah['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($topMadrassahs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No madrassahs found. Add your first madrassah to get started.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- User Distribution -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        User Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="userDistributionChart" height="200"></canvas>
                    
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-circle text-primary me-2"></i>Students</span>
                            <span class="fw-bold"><?php echo number_format($stats['students']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-circle text-success me-2"></i>Teachers</span>
                            <span class="fw-bold"><?php echo number_format($stats['teachers']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-circle text-warning me-2"></i>Parents</span>
                            <span class="fw-bold"><?php echo number_format($stats['parents']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-circle text-danger me-2"></i>Donors</span>
                            <span class="fw-bold"><?php echo number_format($stats['donors']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Recent Activities
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentActivities)): ?>
                        <div class="timeline">
                            <?php foreach ($recentActivities as $activity): ?>
                            <div class="timeline-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-<?php echo $activity['badge_class']; ?> rounded-pill">
                                            <i class="fas fa-user-plus"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1">
                                            <strong><?php echo htmlspecialchars($activity['description']); ?></strong>
                                            registered
                                        </p>
                                        <small class="text-muted">
                                            <?php echo formatDate($activity['created_at'], 'M d, Y H:i'); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            No recent activities
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="/views/super_admin/madrassahs.php" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-mosque fa-2x mb-2"></i>
                                <span>Manage Madrassahs</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/views/super_admin/admins.php" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-user-shield fa-2x mb-2"></i>
                                <span>Manage Admins</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/views/super_admin/analytics.php" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>View Analytics</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="/views/super_admin/settings.php" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-cog fa-2x mb-2"></i>
                                <span>System Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Madrassah Modal -->
<div class="modal fade" id="addMadrassahModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-mosque me-2"></i>
                    Add New Madrassah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMadrassahForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="madrassah_name" class="form-label">Madrassah Name *</label>
                            <input type="text" class="form-control" id="madrassah_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="madrassah_location" class="form-label">Location *</label>
                            <input type="text" class="form-control" id="madrassah_location" name="location" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="madrassah_address" class="form-label">Full Address</label>
                            <textarea class="form-control" id="madrassah_address" name="address" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="madrassah_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="madrassah_phone" name="phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="madrassah_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="madrassah_email" name="email">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="madrassah_description" class="form-label">Description</label>
                            <textarea class="form-control" id="madrassah_description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Add Madrassah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Page-specific JavaScript -->
<script>
$(document).ready(function() {
    // Initialize charts
    initializeTrendsChart();
    initializeUserDistributionChart();
    
    // Handle add madrassah form
    $('#addMadrassahForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        MadrassahSystem.showLoading('Adding madrassah...');
        
        // Simulate API call (replace with actual AJAX call)
        setTimeout(() => {
            MadrassahSystem.hideLoading();
            MadrassahSystem.showSuccessMessage('Madrassah added successfully!');
            $('#addMadrassahModal').modal('hide');
            this.reset();
            
            // Refresh page or update data
            setTimeout(() => {
                location.reload();
            }, 1500);
        }, 2000);
    });
});

function initializeTrendsChart() {
    const ctx = document.getElementById('trendsChart').getContext('2d');
    const monthlyData = <?php echo json_encode($monthlyTrends); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => {
                const date = new Date(item.month + '-01');
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }).reverse(),
            datasets: [{
                label: 'New Registrations',
                data: monthlyData.map(item => item.registrations).reverse(),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function initializeUserDistributionChart() {
    const ctx = document.getElementById('userDistributionChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Students', 'Teachers', 'Parents', 'Donors'],
            datasets: [{
                data: [
                    <?php echo $stats['students']; ?>,
                    <?php echo $stats['teachers']; ?>,
                    <?php echo $stats['parents']; ?>,
                    <?php echo $stats['donors']; ?>
                ],
                backgroundColor: [
                    '#2c5aa0',
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>

<?php include '../../includes/footer.php'; ?>