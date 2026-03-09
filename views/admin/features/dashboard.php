<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Colegio De Naujan</title>
  
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
    
    .sidebar.collapsed .sidebar-header h4,
    .sidebar.collapsed .sidebar-header small {
      opacity: 0;
      display: none;
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

    /* Inquiry Styles */
    .inquiry-list-item {
      padding: 15px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
      transition: background 0.2s;
    }
    .inquiry-list-item:hover {
      background-color: #f8f9fa;
    }
    .inquiry-list-item.unread {
      background-color: #fff8e1;
      border-left: 4px solid var(--accent-gold);
    }
    .chat-container {
      height: 400px;
      overflow-y: auto;
      padding: 20px;
      background: #f0f2f5;
      display: flex;
      flex-direction: column;
    }
    .message-bubble {
      max-width: 80%;
      margin-bottom: 15px;
      padding: 10px 15px;
      border-radius: 15px;
      font-size: 0.9rem;
      position: relative;
    }
    .message-student {
      align-self: flex-start;
      background: white;
      color: #333;
      border-bottom-left-radius: 2px;
    }
    .message-admin {
      align-self: flex-end;
      background: var(--secondary-blue);
      color: white;
      border-bottom-right-radius: 2px;
    }
    .message-time {
      font-size: 0.7rem;
      opacity: 0.7;
      margin-top: 5px;
      display: block;
    }
    .badge-notification {
      position: absolute;
      top: -5px;
      right: -5px;
      background-color: #dc3545;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 0.7rem;
      font-weight: bold;
      border: 2px solid white;
    }
    .topbar-icon {
      position: relative;
      cursor: pointer;
    }
    
    .menu-item i {
      width: 25px;
      font-size: 1.1rem;
      margin-right: 15px;
    }
    
    .sidebar.collapsed .menu-item span {
      display: none;
    }
    
    .sidebar.collapsed .menu-item {
      justify-content: center;
      padding: 12px 0;
    }
    
    .sidebar.collapsed .menu-item i {
      margin-right: 0;
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
    
    .topbar-icon {
      position: relative;
      font-size: 1.2rem;
      color: var(--primary-blue);
      cursor: pointer;
      transition: color 0.3s;
    }
    
    .topbar-icon:hover {
      color: var(--accent-gold);
    }
    
    .badge-notification {
      position: absolute;
      top: -5px;
      right: -5px;
      background: #dc3545;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 0.7rem;
      display: flex;
      align-items: center;
      justify-content: center;
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
    
    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background: white;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .stat-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }
    
    .stat-icon.blue {
      background: rgba(26, 54, 93, 0.1);
      color: var(--primary-blue);
    }
    
    .stat-icon.gold {
      background: rgba(212, 175, 55, 0.1);
      color: var(--accent-gold);
    }
    
    .stat-icon.green {
      background: rgba(40, 167, 69, 0.1);
      color: #28a745;
    }
    
    .stat-icon.red {
      background: rgba(220, 53, 69, 0.1);
      color: #dc3545;
    }
    
    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-blue);
      margin-bottom: 5px;
    }
    
    .stat-label {
      color: #6c757d;
      font-size: 0.9rem;
    }
    
    .stat-change {
      font-size: 0.85rem;
      margin-top: 10px;
    }
    
    .stat-change.positive {
      color: #28a745;
    }
    
    .stat-change.negative {
      color: #dc3545;
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
    
    /* Quick Actions */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 30px;
    }
    
    .quick-action-btn {
      background: white;
      border: 2px solid var(--primary-blue);
      color: var(--primary-blue);
      padding: 15px;
      border-radius: 10px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 600;
    }
    
    .quick-action-btn:hover {
      background: var(--primary-blue);
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(26, 54, 93, 0.3);
    }
    
    .quick-action-btn i {
      display: block;
      font-size: 2rem;
      margin-bottom: 10px;
    }
    
    /* Table Styles */
    .table-responsive {
      border-radius: 8px;
      overflow: hidden;
    }
    
    .custom-table {
      margin: 0;
    }
    
    .custom-table thead {
      background: var(--primary-blue);
      color: white;
    }
    
    .custom-table thead th {
      border: none;
      padding: 15px;
      font-weight: 600;
    }
    
    .custom-table tbody td {
      padding: 15px;
      vertical-align: middle;
    }
    
    .custom-table tbody tr {
      transition: background-color 0.3s;
    }
    
    .custom-table tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    /* Badge Styles */
    .badge-status {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }
    
    .badge-status.pending {
      background: #fff3cd;
      color: #856404;
    }
    
    .badge-status.approved {
      background: #d4edda;
      color: #155724;
    }
    
    .badge-status.rejected {
      background: #f8d7da;
      color: #721c24;
    }
    
    .badge-status.active {
      background: #d1ecf1;
      color: #0c5460;
    }
    
    .badge-status.scheduled {
      background: #cce5ff;
      color: #004085;
    }
    
    .badge-status.verified {
      background: #e2e3e5;
      color: #383d41;
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
      
      .stats-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>
  
  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
      </button>
      <h5 style="margin: 0; color: var(--primary-blue);">Admin Dashboard</h5>
    </div>
    
    <div class="topbar-right">
      <div class="topbar-icon">
        <i class="fas fa-bell"></i>
        <span class="badge-notification">5</span>
      </div>
      <div class="topbar-icon" onclick="openInquiriesModal()">
        <i class="fas fa-envelope"></i>
        <span class="badge-notification" id="inquiryBadge" style="display: none;">0</span>
      </div>
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
    <!-- Dashboard Section -->
    <div class="page-header">
      <h2>Dashboard Overview</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>
    
    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card" onclick="window.location.href='students.php';">
        <div class="stat-card-header">
          <div>
            <div class="stat-number" id="totalStudents">Loading...</div>
            <div class="stat-label">Total Students</div>
          </div>
          <div class="stat-icon blue">
            <i class="fas fa-user-graduate"></i>
          </div>
        </div>
        <div class="stat-change positive">
          <i class="fas fa-arrow-up"></i> <span id="studentsGrowth">Loading...</span>
        </div>
      </div>
      
      <div class="stat-card" onclick="window.location.href='programs.php';">
        <div class="stat-card-header">
          <div>
            <div class="stat-number" id="totalProgramHeads">Loading...</div>
            <div class="stat-label">Program Heads</div>
          </div>
          <div class="stat-icon gold">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
        </div>
        <div class="stat-change positive">
          <i class="fas fa-arrow-up"></i> <span id="programHeadsGrowth">Loading...</span>
        </div>
      </div>
      
      <div class="stat-card" onclick="window.location.href='admissions.php?status=pending';">
        <div class="stat-card-header">
          <div>
            <div class="stat-number" id="pendingAdmissions">Loading...</div>
            <div class="stat-label">Pending Admissions</div>
          </div>
          <div class="stat-icon red">
            <i class="fas fa-file-alt"></i>
          </div>
        </div>
        <div class="stat-change negative">
          <i class="fas fa-arrow-down"></i> <span id="admissionsTrend">Loading...</span>
        </div>
      </div>
      
      <div class="stat-card" onclick="window.location.href='programs.php';">
        <div class="stat-card-header">
          <div>
            <div class="stat-number" id="activePrograms">Loading...</div>
            <div class="stat-label">Active Programs</div>
          </div>
          <div class="stat-icon green">
            <i class="fas fa-book"></i>
          </div>
        </div>
        <div class="stat-change positive">
          <i class="fas fa-arrow-up"></i> <span id="programsGrowth">Loading...</span>
        </div>
      </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="content-card">
      <div class="content-card-header">
        <h5>Quick Actions</h5>
      </div>
      <div class="quick-actions">
        <div class="quick-action-btn" onclick="window.location.href='admissions.php?status=pending';">
          <i class="fas fa-user-plus"></i>
          <div>Review Pending</div>
        </div>
        <div class="quick-action-btn" onclick="window.location.href='students.php';">
          <i class="fas fa-users"></i>
          <div>Manage Students</div>
        </div>
        <div class="quick-action-btn" onclick="window.location.href='programs.php';">
          <i class="fas fa-user-tie"></i>
          <div>Manage Program Heads</div>
        </div>
        <div class="quick-action-btn" onclick="window.location.href='reports.php';">
          <i class="fas fa-file-pdf"></i>
          <div>Generate Report</div>
        </div>
      </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="content-card">
      <div class="content-card-header">
        <h5>Recent Admissions</h5>
      </div>
      <div class="table-responsive">
        <table class="table custom-table">
          <thead>
            <tr>
              <th>Applicant</th>
              <th>Program</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="recentAdmissionsTable">
            <tr>
              <td colspan="4" style="text-align: center; padding: 20px;">
                <i class="fas fa-spinner fa-spin"></i> Loading recent admissions...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Inquiries Modal -->
  <div class="modal fade" id="inquiriesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-envelope me-2"></i>Student Inquiries</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-0">
          <div class="row g-0">
            <!-- Inquiry List -->
            <div class="col-md-4 border-end" style="height: 500px; overflow-y: auto;" id="inquiryList">
              <div class="p-3 text-center text-muted">Loading inquiries...</div>
            </div>
            <!-- Chat View -->
            <div class="col-md-8 d-flex flex-column" style="height: 500px;">
              <div id="chatHeader" class="p-3 border-bottom d-none">
                <h6 class="mb-0" id="chatStudentName">Student Name</h6>
                <small class="text-muted" id="chatStudentEmail">student@email.com</small>
              </div>
              <div id="chatMessages" class="chat-container flex-grow-1">
                <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                  Select an inquiry to view conversation
                </div>
              </div>
              <div id="chatInputArea" class="p-3 border-top d-none">
                <div class="input-group">
                  <textarea id="replyMessage" class="form-control" rows="1" placeholder="Type your reply..."></textarea>
                  <button class="btn btn-primary" onclick="sendReply()"><i class="fas fa-paper-plane"></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <script>
    let inquiriesModal = null;
    let currentInquiryId = null;

    // Load Dashboard Data
    function loadDashboardData() {
      fetch('../../../api/dashboard/statistics.php?t=' + new Date().getTime())
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            updateStatistics(data.statistics);
            updateRecentAdmissions(data.recent_admissions);
          }
        })
        .catch(error => console.error('Error:', error));

      // Fetch Unread Inquiry Count
      fetch('../../../api/inquiries/get_unread_count.php?t=' + new Date().getTime())
        .then(res => res.json())
        .then(res => {
          if (res.success) {
            const badge = document.getElementById('inquiryBadge');
            if (res.count > 0) {
              badge.textContent = res.count;
              badge.style.display = 'block';
            } else {
              badge.style.display = 'none';
            }
          }
        })
        .catch(err => console.error(err));
    }

    function openInquiriesModal() {
      if (!inquiriesModal) {
        inquiriesModal = new bootstrap.Modal(document.getElementById('inquiriesModal'));
      }
      inquiriesModal.show();
      loadInquiries();
    }

    function loadInquiries() {
      const listContainer = document.getElementById('inquiryList');
      listContainer.innerHTML = '<div class="p-3 text-center text-muted">Loading inquiries...</div>';

      fetch('../../../api/inquiries/get_all_admin.php?t=' + new Date().getTime())
        .then(res => res.json())
        .then(res => {
          if (res.success) {
            if (res.inquiries.length === 0) {
              listContainer.innerHTML = '<div class="p-3 text-center text-muted">No inquiries found</div>';
              return;
            }

            listContainer.innerHTML = '';
            res.inquiries.forEach(inq => {
              const item = document.createElement('div');
              item.className = `inquiry-list-item ${inq.unread_count > 0 ? 'unread' : ''}`;
              item.onclick = () => selectInquiry(inq);
              
              const date = inq.last_activity ? new Date(inq.last_activity).toLocaleDateString() : '';
              
              item.innerHTML = `
                <div class="d-flex justify-content-between">
                  <span class="fw-bold text-truncate" style="max-width: 120px;">${inq.full_name}</span>
                  <small class="text-muted">${date}</small>
                </div>
                <div class="text-truncate small text-muted">${inq.latest_message || 'No messages'}</div>
                ${inq.unread_count > 0 ? `<span class="badge bg-danger rounded-pill float-end mt-1">${inq.unread_count}</span>` : ''}
              `;
              listContainer.appendChild(item);
            });
          }
        });
    }

    function selectInquiry(inquiry) {
      currentInquiryId = inquiry.id;
      document.getElementById('chatHeader').classList.remove('d-none');
      document.getElementById('chatInputArea').classList.remove('d-none');
      document.getElementById('chatStudentName').textContent = inquiry.full_name;
      document.getElementById('chatStudentEmail').textContent = inquiry.email;
      
      loadMessages(inquiry.id);
    }

    function loadMessages(inquiryId) {
      const chatMessages = document.getElementById('chatMessages');
      
      fetch(`../../../api/inquiries/get_messages.php?inquiry_id=${inquiryId}&t=${new Date().getTime()}`)
        .then(res => res.json())
        .then(res => {
          if (res.success) {
            chatMessages.innerHTML = '';
            res.messages.forEach(msg => {
              const bubble = document.createElement('div');
              bubble.className = `message-bubble ${msg.sender_type === 'student' ? 'message-student' : 'message-admin'}`;
              
              const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
              
              bubble.innerHTML = `
                <div>${msg.message}</div>
                <span class="message-time">${time}</span>
              `;
              chatMessages.appendChild(bubble);
            });
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Refresh unread count in background
            loadDashboardData();
          }
        });
    }

    function sendReply() {
      const messageInput = document.getElementById('replyMessage');
      const message = messageInput.value.trim();
      
      if (!message || !currentInquiryId) return;
      
      fetch('../../../api/inquiries/admin_reply.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          inquiry_id: currentInquiryId,
          message: message
        })
      })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          messageInput.value = '';
          loadMessages(currentInquiryId);
          loadInquiries();
        } else {
          Swal.fire('Error', res.message, 'error');
        }
      });
    }

    // Auto-expand textarea
    document.getElementById('replyMessage')?.addEventListener('input', function() {
      this.style.height = 'auto';
      this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Update Statistics
    function updateStatistics(stats) {
      document.getElementById('totalStudents').textContent = stats.total_students.toLocaleString();
      document.getElementById('totalProgramHeads').textContent = stats.total_program_heads.toLocaleString();
      document.getElementById('pendingAdmissions').textContent = stats.pending_admissions.toLocaleString();
      document.getElementById('activePrograms').textContent = stats.active_programs.toLocaleString();
      
      // Update growth indicators (you can calculate real growth later)
      document.getElementById('studentsGrowth').textContent = '12% from last month';
      document.getElementById('programHeadsGrowth').textContent = '5% from last month';
      document.getElementById('admissionsTrend').textContent = '8% from last week';
      document.getElementById('programsGrowth').textContent = '2 new programs';
    }
    
    // Update Recent Admissions Table
    function updateRecentAdmissions(admissions) {
      const tbody = document.getElementById('recentAdmissionsTable');
      
      if (admissions.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="4" style="text-align: center; padding: 20px;">
              No recent admissions found
            </td>
          </tr>
        `;
        return;
      }
      
      tbody.innerHTML = '';
      admissions.forEach(admission => {
        const row = document.createElement('tr');
        const statusBadge = getStatusBadge(admission.status);
        const formattedDate = new Date(admission.date).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
        
        row.innerHTML = `
          <td>
            <div style="font-weight: 600;">${admission.name}</div>
            <div style="font-size: 0.85rem; color: #6c757d;">${admission.email}</div>
          </td>
          <td>${admission.program}</td>
          <td>${formattedDate}</td>
          <td>${statusBadge}</td>
        `;
        
        tbody.appendChild(row);
      });
    }
    
    // Get Status Badge HTML
    function getStatusBadge(status) {
      const badges = {
        'pending': '<span class="badge-status pending">Pending</span>',
        'approved': '<span class="badge bg-primary text-white">For Scheduling</span>',
        'rejected': '<span class="badge-status rejected">Rejected</span>',
        'active': '<span class="badge-status active">Active</span>',
        'inactive': '<span class="badge-status rejected">Inactive</span>',
        'verified': '<span class="badge-status verified">Verified</span>',
        'scheduled': '<span class="badge bg-primary text-white">For Scheduling</span>',
        'examed': '<span class="badge bg-success">For Finalization</span>'
      };
      return badges[status] || `<span class="badge-status pending">${status}</span>`;
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
    
    // Load dashboard data when page loads
    document.addEventListener('DOMContentLoaded', loadDashboardData);
    
    // Auto-refresh dashboard data every 30 seconds
    setInterval(loadDashboardData, 30000);
  </script>
</body>
</html>
