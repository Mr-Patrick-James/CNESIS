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
$schedules = [];
if ($sectionId) {
    $stmt = $db->prepare("SELECT cs.*, sub.subject_code, sub.subject_title, sub.units 
                          FROM class_schedules cs
                          JOIN subjects sub ON cs.subject_id = sub.id
                          WHERE cs.section_id = ?
                          ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), cs.start_time");
    $stmt->execute([$sectionId]);
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
    
    .sidebar {
      height: 100vh;
      background: var(--primary-color);
      color: white;
      padding-top: 20px;
      position: fixed;
      width: 250px;
    }
    
    .main-content {
      margin-left: 250px;
      padding: 30px;
    }
    
    .sidebar .nav-link {
      color: rgba(255,255,255,0.8);
      padding: 12px 20px;
      margin: 4px 10px;
      border-radius: 8px;
    }
    
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
      background: rgba(255,255,255,0.1);
      color: white;
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
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="welcome-card shadow-sm">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h2>Welcome back, <?php echo htmlspecialchars($fullName); ?>!</h2>
          <p class="mb-1"><strong>Section:</strong> <?php echo htmlspecialchars($sectionName); ?></p>
          <p class="mb-0"><strong>Status:</strong> <span class="badge <?php echo ($student['enrollment_type'] ?? 'regular') === 'regular' ? 'bg-info' : 'bg-warning text-dark'; ?>"><?php echo ucfirst($student['enrollment_type'] ?? 'regular'); ?></span></p>
        </div>
        <div class="col-md-4 text-md-end">
          <div class="display-4"><i class="fas fa-graduation-cap"></i></div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Summary Cards -->
      <div class="col-lg-8">
        <div class="row">
          <div class="col-md-6 mb-4">
            <div class="content-card text-center h-100">
              <i class="fas fa-book fa-3x text-primary mb-3"></i>
              <h4>My Subjects</h4>
              <p class="text-muted">You are currently enrolled in <?php echo count($schedules); ?> subjects.</p>
              <a href="subjects.php" class="btn btn-primary">View All Subjects</a>
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <div class="content-card text-center h-100">
              <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
              <h4>My Schedule</h4>
              <p class="text-muted">View your weekly class time and locations.</p>
              <a href="schedule.php" class="btn btn-info text-white">View Schedule</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Classmates -->
      <div class="col-lg-4">
        <div class="content-card">
          <h4 class="mb-4"><i class="fas fa-users me-2 text-success"></i>My Classmates</h4>
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
                    <h6 class="mb-0" style="font-size: 0.9rem;"><?php echo htmlspecialchars($mate['first_name'] . ' ' . $mate['last_name']); ?></h6>
                  </div>
                  <?php if ($mate['enrollment_type'] === 'irregular'): ?>
                    <span class="ms-auto badge bg-warning text-dark" style="font-size: 0.5rem;">IRREG</span>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
              <?php if (count($classmates) > 5): ?>
                <div class="text-center mt-3">
                  <small class="text-muted">+ <?php echo count($classmates) - 5; ?> more classmates</small>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
