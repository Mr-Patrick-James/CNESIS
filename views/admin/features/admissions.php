<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admissions Management - Admin Panel</title>
  
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
    
    /* Action Buttons */
    .action-btn {
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s;
      font-size: 0.85rem;
      margin: 0 2px;
    }
    
    .action-btn.view {
      background: #17a2b8;
      color: white;
    }
    
    .action-btn.edit {
      background: #ffc107;
      color: #333;
    }
    
    .action-btn.delete {
      background: #dc3545;
      color: white;
    }
    
    .action-btn:hover {
      opacity: 0.8;
      transform: translateY(-2px);
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
  .action-btn[title]:hover::after {
      content: attr(title);
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      background: #333;
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
      white-space: nowrap;
      z-index: 1000;
      margin-bottom: 5px;
    }
    
    .action-btn[title] {
      position: relative;
    }
    
    /* Tooltip styling for bulk action buttons */
    .btn[title]:hover::after {
      content: attr(title);
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      background: #333;
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
      white-space: nowrap;
      z-index: 1000;
      margin-bottom: 5px;
    }
    
    .btn[title] {
      position: relative;
    }
    
    /* Tooltip styling for checkbox */
    input[type="checkbox"][title]:hover::after {
      content: attr(title);
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      background: #333;
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
      white-space: nowrap;
      z-index: 1000;
      margin-bottom: 5px;
    }
    
    input[type="checkbox"][title] {
      position: relative;
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
      <a class="menu-item active" href="admissions.php">
        <i class="fas fa-file-alt"></i>
        <span>Admissions</span>
      </a>
      <a class="menu-item" href="programs.php">
        <i class="fas fa-book"></i>
        <span>Programs</span>
      </a>
      <a class="menu-item" href="reports.php">
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
      <h5 style="margin: 0; color: var(--primary-blue);">Admissions Management</h5>
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
      <h2>Admissions Management</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Admissions</li>
        </ol>
      </nav>
    </div>
    
    <!-- Admissions Content -->
    <div class="content-card">
      <div class="content-card-header">
        <h5>Admission Applications & Inquiries</h5>
        <div>
          <button class="btn btn-success btn-sm me-2" onclick="approveSelected()" title="Approve selected admissions and send confirmation emails">
            <i class="fas fa-check"></i> Approve Selected
          </button>
          <button class="btn btn-danger btn-sm me-2" onclick="rejectSelected()" title="Reject selected admissions and send rejection emails">
            <i class="fas fa-times"></i> Reject Selected
          </button>
          <button class="btn btn-primary btn-sm me-2" onclick="sendEmailToSelected()" title="Send email to selected applicant">
            <i class="fas fa-envelope"></i> Send Email
          </button>
          <button class="btn btn-warning btn-sm" onclick="requestDocuments()" title="Request additional documents from selected applicants">
            <i class="fas fa-file-alt"></i> Request Documents
          </button>
        </div>
      </div>
      
      <div class="mb-3">
        <input type="text" class="form-control" placeholder="Search applications by name or application ID...">
      </div>
      
      <div class="table-responsive">
        <table class="table custom-table">
          <thead>
            <tr>
              <th><input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" title="Select/Deselect all admissions"></th>
              <th>Application ID</th>
              <th>Applicant Name</th>
              <th>Student ID</th>
              <th>Type</th>
              <th>Program</th>
              <th>Date Applied</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="admissionsTableBody">
            <!-- Data will be loaded dynamically -->
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Email Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Send Email to Applicant</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="emailForm">
              <div class="mb-3">
                <label class="form-label">Recipient Email</label>
                <input type="email" class="form-control" id="recipientEmail" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Email Type</label>
                <select class="form-select" id="emailType">
                  <option value="">Select Email Type</option>
                  <option value="application_received">Application Confirmation</option>
                  <option value="admission_approved">Approval Letter</option>
                  <option value="admission_rejected">Rejection Notice</option>
                  <option value="document_request">Document Request</option>
                  <option value="custom">Custom Email</option>
                </select>
              </div>
              <div class="mb-3" id="customSubjectDiv" style="display: none;">
                <label class="form-label">Subject</label>
                <input type="text" class="form-control" id="customSubject">
              </div>
              <div class="mb-3" id="customMessageDiv" style="display: none;">
                <label class="form-label">Message</label>
                <textarea class="form-control" id="customMessage" rows="5"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Attachments</label>
                <div class="border rounded p-3" id="attachmentsList">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <p class="text-muted mb-0">Available Documents:</p>
                    <button type="button" class="btn btn-sm btn-primary" onclick="showUploadModal()">
                      <i class="fas fa-upload"></i> Upload Document
                    </button>
                  </div>
                  <div id="documentsList">
                    <div class="text-center text-muted">
                      <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                      <p class="mt-2">Loading documents...</p>
                    </div>
                  </div>
                </div>
              </div>
              <input type="hidden" id="admissionId">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="sendEmail()">Send Email</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Document Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Upload Document</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="uploadForm" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label">Select File</label>
                <input type="file" class="form-control" id="documentFile" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt" required>
                <div class="form-text">Allowed: PDF, Word, Excel, Images, Text (Max 10MB)</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="documentDescription" rows="3" placeholder="Brief description of this document (optional)..."></textarea>
                <div class="form-text">Describe what this document is for (e.g., "Application form for new students", "Tuition fee schedule", etc.)</div>
              </div>
              <div class="progress" id="uploadProgress" style="display: none;">
                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="uploadDocument()">Upload</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="statusModalTitle">Update Admission Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="statusForm">
              <div class="mb-3">
                <label class="form-label">Applicant</label>
                <input type="text" class="form-control" id="statusApplicantName" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">New Status</label>
                <select class="form-select" id="newStatus">
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                  <option value="processing">Processing</option>
                  <option value="enrolled">Enrolled</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea class="form-control" id="statusNotes" rows="3" placeholder="Add notes about this status change..."></textarea>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="sendEmailWithStatus" checked>
                <label class="form-check-label" for="sendEmailWithStatus">
                  Send email notification to applicant
                </label>
              </div>
              <input type="hidden" id="statusAdmissionId">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="updateAdmissionStatus()">Update Status</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- View Admission Details Modal -->
    <div class="modal fade" id="viewAdmissionModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Admission Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="viewAdmissionContent">
              <!-- Admission details will be loaded here -->
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="openStatusModalFromView()">Update Status</button>
            <button type="button" class="btn btn-info" onclick="openEmailModalFromView()">Send Email</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Load Admissions Data
    function loadAdmissions() {
      fetch('../../../api/admissions/get-all.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Check for status filter in URL
            const urlParams = new URLSearchParams(window.location.search);
            const statusFilter = urlParams.get('status');
            
            let admissions = data.admissions;
            
            if (statusFilter) {
              // Filter admissions by status
              admissions = admissions.filter(admission => admission.status === statusFilter);
              
              // Update page title to reflect filter
              const pageTitle = document.querySelector('.page-header h2');
              if (pageTitle) {
                // Ensure proper capitalization for "Pending" and "Approved"
                const statusDisplay = statusFilter.charAt(0).toUpperCase() + statusFilter.slice(1);
                pageTitle.textContent = `${statusDisplay} Admissions`;
              }
            } else {
               // Reset title if no filter
               const pageTitle = document.querySelector('.page-header h2');
               if (pageTitle) {
                 pageTitle.textContent = 'Admissions Management';
               }
            }
            
            displayAdmissions(admissions);
          } else {
            console.error('Error loading admissions:', data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
    
    // Display Admissions in Table
    function displayAdmissions(admissions) {
      const tbody = document.getElementById('admissionsTableBody');
      tbody.innerHTML = '';
      
      admissions.forEach(admission => {
        const row = document.createElement('tr');
        
        // Format admission type badge
        const typeBadge = getAdmissionTypeBadge(admission.admission_type);
        
        // Format status badge
        const statusBadge = getStatusBadge(admission.status);
        
        row.innerHTML = `
          <td><input type="checkbox" value="${admission.id}"></td>
          <td>${admission.application_id}</td>
          <td>${admission.first_name} ${admission.last_name}</td>
          <td>${admission.student_id || '-'}</td>
          <td>${typeBadge}</td>
          <td>${admission.program_title || 'N/A'}</td>
          <td>${formatDate(admission.submitted_at)}</td>
          <td>${statusBadge}</td>
          <td>
            <button class="action-btn view" onclick="viewAdmission(${admission.id})" title="View Admission Details"><i class="fas fa-eye"></i></button>
            <button class="action-btn edit" onclick="openStatusModal(${admission.id}, '${admission.first_name} ${admission.last_name}')" title="Update Admission Status"><i class="fas fa-check"></i></button>
            <button class="action-btn email" onclick="openEmailModal(${admission.id})" title="Send Email to Applicant"><i class="fas fa-envelope"></i></button>
            <button class="action-btn delete" onclick="deleteAdmission(${admission.id})" title="Delete Admission Record"><i class="fas fa-trash"></i></button>
          </td>
        `;
        
        tbody.appendChild(row);
      });
    }
    
    // Get Admission Type Badge
    function getAdmissionTypeBadge(type) {
      const badges = {
        'freshman': '<span class="badge bg-primary">Freshman</span>',
        'transferee': '<span class="badge bg-warning">Transferee</span>',
        'returnee': '<span class="badge bg-success">Returnee</span>',
        'shifter': '<span class="badge bg-info">Shifter</span>'
      };
      return badges[type] || '<span class="badge bg-secondary">Unknown</span>';
    }
    
    // Get Status Badge
    function getStatusBadge(status) {
      const badges = {
        'pending': '<span class="badge-status pending">Pending</span>',
        'approved': '<span class="badge-status approved">Approved</span>',
        'rejected': '<span class="badge-status rejected">Rejected</span>',
        'processing': '<span class="badge-status processing">Processing</span>',
        'enrolled': '<span class="badge-status active">Enrolled</span>'
      };
      return badges[status] || '<span class="badge-status pending">Unknown</span>';
    }
    
    // Format Date
    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      });
    }
    
    // View Admission Details
    function viewAdmission(id) {
      // Fetch admission details
      fetch(`../../../api/admissions/get-single.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            displayAdmissionDetails(data.admission);
            
            // Store current admission ID for use in other functions
            window.currentAdmissionId = id;
            window.currentAdmission = data.admission;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('viewAdmissionModal'));
            modal.show();
          } else {
            alert('Error loading admission details: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error loading admission details');
        });
    }
    
    // Display admission details in the modal
    function displayAdmissionDetails(admission) {
      const content = document.getElementById('viewAdmissionContent');
      
      const typeBadge = getAdmissionTypeBadge(admission.admission_type);
      const statusBadge = getStatusBadge(admission.status);
      
      content.innerHTML = `
        <div class="row">
          <div class="col-md-6">
            <h6><strong>Personal Information</strong></h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Application ID:</strong></td>
                <td>${admission.application_id}</td>
              </tr>
              <tr>
                <td><strong>Full Name:</strong></td>
                <td>${admission.first_name} ${admission.middle_name || ''} ${admission.last_name}</td>
              </tr>
              <tr>
                <td><strong>Email:</strong></td>
                <td>${admission.email}</td>
              </tr>
              <tr>
                <td><strong>Phone:</strong></td>
                <td>${admission.phone || 'Not provided'}</td>
              </tr>
              <tr>
                <td><strong>Birthdate:</strong></td>
                <td>${admission.birthdate ? formatDate(admission.birthdate) : 'Not provided'}</td>
              </tr>
              <tr>
                <td><strong>Gender:</strong></td>
                <td>${admission.gender || 'Not provided'}</td>
              </tr>
              <tr>
                <td><strong>Address:</strong></td>
                <td>${admission.address || 'Not provided'}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6><strong>Academic Information</strong></h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Admission Type:</strong></td>
                <td>${typeBadge}</td>
              </tr>
              <tr>
                <td><strong>Program:</strong></td>
                <td>${admission.program_title || 'Not specified'}</td>
              </tr>
              <tr>
                <td><strong>Status:</strong></td>
                <td>${statusBadge}</td>
              </tr>
              <tr>
                <td><strong>Student ID:</strong></td>
                <td>${admission.student_id || 'Not assigned'}</td>
              </tr>
              <tr>
                <td><strong>Date Applied:</strong></td>
                <td>${formatDate(admission.submitted_at)}</td>
              </tr>
              <tr>
                <td><strong>Last Updated:</strong></td>
                <td>${formatDate(admission.updated_at)}</td>
              </tr>
            </table>
          </div>
        </div>
        
        ${admission.notes ? `
        <div class="row mt-3">
          <div class="col-12">
            <h6><strong>Notes</strong></h6>
            <div class="alert alert-info">
              ${admission.notes}
            </div>
          </div>
        </div>
        ` : ''}
      `;
    }
    
    // Helper functions for view modal
    function openStatusModalFromView() {
      // Close view modal
      bootstrap.Modal.getInstance(document.getElementById('viewAdmissionModal')).hide();
      
      // Open status modal with current admission
      if (window.currentAdmission) {
        openStatusModal(window.currentAdmissionId, `${window.currentAdmission.first_name} ${window.currentAdmission.last_name}`);
      }
    }
    
    function openEmailModalFromView() {
      // Close view modal
      bootstrap.Modal.getInstance(document.getElementById('viewAdmissionModal')).hide();
      
      // Open email modal with current admission
      if (window.currentAdmissionId) {
        openEmailModal(window.currentAdmissionId);
      }
    }
    
    // Update Admission Status
    function updateAdmissionStatus(id) {
      // TODO: Implement status update
      console.log('Update admission status:', id);
    }
    
    // Delete Admission
    function deleteAdmission(id) {
      const admissionName = getAdmissionName(id);
      
      if (confirm(`Are you sure you want to delete this admission?\n\n${admissionName}\n\nThis action cannot be undone.`)) {
        // Show loading state on the delete button
        const deleteButton = event.target.closest('button');
        const originalHTML = deleteButton.innerHTML;
        deleteButton.disabled = true;
        deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        // Call delete API
        fetch(`../../../api/admissions/delete.php?id=${id}`, {
          method: 'DELETE'
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
          }
          return response.json();
        })
        .then(data => {
          // Restore button
          deleteButton.disabled = false;
          deleteButton.innerHTML = originalHTML;
          
          if (data.success) {
            alert('Admission deleted successfully!');
            loadAdmissions(); // Reload the table
          } else {
            alert('Error deleting admission: ' + data.message);
          }
        })
        .catch(error => {
          // Restore button
          deleteButton.disabled = false;
          deleteButton.innerHTML = originalHTML;
          
          console.error('Error:', error);
          alert('Error deleting admission: ' + error.message);
        });
      }
    }
    
    // Helper function to get admission name for confirmation
    function getAdmissionName(id) {
      // Find the row with this admission ID
      const checkboxes = document.querySelectorAll('#admissionsTableBody input[type="checkbox"]');
      for (let checkbox of checkboxes) {
        if (checkbox.value === id) {
          const row = checkbox.closest('tr');
          const cells = row.getElementsByTagName('td');
          const nameCell = cells[2]; // Name is in the 3rd column (index 2)
          const appIdCell = cells[1]; // Application ID is in the 2nd column (index 1)
          return `Application ID: ${appIdCell.textContent}\nName: ${nameCell.textContent}`;
        }
      }
      return `Admission ID: ${id}`;
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
    
    // Email Type Change Handler
    document.getElementById('emailType')?.addEventListener('change', function() {
      const emailType = this.value;
      const customSubjectDiv = document.getElementById('customSubjectDiv');
      const customMessageDiv = document.getElementById('customMessageDiv');
      
      if (emailType === 'custom') {
        customSubjectDiv.style.display = 'block';
        customMessageDiv.style.display = 'block';
      } else {
        customSubjectDiv.style.display = 'none';
        customMessageDiv.style.display = 'none';
      }
    });
    
    // Send Email Function
    function sendEmail() {
      const admissionId = document.getElementById('admissionId').value;
      const emailType = document.getElementById('emailType').value;
      const recipientEmail = document.getElementById('recipientEmail').value;
      
      if (!emailType || !recipientEmail) {
        alert('Please select an email type');
        return;
      }
      
      // Get the send button and add loading state
      const sendButton = document.querySelector('#emailModal .btn-primary');
      const originalText = sendButton.innerHTML;
      const originalDisabled = sendButton.disabled;
      
      // Set loading state
      sendButton.disabled = true;
      sendButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending Email...';
      
      // Get selected attachments
      const attachments = [];
      const checkboxes = document.querySelectorAll('#attachmentsList input[type="checkbox"]:checked');
      checkboxes.forEach(checkbox => {
        attachments.push({
          path: checkbox.value,
          name: checkbox.nextElementSibling.textContent.trim()
        });
      });
      
      const emailData = {
        email_type: emailType,
        template_name: emailType,
        admission_id: admissionId,
        recipient_email: recipientEmail,
        attachments: attachments
      };
      
      // Add custom subject and message if custom email
      if (emailType === 'custom') {
        emailData.custom_subject = document.getElementById('customSubject').value;
        emailData.custom_message = document.getElementById('customMessage').value;
      }
      
      fetch('../../../api/email/just-work.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(emailData)
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        // Restore button state
        sendButton.disabled = originalDisabled;
        sendButton.innerHTML = originalText;
        
        if (data.success) {
          alert('Email sent successfully!');
          bootstrap.Modal.getInstance(document.getElementById('emailModal')).hide();
        } else {
          alert('Error sending email: ' + data.message);
          console.error('Email sending failed:', data);
        }
      })
      .catch(error => {
        // Restore button state
        sendButton.disabled = originalDisabled;
        sendButton.innerHTML = originalText;
        
        console.error('Network error:', error);
        alert('Error sending email: ' + error.message);
      });
    }
    
    // Update Admission Status Function
    function updateAdmissionStatus() {
      const admissionId = document.getElementById('statusAdmissionId').value;
      const newStatus = document.getElementById('newStatus').value;
      const notes = document.getElementById('statusNotes').value;
      const sendEmail = document.getElementById('sendEmailWithStatus').checked;
      
      if (!admissionId || !newStatus) {
        alert('Please select a status');
        return;
      }
      
      const statusData = {
        admission_id: admissionId,
        status: newStatus,
        notes: notes,
        send_email: sendEmail
      };
      
      fetch('../../../api/admissions/update-status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(statusData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Admission status updated successfully!');
          bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
          loadAdmissions(); // Reload the table
        } else {
          alert('Error updating status: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
      });
    }
    
    // Approve Selected Function
    function approveSelected() {
      const selected = getSelectedAdmissions();
      if (selected.length === 0) {
        alert('Please select at least one admission to approve');
        return;
      }
      
      if (confirm(`Approve ${selected.length} admission(s)?`)) {
        let completed = 0;
        selected.forEach(admissionId => {
          updateSingleAdmissionStatus(admissionId, 'approved', 'Approved by admin', () => {
            completed++;
            if (completed === selected.length) {
              loadAdmissions(); // Reload the table when all are done
            }
          });
        });
      }
    }
    
    // Reject Selected Function
    function rejectSelected() {
      const selected = getSelectedAdmissions();
      if (selected.length === 0) {
        alert('Please select at least one admission to reject');
        return;
      }
      
      if (confirm(`Reject ${selected.length} admission(s)?`)) {
        let completed = 0;
        selected.forEach(admissionId => {
          updateSingleAdmissionStatus(admissionId, 'rejected', 'Rejected by admin', () => {
            completed++;
            if (completed === selected.length) {
              loadAdmissions(); // Reload the table when all are done
            }
          });
        });
      }
    }
    
    // Send Email to Selected Function
    function sendEmailToSelected() {
      const selected = getSelectedAdmissions();
      if (selected.length === 0) {
        alert('Please select at least one admission to send email');
        return;
      }
      
      if (selected.length === 1) {
        openEmailModal(selected[0]);
      } else {
        alert('Please select only one admission to send custom email');
      }
    }
    
    // Request Documents Function
    function requestDocuments() {
      const selected = getSelectedAdmissions();
      if (selected.length === 0) {
        alert('Please select at least one admission to request documents');
        return;
      }
      
      if (confirm(`Send document request to ${selected.length} applicant(s)?`)) {
        selected.forEach(admissionId => {
          sendDocumentRequestEmail(admissionId);
        });
      }
    }
    
    // Helper Functions
    function toggleSelectAll() {
      const selectAllCheckbox = document.getElementById('selectAllCheckbox');
      const checkboxes = document.querySelectorAll('#admissionsTableBody input[type="checkbox"]');
      
      checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
      });
    }
    
    function getSelectedAdmissions() {
      const checkboxes = document.querySelectorAll('#admissionsTableBody input[type="checkbox"]:checked');
      const selected = [];
      checkboxes.forEach(checkbox => {
        // Get the admission ID from the checkbox value
        selected.push(checkbox.value);
      });
      return selected;
    }
    
    function updateSingleAdmissionStatus(admissionId, status, notes, callback) {
      const statusData = {
        admission_id: admissionId,
        status: status,
        notes: notes,
        send_email: true
      };
      
      fetch('../../../api/admissions/update-status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(statusData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (callback) callback();
        } else {
          console.error('Error updating status:', data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }
    
    function sendDocumentRequestEmail(admissionId) {
      // This would send a document request email
      console.log('Sending document request to admission:', admissionId);
    }
    
    function openEmailModal(admissionId) {
      // Fetch admission details to get the actual email
      fetch(`../../../api/admissions/get-single.php?id=${admissionId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('admissionId').value = admissionId;
            document.getElementById('recipientEmail').value = data.admission.email;
            
            const modal = new bootstrap.Modal(document.getElementById('emailModal'));
            modal.show();
          } else {
            alert('Error loading admission details');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error loading admission details');
        });
    }
    
    // Update action buttons in displayAdmissions function
    function updateActionButtons(admission) {
      return `
        <button class="action-btn view" onclick="viewAdmission(${admission.id})" title="View Details"><i class="fas fa-eye"></i></button>
        <button class="action-btn edit" onclick="openStatusModal(${admission.id}, '${admission.first_name} ${admission.last_name}')" title="Update Status"><i class="fas fa-check"></i></button>
        <button class="action-btn email" onclick="openEmailModal(${admission.id})" title="Send Email"><i class="fas fa-envelope"></i></button>
        <button class="action-btn delete" onclick="deleteAdmission(${admission.id})" title="Delete"><i class="fas fa-trash"></i></button>
      `;
    }
    
    function openStatusModal(admissionId, applicantName) {
      document.getElementById('statusAdmissionId').value = admissionId;
      document.getElementById('statusApplicantName').value = applicantName;
      document.getElementById('newStatus').value = '';
      document.getElementById('statusNotes').value = '';
      document.getElementById('sendEmailWithStatus').checked = true;
      
      const modal = new bootstrap.Modal(document.getElementById('statusModal'));
      modal.show();
    }
    
    // Load admissions when page loads
    document.addEventListener('DOMContentLoaded', function() {
      loadAdmissions();
      loadDocuments(); // Load available documents
    });
    
    // Document Management Functions
    function loadDocuments() {
      fetch('../../../api/documents/professional-documents-filebased.php')
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            displayDocuments(data.documents);
          } else {
            console.error('Error loading documents:', data.message);
            displayDocuments([]); // Show empty list on error
          }
        })
        .catch(error => {
          console.error('Error loading documents:', error);
          displayDocuments([]); // Show empty list on error
        });
    }
    
    function displayDocuments(documents) {
      const documentsList = document.getElementById('documentsList');
      
      if (documents.length === 0) {
        documentsList.innerHTML = `
          <div class="text-center text-muted">
            <i class="fas fa-file-alt fa-2x mb-2"></i>
            <p>No documents available. Upload your first document!</p>
          </div>
        `;
        return;
      }
      
      // Group documents by category
      const grouped = documents.reduce((acc, doc) => {
        if (!acc[doc.category]) acc[doc.category] = [];
        acc[doc.category].push(doc);
        return acc;
      }, {});
      
      let html = '';
      for (const [category, docs] of Object.entries(grouped)) {
        html += `
          <div class="mb-3">
            <h6 class="text-muted text-uppercase" style="font-size: 0.75rem;">${formatCategory(category)}</h6>
            <div class="row g-2">
        `;
        
        docs.forEach(doc => {
          html += `
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="${doc.file_path}" id="doc_${doc.id}" data-name="${doc.document_name}">
                <label class="form-check-label d-flex justify-content-between align-items-center" for="doc_${doc.id}">
                  <div>
                    <span class="fw-medium">${doc.document_name}</span>
                    <small class="text-muted d-block">${doc.file_size_formatted}</small>
                  </div>
                  <div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="previewDocument('${doc.id}')" title="Preview">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDocument('${doc.id}')" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </label>
              </div>
            </div>
          `;
        });
        
        html += `
            </div>
          </div>
        `;
      }
      
      documentsList.innerHTML = html;
    }
    
    function formatCategory(category) {
      const labels = {
        'application-form': 'Application Forms',
        'requirements': 'Requirements',
        'policies': 'School Policies',
        'templates': 'Email Templates',
        'general': 'General Documents',
        'other': 'Other'
      };
      return labels[category] || category;
    }
    
    function showUploadModal() {
      const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
      modal.show();
    }
    
    function uploadDocument() {
      const fileInput = document.getElementById('documentFile');
      const description = document.getElementById('documentDescription').value;
      
      if (!fileInput.files[0]) {
        alert('Please select a file to upload');
        return;
      }
      
      // Get upload button and add loading state
      const uploadButton = document.querySelector('#uploadModal .btn-primary');
      const originalText = uploadButton.innerHTML;
      const originalDisabled = uploadButton.disabled;
      
      // Set loading state
      uploadButton.disabled = true;
      uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
      
      const formData = new FormData();
      formData.append('document', fileInput.files[0]);
      formData.append('description', description);
      
      // Show progress
      const progressDiv = document.getElementById('uploadProgress');
      const progressBar = progressDiv.querySelector('.progress-bar');
      progressDiv.style.display = 'block';
      
      fetch('../../../api/documents/professional-documents-filebased.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        // Restore button state
        uploadButton.disabled = originalDisabled;
        uploadButton.innerHTML = originalText;
        progressDiv.style.display = 'none';
        
        if (data.success) {
          alert('Document uploaded successfully!');
          bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
          
          // Reset form
          document.getElementById('uploadForm').reset();
          
          // Reload documents list
          loadDocuments();
        } else {
          alert('Error uploading document: ' + data.message);
        }
      })
      .catch(error => {
        // Restore button state
        uploadButton.disabled = originalDisabled;
        uploadButton.innerHTML = originalText;
        progressDiv.style.display = 'none';
        
        console.error('Error:', error);
        alert('Error uploading document');
      });
    }
    
    function previewDocument(documentId) {
      // Open document in new tab for preview
      window.open(`/CNESIS/api/documents/preview.php?id=${documentId}`, '_blank');
    }
    
    function deleteDocument(documentId) {
      if (confirm('Are you sure you want to delete this document?')) {
        fetch(`../../../api/documents/professional-documents-filebased.php?id=${documentId}`, {
          method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Document deleted successfully!');
            loadDocuments(); // Reload the list
          } else {
            alert('Error deleting document: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error deleting document');
        });
      }
    }
    
    // Update sendEmail function to use dynamic attachments
    function sendEmail() {
      const admissionId = document.getElementById('admissionId').value;
      const emailType = document.getElementById('emailType').value;
      const recipientEmail = document.getElementById('recipientEmail').value;
      
      if (!emailType || !recipientEmail) {
        alert('Please select an email type');
        return;
      }
      
      // Get the send button and add loading state
      const sendButton = document.querySelector('#emailModal .btn-primary');
      const originalText = sendButton.innerHTML;
      const originalDisabled = sendButton.disabled;
      
      // Set loading state
      sendButton.disabled = true;
      sendButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending Email...';
      
      // Get selected attachments from dynamic document list
      const attachments = [];
      const checkboxes = document.querySelectorAll('#documentsList input[type="checkbox"]:checked');
      checkboxes.forEach(checkbox => {
        attachments.push({
          path: checkbox.value,
          name: checkbox.dataset.name
        });
      });
      
      const emailData = {
        email_type: emailType,
        template_name: emailType,
        admission_id: admissionId,
        recipient_email: recipientEmail,
        attachments: attachments
      };
      
      // Add custom subject and message if custom email
      if (emailType === 'custom') {
        emailData.custom_subject = document.getElementById('customSubject').value;
        emailData.custom_message = document.getElementById('customMessage').value;
      }
      
      fetch('../../../api/email/just-work.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(emailData)
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        // Restore button state
        sendButton.disabled = originalDisabled;
        sendButton.innerHTML = originalText;
        
        if (data.success) {
          alert('Email sent successfully!');
          bootstrap.Modal.getInstance(document.getElementById('emailModal')).hide();
        } else {
          // Show detailed error information
          let errorMsg = 'Error sending email: ' + data.message;
          
          // Add debug info if available
          if (data.debug) {
            errorMsg += '\n\nDebug Info:\n';
            for (const [key, value] of Object.entries(data.debug)) {
              errorMsg += `${key}: ${value}\n`;
            }
          }
          
          // Add response info
          errorMsg += `\n\nResponse Status: ${response.status}`;
          errorMsg += `\nTimestamp: ${new Date().toISOString()}`;
          
          alert(errorMsg);
          console.error('Email sending failed:', data);
        }
      })
      .catch(error => {
        // Restore button state
        sendButton.disabled = originalDisabled;
        sendButton.innerHTML = originalText;
        
        console.error('Network error:', error);
        
        let errorMsg = 'Network error sending email:\n';
        errorMsg += `Error: ${error.message}\n`;
        errorMsg += `Timestamp: ${new Date().toISOString()}\n`;
        
        // Safely access error properties
        if (error.config) {
            errorMsg += `URL: ${error.config.url || 'Unknown'}\n`;
            errorMsg += `Method: ${error.config.method || 'Unknown'}\n`;
        }
        
        if (error.response) {
            errorMsg += `Response Status: ${error.response.status}\n`;
            errorMsg += `Response Text: ${error.response.statusText || 'No text'}`;
        }
        
        // Add stack trace for debugging
        if (error.stack) {
            errorMsg += `\n\nStack Trace:\n${error.stack}`;
        }
        
        alert(errorMsg);
      });
    }
    
    // Initialize page - load admissions data
    document.addEventListener('DOMContentLoaded', function() {
      loadAdmissions();
      loadDocuments();
    });
  </script>
</body>
</html>
