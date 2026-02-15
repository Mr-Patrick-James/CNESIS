<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports & Analytics - Admin Panel</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    :root {
      --primary-blue: #1a365d;
      --secondary-blue: #2c5282;
      --accent-gold: #d4af37;
      --sidebar-width: 260px;
      --topbar-height: 60px;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
      overflow-x: hidden;
    }
    
    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: var(--sidebar-width);
      background: linear-gradient(180deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
      color: white;
      transition: all 0.3s ease;
      z-index: 1000;
      overflow-y: auto;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    
    .sidebar.collapsed {
      width: 70px;
    }
    
    .sidebar-header {
      padding: 20px;
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .sidebar-header h4 {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 5px;
      transition: opacity 0.3s;
    }
    
    .sidebar-header small {
      font-size: 0.75rem;
      opacity: 0.8;
      transition: opacity 0.3s;
    }
    
    .sidebar-menu {
      padding: 20px 0;
    }
    
    .menu-item {
      padding: 12px 20px;
      color: rgba(255,255,255,0.8);
      text-decoration: none;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      cursor: pointer;
      border-left: 3px solid transparent;
    }
    
    .menu-item:hover {
      background-color: rgba(255,255,255,0.1);
      color: white;
      border-left-color: var(--accent-gold);
    }
    
    .menu-item.active {
      background-color: rgba(255,255,255,0.15);
      color: white;
      border-left-color: var(--accent-gold);
    }
    
    .menu-item i {
      width: 25px;
      font-size: 1.1rem;
      margin-right: 15px;
    }
    
    /* Topbar */
    .topbar {
      position: fixed;
      top: 0;
      left: var(--sidebar-width);
      right: 0;
      height: var(--topbar-height);
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 30px;
      z-index: 999;
      transition: left 0.3s ease;
    }
    
    .sidebar.collapsed ~ .topbar {
      left: 70px;
    }
    
    .topbar-left {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .toggle-btn {
      background: none;
      border: none;
      font-size: 1.3rem;
      color: var(--primary-blue);
      cursor: pointer;
      transition: transform 0.3s;
    }
    
    .toggle-btn:hover {
      transform: scale(1.1);
    }
    
    .topbar-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .admin-profile {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
    }
    
    .admin-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--accent-gold);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
    }
    
    /* Main Content */
    .main-content {
      margin-left: var(--sidebar-width);
      margin-top: var(--topbar-height);
      padding: 30px;
      transition: margin-left 0.3s ease;
      min-height: calc(100vh - var(--topbar-height));
    }
    
    .sidebar.collapsed ~ .main-content {
      margin-left: 70px;
    }
    
    .page-header {
      margin-bottom: 30px;
    }
    
    .page-header h2 {
      color: var(--primary-blue);
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .breadcrumb {
      background: none;
      padding: 0;
      margin: 0;
    }
    
    /* Content Card */
    .content-card {
      background: white;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      margin-bottom: 20px;
    }
    
    .content-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f0f0f0;
    }
    
    .content-card-header h5 {
      color: var(--primary-blue);
      font-weight: 600;
      margin: 0;
    }
    
    /* Report Cards */
    .report-card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      margin-bottom: 20px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .report-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .report-card h6 {
      color: var(--primary-blue);
      font-weight: 600;
      margin-bottom: 10px;
    }
    
    .report-card p {
      color: #6c757d;
      margin-bottom: 15px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
      }
      
      .sidebar-header h4,
      .sidebar-header small,
      .menu-item span {
        display: none;
      }
      
      .topbar {
        left: 70px;
      }
      
      .main-content {
        margin-left: 70px;
        padding: 15px;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <i class="fas fa-graduation-cap" style="font-size: 2rem; color: var(--accent-gold);"></i>
      <h4>COLEGIO DE NAUJAN</h4>
      <small>Admin Portal</small>
    </div>
    
    <div class="sidebar-menu">
      <a class="menu-item" href="dashboard.php">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
      </a>
      <a class="menu-item" href="students.php">
        <i class="fas fa-user-graduate"></i>
        <span>Students</span>
      </a>
      <a class="menu-item" href="program-heads.php">
        <i class="fas fa-chalkboard-teacher"></i>
        <span>Program Heads</span>
      </a>
      <a class="menu-item" data-bs-toggle="collapse" href="#admissionsSubmenu" role="button" aria-expanded="false" aria-controls="admissionsSubmenu">
        <i class="fas fa-file-alt"></i>
        <span>Admissions</span>
        <i class="fas fa-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
      </a>
      <div class="collapse" id="admissionsSubmenu">
        <a class="menu-item ps-5" href="admissions.php">
          <i class="fas fa-list" style="font-size: 0.9rem;"></i>
          <span style="font-size: 0.9rem;">All Admissions</span>
        </a>
        <a class="menu-item ps-5" href="admissions.php?status=pending">
          <i class="fas fa-clock" style="font-size: 0.9rem;"></i>
          <span style="font-size: 0.9rem;">Pending</span>
        </a>
        <a class="menu-item ps-5" href="admissions.php?status=approved">
          <i class="fas fa-check-circle" style="font-size: 0.9rem;"></i>
          <span style="font-size: 0.9rem;">Approved</span>
        </a>
      </div>
      <a class="menu-item" href="programs.php">
        <i class="fas fa-book"></i>
        <span>Programs</span>
      </a>
      <a class="menu-item active" href="reports.php">
        <i class="fas fa-chart-bar"></i>
        <span>Reports</span>
      </a>
      <a class="menu-item" href="settings.php">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
      <a class="menu-item" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>
  
  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
      </button>
      <h5 style="margin: 0; color: var(--primary-blue);">Reports & Analytics</h5>
    </div>
    
    <div class="topbar-right">
      <div class="admin-profile">
        <div class="admin-avatar">AD</div>
        <div>
          <div style="font-weight: 600; font-size: 0.9rem;">Admin User</div>
          <div style="font-size: 0.75rem; color: #6c757d;">Administrator</div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <div class="main-content">
    <div class="page-header">
      <h2>Reports & Analytics</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Reports</li>
        </ol>
      </nav>
    </div>
    
    <div class="content-card">
      <div class="content-card-header">
        <h5>Generate Reports</h5>
        <button class="btn btn-secondary btn-sm" onclick="refreshAllReports()">
          <i class="fas fa-sync-alt me-1"></i> Refresh All
        </button>
      </div>
      
      <div class="row">
        
        <div class="col-md-6 mb-3">
          <div class="report-card">
            <h6><i class="fas fa-file-alt"></i> Admission Statistics</h6>
            <p>View admission trends, acceptance rates, and application statistics.</p>
            <div class="dropdown">
              <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="admissionReportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                Generate Report
              </button>
              <ul class="dropdown-menu" aria-labelledby="admissionReportDropdown">
                <li><button class="dropdown-item" type="button" onclick="generateReport('admission-statistics', '')">All Statuses</button></li>
                <li><button class="dropdown-item" type="button" onclick="generateReport('admission-statistics', 'pending')">Pending Only</button></li>
                <li><button class="dropdown-item" type="button" onclick="generateReport('admission-statistics', 'approved')">Approved Only</button></li>
              </ul>
            </div>
            <div id="admission-statistics-loading" class="mt-2" style="display: none;">
              <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <span class="ms-2">Generating report...</span>
            </div>
          </div>
        </div>
        
        
        
        <div class="col-md-6 mb-3">
          <div class="report-card">
            <h6><i class="fas fa-download"></i> Prospectus Download Report</h6>
            <p>View statistics on prospectus downloads by program and date range.</p>
            <button class="btn btn-primary btn-sm" onclick="generateReport('prospectus-downloads')">Generate Report</button>
            <div id="prospectus-downloads-loading" class="mt-2" style="display: none;">
              <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <span class="ms-2">Generating report...</span>
            </div>
          </div>
        </div>
        
      </div>
    </div>
    
    <!-- Report Display Section -->
    <div class="content-card" id="reportDisplay" style="display: none;">
      <div class="content-card-header">
        <h5 id="reportTitle">Report Results</h5>
        <div>
          <button class="btn btn-success btn-sm" onclick="exportReport()">
            <i class="fas fa-download me-1"></i> Export
          </button>
          <button class="btn btn-secondary btn-sm ms-2" onclick="closeReport()">
            <i class="fas fa-times me-1"></i> Close
          </button>
        </div>
      </div>
      <div id="reportContent">
        <!-- Report content will be displayed here -->
      </div>
    </div>
    
    <!-- Quick Stats Summary -->
    <div class="content-card">
      <div class="content-card-header">
        <h5>System Overview</h5>
        <button class="btn btn-primary btn-sm" onclick="loadSummaryStats()">
          <i class="fas fa-sync-alt me-1"></i> Refresh
        </button>
      </div>
      <div id="summaryStats">
        <div class="row">
          <div class="col-md-12 text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading system statistics...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    let currentReportData = null;
    let currentReportType = null;
    
    // Load Summary Statistics
    function loadSummaryStats() {
      fetch('../../../api/reports/generate-report.php')
        .then(response => response.json())
        .then(data => {
          if (data.success && data.report_type === 'summary') {
            displaySummaryStats(data.statistics);
          } else {
            console.error('Error loading summary stats:', data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
    
    // Display Summary Statistics
    function displaySummaryStats(stats) {
      const summaryHtml = `
        <div class="row g-3">
          <div class="col-md-3">
            <div class="text-center">
              <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
              </div>
              <div class="stat-number">${stats.students.toLocaleString()}</div>
              <div class="stat-label">Total Students</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <div class="stat-icon">
                <i class="fas fa-book"></i>
              </div>
              <div class="stat-number">${stats.programs.toLocaleString()}</div>
              <div class="stat-label">Active Programs</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <div class="stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
              </div>
              <div class="stat-number">${stats.program_heads.toLocaleString()}</div>
              <div class="stat-label">Program Heads</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
              </div>
              <div class="stat-number">${stats.admissions.toLocaleString()}</div>
              <div class="stat-label">Admissions</div>
            </div>
          </div>
        </div>
      `;
      document.getElementById('summaryStats').innerHTML = summaryHtml;
    }
    
    // Generate Report
    function generateReport(reportType, statusFilter = null) {
      console.log('Generating report:', reportType, statusFilter);
      
      // Show loading indicator
      const loadingDiv = document.getElementById(`${reportType}-loading`);
      if (loadingDiv) {
        loadingDiv.style.display = 'block';
      }
      
      let url = `../../../api/reports/generate-report.php?type=${reportType}`;
      
      // Add status filter if provided or found in DOM
      if (statusFilter !== null) {
        url += `&status=${statusFilter}`;
      } else if (reportType === 'admission-statistics') {
        const statusEl = document.getElementById('admission-status-filter');
        if (statusEl && statusEl.value) {
          url += `&status=${statusEl.value}`;
        }
      }
      
      fetch(url)
        .then(response => {
          console.log('Response status:', response.status);
          return response.json();
        })
        .then(data => {
          console.log('Report data:', data);
          
          if (loadingDiv) {
            loadingDiv.style.display = 'none';
          }
          
          if (data.success) {
            currentReportData = data;
            currentReportType = reportType;
            displayReport(data);
          } else {
            alert('Error generating report: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          if (loadingDiv) {
            loadingDiv.style.display = 'none';
          }
          alert('Error generating report. Please try again.');
        });
    }
    
    // Display Report
    function displayReport(data) {
      const reportDisplay = document.getElementById('reportDisplay');
      const reportTitle = document.getElementById('reportTitle');
      const reportContent = document.getElementById('reportContent');
      
      // Set title
      const titles = {
        'admission-statistics': 'Admission Statistics Report',
        'prospectus-downloads': 'Prospectus Downloads Report',
      };
      
      reportTitle.textContent = titles[data.report_type] || 'Report';
      
      // Generate content based on report type
      let contentHtml = '';
      
      switch (data.report_type) {
        case 'admission-statistics':
          contentHtml = generateAdmissionStatisticsContent(data);
          break;
        case 'prospectus-downloads':
          contentHtml = generateProspectusDownloadsContent(data);
          break;
        default:
          contentHtml = '<p>Report data not available.</p>';
      }
      
      reportContent.innerHTML = contentHtml;
      reportDisplay.style.display = 'block';
      
      // Scroll to report
      reportDisplay.scrollIntoView({ behavior: 'smooth' });
    }
    
    
    // Generate Admission Statistics Content
    function generateAdmissionStatisticsContent(data) {
      let html = `
        <div class="mb-4">
          <h6>Summary Statistics</h6>
          <div class="row">
            <div class="col-md-3">
              <div class="p-3 bg-light rounded">
                <div class="h5">${data.summary.total_applications.toLocaleString()}</div>
                <div class="text-muted">Total Applications</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="p-3 bg-light rounded">
                <div class="h5">${data.summary.approved.toLocaleString()}</div>
                <div class="text-muted">Approved</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="p-3 bg-light rounded">
                <div class="h5">${data.summary.pending.toLocaleString()}</div>
                <div class="text-muted">Pending</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="p-3 bg-light rounded">
                <div class="h5">${data.summary.enrolled.toLocaleString()}</div>
                <div class="text-muted">Enrolled</div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Month</th>
                <th>Admission Type</th>
                <th>Status</th>
                <th>Count</th>
              </tr>
            </thead>
            <tbody>
      `;
      
      data.details.forEach(item => {
        html += `
          <tr>
            <td>${item.month}</td>
            <td>${item.admission_type}</td>
            <td><span class="badge bg-${getStatusColor(item.status)}">${item.status}</span></td>
            <td>${item.count.toLocaleString()}</td>
          </tr>
        `;
      });
      
      html += `
            </tbody>
          </table>
        </div>
      `;
      
      return html;
    }
    
    
    
    // Generate Prospectus Downloads Content
    function generateProspectusDownloadsContent(data) {
      let html = `
        <div class="mb-4">
          <h6>Summary Statistics</h6>
          <div class="row">
            <div class="col-md-6">
              <div class="p-3 bg-light rounded">
                <div class="h5">${data.summary.total_downloads.toLocaleString()}</div>
                <div class="text-muted">Total Downloads</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded">
                <div class="h5">${data.summary.programs_with_downloads.toLocaleString()}</div>
                <div class="text-muted">Programs with Downloads</div>
              </div>
            </div>
          </div>
        </div>
        
        <h6>Downloads by Program</h6>
        <div class="table-responsive mb-4">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Program</th>
                <th>Downloads</th>
                <th>Last Download</th>
              </tr>
            </thead>
            <tbody>
      `;
      
      data.by_program.forEach(item => {
        html += `
          <tr>
            <td>${item.program_title}</td>
            <td>${item.download_count.toLocaleString()}</td>
            <td>${item.last_download ? new Date(item.last_download).toLocaleDateString() : 'Never'}</td>
          </tr>
        `;
      });
      
      html += `
            </tbody>
          </table>
        </div>
        
        <h6>Monthly Trends</h6>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Month</th>
                <th>Downloads</th>
              </tr>
            </thead>
            <tbody>
      `;
      
      data.monthly_trends.forEach(item => {
        html += `
          <tr>
            <td>${item.month}</td>
            <td>${item.downloads.toLocaleString()}</td>
          </tr>
        `;
      });
      
      html += `
            </tbody>
          </table>
        </div>
      `;
      
      return html;
    }
    
    
    // Export Report
    function exportReport() {
      if (!currentReportData) {
        alert('No report data to export');
        return;
      }
      
      // Create CSV content
      let csvContent = '';
      
      switch (currentReportData.report_type) {
        case 'prospectus-downloads':
          csvContent = 'Program,Downloads,Last Download\n';
          currentReportData.by_program.forEach(item => {
            csvContent += `"${item.program_title}",${item.download_count},"${item.last_download}"\n`;
          });
          break;
        case 'admission-statistics':
          csvContent = 'Status,Type,Count,Month\n';
          currentReportData.details.forEach(item => {
            csvContent += `${item.status},${item.admission_type},${item.count},${item.month}\n`;
          });
          break;
        // Add other report types as needed
        default:
          csvContent = JSON.stringify(currentReportData, null, 2);
      }
      
      // Create download link
      const blob = new Blob([csvContent], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `${currentReportType}_report_${new Date().toISOString().split('T')[0]}.csv`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
    }
    
    // Close Report
    function closeReport() {
      document.getElementById('reportDisplay').style.display = 'none';
      currentReportData = null;
      currentReportType = null;
    }
    
    // Refresh All Reports
    function refreshAllReports() {
      loadSummaryStats();
      // You could also refresh other reports if needed
    }
    
    // Helper Functions
    
    function getStatusColor(status) {
      const colors = {
        'pending': 'warning',
        'approved': 'success',
        'rejected': 'danger',
        'enrolled': 'primary',
        'active': 'success',
        'inactive': 'secondary'
      };
      return colors[status] || 'secondary';
    }
    
    // Toggle Sidebar
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('collapsed');
    }
    
    // Logout Function
    function logout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '/CNESIS/index.php';
      }
    }
    
    // Auto-collapse sidebar on mobile
    if (window.innerWidth <= 768) {
      document.getElementById('sidebar').classList.add('collapsed');
    }
    
    // Load summary stats when page loads
    document.addEventListener('DOMContentLoaded', loadSummaryStats);
  </script>
</body>
</html>
