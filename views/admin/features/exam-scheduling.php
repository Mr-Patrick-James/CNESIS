<?php
require_once '../../../api/config/database.php';
$database = new Database();
$db = $database->getConnection();

// Check and create exam_schedules table if not exists
try {
    $checkTable = $db->query("SHOW TABLES LIKE 'exam_schedules'");
    if ($checkTable->rowCount() == 0) {
        $sql = "CREATE TABLE IF NOT EXISTS exam_schedules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            batch_name VARCHAR(100) NOT NULL,
            exam_date DATE NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            venue VARCHAR(255) NOT NULL,
            max_slots INT NOT NULL,
            current_slots INT DEFAULT 0,
            status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->exec($sql);
        
        // Add exam_schedule_id to admissions table
        $checkCol = $db->query("SHOW COLUMNS FROM admissions LIKE 'exam_schedule_id'");
        if ($checkCol->rowCount() == 0) {
            $db->exec("ALTER TABLE admissions ADD COLUMN exam_schedule_id INT NULL DEFAULT NULL AFTER status");
            $db->exec("ALTER TABLE admissions ADD CONSTRAINT fk_exam_schedule FOREIGN KEY (exam_schedule_id) REFERENCES exam_schedules(id) ON DELETE SET NULL");
        }
    }
} catch (PDOException $e) {
    // Log error or show alert
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Scheduling - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Using the same styles as other admin pages -->
    <style>
        :root {
            --primary-blue: #1a365d;
            --secondary-blue: #2c5282;
            --accent-gold: #d4af37;
            --sidebar-width: 260px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
        }
        
        .card {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #eee;
            padding: 20px;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-blue);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-active { background-color: #e3f2fd; color: #0d47a1; }
        .status-completed { background-color: #e8f5e9; color: #1b5e20; }
        .status-cancelled { background-color: #ffebee; color: #b71c1c; }

        /* Sidebar Styles */
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

        /* Table Header Style */
        .table thead {
            background-color: var(--primary-blue);
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
        }

        /* Sorting Styles */
        .sortable {
            cursor: pointer;
            position: relative;
            padding-right: 30px !important;
            transition: background-color 0.2s;
        }
        
        .sortable:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sortable:after {
            content: "\f0dc";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 10px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.8rem;
        }
        
        .sortable.asc:after {
            content: "\f0de";
            color: white;
        }
        
        .sortable.desc:after {
            content: "\f0dd";
            color: white;
        }
    </style>
</head>
<body>

    <!-- Sidebar placeholder (loaded via JS) -->
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Exam Scheduling</h2>
                <p class="text-muted">Manage entrance exam batches and schedules</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                <i class="fas fa-plus me-2"></i>Create New Batch
            </button>
        </div>

        <?php if (isset($dbError)): ?>
        <div class="alert alert-danger">
            Database Setup Error: <?php echo htmlspecialchars($dbError); ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Active Schedules -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Active Exam Batches</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="schedulesTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" onclick="sortSchedules('batch_name', this)">Batch Name</th>
                                        <th class="sortable" onclick="sortSchedules('exam_date', this)">Date & Time</th>
                                        <th class="sortable" onclick="sortSchedules('venue', this)">Venue</th>
                                        <th class="sortable" onclick="sortSchedules('current_slots', this)">Slots</th>
                                        <th class="sortable" onclick="sortSchedules('status', this)">Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="schedulesTableBody">
                                    <!-- Populated via JS -->
                                    <tr><td colspan="5" class="text-center">Loading schedules...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unscheduled Students (Scheduling Pool) -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Applicants in Scheduling Pool</h5>
                        <div>
                            <button class="btn btn-outline-primary btn-sm" id="assignSelectedBtn" disabled>
                                Assign Selected to Batch
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="studentsTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAllStudents"></th>
                                        <th class="sortable" onclick="sortStudents('first_name', this)">Student Name</th>
                                        <th class="sortable" onclick="sortStudents('program_code', this)">Program</th>
                                        <th class="sortable" onclick="sortStudents('email', this)">Email</th>
                                        <th class="sortable" onclick="sortStudents('submitted_at', this)">Submitted Date</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <!-- Populated via JS -->
                                    <tr><td colspan="5" class="text-center">Loading students...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Schedule Modal -->
    <div class="modal fade" id="createScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Exam Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createScheduleForm">
                        <div class="mb-3">
                            <label class="form-label">Batch Name</label>
                            <input type="text" class="form-control" name="batch_name" required placeholder="e.g., Batch A - Morning">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Exam Date</label>
                            <input type="date" class="form-control" name="exam_date" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Time</label>
                                <input type="time" class="form-control" name="start_time" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Time</label>
                                <input type="time" class="form-control" name="end_time" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Venue</label>
                            <input type="text" class="form-control" name="venue" required placeholder="e.g., Room 101, Main Building">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max Slots</label>
                            <input type="number" class="form-control" name="max_slots" required min="1" value="30">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveScheduleBtn">Create Batch</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Schedule Modal -->
    <div class="modal fade" id="editScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Exam Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editScheduleForm">
                        <input type="hidden" name="id" id="editBatchId">
                        <div class="mb-3">
                            <label class="form-label">Batch Name</label>
                            <input type="text" class="form-control" name="batch_name" id="editBatchNameInput" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Exam Date</label>
                            <input type="date" class="form-control" name="exam_date" id="editExamDateInput" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Time</label>
                                <input type="time" class="form-control" name="start_time" id="editStartTimeInput" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Time</label>
                                <input type="time" class="form-control" name="end_time" id="editEndTimeInput" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Venue</label>
                            <input type="text" class="form-control" name="venue" id="editVenueInput" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max Slots</label>
                            <input type="number" class="form-control" name="max_slots" id="editMaxSlotsInput" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="editStatusInput">
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="notifyStudentsOnUpdate" checked>
                            <label class="form-check-label" for="notifyStudentsOnUpdate">Notify assigned students of changes via email</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateScheduleBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Batch Modal -->
    <div class="modal fade" id="assignBatchModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Students to Batch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Assign <span id="selectedCount">0</span> student(s) to:</p>
                    <select class="form-select" id="targetBatchSelect">
                        <option value="">Select a batch...</option>
                        <!-- Populated via JS -->
                    </select>
                    <div class="mt-3 form-check">
                        <input type="checkbox" class="form-check-input" id="sendEmailCheck" checked>
                        <label class="form-check-label" for="sendEmailCheck">Send email notification to students</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAssignBtn">Confirm Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Batch Students Modal -->
    <div class="modal fade" id="viewBatchStudentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Students in <span id="viewBatchName">Batch</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <select class="form-select form-select-sm me-2" id="bulkStatusUpdate" style="width: 200px;">
                                <option value="">Update Status...</option>
                                <option value="examed">Examed</option>
                                <option value="did not attend">Did Not Attend</option>
                                <option value="reschedule">Reschedule</option>
                            </select>
                            <button class="btn btn-sm btn-primary me-2" id="applyBulkStatusBtn" disabled>Apply</button>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-file-export me-1"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="exportBatch('excel')"><i class="fas fa-file-excel me-2 text-success"></i>Excel</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportBatch('csv')"><i class="fas fa-file-csv me-2 text-primary"></i>CSV</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportBatch('pdf')"><i class="fas fa-file-pdf me-2 text-danger"></i>PDF (Print)</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportBatch('docx')"><i class="fas fa-file-word me-2 text-info"></i>Word (DOCX)</a></li>
                                </ul>
                            </div>
                        </div>
                        <span class="text-muted small" id="selectedExamineesCount">0 selected</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAllExaminees"></th>
                                    <th>Student Name</th>
                                    <th>Program</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="batchStudentsTableBody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let globalSchedules = [];
        let globalStudents = [];
        let schedulesSort = { column: '', direction: 'asc' };
        let studentsSort = { column: '', direction: 'asc' };

        let assignBatchModal = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadSchedules();
            loadUnscheduledStudents();
            
            // Create Schedule
            document.getElementById('saveScheduleBtn').addEventListener('click', function() {
                const form = document.getElementById('createScheduleForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                
                // Loading state
                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';
                
                fetch('../../../api/exams/create.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire('Success', 'Exam batch created successfully', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('createScheduleModal')).hide();
                        form.reset();
                        loadSchedules();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Failed to create schedule', 'error'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
            
            // Select All Logic
            document.getElementById('selectAllStudents').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.student-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateAssignButton();
            });
            
            // Assign Button Logic
            document.getElementById('assignSelectedBtn').addEventListener('click', function() {
                const selected = document.querySelectorAll('.student-checkbox:checked');
                if (selected.length === 0) return;
                
                document.getElementById('selectedCount').textContent = selected.length;
                
                // Populate batch select
                const select = document.getElementById('targetBatchSelect');
                select.innerHTML = '<option value="">Select a batch...</option>';
                
                // Get batches from the table data (assuming loaded)
                // Better to fetch active batches specifically for dropdown
                fetch('../../../api/exams/get-all.php?status=active&t=' + new Date().getTime())
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        res.schedules.forEach(schedule => {
                            const option = document.createElement('option');
                            option.value = schedule.id;
                            option.textContent = `${schedule.batch_name} (${schedule.current_slots}/${schedule.max_slots} slots)`;
                            if (schedule.current_slots >= schedule.max_slots) {
                                option.disabled = true;
                                option.textContent += ' - FULL';
                            }
                            select.appendChild(option);
                        });
                        
                        if (!assignBatchModal) {
                            assignBatchModal = new bootstrap.Modal(document.getElementById('assignBatchModal'));
                        }
                        assignBatchModal.show();
                    }
                });
            });
            
            // Update Batch Logic
            document.getElementById('updateScheduleBtn').addEventListener('click', function() {
                const form = document.getElementById('editScheduleForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                data.notify = document.getElementById('notifyStudentsOnUpdate').checked;
                
                // Loading state
                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
                
                fetch('../../../api/exams/update.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        let msg = 'Exam batch updated successfully';
                        if (res.notifications_sent > 0) msg += ` and ${res.notifications_sent} students notified`;
                        Swal.fire('Success', msg, 'success');
                        if (editScheduleModal) editScheduleModal.hide();
                        loadSchedules();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Failed to update schedule', 'error'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
            
            // Confirm Assign
            document.getElementById('confirmAssignBtn').addEventListener('click', function() {
                const batchId = document.getElementById('targetBatchSelect').value;
                if (!batchId) {
                    Swal.fire('Error', 'Please select a batch', 'error');
                    return;
                }
                
                const selected = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
                const sendEmail = document.getElementById('sendEmailCheck').checked;
                
                // Loading state
                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                
                fetch('../../../api/exams/assign.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        exam_schedule_id: batchId,
                        student_ids: selected,
                        send_email: sendEmail
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire('Success', res.message, 'success');
                        if (assignBatchModal) assignBatchModal.hide();
                        loadUnscheduledStudents();
                        loadSchedules();
                        document.getElementById('selectAllStudents').checked = false;
                        updateAssignButton();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Failed to assign students', 'error'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            });
        });
        
        function updateAssignButton() {
            const count = document.querySelectorAll('.student-checkbox:checked').length;
            const btn = document.getElementById('assignSelectedBtn');
            btn.disabled = count === 0;
            btn.textContent = count > 0 ? `Assign ${count} Selected to Batch` : 'Assign Selected to Batch';
        }
        
        function loadSchedules() {
            fetch('../../../api/exams/get-all.php?t=' + new Date().getTime())
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    globalSchedules = res.schedules || [];
                    renderSchedules();
                } else {
                    document.getElementById('schedulesTableBody').innerHTML = '<tr><td colspan="5" class="text-center">No active schedules found</td></tr>';
                }
            })
            .catch(err => console.error(err));
        }

        function renderSchedules() {
            const tbody = document.getElementById('schedulesTableBody');
            tbody.innerHTML = '';
            
            if (globalSchedules.length > 0) {
                globalSchedules.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.style.cursor = 'pointer';
                    tr.innerHTML = `
                        <td>${s.batch_name}</td>
                        <td>${s.exam_date} <br><small class="text-muted">${s.start_time} - ${s.end_time}</small></td>
                        <td>${s.venue}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" style="width: ${(s.current_slots/s.max_slots)*100}%">
                                    ${s.current_slots}/${s.max_slots}
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge status-${s.status}">${s.status.charAt(0).toUpperCase() + s.status.slice(1)}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditBatchModal(${s.id}, event)"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteSchedule(${s.id}, event)"><i class="fas fa-trash"></i></button>
                        </td>
                    `;
                    
                    tr.addEventListener('click', function(e) {
                        if (e.target.closest('button') || e.target.closest('input')) return;
                        viewBatchStudents(s);
                    });
                    
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No active schedules found</td></tr>';
            }
        }

        let editScheduleModal = null;
        function openEditBatchModal(batchId, event) {
            if (event) event.stopPropagation();
            
            const batch = globalSchedules.find(s => s.id == batchId);
            if (!batch) return;
            
            document.getElementById('editBatchId').value = batch.id;
            document.getElementById('editBatchNameInput').value = batch.batch_name;
            document.getElementById('editExamDateInput').value = batch.exam_date;
            document.getElementById('editStartTimeInput').value = batch.start_time;
            document.getElementById('editEndTimeInput').value = batch.end_time;
            document.getElementById('editVenueInput').value = batch.venue;
            document.getElementById('editMaxSlotsInput').value = batch.max_slots;
            document.getElementById('editStatusInput').value = batch.status;
            
            if (!editScheduleModal) {
                editScheduleModal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
            }
            editScheduleModal.show();
        }

        let batchStudentsModal = null;

        let currentViewingBatch = null;

        function viewBatchStudents(batch, showModal = true) {
            currentViewingBatch = batch;
            document.getElementById('viewBatchName').textContent = batch.batch_name;
            const tbody = document.getElementById('batchStudentsTableBody');
            tbody.innerHTML = '<tr><td colspan="5" class="text-center"><span class="spinner-border spinner-border-sm"></span> Loading...</td></tr>';
            
            // Reset modal state
            document.getElementById('selectAllExaminees').checked = false;
            document.getElementById('bulkStatusUpdate').value = '';
            updateBulkStatusButton();
            
            if (showModal) {
                if (!batchStudentsModal) {
                    batchStudentsModal = new bootstrap.Modal(document.getElementById('viewBatchStudentsModal'));
                }
                batchStudentsModal.show();
            }
            
            fetch('../../../api/admissions/get-all.php?t=' + new Date().getTime())
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        const students = res.admissions.filter(a => a.exam_schedule_id == batch.id);
                        tbody.innerHTML = '';
                        
                        if (students.length > 0) {
                            students.forEach(s => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td><input type="checkbox" class="form-check-input examinee-checkbox" value="${s.id}" ${s.status === 'examed' ? 'disabled title="Examed students cannot be modified"' : ''} onchange="updateBulkStatusButton()"></td>
                                    <td>${s.first_name} ${s.last_name}</td>
                                    <td>${s.program_title || s.program_code}</td>
                                    <td>${s.email}</td>
                                    <td>
                                        ${s.status === 'examed' ? 
                                            '<span class="badge bg-success">Examed</span>' : 
                                            `<select class="form-select form-select-sm status-select" onchange="updateIndividualStatus(${s.id}, this.value, '${s.first_name} ${s.last_name}')">
                                                <option value="scheduled" ${s.status === 'scheduled' ? 'selected' : ''}>Scheduling</option>
                                                <option value="examed" ${s.status === 'examed' ? 'selected' : ''}>Examed</option>
                                                <option value="did not attend" ${s.status === 'did not attend' ? 'selected' : ''}>Did Not Attend</option>
                                                <option value="reschedule" ${s.status === 'reschedule' ? 'selected' : ''}>Reschedule</option>
                                            </select>`
                                        }
                                    </td>
                                `;
                                tr.addEventListener('click', function(e) {
                                     if (e.target.type !== 'checkbox' && !e.target.classList.contains('status-select')) {
                                         const cb = this.querySelector('.examinee-checkbox');
                                         cb.checked = !cb.checked;
                                         updateBulkStatusButton();
                                     }
                                 });
                                tbody.appendChild(tr);
                            });
                        } else {
                            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No students assigned to this batch</td></tr>';
                        }
                    }
                })
                .catch(err => {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading students</td></tr>';
                });
        }

        function updateBulkStatusButton() {
            const selected = document.querySelectorAll('.examinee-checkbox:checked');
            const btn = document.getElementById('applyBulkStatusBtn');
            const countDisplay = document.getElementById('selectedExamineesCount');
            const statusSelect = document.getElementById('bulkStatusUpdate');
            
            btn.disabled = selected.length === 0 || !statusSelect.value;
            countDisplay.textContent = `${selected.length} selected`;
        }

        // Add event listeners for the examinee modal
        document.addEventListener('DOMContentLoaded', function() {
            // Select All Examinees
            document.getElementById('selectAllExaminees').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.examinee-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkStatusButton();
            });

            // Status select change
            document.getElementById('bulkStatusUpdate').addEventListener('change', updateBulkStatusButton);

            // Apply bulk status update
            document.getElementById('applyBulkStatusBtn').addEventListener('click', function() {
                const selected = Array.from(document.querySelectorAll('.examinee-checkbox:checked')).map(cb => cb.value);
                const status = document.getElementById('bulkStatusUpdate').value;
                
                if (selected.length === 0 || !status) return;
                
                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                
                fetch('../../../api/exams/update-examinees-status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        student_ids: selected,
                        status: status
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire('Success', res.message, 'success');
                        // Refresh the modal content
                        const currentBatchId = globalSchedules.find(s => s.batch_name === document.getElementById('viewBatchName').textContent)?.id;
                        if (currentBatchId) {
                            viewBatchStudents({ id: currentBatchId, batch_name: document.getElementById('viewBatchName').textContent }, false);
                        }
                        loadUnscheduledStudents();
                        loadSchedules();
                     } else {
                         Swal.fire('Error', res.message, 'error');
                     }
                 })
                 .catch(err => Swal.fire('Error', 'Failed to update statuses', 'error'))
                 .finally(() => {
                     btn.disabled = false;
                     btn.innerHTML = originalText;
                 });
             });
         });

         function updateIndividualStatus(admissionId, status, name) {
             Swal.fire({
                 title: 'Update Status?',
                 text: `Change ${name}'s status to ${status}?`,
                 icon: 'question',
                 showCancelButton: true,
                 confirmButtonText: 'Yes, update'
             }).then((result) => {
                 if (result.isConfirmed) {
                     fetch('../../../api/admissions/update-status.php', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json' },
                         body: JSON.stringify({
                             admission_id: admissionId,
                             status: status,
                             notes: `Status manually updated in exam scheduling modal to ${status}`
                         })
                     })
                     .then(res => res.json())
                     .then(res => {
                         if (res.success) {
                             Swal.fire('Updated!', res.message, 'success');
                             const currentBatchId = globalSchedules.find(s => s.batch_name === document.getElementById('viewBatchName').textContent)?.id;
                             if (currentBatchId) {
                                 viewBatchStudents({ id: currentBatchId, batch_name: document.getElementById('viewBatchName').textContent }, false);
                             }
                             loadUnscheduledStudents();
                             loadSchedules();
                         } else {
                             Swal.fire('Error', res.message, 'error');
                             // Refresh modal to revert the select value if needed
                             const currentBatchId = globalSchedules.find(s => s.batch_name === document.getElementById('viewBatchName').textContent)?.id;
                             if (currentBatchId) {
                                 viewBatchStudents({ id: currentBatchId, batch_name: document.getElementById('viewBatchName').textContent }, false);
                             }
                         }
                     })
                     .catch(err => Swal.fire('Error', 'Failed to update status', 'error'));
                 } else {
                     // Revert the select if cancelled
                     const currentBatchId = globalSchedules.find(s => s.batch_name === document.getElementById('viewBatchName').textContent)?.id;
                     if (currentBatchId) {
                         viewBatchStudents({ id: currentBatchId, batch_name: document.getElementById('viewBatchName').textContent }, false);
                     }
                 }
             });
         }

         function exportBatch(format) {
            if (!currentViewingBatch) return;
            const batchId = currentViewingBatch.id;
            const url = `../../../api/exams/export-batch.php?batch_id=${batchId}&format=${format}&t=${new Date().getTime()}`;
            
            if (format === 'pdf') {
                // PDF is usually a printable window
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        }

        function sortSchedules(column, el) {
            // Reset icons
            document.querySelectorAll('#schedulesTable th.sortable').forEach(th => {
                if (th !== el) th.classList.remove('asc', 'desc');
            });

            // Toggle direction
            if (schedulesSort.column === column) {
                schedulesSort.direction = schedulesSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                schedulesSort.column = column;
                schedulesSort.direction = 'asc';
            }

            // Update UI
            el.classList.remove('asc', 'desc');
            el.classList.add(schedulesSort.direction);

            // Sort data
            globalSchedules.sort((a, b) => {
                let valA = a[column];
                let valB = b[column];

                // Handle numeric slots
                if (column === 'current_slots') {
                    valA = parseInt(valA);
                    valB = parseInt(valB);
                }

                if (valA < valB) return schedulesSort.direction === 'asc' ? -1 : 1;
                if (valA > valB) return schedulesSort.direction === 'asc' ? 1 : -1;
                return 0;
            });

            renderSchedules();
        }

        function loadUnscheduledStudents() {
            fetch('../../../api/exams/get-unscheduled.php?t=' + new Date().getTime())
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    globalStudents = res.students || [];
                    renderStudents();
                } else {
                    document.getElementById('studentsTableBody').innerHTML = '<tr><td colspan="5" class="text-center">No students pending scheduling</td></tr>';
                }
            })
            .catch(err => console.error(err));
        }

        function renderStudents() {
            const tbody = document.getElementById('studentsTableBody');
            tbody.innerHTML = '';
            
            if (globalStudents.length > 0) {
                globalStudents.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.style.cursor = 'pointer';
                    tr.innerHTML = `
                        <td><input type="checkbox" class="form-check-input student-checkbox" value="${s.id}" onchange="updateAssignButton()"></td>
                        <td>${s.first_name} ${s.last_name}</td>
                        <td>${s.program_code}</td>
                        <td>${s.email}</td>
                        <td>${s.submitted_at}</td>
                    `;
                    
                    // Add click listener to the whole row
                    tr.addEventListener('click', function(e) {
                        // Don't toggle if the checkbox itself was clicked
                        if (e.target.type !== 'checkbox') {
                            const cb = this.querySelector('.student-checkbox');
                            cb.checked = !cb.checked;
                            updateAssignButton();
                        }
                    });
                    
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No students pending scheduling</td></tr>';
            }
        }

        function sortStudents(column, el) {
            // Reset icons
            document.querySelectorAll('#studentsTable th.sortable').forEach(th => {
                if (th !== el) th.classList.remove('asc', 'desc');
            });

            // Toggle direction
            if (studentsSort.column === column) {
                studentsSort.direction = studentsSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                studentsSort.column = column;
                studentsSort.direction = 'asc';
            }

            // Update UI
            el.classList.remove('asc', 'desc');
            el.classList.add(studentsSort.direction);

            // Sort data
            globalStudents.sort((a, b) => {
                let valA = a[column];
                let valB = b[column];

                if (column === 'first_name') {
                    valA = `${a.first_name} ${a.last_name}`.toLowerCase();
                    valB = `${b.first_name} ${b.last_name}`.toLowerCase();
                } else if (valA && typeof valA === 'string') {
                    valA = valA.toLowerCase();
                    valB = valB.toLowerCase();
                }

                if (valA < valB) return studentsSort.direction === 'asc' ? -1 : 1;
                if (valA > valB) return studentsSort.direction === 'asc' ? 1 : -1;
                return 0;
            });

            renderStudents();
        }

        function deleteSchedule(id, event) {
            if (event) event.stopPropagation();
            Swal.fire({
                title: 'Are you sure?',
                text: "This will remove the batch and return assigned students to 'Approved' status.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../../../api/exams/delete.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id: id })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Deleted!', res.message, 'success');
                            loadSchedules();
                            loadUnscheduledStudents();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Failed to connect to server', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
