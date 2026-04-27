<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Class Schedules - Admin Panel</title>
  
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
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
    }
    
    /* Sidebar base styles live in sidebar.php — only define collapsed state here */
    .sidebar.collapsed { width: 70px; }
    .sidebar.collapsed .sidebar-header h4,
    .sidebar.collapsed .sidebar-header small { opacity: 0; display: none; }
    .sidebar.collapsed .menu-item span { display: none; }
    .sidebar.collapsed .menu-item { justify-content: center; padding: 12px 0; }
    .sidebar.collapsed .menu-item i { margin-right: 0; }

    .main-content {
      margin-left: var(--sidebar-width);
      margin-top: var(--topbar-height);
      padding: 30px;
      transition: margin-left 0.3s ease;
    }
    
    .topbar {
      position: fixed; top: 0; left: var(--sidebar-width); right: 0;
      height: var(--topbar-height); background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 30px; z-index: 999;
      transition: left 0.3s ease;
    }

    .sidebar.collapsed ~ .topbar { left: 70px; }
    .sidebar.collapsed ~ .main-content { margin-left: 70px; }

    /* Mobile — sidebar.php handles the overlay; just reset layout */
    @media (max-width: 768px) {
      .topbar { left: 0 !important; padding: 0 15px !important; }
      .main-content { margin-left: 0 !important; padding: 15px !important; }
      .sidebar.collapsed ~ .topbar { left: 0 !important; }
      .sidebar.collapsed ~ .main-content { margin-left: 0 !important; }
      /* Restore collapsed menu-item text on mobile */
      .sidebar.collapsed .menu-item span { display: inline !important; }
      .sidebar.collapsed .menu-item { justify-content: flex-start !important; padding: 14px 20px !important; }
      .sidebar.collapsed .menu-item i { margin-right: 5px !important; }
    }
    
    .content-card {
      background: white; border-radius: 10px; padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;
    }
    
    .section-header {
      border-bottom: 2px solid #f0f0f0; margin-bottom: 20px; padding-bottom: 10px;
    }
    
    .irregular-badge {
      background: #fef2f2;
      color: #ef4444;
      border: 1px solid #fee2e2;
      font-size: 0.7rem;
      padding: 2px 6px;
      border-radius: 4px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .room-badge {
      display: block;
      font-size: 0.7rem;
      font-weight: 400;
      color: #000;
      margin-top: 2px;
    }

    /* Grid View / Print Styles */
    .print-container {
      background: white;
      padding: 40px;
      width: 100%;
      max-width: 1000px;
      margin: 0 auto;
      border: 1px solid #ddd;
      color: #000;
    }
    
    .print-header {
      text-align: center;
      margin-bottom: 20px;
    }
    
    .print-header img {
      width: 80px;
      height: 80px;
      margin: 0 15px;
    }
    
    .print-header h4 {
      margin: 5px 0;
      font-weight: 700;
      text-transform: uppercase;
    }
    
    .schedule-grid-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    
    .schedule-grid-table th, .schedule-grid-table td {
      border: 1px solid #000;
      padding: 10px;
      text-align: center;
      font-size: 0.85rem;
    }
    
    .schedule-grid-table th {
      background-color: #f8f9fa;
      font-weight: 700;
      text-transform: uppercase;
    }
    
    .grid-header-row {
      background-color: #fff !important;
      text-align: center;
      font-weight: 800;
      font-size: 1.1rem;
    }
    
    .time-col {
      width: 120px;
      font-weight: 700;
    }
    
    .subject-cell {
      padding: 5px !important;
      line-height: 1.2;
    }
    
    .subject-cell .code {
      font-weight: 700;
      display: block;
    }
    
    .subject-cell .title {
      font-size: 0.75rem;
      display: block;
    }
    
    .adviser-row {
      margin-top: 30px;
      text-align: center;
      font-weight: 700;
    }

    @media print {
      body * { visibility: hidden; }
      #gridViewModal, #gridViewModal * { visibility: visible; }
      #gridViewModal { position: absolute; left: 0; top: 0; width: 100%; }
      .modal-footer, .btn-close { display: none !important; }
      .modal-content { border: none !important; }
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>
  
  <div class="topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-link p-0" onclick="toggleSidebar()" style="font-size: 1.3rem; color: var(--primary-blue); text-decoration: none;">
        <i class="fas fa-bars"></i>
      </button>
      <h5 style="margin: 0; color: var(--primary-blue);">Class Schedule Management</h5>
    </div>
    <div class="admin-profile d-flex align-items-center gap-2">
      <div style="font-weight: 600; font-size: 0.9rem;" class="d-none d-sm-block">Admin User</div>
      <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; color: white;">AD</div>
    </div>
  </div>
  
  <div class="main-content">
    <div class="page-header mb-4">
      <h2>Manage Class Schedules</h2>
      <p class="text-muted">Assign and manage subjects for each class section.</p>
    </div>
    
    <div class="row">
      <!-- Section Selection -->
      <div class="col-md-4">
        <div class="content-card">
          <h5 class="section-header">Select Section</h5>
          <div class="mb-3">
            <label class="form-label">Department</label>
            <select class="form-select" id="deptSelect" onchange="loadSections()">
              <option value="">Select Department...</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Section</label>
            <select class="form-select" id="sectionSelect" onchange="onSectionChange()">
              <option value="">Choose section...</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Semester</label>
            <select class="form-select" id="semesterSelect" onchange="onSemesterChange()">
              <option value="1">First Semester</option>
              <option value="2">Second Semester</option>
            </select>
          </div>
          <div id="irregularStudentContainer" class="mt-3 animate-in" style="display: none;">
            <label class="form-label">Select Irregular Student</label>
            <select class="form-select" id="studentSelect" onchange="loadSchedule()">
              <option value="">Choose student...</option>
            </select>
            <small class="text-danger mt-1 d-block fw-bold">Manual schedule entry required for irregular students.</small>
          </div>
          
          <div id="adviserContainer" class="mt-3" style="display: none;">
            <label class="form-label">Class Adviser</label>
            <div class="input-group">
              <input type="text" class="form-control" id="classAdviser" placeholder="e.g., Mr. John Doe">
              <button class="btn btn-outline-primary" type="button" onclick="saveAdviser()">
                <i class="fas fa-save"></i>
              </button>
            </div>
            <small class="text-muted">Adviser for the entire section.</small>
          </div>
        </div>

        <div id="addSubjectContainer" style="display: none;">
          <div class="content-card">
            <h5 class="section-header">Add Subject</h5>
            <div class="mb-3">
              <label class="form-label">Subject Code</label>
              <input type="text" class="form-control" id="subjectCode" placeholder="e.g., CS101">
            </div>
            <div class="mb-3">
              <label class="form-label">Subject Title</label>
              <input type="text" class="form-control" id="subjectTitle" placeholder="e.g., Intro to Computing">
            </div>
            <div class="mb-3">
              <label class="form-label">Day of Week</label>
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
                <label class="form-label">Start Time</label>
                <input type="time" class="form-control" id="subjectStart">
              </div>
              <div class="col">
                <label class="form-label">End Time</label>
                <input type="time" class="form-control" id="subjectEnd">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Instructor</label>
              <input type="text" class="form-control" id="subjectInstructor" placeholder="e.g., Prof. Juan Dela Cruz">
            </div>
            <div class="mb-3">
              <label class="form-label">Room</label>
              <input type="text" class="form-control" id="subjectRoom" placeholder="e.g., Room 101">
            </div>
            <button class="btn btn-primary w-100" onclick="addSubject()">
              <i class="fas fa-plus me-2"></i>Add to Schedule
            </button>
          </div>
        </div>
      </div>
      
      <!-- Schedule Display -->
      <div class="col-md-8">
        <div class="content-card">
          <div class="d-flex justify-content-between align-items-center section-header">
            <h5 class="mb-0">Class Schedule: <span id="displaySectionName" class="text-primary">None Selected</span></h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('importFile').click()">
                <i class="fas fa-file-import me-1"></i> Import Template
              </button>
              <input type="file" id="importFile" style="display: none;" onchange="onFileSelected(this)" accept=".docx,.xlsx,.xls,.csv">
              <button class="btn btn-sm btn-success" onclick="showGridView()">
                <i class="fas fa-th me-1"></i> Grid View / Print
              </button>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Code</th>
                  <th>Subject Title</th>
                  <th>Schedule</th>
                  <th>Instructor</th>
                  <th>Room</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="scheduleTableBody">
                <tr><td colspan="6" class="text-center text-muted">Please select a section to view its schedule.</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Grid View Modal -->
  <div class="modal fade" id="gridViewModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Class Schedule Grid View</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body bg-light">
          <div class="print-container">
            <div class="print-header">
              <div class="d-flex justify-content-center align-items-center mb-3">
                <img src="../../../assets/img/logo.png" alt="School Logo" onerror="this.src='https://via.placeholder.com/80'">
                <div>
                  <h4 class="mb-0">Colegio de Naujan</h4>
                  <p class="mb-0">Barangay Santiago, Naujan, Oriental Mindoro</p>
                  <p class="mb-0">Email: colegiodenaujan@gmail.com</p>
                </div>
                <img src="../../../assets/img/logo.png" alt="School Logo" onerror="this.src='https://via.placeholder.com/80'">
              </div>
              <div class="grid-header-row py-2 border">
                CLASS SCHEDULE FOR <span id="printSemesterText">FIRST</span> SEMESTER, A.Y. 2025-2026
              </div>
            </div>

            <table class="schedule-grid-table">
              <thead>
                <tr>
                  <th class="time-col">TIME</th>
                  <th>MONDAY</th>
                  <th>TUESDAY</th>
                  <th>WEDNESDAY</th>
                  <th>THURSDAY</th>
                  <th>FRIDAY</th>
                  <th>SATURDAY</th>
                  <th>SUNDAY</th>
                </tr>
              </thead>
              <tbody id="gridTableBody">
                <!-- Grid will be generated here -->
              </tbody>
            </table>
            
            <div class="adviser-row mt-4">
              ADVISER: <span id="displayAdviserName" class="border-bottom border-dark px-4">___________________________</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Print Schedule
          </button>
        </div>
      </div>
    </div>
  </div>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let allSections = [];
    let allPrograms = [];
    let allStudents = [];
    let currentSchedules = []; // Store currently loaded schedules for grid view

    function fetchWithTimeout(url, options = {}, timeout = 5000) {
      const controller = new AbortController();
      const id = setTimeout(() => controller.abort(), timeout);
      return fetch(url, { ...options, signal: controller.signal })
        .finally(() => clearTimeout(id));
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Auto-fill subject title from datalist
      document.getElementById('subjectCode').addEventListener('input', function(e) {
        const code = e.target.value;
        const dl = document.getElementById('subjectSuggestions');
        if (!dl) return;
        
        const options = dl.options;
        for (let i = 0; i < options.length; i++) {
          if (options[i].value === code) {
            document.getElementById('subjectTitle').value = options[i].textContent;
            break;
          }
        }
      });

      // Fetch data sequentially to avoid blocking the browser
      fetchWithTimeout('../../../api/programs/get-all.php')
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            allPrograms = data.programs;
            const deptSelect = document.getElementById('deptSelect');
            allPrograms.forEach(p => {
              const opt = document.createElement('option');
              opt.value = p.code;
              opt.textContent = p.code;
              deptSelect.appendChild(opt);
            });
          }
        })
        .then(() => fetchWithTimeout('../../../api/sections/get-all.php'))
        .then(r => r.json())
        .then(data => {
          if (data.success) allSections = data.sections;
        })
        .then(() => fetchWithTimeout('../../../api/students/get-all.php'))
        .then(r => r.json())
        .then(data => {
          if (data.success) allStudents = data.students;
        })
        .catch(err => console.error("Initial load error:", err))
        .finally(() => loadAllSubjects());
    });

    function loadSections() {
      const dept = document.getElementById('deptSelect').value;
      const sectionSelect = document.getElementById('sectionSelect');
      sectionSelect.innerHTML = '<option value="">Select Section...</option>';
      
      if (!dept) return;

      const filtered = allSections.filter(s => 
        s.department_code === dept || s.section_name.startsWith(dept)
      );

      filtered.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = s.section_name;
        sectionSelect.appendChild(opt);
      });
    }

    function onSectionChange() {
      const sectionId = document.getElementById('sectionSelect').value;
      const irrContainer = document.getElementById('irregularStudentContainer');
      const studentSelect = document.getElementById('studentSelect');
      
      studentSelect.innerHTML = '<option value="">Choose student...</option>';
      
      if (!sectionId) {
        irrContainer.style.display = 'none';
        loadSchedule();
        return;
      }

      // Check if this section has irregular students
      const irregulars = allStudents.filter(s => s.section_id == sectionId && s.enrollment_type == 'irregular');
      
      if (irregulars.length > 0) {
        irrContainer.style.display = 'block';
        irregulars.forEach(s => {
          const opt = document.createElement('option');
          opt.value = s.id;
          opt.textContent = `${s.last_name}, ${s.first_name} (${s.student_id})`;
          studentSelect.appendChild(opt);
        });
      } else {
        irrContainer.style.display = 'none';
      }

      loadSchedule();
    }

    function onSemesterChange() {
      const sectionId = document.getElementById('sectionSelect').value;
      const studentId = document.getElementById('studentSelect').value;
      if (sectionId) {
        loadFilteredSubjects(sectionId, !!studentId);
      }
      loadSchedule();
    }

    function loadSchedule(silent = false) {
      const sectionSelect = document.getElementById('sectionSelect');
      const sectionId = sectionSelect.value;
      const studentId = document.getElementById('studentSelect').value;
      const semester = document.getElementById('semesterSelect').value;
      const sectionName = sectionSelect.selectedIndex >= 0 ? sectionSelect.options[sectionSelect.selectedIndex].text : 'None Selected';
      const tbody = document.getElementById('scheduleTableBody');
      const container = document.getElementById('addSubjectContainer');
      const advContainer = document.getElementById('adviserContainer');

      if (!sectionId) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Please select a section to view its schedule.</td></tr>';
        document.getElementById('displaySectionName').textContent = 'None Selected';
        container.style.display = 'none';
        advContainer.style.display = 'none';
        currentSchedules = [];
        updateGridView();
        return;
      }

      const isIrregular = !!studentId;
      document.getElementById('displaySectionName').innerHTML = sectionName + (isIrregular ? ' <span class="irregular-badge">Irregular</span>' : '');
      container.style.display = 'block';
      advContainer.style.display = isIrregular ? 'none' : 'block';
      
      if (!isIrregular) {
        const section = allSections.find(s => s.id == sectionId);
        document.getElementById('classAdviser').value = section ? (section.adviser || '') : '';
      }

      if (!silent) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Loading schedule...</td></tr>';
      }

      // Load subjects based on section context (prospectus filtering)
      loadFilteredSubjects(sectionId, isIrregular);

      const apiUrl = isIrregular 
        ? `../../../api/schedules/get-by-student.php?student_id=${studentId}&semester=${semester}&t=${Date.now()}`
        : `../../../api/schedules/get-by-section.php?section_id=${sectionId}&semester=${semester}&t=${Date.now()}`;

      fetchWithTimeout(apiUrl)
        .then(r => r.json())
        .then(data => {
          currentSchedules = data.success ? data.schedules : [];
          renderScheduleTable();
          updateGridView();
        })
        .catch(err => {
          console.error(err);
          tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading schedule. Please try again.</td></tr>';
        });
    }

    function renderScheduleTable() {
      const tbody = document.getElementById('scheduleTableBody');
      tbody.innerHTML = '';
      
      if (currentSchedules.length > 0) {
        currentSchedules.forEach(s => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td><strong>${s.subject_code}</strong></td>
            <td>${s.subject_title}</td>
            <td><span class="badge bg-light text-dark">${s.day_of_week}</span><br><small>${s.start_time.substring(0,5)} - ${s.end_time.substring(0,5)}</small></td>
            <td>${s.instructor_name || 'N/A'}</td>
            <td>${s.room || 'N/A'}</td>
            <td>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule(${s.id})">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        const studentId = document.getElementById('studentSelect').value;
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">No subjects assigned to this ${!!studentId ? 'student' : 'section'}.</td></tr>`;
      }
    }

    function showGridView() {
      updateGridView();
      const modal = new bootstrap.Modal(document.getElementById('gridViewModal'));
      modal.show();
    }

    function updateGridView() {
      const semester = document.getElementById('semesterSelect').value;
      const printSemesterText = document.getElementById('printSemesterText');
      if (printSemesterText) printSemesterText.textContent = semester == 1 ? 'FIRST' : 'SECOND';
      
      const gridTbody = document.getElementById('gridTableBody');
      if (!gridTbody) return;
      
      gridTbody.innerHTML = '';

      if (currentSchedules.length === 0) {
        gridTbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">No schedules to display.</td></tr>';
        return;
      }

      // Update Adviser Display
      const sectionId = document.getElementById('sectionSelect').value;
      const studentId = document.getElementById('studentSelect').value;
      const displayAdv = document.getElementById('displayAdviserName');
      if (displayAdv) {
        if (studentId) {
          displayAdv.textContent = '___________________________'; // N/A for individual students
        } else {
          const section = allSections.find(s => s.id == sectionId);
          displayAdv.textContent = (section && section.adviser) ? section.adviser.toUpperCase() : '___________________________';
        }
      }

      // 1. Determine the range of time
      let minHour = 8; // Default start at 8 AM
      let maxHour = 17; // Default end at 5 PM

      currentSchedules.forEach(s => {
        const startH = parseInt(s.start_time.split(':')[0]);
        const endH = parseInt(s.end_time.split(':')[0]);
        if (startH < minHour) minHour = startH;
        if (endH >= maxHour) maxHour = endH + 1;
      });

      // 2. Generate dynamic time slots (1.5 hour blocks)
      const timeSlots = [];
      for (let hour = minHour; hour < maxHour; hour += 1.5) {
        const startH = Math.floor(hour);
        const startM = (hour % 1) * 60;
        const endHour = hour + 1.5;
        const endH = Math.floor(endHour);
        const endM = (endHour % 1) * 60;

        const formatTime = (h, m) => {
          const ampm = h >= 12 ? 'PM' : 'AM';
          const displayH = h > 12 ? h - 12 : (h === 0 ? 12 : h);
          return `${displayH}:${m.toString().padStart(2, '0')}`;
        };

        const format24 = (h, m) => `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`;

        timeSlots.push({
          label: `${formatTime(startH, startM)}-${formatTime(endH, endM)}`,
          start: format24(startH, startM),
          end: format24(endH, endM)
        });
      }
      
      const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
      
      timeSlots.forEach(slot => {
        const tr = document.createElement('tr');
        const timeTd = document.createElement('td');
        timeTd.className = 'time-col';
        timeTd.textContent = slot.label;
        tr.appendChild(timeTd);
        
        days.forEach(day => {
          const td = document.createElement('td');
          td.className = 'subject-cell';
          
          const match = currentSchedules.find(s => {
            if (s.day_of_week !== day) return false;
            const sStart = s.start_time.substring(0,5);
            const sEnd = s.end_time.substring(0,5);
            return sStart < slot.end && sEnd > slot.start;
          });
          
          if (match) {
            td.innerHTML = `<span class="code">${match.subject_code}</span><span class="title">${match.subject_title}</span>`;
            if (match.room) td.innerHTML += `<div class="room-badge mt-1">${match.room}</div>`;
          }
          
          tr.appendChild(td);
        });
        
        gridTbody.appendChild(tr);
      });
    }

    function onFileSelected(input) {
      if (!input.files || !input.files[0]) return;
      
      const sectionId = document.getElementById('sectionSelect').value;
      const semester = document.getElementById('semesterSelect').value;
      
      if (!sectionId) {
        alert('Please select a section before importing.');
        input.value = '';
        return;
      }

      if (!confirm('Importing will replace the current schedule for this section/semester. Continue?')) {
        input.value = '';
        return;
      }

      const formData = new FormData();
      formData.append('file', input.files[0]);
      formData.append('section_id', sectionId);
      formData.append('semester', semester);

      fetch('../../../api/schedules/import-docx.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          loadSchedule(true); // Silent update
        } else {
          alert('Import failed: ' + data.message);
        }
        input.value = '';
      })
      .catch(err => {
        console.error(err);
        alert('Error during import.');
        input.value = '';
      });
    }

    function loadFilteredSubjects(sectionId, isIrregular = false) {
      const section = allSections.find(s => s.id == sectionId);
      const programCode = document.getElementById('deptSelect').value;
      const yearLevel = (section && !isIrregular) ? section.year_level : null;
      const semester = document.getElementById('semesterSelect').value;
      
      // Update datalist for suggestions
      const dl = document.getElementById('subjectSuggestions');
      if (!dl) {
        const newDl = document.createElement('datalist');
        newDl.id = 'subjectSuggestions';
        document.body.appendChild(newDl);
        document.getElementById('subjectCode').setAttribute('list', 'subjectSuggestions');
      }

      const queryParams = new URLSearchParams();
      if (programCode) queryParams.append('program_code', programCode);
      if (yearLevel) queryParams.append('year_level', yearLevel);
      if (semester) queryParams.append('semester', semester);

      fetchWithTimeout(`../../../api/subjects/get-filtered.php?${queryParams.toString()}`)
        .then(r => r.json())
        .then(data => {
          const dl = document.getElementById('subjectSuggestions');
          dl.innerHTML = '';
          if (data.success && data.subjects.length > 0) {
            data.subjects.forEach(sub => {
              const opt = document.createElement('option');
              opt.value = sub.subject_code;
              opt.textContent = sub.subject_title;
              dl.appendChild(opt);
            });
          }
        })
        .catch(err => console.error(err));
    }

    function loadAllSubjects() {
      // Keep datalist updated with all subjects as fallback
      fetchWithTimeout('../../../api/subjects/get-all.php')
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            const dl = document.getElementById('subjectSuggestions');
            if (!dl) return;
            const existingCodes = new Set([...dl.options].map(o => o.value));
            data.subjects.forEach(sub => {
              if (!existingCodes.has(sub.subject_code)) {
                const opt = document.createElement('option');
                opt.value = sub.subject_code;
                opt.textContent = sub.subject_title;
                dl.appendChild(opt);
              }
            });
          }
        });
    }

    function addSubject() {
      const btn = event.target.closest('button');
      const originalText = btn.innerHTML;
      
      const sectionId = document.getElementById('sectionSelect').value;
      const studentId = document.getElementById('studentSelect').value;
      const semester = document.getElementById('semesterSelect').value;
      const payload = {
        section_id: sectionId,
        student_id: studentId || null,
        semester: semester,
        subject_code: document.getElementById('subjectCode').value.trim(),
        subject_title: document.getElementById('subjectTitle').value.trim(),
        day_of_week: document.getElementById('subjectDay').value,
        start_time: document.getElementById('subjectStart').value,
        end_time: document.getElementById('subjectEnd').value,
        instructor_name: document.getElementById('subjectInstructor').value,
        room: document.getElementById('subjectRoom').value
      };

      if (!payload.subject_code || !payload.subject_title || !payload.start_time || !payload.end_time) {
        alert('Please fill in all required fields (Code, Title, Time)');
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';

      fetch('../../../api/schedules/create.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          loadSchedule(true); // Silent update
          // Reset fields
          document.getElementById('subjectCode').value = '';
          document.getElementById('subjectTitle').value = '';
          document.getElementById('subjectInstructor').value = '';
          document.getElementById('subjectRoom').value = '';
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        console.error(err);
        alert('An error occurred while adding the subject.');
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
      });
    }

    function deleteSchedule(id) {
      if (!confirm('Remove this subject from the schedule?')) return;
      fetch('../../../api/schedules/delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          loadSchedule(true); // Silent update
        }
      });
    }

    function saveAdviser() {
      const sectionId = document.getElementById('sectionSelect').value;
      const adviser = document.getElementById('classAdviser').value.trim();
      const btn = event.target.closest('button');
      
      if (!sectionId) return;

      btn.disabled = true;
      const originalIcon = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

      fetch('../../../api/sections/save-adviser.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ section_id: sectionId, adviser: adviser })
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          // Update local data
          const section = allSections.find(s => s.id == sectionId);
          if (section) section.adviser = adviser;
          updateGridView();
          alert('Adviser saved successfully!');
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => console.error(err))
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalIcon;
      });
    }
  </script>
</body>
</html>