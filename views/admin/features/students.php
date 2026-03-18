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
          <button class="btn btn-success btn-sm me-2" onclick="importStudents()" title="Import students from CSV/Excel">
            <i class="fas fa-file-import"></i> Import
          </button>
          <button class="btn btn-success btn-sm me-2" onclick="generateStudentListExcel()" title="Generate student list Excel (Regular/Irregular)">
            <i class="fas fa-file-excel"></i> Excel
          </button>
          <button class="btn btn-info btn-sm me-2" onclick="exportStudents()" title="Export students to CSV/Excel">
            <i class="fas fa-file-export"></i> Export
          </button>
          <button class="btn btn-primary" onclick="openAddStudentModal()">
            <i class="fas fa-plus"></i> Add New Student
          </button>
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-3">
          <input type="text" id="searchInput" class="form-control" placeholder="Search students by name, ID">
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
  
  <!-- Add Student Modal -->
  <div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="addStudentForm">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Student ID *</label>
                  <input type="text" class="form-control" id="studentId" required maxlength="20">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" id="studentEmail" maxlength="255">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">First Name *</label>
                  <input type="text" class="form-control" id="firstName" required maxlength="63" pattern="^[A-Za-z\s\.\-]+$" title="Only letters, spaces, dots, and hyphens are allowed">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="middleName" maxlength="63" pattern="^[A-Za-z\s\.\-]+$" title="Only letters, spaces, dots, and hyphens are allowed">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Last Name *</label>
                  <input type="text" class="form-control" id="lastName" required maxlength="63" pattern="^[A-Za-z\s\.\-]+$" title="Only letters, spaces, dots, and hyphens are allowed">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Phone</label>
                  <input type="tel" class="form-control" id="studentPhone" pattern="^09\d{9}$" maxlength="11" title="Please enter a valid 11-digit mobile number starting with 09">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Birthdate</label>
                  <input type="date" class="form-control" id="birthdate">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Gender</label>
                  <select class="form-control" id="gender">
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
                  <select class="form-control" id="department" onchange="updateSectionDropdown('department', 'sectionSelect')">
                    <option value="">Select Department...</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Section</label>
                  <select class="form-control" id="sectionSelect">
                    <option value="">Select Section...</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Year Level *</label>
                  <select class="form-control" id="yearLevel" required>
                    <option value="">Select...</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                    <option value="5">5th Year</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <select class="form-control" id="status">
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
                  <select class="form-control" id="enrollmentType" required>
                    <option value="regular">Regular</option>
                    <option value="irregular">Irregular</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea class="form-control" id="address" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveStudent()">Save Student</button>
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
          <div id="viewStudentContent">
            <!-- Student details will be loaded here -->
          </div>
        </div>
        <div class="modal-footer">
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
                    <option value="5">5th Year</option>
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
        const joined = row.map(c => normalizeCellValue(c).toLowerCase()).join(' | ');
        if (joined.includes('id number') || joined.includes('student id') || joined.includes('id no') || joined.includes('id#')) {
          return r;
        }
        if (joined.includes('first name') && joined.includes('last name')) {
          return r;
        }
      }
      return -1;
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
        const nameCol = findCol(colMap, ['name', 'full name', 'student name']);
        const deptCol = findCol(colMap, ['department', 'program', 'course', 'dept']);
        const sectionCol = findCol(colMap, ['section', 'section name', 'section_code', 'section code', 'sec']);
        const yearCol = findCol(colMap, ['year level', 'yearlevel', 'year', 'yr', 'yr level', 'yrlevel', 'yl']);
        const typeCol = findCol(colMap, ['enrollment type', 'type', 'regular/irregular', 'regular - irregular', 'reg/irreg', 'reg-irreg', 'status', 'remarks']);

        const sheetSection = findSectionFromText('', sheetName);

        for (let r = headerIdx + 1; r < rows.length; r++) {
          const row = rows[r] || [];
          const studentId = idCol !== -1 ? normalizeCellValue(row[idCol]) : '';
          if (!studentId) continue;

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

          const email = buildStudentEmailFromName(first, middle, last, usedEmails);

          if (!studentsById.has(studentId)) {
            studentsById.set(studentId, {
              student_id: studentId,
              first_name: first,
              middle_name: middle || null,
              last_name: last,
              email,
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
            // Then load students
            return fetch('../../../api/students/get-all.php');
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            allStudents = data.students;
            filteredStudents = [...allStudents];
            currentPage = 1;
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
            select.innerHTML = '';
            select.appendChild(firstOption);
            
            allPrograms.forEach(program => {
                 const option = document.createElement('option');
                 option.value = program.code;
                 option.textContent = program.code; // Display the code as requested
                 select.appendChild(option);
             });
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
      const searchTerm = document.getElementById('searchInput').value.toLowerCase();
      const departmentFilter = document.getElementById('departmentFilter').value;
      const sectionFilter = document.getElementById('sectionFilter').value;
      const enrollmentTypeFilter = document.getElementById('enrollmentTypeFilter').value;
      
      filteredStudents = allStudents.filter(student => {
        const matchesSearch = (
          student.student_id.toLowerCase().includes(searchTerm) ||
          student.first_name.toLowerCase().includes(searchTerm) ||
          student.last_name.toLowerCase().includes(searchTerm) ||
          (student.middle_name && student.middle_name.toLowerCase().includes(searchTerm))
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
          <td>${student.student_id}</td>
          <td>${student.first_name} ${student.middle_name ? student.middle_name + ' ' : ''}${student.last_name}</td>
          <td>${departmentDisplay}</td>
          <td>${student.section_name || 'N/A'}</td>
          <td>${yearLevel}</td>
          <td><span class="badge ${enrollmentType === 'regular' ? 'bg-info' : 'bg-warning'} text-dark">${typeLabel}</span></td>
        `;

        // Add click listener to the whole row
        row.addEventListener('click', function() {
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
        4: '4th Year',
        5: '5th Year'
      };
      return levels[yearLevel] || `${yearLevel}th Year`;
    }
    
    // View Student Details
    function viewStudent(id) {
      console.log('Viewing student:', id);
      
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
            
            const content = `
              <div class="row">
                <div class="col-md-6">
                  <p><strong>Student ID:</strong> ${student.student_id}</p>
                  <p><strong>Name:</strong> ${student.first_name} ${student.middle_name ? student.middle_name + ' ' : ''}${student.last_name}</p>
                  <p><strong>Email:</strong> ${student.email}</p>
                  <p><strong>Phone:</strong> ${student.phone || 'N/A'}</p>
                  <p><strong>Birthdate:</strong> ${student.birth_date || 'N/A'}</p>
                  <p><strong>Gender:</strong> ${student.gender || 'N/A'}</p>
                </div>
                <div class="col-md-6">
                  <p><strong>Enrollment Type:</strong> <span class="badge ${enrollmentType === 'regular' ? 'bg-info' : 'bg-warning'} text-dark">${enrollmentTypeLabel}</span></p>
                  <p><strong>Department:</strong> ${departmentDisplay}</p>
                  <p><strong>Section:</strong> ${student.section_name || 'N/A'}</p>
                  <p><strong>Year Level:</strong> ${getYearLevelText(student.year_level)}</p>
                  <p><strong>Address:</strong> ${student.address || 'N/A'}</p>
                  <p><strong>Created:</strong> ${new Date(student.created_at).toLocaleDateString()}</p>
                  <p><strong>Updated:</strong> ${new Date(student.updated_at).toLocaleDateString()}</p>
                </div>
              </div>
            `;
            document.getElementById('viewStudentContent').innerHTML = content;
            
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
    
    // Edit Student
    function editStudent(id) {
      console.log('Editing student:', id);
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
            document.getElementById('editDepartment').value = student.department || '';
            
            // Populate sections based on department and select current section
            // Ensure allSections is populated (it should be if loadStudents completed)
            if (allSections.length === 0) {
                 // Fallback: try to fetch sections again or just log warning
                 console.warn('allSections is empty, section dropdown might be incomplete');
            }
            updateSectionDropdown('editDepartment', 'editSectionSelect', student.section_id);
            
            document.getElementById('editYearLevel').value = student.year_level || '';
            document.getElementById('editEnrollmentType').value = student.enrollment_type || 'regular';
            document.getElementById('editStatus').value = student.status;
            document.getElementById('editAddress').value = student.address || '';
            
            showModal('editStudentModal');
          } else {
            alert('Error loading student: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error loading student details. Please check console for details.');
        });
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
    
    // Open Add Student Modal
    function openAddStudentModal() {
      // Reset form
      document.getElementById('addStudentForm').reset();
      
      // Show modal
      showModal('addStudentModal');
    }
    
    // Save Student
    function saveStudent() {
      // Get form values
      const studentData = {
        student_id: document.getElementById('studentId').value.trim(),
        first_name: document.getElementById('firstName').value.trim(),
        middle_name: document.getElementById('middleName').value.trim(),
        last_name: document.getElementById('lastName').value.trim(),
        email: document.getElementById('studentEmail').value.trim(),
        phone: document.getElementById('studentPhone').value.trim(),
        birth_date: document.getElementById('birthdate').value,
        gender: document.getElementById('gender').value,
        address: document.getElementById('address').value.trim(),
        department: document.getElementById('department').value || null,
        section_id: document.getElementById('sectionSelect').value || null,
        year_level: parseInt(document.getElementById('yearLevel').value),
        enrollment_type: document.getElementById('enrollmentType').value,
        status: document.getElementById('status').value || 'active'
      };

      if (!studentData.email) {
        studentData.email = buildStudentEmailFromName(studentData.first_name, studentData.middle_name, studentData.last_name);
        document.getElementById('studentEmail').value = studentData.email;
      }
      
      // Validate required fields
      if (!studentData.student_id || !studentData.first_name || !studentData.last_name || 
          !studentData.email || !studentData.year_level) {
        alert('Please fill in all required fields');
        return;
      }
      
      // Send to API
      fetch('../../../api/students/create.php', {
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
          const modal = bootstrap.Modal.getInstance(document.getElementById('addStudentModal'));
          modal.hide();
          
          // Reload students
          loadStudents();
          
          // Show success message
          alert('Student added successfully!');
        } else {
          alert('Error adding student: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error adding student. Please try again.');
      });
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
  </script>
</body>
</html>
