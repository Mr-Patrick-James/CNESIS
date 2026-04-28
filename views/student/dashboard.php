<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../../index.php?error=unauthorized');
    exit;
}

require_once '../../api/config/database.php';
$database = new Database();
$db = $database->getConnection();

$fullName = $_SESSION['full_name'];
$email = $_SESSION['email'];

// Get student details to find their section
$stmt = $db->prepare("SELECT s.*, sec.section_name, d.department_name 
                      FROM students s 
                      LEFT JOIN sections sec ON s.section_id = sec.id 
                      LEFT JOIN departments d ON s.department = d.department_code
                      WHERE s.email = ? LIMIT 1");
$stmt->execute([$email]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$sectionId = $student['section_id'] ?? null;
$sectionName = $student['section_name'] ?? 'Not Assigned';

// 1. Get Classmates (students in the same section)
$classmates = [];
if ($sectionId) {
    $stmt = $db->prepare("SELECT first_name, middle_name, last_name, email, enrollment_type 
                          FROM students 
                          WHERE section_id = ? AND email != ? 
                          ORDER BY last_name, first_name");
    $stmt->execute([$sectionId, $email]);
    $classmates = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 2. Get Subjects and Schedules
$semester = isset($_GET['semester']) ? $_GET['semester'] : 1;
$schedules = [];
$isIrregular = ($student['enrollment_type'] ?? 'regular') === 'irregular';

if ($isIrregular) {
    // Check if irregular student has a specific schedule
    $stmt = $db->prepare("SELECT cs.*, sub.subject_code, sub.subject_title, sub.units 
                          FROM class_schedules cs
                          JOIN subjects sub ON cs.subject_id = sub.id
                          WHERE cs.student_id = ? AND cs.semester = ?
                          ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), cs.start_time");
    $stmt->execute([$student['id'], $semester]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($sectionId) {
    // Regular students see subjects assigned to their section
    $stmt = $db->prepare("SELECT cs.*, sub.subject_code, sub.subject_title, sub.units 
                          FROM class_schedules cs
                          JOIN subjects sub ON cs.subject_id = sub.id
                          WHERE cs.section_id = ? AND cs.semester = ? AND cs.student_id IS NULL
                          ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), cs.start_time");
    $stmt->execute([$sectionId, $semester]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Portal - Colegio De Naujan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #1a365d;
      --secondary-color: #2c5282;
      --accent-color: #f6ad55;
      --bg-light: #f7fafc;
    }
    
    body {
      background-color: var(--bg-light);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Sidebar base styles and mobile overlay are in sidebar.php */
    .main-content {
      margin-left: 250px;
      padding: 30px;
      transition: margin-left 0.3s ease;
    }

    /* Mobile */
    @media (max-width: 768px) {
      .main-content {
        margin-left: 0 !important;
        margin-top: 56px;
        padding: 15px !important;
      }
    }
    @media (max-width: 480px) {
      .main-content { padding: 10px !important; }
    }
    
    .content-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }
    
    .welcome-card {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border: none;
      border-radius: 15px;
      padding: 30px;
      margin-bottom: 30px;
    }

    .table thead th {
      background-color: #f8f9fa;
      border-bottom: 2px solid #dee2e6;
      color: var(--primary-color);
      font-weight: 600;
    }

    .badge-regular { background-color: #ebf8ff; color: #2b6cb0; }
    .badge-irregular { background-color: #fffaf0; color: #9c4221; }

    /* Mobile tweaks */
    @media (max-width: 768px) {
      body { overflow-x: hidden; }
      .welcome-card { padding: 20px; }
      .welcome-card h2 { font-size: 1.3rem; }
      /* Stack semester selector below name on mobile */
      .welcome-card .col-md-6.text-md-end { text-align: left !important; margin-top: 15px; }
      .content-card { padding: 15px; }
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="welcome-card shadow-sm">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h2>Welcome back, <?php echo htmlspecialchars($fullName); ?>!</h2>
          <p class="mb-1"><strong>Section:</strong> <?php echo htmlspecialchars($sectionName); ?></p>
          <p class="mb-0"><strong>Status:</strong> <span class="badge <?php echo ($student['enrollment_type'] ?? 'regular') === 'regular' ? 'bg-info' : 'bg-warning text-dark'; ?>"><?php echo ucfirst($student['enrollment_type'] ?? 'regular'); ?></span></p>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="d-inline-block text-start me-4">
            <label class="small text-white-50 d-block mb-1">Active Semester</label>
            <select class="form-select form-select-sm bg-white border-0" onchange="window.location.href='?semester='+this.value">
              <option value="1" <?php echo $semester == 1 ? 'selected' : ''; ?>>First Semester</option>
              <option value="2" <?php echo $semester == 2 ? 'selected' : ''; ?>>Second Semester</option>
            </select>
          </div>
          <div class="display-4 d-inline-block align-middle"><i class="fas fa-graduation-cap"></i></div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Summary Cards -->
      <div class="col-lg-8">
        <div class="row">
          <div class="col-md-12 mb-4">
            <div class="content-card text-center h-100">
              <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
              <h4>My Class Schedule</h4>
              <p class="text-muted">You are currently enrolled in <?php echo count($schedules); ?> subjects for this semester.</p>
              <a href="schedule.php" class="btn btn-info text-white">View Schedule</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Classmates -->
      <div class="col-lg-4">
        <div class="content-card">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-users me-2 text-success"></i>My Classmates</h4>
            <?php if (!empty($classmates)): ?>
              <button class="btn btn-sm btn-link text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#classmatesModal">View All</button>
            <?php endif; ?>
          </div>
          <div class="list-group list-group-flush">
            <?php if (empty($classmates)): ?>
              <p class="text-center py-3 text-muted">No classmates found in your section.</p>
            <?php else: ?>
              <?php foreach (array_slice($classmates, 0, 5) as $mate): ?>
                <div class="list-group-item d-flex align-items-center border-0 px-0 py-2">
                  <div class="avatar-circle me-3" style="width: 35px; height: 35px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #4a5568; font-size: 0.8rem; font-weight: bold;">
                    <?php echo substr($mate['first_name'], 0, 1) . substr($mate['last_name'], 0, 1); ?>
                  </div>
                  <div>
                    <h6 class="mb-0" style="font-size: 0.9rem;"><?php 
                      $fullName = trim($mate['first_name'] . ' ' . ($mate['middle_name'] ? $mate['middle_name'] . ' ' : '') . $mate['last_name']);
                      echo htmlspecialchars($fullName); 
                    ?></h6>
                  </div>
                  <?php if ($mate['enrollment_type'] === 'irregular'): ?>
                    <span class="ms-auto badge bg-warning text-dark" style="font-size: 0.5rem;">IRREG</span>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
              <?php if (count($classmates) > 5): ?>
                <div class="text-center mt-3">
                  <a href="#" class="text-muted text-decoration-none small" data-bs-toggle="modal" data-bs-target="#classmatesModal">
                    + <?php echo count($classmates) - 5; ?> more classmates
                  </a>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Classmates Modal -->
  <div class="modal fade" id="classmatesModal" tabindex="-1" aria-labelledby="classmatesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="classmatesModalLabel"><i class="fas fa-users me-2 text-success"></i>All Classmates (<?php echo count($classmates); ?>)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="list-group list-group-flush">
            <?php foreach ($classmates as $mate): ?>
              <div class="list-group-item d-flex align-items-center px-0 py-3">
                <div class="avatar-circle me-3" style="width: 45px; height: 45px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #4a5568; font-size: 1rem; font-weight: bold;">
                  <?php echo substr($mate['first_name'], 0, 1) . substr($mate['last_name'], 0, 1); ?>
                </div>
                <div>
                  <h6 class="mb-0"><?php 
                    $fullName = trim($mate['first_name'] . ' ' . ($mate['middle_name'] ? $mate['middle_name'] . ' ' : '') . $mate['last_name']);
                    echo htmlspecialchars($fullName); 
                  ?></h6>
                  <small class="text-muted"><?php echo htmlspecialchars($mate['email']); ?></small>
                </div>
                <?php if ($mate['enrollment_type'] === 'irregular'): ?>
                  <span class="ms-auto badge bg-warning text-dark">Irregular</span>
                <?php else: ?>
                  <span class="ms-auto badge bg-info">Regular</span>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
