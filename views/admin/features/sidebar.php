<?php
// Get current page name to set active class
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<style>
    /* Submenu Styles - Inline for portability */
    .submenu {
      display: none;
      background-color: rgba(0,0,0,0.1);
      padding-left: 0;
    }
    
    .submenu.show {
      display: block;
    }
    
    .submenu .menu-item {
      padding-left: 50px;
      font-size: 0.9rem;
    }
    
    .submenu .menu-item i {
      font-size: 0.8rem;
      width: 20px;
      margin-right: 15px;
    }

    .menu-item .fa-chevron-down {
      transition: transform 0.3s;
    }
    
    .menu-item.collapsed .fa-chevron-down {
      transform: rotate(-90deg);
    }
    
    .sidebar.collapsed .submenu {
      display: none !important;
    }
    
    .sidebar.collapsed .menu-item .fa-chevron-down {
        display: none;
    }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-graduation-cap" style="font-size: 2rem; color: var(--accent-gold);"></i>
        <h4>COLEGIO DE NAUJAN</h4>
        <small>Admin Portal</small>
    </div>
    
    <div class="sidebar-menu">
        <a href="dashboard.php" class="menu-item <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="students.php" class="menu-item <?php echo $currentPage == 'students.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-graduate"></i>
            <span>Students</span>
        </a>
        
        <?php if ($currentPage == 'admissions.php'): ?>
        <a class="menu-item active" href="admissions.php" id="admissionsMenuLink">
            <i class="fas fa-file-alt"></i>
            <span>Admissions</span>
            <i class="fas fa-chevron-down ms-auto" style="font-size: 0.8rem; margin-right: 0;"></i>
        </a>
        <div class="submenu show" id="admissionsSubmenu">
            <a class="menu-item" href="admissions.php?status=pending">
                <i class="fas fa-clock"></i>
                <span>Pending</span>
            </a>
            <a class="menu-item" href="admissions.php?status=scheduled">
                <i class="fas fa-calendar-check"></i>
                <span>Scheduled</span>
            </a>
            <a class="menu-item" href="admissions.php?status=approved">
                <i class="fas fa-check-circle"></i>
                <span>Approved</span>
            </a>
            <a class="menu-item" href="admissions.php?status=rejected">
                <i class="fas fa-times-circle"></i>
                <span>Rejected</span>
            </a>
            <a class="menu-item" href="admissions.php">
                <i class="fas fa-list"></i>
                <span>All</span>
            </a>
        </div>
        <?php else: ?>
        <a href="admissions.php" class="menu-item <?php echo $currentPage == 'admissions.php' ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i>
            <span>Admissions</span>
        </a>
        <?php endif; ?>

        <a href="exam-scheduling.php" class="menu-item <?php echo $currentPage == 'exam-scheduling.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i>
            <span>Exam Scheduling</span>
        </a>
        <a href="programs.php" class="menu-item <?php echo $currentPage == 'programs.php' ? 'active' : ''; ?>">
            <i class="fas fa-book"></i>
            <span>Programs</span>
        </a>
        <a href="reports.php" class="menu-item <?php echo $currentPage == 'reports.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        <a href="settings.php" class="menu-item <?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        <a href="#" class="menu-item" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>
