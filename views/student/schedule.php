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
$stmt = $db->prepare("SELECT s.*, sec.section_name, sec.adviser 
                      FROM students s 
                      LEFT JOIN sections sec ON s.section_id = sec.id 
                      WHERE s.email = ? LIMIT 1");
$stmt->execute([$email]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$sectionId = $student['section_id'] ?? null;
$classAdviser = $student['adviser'] ?? null;

// Get Schedules
$semester = isset($_GET['semester']) ? $_GET['semester'] : 1;
$schedules = [];
$isIrregular = ($student['enrollment_type'] ?? 'regular') === 'irregular';

if ($isIrregular) {
    // Irregular students see subjects specifically assigned to them
    $stmt = $db->prepare("SELECT cs.*, sub.subject_code, sub.subject_title 
                          FROM class_schedules cs
                          JOIN subjects sub ON cs.subject_id = sub.id
                          WHERE cs.student_id = ? AND cs.semester = ?
                          ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), cs.start_time");
    $stmt->execute([$student['id'], $semester]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($sectionId) {
    // Regular students see subjects assigned to their section
    $stmt = $db->prepare("SELECT cs.*, sub.subject_code, sub.subject_title 
                          FROM class_schedules cs
                          JOIN subjects sub ON cs.subject_id = sub.id
                          WHERE cs.section_id = ? AND cs.semester = ? AND cs.student_id IS NULL
                          ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), cs.start_time");
    $stmt->execute([$sectionId, $semester]);
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

    /* Grid Template Styles */
    .print-container {
      background: white;
      padding: 30px;
      width: 100%;
      color: #000;
      border: 1px solid #edf2f7;
      border-radius: 15px;
    }
    
    .print-header {
      text-align: center;
      margin-bottom: 20px;
    }
    
    .print-header img {
      width: 60px;
      height: 60px;
      margin: 0 10px;
    }
    
    .print-header h5 {
      margin: 2px 0;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 1rem;
    }
    
    .schedule-grid-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      table-layout: fixed;
    }
    
    .schedule-grid-table th, .schedule-grid-table td {
      border: 1px solid #dee2e6;
      padding: 8px 4px;
      text-align: center;
      font-size: 0.75rem;
      word-wrap: break-word;
    }
    
    .schedule-grid-table th {
      background-color: #f8f9fa;
      font-weight: 700;
      text-transform: uppercase;
    }
    
    .grid-header-row {
      background-color: #f8f9fa;
      text-align: center;
      font-weight: 700;
      font-size: 0.9rem;
      padding: 8px;
      border: 1px solid #dee2e6;
      margin-bottom: -1px;
    }
    
    .time-col {
      width: 90px;
      font-weight: 700;
      background-color: #f8f9fa;
    }
    
    .subject-cell {
      line-height: 1.1;
      height: 60px;
      vertical-align: middle;
    }
    
    .subject-cell .code {
      font-weight: 700;
      display: block;
      font-size: 0.7rem;
    }
    
    .subject-cell .title {
      font-size: 0.65rem;
      display: block;
      margin-bottom: 2px;
    }

    .subject-cell .room {
      font-size: 0.6rem;
      color: #000;
      display: block;
    }
    
    .adviser-row {
      margin-top: 20px;
      text-align: center;
      font-weight: 600;
      font-size: 0.9rem;
    }

    @media print {
      body * { visibility: hidden; }
      .print-container, .print-container * { visibility: visible; }
      .print-container { 
        position: absolute; 
        left: 0; top: 0; width: 100%; 
        border: none !important;
        padding: 0 !important;
      }
      .sidebar, .main-content > div:first-child, .nav-tabs, .tab-content > :not(.active) { 
        display: none !important; 
      }
      .main-content { margin: 0 !important; padding: 0 !important; }
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>My Class Schedule</h2>
        <p class="text-muted small mb-0">Viewing schedule for <?php echo $semester == 1 ? 'First' : 'Second'; ?> Semester</p>
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
            <li class="breadcrumb-item active">Schedule</li>
          </ol>
        </nav>
      </div>
    </div>

    <?php if (empty($groupedSchedules)): ?>
      <div class="content-card text-center py-5">
        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
        <h3>No schedules found</h3>
        <p class="text-muted">You do not have any classes scheduled at this time.</p>
      </div>
    <?php else: ?>
      <!-- Tab Navigation -->
      <ul class="nav nav-tabs mb-4" id="scheduleTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="list-tab" data-bs-toggle="tab" data-bs-target="#listView" type="button" role="tab">
            <i class="fas fa-list me-2"></i>List View
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="grid-tab" data-bs-toggle="tab" data-bs-target="#gridView" type="button" role="tab">
            <i class="fas fa-th me-2"></i>Template View
          </button>
        </li>
      </ul>

      <div class="tab-content" id="scheduleTabContent">
        <!-- List View -->
        <div class="tab-pane fade show active" id="listView" role="tabpanel">
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
        </div>

        <!-- Template View (Grid) -->
        <div class="tab-pane fade" id="gridView" role="tabpanel">
          <div class="d-flex justify-content-end mb-3 no-print">
            <button class="btn btn-primary" onclick="window.print()">
              <i class="fas fa-print me-2"></i>Print Schedule
            </button>
          </div>
          <div class="print-container">
            <div class="print-header">
              <div class="d-flex justify-content-center align-items-center mb-3">
                <img src="../../assets/img/logo.png" alt="Logo">
                <div>
                  <h5>Colegio de Naujan</h5>
                  <p class="mb-0 small">Barangay Santiago, Naujan, Oriental Mindoro</p>
                  <p class="mb-0 small">Email: colegiodenaujan@gmail.com</p>
                </div>
                <img src="../../assets/img/logo.png" alt="Logo">
              </div>
              <div class="grid-header-row">
                CLASS SCHEDULE FOR <?php echo strtoupper($semester == 1 ? 'First' : 'Second'); ?> SEMESTER, A.Y. 2025-2026
              </div>
            </div>

            <table class="schedule-grid-table">
              <thead>
                <tr>
                  <th class="time-col">TIME</th>
                  <?php 
                  $displayDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                  foreach ($displayDays as $d) echo "<th>" . strtoupper($d) . "</th>";
                  ?>
                </tr>
              </thead>
              <tbody>
                <?php
                // 1. Determine the range of time from actual schedules
                $minHour = 8; // Default 8 AM
                $maxHour = 17; // Default 5 PM

                foreach ($schedules as $s) {
                    $startH = (int)explode(':', $s['start_time'])[0];
                    $endH = (int)explode(':', $s['end_time'])[0];
                    if ($startH < $minHour) $minHour = $startH;
                    if ($endH >= $maxHour) $maxHour = $endH + 1;
                }

                // 2. Generate dynamic time slots (1.5 hour blocks)
                $timeSlots = [];
                for ($hour = $minHour; $hour < $maxHour; $hour += 1.5) {
                    $startH = floor($hour);
                    $startM = ($hour % 1) * 60;
                    $endHour = $hour + 1.5;
                    $endH = floor($endHour);
                    $endM = ($endHour % 1) * 60;

                    $formatTime = function($h, $m) {
                        $displayH = $h > 12 ? $h - 12 : ($h == 0 ? 12 : $h);
                        return $displayH . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
                    };

                    $timeSlots[] = [
                        'label' => $formatTime($startH, $startM) . '-' . $formatTime($endH, $endM),
                        'start' => str_pad($startH, 2, '0', STR_PAD_LEFT) . ':' . str_pad($startM, 2, '0', STR_PAD_LEFT),
                        'end' => str_pad($endH, 2, '0', STR_PAD_LEFT) . ':' . str_pad($endM, 2, '0', STR_PAD_LEFT)
                    ];
                }

                foreach ($timeSlots as $slot):
                ?>
                <tr>
                  <td class="time-col"><?php echo $slot['label']; ?></td>
                  <?php foreach ($displayDays as $day): ?>
                    <td class="subject-cell">
                      <?php
                      if (isset($groupedSchedules[$day])) {
                        foreach ($groupedSchedules[$day] as $s) {
                          $sStart = substr($s['start_time'], 0, 5);
                          $sEnd = substr($s['end_time'], 0, 5);
                          if ($sStart < $slot['end'] && $sEnd > $slot['start']) {
                            echo '<span class="code">' . htmlspecialchars($s['subject_code']) . '</span>';
                            echo '<span class="title">' . htmlspecialchars($s['subject_title']) . '</span>';
                            if (!empty($s['room'])) echo '<span class="room">' . htmlspecialchars($s['room']) . '</span>';
                          }
                        }
                      }
                      ?>
                    </td>
                  <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            
            <div class="adviser-row">
              ADVISER: <span class="border-bottom border-dark px-4"><?php echo !empty($classAdviser) ? strtoupper(htmlspecialchars($classAdviser)) : '___________________________'; ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
