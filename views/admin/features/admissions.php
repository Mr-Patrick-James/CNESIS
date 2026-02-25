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
    
    /* Submenu Styles */
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
      margin-right: 15px; /* Added spacing */
    }

    /* Chevron rotation */
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

    .badge-status.verified {
      background: #e2e3e5;
      color: #383d41;
    }
    
    .badge-status.scheduled {
      background: #cce5ff;
      color: #004085;
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
  <?php include 'sidebar.php'; ?>
  
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
          <button id="approveSelectedBtn" class="btn btn-success btn-sm me-2" onclick="approveSelected()" title="Approve selected admissions and send confirmation emails">
            <i class="fas fa-check"></i> Approve Selected
          </button>
          <button id="rejectSelectedBtn" class="btn btn-danger btn-sm me-2" onclick="rejectSelected()" title="Reject selected admissions and send rejection emails">
            <i class="fas fa-times"></i> Reject Selected
          </button>
          <!-- Email and Request Documents actions removed per client request -->
        </div>
      </div>
      
      <div class="mb-3">
        <div class="row g-2">
          <div class="col-md-4">
            <input type="text" class="form-control" id="searchAdmissions" placeholder="Search applications by name or application ID...">
          </div>
          <div class="col-md-4">
            <select class="form-select" id="filterAdmissionType">
              <option value="">All Types</option>
              <option value="freshman">Freshman</option>
              <option value="transferee">Transferee</option>
            </select>
          </div>
          <div class="col-md-4">
            <select class="form-select" id="filterProgram">
              <option value="">All Programs</option>
            </select>
          </div>
        </div>
      </div>
      
      <div class="table-responsive">
        <table class="table custom-table">
          <thead>
            <tr>
              <th><input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" title="Select/Deselect all admissions"></th>
              <th>Application ID</th>
              <th>Applicant Name</th>
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
                  <option value="verified">Verified</option>
                  <option value="scheduled">Scheduled</option>
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
            window.allAdmissions = admissions;
            
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
              
              // Hide or show header actions based on filter
              const approveBtn = document.getElementById('approveSelectedBtn');
              const rejectBtn = document.getElementById('rejectSelectedBtn');
              if (approveBtn && rejectBtn) {
                if (statusFilter === 'approved') {
                  approveBtn.style.display = 'none';
                  rejectBtn.style.display = 'none';
                } else if (statusFilter === 'pending') {
                  approveBtn.style.display = '';
                  rejectBtn.style.display = '';
                } else {
                  approveBtn.style.display = '';
                  rejectBtn.style.display = '';
                }
              }
              
              // Store filter for row actions
              window.currentStatusFilter = statusFilter;
            } else {
               // Reset title if no filter
               const pageTitle = document.querySelector('.page-header h2');
               if (pageTitle) {
                 pageTitle.textContent = 'Admissions Management';
               }
               // Ensure header actions visible in "All"
               const approveBtn = document.getElementById('approveSelectedBtn');
               const rejectBtn = document.getElementById('rejectSelectedBtn');
               if (approveBtn) approveBtn.style.display = '';
               if (rejectBtn) rejectBtn.style.display = '';
               window.currentStatusFilter = null;
            }
            
            displayAdmissions(applyFilters(window.allAdmissions));
          } else {
            console.error('Error loading admissions:', data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
    
    function applyFilters(list) {
      const typeVal = document.getElementById('filterAdmissionType')?.value || '';
      const programVal = document.getElementById('filterProgram')?.value || '';
      const statusVal = window.currentStatusFilter || '';
      
      let result = Array.isArray(list) ? list.slice() : [];
      
      if (statusVal) {
        result = result.filter(item => item.status === statusVal);
      }
      if (typeVal) {
        result = result.filter(item => (item.admission_type || '').toLowerCase() === typeVal.toLowerCase());
      }
      if (programVal) {
        result = result.filter(item => (item.program_code || '').toLowerCase() === programVal.toLowerCase());
      }
      return result;
    }
    
    // Display Admissions in Table
    function displayAdmissions(admissions) {
      const tbody = document.getElementById('admissionsTableBody');
      tbody.innerHTML = '';
      
      const filtered = applyFilters(admissions);
      
      filtered.forEach(admission => {
        const row = document.createElement('tr');
        
        // Format admission type badge
        const typeBadge = getAdmissionTypeBadge(admission.admission_type);
        
        // Format status badge
        const statusBadge = getStatusBadge(admission.status);
        
        const deleteButtonHtml = (window.currentStatusFilter === 'approved') ? '' :
          `<button class="action-btn delete" onclick="deleteAdmission(${admission.id})" title="Delete Admission Record"><i class="fas fa-trash"></i></button>`;
        
        row.innerHTML = `
          <td><input type="checkbox" value="${admission.id}"></td>
          <td>${admission.application_id}</td>
          <td>${admission.first_name} ${admission.last_name}</td>
          <td>${typeBadge}</td>
          <td>${admission.program_title || 'N/A'}</td>
          <td>${formatDate(admission.submitted_at)}</td>
          <td>${statusBadge}</td>
          <td>
            <button class="action-btn view" onclick="viewAdmission(${admission.id})" title="View Admission Details"><i class="fas fa-eye"></i></button>
            <button class="action-btn edit" onclick="openStatusModal(${admission.id}, '${admission.first_name} ${admission.last_name}')" title="Update Admission Status"><i class="fas fa-check"></i></button>
            ${deleteButtonHtml}
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
        'enrolled': '<span class="badge-status active">Enrolled</span>',
        'verified': '<span class="badge-status verified">Verified</span>',
        'scheduled': '<span class="badge-status scheduled">Scheduled</span>'
      };
      return badges[status] || '<span class="badge-status pending">Unknown</span>';
    }
    
    // Format Date
    function formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return 'N/A';
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
      
      // Parse JSON data
      let formData = {};
      let attachments = [];
      
      try {
        formData = typeof admission.form_data === 'string' ? JSON.parse(admission.form_data || '{}') : admission.form_data;
      } catch (e) {
        console.error('Error parsing form_data:', e);
      }
      
      try {
        attachments = typeof admission.attachments === 'string' ? JSON.parse(admission.attachments || '[]') : admission.attachments;
      } catch (e) {
        console.error('Error parsing attachments:', e);
      }
      
      // Construct Parents HTML
      let parentsHtml = `
          <div class="text-center py-5 text-muted">
              <i class="fas fa-users fa-3x mb-3 text-secondary opacity-50"></i>
              <p>No parent information available</p>
          </div>`;
      
      // Check for object array structure (new format) or flat arrays (legacy)
      const hasParents = formData && (
          (formData.parents && Array.isArray(formData.parents) && formData.parents.length > 0) ||
          (formData.parent_first_name && Array.isArray(formData.parent_first_name) && formData.parent_first_name.length > 0) ||
          formData.father_name || formData.mother_name || formData.guardian_name
      );

      if (hasParents) {
        parentsHtml = '<div class="row g-3">';
        
        // Helper to create card
        const createParentCard = (parent) => `
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm" style="background-color: #f8f9fa;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-white p-3 shadow-sm me-3 text-primary">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">${parent.first_name} ${parent.middle_name || ''} ${parent.last_name || ''} ${parent.extension || ''}</h6>
                                <div class="badge bg-info bg-opacity-10 text-info">${parent.relationship || 'Guardian'}</div>
                                ${parent.is_emergency ? '<span class="badge bg-danger bg-opacity-10 text-danger ms-1">Emergency</span>' : ''}
                            </div>
                        </div>
                        <div class="small text-muted ps-1">
                            <div class="mb-1 row"><div class="col-4"><i class="fas fa-birthday-cake me-2"></i> Age:</div><div class="col-8 fw-medium">${parent.age || 'N/A'}</div></div>
                            <div class="mb-1 row"><div class="col-4"><i class="fas fa-briefcase me-2"></i> Job:</div><div class="col-8 fw-medium">${parent.occupation || 'N/A'}</div></div>
                            <div class="mb-1 row"><div class="col-4"><i class="fas fa-money-bill-wave me-2"></i> Income:</div><div class="col-8 fw-medium">${parent.income || 'N/A'}</div></div>
                            <div class="mb-1 row"><div class="col-4"><i class="fas fa-phone-alt me-2"></i> Contact:</div><div class="col-8 fw-medium">${parent.contact || 'N/A'}</div></div>
                            <div class="mb-1 row"><div class="col-4"><i class="fas fa-graduation-cap me-2"></i> Educ:</div><div class="col-8 fw-medium">${parent.education || 'N/A'}</div></div>
                            <div class="row"><div class="col-4"><i class="fas fa-map-marker-alt me-2"></i> Addr:</div><div class="col-8 fw-medium">${parent.city ? (parent.street ? parent.street + ', ' : '') + parent.city : 'N/A'}</div></div>
                        </div>
                    </div>
                </div>
            </div>`;

        // Handle object array structure (newest format)
        if (formData.parents && Array.isArray(formData.parents)) {
             formData.parents.forEach(parent => {
                 if (parent.first_name) {
                     parentsHtml += createParentCard(parent);
                 }
             });
        }
        // Handle array structure from earlier form versions
        else if (Array.isArray(formData.parent_first_name)) {
            for(let i=0; i<formData.parent_first_name.length; i++) {
                if(formData.parent_first_name[i]) {
                    parentsHtml += createParentCard({
                        first_name: formData.parent_first_name[i],
                        last_name: formData.parent_last_name[i],
                        relationship: formData.parent_relationship[i],
                        contact: formData.parent_contact[i],
                        occupation: formData.parent_occupation[i],
                        // Legacy mapping might not have all fields
                    });
                }
            }
        } 
        // Handle legacy flat structure if any
        else {
            if (formData.father_name) parentsHtml += createParentCard({first_name: formData.father_name, relationship: 'Father', contact: formData.father_contact});
            if (formData.mother_name) parentsHtml += createParentCard({first_name: formData.mother_name, relationship: 'Mother', contact: formData.mother_contact});
            if (formData.guardian_name) parentsHtml += createParentCard({first_name: formData.guardian_name, relationship: 'Guardian', contact: formData.guardian_contact});
        }
        parentsHtml += '</div>';
      }

      // Construct Schools HTML
      let schoolsHtml = `
          <div class="text-center py-5 text-muted">
              <i class="fas fa-school fa-3x mb-3 text-secondary opacity-50"></i>
              <p>No school information available</p>
          </div>`;
      
      const hasSchools = formData && (
          (formData.schools && Array.isArray(formData.schools) && formData.schools.length > 0) ||
          (formData.school_name && Array.isArray(formData.school_name) && formData.school_name.length > 0) ||
          formData.last_school
      );

      if (hasSchools) {
         schoolsHtml = '<div class="timeline">';
         
         const createSchoolItem = (school) => `
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="fw-bold mb-1 text-primary">${school.name}</h6>
                            <div class="mb-2">
                                <span class="badge bg-light text-dark border me-1">${school.level || 'N/A'}</span>
                                <span class="badge bg-light text-dark border me-1">${school.type || 'Type N/A'}</span>
                            </div>
                            <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i> ${school.city || 'City N/A'}</div>
                        </div>
                        <span class="badge bg-primary rounded-pill">${school.year || 'Year N/A'}</span>
                    </div>
                    ${school.honors ? `<div class="mt-2 small text-success"><i class="fas fa-award me-1"></i> ${school.honors}</div>` : ''}
                </div>
            </div>`;

         // Handle object array structure (newest format)
         if (formData.schools && Array.isArray(formData.schools)) {
             formData.schools.forEach(school => {
                 if (school.name) {
                     schoolsHtml += createSchoolItem(school);
                 }
             });
         }
         // Handle array structure from earlier versions
         else if (Array.isArray(formData.school_name)) {
             for(let i=0; i<formData.school_name.length; i++) {
                 if(formData.school_name[i]) {
                     schoolsHtml += createSchoolItem({
                         name: formData.school_name[i],
                         level: formData.school_level[i],
                         year: formData.school_year[i],
                         honors: formData.school_honors[i],
                         type: formData.school_type ? formData.school_type[i] : null,
                         city: formData.school_city ? formData.school_city[i] : null
                     });
                 }
             }
         } else if (formData.last_school) {
             schoolsHtml += createSchoolItem({
                 name: formData.last_school,
                 level: 'Previous School',
                 year: formData.year_graduated
             });
         }
         schoolsHtml += '</div>';
      }
      
      // Construct Attachments HTML
       let attachmentsHtml = `
          <div class="text-center py-5 text-muted">
              <i class="fas fa-file-upload fa-3x mb-3 text-secondary opacity-50"></i>
              <p>No documents uploaded</p>
          </div>`;
       
       // Normalize attachments to array
       let attachmentsArray = [];
       if (Array.isArray(attachments)) {
           attachmentsArray = attachments;
       } else if (typeof attachments === 'object' && attachments !== null) {
           for (const [key, value] of Object.entries(attachments)) {
               if (Array.isArray(value)) {
                    value.forEach(path => {
                        attachmentsArray.push({ name: key, path: path, type: key });
                    });
               } else {
                    attachmentsArray.push({ name: key, path: value, type: key });
               }
           }
       }
       
       if (attachmentsArray.length > 0) {
         attachmentsHtml = '<div class="row g-3">';
         attachmentsArray.forEach(file => {
             // Determine icon based on file type
             let icon = 'fa-file';
             let colorClass = 'text-secondary';
             // Handle case where path might be undefined
             if (!file.path) return;
             
             const ext = file.path.split('.').pop().toLowerCase();
             if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) { icon = 'fa-file-image'; colorClass = 'text-success'; }
             else if (ext === 'pdf') { icon = 'fa-file-pdf'; colorClass = 'text-danger'; }
             else if (['doc', 'docx'].includes(ext)) { icon = 'fa-file-word'; colorClass = 'text-primary'; }
             
             // Construct correct path
             let filePath = file.path;
             
             // If path is absolute or starts with http, keep it
             if (filePath.startsWith('http') || filePath.startsWith('//')) {
                 // keep as is
             } 
             // If it starts with /CNESIS, make it relative to root
             else if (filePath.startsWith('/CNESIS/')) {
                  filePath = '../../..' + filePath.replace('/CNESIS', '');
             }
             // If it's a relative path from root (assets/...)
             else if (!filePath.startsWith('/')) {
                 filePath = '../../../' + filePath;
             }
             
             // Format display name
             let displayName = file.name || 'Document';
             
             // Special handling for common technical names
             const technicalNames = {
                 'valid_id': 'Valid ID',
                 'shs_cert': 'SHS Report Card (Form 138)',
                 'good_moral': 'Good Moral Certificate',
                 'diploma': 'Diploma / Graduation Cert',
                 'tor': 'Transcript of Records (TOR)',
                 'transfer_cred': 'Honorable Dismissal / Transfer Credentials'
             };
             
             if (technicalNames[file.name]) {
                 displayName = technicalNames[file.name];
             } else {
                 // Capitalize and replace underscores
                 displayName = displayName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
             }
             
             attachmentsHtml += `
             <div class="col-md-6 col-lg-4">
                 <div class="card h-100 border shadow-sm hover-shadow transition-all">
                     <div class="card-body text-center p-4">
                         <i class="fas ${icon} fa-3x mb-3 ${colorClass}"></i>
                         <h6 class="card-title text-truncate w-100" title="${displayName}">${displayName}</h6>
                         <p class="text-muted small mb-3">${file.type || 'Document'}</p>
                         <a href="${filePath}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                             <i class="fas fa-eye me-1"></i> View
                         </a>
                     </div>
                 </div>
             </div>`;
         });
         attachmentsHtml += '</div>';
       }

       // Construct Additional Info HTML
       const additionalHtml = `
           <div class="card border-0 shadow-sm mb-3">
               <div class="card-body">
                   <h6 class="fw-bold mb-3 text-primary">Academic Background</h6>
                   <div class="row g-3 mb-4">
                       <div class="col-12">
                           <label class="small text-muted">Alternative Program (2nd Choice)</label>
                           <div class="fw-medium">${formData.alternative_program_title || 'None selected'}</div>
                       </div>
                       <div class="col-md-6">
                           <label class="small text-muted">SHS Strand</label>
                           <div class="fw-medium">${formData.shs_strand || 'N/A'}</div>
                       </div>
                       <div class="col-md-6">
                           <label class="small text-muted">Latest Attainment</label>
                           <div class="fw-medium">${formData.latest_attainment || 'N/A'}</div>
                       </div>
                       <div class="col-md-4">
                           <label class="small text-muted">Grade 10 GPA</label>
                           <div class="fw-medium">${formData.grade10_gpa || 'N/A'}</div>
                       </div>
                       <div class="col-md-4">
                           <label class="small text-muted">Grade 11 GPA</label>
                           <div class="fw-medium">${formData.grade11_gpa || 'N/A'}</div>
                       </div>
                       <div class="col-md-4">
                           <label class="small text-muted">Grade 12 GPA</label>
                           <div class="fw-medium">${formData.grade12_gpa || 'N/A'}</div>
                       </div>
                   </div>

                   <h6 class="fw-bold mb-3 text-primary">Status & Welfare</h6>
                   <div class="row g-3">
                       <!-- Removed Health Problem as per client request -->
                       <!-- Removed First Male in Family as per client request -->
                       <!-- Removed Equity Target Group as per client request -->
                   </div>
               </div>
           </div>`;

       // Construct AAP Survey HTML
       const aapHtml = `
           <div class="card border-0 shadow-sm">
               <div class="card-body">
                   <h6 class="fw-bold mb-3 text-primary">AAP / Survey Responses</h6>
                   <div class="alert alert-light border">
                       <div class="mb-3">
                           <label class="small text-muted fw-bold">Academic Status</label>
                           <div class="text-capitalize">${formData.academic_status || 'N/A'}</div>
                       </div>
                       <div class="mb-3">
                           <label class="small text-muted fw-bold">Already Enrolled in College?</label>
                           <div class="text-capitalize">${formData.already_enrolled || 'N/A'}</div>
                       </div>
                       <div class="mb-3">
                           <label class="small text-muted fw-bold">First Time Applying?</label>
                           <div class="text-capitalize">${formData.first_time_apply || 'N/A'}</div>
                       </div>
                        <div class="mb-3">
                           <label class="small text-muted fw-bold">Transferred during SHS?</label>
                           <div class="text-capitalize">${formData.shs_transfer || 'N/A'}</div>
                           ${formData.shs_transfer === 'yes' ? `
                               <div class="ps-3 mt-1 small text-muted border-start border-3 border-warning">
                                   <div>From: ${formData.shs_transfer_from || 'N/A'}</div>
                                   <div>Year: ${formData.shs_transfer_year || 'N/A'}</div>
                               </div>
                           ` : ''}
                       </div>
                   </div>
               </div>
           </div>`;

      content.innerHTML = `
        <!-- Profile Header -->
        <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 80px; height: 80px; font-size: 2rem;">
                ${admission.first_name.charAt(0)}${admission.last_name.charAt(0)}
            </div>
            <div>
                <h4 class="mb-1 fw-bold">${admission.first_name} ${admission.middle_name || ''} ${admission.last_name} ${formData.suffix || ''}</h4>
                <div class="d-flex align-items-center text-muted mb-2">
                    <span class="me-3"><i class="fas fa-envelope me-1"></i> <a href="mailto:${admission.email}" class="text-decoration-none text-muted">${admission.email}</a></span>
                    <span><i class="fas fa-phone me-1"></i> ${admission.phone || 'N/A'}</span>
                </div>
                <div class="d-flex gap-2">
                    ${statusBadge}
                    <span class="badge bg-light text-dark border">App ID: ${admission.application_id}</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Program</small>
                        <div class="fw-bold text-dark mt-1">${admission.program_title || 'N/A'}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Admission Type</small>
                        <div class="mt-1">${typeBadge}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Date Applied</small>
                        <div class="fw-bold text-dark mt-1">${formatDate(admission.submitted_at)}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs Navigation -->
        <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded" id="admissionDetailsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                    <i class="fas fa-user me-2"></i>Personal
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" id="education-tab" data-bs-toggle="tab" data-bs-target="#education" type="button" role="tab">
                    <i class="fas fa-graduation-cap me-2"></i>Education
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" id="family-tab" data-bs-toggle="tab" data-bs-target="#family" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Family
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" id="other-tab" data-bs-toggle="tab" data-bs-target="#other" type="button" role="tab">
                    <i class="fas fa-info-circle me-2"></i>Other Info
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                    <i class="fas fa-folder-open me-2"></i>Documents
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="admissionDetailsContent">
            <!-- Personal Info Tab -->
            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-primary">Basic Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="small text-muted">Birthdate</label>
                                <div class="fw-medium">${admission.birthdate ? formatDate(admission.birthdate) : 'Not provided'}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Gender</label>
                                <div class="fw-medium text-capitalize">${admission.gender || formData.gender || 'Not provided'}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Civil Status</label>
                                <div class="fw-medium text-capitalize">${formData.civil_status || 'Not provided'}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Citizenship</label>
                                <div class="fw-medium text-capitalize">${formData.citizenship || 'Not provided'}</div>
                            </div>
                            <div class="col-12">
                                <label class="small text-muted">Address</label>
                                <div class="fw-medium">
                                    ${admission.address || 
                                      (formData.street_no ? `${formData.street_no}, ` : '') + 
                                      (formData.barangay ? `${formData.barangay}, ` : '') + 
                                      (formData.city_province ? `${formData.city_province} ` : '') + 
                                      (formData.zip_code || '') || 
                                      (formData.house_no || '') + ' ' + (formData.street || '') + ' ' + (formData.barangay || '') + ' ' + (formData.municipality || '') + ' ' + (formData.zip_code || '')}
                                </div>
                            </div>
                             <div class="col-12">
                                <label class="small text-muted">Birth Place</label>
                                <div class="fw-medium">${formData.birth_place || 'Not provided'}</div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-primary">Academic Details</h6>
                        <div class="row g-3">
                            ${admission.admission_type !== 'freshman' ? `
                            <div class="col-md-6">
                                <label class="small text-muted">Student ID</label>
                                <div class="fw-medium font-monospace">${admission.student_id || 'Not assigned'}</div>
                            </div>
                            ` : ''}
                            <div class="col-md-6">
                                <label class="small text-muted">Last Updated</label>
                                <div class="fw-medium">${formatDate(admission.updated_at)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Education Tab -->
            <div class="tab-pane fade" id="education" role="tabpanel">
                ${schoolsHtml}
            </div>

            <!-- Family Tab -->
            <div class="tab-pane fade" id="family" role="tabpanel">
                ${parentsHtml}
            </div>

             <!-- Other Info Tab -->
            <div class="tab-pane fade" id="other" role="tabpanel">
                ${additionalHtml}
                <div class="mt-4">
                    ${aapHtml}
                </div>
            </div>

            <!-- Documents Tab -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                ${attachmentsHtml}
            </div>
        </div>
        
        ${admission.notes ? `
        <div class="mt-4">
            <h6 class="fw-bold mb-2 text-primary"><i class="fas fa-sticky-note me-2"></i>Admin Notes</h6>
            <div class="alert alert-info border-0 shadow-sm">
                ${admission.notes}
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

    // Toggle Submenu
    document.getElementById('admissionsMenuLink').addEventListener('click', function(e) {
        e.preventDefault();
        
        const submenu = document.getElementById('admissionsSubmenu');
        const chevron = this.querySelector('.fa-chevron-down');
        
        if (submenu.classList.contains('show')) {
            submenu.classList.remove('show');
            this.classList.add('collapsed');
            if(chevron) chevron.style.transform = 'rotate(-90deg)';
        } else {
            submenu.classList.add('show');
            this.classList.remove('collapsed');
            if(chevron) chevron.style.transform = 'rotate(0deg)';
        }
        
        // If sidebar is collapsed, expand it
        const sidebar = document.getElementById('sidebar');
        if (sidebar.classList.contains('collapsed')) {
            sidebar.classList.remove('collapsed');
        }
    });

    // Check active state for submenu
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        
        const submenu = document.getElementById('admissionsSubmenu');
        if (!submenu) return;
        
        const mainLink = document.getElementById('admissionsMenuLink');
        
        // Reset all active classes in submenu
        const links = submenu.querySelectorAll('a');
        links.forEach(link => link.classList.remove('active'));
        
        if (status === 'pending') {
            links[0].classList.add('active'); // Pending
        } else if (status === 'approved') {
            links[1].classList.add('active'); // Approved
        } else {
            // Check if we are on the admissions page without params (All)
            if (window.location.pathname.endsWith('admissions.php') && !status) {
                 links[2].classList.add('active'); // All
            }
        }
        
        // Ensure main link is active
        if (mainLink) {
            mainLink.classList.add('active');
            mainLink.classList.remove('collapsed');
        }
        
        // Ensure submenu is shown
        submenu.classList.add('show');
    });
    
    // Email Type Change Handler
    // Update Admission Status Function
    function updateAdmissionStatus() {
      const admissionId = document.getElementById('statusAdmissionId').value;
      const newStatus = document.getElementById('newStatus').value;
      const notes = document.getElementById('statusNotes').value;
      
      if (!admissionId || !newStatus) {
        alert('Please select a status');
        return;
      }
      
      const statusData = {
        admission_id: admissionId,
        status: newStatus,
        notes: notes
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
        notes: notes
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
    
    // Email modal and related action buttons removed
    
    function openStatusModal(admissionId, applicantName) {
      document.getElementById('statusAdmissionId').value = admissionId;
      document.getElementById('statusApplicantName').value = applicantName;
      document.getElementById('newStatus').value = '';
      document.getElementById('statusNotes').value = '';
      // Removed email notification checkbox
      
      const modal = new bootstrap.Modal(document.getElementById('statusModal'));
      modal.show();
    }
    
    // Load admissions when page loads
    document.addEventListener('DOMContentLoaded', function() {
      loadAdmissions();
      fetch('../../../api/programs/get-all.php?status=active')
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            const sel = document.getElementById('filterProgram');
            res.programs.forEach(p => {
              const opt = document.createElement('option');
              opt.value = (p.code || '').toLowerCase();
              opt.textContent = p.short_title ? `${p.short_title} (${p.code})` : `${p.title} (${p.code})`;
              sel.appendChild(opt);
            });
          }
        });
      const typeSel = document.getElementById('filterAdmissionType');
      const progSel = document.getElementById('filterProgram');
      const searchInput = document.getElementById('searchAdmissions');
      if (typeSel) typeSel.addEventListener('change', () => displayAdmissions(window.allAdmissions || []));
      if (progSel) progSel.addEventListener('change', () => displayAdmissions(window.allAdmissions || []));
      if (searchInput) searchInput.addEventListener('input', () => displayAdmissions(window.allAdmissions || []));
    });
    
    // Document management functions removed
    
    // Email and document upload functions removed
    
    // Initialize page - load admissions data
    document.addEventListener('DOMContentLoaded', function() {
      loadAdmissions();
    });
  </script>
</body>
</html>
