/**
 * Main JavaScript for Madrassah Management System
 * Handles common functionality, AJAX requests, and UI interactions
 */

// Global variables
let loadingOverlay;

// Document ready
$(document).ready(function() {
    initializeApp();
});

/**
 * Initialize application
 */
function initializeApp() {
    createLoadingOverlay();
    initializeTooltips();
    initializePopovers();
    initializeDataTables();
    initializeFormValidation();
    initializeFileUploads();
    initializeSearchFunctionality();
    bindCommonEvents();
    
    // Add fade-in animation to page content
    $('main.main-content').addClass('fade-in');
}

/**
 * Create loading overlay
 */
function createLoadingOverlay() {
    if (!$('.loading-overlay').length) {
        loadingOverlay = $(`
            <div class="loading-overlay">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-3">
                        <strong>Processing...</strong>
                    </div>
                </div>
            </div>
        `);
        $('body').append(loadingOverlay);
    } else {
        loadingOverlay = $('.loading-overlay');
    }
}

/**
 * Show loading overlay
 */
function showLoading(message = 'Processing...') {
    loadingOverlay.find('strong').text(message);
    loadingOverlay.fadeIn(200);
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    loadingOverlay.fadeOut(200);
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Initialize Bootstrap popovers
 */
function initializePopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

/**
 * Initialize DataTables
 */
function initializeDataTables() {
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records...",
                lengthMenu: "_MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ records",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>'
                }
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        });
    }
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
    // Custom validation styles
    $('form.needs-validation').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            // Focus on first invalid field
            const firstInvalid = $(this).find(':invalid').first();
            if (firstInvalid.length) {
                firstInvalid.focus();
                
                // Scroll to the field if needed
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 100
                }, 300);
            }
        }
        $(this).addClass('was-validated');
    });
    
    // Real-time validation feedback
    $('input, select, textarea').on('blur', function() {
        if ($(this).closest('form').hasClass('was-validated')) {
            if (this.checkValidity()) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        }
    });
}

/**
 * Initialize file upload functionality
 */
function initializeFileUploads() {
    $('.file-upload').on('change', function() {
        const file = this.files[0];
        const $input = $(this);
        const $preview = $input.siblings('.file-preview');
        const $error = $input.siblings('.file-error');
        
        // Clear previous states
        $preview.empty();
        $error.empty();
        $input.removeClass('is-invalid is-valid');
        
        if (file) {
            // Validate file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                $error.text('File size must be less than 10MB');
                $input.addClass('is-invalid');
                return;
            }
            
            // Validate file type
            const allowedTypes = $input.data('allowed-types');
            if (allowedTypes) {
                const fileExtension = file.name.split('.').pop().toLowerCase();
                if (!allowedTypes.split(',').includes(fileExtension)) {
                    $error.text(`Only ${allowedTypes} files are allowed`);
                    $input.addClass('is-invalid');
                    return;
                }
            }
            
            // Show file preview
            $input.addClass('is-valid');
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $preview.html(`<img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">`);
                };
                reader.readAsDataURL(file);
            } else {
                $preview.html(`
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file me-2"></i>
                        <span>${file.name}</span>
                        <small class="text-muted ms-2">(${formatFileSize(file.size)})</small>
                    </div>
                `);
            }
        }
    });
}

/**
 * Initialize search functionality
 */
function initializeSearchFunctionality() {
    $('.search-input').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        const targetSelector = $(this).data('target');
        
        $(targetSelector).each(function() {
            const text = $(this).text().toLowerCase();
            if (text.includes(searchTerm)) {
                $(this).show().addClass('slide-in-right');
            } else {
                $(this).hide().removeClass('slide-in-right');
            }
        });
    });
}

/**
 * Bind common events
 */
