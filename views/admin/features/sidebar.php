<?php
// Get current page name to set active class
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<style>
    .sidebar {
      position: fixed;
      top: 0; left: 0; height: 100vh; width: var(--sidebar-width);
      background: linear-gradient(180deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
      color: white; z-index: 1000; overflow-y: auto;
      display: flex;
      flex-direction: column;
    }

    /* Improved Submenu Styles */
    .submenu {
      display: none;
      background-color: rgba(0, 0, 0, 0.15);
      padding: 5px 0;
      margin: 2px 0;
      border-radius: 4px;
      overflow: hidden;
      flex-direction: column !important;
    }
    
    .submenu.show {
      display: block;
      animation: fadeInSubmenu 0.3s ease-in-out;
    }

    @keyframes fadeInSubmenu {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .submenu .menu-item {
      padding: 12px 20px 12px 55px !important;
      font-size: 0.85rem !important;
      border-left: none !important;
      position: relative;
      color: rgba(255, 255, 255, 0.7);
      display: flex;
      align-items: center;
      text-decoration: none;
      transition: all 0.2s ease;
      gap: 10px !important;
    }
    
    .submenu .menu-item i {
      font-size: 0.8rem;
      width: 25px;
      margin-right: 15px;
      opacity: 0.7;
    }

    .submenu .menu-item:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    .submenu .menu-item.active {
      background-color: rgba(255, 255, 255, 0.2);
      color: var(--accent-gold);
      font-weight: 600;
    }

    .submenu .menu-item.active i {
      color: var(--accent-gold);
      opacity: 1;
    }

    /* Parent item adjustments */
    .menu-item .fa-chevron-down {
      font-size: 0.75rem;
      transition: transform 0.3s ease;
      opacity: 0.6;
    }
    
    .menu-item.active .fa-chevron-down {
      opacity: 1;
    }

    .menu-item:not(.collapsed) .fa-chevron-down {
      transform: rotate(0deg);
    }
    
    .menu-item.collapsed .fa-chevron-down {
      transform: rotate(-90deg);
    }
    
    /* Responsive/Collapsed Sidebar */
    .sidebar.collapsed .submenu {
      display: none !important;
    }
    
    .sidebar.collapsed .menu-item .fa-chevron-down {
      display: none;
    }

    /* User Profile Section */
    .user-profile-section {
      padding: 15px 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      background: rgba(0, 0, 0, 0.2);
      margin-top: auto;
    }
    .user-info {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }
    .user-avatar {
      width: 35px;
      height: 35px;
      background: var(--accent-gold);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-blue);
      font-weight: bold;
      font-size: 1rem;
    }
    .user-details {
      overflow: hidden;
    }
    .user-name {
      font-size: 0.85rem;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin: 0;
      color: #fff;
    }
    .user-role {
      font-size: 0.7rem;
      opacity: 0.7;
      margin: 0;
      color: var(--accent-gold);
    }
    .logout-btn {
      width: 100%;
      text-align: left;
      padding: 8px 12px;
      border-radius: 6px;
      color: #feb2b2 !important;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.85rem;
      transition: background 0.2s;
    }
    .logout-btn:hover {
      background: rgba(254, 178, 178, 0.1);
    }

    .sidebar-header {
      padding: 30px 15px;
      text-align: center;
      background: rgba(0, 0, 0, 0.2);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 10px;
    }

    .sidebar-header h4 {
      font-size: 1.1rem;
      font-weight: 700;
      margin: 10px 0 2px;
      letter-spacing: 0.5px;
      color: #fff;
    }

    .sidebar-header small {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      opacity: 0.6;
      color: var(--accent-gold);
      font-weight: 600;
    }
    .sidebar-menu {
      padding: 20px 0;
      display: flex;
      flex-direction: column;
    }

    .menu-item {
      padding: 14px 20px !important;
      color: rgba(255, 255, 255, 0.8) !important;
      text-decoration: none !important;
      display: flex !important;
      align-items: center !important;
      transition: all 0.3s ease !important;
      cursor: pointer !important;
      border-left: 3px solid transparent !important;
      width: 100% !important;
      box-sizing: border-box !important;
      background: none !important;
      gap: 12px !important;
    }

    .menu-item i:first-child {
      width: 20px !important;
      text-align: center !important;
      font-size: 1.1rem !important;
      margin-right: 5px !important;
    }

    .menu-item:hover {
      background-color: rgba(255, 255, 255, 0.1) !important;
      color: #fff !important;
      border-left-color: var(--accent-gold) !important;
    }

    .menu-item.active {
      background-color: rgba(255, 255, 255, 0.15) !important;
      color: #fff !important;
      border-left-color: var(--accent-gold) !important;
    }
    .sidebar-menu a {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none !important;
    }

    .sidebar-menu a:hover, .sidebar-menu a.active {
      color: #fff;
    }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="../../../assets/img/logo.png" alt="Logo" style="height: 60px; margin-bottom: 10px;">
        <h4>COLEGIO DE NAUJAN</h4>
        <small>Admin Portal</small>
    </div>
    
    <div class="sidebar-menu">
        <a href="dashboard.php" class="menu-item <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <?php 
        $studentPages = ['students.php'];
        $isStudentSection = in_array($currentPage, $studentPages);
        ?>
        <a class="menu-item <?php echo $isStudentSection ? 'active' : ''; ?> <?php echo !$isStudentSection ? 'collapsed' : ''; ?>" 
           href="javascript:void(0)" 
           id="studentsMenuLink" 
           onclick="toggleSubmenu('studentsSubmenu', this)">
            <i class="fas fa-user-graduate"></i>
            <span>Student List</span>
            <i class="fas fa-chevron-down ms-auto" style="font-size: 0.8rem; margin-right: 0;"></i>
        </a>
        <div class="submenu <?php echo $isStudentSection ? 'show' : ''; ?>" id="studentsSubmenu" style="display: <?php echo $isStudentSection ? 'flex' : 'none'; ?>; flex-direction: column;">
            <a class="menu-item <?php echo $currentPage == 'students.php' && ($_GET['type'] ?? '') == 'regular' ? 'active' : ''; ?>" href="students.php?type=regular">
                <i class="fas fa-user-check"></i>
                <span>Regular</span>
            </a>
            <a class="menu-item <?php echo $currentPage == 'students.php' && ($_GET['type'] ?? '') == 'irregular' ? 'active' : ''; ?>" href="students.php?type=irregular">
                <i class="fas fa-user-tag"></i>
                <span>Irregular</span>
            </a>
        </div>

        <a href="class-schedules.php" class="menu-item <?php echo $currentPage == 'class-schedules.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i>
            <span>Class Schedules</span>
        </a>
        
        <?php 
        $admissionPages = ['admissions.php'];
        $isAdmissionSection = in_array($currentPage, $admissionPages);
        ?>
        <a class="menu-item <?php echo $isAdmissionSection ? 'active' : ''; ?> <?php echo !$isAdmissionSection ? 'collapsed' : ''; ?>" 
           href="javascript:void(0)" 
           id="admissionsMenuLink" 
           onclick="toggleSubmenu('admissionsSubmenu', this)">
            <i class="fas fa-file-alt"></i>
            <span>Admissions</span>
            <i class="fas fa-chevron-down ms-auto" style="font-size: 0.8rem; margin-right: 0;"></i>
        </a>
        <div class="submenu <?php echo $isAdmissionSection ? 'show' : ''; ?>" id="admissionsSubmenu" style="display: <?php echo $isAdmissionSection ? 'flex' : 'none'; ?>; flex-direction: column;">
            <a class="menu-item <?php echo $currentPage == 'admissions.php' && ($_GET['status'] ?? '') == 'pending' ? 'active' : ''; ?>" href="admissions.php?status=pending">
                <i class="fas fa-clock"></i>
                <span>Pending</span>
            </a>
            <a class="menu-item <?php echo $currentPage == 'admissions.php' && ($_GET['status'] ?? '') == 'scheduled' ? 'active' : ''; ?>" href="admissions.php?status=scheduled">
                <i class="fas fa-calendar-alt"></i>
                <span>For Scheduling</span>
            </a>
            <a class="menu-item <?php echo $currentPage == 'admissions.php' && ($_GET['status'] ?? '') == 'examed' ? 'active' : ''; ?>" href="admissions.php?status=examed">
                <i class="fas fa-user-check"></i>
                <span>For Finalization</span>
            </a>
            <a class="menu-item <?php echo $currentPage == 'admissions.php' && ($_GET['status'] ?? '') == 'passed' ? 'active' : ''; ?>" href="admissions.php?status=passed">
                <i class="fas fa-check-double"></i>
                <span>Finalized</span>
            </a>
            <a class="menu-item <?php echo $currentPage == 'admissions.php' && ($_GET['status'] ?? '') == 'rejected' ? 'active' : ''; ?>" href="admissions.php?status=rejected">
                <i class="fas fa-times-circle"></i>
                <span>Rejected</span>
            </a>
        </div>

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
    </div>

    <div class="user-profile-section">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo substr($_SESSION['full_name'] ?? 'A', 0, 1); ?>
            </div>
            <div class="user-details">
                <p class="user-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin User'); ?></p>
                <p class="user-role">Administrator</p>
            </div>
        </div>
        <a href="../../../api/auth/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<script>
function toggleSubmenu(id, el) {
    const submenu = document.getElementById(id);
    const isShowing = submenu.classList.contains('show');
    
    // Close all other submenus first
    document.querySelectorAll('.submenu').forEach(s => {
        if (s.id !== id) s.classList.remove('show');
    });
    document.querySelectorAll('.menu-item').forEach(m => {
        if (m !== el) m.classList.add('collapsed');
    });

    if (isShowing) {
        submenu.style.display = 'none';
        submenu.classList.remove('show');
        el.classList.add('collapsed');
    } else {
        submenu.style.display = 'flex';
        submenu.classList.add('show');
        el.classList.remove('collapsed');
    }
}
</script>
