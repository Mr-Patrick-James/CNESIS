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

// Get student details
$stmt = $db->prepare("SELECT s.*, sec.section_name 
                      FROM students s 
                      LEFT JOIN sections sec ON s.section_id = sec.id 
                      WHERE s.email = ? LIMIT 1");
$stmt->execute([$email]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$sectionId = $student['section_id'] ?? null;

// Get Subjects
$semester = isset($_GET['semester']) ? $_GET['semester'] : 1;
$subjects = [];
if ($sectionId) {
    $isIrregular = ($student['enrollment_type'] ?? 'regular') === 'irregular';
    
    if ($isIrregular) {
        $stmt = $db->prepare("SELECT sub.*, cs.instructor_name
                              FROM subjects sub
                              JOIN class_schedules cs ON sub.id = cs.subject_id
                              WHERE cs.student_id = (SELECT id FROM students WHERE email = ? LIMIT 1)
                              AND cs.semester = ?
                              GROUP BY sub.id");
        $stmt->execute([$email, $semester]);
    } else {
        $stmt = $db->prepare("SELECT sub.*, cs.instructor_name
                              FROM subjects sub
                              JOIN class_schedules cs ON sub.id = cs.subject_id
                              WHERE cs.section_id = ? AND cs.semester = ? AND cs.student_id IS NULL
                              GROUP BY sub.id");
        $stmt->execute([$sectionId, $semester]);
    }
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Subjects - Student Portal</title>
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

    .subject-card {
      border-left: 5px solid var(--primary-color);
      transition: transform 0.2s;
    }

    .subject-card:hover {
      transform: translateY(-5px);
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-0"><i class="fas fa-book me-2 text-primary"></i>My Enrolled Subjects</h2>
        <p class="text-muted small mb-0">Viewing subjects for <?php echo $semester == 1 ? 'First' : 'Second'; ?> Semester</p>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="text-end">
          <label class="small text-muted d-block mb-1">Switch Semester</label>
          <select class="form-select form-select-sm" onchange="window.location.href='?semester='+this.value">
            <option value="1" <?php echo $semester == 1 ? 'selected' : ''; ?>>First Semester</option>
            <option value="2" <?php echo $semester == 2 ? 'selected' : ''; ?>>Second Semester</option>
          </select>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Subjects</li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="row">
      <?php if (empty($subjects)): ?>
        <div class="col-12">
          <div class="content-card text-center py-5">
            <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
            <h3>No subjects found</h3>
            <p class="text-muted">You are not currently enrolled in any subjects for this semester.</p>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($subjects as $sub): ?>
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="content-card h-100 subject-card">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="badge bg-primary"><?php echo htmlspecialchars($sub['subject_code']); ?></span>
                <span class="text-muted small"><i class="fas fa-clock me-1"></i> <?php echo $sub['units']; ?> Units</span>
              </div>
              <h5 class="mb-3"><?php echo htmlspecialchars($sub['subject_title']); ?></h5>
              <hr>
              <div class="d-flex align-items-center">
                <div class="avatar-circle me-2" style="width: 30px; height: 30px; background: #edf2f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                  <i class="fas fa-user-tie"></i>
                </div>
                <small class="text-muted">Instructor: <strong><?php echo htmlspecialchars($sub['instructor_name'] ?? 'TBA'); ?></strong></small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
