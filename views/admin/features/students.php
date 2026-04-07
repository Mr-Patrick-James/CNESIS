<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student List - Admin Panel</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  
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
    
    .sortable-col {
      cursor: pointer;
      user-select: none;
      transition: background-color 0.2s;
      position: relative;
    }
    
    .sortable-col:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
    
    .sort-icon {
      font-size: 0.8rem;
      margin-left: 5px;
      opacity: 0.5;
    }
    
    .sortable-col.active-sort .sort-icon {
      opacity: 1;
      color: var(--accent-gold);
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
    
    .badge-status.inactive {
      background: #f8d7da;
      color: #721c24;
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

    /* Fix for modal z-index issues */
    .modal-backdrop {
      z-index: 1040 !important;
    }
    .modal {
      z-index: 1050 !important;
    }
    
    /* Ensure modal content is visible */
    .modal-content {
        box-shadow: 0 5px 15px rgba(0,0,0,0.5);
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
  <?php include 'sidebar.php'; ?>
  
  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
      </button>
      <h5 style="margin: 0; color: var(--primary-blue);">Student List</h5>
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
      <h2>Student List</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Students</li>
        </ol>
      </nav>
    </div>
    
    <div class="content-card">
      <div class="content-card-header">
        <h5>All Students</h5>
        <div>
          <button class="btn btn-danger btn-sm me-2" onclick="deleteAllStudents()" title="Delete all students and their accounts">
            <i class="fas fa-trash-alt"></i> Delete All
          </button>
          <button class="btn btn-success btn-sm me-2" onclick="importStudents()" title="Import students from CSV/Excel">
            <i class="fas fa-file-import"></i> Import
          </button>
          <button class="btn btn-success btn-sm me-2" onclick="generateStudentListExcel()" title="Generate student list Excel (Regular/Irregular)">
            <i class="fas fa-file-excel"></i> Excel
          </button>
          <button class="btn btn-info btn-sm me-2" onclick="exportStudents()" title="Export students to CSV/Excel">
            <i class="fas fa-file-export"></i> Export
          </button>
          <button class="btn btn-secondary btn-sm me-2" onclick="graduateFourthYears()" title="Set all regular 4th year students to Graduated">
            <i class="fas fa-graduation-cap"></i> Graduate 4th Years
          </button>
          <button class="btn btn-warning btn-sm me-2 d-none" id="bulkChangeSectionBtn" onclick="openBulkChangeSectionModal()" title="Change section for selected students">
            <i class="fas fa-exchange-alt"></i> Change Section (<span id="selectedCount">0</span>)
          </button>
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-3">
          <input type="text" id="searchInput" class="form-control" placeholder="Search by name, ID, or username">
        </div>
        <div class="col-md-3">
          <select class="form-select" id="departmentFilter">
            <option value="">All Departments</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-select" id="sectionFilter">
            <option value="">All Sections</option>
          </select>
        </div>
        <div class="col-md-2">
          <select class="form-select" id="enrollmentTypeFilter">
            <option value="">All Types</option>
            <option value="regular">Regular</option>
            <option value="irregular">Irregular</option>
          </select>
        </div>
         <div class="col-md-1">
            <button class="btn btn-primary w-100" onclick="filterStudents()">Filter</button>
         </div>
      </div>
      
      <div class="table-responsive">
        <table class="table custom-table">
          <thead>
            <tr>
              <th style="width:40px;">
                <input type="checkbox" id="selectAllCheckbox" title="Select all on this page" onchange="toggleSelectAll(this)">
              </th>
              <th class="sortable-col" onclick="sortByColumn('student_id')" id="th-student_id">
                Student ID <i class="fas fa-sort sort-icon"></i>
              </th>
              <th class="sortable-col" onclick="sortByColumn('name')" id="th-name">
                Name <i class="fas fa-sort sort-icon"></i>
              </th>
              <th class="sortable-col" onclick="sortByColumn('department')" id="th-department">
                Department <i class="fas fa-sort sort-icon"></i>
              </th>
              <th class="sortable-col" onclick="sortByColumn('section')" id="th-section">
                <span style="color: white !important;">Section</span> <i class="fas fa-sort sort-icon"></i>
              </th>
              <th class="sortable-col" onclick="sortByColumn('year_level')" id="th-year_level">
                Year Level <i class="fas fa-sort sort-icon"></i>
              </th>
              <th class="sortable-col" onclick="sortByColumn('enrollment_type')" id="th-enrollment_type">
                Type <i class="fas fa-sort sort-icon"></i>
              </th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="studentsTableBody">
            <!-- Data will be loaded dynamically -->
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small" id="paginationInfo">
          Showing 0 to 0 of 0 students
        </div>
        <nav aria-label="Student list pagination">
          <ul class="pagination pagination-sm mb-0" id="paginationControls">
            <!-- Pagination buttons will be loaded here -->
          </ul>
        </nav>
      </div>
    </div>
  </div>
  
  <!-- Manage Subjects Modal -->
  <div class="modal fade" id="manageSubjectsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Manage Subjects for Section: <span id="manageSubjectsSectionName" class="text-primary"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="manageSubjectsSectionId">
          <div class="row">
            <div class="col-md-4 border-end">
              <h6>Add Subject to Section</h6>
              <hr>
              <div class="mb-3">
                <label class="form-label">Subject</label>
                <select class="form-select" id="subjectSelect">
                  <option value="">Choose subject...</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Day</label>
                <select class="form-select" id="subjectDay">
                  <option value="Monday">Monday</option>
                  <option value="Tuesday">Tuesday</option>
                  <option value="Wednesday">Wednesday</option>
                  <option value="Thursday">Thursday</option>
                  <option value="Friday">Friday</option>
                  <option value="Saturday">Saturday</option>
                  <option value="Sunday">Sunday</option>
                </select>
              </div>
              <div class="row mb-3">
                <div class="col">
                  <label class="form-label">Start</label>
                  <input type="time" class="form-control" id="subjectStart">
                </div>
                <div class="col">
                  <label class="form-label">End</label>
                  <input type="time" class="form-control" id="subjectEnd">
                </div>
              </div>
              <button class="btn btn-primary w-100" onclick="addSubjectToSection()">Add</button>
            </div>
            <div class="col-md-8">
              <h6>Current Subjects</h6>
              <hr>
              <table class="table table-sm">
                <thead><tr><th>Code</th><th>Title</th><th>Schedule</th><th>Action</th></tr></thead>
                <tbody id="sectionSubjectsTableBody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- View Student Modal -->
  <div class="modal" id="viewStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Student Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs mb-3" id="studentDetailTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane" type="button" role="tab">Details</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">History</button>
            </li>
          </ul>
          <div class="tab-content" id="studentDetailTabsContent">
            <div class="tab-pane fade show active" id="details-pane" role="tabpanel">
              <div id="viewStudentContent">
                <!-- Student details will be loaded here -->
              </div>
            </div>
            <div class="tab-pane fade" id="history-pane" role="tabpanel">
              <div id="studentHistoryContent">
                <p class="text-center text-muted py-4">Loading history...</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="modalEditBtn">Edit Student</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Edit Student Modal -->
  <div class="modal" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editStudentForm">
            <input type="hidden" id="editStudentId">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Student ID *</label>
                  <input type="text" class="form-control" id="editStudentIdField" required maxlength="20">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" id="editStudentEmail" maxlength="255">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">First Name *</label>
                  <input type="text" class="form-control" id="editFirstName" required maxlength="63" pattern="^[A-Za-z\s\.\-]+$" title="Only letters, spaces, dots, and hyphens are allowed">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="editMiddleName" maxlength="63" pattern="^[A-Za-z\s\.\-]+$" title="Only letters, spaces, dots, and hyphens are allowed">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Last Name *</label>
                  <input type="text" class="form-control" id="editLastName" required maxlength="63" pattern="^[A-Za-z\s\.\-]+$" title="Only letters, spaces, dots, and hyphens are allowed">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Phone</label>
                  <input type="tel" class="form-control" id="editPhone" pattern="^09\d{9}$" maxlength="11" title="Please enter a valid 11-digit mobile number starting with 09">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Birthdate</label>
                  <input type="date" class="form-control" id="editBirthdate">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Gender</label>
                  <select class="form-control" id="editGender">
                    <option value="">Select...</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Department</label>
                  <select class="form-control" id="editDepartment" onchange="updateSectionDropdown('editDepartment', 'editSectionSelect')">
                    <option value="">Select Department...</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Section</label>
                  <select class="form-control" id="editSectionSelect">
                    <option value="">Select Section...</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Year Level *</label>
                  <select class="form-control" id="editYearLevel" required>
                    <option value="">Select...</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <select class="form-control" id="editStatus">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="graduated">Graduated</option>
                    <option value="suspended">Suspended</option>
                    <option value="on_leave">On Leave</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Enrollment Type *</label>
                  <select class="form-control" id="editEnrollmentType" required>
                    <option value="regular">Regular</option>
                    <option value="irregular">Irregular</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea class="form-control" id="editAddress" rows="3"></textarea>
            </div>
            <hr>
            <p class="text-muted small mb-2">Leave password fields blank to keep the current password.</p>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">New Password</label>
                  <input type="password" class="form-control" id="editNewPassword" maxlength="128" placeholder="Enter new password">
                  <div class="form-text">Min 8 characters, must include uppercase, lowercase, and a number.</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Confirm Password</label>
                  <input type="password" class="form-control" id="editConfirmPassword" maxlength="128" placeholder="Confirm new password">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="updateStudent()">Update Student</button>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Change Section Modal -->
  <div class="modal fade" id="changeSectionModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Change Section</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-3" id="changeSectionSubtitle"></p>
          <input type="hidden" id="changeSectionStudentIds">
          <div class="mb-3">
            <label class="form-label fw-semibold">New Section</label>
            <select class="form-select" id="changeSectionSelect">
              <option value="">-- Select Section --</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Year Level (optional)</label>
            <select class="form-select" id="changeSectionYearLevel">
              <option value="">Keep current</option>
              <option value="1">1st Year</option>
              <option value="2">2nd Year</option>
              <option value="3">3rd Year</option>
              <option value="4">4th Year</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" onclick="applyGraduateSelected()" title="Mark selected students as Graduated">
            <i class="fas fa-graduation-cap me-1"></i> Graduate
          </button>
          <button type="button" class="btn btn-warning" onclick="applyChangeSection()">
            <i class="fas fa-exchange-alt me-1"></i> Apply Change
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <script>
    let allStudents = [];
    let allSections = [];
    let allPrograms = [];
    
    // Pagination state
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredStudents = [];
    
    // Sort state
    let currentSortCol = null;
    let currentSortDir = 'asc';

    function normalizeEmailPart(value) {
      return (value || '')
        .toString()
        .trim()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '.')
        .replace(/\.+/g, '.')
        .replace(/^\./, '')
        .replace(/\.$/, '');
    }

    function buildStudentEmailFromName(firstName, middleName, lastName, usedEmails = null) {
      const domain = 'colegiodenaujan.edu.ph';
      const first = normalizeEmailPart(firstName);
      const middle = normalizeEmailPart(middleName);
      const last = normalizeEmailPart(lastName);

      let base = [first, last].filter(Boolean).join('.');
      if (!base) {
        base = 'student';
      }

      const getUnique = (local) => {
        if (!usedEmails) return local;
        const count = usedEmails.get(local) || 0;
        if (count === 0) {
          usedEmails.set(local, 1);
          return local;
        }
        usedEmails.set(local, count + 1);
        return `${local}${count + 1}`;
      };

      let local = base;
      if (usedEmails && usedEmails.has(local) && middle) {
        const alt = [first, middle.charAt(0), last].filter(Boolean).join('.');
        if (alt && !usedEmails.has(alt)) {
          local = alt;
        }
      }

      local = getUnique(local);
      return `${local}@${domain}`;
    }

    function normalizeCellValue(value) {
      if (value === null || value === undefined) return '';
      if (typeof value === 'number') return Number.isFinite(value) ? String(Math.trunc(value)) : '';
      return String(value).trim();
    }

    function normalizeHeaderKey(value) {
      return normalizeCellValue(value)
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
    }

    function parseYearLevel(value, fallbackText = '') {
      const raw = normalizeCellValue(value).toLowerCase();
      const source = raw || normalizeCellValue(fallbackText).toLowerCase();
      if (!source) return null;

      const digitMatch = source.match(/(?:^|[^0-9])([1-5])(?:[^0-9]|$)/);
      if (digitMatch) return parseInt(digitMatch[1], 10);

      const wordMap = new Map([
        ['first', 1],
        ['second', 2],
        ['third', 3],
        ['fourth', 4],
        ['fifth', 5]
      ]);
      for (const [word, num] of wordMap.entries()) {
        if (source.includes(word)) return num;
      }

      const romanMap = new Map([
        [' i ', 1],
        [' ii ', 2],
        [' iii ', 3],
        [' iv ', 4],
        [' v ', 5]
      ]);
      const padded = ` ${source.replace(/[^a-z]/g, ' ')} `;
      for (const [roman, num] of romanMap.entries()) {
        if (padded.includes(roman)) return num;
      }

      return null;
    }

    function parseFullName(name) {
      const raw = normalizeCellValue(name);
      if (!raw) return { first_name: '', middle_name: '', last_name: '' };

      // Case: "Last, First Middle"
      if (raw.includes(',')) {
        const [lastPart, restPart] = raw.split(',').map(s => s.trim());
        const rest = restPart ? restPart.split(/\s+/).filter(Boolean) : [];
        const first = rest[0] || '';
        // Middle name is everything between first and end
        const middle = rest.length > 1 ? rest.slice(1).join(' ') : '';
        return { first_name: first, middle_name: middle, last_name: lastPart || '' };
      }

      // Case: "First Middle Last"
      const parts = raw.split(/\s+/).filter(Boolean);
      if (parts.length === 1) return { first_name: parts[0], middle_name: '', last_name: '' };
      if (parts.length === 2) return { first_name: parts[0], middle_name: '', last_name: parts[1] };
      
      // Assume last part is Last Name, first part is First Name, middle is in between
      return { 
        first_name: parts[0], 
        middle_name: parts.slice(1, -1).join(' '), 
        last_name: parts[parts.length - 1] 
      };
    }

    function detectHeaderRow(rows) {
      for (let r = 0; r < Math.min(rows.length, 30); r++) {
        const row = rows[r] || [];
        const joined = row.map(c => normalizeHeaderKey(c)).join(' | ');
        if (joined.includes('id number') || joined.includes('student id') || joined.includes('id no') || joined.includes('id') || joined.includes('id#')) {
          return r;
        }
        if (joined.includes('first name') && joined.includes('last name')) {
          return r;
        }
      }
      return -1;
    }

    // --- SUBJECT MANAGEMENT FUNCTIONS ---

    function openManageSubjectsModal(sectionId, sectionName, event) {
        if (event) event.stopPropagation();
        if (!sectionId) { alert('Student is not assigned to a section.'); return; }
        document.getElementById('manageSubjectsSectionId').value = sectionId;
        document.getElementById('manageSubjectsSectionName').textContent = sectionName;
        loadSectionSubjects(sectionId);
        loadAllSubjectsForModal();
        const modal = new bootstrap.Modal(document.getElementById('manageSubjectsModal'));
        modal.show();
    }

    function loadSectionSubjects(sectionId) {
        const tbody = document.getElementById('sectionSubjectsTableBody');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
        fetch(`../../../api/schedules/get-by-section.php?section_id=${sectionId}`)
            .then(r => r.json())
            .then(data => {
                tbody.innerHTML = '';
                if (data.success && data.schedules.length > 0) {
                    data.schedules.forEach(s => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td>${s.subject_code}</td><td>${s.subject_title}</td><td>${s.day_of_week} ${s.start_time.substring(0,5)}-${s.end_time.substring(0,5)}</td><td><button class="btn btn-sm btn-danger" onclick="deleteSchedule(${s.id}, ${sectionId})"><i class="fas fa-trash"></i></button></td>`;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No subjects assigned.</td></tr>';
                }
            });
    }

    function loadAllSubjectsForModal() {
        const select = document.getElementById('subjectSelect');
        if (select.options.length > 1) return;
        fetch('../../../api/subjects/get-all.php')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    data.subjects.forEach(sub => {
                        const opt = document.createElement('option');
                        opt.value = sub.id;
                        opt.textContent = `${sub.subject_code} - ${sub.subject_title}`;
                        select.appendChild(opt);
                    });
                }
            });
    }

    function addSubjectToSection() {
        const sectionId = document.getElementById('manageSubjectsSectionId').value;
        const payload = {
            section_id: sectionId,
            subject_id: document.getElementById('subjectSelect').value,
            day_of_week: document.getElementById('subjectDay').value,
            start_time: document.getElementById('subjectStart').value,
            end_time: document.getElementById('subjectEnd').value
        };
        if (!payload.subject_id || !payload.start_time || !payload.end_time) { alert('Please fill in all fields'); return; }
        fetch('../../../api/schedules/create.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(r => r.json()).then(data => { if (data.success) loadSectionSubjects(sectionId); else alert(data.message); });
    }

    function deleteSchedule(id, sectionId) {
        if (!confirm('Remove this subject?')) return;
        fetch('../../../api/schedules/delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        }).then(r => r.json()).then(data => { if (data.success) loadSectionSubjects(sectionId); });
    }

    function buildColumnIndexMap(headerRow) {
      const map = {};
      headerRow.forEach((cell, idx) => {
        const key = normalizeCellValue(cell).toLowerCase();
        const normKey = normalizeHeaderKey(cell);
        if (key) map[key] = idx;
        if (normKey && !Object.prototype.hasOwnProperty.call(map, normKey)) {
          map[normKey] = idx;
        }
      });
      return map;
    }

    function findCol(map, candidates) {
      for (const c of candidates) {
        const key = c.toLowerCase();
        const normKey = normalizeHeaderKey(c);
        if (Object.prototype.hasOwnProperty.call(map, key)) return map[key];
        if (normKey && Object.prototype.hasOwnProperty.call(map, normKey)) return map[normKey];
      }
      return -1;
    }

    function extractStudentsFromWorkbook(workbook) {
      const studentsById = new Map();
      const usedEmails = new Map();

      const programCodes = Array.isArray(allPrograms) ? allPrograms.map(p => (p.code || '').toString().trim().toUpperCase()).filter(Boolean) : [];
      const programCodeSet = new Set(programCodes);
      const programTitleToCode = new Map(
        (Array.isArray(allPrograms) ? allPrograms : [])
          .map(p => [((p.title || p.short_title || '').toString().trim().toLowerCase()), (p.code || '').toString().trim().toUpperCase()])
          .filter(([k, v]) => k && v)
      );

      const normalizeSectionKey = (value) => {
        return normalizeCellValue(value).toLowerCase().replace(/[^a-z0-9]+/g, '');
      };

      const sections = Array.isArray(allSections) ? allSections : [];
      const sectionByNameKey = new Map();
      const sectionByCodeKey = new Map();
      sections.forEach(s => {
        const nameKey = normalizeSectionKey(s.section_name);
        const codeKey = normalizeSectionKey(s.section_code);
        if (nameKey) sectionByNameKey.set(nameKey, s);
        if (codeKey) sectionByCodeKey.set(codeKey, s);
      });

      const findSectionFromText = (value, sheetName = '') => {
        const key = normalizeSectionKey(value);
        if (key) {
          if (sectionByNameKey.has(key)) return sectionByNameKey.get(key);
          if (sectionByCodeKey.has(key)) return sectionByCodeKey.get(key);
        }

        const sheetKey = normalizeSectionKey(sheetName);
        if (sheetKey) {
          let best = null;
          let bestLen = 0;
          for (const s of sections) {
            const nKey = normalizeSectionKey(s.section_name);
            const cKey = normalizeSectionKey(s.section_code);
            const candidates = [nKey, cKey].filter(Boolean);
            for (const ck of candidates) {
              if (ck.length < 3) continue;
              if (sheetKey.includes(ck) || ck.includes(sheetKey)) {
                if (ck.length > bestLen) {
                  best = s;
                  bestLen = ck.length;
                }
              }
            }
          }
          if (best) return best;
        }

        return null;
      };

      workbook.SheetNames.forEach(sheetName => {
        const sheet = workbook.Sheets[sheetName];
        if (!sheet) return;

        const rows = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: '' });
        const headerIdx = detectHeaderRow(rows);
        if (headerIdx === -1) return;

        const header = rows[headerIdx] || [];
        const colMap = buildColumnIndexMap(header);

        const idCol = findCol(colMap, ['id number', 'student id', 'id no', 'id#', 'id']);
        const firstCol = findCol(colMap, ['first name', 'firstname', 'given name']);
        const middleCol = findCol(colMap, ['middle name', 'middlename', 'mi']);
        const lastCol = findCol(colMap, ['last name', 'lastname', 'surname']);
        const genderCol = findCol(colMap, ['gender', 'sex', 'gen']);
        const nameCol = findCol(colMap, ['name', 'full name', 'student name']);
        const deptCol = findCol(colMap, ['department', 'program', 'course', 'dept']);
        const sectionCol = findCol(colMap, ['section', 'section name', 'section_code', 'section code', 'sec']);
        const yearCol = findCol(colMap, ['year level', 'yearlevel', 'year', 'yr', 'yr level', 'yrlevel', 'yl']);
        const typeCol = findCol(colMap, ['enrollment type', 'type', 'regular/irregular', 'regular - irregular', 'reg/irreg', 'reg-irreg', 'status', 'remarks']);

        const sheetSection = findSectionFromText('', sheetName);

        for (let r = headerIdx + 1; r < rows.length; r++) {
          const row = rows[r] || [];
          let studentId = idCol !== -1 ? normalizeCellValue(row[idCol]) : '';
          
          // If student ID is missing, we still want to import if name is present
          // We'll generate a placeholder ID that the API can handle
          if (!studentId) {
            // Check if name is present first
            const first = firstCol !== -1 ? normalizeCellValue(row[firstCol]) : '';
            const last = lastCol !== -1 ? normalizeCellValue(row[lastCol]) : '';
            if (first && last) {
                studentId = 'NEW-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
            } else {
                continue;
            }
          }

          let first = firstCol !== -1 ? normalizeCellValue(row[firstCol]) : '';
          let middle = middleCol !== -1 ? normalizeCellValue(row[middleCol]) : '';
          let last = lastCol !== -1 ? normalizeCellValue(row[lastCol]) : '';

          if (!first || !last) {
            const full = nameCol !== -1 ? row[nameCol] : '';
            const parsed = parseFullName(full);
            if (!first) first = parsed.first_name;
            if (!middle) middle = parsed.middle_name;
            if (!last) last = parsed.last_name;
          }

          if (!first || !last) continue;

          let enrollmentType = 'regular';
          const sheetLower = sheetName.toLowerCase();
          if (sheetLower.includes('irregular') || sheetLower.includes('irreg')) enrollmentType = 'irregular';
          
          if (typeCol !== -1) {
            const t = normalizeCellValue(row[typeCol]).toLowerCase();
            if (t.includes('irreg') || t.includes('irrege')) enrollmentType = 'irregular';
            else if (t.includes('reg') && !t.includes('irreg')) enrollmentType = 'regular';
          }

          let department = null;
          if (deptCol !== -1) {
            const rawDept = normalizeCellValue(row[deptCol]);
            const deptUpper = rawDept.toUpperCase();
            const deptLower = rawDept.toLowerCase();
            if (programCodeSet.has(deptUpper)) {
              department = deptUpper;
            } else if (programTitleToCode.has(deptLower)) {
              department = programTitleToCode.get(deptLower);
            }
          }

          let sectionId = null;
          let sectionDepartment = null;

          if (sectionCol !== -1) {
            const rawSection = normalizeCellValue(row[sectionCol]);
            const matched = findSectionFromText(rawSection, sheetName);
            if (matched && matched.id) {
              sectionId = matched.id;
              sectionDepartment = matched.department_code || null;
            }
          } else if (sheetSection && sheetSection.id) {
            sectionId = sheetSection.id;
            sectionDepartment = sheetSection.department_code || null;
          }

          if (!department && sectionDepartment && programCodeSet.has(sectionDepartment.toUpperCase())) {
            department = sectionDepartment.toUpperCase();
          }

          let yearLevel = null;
          if (yearCol !== -1) yearLevel = parseYearLevel(row[yearCol], sheetName);
          if (!yearLevel) yearLevel = parseYearLevel('', sheetName);

          let gender = '';
          if (genderCol !== -1) {
            const g = normalizeCellValue(row[genderCol]).toLowerCase();
            if (g.startsWith('m')) gender = 'male';
            else if (g.startsWith('f')) gender = 'female';
            else if (g.startsWith('o')) gender = 'other';
          }

          const email = buildStudentEmailFromName(first, middle, last, usedEmails);

          if (!studentsById.has(studentId)) {
            studentsById.set(studentId, {
              student_id: studentId,
              first_name: first,
              middle_name: middle || null,
              last_name: last,
              email,
              gender: gender || null,
              department,
              section_id: sectionId,
              year_level: yearLevel,
              enrollment_type: enrollmentType,
              status: 'active'
            });
          }
        }
      });

      return Array.from(studentsById.values());
    }

    // Sort By Column
    function sortByColumn(col) {
      if (currentSortCol === col) {
        currentSortDir = currentSortDir === 'asc' ? 'desc' : 'asc';
      } else {
        currentSortCol = col;
        currentSortDir = 'asc';
      }
      
      // Update UI
      document.querySelectorAll('.sortable-col').forEach(th => {
        th.classList.remove('active-sort');
        const icon = th.querySelector('.sort-icon');
        if (icon) icon.className = 'fas fa-sort sort-icon';
      });
      
      const activeTh = document.getElementById(`th-${col}`);
      if (activeTh) {
        activeTh.classList.add('active-sort');
        const icon = activeTh.querySelector('.sort-icon');
        if (icon) {
          icon.className = `fas fa-sort-${currentSortDir === 'asc' ? 'up' : 'down'} sort-icon`;
        }
      }
      
      filterStudents();
    }

    // Load Students Data
    function loadStudents() {
      console.log('Loading students and filters...');
      // Load programs first
      fetch('../../../api/programs/get-all.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allPrograms = data.programs;
                populateDepartmentFilters();
            }
        })
        .then(() => {
            // Then load sections
            return fetch('../../../api/sections/get-all.php');
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allSections = data.sections;
                populateSectionFilter();
            }
        })
        .then(() => {
            // Check for URL parameters from Admission Finalization
            const urlParams = new URLSearchParams(window.location.search);
            const deptParam = urlParams.get('department');
            const sectionParam = urlParams.get('section');
            const yearParam = urlParams.get('year');
            
            console.log('URL Parameters:', { deptParam, sectionParam, yearParam });
            
            if (deptParam) {
                const deptFilter = document.getElementById('departmentFilter');
                if (deptFilter) {
                    deptFilter.value = deptParam;
                    // Trigger section population manually
                    populateSectionFilter();
                    
                    if (sectionParam) {
                        const sectionFilter = document.getElementById('sectionFilter');
                        if (sectionFilter) {
                            // Find the option by text content since value might be ID
                            for (let i = 0; i < sectionFilter.options.length; i++) {
                                if (sectionFilter.options[i].textContent === sectionParam) {
                                    sectionFilter.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            
            // Then load students
            return fetch('../../../api/students/get-all.php');
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            allStudents = data.students;
            console.log('Total students loaded:', allStudents.length);
            
            // Apply initial filters if any
            filterStudents(); 
            sortByColumn('student_id'); // Initial sort
          } else {
            console.error('Error loading students:', data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    // Populate Department Filters
    function populateDepartmentFilters() {
        const filters = ['departmentFilter', 'department', 'editDepartment'];
        
        filters.forEach(filterId => {
            const select = document.getElementById(filterId);
            if (!select) return;
            
            const firstOption = select.options[0];
            const currentValue = select.value;
            select.innerHTML = '';
            select.appendChild(firstOption);
            
            allPrograms.forEach(program => {
                 const option = document.createElement('option');
                 option.value = program.code; 
                 // Use Title as visible text if available, fallback to Code
                 // For editDepartment, we use the title to match what's in the database view
                 option.textContent = program.title || program.code; 
                 select.appendChild(option);
             });
             
             // Restore value if possible
             select.value = currentValue;
        });
    }
    
    // Populate Section Filter (Main Filter)
    function populateSectionFilter() {
      const departmentFilter = document.getElementById('departmentFilter').value;
      const sectionFilter = document.getElementById('sectionFilter');
      const currentSection = sectionFilter.value;
      
      // Filter sections based on department
      let filteredSections = [];
      if (departmentFilter) {
          const codeKey = departmentFilter.toString().trim().toLowerCase();
          filteredSections = allSections.filter(sec => {
              const deptCode = (sec.department_code || '').toString().trim().toLowerCase();
              const secName = (sec.section_name || '').toString().trim().toLowerCase();
              // Link by department_code OR by section name prefix (e.g., BTVTED-CHS1 starts with BTVTED-CHS)
              return deptCode === codeKey || secName.startsWith(codeKey);
          });
      } else {
          // If no department is selected, we show all sections as a default or empty?
          // The user said "follows the department first", so if no dept, maybe empty or all?
          // Let's keep it empty until a department is chosen for better UX.
          filteredSections = [];
      }
      
      // Sort sections by name
      filteredSections.sort((a, b) => a.section_name.localeCompare(b.section_name));
      
      // Clear and repopulate
      sectionFilter.innerHTML = '<option value="">All Sections</option>';
      filteredSections.forEach(sec => {
        const option = document.createElement('option');
        option.value = sec.section_name; // Filter uses name currently
        option.textContent = sec.section_name;
        sectionFilter.appendChild(option);
      });
      
      // Restore selection if valid
      const exists = filteredSections.some(sec => sec.section_name === currentSection);
      if (exists) {
        sectionFilter.value = currentSection;
      } else {
        sectionFilter.value = "";
      }
    }

    // Update Modal Section Dropdown
    function updateSectionDropdown(deptSelectId, sectionSelectId, selectedSectionId = null) {
        const deptSelect = document.getElementById(deptSelectId);
        const sectionSelect = document.getElementById(sectionSelectId);
        const department = deptSelect.value;
        
        // Filter sections
        let filteredSections = [];
        if (department) {
            const deptCodeKey = department.toString().trim().toLowerCase();
            filteredSections = allSections.filter(sec => {
                const secDept = (sec.department_code || '').toString().trim().toLowerCase();
                const secName = (sec.section_name || '').toString().trim().toLowerCase();
                // Link by department_code OR by section name prefix (e.g., BTVTED-CHS1 starts with BTVTED-CHS)
                return secDept === deptCodeKey || secName.startsWith(deptCodeKey);
            });
        } else {
            // If no department selected, show no sections or all? Usually none until department selected
            filteredSections = []; 
        }
        
        filteredSections.sort((a, b) => a.section_name.localeCompare(b.section_name));
        
        sectionSelect.innerHTML = '<option value="">Select Section...</option>';
        filteredSections.forEach(sec => {
            const option = document.createElement('option');
            option.value = sec.id; // Use ID for value in forms
            option.textContent = sec.section_name;
            sectionSelect.appendChild(option);
        });
        
        if (selectedSectionId) {
            sectionSelect.value = selectedSectionId;
        }
    }

    // Filter Students
    function filterStudents() {
      const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
      const departmentFilter = document.getElementById('departmentFilter').value;
      const sectionFilter = document.getElementById('sectionFilter').value;
      const enrollmentTypeFilter = document.getElementById('enrollmentTypeFilter').value;
      
      filteredStudents = allStudents.filter(student => {
        // Search by ID, First Name, Last Name, Middle Name, and Email (Username)
        // Highly responsive: filters even with 1 character
        const matchesSearch = !searchTerm ? true : (
          (student.student_id && student.student_id.toLowerCase().includes(searchTerm)) ||
          (student.first_name && student.first_name.toLowerCase().includes(searchTerm)) ||
          (student.last_name && student.last_name.toLowerCase().includes(searchTerm)) ||
          (student.middle_name && student.middle_name.toLowerCase().includes(searchTerm)) ||
          (student.email && student.email.toLowerCase().includes(searchTerm))
        );
        
        // Smarter department matching: check student.department OR student.section_department_code OR section name prefix
        const studentDept = (student.department || '').toString().trim().toLowerCase();
        const sectionDept = (student.section_department_code || '').toString().trim().toLowerCase();
        const sectionName = (student.section_name || '').toString().trim().toLowerCase();
        const filterDept = (departmentFilter || '').toString().trim().toLowerCase();
        
        const matchesDepartment = !departmentFilter || 
                                 studentDept === filterDept || 
                                 sectionDept === filterDept ||
                                 sectionName.startsWith(filterDept);
        
        const sectionNorm = (student.section_name || '').toString().trim().toLowerCase();
        const sectionFilterNorm = (sectionFilter || '').toString().trim().toLowerCase();
        
        const matchesSection = !sectionFilter || sectionNorm === sectionFilterNorm;

        const typeVal = (student.enrollment_type || 'regular').toString().trim().toLowerCase();
        const matchesEnrollmentType = !enrollmentTypeFilter || typeVal === enrollmentTypeFilter;
        
        return matchesSearch && matchesDepartment && matchesSection && matchesEnrollmentType;
      });
      
      // Apply Sorting
      filteredStudents.sort((a, b) => {
        let valA, valB;
        
        switch(currentSortCol) {
          case 'name':
            valA = `${a.last_name}, ${a.first_name}`.toLowerCase();
            valB = `${b.last_name}, ${b.first_name}`.toLowerCase();
            break;
          case 'department':
            valA = (a.section_department_code || a.department || '').toLowerCase();
            valB = (b.section_department_code || b.department || '').toLowerCase();
            break;
          case 'section':
            valA = (a.section_name || '').toLowerCase();
            valB = (b.section_name || '').toLowerCase();
            break;
          case 'year_level':
            valA = parseInt(a.year_level) || 0;
            valB = parseInt(b.year_level) || 0;
            break;
          case 'enrollment_type':
            valA = (a.enrollment_type || 'regular').toLowerCase();
            valB = (b.enrollment_type || 'regular').toLowerCase();
            break;
          default: // student_id
            valA = (a.student_id || '').toLowerCase();
            valB = (b.student_id || '').toLowerCase();
        }
        
        if (valA < valB) return currentSortDir === 'asc' ? -1 : 1;
        if (valA > valB) return currentSortDir === 'asc' ? 1 : -1;
        return 0;
      });
      
      currentPage = 1; // Reset to first page on filter
      displayStudents();
    }
    
    // Add Event Listeners
    document.getElementById('searchInput').addEventListener('input', filterStudents);
    document.getElementById('departmentFilter').addEventListener('change', function() {
      populateSectionFilter();
      filterStudents();
    });
    document.getElementById('sectionFilter').addEventListener('change', filterStudents);
    document.getElementById('enrollmentTypeFilter').addEventListener('change', filterStudents);

    // Display Students in Table
    function displayStudents() {
      const tbody = document.getElementById('studentsTableBody');
      const paginationControls = document.getElementById('paginationControls');
      const paginationInfo = document.getElementById('paginationInfo');
      
      tbody.innerHTML = '';
      
      if (filteredStudents.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No students found</td></tr>';
        paginationControls.innerHTML = '';
        paginationInfo.textContent = 'Showing 0 to 0 of 0 students';
        return;
      }

      // Calculate pagination
      const totalItems = filteredStudents.length;
      const totalPages = Math.ceil(totalItems / itemsPerPage);
      
      // Ensure currentPage is within bounds
      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;
      
      const startIndex = (currentPage - 1) * itemsPerPage;
      const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
      const paginatedItems = filteredStudents.slice(startIndex, endIndex);
      
      paginatedItems.forEach(student => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        
        // Format year level
        const yearLevel = getYearLevelText(student.year_level);
        
        // Find program title for display
        // Priority: section name prefix match > section_department_code > department
        let deptCode = student.section_department_code || student.department;
        const sectionName = (student.section_name || '').toString().trim().toUpperCase();
        if (sectionName) {
            const specificProgram = allPrograms.find(p => sectionName.startsWith(p.code.toUpperCase()));
            if (specificProgram) {
                deptCode = specificProgram.code;
            }
        }
        
        const program = allPrograms.find(p => p.code === deptCode);
        const departmentDisplay = program ? (program.short_title || program.title) : (deptCode || 'N/A');
        
        const enrollmentType = (student.enrollment_type || 'regular').toString().trim().toLowerCase();
        const typeLabel = enrollmentType.charAt(0).toUpperCase() + enrollmentType.slice(1);

        row.innerHTML = `
          <td><input type="checkbox" class="student-checkbox" value="${student.id}" onchange="onStudentCheckboxChange()"></td>
          <td>${student.student_id}</td>
          <td>${student.first_name} ${student.middle_name ? student.middle_name + ' ' : ''}${student.last_name}</td>
          <td>${departmentDisplay}</td>
          <td>${student.section_name || 'N/A'}</td>
          <td>${yearLevel}</td>
          <td><span class="badge ${enrollmentType === 'regular' ? 'bg-info' : 'bg-warning'} text-dark">${typeLabel}</span></td>
          <td>
            <button class="btn btn-sm btn-outline-secondary action-btn me-1" onclick="openChangeSectionModal(${student.id}, '${(student.section_name||'').replace(/'/g,"\\'")}', '${(student.first_name+' '+student.last_name).replace(/'/g,"\\'")}', ${student.section_id || 'null'})" title="Change Section">
              <i class="fas fa-exchange-alt"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary action-btn me-1" onclick="editStudent(${student.id})" title="Edit Student">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-info action-btn" onclick="viewStudent(${student.id})" title="View Details">
              <i class="fas fa-eye"></i>
            </button>
          </td>
        `;

        // Add click listener to the whole row
        row.addEventListener('click', function(e) {
            if (e.target.closest('.action-btn')) return;
            viewStudent(student.id);
        });
        
        tbody.appendChild(row);
      });

      // Update Pagination Info
      paginationInfo.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} students`;

      // Update Pagination Controls
      updatePaginationControls(totalPages);
    }

    function updatePaginationControls(totalPages) {
        const paginationControls = document.getElementById('paginationControls');
        paginationControls.innerHTML = '';

        if (totalPages <= 1) return;

        // Previous Button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})">Previous</a>`;
        paginationControls.appendChild(prevLi);

        // Page Numbers (Simplified: shows current, one before, one after, and first/last)
        const range = 1;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - range && i <= currentPage + range)) {
                const li = document.createElement('li');
                li.className = `page-item ${currentPage === i ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="changePage(${i})">${i}</a>`;
                paginationControls.appendChild(li);
            } else if (i === currentPage - range - 1 || i === currentPage + range + 1) {
                const li = document.createElement('li');
                li.className = 'page-item disabled';
                li.innerHTML = '<span class="page-link">...</span>';
                paginationControls.appendChild(li);
            }
        }

        // Next Button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})">Next</a>`;
        paginationControls.appendChild(nextLi);
    }

    function changePage(page) {
        currentPage = page;
        displayStudents();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Get Status Badge
    function getStatusBadge(status) {
      const badges = {
        'active': '<span class="badge-status active">Active</span>',
        'inactive': '<span class="badge-status inactive">Inactive</span>',
        'graduated': '<span class="badge-status graduated">Graduated</span>',
        'suspended': '<span class="badge-status suspended">Suspended</span>',
        'on_leave': '<span class="badge-status on-leave">On Leave</span>'
      };
      return badges[status] || '<span class="badge-status inactive">Unknown</span>';
    }
    
    // Get Year Level Text
    function getYearLevelText(yearLevel) {
      const levels = {
        1: '1st Year',
        2: '2nd Year',
        3: '3rd Year',
        4: '4th Year'
      };
      return levels[yearLevel] || `${yearLevel}th Year`;
    }
    
    // View Student Details
    function viewStudent(id) {
      console.log('Viewing student:', id);
      
      // Reset tabs
      const detailsTab = document.getElementById('details-tab');
      if (detailsTab) {
          const tab = new bootstrap.Tab(detailsTab);
          tab.show();
      }
      
      fetch(`../../../api/students/get-single.php?id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
          if (data.success) {
            const student = data.student;
            const enrollmentType = (student.enrollment_type || 'regular').toString().trim().toLowerCase();
            const enrollmentTypeLabel = enrollmentType.charAt(0).toUpperCase() + enrollmentType.slice(1);
            
            // Find program title for display
             let deptCode = student.section_department_code || student.department;
             const sectionName = (student.section_name || '').toString().trim().toUpperCase();
             if (sectionName) {
                 const specificProgram = allPrograms.find(p => sectionName.startsWith(p.code.toUpperCase()));
                 if (specificProgram) {
                     deptCode = specificProgram.code;
                 }
             }
             
             const program = allPrograms.find(p => p.code === deptCode);
             const departmentDisplay = program ? (program.short_title || program.title) : (deptCode || 'N/A');
            
            const content = `
              <div class="row">
                <div class="col-md-4 text-center border-end">
                  <div class="mb-3">
                    <div class="avatar-placeholder rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                      <i class="fas fa-user fa-4x text-muted"></i>
                    </div>
                  </div>
                  <h5 class="mb-1">${student.first_name} ${student.last_name}</h5>
                  <p class="text-muted small mb-3">${student.student_id}</p>
                  <div class="mb-2">${getStatusBadge(student.status)}</div>
                  <div><span class="badge ${enrollmentType === 'regular' ? 'bg-info' : 'bg-warning'} text-dark">${enrollmentTypeLabel}</span></div>
                </div>
                <div class="col-md-8 px-4">
                  <div class="row mb-2">
                    <div class="col-sm-4 text-muted small">Full Name</div>
                    <div class="col-sm-8 fw-bold">${student.first_name} ${student.middle_name ? student.middle_name + ' ' : ''}${student.last_name}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-4 text-muted small">Email</div>
                    <div class="col-sm-8 text-break">${student.email}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-4 text-muted small">Department</div>
                    <div class="col-sm-8">${departmentDisplay}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-4 text-muted small">Section</div>
                    <div class="col-sm-8">${student.section_name || 'N/A'}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-4 text-muted small">Year Level</div>
                    <div class="col-sm-8">${getYearLevelText(student.year_level)}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-4 text-muted small">Contact</div>
                    <div class="col-sm-8">${student.phone || 'N/A'}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-4 text-muted small">Birthdate</div>
                    <div class="col-sm-8">${student.birth_date || 'N/A'}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-4 text-muted small">Gender</div>
                    <div class="col-sm-8 text-capitalize">${student.gender || 'N/A'}</div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-4 text-muted small">Address</div>
                    <div class="col-sm-8">${student.address || 'N/A'}</div>
                  </div>
                  <div class="row mb-0">
                    <div class="col-sm-4 text-muted small">Last Updated</div>
                    <div class="col-sm-8 small">${new Date(student.updated_at).toLocaleString()}</div>
                  </div>
                </div>
              </div>
            `;
            document.getElementById('viewStudentContent').innerHTML = content;
            
            // Set up edit button in modal
            const modalEditBtn = document.getElementById('modalEditBtn');
            if (modalEditBtn) {
              modalEditBtn.onclick = function() {
                const modal = bootstrap.Modal.getInstance(document.getElementById('viewStudentModal'));
                if (modal) modal.hide();
                editStudent(id);
              };
            }

            // Load history
            loadStudentHistory(id);
            
            showModal('viewStudentModal');
          } else {
            alert('Error loading student: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error loading student details. Please check console for details.');
        });
    }

    function loadStudentHistory(studentId) {
      const container = document.getElementById('studentHistoryContent');
      container.innerHTML = '<p class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading history...</p>';
      
      fetch(`../../../api/students/get-history.php?student_id=${studentId}`)
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            if (!data.history || data.history.length === 0) {
              container.innerHTML = '<p class="text-center text-muted py-5">No history recorded for this student.</p>';
              return;
            }
            
            let html = '<div class="timeline p-2">';
            data.history.forEach(item => {
              const date = new Date(item.changed_at).toLocaleString();
              
              const fieldLabels = {
                'department': 'Department',
                'section_id': 'Section',
                'yearlevel': 'Year Level',
                'status': 'Status',
                'enrollment_type': 'Enrollment Type'
              };
              
              let oldVal = item.old_value;
              let newVal = item.new_value;
              
              if (item.field_name === 'section_id') {
                oldVal = item.old_section || 'N/A';
                newVal = item.new_section || 'N/A';
              } else if (item.field_name === 'department') {
                oldVal = item.old_dept || item.old_value || 'N/A';
                newVal = item.new_dept || item.new_value || 'N/A';
              } else if (item.field_name === 'yearlevel') {
                oldVal = oldVal ? getYearLevelText(oldVal) : 'N/A';
                newVal = newVal ? getYearLevelText(newVal) : 'N/A';
              }
              
              const label = fieldLabels[item.field_name] || item.field_name;
              
              html += `
                <div class="card mb-2 border-0 bg-light shadow-sm">
                  <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="badge bg-secondary small">${label} Changed</span>
                      <small class="text-muted" style="font-size: 0.7rem;">${date}</small>
                    </div>
                    <div class="small d-flex align-items-center">
                      <span class="text-danger text-decoration-line-through">${oldVal}</span>
                      <i class="fas fa-arrow-right mx-2 text-muted" style="font-size: 0.7rem;"></i>
                      <span class="text-success fw-bold">${newVal}</span>
                    </div>
                    <div class="mt-1 text-muted" style="font-size: 0.7rem;">
                      <i class="fas fa-user-edit me-1"></i> By: ${item.changed_by_name || 'System'}
                    </div>
                  </div>
                </div>
              `;
            });
            html += '</div>';
            container.innerHTML = html;
          } else {
            container.innerHTML = `<div class="alert alert-danger m-3 small">${data.message}</div>`;
          }
        })
        .catch(err => {
          container.innerHTML = '<div class="alert alert-danger m-3 small">Failed to load history</div>';
        });
    }
    
    // Edit Student
    async function editStudent(id) {
      console.log('Editing student:', id);
      
      try {
        // 1. Ensure core data (programs/sections) is loaded before proceeding
        if (allPrograms.length === 0 || allSections.length === 0) {
            console.log('Core data missing, fetching before edit...');
            const [progRes, secRes] = await Promise.all([
                fetch('../../../api/programs/get-all.php').then(r => r.json()),
                fetch('../../../api/sections/get-all.php').then(r => r.json())
            ]);
            if (progRes.success) allPrograms = progRes.programs;
            if (secRes.success) allSections = secRes.sections;
            populateDepartmentFilters();
        }

        const response = await fetch(`../../../api/students/get-single.php?id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const student = data.student;
            
            // Populate form fields
            document.getElementById('editStudentId').value = student.id;
            document.getElementById('editStudentIdField').value = student.student_id;
            document.getElementById('editStudentEmail').value = student.email;
            document.getElementById('editFirstName').value = student.first_name;
            document.getElementById('editMiddleName').value = student.middle_name || '';
            document.getElementById('editLastName').value = student.last_name;
            document.getElementById('editPhone').value = student.phone || '';
            document.getElementById('editBirthdate').value = student.birth_date || '';
            document.getElementById('editGender').value = student.gender || '';
            
            // Handle department and section assignment
            const studentSectionId = student.section_id;

            const deptSelect = document.getElementById('editDepartment');
            const sectionSelect = document.getElementById('editSectionSelect');

            if (deptSelect) {
                populateDepartmentFilters();

                // Derive department from section name prefix (e.g. "BSIS-3" → "BSIS")
                // Section name format is always CODE-number, so grab everything before the last hyphen+digit block
                let derivedDept = '';
                const secObj = allSections.find(s => s.id == studentSectionId);
                if (secObj) {
                    // Strip trailing -<number> to get the program code (e.g. BSIS-3 → BSIS, BTVTED-CHS4 → BTVTED-CHS)
                    derivedDept = secObj.section_name.replace(/-?\d+$/, '').trim();
                }

                // Try to set the dropdown by the derived code
                if (derivedDept) {
                    deptSelect.value = derivedDept;

                    // If no exact match, scan options for a code that the section name starts with
                    if (!deptSelect.value) {
                        for (let i = 0; i < deptSelect.options.length; i++) {
                            const optCode = deptSelect.options[i].value.toUpperCase();
                            if (optCode && secObj && secObj.section_name.toUpperCase().startsWith(optCode)) {
                                deptSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                }

                // Populate sections for the selected department, pre-selecting the student's section
                updateSectionDropdown('editDepartment', 'editSectionSelect', studentSectionId);

                // Final safety: force section if still not selected
                if (studentSectionId && (!sectionSelect.value || sectionSelect.value != studentSectionId)) {
                    if (secObj) {
                        const opt = document.createElement('option');
                        opt.value = secObj.id;
                        opt.textContent = secObj.section_name;
                        sectionSelect.appendChild(opt);
                        sectionSelect.value = secObj.id;
                    }
                }
            }
            
            document.getElementById('editYearLevel').value = student.year_level || '';
            document.getElementById('editEnrollmentType').value = student.enrollment_type || 'regular';
            document.getElementById('editStatus').value = student.status;
            document.getElementById('editAddress').value = student.address || '';
            
            showModal('editStudentModal');
          } else {
            alert('Error loading student: ' + data.message);
          }
      } catch (error) {
          console.error('Error:', error);
          alert('Error loading student details. Please check console.');
      }
    }
    
    // Update Student
    function updateStudent() {
      const studentData = {
        id: document.getElementById('editStudentId').value,
        student_id: document.getElementById('editStudentIdField').value.trim(),
        first_name: document.getElementById('editFirstName').value.trim(),
        middle_name: document.getElementById('editMiddleName').value.trim(),
        last_name: document.getElementById('editLastName').value.trim(),
        email: document.getElementById('editStudentEmail').value.trim(),
        phone: document.getElementById('editPhone').value.trim(),
        birth_date: document.getElementById('editBirthdate').value,
        gender: document.getElementById('editGender').value,
        address: document.getElementById('editAddress').value.trim(),
        department: document.getElementById('editDepartment').value || null,
        section_id: document.getElementById('editSectionSelect').value || null,
        year_level: parseInt(document.getElementById('editYearLevel').value),
        enrollment_type: document.getElementById('editEnrollmentType').value,
        status: document.getElementById('editStatus').value
      };

      if (!studentData.email) {
        studentData.email = buildStudentEmailFromName(studentData.first_name, studentData.middle_name, studentData.last_name);
        document.getElementById('editStudentEmail').value = studentData.email;
      }
      
      // Validate required fields
      if (!studentData.student_id || !studentData.first_name || !studentData.last_name || 
          !studentData.email || !studentData.year_level) {
        alert('Please fill in all required fields');
        return;
      }

      // Password validation (only if provided)
      const newPassword = document.getElementById('editNewPassword').value;
      const confirmPassword = document.getElementById('editConfirmPassword').value;

      if (newPassword || confirmPassword) {
        if (newPassword.length < 8) {
          alert('Password must be at least 8 characters.');
          return;
        }
        if (!/[A-Z]/.test(newPassword)) {
          alert('Password must contain at least one uppercase letter.');
          return;
        }
        if (!/[a-z]/.test(newPassword)) {
          alert('Password must contain at least one lowercase letter.');
          return;
        }
        if (!/[0-9]/.test(newPassword)) {
          alert('Password must contain at least one number.');
          return;
        }
        if (newPassword !== confirmPassword) {
          alert('Passwords do not match.');
          return;
        }
        studentData.new_password = newPassword;
      }
      
      // Send to API
      fetch('../../../api/students/update.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(studentData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('editStudentModal'));
          modal.hide();

          // Clear password fields
          document.getElementById('editNewPassword').value = '';
          document.getElementById('editConfirmPassword').value = '';
          
          // Reload students
          loadStudents();
          
          // Show success message
          alert('Student updated successfully!');
        } else {
          alert('Error updating student: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error updating student. Please try again.');
      });
    }
    
    // Delete Student
    function deleteStudent(id) {
      if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
        fetch('../../../api/students/delete.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Reload students
            loadStudents();
            
            // Show success message
            alert('Student deleted successfully!');
          } else {
            alert('Error deleting student: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error deleting student. Please try again.');
        });
      }
    }

    function deleteAllStudents() {
      if (confirm('CRITICAL ACTION: Are you sure you want to delete ALL students and their portal accounts? This will wipe the student list clean for a fresh import.')) {
        if (confirm('Please confirm one more time. This will delete all records permanently (though they will be archived).')) {
          const loadingOverlay = document.createElement('div');
          loadingOverlay.style = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; justify-content: center; align-items: center; color: white;';
          loadingOverlay.innerHTML = '<div><i class="fas fa-spinner fa-spin fa-3x mb-3"></i><br>Deleting all records...</div>';
          document.body.appendChild(loadingOverlay);

          fetch('../../../api/students/delete-all.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
          })
          .then(r => r.json())
          .then(data => {
            document.body.removeChild(loadingOverlay);
            if (data.success) {
              alert(data.message);
              loadStudents();
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch(err => {
            document.body.removeChild(loadingOverlay);
            console.error(err);
            alert('Bulk deletion failed.');
          });
        }
      }
    }
    
    // Import Students (Upsert)
    async function importStudents() {
      if (typeof XLSX === 'undefined') {
        alert('Excel library not loaded.');
        return;
      }

      const input = document.createElement('input');
      input.type = 'file';
      input.accept = '.csv,.xlsx,.xls';
      input.onchange = async function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const loadingOverlay = document.createElement('div');
        loadingOverlay.style = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; justify-content: center; align-items: center; color: white;';
        loadingOverlay.innerHTML = '<div><i class="fas fa-spinner fa-spin fa-3x mb-3"></i><br>Processing students...</div>';
        document.body.appendChild(loadingOverlay);

        try {
          // Pre-load programs and sections if needed
          if (!Array.isArray(allPrograms) || allPrograms.length === 0) {
            const pRes = await fetch(`../../../api/programs/get-all.php?t=${Date.now()}`);
            const pData = await pRes.json();
            if (pData.success) allPrograms = pData.programs || [];
          }
          if (!Array.isArray(allSections) || allSections.length === 0) {
            const sRes = await fetch(`../../../api/sections/get-all.php?t=${Date.now()}`);
            const sData = await sRes.json();
            if (sData.success) allSections = sData.sections || [];
          }

          const buf = await file.arrayBuffer();
          const workbook = XLSX.read(buf, { type: 'array' });
          const students = extractStudentsFromWorkbook(workbook);

          if (!students.length) {
            alert('No students found in the file.');
            document.body.removeChild(loadingOverlay);
            return;
          }

          const resp = await fetch('../../../api/students/upsert.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ students })
          });
          const out = await resp.json();
          
          document.body.removeChild(loadingOverlay);

          if (out.success) {
            alert(`Process complete!\n\nInserted: ${out.inserted}\nUpdated: ${out.updated}`);
            loadStudents();
          } else {
            alert('Import failed: ' + (out.message || 'Unknown error'));
          }
        } catch (error) {
          console.error('Import error:', error);
          document.body.removeChild(loadingOverlay);
          alert('An error occurred during import. Please check console for details.');
        }
      };
      input.click();
    }
    
    // Export Students
    function exportStudents() {
      if (filteredStudents.length === 0) {
        alert('No students to export.');
        return;
      }

      if (!confirm(`Are you sure you want to export ${filteredStudents.length} students to CSV?`)) {
        return;
      }

      // Define CSV headers
      const headers = [
        'Student ID',
        'First Name',
        'Middle Name',
        'Last Name',
        'Email',
        'Phone',
        'Birthdate',
        'Gender',
        'Department',
        'Section',
        'Year Level',
        'Enrollment Type',
        'Status',
        'Address'
      ];

      // Prepare CSV data
      const csvRows = [];
      csvRows.push(headers.join(','));

      filteredStudents.forEach(student => {
        // Find department display name
        let deptCode = student.section_department_code || student.department;
        const sectionName = (student.section_name || '').toString().trim().toUpperCase();
        if (sectionName) {
            const specificProgram = allPrograms.find(p => sectionName.startsWith(p.code.toUpperCase()));
            if (specificProgram) {
                deptCode = specificProgram.code;
            }
        }
        const program = allPrograms.find(p => p.code === deptCode);
        const departmentDisplay = program ? (program.short_title || program.title) : (deptCode || 'N/A');

        const row = [
          `"${student.student_id || ''}"`,
          `"${student.first_name || ''}"`,
          `"${student.middle_name || ''}"`,
          `"${student.last_name || ''}"`,
          `"${buildStudentEmailFromName(student.first_name, student.middle_name, student.last_name)}"`,
          `"${student.phone || ''}"`,
          `"${student.birthdate || ''}"`,
          `"${student.gender || ''}"`,
          `"${departmentDisplay}"`,
          `"${student.section_name || 'N/A'}"`,
          `"${getYearLevelText(student.year_level)}"`,
          `"${(student.enrollment_type || 'regular')}"`,
          `"${student.status || ''}"`,
          `"${(student.address || '').replace(/"/g, '""').replace(/\n/g, ' ')}"`
        ];
        csvRows.push(row.join(','));
      });

      // Create blob and download
      const csvString = csvRows.join('\n');
      const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      
      const timestamp = new Date().toISOString().split('T')[0];
      link.setAttribute('href', url);
      link.setAttribute('download', `student_list_${timestamp}.csv`);
      link.style.visibility = 'hidden';
      
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    async function generateStudentListExcel() {
      if (typeof XLSX === 'undefined') {
        alert('Excel library not loaded.');
        return;
      }

      const response = await fetch(`../../../api/students/get-all.php?t=${Date.now()}`);
      const data = await response.json();

      if (!data.success) {
        alert('Failed to load students: ' + (data.message || 'Unknown error'));
        return;
      }

      const students = Array.isArray(data.students) ? data.students : [];
      if (students.length === 0) {
        alert('No students found to export.');
        return;
      }

      const usedEmails = new Map();

      const toRow = (s) => {
        const enrollmentType = (s.enrollment_type || 'regular').toString().trim().toLowerCase();
        const typeLabel = enrollmentType.charAt(0).toUpperCase() + enrollmentType.slice(1);
        const email = buildStudentEmailFromName(s.first_name, s.middle_name, s.last_name, usedEmails);
        return {
          'Student ID': s.student_id || '',
          'First Name': s.first_name || '',
          'Middle Name': s.middle_name || '',
          'Last Name': s.last_name || '',
          'Email': email,
          'Phone': s.phone || '',
          'Birthdate': s.birth_date || '',
          'Gender': s.gender || '',
          'Department': s.department || '',
          'Section': s.section_name || '',
          'Year Level': s.year_level || '',
          'Enrollment Type': typeLabel,
          'Status': s.status || ''
        };
      };

      const regularRows = [];
      const irregularRows = [];

      students.forEach(s => {
        const enrollmentType = (s.enrollment_type || 'regular').toString().trim().toLowerCase();
        if (enrollmentType === 'irregular') {
          irregularRows.push(toRow(s));
        } else {
          regularRows.push(toRow(s));
        }
      });

      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(regularRows), 'Regular');
      XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(irregularRows), 'Irregular');

      const stamp = new Date().toISOString().split('T')[0];
      XLSX.writeFile(wb, `student_list_${stamp}.xlsx`);
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
    
    // Load students when page loads
    document.addEventListener('DOMContentLoaded', function() {
      // Check if Bootstrap is loaded
      if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap 5 is not loaded properly!');
        alert('Warning: Bootstrap library not loaded. Modals may not work.');
      } else {
        console.log('Bootstrap 5 is loaded.');
      }
      
      // Check for URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      const enrollmentType = urlParams.get('type');
      if (enrollmentType) {
          const filterEl = document.getElementById('enrollmentTypeFilter');
          if (filterEl) {
              filterEl.value = enrollmentType;
          }
      }
      
      loadStudents();
    });

    // Helper to safely show modal
    function showModal(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) {
            console.error(`Modal element #${modalId} not found`);
            return;
        }
        
        try {
            // Try getOrCreateInstance first (Bootstrap 5 standard)
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } catch (e) {
            console.error('Error showing modal with getOrCreateInstance:', e);
            try {
                // Fallback to creating new instance
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } catch (e2) {
                console.error('Error showing modal with new bootstrap.Modal:', e2);
                // Last resort fallback: manual show
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.setAttribute('aria-modal', 'true');
                modalEl.setAttribute('role', 'dialog');
                
                // Add backdrop if not exists
                if (!document.querySelector('.modal-backdrop')) {
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
                document.body.classList.add('modal-open');
                
                // Add close handler for manual show
                const closeBtns = modalEl.querySelectorAll('[data-bs-dismiss="modal"]');
                closeBtns.forEach(btn => {
                    btn.onclick = () => {
                        modalEl.classList.remove('show');
                        modalEl.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                    };
                });
            }
        }
    }

    // ---- CHANGE SECTION FEATURE ----

    function populateChangeSectionDropdown() {
      const sel = document.getElementById('changeSectionSelect');
      sel.innerHTML = '<option value="">-- Select Section --</option>';
      (allSections || []).forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = `${s.section_name}${s.section_code ? ' (' + s.section_code + ')' : ''}`;
        sel.appendChild(opt);
      });
    }

    function openChangeSectionModal(studentId, currentSection, studentName, currentSectionId) {
      document.getElementById('changeSectionStudentIds').value = JSON.stringify([studentId]);
      document.getElementById('changeSectionSubtitle').textContent = `Student: ${studentName} — Current section: ${currentSection || 'None'}`;
      document.getElementById('changeSectionYearLevel').value = '';
      populateChangeSectionDropdown();
      const sel = document.getElementById('changeSectionSelect');
      if (currentSectionId) sel.value = currentSectionId;
      new bootstrap.Modal(document.getElementById('changeSectionModal')).show();
    }

    function openBulkChangeSectionModal() {
      const ids = getSelectedStudentIds();
      if (ids.length === 0) { alert('No students selected.'); return; }
      document.getElementById('changeSectionStudentIds').value = JSON.stringify(ids);
      document.getElementById('changeSectionSubtitle').textContent = `Changing section for ${ids.length} selected student(s).`;
      document.getElementById('changeSectionSelect').value = '';
      document.getElementById('changeSectionYearLevel').value = '';
      populateChangeSectionDropdown();
      new bootstrap.Modal(document.getElementById('changeSectionModal')).show();
    }

    async function applyChangeSection() {
      const sectionId = document.getElementById('changeSectionSelect').value;
      if (!sectionId) { alert('Please select a section.'); return; }
      const ids = JSON.parse(document.getElementById('changeSectionStudentIds').value || '[]');
      const yearLevel = document.getElementById('changeSectionYearLevel').value;

      const btn = document.querySelector('#changeSectionModal .btn-warning');
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Applying...';

      let successCount = 0, failCount = 0;
      for (const id of ids) {
        const student = allStudents.find(s => s.id == id);
        if (!student) { failCount++; continue; }
        const payload = {
          id: student.id,
          student_id: student.student_id,
          first_name: student.first_name,
          middle_name: student.middle_name || '',
          last_name: student.last_name,
          email: student.email,
          phone: student.phone || '',
          birth_date: student.birth_date || '',
          gender: student.gender || '',
          address: student.address || '',
          department: student.department || '',
          section_id: sectionId,
          year_level: yearLevel || student.year_level,
          enrollment_type: student.enrollment_type || 'regular',
          status: student.status || 'active',
          remarks: student.remarks || ''
        };
        try {
          const res = await fetch('../../../api/students/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (data.success) successCount++; else failCount++;
        } catch { failCount++; }
      }

      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-exchange-alt me-1"></i> Apply Change';

      bootstrap.Modal.getInstance(document.getElementById('changeSectionModal')).hide();
      alert(`Done! ${successCount} student(s) updated.${failCount > 0 ? ' ' + failCount + ' failed.' : ''}`);
      loadStudents();
      clearSelections();
    }

    async function applyGraduateSelected() {
      const ids = JSON.parse(document.getElementById('changeSectionStudentIds').value || '[]');
      if (ids.length === 0) { alert('No students selected.'); return; }
      if (!confirm(`Mark ${ids.length} student(s) as Graduated?`)) return;

      const btn = document.querySelector('#changeSectionModal .btn-success');
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

      let successCount = 0, failCount = 0;
      for (const id of ids) {
        const student = allStudents.find(s => s.id == id);
        if (!student) { failCount++; continue; }
        const payload = {
          id: student.id,
          student_id: student.student_id,
          first_name: student.first_name,
          middle_name: student.middle_name || '',
          last_name: student.last_name,
          email: student.email,
          phone: student.phone || '',
          birth_date: student.birth_date || '',
          gender: student.gender || '',
          address: student.address || '',
          department: student.department || '',
          section_id: student.section_id || '',
          year_level: student.year_level,
          enrollment_type: student.enrollment_type || 'regular',
          status: 'graduated',
          remarks: student.remarks || ''
        };
        try {
          const res = await fetch('../../../api/students/update.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (data.success) successCount++; else failCount++;
        } catch { failCount++; }
      }

      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-graduation-cap me-1"></i> Graduate';
      bootstrap.Modal.getInstance(document.getElementById('changeSectionModal')).hide();
      alert(`Done! ${successCount} student(s) marked as Graduated.${failCount > 0 ? ' ' + failCount + ' failed.' : ''}`);
      loadStudents();
      clearSelections();
    }

    function getSelectedStudentIds() {
      return Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => parseInt(cb.value));
    }

    function onStudentCheckboxChange() {
      const ids = getSelectedStudentIds();
      const btn = document.getElementById('bulkChangeSectionBtn');
      const countEl = document.getElementById('selectedCount');
      countEl.textContent = ids.length;
      btn.classList.toggle('d-none', ids.length === 0);
      // Sync select-all checkbox state
      const all = document.querySelectorAll('.student-checkbox');
      document.getElementById('selectAllCheckbox').checked = all.length > 0 && ids.length === all.length;
    }

    function toggleSelectAll(checkbox) {
      document.querySelectorAll('.student-checkbox').forEach(cb => { cb.checked = checkbox.checked; });
      onStudentCheckboxChange();
    }

    function clearSelections() {
      document.querySelectorAll('.student-checkbox').forEach(cb => { cb.checked = false; });
      document.getElementById('selectAllCheckbox').checked = false;
      onStudentCheckboxChange();
    }

    async function graduateFourthYears() {
      const targets = allStudents.filter(s =>
        parseInt(s.year_level) === 4 &&
        (s.enrollment_type || 'regular').toLowerCase() === 'regular' &&
        (s.status || '').toLowerCase() !== 'graduated'
      );

      if (targets.length === 0) {
        alert('No regular 4th year students found to graduate.');
        return;
      }

      if (!confirm(`This will mark ${targets.length} regular 4th year student(s) as Graduated. Irregular students will not be affected. Continue?`)) return;

      const btn = document.querySelector('[onclick="graduateFourthYears()"]');
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

      let successCount = 0, failCount = 0;
      for (const student of targets) {
        const payload = {
          id: student.id,
          student_id: student.student_id,
          first_name: student.first_name,
          middle_name: student.middle_name || '',
          last_name: student.last_name,
          email: student.email,
          phone: student.phone || '',
          birth_date: student.birth_date || '',
          gender: student.gender || '',
          address: student.address || '',
          department: student.department || '',
          section_id: student.section_id || '',
          year_level: student.year_level,
          enrollment_type: student.enrollment_type || 'regular',
          status: 'graduated',
          remarks: student.remarks || ''
        };
        try {
          const res = await fetch('../../../api/students/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const data = await res.json();
          if (data.success) successCount++; else failCount++;
        } catch { failCount++; }
      }

      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-graduation-cap"></i> Graduate 4th Years';
      alert(`Done! ${successCount} student(s) marked as Graduated.${failCount > 0 ? ' ' + failCount + ' failed.' : ''}`);
      loadStudents();
    }

  </script>
</body>
</html>
