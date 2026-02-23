<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Programs Management - Admin Panel</title>
  
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
      <a class="menu-item" href="admissions.php">
        <i class="fas fa-file-alt"></i>
        <span>Admissions</span>
      </a>
      <a class="menu-item active" href="programs.php">
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
      <h5 style="margin: 0; color: var(--primary-blue);">Programs Management</h5>
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
      <h2>Programs Management</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Programs</li>
        </ol>
      </nav>
    </div>
    
    <div class="content-card">
      <div class="content-card-header">
        <h5>Academic Programs</h5>
        <button class="btn btn-primary" onclick="openAddProgramModal()">
          <i class="fas fa-plus"></i> Add New Program
        </button>
      </div>
      
      <div class="mb-3">
        <div class="row g-2">
          <div class="col-md-6">
            <input type="text" class="form-control" id="searchPrograms" placeholder="Search programs by code, name, or department...">
          </div>
          <div class="col-md-3">
            <select class="form-select" id="filterCategory">
              <option value="">All Categories</option>
              <option value="4-years">4 Years</option>
              <option value="technical">Technical-Vocational</option>
            </select>
          </div>
          <div class="col-md-3">
            <select class="form-select" id="filterStatus">
              <option value="active">Active Only</option>
              <option value="inactive">Inactive Only</option>
              <option value="">All Status</option>
            </select>
          </div>
        </div>
      </div>
      
      <div class="table-responsive">
        <table class="table custom-table" id="programsTable">
          <thead>
            <tr>
              <th>Code</th>
              <th>Program Name</th>
              <th>Category</th>
              <th>Department</th>
              <th>Duration</th>
              <th>Enrolled</th>
              <th>Prospectus Downloads</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="9" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading programs...</span>
                </div>
                <p class="mt-2 text-muted">Loading programs...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Add/Edit Program Modal -->
  <div class="modal fade" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="programModalLabel">Add Program</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="programForm">
            <input type="hidden" id="programId">
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="programCode" class="form-label">Program Code</label>
                  <input type="text" class="form-control" id="programCode" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="shortTitle" class="form-label">Short Title</label>
                  <input type="text" class="form-control" id="shortTitle" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="category" class="form-label">Category</label>
                  <select class="form-select" id="category" required>
                    <option value="">Select Category</option>
                    <option value="4-years">4 Years</option>
                    <option value="technical">Technical-Vocational</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="programTitle" class="form-label">Full Title</label>
              <input type="text" class="form-control" id="programTitle" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" rows="3" required></textarea>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="department" class="form-label">Department</label>
                  <input type="text" class="form-control" id="department" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="duration" class="form-label">Duration</label>
                  <input type="text" class="form-control" id="duration" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="units" class="form-label">Units</label>
                  <input type="text" class="form-control" id="units" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="mb-3">
                  <label for="programHead" class="form-label">Program Head</label>
                  <input type="text" class="form-control" id="programHead" placeholder="Enter Program Head Name">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="programImage" class="form-label">Program Image</label>
                  <input type="file" class="form-control" id="programImage" accept="image/*" onchange="previewImage(this)">
                  <div class="form-text">Allowed: JPG, PNG, WebP (Max: 5MB)</div>
                  
                  <!-- Current File Display -->
                  <div id="currentImageDisplay" class="mt-2">
                    <div class="alert alert-info py-2" id="currentImageInfo" style="display: none;">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <i class="fas fa-image me-2"></i>
                          <span id="currentImageName">Current file</span>
                          <br>
                          <small class="text-muted" id="currentImagePath"></small>
                        </div>
                        <div>
                          <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCurrentImage()" id="removeImageBtn" style="display: none;">
                            <i class="fas fa-trash"></i> Remove
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="text-muted small" id="noImageText">No image uploaded</div>
                  </div>
                  
                  <!-- Preview -->
                  <div id="imagePreview" class="mt-2" style="display: none;">
                    <img id="imagePreviewImg" src="" alt="Image preview" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                    <div class="progress mt-2" id="imageUploadProgress" style="display: none;">
                      <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="prospectusFile" class="form-label">Prospectus File</label>
                  <input type="file" class="form-control" id="prospectusFile" accept=".pdf,.xlsx,.xls,.docx,.doc" onchange="showFileInfo(this, 'prospectus')">
                  <div class="form-text">Allowed: PDF, Excel, Word (Max: 10MB)</div>
                  
                  <!-- Current File Display -->
                  <div id="currentProspectusDisplay" class="mt-2">
                    <div class="alert alert-info py-2" id="currentProspectusInfo" style="display: none;">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <i class="fas fa-file me-2"></i>
                          <span id="currentProspectusName">Current file</span>
                          <br>
                          <small class="text-muted" id="currentProspectusPath"></small>
                        </div>
                        <div>
                          <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCurrentProspectus()" id="removeProspectusBtn" style="display: none;">
                            <i class="fas fa-trash"></i> Remove
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="text-muted small" id="noProspectusText">No prospectus uploaded</div>
                  </div>
                  
                  <!-- New File Info -->
                  <div id="prospectusFileInfo" class="mt-2" style="display: none;">
                    <div class="alert alert-success py-2">
                      <i class="fas fa-file me-2"></i>
                      <span id="prospectusFileName"></span>
                      <small class="d-block text-muted" id="prospectusFileSize"></small>
                    </div>
                    <div class="progress mt-2" id="prospectusUploadProgress" style="display: none;">
                      <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
                        <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="enrolledStudents" class="form-label">Enrolled Students</label>
                  <input type="number" class="form-control" id="enrolledStudents" min="0" value="0">
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
            <div class="mb-3">
              <label class="form-label">Program Highlights</label>
              <div id="highlightsList" class="mb-2">
                <!-- Dynamic highlight fields will be added here -->
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="addHighlight()">
                <i class="fas fa-plus"></i> Add Highlight
              </button>
            </div>
            <div class="mb-3">
              <label class="form-label">Career Opportunities</label>
              <div id="careersList" class="mb-2">
                <!-- Dynamic career fields will be added here -->
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="addCareer()">
                <i class="fas fa-plus"></i> Add Career Opportunity
              </button>
            </div>
            <div class="mb-3">
              <label class="form-label">Admission Requirements</label>
              <div id="requirementsList" class="mb-2">
                <!-- Dynamic requirement fields will be added here -->
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm" onclick="addRequirement()">
                <i class="fas fa-plus"></i> Add Requirement
              </button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveProgram()">Save Program</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- View Program Modal -->
  <div class="modal fade" id="viewProgramModal" tabindex="-1" aria-labelledby="viewProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewProgramModalLabel">Program Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Program Code:</strong> <span id="viewCode"></span></p>
              <p><strong>Title:</strong> <span id="viewTitle"></span></p>
              <p><strong>Short Title:</strong> <span id="viewShortTitle"></span></p>
              <p><strong>Category:</strong> <span id="viewCategory"></span></p>
              <p><strong>Department:</strong> <span id="viewDepartment"></span></p>
              <p><strong>Duration:</strong> <span id="viewDuration"></span></p>
              <p><strong>Units:</strong> <span id="viewUnits"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Status:</strong> <span id="viewStatus"></span></p>
              <p><strong>Enrolled Students:</strong> <span id="viewEnrolled"></span></p>
              <p><strong>Program Head:</strong> <span id="viewProgramHead"></span></p>
              <p><strong>Created:</strong> <span id="viewCreated"></span></p>
              <p><strong>Updated:</strong> <span id="viewUpdated"></span></p>
              <p><strong>Prospectus Downloads:</strong> <span id="viewDownloads"></span></p>
              <p><strong>Has Prospectus:</strong> <span id="viewHasProspectus"></span></p>
            </div>
          </div>
          <div class="mb-3">
            <strong>Description:</strong>
            <p id="viewDescription"></p>
          </div>
          <div class="mb-3">
            <strong>Highlights:</strong>
            <ul id="viewHighlights"></ul>
          </div>
          <div class="mb-3">
            <strong>Career Opportunities:</strong>
            <ul id="viewCareerOpportunities"></ul>
          </div>
          <div class="mb-3">
            <strong>Admission Requirements:</strong>
            <ul id="viewAdmissionRequirements"></ul>
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
    
    // Load programs data
    function loadPrograms() {
      const tbody = document.querySelector('#programsTable tbody');
      
      // Show loading state
      tbody.innerHTML = `
        <tr>
          <td colspan="9" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading programs...</span>
            </div>
            <p class="mt-2 text-muted">Loading programs...</p>
          </td>
        </tr>
      `;
      
      fetch('http://localhost/CNESIS/api/programs/get-all.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            populateTable(data.programs);
          } else {
            tbody.innerHTML = `
              <tr>
                <td colspan="9" class="text-center py-4 text-danger">
                  Failed to load programs: ${data.message}
                </td>
              </tr>
            `;
          }
        })
        .catch(error => {
          console.error('Error loading programs:', error);
          tbody.innerHTML = `
            <tr>
              <td colspan="9" class="text-center py-4 text-danger">
                Error loading programs: ${error.message}
                <br><small>Check console for details</small>
              </td>
            </tr>
          `;
        });
    }
    
    // Populate table with data
    function populateTable(programs) {
      const tbody = document.querySelector('#programsTable tbody');
      tbody.innerHTML = '';
      
      if (!programs || programs.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="9" class="text-center py-4 text-muted">
              <i class="fas fa-inbox fa-3x mb-3"></i>
              <p>No programs found</p>
              <small>Click "Add New Program" to create your first program</small>
            </td>
          </tr>
        `;
        return;
      }
      
      programs.forEach(program => {
        const row = document.createElement('tr');
        row.setAttribute('data-program-id', program.id);
        row.innerHTML = `
          <td>${program.code}</td>
          <td>${program.short_title}</td>
          <td>${program.category}</td>
          <td>${program.department}</td>
          <td>${program.duration}</td>
          <td>${program.enrolled_students || 0}</td>
          <td class="download-count">${program.download_count || 0}</td>
          <td><span class="badge-status ${program.status}">${program.status.charAt(0).toUpperCase() + program.status.slice(1)}</span></td>
          <td>
            <button class="action-btn view" onclick="viewProgram(${program.id})" title="View Details">
              <i class="fas fa-eye"></i>
            </button>
            <button class="action-btn edit" onclick="editProgram(${program.id})" title="Edit Program">
              <i class="fas fa-edit"></i>
            </button>
            <button class="action-btn delete" onclick="deleteProgram(${program.id}, '${program.short_title}')" title="Delete Program">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        `;
        tbody.appendChild(row);
      });
    }
    
    // Load Program Heads for dropdown - DEPRECATED/REMOVED
    // function loadProgramHeads() { ... }
    
    // Open Add Program Modal
    function openAddProgramModal() {
      document.getElementById('programModalLabel').textContent = 'Add Program';
      document.getElementById('programForm').reset();
      document.getElementById('programId').value = '';
      
      // Clear dynamic lists
      document.getElementById('highlightsList').innerHTML = '';
      document.getElementById('careersList').innerHTML = '';
      document.getElementById('requirementsList').innerHTML = '';
      
      // Add initial empty fields
      addHighlight();
      addCareer();
      addRequirement();
      
      // Reset file displays
      resetFileDisplays();
      
      const modal = new bootstrap.Modal(document.getElementById('programModal'));
      modal.show();
    }
    
    // View Program
    function viewProgram(id) {
      console.log('View Program called with ID:', id);
      fetch(`http://localhost/CNESIS/api/programs/get-one.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          console.log('View Program API response:', data);
          if (data.success) {
            const program = data.program;
            document.getElementById('viewCode').textContent = program.code;
            document.getElementById('viewTitle').textContent = program.title;
            document.getElementById('viewShortTitle').textContent = program.short_title;
            document.getElementById('viewCategory').textContent = program.category;
            document.getElementById('viewDepartment').textContent = program.department;
            document.getElementById('viewDuration').textContent = program.duration;
            document.getElementById('viewUnits').textContent = program.units;
            document.getElementById('viewStatus').innerHTML = `<span class="badge-status ${program.status}">${program.status.charAt(0).toUpperCase() + program.status.slice(1)}</span>`;
            document.getElementById('viewEnrolled').textContent = program.enrolled_students || 0;
            document.getElementById('viewProgramHead').textContent = program.program_head_name || 'Not Assigned';
            document.getElementById('viewCreated').textContent = program.created_at;
            document.getElementById('viewUpdated').textContent = program.updated_at;
            document.getElementById('viewDescription').textContent = program.description;
            
            // Handle JSON fields
            try {
              const highlights = JSON.parse(program.highlights || '[]');
              const highlightsList = document.getElementById('viewHighlights');
              highlightsList.innerHTML = '';
              highlights.forEach(h => {
                const li = document.createElement('li');
                li.textContent = h;
                highlightsList.appendChild(li);
              });
            } catch(e) {
              document.getElementById('viewHighlights').innerHTML = '<li>Invalid data format</li>';
            }
            
            try {
              const careerOpps = JSON.parse(program.career_opportunities || '[]');
              const careerList = document.getElementById('viewCareerOpportunities');
              careerList.innerHTML = '';
              careerOpps.forEach(c => {
                const li = document.createElement('li');
                li.textContent = c;
                careerList.appendChild(li);
              });
            } catch(e) {
              document.getElementById('viewCareerOpportunities').innerHTML = '<li>Invalid data format</li>';
            }
            
            try {
              const reqs = JSON.parse(program.admission_requirements || '[]');
              const reqList = document.getElementById('viewAdmissionRequirements');
              reqList.innerHTML = '';
              reqs.forEach(r => {
                const li = document.createElement('li');
                li.textContent = r;
                reqList.appendChild(li);
              });
            } catch(e) {
              document.getElementById('viewAdmissionRequirements').innerHTML = '<li>Invalid data format</li>';
            }
            
            // Get download stats for this program
            fetch(`http://localhost/CNESIS/api/programs/get-prospectus-downloads.php?program_id=${id}`)
              .then(response => response.json())
              .then(downloadData => {
                if (downloadData.success) {
                  document.getElementById('viewDownloads').textContent = downloadData.download_count;
                  document.getElementById('viewHasProspectus').textContent = program.prospectus_path ? 'Yes' : 'No';
                }
              })
              .catch(() => {
                document.getElementById('viewDownloads').textContent = 'N/A';
                document.getElementById('viewHasProspectus').textContent = program.prospectus_path ? 'Yes' : 'No';
              });
            
            const modal = new bootstrap.Modal(document.getElementById('viewProgramModal'));
            modal.show();
          }
        })
        .catch(error => {
          console.error('Error fetching program:', error);
        });
    }
    
    // Edit Program
    function editProgram(id) {
      console.log('Edit Program called with ID:', id);
      fetch(`http://localhost/CNESIS/api/programs/get-one.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
          console.log('Edit Program API response:', data);
          if (data.success) {
            const program = data.program;
            
            // Reset file previews first
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('prospectusFileInfo').style.display = 'none';
            
            // Populate form fields
            document.getElementById('programModalLabel').textContent = 'Edit Program';
            document.getElementById('programId').value = program.id;
            document.getElementById('programCode').value = program.code;
            document.getElementById('shortTitle').value = program.short_title;
            document.getElementById('programTitle').value = program.title;
            document.getElementById('category').value = program.category;
            document.getElementById('description').value = program.description;
            document.getElementById('department').value = program.department;
            document.getElementById('duration').value = program.duration;
            document.getElementById('units').value = program.units;
            document.getElementById('status').value = program.status;
            document.getElementById('enrolledStudents').value = program.enrolled_students || 0;
            document.getElementById('programHead').value = program.program_head_name || '';
            
            // Display existing files
            displayExistingFiles(program.image_path, program.prospectus_path);
            
            // Show preview of existing image if available
            if (program.image_path) {
              document.getElementById('imagePreviewImg').src = program.image_path;
              document.getElementById('imagePreview').style.display = 'block';
            }
            
            // Clear dynamic lists and populate them
            document.getElementById('highlightsList').innerHTML = '';
            document.getElementById('careersList').innerHTML = '';
            document.getElementById('requirementsList').innerHTML = '';
            
            populateList('highlightsList', program.highlights, 'highlight');
            populateList('careersList', program.career_opportunities, 'career');
            populateList('requirementsList', program.admission_requirements, 'requirement');
            
            console.log('Opening edit modal for program:', program.short_title);
            const modal = new bootstrap.Modal(document.getElementById('programModal'));
            modal.show();
          }
        })
        .catch(error => {
          console.error('Error fetching program:', error);
        });
    }
    
    // Save Program
    async function saveProgram() {
      const programId = document.getElementById('programId').value;
      const isEdit = programId !== '';
      
      // Collect form data
      const formData = {
        code: document.getElementById('programCode').value,
        short_title: document.getElementById('shortTitle').value,
        title: document.getElementById('programTitle').value,
        category: document.getElementById('category').value,
        department: document.getElementById('department').value,
        duration: document.getElementById('duration').value,
        units: document.getElementById('units').value,
        enrolled_students: parseInt(document.getElementById('enrolledStudents').value) || 0,
        description: document.getElementById('description').value,
        status: document.getElementById('status').value,
        program_head_name: document.getElementById('programHead').value || null,
        highlights: getListValues('highlight'),
        career_opportunities: getListValues('career'),
        admission_requirements: getListValues('requirement')
      };
      
      if (isEdit) {
        formData.id = programId;
      }
      
      // Validate
      if (!formData.code || !formData.title || !formData.category) {
        alert('Please fill in all required fields');
        return;
      }
      
      try {
        // Save basic program data first
        const endpoint = isEdit ? 
          'http://localhost/CNESIS/api/programs/update.php' : 
          'http://localhost/CNESIS/api/programs/create.php';
        
        console.log('Saving program with data:', formData);
        console.log('Using endpoint:', endpoint);
          
        const response = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formData)
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
          // Handle file uploads if any
          const finalProgramId = data.program_id || programId;
          await handleFileUploads(finalProgramId, formData.code);
          
          alert(isEdit ? 'Program updated successfully!' : 'Program created successfully!');
          const modal = bootstrap.Modal.getInstance(document.getElementById('programModal'));
          modal.hide();
          loadPrograms();
        } else {
          console.error('Server error:', data);
          alert('Failed to save program: ' + data.message);
        }
      } catch (error) {
        console.error('Error saving program:', error);
        console.error('Error details:', error.message);
        console.error('Error stack:', error.stack);
        alert('Error saving program. Please check console for details.');
      }
    }

    /**
     * Handle file uploads
     */
    async function handleFileUploads(programId, programCode) {
      const imageFile = document.getElementById('programImage').files[0];
      const prospectusFile = document.getElementById('prospectusFile').files[0];
      
      // Upload image
      if (imageFile) {
        const imageFormData = new FormData();
        imageFormData.append('image', imageFile);
        imageFormData.append('program_code', programCode);
        imageFormData.append('program_id', programId);
        
        try {
          const imageResponse = await fetch('http://localhost/CNESIS/api/programs/upload-image.php', {
            method: 'POST',
            body: imageFormData
          });
          
          const imageResult = await imageResponse.json();
          if (imageResult.success) {
            console.log('Image uploaded successfully:', imageResult.path);
            // Update the display to show new file
            displayExistingFiles(imageResult.path, null);
          } else {
            console.error('Image upload failed:', imageResult.message);
          }
        } catch (error) {
          console.error('Error uploading image:', error);
        }
      }
      
      // Upload prospectus
      if (prospectusFile) {
        const prospectusFormData = new FormData();
        prospectusFormData.append('prospectus', prospectusFile);
        prospectusFormData.append('program_code', programCode);
        prospectusFormData.append('program_id', programId);
        
        try {
          const prospectusResponse = await fetch('http://localhost/CNESIS/api/programs/upload-prospectus.php', {
            method: 'POST',
            body: prospectusFormData
          });
          
          const prospectusResult = await prospectusResponse.json();
          if (prospectusResult.success) {
            console.log('Prospectus uploaded successfully:', prospectusResult.path);
            // Update the display to show new file
            displayExistingFiles(null, prospectusResult.path);
          } else {
            console.error('Prospectus upload failed:', prospectusResult.message);
          }
        } catch (error) {
          console.error('Error uploading prospectus:', error);
        }
      }
    }
    
    // Delete Program
    function deleteProgram(id, name) {
      if (confirm(`Are you sure you want to delete program ${name}?`)) {
        fetch(`http://localhost/CNESIS/api/programs/delete.php?id=${id}`, {
          method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Program deleted successfully!');
            loadPrograms();
          } else {
            alert('Failed to delete program: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error deleting program:', error);
          alert('Error deleting program. Please try again.');
        });
      }
    }
    
    // Load prospectus download counts
    function loadProspectusDownloadCounts() {
      fetch('http://localhost/CNESIS/api/programs/get-prospectus-downloads.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            data.download_stats.forEach(stat => {
              // Update the download count in the table
              const row = document.querySelector(`tr[data-program-id="${stat.id}"]`);
              if (row) {
                const downloadCell = row.querySelector('.download-count');
                if (downloadCell) {
                  downloadCell.textContent = stat.download_count;
                }
              }
            });
          }
        })
        .catch(error => {
          console.error('Error fetching download counts:', error);
        });
    }
    
    // Add highlight field
    function addHighlight() {
      addListItem('highlightsList', 'highlight', 'Enter program highlight');
    }

    // Add career field
    function addCareer() {
      addListItem('careersList', 'career', 'Enter career opportunity');
    }

    // Add requirement field
    function addRequirement() {
      addListItem('requirementsList', 'requirement', 'Enter admission requirement');
    }

    // Add list item helper
    function addListItem(containerId, name, placeholder) {
      const container = document.getElementById(containerId);
      const index = container.children.length;
      
      const div = document.createElement('div');
      div.className = 'input-group mb-2';
      div.innerHTML = `
        <input type="text" class="form-control" name="${name}[]" placeholder="${placeholder}">
        <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
          <i class="fas fa-times"></i>
        </button>
      `;
      
      container.appendChild(div);
    }

    // Populate list helper
    function populateList(containerId, items, name) {
      const container = document.getElementById(containerId);
      container.innerHTML = '';
      
      if (items && items.length > 0) {
        items.forEach(item => {
          const div = document.createElement('div');
          div.className = 'input-group mb-2';
          div.innerHTML = `
            <input type="text" class="form-control" name="${name}[]" value="${item}">
            <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
              <i class="fas fa-times"></i>
            </button>
          `;
          container.appendChild(div);
        });
      } else {
        addListItem(containerId, name, `Enter ${name}`);
      }
    }

    // Get list values helper
    function getListValues(name) {
      const inputs = document.querySelectorAll(`input[name="${name}[]"]`);
      return Array.from(inputs)
        .map(input => input.value.trim())
        .filter(value => value !== '');
    }
    
    // File preview functions
    function previewImage(input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size
        if (file.size > 5 * 1024 * 1024) {
          alert('Image file size must be less than 5MB');
          input.value = '';
          return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('imagePreviewImg').src = e.target.result;
          document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        document.getElementById('imagePreview').style.display = 'none';
      }
    }
    
    function showFileInfo(input, type) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size
        if (file.size > 10 * 1024 * 1024) {
          alert('File size must be less than 10MB');
          input.value = '';
          return;
        }
        
        if (type === 'prospectus') {
          document.getElementById('prospectusFileName').textContent = file.name;
          document.getElementById('prospectusFileSize').textContent = formatFileSize(file.size);
          document.getElementById('prospectusFileInfo').style.display = 'block';
        }
      } else {
        if (type === 'prospectus') {
          document.getElementById('prospectusFileInfo').style.display = 'none';
        }
      }
    }
    
    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // File management functions
    function resetFileDisplays() {
      // Reset image display
      document.getElementById('currentImageInfo').style.display = 'none';
      document.getElementById('noImageText').style.display = 'block';
      document.getElementById('imagePreview').style.display = 'none';
      document.getElementById('programImage').value = '';
      
      // Reset prospectus display
      document.getElementById('currentProspectusInfo').style.display = 'none';
      document.getElementById('noProspectusText').style.display = 'block';
      document.getElementById('prospectusFileInfo').style.display = 'none';
      document.getElementById('prospectusFile').value = '';
    }
    
    function displayExistingFiles(imagePath, prospectusPath) {
      // Display existing image
      if (imagePath) {
        const fileName = imagePath.split('/').pop() || imagePath;
        document.getElementById('currentImageName').textContent = fileName;
        document.getElementById('currentImagePath').textContent = imagePath;
        document.getElementById('currentImageInfo').style.display = 'block';
        document.getElementById('noImageText').style.display = 'none';
        document.getElementById('removeImageBtn').style.display = 'block';
      } else {
        document.getElementById('currentImageInfo').style.display = 'none';
        document.getElementById('noImageText').style.display = 'block';
        document.getElementById('removeImageBtn').style.display = 'none';
      }
      
      // Display existing prospectus
      if (prospectusPath) {
        const fileName = prospectusPath.split('/').pop() || prospectusPath;
        document.getElementById('currentProspectusName').textContent = fileName;
        document.getElementById('currentProspectusPath').textContent = prospectusPath;
        document.getElementById('currentProspectusInfo').style.display = 'block';
        document.getElementById('noProspectusText').style.display = 'none';
        document.getElementById('removeProspectusBtn').style.display = 'block';
      } else {
        document.getElementById('currentProspectusInfo').style.display = 'none';
        document.getElementById('noProspectusText').style.display = 'block';
        document.getElementById('removeProspectusBtn').style.display = 'none';
      }
    }
    
    function removeCurrentImage() {
      if (confirm('Are you sure you want to remove the current image? This will clear the image path.')) {
        document.getElementById('currentImageInfo').style.display = 'none';
        document.getElementById('noImageText').style.display = 'block';
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('programImage').value = '';
        
        // Update database via API to remove image path
        const programId = document.getElementById('programId').value;
        if (programId) {
          fetch('http://localhost/CNESIS/api/programs/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              id: programId,
              image_path: null
            })
          })
          .then(response => response.json())
          .then(data => {
            if (!data.success) {
              console.error('Failed to remove image path:', data.message);
            }
          })
          .catch(error => {
            console.error('Error removing image path:', error);
          });
        }
      }
    }
    
    function removeCurrentProspectus() {
      if (confirm('Are you sure you want to remove the current prospectus file? This will clear the prospectus path.')) {
        document.getElementById('currentProspectusInfo').style.display = 'none';
        document.getElementById('noProspectusText').style.display = 'block';
        document.getElementById('prospectusFile').value = '';
        
        // Update database via API to remove prospectus path
        const programId = document.getElementById('programId').value;
        if (programId) {
          fetch('http://localhost/CNESIS/api/programs/update.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              id: programId,
              prospectus_path: null
            })
          })
          .then(response => response.json())
          .then(data => {
            if (!data.success) {
              console.error('Failed to remove prospectus path:', data.message);
            }
          })
          .catch(error => {
            console.error('Error removing prospectus path:', error);
          });
        }
      }
    }
    
    // Auto-collapse sidebar on mobile
    if (window.innerWidth <= 768) {
      document.getElementById('sidebar').classList.add('collapsed');
    }
    
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      // Load program heads dropdown - REMOVED
      // loadProgramHeads();
      
      // Add event listeners for filters
      const searchInput = document.getElementById('searchPrograms');
      const categoryFilter = document.getElementById('filterCategory');
      const statusFilter = document.getElementById('filterStatus');
      
      if (searchInput) searchInput.addEventListener('input', filterPrograms);
      if (categoryFilter) categoryFilter.addEventListener('change', filterPrograms);
      if (statusFilter) statusFilter.addEventListener('change', filterPrograms);
      
      loadPrograms();
      loadProspectusDownloadCounts();
    });
    
    /**
     * Filter programs based on search and filters
     */
    function filterPrograms() {
      const searchTerm = document.getElementById('searchPrograms')?.value.toLowerCase() || '';
      const categoryFilter = document.getElementById('filterCategory')?.value || '';
      const statusFilter = document.getElementById('filterStatus')?.value || '';
      
      const allRows = document.querySelectorAll('#programsTable tbody tr');
      
      allRows.forEach(row => {
        const cells = row.cells;
        const code = cells[0]?.textContent.toLowerCase() || '';
        const title = cells[1]?.textContent.toLowerCase() || '';
        const category = cells[2]?.textContent.toLowerCase() || '';
        const department = cells[3]?.textContent.toLowerCase() || '';
        const status = cells[7]?.textContent.toLowerCase() || '';
        
        const matchesSearch = !searchTerm || 
          code.includes(searchTerm) ||
          title.includes(searchTerm) ||
          department.includes(searchTerm);
          
        const matchesCategory = !categoryFilter || category === categoryFilter.toLowerCase();
        const matchesStatus = !statusFilter || status.includes(statusFilter.toLowerCase());
        
        row.style.display = matchesSearch && matchesCategory && matchesStatus ? '' : 'none';
      });
    }
  </script>
</body>
</html>
