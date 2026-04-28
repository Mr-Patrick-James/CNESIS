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
$stmt = $db->prepare("SELECT s.*, sec.section_name 
                      FROM students s 
                      LEFT JOIN sections sec ON s.section_id = sec.id 
                      WHERE s.email = ? LIMIT 1");
$stmt->execute([$email]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$sectionId = $student['section_id'] ?? null;
$sectionName = $student['section_name'] ?? 'Not Assigned';

// Get Classmates
$classmates = [];
if ($sectionId) {
    $stmt = $db->prepare("SELECT first_name, middle_name, last_name, email, enrollment_type, phone 
                          FROM students 
                          WHERE section_id = ? AND email != ? 
                          ORDER BY last_name, first_name");
    $stmt->execute([$sectionId, $email]);
    $classmates = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Classmates - Student Portal</title>
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
    
    .avatar-circle {
      width: 50px;
      height: 50px;
      background: #e2e8f0;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #4a5568;
      font-size: 1.2rem;
      font-weight: bold;
    }

    .table thead th {
      background-color: #f8f9fa;
      border-bottom: 2px solid #dee2e6;
      color: var(--primary-color);
      font-weight: 600;
    }

    /* Mobile tweaks */
    @media (max-width: 768px) {
      body { overflow-x: hidden; }
      .content-card { padding: 15px; }
      .avatar-circle { width: 35px; height: 35px; font-size: 0.85rem; }
      /* Hide email column on mobile */
      .col-email { display: none; }
      /* Stack page header */
      .page-header-row { flex-direction: column !important; align-items: flex-start !important; gap: 10px; }
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header-row flex-wrap gap-2">
      <div>
        <h2 class="mb-1">My Classmates</h2>
        <p class="text-muted mb-0">Section: <strong><?php echo htmlspecialchars($sectionName); ?></strong> (<?php echo count($classmates); ?> total)</p>
      </div>
      <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
      </a>
    </div>

    <div class="content-card">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th style="width: 60px;">Avatar</th>
              <th>Full Name</th>
              <th class="col-email">Email Address</th>
              <th>Status</th>
              <th>Type</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($classmates)): ?>
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">No classmates found in your section.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($classmates as $mate): ?>
                <tr>
                  <td>
                    <div class="avatar-circle">
                      <?php echo substr($mate['first_name'], 0, 1) . substr($mate['last_name'], 0, 1); ?>
                    </div>
                  </td>
                  <td>
                    <div class="fw-bold"><?php 
                      $fullName = trim($mate['first_name'] . ' ' . ($mate['middle_name'] ? $mate['middle_name'] . ' ' : '') . $mate['last_name']);
                      echo htmlspecialchars($fullName); 
                    ?></div>
                  </td>
                  <td class="col-email"><?php echo htmlspecialchars($mate['email']); ?></td>
                  <td>
                    <span class="badge bg-success">Enrolled</span>
                  </td>
                  <td>
                    <?php if ($mate['enrollment_type'] === 'irregular'): ?>
                      <span class="badge bg-warning text-dark">Irregular</span>
                    <?php else: ?>
                      <span class="badge bg-info">Regular</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>