function bindCommonEvents() {
    // Confirm delete actions
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href') || $(this).data('url');
        const itemName = $(this).data('item') || 'this item';
        
        showConfirmDialog(
            'Confirm Delete',
            `Are you sure you want to delete ${itemName}? This action cannot be undone.`,
            'danger',
            function() {
                if (url) {
                    window.location.href = url;
                }
            }
        );
    });
    
    // Auto-hide alerts
    $('.alert.auto-hide').delay(5000).fadeOut(500);
    
    // Back to top button
    if (!$('.back-to-top').length) {
        $('body').append('<button class="btn btn-primary back-to-top" style="display: none;"><i class="fas fa-arrow-up"></i></button>');
        
        $('.back-to-top').css({
            position: 'fixed',
            bottom: '20px',
            right: '20px',
            borderRadius: '50%',
            width: '50px',
            height: '50px',
            zIndex: 1000
        });
    }
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn();
        } else {
            $('.back-to-top').fadeOut();
        }
    });
    
    $('.back-to-top').on('click', function() {
        $('html, body').animate({scrollTop: 0}, 600);
        return false;
    });
}

/**
 * Show success message
 */
function showSuccessMessage(message, autoHide = true) {
    showAlert(message, 'success', autoHide);
}

/**
 * Show error message
 */
function showErrorMessage(message, autoHide = true) {
    showAlert(message, 'danger', autoHide);
}

/**
 * Show warning message
 */
function showWarningMessage(message, autoHide = true) {
    showAlert(message, 'warning', autoHide);
}

/**
 * Show info message
 */
