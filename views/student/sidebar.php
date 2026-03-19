<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="text-center mb-4">
        <img src="../../assets/img/logo.png" alt="Logo" style="width: 80px; filter: brightness(0) invert(1);">
        <h5 class="mt-2">Student Portal</h5>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage == 'subjects.php' ? 'active' : ''; ?>" href="subjects.php">
                <i class="fas fa-book me-2"></i> My Subjects
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage == 'schedule.php' ? 'active' : ''; ?>" href="schedule.php">
                <i class="fas fa-calendar-alt me-2"></i> Class Schedule
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-user me-2"></i> Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../../api/auth/logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>
