<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Program Heads Management - Admin Panel</title>
  
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
      <a class="menu-item active" href="program-heads.php">
        <i class="fas fa-chalkboard-teacher"></i>
        <span>Program Heads</span>
      </a>
      <a class="menu-item" href="admissions.php">
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
      <h5 style="margin: 0; color: var(--primary-blue);">Program Heads Management</h5>
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
      <h2>Program Head Management</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Program Heads</li>
        </ol>
      </nav>
    </div>
    
    <div class="content-card">
      <div class="content-card-header">
        <h5>All Program Heads</h5>
        <button class="btn btn-primary" onclick="openAddProgramHeadModal()">
          <i class="fas fa-plus"></i> Add New Program Head
        </button>
      </div>
      
      <div class="mb-3">
        <input type="text" class="form-control" placeholder="Search program heads by name, department, or specialization...">
      </div>
      
      <div class="table-responsive">
        <table class="table custom-table" id="programHeadsTable">
          <thead>
            <tr>
              <th>Program Head ID</th>
              <th>Name</th>
              <th>Department</th>
              <th>Specialization</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Add/Edit Program Head Modal -->
  <div class="modal fade" id="programHeadModal" tabindex="-1" aria-labelledby="programHeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="programHeadModalLabel">Add Program Head</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="programHeadForm">
            <input type="hidden" id="programHeadId">
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="employeeId" class="form-label">Employee ID</label>
                  <input type="text" class="form-control" id="employeeId" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="firstName" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="firstName" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="lastName" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="lastName" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="middleName" class="form-label">Middle Name</label>
                  <input type="text" class="form-control" id="middleName">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone</label>
                  <input type="text" class="form-control" id="phone" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="hireDate" class="form-label">Hire Date</label>
                  <input type="date" class="form-control" id="hireDate" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="department" class="form-label">Department</label>
                  <select class="form-select" id="department" required>
                    <option value="">Select Department</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Public Administration">Public Administration</option>
                    <option value="Technical-Vocational">Technical-Vocational</option>
                    <option value="Business">Business</option>
                    <option value="Education">Education</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="specialization" class="form-label">Specialization</label>
                  <input type="text" class="form-control" id="specialization">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveProgramHead()">Save Program Head</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- View Program Head Modal -->
  <div class="modal fade" id="viewProgramHeadModal" tabindex="-1" aria-labelledby="viewProgramHeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewProgramHeadModalLabel">Program Head Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Employee ID:</strong> <span id="viewEmployeeId"></span></p>
              <p><strong>Name:</strong> <span id="viewFullName"></span></p>
              <p><strong>Email:</strong> <span id="viewEmail"></span></p>
              <p><strong>Phone:</strong> <span id="viewPhone"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Department:</strong> <span id="viewDepartment"></span></p>
              <p><strong>Specialization:</strong> <span id="viewSpecialization"></span></p>
              <p><strong>Hire Date:</strong> <span id="viewHireDate"></span></p>
              <p><strong>Status:</strong> <span id="viewStatus"></span></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
  <script>
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
    
    // Load program heads data
    function loadProgramHeads() {
      fetch('../../../api/program-heads/get-all.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            populateTable(data.program_heads);
          }
        })
        .catch(error => {
          console.error('Error loading program heads:', error);
        });
    }
    
    // Populate table with data
    function populateTable(programHeads) {
      const tbody = document.querySelector('#programHeadsTable tbody');
      tbody.innerHTML = '';
      
      programHeads.forEach(head => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${head.employee_id}</td>
          <td>${head.first_name} ${head.middle_name ? head.middle_name + ' ' : ''}${head.last_name}</td>
          <td>${head.department}</td>
          <td>${head.specialization || 'N/A'}</td>
          <td><span class="badge-status ${head.status}">${head.status.charAt(0).toUpperCase() + head.status.slice(1)}</span></td>
          <td>
            <button class="action-btn view" onclick="viewProgramHead(${head.id})"><i class="fas fa-eye"></i></button>
            <button class="action-btn edit" onclick="editProgramHead(${head.id})"><i class="fas fa-edit"></i></button>
            <button class="action-btn delete" onclick="deleteProgramHead(${head.id}, '${head.first_name} ${head.last_name}')"><i class="fas fa-trash"></i></button>
          </td>
        `;
        tbody.appendChild(row);
      });
    }
    
    // Open Add Program Head Modal
    function openAddProgramHeadModal() {
      document.getElementById('programHeadModalLabel').textContent = 'Add Program Head';
      document.getElementById('programHeadForm').reset();
      document.getElementById('programHeadId').value = '';
      
      const modal = new bootstrap.Modal(document.getElementById('programHeadModal'));
      modal.show();
    }
    
    // View Program Head
    function viewProgramHead(id) {
      fetch(`../../../api/program-heads/get-one.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const head = data.program_head;
            document.getElementById('viewEmployeeId').textContent = head.employee_id;
            document.getElementById('viewFullName').textContent = `${head.first_name} ${head.middle_name ? head.middle_name + ' ' : ''}${head.last_name}`;
            document.getElementById('viewEmail').textContent = head.email;
            document.getElementById('viewPhone').textContent = head.phone;
            document.getElementById('viewDepartment').textContent = head.department;
            document.getElementById('viewSpecialization').textContent = head.specialization || 'N/A';
            document.getElementById('viewHireDate').textContent = head.hire_date;
            document.getElementById('viewStatus').innerHTML = `<span class="badge-status ${head.status}">${head.status.charAt(0).toUpperCase() + head.status.slice(1)}</span>`;
            
            const modal = new bootstrap.Modal(document.getElementById('viewProgramHeadModal'));
            modal.show();
          }
        })
        .catch(error => {
          console.error('Error fetching program head:', error);
        });
    }
    
    // Edit Program Head
    function editProgramHead(id) {
      fetch(`../../../api/program-heads/get-one.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const head = data.program_head;
            document.getElementById('programHeadModalLabel').textContent = 'Edit Program Head';
            document.getElementById('programHeadId').value = head.id;
            document.getElementById('employeeId').value = head.employee_id;
            document.getElementById('firstName').value = head.first_name;
            document.getElementById('middleName').value = head.middle_name || '';
            document.getElementById('lastName').value = head.last_name;
            document.getElementById('email').value = head.email;
            document.getElementById('phone').value = head.phone;
            document.getElementById('hireDate').value = head.hire_date;
            document.getElementById('department').value = head.department;
            document.getElementById('specialization').value = head.specialization || '';
            document.getElementById('status').value = head.status;
            
            const modal = new bootstrap.Modal(document.getElementById('programHeadModal'));
            modal.show();
          }
        })
        .catch(error => {
          console.error('Error fetching program head:', error);
        });
    }
    
    // Save Program Head
    function saveProgramHead() {
      const id = document.getElementById('programHeadId').value;
      const formData = {
        employee_id: document.getElementById('employeeId').value,
        first_name: document.getElementById('firstName').value,
        middle_name: document.getElementById('middleName').value,
        last_name: document.getElementById('lastName').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        hire_date: document.getElementById('hireDate').value,
        department: document.getElementById('department').value,
        specialization: document.getElementById('specialization').value,
        status: document.getElementById('status').value
      };
      
      let url, method;
      if (id) {
        url = `../../../api/program-heads/update.php?id=${id}`;
        method = 'PUT';
      } else {
        url = '../../../api/program-heads/create.php';
        method = 'POST';
      }
      
      fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('programHeadModal'));
          modal.hide();
          loadProgramHeads();
          alert(data.message);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error saving program head:', error);
        alert('An error occurred while saving the program head.');
      });
    }
    
    // Delete Program Head
    function deleteProgramHead(id, name) {
      if (confirm(`Are you sure you want to delete program head ${name}?`)) {
        fetch(`../../../api/program-heads/delete.php?id=${id}`, {
          method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            loadProgramHeads();
            alert(data.message);
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error deleting program head:', error);
          alert('An error occurred while deleting the program head.');
        });
      }
    }
    
    // Auto-collapse sidebar on mobile
    if (window.innerWidth <= 768) {
      document.getElementById('sidebar').classList.add('collapsed');
    }
    
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      loadProgramHeads();
    });
  </script>
</body>
</html>
