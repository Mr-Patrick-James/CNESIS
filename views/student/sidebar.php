<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<style>
    .sidebar {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .user-profile-section {
        padding: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
        background: rgba(0,0,0,0.2);
        margin-top: auto;
    }
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        background: var(--accent-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-weight: bold;
        font-size: 1.1rem;
    }
    .user-details {
        overflow: hidden;
    }
    .user-name {
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
    }
    .user-role {
        font-size: 0.75rem;
        opacity: 0.7;
        margin: 0;
    }
    .logout-btn {
        width: 100%;
        text-align: left;
        padding: 10px 15px;
        border-radius: 8px;
        color: #feb2b2 !important;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        transition: background 0.2s;
    }
    .logout-btn:hover {
        background: rgba(254, 178, 178, 0.1);
    }
    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8) !important;
        padding: 14px 20px !important;
        border-radius: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        transition: all 0.2s !important;
        border-left: 3px solid transparent !important;
    }
    .sidebar .nav-link i {
        width: 20px !important;
        text-align: center !important;
        font-size: 1.1rem !important;
        margin-right: 5px !important;
    }
    .sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-left-color: var(--accent-color) !important;
    }
    .sidebar .nav-link.active {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #fff !important;
        border-left-color: var(--accent-color) !important;
        font-weight: 600 !important;
    }
</style>
<div class="sidebar">
    <div>
        <div class="text-center mb-4">
            <img src="../../assets/img/logo.png" alt="Logo" style="width: 80px;">
            <h5 class="mt-2">Student Portal</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage == 'schedule.php' ? 'active' : ''; ?>" href="schedule.php">
                    <i class="fas fa-calendar-alt me-2"></i> Class Schedule
                </a>
            </li>
            <li class="nav-item">
            <a class="nav-link <?php echo $currentPage == 'classmates.php' ? 'active' : ''; ?>" href="classmates.php">
                <i class="fas fa-users me-2"></i> My Classmates
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage == 'inquiries.php' ? 'active' : ''; ?>" href="inquiries.php">
                <i class="fas fa-comments me-2"></i> Inquiry / Chat
            </a>
        </li>
            <li class="nav-item">
            <a class="nav-link" href="../../index.php">
                <i class="fas fa-globe me-2"></i> Go to Homepage
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-user me-2"></i> Profile
            </a>
        </li>
    </ul>
</div>

    <div class="user-profile-section">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo substr($_SESSION['full_name'] ?? 'S', 0, 1); ?>
            </div>
            <div class="user-details">
                <p class="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Student'); ?></p>
                <p class="user-role">Student</p>
            </div>
        </div>
        <a href="../../api/auth/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<!-- Mandatory Password Change Modal -->
<div class="modal fade" id="passwordChangeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="passwordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title" id="passwordChangeModalLabel"><i class="fas fa-lock me-2"></i>Security Update Required</h5>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4">You are currently using a default password. For your security, please update your password to continue using the portal.</p>
                <form id="passwordChangeForm">
                    <div class="mb-3">
                        <label for="newPassword" class="form-label small fw-bold text-dark">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                            <input type="password" class="form-control border-0 bg-light" id="newPassword" name="newPassword" required minlength="8" placeholder="At least 8 characters">
                            <button class="btn btn-outline-secondary border-0 bg-light" type="button" onclick="togglePasswordVisibility('newPassword')">
                                <i class="fas fa-eye" id="newPasswordEye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="confirmPassword" class="form-label small fw-bold text-dark">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                            <input type="password" class="form-control border-0 bg-light" id="confirmPassword" name="confirmPassword" required minlength="8" placeholder="Repeat new password">
                            <button class="btn btn-outline-secondary border-0 bg-light" type="button" onclick="togglePasswordVisibility('confirmPassword')">
                                <i class="fas fa-eye" id="confirmPasswordEye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm" id="submitPasswordBtn">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if password change is required
    const mustChange = <?php echo (isset($_SESSION['must_change_password']) && $_SESSION['must_change_password']) ? 'true' : 'false'; ?>;
    if (mustChange) {
        // Wait a bit for bootstrap to be ready if needed
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined') {
                const passwordModal = new bootstrap.Modal(document.getElementById('passwordChangeModal'));
                passwordModal.show();
            } else if (typeof Bootstrap !== 'undefined') {
                const passwordModal = new Bootstrap.Modal(document.getElementById('passwordChangeModal'));
                passwordModal.show();
            }
        }, 500);
    }

    // Password Change Form Handler
    const passwordForm = document.getElementById('passwordChangeForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const submitBtn = document.getElementById('submitPasswordBtn');

            if (newPassword !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }

            if (newPassword.length < 8) {
                alert('Password must be at least 8 characters long');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

            fetch('../../api/auth/update-password.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ new_password: newPassword, confirm_password: confirmPassword })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Password updated successfully. Please continue.');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Update Password';
                }
            })
            .catch(err => {
                console.error('Update error:', err);
                alert('A system error occurred');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Update Password';
            });
        });
    }
});

function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    const icon = document.getElementById(id + 'Eye');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