function showInfoMessage(message, autoHide = true) {
    showAlert(message, 'info', autoHide);
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info', autoHide = true) {
    const alertId = 'alert-' + Date.now();
    const alert = $(`
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${getAlertIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    // Insert at the top of main content
    $('main.main-content').prepend(alert);
    
    // Auto-hide after 5 seconds
    if (autoHide) {
        setTimeout(() => {
            $(`#${alertId}`).fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }
}

/**
 * Get alert icon based on type
 */
function getAlertIcon(type) {
    const icons = {
        success: 'check-circle',
        danger: 'exclamation-triangle',
        warning: 'exclamation-circle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Show confirmation dialog
 */
function showConfirmDialog(title, message, type = 'primary', onConfirm = null, onCancel = null) {
    const modalId = 'confirmModal-' + Date.now();
    const modal = $(`
        <div class="modal fade" id="${modalId}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-${getAlertIcon(type)} me-2"></i>
                            ${title}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${message}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-${type}" id="confirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    `);
    
    $('body').append(modal);
    
    const modalInstance = new bootstrap.Modal(document.getElementById(modalId));
    modalInstance.show();
    
    // Handle confirm
    modal.find('#confirmBtn').on('click', function() {
        modalInstance.hide();
        if (onConfirm) onConfirm();
    });
    
    // Handle cancel
    modal.on('hidden.bs.modal', function() {
        if (onCancel) onCancel();
        modal.remove();
    });
}

/**
 * AJAX helper function
 */
function ajaxRequest(url, method = 'GET', data = null, options = {}) {
    const defaults = {
        dataType: 'json',
        beforeSend: function() {
            if (options.showLoading !== false) {
                showLoading(options.loadingMessage);
            }
        },
        complete: function() {
            if (options.showLoading !== false) {
                hideLoading();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showErrorMessage('An error occurred. Please try again.');
        }
    };
    
    const settings = $.extend({}, defaults, options, {
        url: url,
        method: method,
        data: data
    });
    
    return $.ajax(settings);
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

/**
 * Format date
 */
function formatDate(dateString, format = 'MMM dd, yyyy') {
    const date = new Date(dateString);
    const options = {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    };
    
    if (format.includes('time')) {
        options.hour = '2-digit';
        options.minute = '2-digit';
    }
    
    return date.toLocaleDateString('en-US', options);
}

/**
 * Debounce function
 */
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction() {
        const context = this;
        const args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

/**
 * Progress tracking functions
 */
const ProgressTracker = {
    /**
     * Update progress bar
     */
    updateProgressBar: function(selector, percentage, animated = true) {
        const $progressBar = $(selector);
        if (animated) {
            $progressBar.animate({
                width: percentage + '%'
            }, 800);
        } else {
            $progressBar.css('width', percentage + '%');
        }
        $progressBar.attr('aria-valuenow', percentage);
        $progressBar.find('.progress-text').text(percentage + '%');
    },
    
    /**
     * Create circular progress
     */
    createCircularProgress: function(selector, percentage, options = {}) {
        const defaults = {
            size: 120,
            strokeWidth: 8,
            color: '#2c5aa0',
            backgroundColor: '#e9ecef'
        };
        const settings = $.extend({}, defaults, options);
        
        // Implementation would go here for circular progress
        // This is a placeholder for the actual SVG implementation
    }
};

/**
 * Quran progress tracking
 */
const QuranTracker = {
    /**
     * Update memorization progress
     */
    updateMemorizationProgress: function(studentId, surahNumber, verses, status) {
        const data = {
            student_id: studentId,
            surah_number: surahNumber,
            verses: verses,
            status: status
        };
        
        return ajaxRequest('/controllers/update_quran_progress.php', 'POST', data);
    },
    
    /**
     * Get surah information
     */
    getSurahInfo: function(surahNumber) {
        const surahs = {
            1: { name: 'Al-Fatiha', verses: 7 },
            2: { name: 'Al-Baqara', verses: 286 },
            3: { name: 'Al-Imran', verses: 200 },
            // ... Add all 114 surahs
        };
        
        return surahs[surahNumber] || { name: 'Unknown', verses: 0 };
    }
};

/**
 * Certificate generator
 */
const CertificateGenerator = {
    /**
     * Generate certificate
     */
    generate: function(studentId, type, title, description) {
        const data = {
            student_id: studentId,
            type: type,
            title: title,
            description: description
        };
        
        return ajaxRequest('/controllers/generate_certificate.php', 'POST', data, {
            loadingMessage: 'Generating certificate...'
        });
    },
    
    /**
     * Print certificate
     */
    print: function(certificateId) {
        window.open(`/views/shared/print_certificate.php?id=${certificateId}`, '_blank');
    }
};

/**
 * Attendance functions
 */
const AttendanceManager = {
    /**
     * Mark attendance
     */
    markAttendance: function(studentId, classId, status, notes = '') {
        const data = {
            student_id: studentId,
            class_id: classId,
            status: status,
            notes: notes,
            attendance_date: new Date().toISOString().split('T')[0]
        };
        
        return ajaxRequest('/controllers/mark_attendance.php', 'POST', data);
    },
    
    /**
     * Bulk mark attendance
     */
    bulkMarkAttendance: function(attendanceData) {
        return ajaxRequest('/controllers/bulk_attendance.php', 'POST', { 
            attendance: attendanceData 
        }, {
            loadingMessage: 'Saving attendance...'
        });
    }
};

/**
 * Notification system
 */
const NotificationSystem = {
    /**
     * Send message
     */
    sendMessage: function(recipientId, subject, message, type = 'general') {
        const data = {
            recipient_id: recipientId,
            subject: subject,
            message: message,
            message_type: type
        };
        
        return ajaxRequest('/controllers/send_message.php', 'POST', data);
    },
    
    /**
     * Mark message as read
     */
    markAsRead: function(messageId) {
        return ajaxRequest('/controllers/mark_message_read.php', 'POST', { 
            message_id: messageId 
        });
    },
    
    /**
     * Get unread count
     */
    getUnreadCount: function() {
        return ajaxRequest('/controllers/get_unread_count.php');
    }
};

// Export for use in other files
window.MadrassahSystem = {
    showLoading,
    hideLoading,
    showSuccessMessage,
    showErrorMessage,
    showWarningMessage,
    showInfoMessage,
    showConfirmDialog,
    ajaxRequest,
    formatFileSize,
    formatDate,
    ProgressTracker,
    QuranTracker,
    CertificateGenerator,
    AttendanceManager,
    NotificationSystem
};