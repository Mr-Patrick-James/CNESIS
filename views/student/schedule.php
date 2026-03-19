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

// Get Schedules
$schedules = [];
if ($sectionId) {
    $stmt = $db->prepare("SELECT cs.*, sub.subject_code, sub.subject_title 
                          FROM class_schedules cs
                          JOIN subjects sub ON cs.subject_id = sub.id
                          WHERE cs.section_id = ?
                          ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), cs.start_time");
    $stmt->execute([$sectionId]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Group by day for easier display
$groupedSchedules = [];
foreach ($schedules as $sched) {
    $groupedSchedules[$sched['day_of_week']][] = $sched;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Class Schedule - Student Portal</title>
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

    .day-header {
      background-color: var(--primary-color);
      color: white;
      padding: 10px 20px;
      border-radius: 10px 10px 0 0;
      font-weight: 600;
    }

    .schedule-item {
      border-bottom: 1px solid #edf2f7;
      padding: 15px 20px;
    }

    .schedule-item:last-child {
      border-bottom: none;
    }

    .time-badge {
      background-color: #ebf8ff;
      color: #2b6cb0;
      padding: 5px 10px;
      border-radius: 5px;
      font-weight: 600;
      font-size: 0.85rem;
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2><i class="fas fa-calendar-alt me-2 text-primary"></i>My Class Schedule</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Schedule</li>
        </ol>
      </nav>
    </div>

    <?php if (empty($groupedSchedules)): ?>
      <div class="content-card text-center py-5">
        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
        <h3>No schedules found</h3>
        <p class="text-muted">You do not have any classes scheduled at this time.</p>
      </div>
    <?php else: ?>
      <div class="row">
        <?php 
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day): 
          if (!isset($groupedSchedules[$day])) continue;
        ?>
          <div class="col-12 mb-4">
            <div class="content-card p-0 overflow-hidden">
              <div class="day-header">
                <?php echo $day; ?>
              </div>
              <?php foreach ($groupedSchedules[$day] as $item): ?>
                <div class="schedule-item">
                  <div class="row align-items-center">
                    <div class="col-md-2 mb-2 mb-md-0">
                      <span class="time-badge">
                        <i class="far fa-clock me-1"></i>
                        <?php echo date('h:i A', strtotime($item['start_time'])); ?>
                      </span>
                    </div>
                    <div class="col-md-5 mb-2 mb-md-0">
                      <h6 class="mb-0"><?php echo htmlspecialchars($item['subject_title']); ?></h6>
                      <small class="text-muted"><?php echo htmlspecialchars($item['subject_code']); ?></small>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                      <small class="text-muted"><i class="fas fa-door-open me-1"></i> Room: <?php echo htmlspecialchars($item['room']); ?></small>
                    </div>
                    <div class="col-md-3">
                      <small class="text-muted"><i class="fas fa-user-tie me-1"></i> <?php echo htmlspecialchars($item['instructor_name']); ?></small>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
