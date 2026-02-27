<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student List - Admin Panel</title>
  
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
          <button class="btn btn-info btn-sm me-2" onclick="exportStudents()" title="Export students to CSV/Excel">
            <i class="fas fa-file-export"></i> Export
          </button>
          <button class="btn btn-primary" onclick="openAddStudentModal()">
            <i class="fas fa-plus"></i> Add New Student
          </button>
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-4">
          <input type="text" id="searchInput" class="form-control" placeholder="Search students by name, ID...">
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
          <select class="form-select" id="programFilter">
            <option value="">All Programs</option>
          </select>
        </div>
         <div class="col-md-2">
            <button class="btn btn-primary w-100" onclick="filterStudents()">Filter</button>
         </div>
      </div>
      
      <div class="table-responsive">
        <table class="table custom-table">
          <thead>
            <tr>
              <th>Student ID</th>
              <th>Name</th>
              <th>Department</th>
              <th>Section</th>
              <th>Year Level</th>
            </tr>
          </thead>
          <tbody id="studentsTableBody">
            <!-- Data will be loaded dynamically -->
          </tbody>
        </table>
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
                  <input type="text" class="form-control" id="studentId" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" id="studentEmail">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">First Name *</label>
                  <input type="text" class="form-control" id="firstName" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="middleName">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Last Name *</label>
                  <input type="text" class="form-control" id="lastName" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Phone</label>
                  <input type="tel" class="form-control" id="studentPhone">
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
                    <option value="BSIS">Bachelor of Science in Information Systems</option>
                    <option value="BPA">Business Administration</option>
                    <option value="BTVTED">Bachelor of Technical-Vocational Teacher Education</option>
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
                  <input type="text" class="form-control" id="editStudentIdField" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" id="editStudentEmail">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">First Name *</label>
                  <input type="text" class="form-control" id="editFirstName" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="editMiddleName">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Last Name *</label>
                  <input type="text" class="form-control" id="editLastName" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Phone</label>
                  <input type="tel" class="form-control" id="editPhone">
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
                    <option value="BSIS">Bachelor of Science in Information Systems</option>
                    <option value="BPA">Business Administration</option>
                    <option value="BTVTED">Bachelor of Technical-Vocational Teacher Education</option>
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

    // Load Students Data
    function loadStudents() {
      // Load sections first
      fetch('../../../api/sections/get-all.php')
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
            displayStudents(allStudents);
          } else {
            console.error('Error loading students:', data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
    
    // Populate Section Filter (Main Filter)
    function populateSectionFilter() {
      const departmentFilter = document.getElementById('departmentFilter').value;
      const sectionFilter = document.getElementById('sectionFilter');
      const currentSection = sectionFilter.value;
      
      // Filter sections based on department
      let filteredSections = allSections;
      if (departmentFilter) {
          const codeKey = departmentFilter.toString().trim().toLowerCase();
          filteredSections = allSections.filter(sec => ((sec.department_code || '').toString().trim().toLowerCase()) === codeKey);
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
      // check if currentSection is in filteredSections
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
            filteredSections = allSections.filter(sec => sec.department_code === department);
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
      const department = document.getElementById('departmentFilter').value;
      const section = document.getElementById('sectionFilter').value;
      const programId = document.getElementById('programFilter') ? document.getElementById('programFilter').value : '';
      let deptFromProgram = '';
      if (programId && window.allPrograms && Array.isArray(window.allPrograms)) {
        const prog = window.allPrograms.find(p => String(p.id) === String(programId));
        if (prog && prog.code) {
          const deptKey = (prog.code || '').toString().trim().toLowerCase();
          document.getElementById('departmentFilter').value = deptKey;
          populateSectionFilter();
          deptFromProgram = prog.code || '';
        }
      }
      
      const filtered = allStudents.filter(student => {
        const matchesSearch = (
          student.student_id.toLowerCase().includes(searchTerm) ||
          student.first_name.toLowerCase().includes(searchTerm) ||
          student.last_name.toLowerCase().includes(searchTerm) ||
          (student.middle_name && student.middle_name.toLowerCase().includes(searchTerm))
        );
        
        const deptNorm = (student.department || '').toString().trim().toLowerCase();
        const departmentFilterNorm = (department || '').toString().trim().toLowerCase();
        const deptFromProgramNorm = (deptFromProgram || '').toString().trim().toLowerCase();
        const sectionNorm = (student.section_name || '').toString().trim().toLowerCase();
        const sectionFilterNorm = (section || '').toString().trim().toLowerCase();
        
        const matchesDepartment = !department || deptNorm === departmentFilterNorm;
        const matchesProgram = !programId || deptNorm === deptFromProgramNorm;
        const matchesSection = !section || sectionNorm === sectionFilterNorm;
        
        return matchesSearch && matchesDepartment && matchesProgram && matchesSection;
      });
      
      displayStudents(filtered);
    }
    
    // Add Event Listeners
    document.getElementById('searchInput').addEventListener('input', filterStudents);
    document.getElementById('departmentFilter').addEventListener('change', function() {
      populateSectionFilter();
      filterStudents();
    });
    document.getElementById('sectionFilter').addEventListener('change', filterStudents);
    (function() {
      const progSel = document.getElementById('programFilter');
      if (progSel) {
        progSel.addEventListener('change', function() {
          const programId = this.value;
          if (programId && window.allPrograms) {
            const prog = window.allPrograms.find(p => String(p.id) === String(programId));
            if (prog && prog.code) {
              document.getElementById('departmentFilter').value = prog.code;
              populateSectionFilter();
            }
          }
          filterStudents();
        });
      }
    })();

    // Display Students in Table
    function displayStudents(students) {
      const tbody = document.getElementById('studentsTableBody');
      tbody.innerHTML = '';
      
      if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No students found</td></tr>';
        return;
      }
      
      students.forEach(student => {
        const row = document.createElement('tr');
        row.style.cursor = 'pointer';
        
        // Format year level
        const yearLevel = getYearLevelText(student.year_level);
        
        row.innerHTML = `
          <td>${student.student_id}</td>
          <td>${student.first_name} ${student.middle_name ? student.middle_name + ' ' : ''}${student.last_name}</td>
          <td>${student.department || 'N/A'}</td>
          <td>${student.section_name || 'N/A'}</td>
          <td>${yearLevel}</td>
        `;

        // Add click listener to the whole row
        row.addEventListener('click', function() {
            viewStudent(student.id);
        });
        
        tbody.appendChild(row);
      });
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
                  <p><strong>Department:</strong> ${student.department || 'N/A'}</p>
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
        status: document.getElementById('editStatus').value
      };
      
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
        status: document.getElementById('status').value || 'active'
      };
      
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
    
    // Import Students
    function importStudents() {
      // Create file input
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = '.csv,.xlsx,.xls';
      input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
          // TODO: Implement file upload and processing
          alert('Import functionality will be implemented soon!\n\nSelected file: ' + file.name + '\n\nThis will allow you to import students from CSV or Excel files.');
        }
      };
      input.click();
    }
    
    // Export Students
    function exportStudents() {
      // TODO: Implement export functionality
      alert('Export functionality will be implemented soon!\n\nThis will allow you to export current students to CSV or Excel format.');
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
      // Load programs to populate Program and Department filters
      fetch('../../../api/programs/get-all.php?status=active')
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            window.allPrograms = res.programs || [];
            const progSel = document.getElementById('programFilter');
            const deptSel = document.getElementById('departmentFilter');
            // Populate Program filter
            if (progSel) {
              const currentProg = progSel.value;
              progSel.innerHTML = '<option value="">All Programs</option>';
              window.allPrograms.forEach(p => {
                const opt = document.createElement('option');
                opt.value = String(p.id);
                opt.textContent = p.short_title ? `${p.short_title} (${p.code})` : `${p.title} (${p.code})`;
                progSel.appendChild(opt);
              });
              if (currentProg) progSel.value = currentProg;
            }
            // Populate Department filter from program codes (dynamic, not static)
            if (deptSel) {
              const currentDept = deptSel.value;
              const uniqueCodes = Array.from(new Set(window.allPrograms.map(p => (p.code || '').trim().toLowerCase()))).filter(Boolean).sort();
              deptSel.innerHTML = '<option value="">All Departments</option>';
              uniqueCodes.forEach(code => {
                const opt = document.createElement('option');
                opt.value = code;
                opt.textContent = code.toUpperCase();
                deptSel.appendChild(opt);
              });
              if (currentDept && uniqueCodes.includes(currentDept.toLowerCase())) {
                deptSel.value = currentDept.toLowerCase();
              }
            }
          }
        })
        .catch(err => console.error('Error loading programs:', err));
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
