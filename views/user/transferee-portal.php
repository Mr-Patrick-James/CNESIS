<?php
session_start();
require_once '../../api/config/database.php';

$database = new Database();
$db = $database->getConnection();

$verified_email = null;

// Case 1: Token in URL (preferred)
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Validate token
    $stmt = $db->prepare("SELECT email, token_expires_at FROM email_verifications 
                          WHERE portal_token = ? AND status = 'verified' 
                          ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $expires_at = strtotime($row['token_expires_at']);
        if (time() <= $expires_at) {
            $verified_email = $row['email'];
            $_SESSION['verified_email'] = $verified_email;
            $_SESSION['student_type'] = 'transferee';
            $_SESSION['portal_token'] = $token;
        } else {
            // Token expired
            header('Location: ../../index.php?error=expired_token');
            exit;
        }
    } else {
        // Invalid token
        header('Location: ../../index.php?error=invalid_token');
        exit;
    }
} 
// Case 2: Session exists
elseif (isset($_SESSION['verified_email'])) {
    $verified_email = $_SESSION['verified_email'];
} 
// Case 3: No access
else {
    header('Location: ../../index.php?error=unauthorized');
    exit;
}

$email = $verified_email;

// Check if there's an existing admission record
$stmt = $db->prepare("SELECT * FROM admissions WHERE email = ? ORDER BY submitted_at DESC LIMIT 1");
$stmt->execute([$email]);
$admission = $stmt->fetch(PDO::FETCH_ASSOC);

// If this is a freshman, redirect to admission portal
if ($admission && $admission['admission_type'] === 'freshman') {
    $token_param = isset($_GET['token']) ? '?token=' . $_GET['token'] : '';
    header('Location: admission-portal.php' . $token_param);
    exit;
}

// If status is 'new' or 'draft', treat it as no admission (drafting)
if ($admission && ($admission['status'] == 'new' || $admission['status'] == 'draft')) {
    $current_status = $admission['status'];
} else {
    $current_status = $admission ? $admission['status'] : 'new';
}

// Prepare existing data for JavaScript
$existing_data_json = $admission ? json_encode([
    'details' => $admission,
    'form_data' => json_decode($admission['form_data'] ?? '{}', true),
    'attachments' => json_decode($admission['attachments'] ?? '[]', true)
]) : 'null';

// Fetch programs for the application form
$stmt = $db->prepare("SELECT id, title, code FROM programs WHERE status = 'active' ORDER BY title");
$stmt->execute();
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transferee Admission Portal – Colegio De Naujan</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  
  <style>
    :root {
      --primary-blue: #1a365d;
      --secondary-blue: #2d55a0;
      --accent-gold: #d4af37;
      --success-green: #28a745;
      --light-gray: #f8f9fa;
    }
    
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f4f7f6;
      color: #333;
    }
    
    .portal-header {
      background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
      color: white;
      padding: 40px 0;
      margin-bottom: 40px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .step-container {
      background: white;
      border-radius: 15px;
      padding: 40px 30px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }
    
    .horizontal-steps {
      display: flex;
      justify-content: space-between;
      position: relative;
      margin-bottom: 50px;
      padding: 0;
      width: 100%;
      overflow-x: auto;
      padding-bottom: 10px;
    }
    
    .horizontal-steps::-webkit-scrollbar {
      height: 4px;
    }

    .horizontal-steps::-webkit-scrollbar-thumb {
      background: #e0e0e0;
      border-radius: 2px;
    }

    .horizontal-steps::before {
      content: '';
      position: absolute;
      top: 15px;
      left: 0;
      right: 0;
      height: 1.5px;
      background-color: #e0e0e0;
      z-index: 1;
    }
    
    .step-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      z-index: 2;
      min-width: 100px;
      flex: 1;
      text-align: center;
    }
    
    .step-icon {
      width: 32px;
      height: 32px;
      background-color: white; 
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 8px;
      color: #ccc;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      border: none;
      box-shadow: none;
      position: relative;
    }
    
    .step-item.active .step-icon {
      color: #444;
    }

    .step-item.active .step-icon i {
      background: #e9ecef;
      padding: 8px;
      border-radius: 50%;
    }

    .step-item.active .step-icon::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 50%;
      transform: translateX(-50%);
      width: 15px;
      height: 2px;
      background-color: #444;
    }

    .step-item.completed .step-icon {
      color: var(--success-green);
    }
    
    .step-title {
      font-family: 'Roboto', sans-serif;
      font-weight: 500;
      font-size: 0.7rem;
      margin-top: 5px;
      color: #999;
      letter-spacing: 0.3px;
      line-height: 1.2;
    }

    .step-item.active .step-title {
      color: #444;
      font-weight: 600;
    }

    /* AAP Section Styling */
    .aap-question {
      margin-bottom: 25px;
      padding-bottom: 20px;
      border-bottom: 1px solid #f0f0f0;
    }

    .aap-question:last-child {
      border-bottom: none;
    }

    .aap-question-label {
      font-weight: 600;
      color: #333;
      margin-bottom: 12px;
      display: block;
      font-size: 0.95rem;
    }

    .aap-question-label span {
      color: #dc3545;
      margin-left: 3px;
    }

    /* Enhanced Form Styling */
    .form-section-header {
      border-bottom: 2px solid #28a745;
      padding-bottom: 8px;
      margin-bottom: 25px;
      color: #28a745;
      font-weight: 600;
      display: flex;
      align-items: center;
    }
    
    .form-section-header i {
      margin-right: 10px;
    }

    .form-group label {
      font-weight: 500;
      color: #555;
      font-size: 0.85rem;
      margin-bottom: 5px;
    }
    
    .form-group label span {
      color: #dc3545;
      margin-left: 3px;
    }

    #parents-container .form-group > label:not(.form-check-label) {
      min-height: 42px; /* Fixed height for 2 lines */
      display: flex;
      align-items: flex-end; /* Align text to bottom */
      line-height: 1.2;
      margin-bottom: 5px;
    }

    .dynamic-container {
      background: #fdfdfd;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      position: relative;
    }

    .btn-remove {
      background-color: #ff6b6b;
      color: white;
      border: none;
      width: 100%;
      padding: 8px;
      border-radius: 5px;
      margin-top: 15px;
      transition: all 0.2s;
    }
    
    .btn-remove:hover {
      background-color: #fa5252;
    }

    .btn-add {
      background-color: #28a745;
      color: white;
      border: none;
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 30px;
      font-weight: 500;
    }
    
    .btn-add:hover {
      background-color: #218838;
      color: white;
    }

    .equity-option-item {
      border: 1px solid #e9ecef;
      border-radius: 5px;
      padding: 10px 15px;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      transition: all 0.2s;
      cursor: pointer;
    }

    .equity-option-item:hover {
      background-color: #f8f9fa;
      border-color: #dee2e6;
    }

    .equity-option-item input[type="radio"] {
      margin-right: 12px;
      cursor: pointer;
    }

    .equity-option-item label {
      margin-bottom: 0;
      cursor: pointer;
      font-size: 0.9rem;
      color: #444;
      flex: 1;
    }

    .nav-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 40px;
    }

    .btn-nav-back {
      background-color: #f8f9fa;
      color: #333;
      border: 1px solid #dee2e6;
      padding: 10px 25px;
      font-weight: 600;
      border-radius: 8px;
    }

    .btn-nav-save {
      background-color: #fff;
      color: var(--primary-blue);
      border: 1px solid var(--primary-blue);
      padding: 10px 25px;
      font-weight: 600;
      border-radius: 8px;
    }

    .btn-nav-next {
      background-color: var(--primary-blue);
      color: #fff;
      border: none;
      padding: 10px 25px;
      font-weight: 600;
      border-radius: 8px;
    }
    .btn-nav-next {
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-nav-next {
      background-color: #ffc107;
      color: #000;
      border: none;
      padding: 10px 30px;
      border-radius: 5px;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .note-text {
      font-size: 0.75rem;
      color: #777;
      text-align: center;
      margin-top: 10px;
    }

    .school-group-title {
      font-weight: 600;
      color: #333;
      margin-bottom: 15px;
      display: block;
      border-bottom: 1px solid #eee;
      padding-bottom: 5px;
    }

    .custom-radio-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .custom-radio {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      font-size: 0.9rem;
      color: #555;
    }

    .custom-radio input {
      width: 18px;
      height: 18px;
      cursor: pointer;
    }

    .conditional-input {
      margin-top: 10px;
      padding-left: 28px;
    }

    .conditional-input input {
      border: none;
      border-bottom: 1px solid #ccc;
      border-radius: 0;
      padding: 5px 0;
      width: 100%;
      font-size: 0.9rem;
      transition: border-color 0.3s;
    }

    .conditional-input input:focus {
      outline: none;
      border-bottom-color: var(--primary-blue);
    }

    .required-note {
      color: #dc3545;
      font-size: 0.85rem;
      margin-bottom: 20px;
    }

    /* Remove badges from steps to match minimalist look */
    .status-badge {
      display: none;
    }

    /* Attachment Section Styling */
    .attachment-card {
      background: #fff;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 25px;
      margin-bottom: 25px;
    }

    .attachment-title {
      color: #28a745;
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 15px;
    }

    .attachment-subtitle {
      font-size: 0.9rem;
      color: #333;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
    }

    .attachment-subtitle i {
      margin-right: 8px;
    }

    .btn-select-file {
      background-color: #ffc107;
      color: #000;
      border: none;
      padding: 8px 20px;
      border-radius: 5px;
      font-weight: 500;
      font-size: 0.85rem;
      margin-bottom: 15px;
    }

    .file-upload-note {
      background: #f8f9fa;
      padding: 8px 15px;
      border-radius: 4px;
      font-size: 0.8rem;
      color: #666;
      margin-bottom: 15px;
    }

    .file-upload-note strong {
      color: #dc3545;
    }

    .preview-container {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-top: 15px;
    }

    .preview-item {
      width: 120px;
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 10px;
      position: relative;
      text-align: center;
    }

    .preview-item img {
      width: 100%;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
      margin-bottom: 8px;
    }

    .btn-delete-file {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      background: #ff5252;
      color: white;
      border: none;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      cursor: pointer;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      transition: all 0.2s;
    }

    .btn-delete-file:hover {
      background: #ff1744;
      transform: translateX(-50%) scale(1.1);
    }

    .step-details-container {
      background: #f8f9fa;
      border-radius: 12px;
      padding: 25px;
      border-left: 5px solid var(--primary-blue);
    }
    
    .active-step-content h4 {
      color: var(--primary-blue);
      font-weight: 700;
      margin-bottom: 10px;
    }

    .guideline-card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      border-left: 5px solid var(--accent-gold);
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .guideline-item {
      display: flex;
      align-items: flex-start;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px dashed #eee;
    }

    .guideline-item:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .guideline-icon {
      width: 40px;
      height: 40px;
      background: rgba(212, 175, 55, 0.1);
      color: var(--accent-gold);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      flex-shrink: 0;
      font-size: 1.1rem;
    }

    .guideline-text strong {
      display: block;
      color: var(--primary-blue);
      margin-bottom: 2px;
    }

    .guideline-text span {
      font-size: 0.9rem;
      color: #666;
    }
    
    .badge-completed { background-color: rgba(40, 167, 69, 0.1); color: var(--success-green); }
    .badge-pending { background-color: rgba(212, 175, 55, 0.1); color: var(--accent-gold); }
    .badge-waiting { background-color: #f0f0f0; color: #999; }
    
    .btn-portal {
      background-color: var(--primary-blue);
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .section-portal {
      display: none;
    }
    
    .section-portal.active {
      display: block;
      animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .form-group label {
      font-weight: 600;
      color: var(--primary-blue);
      margin-bottom: 5px;
      font-size: 0.9rem;
    }

    .form-control:focus {
      border-color: var(--accent-gold);
      box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
    }

    .admission-form-container {
      background: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .review-summary-card {
      background: #fff;
      border: 1px solid #e9ecef;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
    }

    .review-section-title {
      font-weight: 700;
      color: var(--primary-blue);
      border-bottom: 1px solid #eee;
      padding-bottom: 8px;
      margin-bottom: 15px;
      font-size: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .review-item-label {
      font-weight: 600;
      color: #666;
      font-size: 0.85rem;
      margin-bottom: 2px;
    }

    .review-item-value {
      color: #333;
      font-size: 0.95rem;
      margin-bottom: 12px;
    }

    .form-section-title {
      border-bottom: 2px solid var(--accent-gold);
      padding-bottom: 10px;
      margin-bottom: 20px;
      color: var(--primary-blue);
      font-weight: 700;
      font-size: 1.1rem;
    }
  </style>
</head>
<body>

  <header class="portal-header">
    <div class="container text-center">
      <h1 class="display-5 fw-bold mb-2">Transferee Admission Portal</h1>
      <p class="lead mb-0">Track your application progress for Colegio De Naujan</p>
      <div class="mt-3">
        <span class="badge bg-light text-dark px-3 py-2">
          <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($email); ?>
        </span>
      </div>
    </div>
  </header>

  <div class="container pb-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        
        <div class="step-container">
          <h3 class="mb-5 fw-bold text-center" style="color: var(--primary-blue);">Admission Journey</h3>
          
          <div class="horizontal-steps">
            <!-- Step 1: Welcome -->
            <div id="step-marker-welcome" class="step-item completed">
              <div class="step-icon"><i class="fas fa-home"></i></div>
              <div class="step-title">Welcome</div>
            </div>

            <!-- Step 2: Read First -->
            <div id="step-marker-guidelines" class="step-item active">
              <div class="step-icon"><i class="fas fa-info-circle"></i></div>
              <div class="step-title">Read First</div>
            </div>

            <!-- Step 3: Confirmation AAP -->
            <div id="step-marker-aap" class="step-item">
              <div class="step-icon"><i class="fas fa-question-circle"></i></div>
              <div class="step-title">Confirmation AAP</div>
            </div>

            <!-- Step 4: Personal -->
            <div id="step-marker-personal" class="step-item">
              <div class="step-icon"><i class="fas fa-user"></i></div>
              <div class="step-title">Personal</div>
            </div>

            <!-- Step 5: Education & Program -->
            <div id="step-marker-education" class="step-item">
              <div class="step-icon"><i class="fas fa-graduation-cap"></i></div>
              <div class="step-title">Education & Program</div>
            </div>

            <!-- Step 6: Attachments -->
            <div id="step-marker-attachments" class="step-item">
              <div class="step-icon"><i class="fas fa-paperclip"></i></div>
              <div class="step-title">Attachments</div>
            </div>

            <!-- Step 7: Review -->
            <div id="step-marker-review" class="step-item">
              <div class="step-icon"><i class="fas fa-eye"></i></div>
              <div class="step-title">Review</div>
            </div>

            <!-- Step 8: Submit -->
            <div id="step-marker-submit" class="step-item">
              <div class="step-icon"><i class="fas fa-check-double"></i></div>
              <div class="step-title">Final Submit</div>
            </div>
          </div>

          <!-- Active Step Details -->
          <div class="step-details-container mt-4">
            <div class="active-step-content">
              
              <!-- GUIDELINES SECTION -->
              <div id="guidelines-section" class="section-portal <?php echo (!$admission || $current_status == 'draft') ? 'active' : ''; ?>">
                <div class="text-center mb-4">
                  <h4 class="mb-2">Welcome to Your Admission Portal</h4>
                  <p class="text-muted">Your email has been verified. Please follow these guidelines to complete your application.</p>
                </div>

                  <div class="guideline-card mx-auto" style="max-width: 800px;">
                    <h5 class="mb-4" style="color: var(--primary-blue); font-weight: 600;">Admission Guidelines</h5>
                    
                    <div class="guideline-item">
                      <div class="guideline-icon"><i class="fas fa-file-alt"></i></div>
                      <div class="guideline-text">
                        <strong>Step 1: Prepare Your Documents</strong>
                        <span>Have high-quality digital copies (PDF or clear JPEG) of your Form 138 (Report Card), PSA Birth Certificate, and Good Moral Certificate ready for uploading. Files should not exceed 2MB each.</span>
                      </div>
                    </div>

                    <div class="guideline-item">
                      <div class="guideline-icon"><i class="fas fa-user-circle"></i></div>
                      <div class="guideline-text">
                        <strong>Step 2: Photo Requirements</strong>
                        <span>You will need a recent 2x2 ID picture. Ensure it has a white background, you are wearing a collared shirt, and it includes a name tag (Last Name, First Name, Middle Initial).</span>
                      </div>
                    </div>

                    <div class="guideline-item">
                      <div class="guideline-icon"><i class="fas fa-edit"></i></div>
                      <div class="guideline-text">
                        <strong>Step 3: Complete the Online Form</strong>
                        <span>Fill out all required fields in the application form accurately. Double-check your contact information (phone and email) as these will be used for official notifications.</span>
                      </div>
                    </div>

                    <div class="guideline-item">
                      <div class="guideline-icon"><i class="fas fa-calendar-check"></i></div>
                      <div class="guideline-text">
                        <strong>Step 4: Examination Schedule</strong>
                        <span>Once your documents are verified, you will receive a notification here and via email regarding your entrance examination schedule. Please be on time.</span>
                      </div>
                    </div>

                    <div class="guideline-item">
                      <div class="guideline-icon"><i class="fas fa-comments"></i></div>
                      <div class="guideline-text">
                        <strong>Step 5: Interview Phase</strong>
                        <span>After passing the exam, you will be scheduled for a face-to-face or online interview with the department head of your chosen program.</span>
                      </div>
                    </div>

                    <div class="guideline-item">
                      <div class="guideline-icon"><i class="fas fa-paper-plane"></i></div>
                      <div class="guideline-text">
                        <strong>Final Step: Review and Approval</strong>
                        <span>The final admission decision will be posted in this portal. If approved, you can proceed to the enrollment phase and download your acceptance letter.</span>
                      </div>
                    </div>

                    <div class="mt-4 text-center">
                      <button onclick="showAAPSection()" class="btn btn-portal btn-lg px-5">Start Application Form</button>
                    </div>
                  </div>
                </div>

                <!-- CONFIRMATION AAP SECTION -->
                <div id="aap-section" class="section-portal">
                  <div class="admission-form-container mx-auto" style="max-width: 900px;">
                    <div class="form-section-header">
                      <i class="fas fa-question-circle"></i> Confirmation AAP
                    </div>
                    
                    <div class="required-note">Note: (*) - Required</div>
                    <p class="mb-4">Kindly provide the necessary information.</p>

                    <form id="aapForm">
                      <!-- 1. Academic Status -->
                      <div class="aap-question">
                        <label class="aap-question-label">1. Academic Status<span>*</span></label>
                        <div class="custom-radio-group">
                          <label class="custom-radio">
                            <input type="radio" name="academic_status" value="graduating" required>
                            Graduating Senior High School student of AY 2025-2026
                          </label>
                          <label class="custom-radio">
                            <input type="radio" name="academic_status" value="graduated" required>
                            Graduated Senior High School student
                          </label>
                        </div>
                      </div>

                      <!-- 2. College Enrollment -->
                      <div class="aap-question">
                        <label class="aap-question-label">2. Is the applicant already enrolled (or was enrolled) in a college program in CNESIS or in other schools?<span>*</span></label>
                        <div class="custom-radio-group">
                          <label class="custom-radio">
                            <input type="radio" name="already_enrolled" value="yes" required>
                            Yes
                          </label>
                          <label class="custom-radio">
                            <input type="radio" name="already_enrolled" value="no" required>
                            No
                          </label>
                        </div>
                      </div>

                      <!-- 3. First Time Apply -->
                      <div class="aap-question">
                        <label class="aap-question-label">3. Is this the applicant's first time to apply for CNESIS College Admission?<span>*</span></label>
                        <div class="custom-radio-group">
                          <label class="custom-radio">
                            <input type="radio" name="first_time_apply" value="yes" required>
                            Yes
                          </label>
                          <label class="custom-radio">
                            <input type="radio" name="first_time_apply" value="no" required>
                            No
                          </label>
                        </div>
                      </div>

                      <!-- 4. Transferred during SHS -->
                      <div class="aap-question">
                        <label class="aap-question-label">4. Have you ever transferred during your Senior Your High School?<span>*</span></label>
                        <div class="custom-radio-group">
                          <label class="custom-radio">
                            <input type="radio" name="shs_transfer" value="yes" required onclick="toggleAAPConditional('shs_transfer_details', true)">
                            Yes, previously from :
                          </label>
                          <div id="shs_transfer_details" class="conditional-input d-none">
                            <input type="text" name="shs_transfer_from" placeholder="Name of School">
                            <div class="mt-2">In what year?</div>
                            <input type="text" name="shs_transfer_year" placeholder="Year">
                          </div>
                          <label class="custom-radio">
                            <input type="radio" name="shs_transfer" value="no" required onclick="toggleAAPConditional('shs_transfer_details', false)">
                            No
                          </label>
                        </div>
                      </div>

                      <div class="nav-buttons mt-4">
                        <button type="button" onclick="showGuidelines()" class="btn btn-nav-back"><i class="fas fa-chevron-left me-1"></i> BACK</button>
                        <button type="button" class="btn btn-nav-save" onclick="saveDraft(this)"><i class="fas fa-save"></i> SAVE</button>
                        <button type="button" onclick="showApplicationForm()" class="btn btn-nav-next">NEXT <i class="fas fa-chevron-right ms-1"></i></button>
                      </div>
                      <div class="note-text mt-3">Note: You can save your application form information and continue any time</div>
                    </form>
                  </div>
                </div>

                <!-- APPLICATION FORM SECTION -->
                <div id="form-section" class="section-portal">
                  <div class="admission-form-container mx-auto" style="max-width: 900px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                      <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Admission Application Form</h4>
                      <button onclick="showGuidelines()" class="btn btn-sm btn-outline-secondary">Back to Guidelines</button>
                    </div>
                    
                    <form id="admissionForm">
                      <input type="hidden" name="admission_type" value="transferee">
                      <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                      <input type="hidden" name="status" value="new">
                      
                      <!-- Form Step 1: Basic Information -->
                      <div id="form-step-1" class="form-step-content active">
                        
                        <!-- Personal Information Section -->
                        <div class="form-section-header">
                          <i class="fas fa-user"></i> Personal Information
                        </div>
                        <div class="row g-3 mb-5">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>First Name<span>*</span></label>
                              <input type="text" name="first_name" class="form-control" placeholder="Juan" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Middle Name</label>
                              <input type="text" name="middle_name" class="form-control" placeholder="Santos">
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Last Name<span>*</span></label>
                              <input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" required>
                            </div>
                          </div>
                          <div class="col-md-1">
                            <div class="form-group">
                              <label>Suffix</label>
                              <input type="text" name="extension_name" class="form-control" placeholder="Jr.">
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Date of Birth<span>*</span></label>
                              <input type="date" name="birthdate" class="form-control" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Gender<span>*</span></label>
                              <select name="gender" class="form-control" required>
                                <option value="">Select...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Civil Status<span>*</span></label>
                              <select name="civil_status" class="form-control" required>
                                <option value="">Select...</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="separated">Separated</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Mobile Number<span>*</span></label>
                              <input type="tel" name="phone" class="form-control" placeholder="09xxxxxxxxx" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Citizenship<span>*</span></label>
                              <input type="text" name="citizenship" class="form-control" placeholder="Filipino" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Birth Place<span>*</span></label>
                              <input type="text" name="birth_place" class="form-control" placeholder="City/Municipality, Province" required>
                            </div>
                          </div>
                        </div>

                        <!-- Home Address Section -->
                        <div class="form-section-header">
                          <i class="fas fa-home"></i> Home Address
                        </div>
                        <div class="row g-3 mb-5">
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Street/House No<span>*</span></label>
                              <input type="text" name="street_no" class="form-control" placeholder="12th Street" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Barangay<span>*</span></label>
                              <input type="text" name="barangay" class="form-control" placeholder="Pangasugan" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Town/City & Province<span>*</span></label>
                              <input type="text" name="city_province" class="form-control" placeholder="Santiago Naujan Oriental Mindoro" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Zip code<span>*</span></label>
                              <input type="text" name="zip_code" class="form-control" placeholder="6521" required>
                            </div>
                          </div>
                        </div>

                        <!-- Parents/Guardian Information Section -->
                        <div class="form-section-header">
                          <i class="fas fa-users"></i> Parents/Guardian Information
                        </div>
                        <div id="parents-container">
                          <div class="dynamic-container parent-item">
                            <div class="row g-3">
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>First Name<span>*</span></label>
                                  <input type="text" name="parent_first_name[]" class="form-control" placeholder="Mark" required>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>Middle Name</label>
                                  <input type="text" name="parent_middle_name[]" class="form-control" placeholder="Middle Name">
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>Last Name<span>*</span></label>
                                  <input type="text" name="parent_last_name[]" class="form-control" placeholder="Dela Cruz" required>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>Extension Name</label>
                                  <input type="text" name="parent_extension[]" class="form-control" placeholder="Ex. Jr., III">
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <label>Age<span>*</span></label>
                                  <input type="number" name="parent_age[]" class="form-control" placeholder="56" required>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <label>Relationship<span>*</span></label>
                                  <select name="parent_relationship[]" class="form-control" required>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Guardian">Guardian</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>Highest Educational Attainment<span>*</span></label>
                                  <input type="text" name="parent_education[]" class="form-control" placeholder="College Graduate" required>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <label>Occupation<span>*</span></label>
                                  <input type="text" name="parent_occupation[]" class="form-control" placeholder="Teacher" required>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>Monthly income (in PHP)<span>*</span></label>
                                  <input type="text" name="parent_income[]" class="form-control" placeholder="20000" required>
                                  <div class="note-text text-start">*Estimate</div>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>Contact no.<span>*</span></label>
                                  <input type="text" name="parent_contact[]" class="form-control" placeholder="09564855665" required>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>Street No/Brgy<span>*</span></label>
                                  <input type="text" name="parent_street[]" class="form-control" placeholder="12th Street" required>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>Town/City & Province<span>*</span></label>
                                  <input type="text" name="parent_city[]" class="form-control" placeholder="Santiago Naujan Oriental Mindoro" required>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group border p-2 rounded bg-white">
                                  <label class="mb-0 d-block">Emergency Contact Person</label>
                                  <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="is_emergency[]" id="emergency1">
                                    <label class="form-check-label" for="emergency1" style="font-size: 0.75rem;">Notify this person</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <button type="button" class="btn btn-add" onclick="addParent()">+ Add another Parent / Guardian</button>

                        <!-- Previous Schools Attended Section -->
                        <div class="form-section-header">
                          <i class="fas fa-school"></i> Previous Schools Attended
                        </div>
                        <div class="alert alert-light border-0 py-2 mb-4" style="font-size: 0.8rem; background: #f8f9fa;">
                          <i class="fas fa-info-circle me-2 text-primary"></i> Please enter the details of your previous schools attended from elementary to your current/recent school. You can input schools in any order.
                          <br><i class="fas fa-exclamation-circle me-2 text-danger"></i> Failure to include the latest/current school in the list will result in the reversion of your application.
                        </div>

                        <div id="schools-container">
                          <!-- Elementary -->
                          <div class="dynamic-container school-item">
                            <span class="school-group-title">Elementary :</span>
                            <div class="row g-3">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <input type="text" name="school_name[]" class="form-control" placeholder="Baybay Grace Christian School (Elementary)" required>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Year Graduated / Last Year Attended</label>
                                  <input type="text" name="school_year[]" class="form-control" placeholder="2020">
                                  <div class="note-text text-start">Note: If you're graduating this school year (2025-2026) kindly select year 2026</div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Level</label>
                                  <input type="text" name="school_level[]" class="form-control" value="Elementary" readonly>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Type</label>
                                  <select name="school_type[]" class="form-control">
                                    <option value="PRIVATE">PRIVATE</option>
                                    <option value="PUBLIC">PUBLIC</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Town/City & Province</label>
                                  <input type="text" name="school_city[]" class="form-control" placeholder="Santiago Naujan Oriental Mindoro">
                                  <div class="note-text text-start">Enter town name and select from the list suggested</div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Junior High School -->
                          <div class="dynamic-container school-item">
                            <span class="school-group-title">Junior High School :</span>
                            <div class="row g-3">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <input type="text" name="school_name[]" class="form-control" placeholder="Baybay National High School (Junior High School)">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Year Graduated / Last Year Attended</label>
                                  <input type="text" name="school_year[]" class="form-control" placeholder="2024">
                                  <div class="note-text text-start">Note: If you're graduating this school year (2025-2026) kindly select year 2026</div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Level</label>
                                  <input type="text" name="school_level[]" class="form-control" value="Junior High School" readonly>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Type</label>
                                  <select name="school_type[]" class="form-control">
                                    <option value="PUBLIC">PUBLIC</option>
                                    <option value="PRIVATE">PRIVATE</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Town/City & Province</label>
                                  <input type="text" name="school_city[]" class="form-control" placeholder="Santiago Naujan Oriental Mindoro">
                                  <div class="note-text text-start">Enter town name and select from the list suggested</div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Senior High School -->
                          <div class="dynamic-container school-item">
                            <span class="school-group-title">Senior High School :</span>
                            <div class="row g-3">
                              <div class="col-md-12">
                                <div class="form-group">
                                  <input type="text" name="school_name[]" class="form-control" placeholder="BAYBAY CITY SENIOR HIGH SCHOOL (Senior High School)">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Year Graduated / Last Year Attended</label>
                                  <input type="text" name="school_year[]" class="form-control" placeholder="2026">
                                  <div class="note-text text-start">Note: If you're graduating this school year (2025-2026) kindly select year 2026</div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Level</label>
                                  <input type="text" name="school_level[]" class="form-control" value="Senior High School" readonly>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Type</label>
                                  <select name="school_type[]" class="form-control">
                                    <option value="PUBLIC">PUBLIC</option>
                                    <option value="PRIVATE">PRIVATE</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Town/City & Province</label>
                                  <input type="text" name="school_city[]" class="form-control" placeholder="Santiago Naujan Oriental Mindoro">
                                  <div class="note-text text-start">Enter town name and select from the list suggested</div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <button type="button" class="btn btn-add" onclick="addSchool()">+ Add another School Attended</button>

                        <!-- Navigation Buttons -->
                        <div class="nav-buttons">
                          <button type="button" onclick="showAAPSection()" class="btn btn-nav-back"><i class="fas fa-chevron-left me-1"></i> BACK</button>
                          <button type="button" class="btn btn-nav-save" onclick="saveDraft(this)"><i class="fas fa-save"></i> SAVE</button>
                          <button type="button" onclick="nextStep(2)" class="btn btn-nav-next">NEXT <i class="fas fa-chevron-right ms-1"></i></button>
                        </div>
                        <div class="note-text mt-3">Note: You can save your application form information and continue any time</div>
                      </div>

                      <!-- Form Step 2: Academic Choice & Background -->
                      <div id="form-step-2" class="form-step-content d-none">
                        <div class="form-section-header">
                          <i class="fas fa-graduation-cap"></i> Academic Choice & Background
                        </div>
                        <div class="row g-3 mb-4">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>1st Choice Program</label>
                              <select name="program_id_1" class="form-control">
                                <option value="">Select your first choice...</option>
                                <?php foreach ($programs as $prog): ?>
                                  <option value="<?php echo $prog['id']; ?>">
                                    <?php echo htmlspecialchars($prog['title'] . ' (' . $prog['code'] . ')'); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>2nd Choice Program</label>
                              <select name="program_id_2" class="form-control">
                                <option value="">Select your second choice...</option>
                                <?php foreach ($programs as $prog): ?>
                                  <option value="<?php echo $prog['id']; ?>">
                                    <?php echo htmlspecialchars($prog['title'] . ' (' . $prog['code'] . ')'); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>
                        </div>

                        <div class="row g-3 mb-4">
                          <div class="col-md-12">
                            <!-- Removed "Any allergies or health problem?" as per client request -->
                          </div>
                          <div class="col-md-12">
                            <!-- Removed "Are you the first male in your family to attend college?" as per client request -->
                          </div>
                        </div>

                        <div class="form-section-header">
                          <i class="fas fa-info-circle"></i> Other Information
                        </div>

                        <div class="alert alert-info py-3 mb-4" style="font-size: 0.85rem;">
                          <div class="mb-2"><i class="fas fa-info-circle me-2"></i> Please enter your GPA or Rating based on your certification from school / TOR. Kindly convert your grade or GPA to a 100-point system if the grades indicated on your certification / TOR are not based on a 100-point scale.</div>
                          <div><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Inputting incorrect may result in your application being reverted.</div>
                        </div>

                        <div class="row g-3 mb-4">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Latest Education Attainment</label>
                              <select name="latest_attainment" class="form-control">
                                <option value="Graduating Senior High School">Graduating Senior High School</option>
                                <option value="High School Graduate">High School Graduate</option>
                                <option value="College Level">College Level</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Obtained GPA or Rating</label>
                              <input type="text" name="gpa_rating" class="form-control" placeholder="91.000" required>
                              <div class="note-text text-start">Please input Grade 10, 11 and 12 GPAs</div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>SHS Strand</label>
                              <select name="shs_strand" class="form-control">
                                <option value="STEM">STEM - Science, Technology, Engineering, and Mathematics</option>
                                <option value="ABM">ABM - Accountancy, Business, and Management</option>
                                <option value="HUMSS">HUMSS - Humanities and Social Sciences</option>
                                <option value="GAS">GAS - General Academic Strand</option>
                                <option value="TVL">TVL - Technical-Vocational-Livelihood</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Grade 10 GPA<span>*</span></label>
                              <input type="number" step="0.001" name="grade10_gpa" class="form-control" placeholder="85" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Grade 11 GPA<span>*</span></label>
                              <input type="number" step="0.001" name="grade11_gpa" class="form-control" placeholder="92" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Grade 12 GPA<span>*</span></label>
                              <input type="number" step="0.001" name="grade12_gpa" class="form-control" placeholder="96" required>
                            </div>
                          </div>
                        </div>

                        <div class="alert alert-warning py-2 mb-4" style="font-size: 0.85rem;">
                          <i class="fas fa-exclamation-triangle me-2"></i>
                          By submitting this form, you certify that all information provided is true and correct.
                        </div>

                        <div class="nav-buttons">
                          <button type="button" onclick="prevStep(1)" class="btn btn-nav-back"><i class="fas fa-chevron-left me-1"></i> BACK</button>
                          <button type="button" class="btn btn-nav-save" onclick="saveDraft(this)"><i class="fas fa-save"></i> SAVE</button>
                          <button type="button" onclick="nextStep(3)" class="btn btn-nav-next">NEXT <i class="fas fa-chevron-right ms-1"></i></button>
                        </div>
                        <div class="note-text mt-3">Note: You can save your application form information and continue any time</div>
                      </div>

                      <!-- Form Step 3: Attachments -->
                      <div id="form-step-3" class="form-step-content d-none">
                        <div class="form-section-header">
                          <i class="fas fa-paperclip"></i> Attachments
                        </div>

                        <div class="alert alert-warning py-3 mb-4" style="font-size: 0.85rem;">
                          <i class="fas fa-exclamation-triangle me-2"></i> Submitting incorrect or falsified attachments will result in your application being reverted and may lead to rejection or, in the worst case, blacklisting from our admission records.
                        </div>

                        <!-- 1. Valid ID -->
                        <div class="attachment-card">
                          <div class="attachment-title"><span class="text-danger">*</span> Any valid ID (School Id, Drivers license, Postal Id, Voters Id, National Id, Passport)</div>
                          <div class="attachment-subtitle">
                            <i class="fas fa-info-circle"></i> (If no valid ID, present voters' certification)
                          </div>
                          <input type="file" id="file_valid_id" name="attachments[valid_id][]" multiple class="d-none" accept=".png,.jpg,.jpeg,.pdf">
                          <button type="button" class="btn btn-select-file" onclick="document.getElementById('file_valid_id').click()">
                            <i class="fas fa-upload me-2"></i> Select file(s)
                          </button>
                          <div class="file-upload-note">
                            <strong>Note:</strong> Only <strong>png, jpeg/jpg, pdf</strong> are allowed file types. Limit of <strong>3</strong> file(s) to upload
                          </div>
                          <div id="preview_valid_id" class="preview-container"></div>
                        </div>

                        <!-- 2. Senior High School Certification -->
                        <div class="attachment-card">
                          <div class="attachment-title"><span class="text-danger">*</span> Senior High School Graduate (Form 138)</div>
                          <div class="attachment-subtitle">
                            <i class="fas fa-info-circle"></i> Certification of your grades with a computed Grade Point Average (GPA) from Grades 10, 11 and 12 (Report Card/Form 138)
                          </div>
                          <input type="file" id="file_shs_cert" name="attachments[shs_cert][]" class="d-none" accept=".png,.jpg,.jpeg">
                          <button type="button" class="btn btn-select-file" onclick="document.getElementById('file_shs_cert').click()">
                            <i class="fas fa-upload me-2"></i> Select file(s)
                          </button>
                          <div class="file-upload-note">
                            <strong>Note:</strong> Only <strong>png, jpeg/jpg</strong> are allowed file types. Limit of <strong>1</strong> file(s) to upload
                          </div>
                          <div id="preview_shs_cert" class="preview-container"></div>
                        </div>

                        <!-- 3. Certificate of Good Moral Character -->
                        <div class="attachment-card">
                          <div class="attachment-title"><span class="text-danger">*</span> Certificate of Good Moral Character</div>
                          <div class="attachment-subtitle">
                            <i class="fas fa-info-circle"></i> Please upload a clear copy of your Certificate of Good Moral Character from your previous school.
                          </div>
                          <input type="file" id="file_good_moral" name="attachments[good_moral][]" class="d-none" accept=".png,.jpg,.jpeg,.pdf">
                          <button type="button" class="btn btn-select-file" onclick="document.getElementById('file_good_moral').click()">
                            <i class="fas fa-upload me-2"></i> Select file(s)
                          </button>
                          <div class="file-upload-note">
                            <strong>Note:</strong> Only <strong>png, jpeg/jpg, pdf</strong> are allowed file types. Limit of <strong>1</strong> file(s) to upload
                          </div>
                          <div id="preview_good_moral" class="preview-container"></div>
                        </div>

                        <!-- 4. Certificate of Graduation / Diploma -->
                        <div class="attachment-card">
                          <div class="attachment-title"><span class="text-danger">*</span> Certificate of Graduation / Diploma</div>
                          <div class="attachment-subtitle">
                            <i class="fas fa-info-circle"></i> Please upload your Diploma or Certificate of Graduation.
                          </div>
                          <input type="file" id="file_diploma" name="attachments[diploma][]" class="d-none" accept=".png,.jpg,.jpeg,.pdf">
                          <button type="button" class="btn btn-select-file" onclick="document.getElementById('file_diploma').click()">
                            <i class="fas fa-upload me-2"></i> Select file(s)
                          </button>
                          <div class="file-upload-note">
                            <strong>Note:</strong> Only <strong>png, jpeg/jpg, pdf</strong> are allowed file types. Limit of <strong>1</strong> file(s) to upload
                          </div>
                          <div id="preview_diploma" class="preview-container"></div>
                        </div>

                        <!-- 5. Transcript of Records (For Transferees) -->
                        <div class="attachment-card">
                          <div class="attachment-title"><span class="text-danger">*</span> Transcript of Records (TOR)</div>
                          <div class="attachment-subtitle">
                            <i class="fas fa-info-circle"></i> Please upload your official Transcript of Records from your previous college.
                          </div>
                          <input type="file" id="file_tor" name="attachments[tor][]" class="d-none" accept=".png,.jpg,.jpeg,.pdf">
                          <button type="button" class="btn btn-select-file" onclick="document.getElementById('file_tor').click()">
                            <i class="fas fa-upload me-2"></i> Select file(s)
                          </button>
                          <div class="file-upload-note">
                            <strong>Note:</strong> Only <strong>png, jpeg/jpg, pdf</strong> are allowed file types. Limit of <strong>1</strong> file(s) to upload
                          </div>
                          <div id="preview_tor" class="preview-container"></div>
                        </div>

                        <!-- 6. Honorable Dismissal (For Transferees) -->
                        <div class="attachment-card">
                          <div class="attachment-title"><span class="text-danger">*</span> Honorable Dismissal / Transfer Credentials</div>
                          <div class="attachment-subtitle">
                            <i class="fas fa-info-circle"></i> Please upload your Honorable Dismissal or Transfer Credentials.
                          </div>
                          <input type="file" id="file_transfer_cred" name="attachments[transfer_cred][]" class="d-none" accept=".png,.jpg,.jpeg,.pdf">
                          <button type="button" class="btn btn-select-file" onclick="document.getElementById('file_transfer_cred').click()">
                            <i class="fas fa-upload me-2"></i> Select file(s)
                          </button>
                          <div class="file-upload-note">
                            <strong>Note:</strong> Only <strong>png, jpeg/jpg, pdf</strong> are allowed file types. Limit of <strong>1</strong> file(s) to upload
                          </div>
                          <div id="preview_transfer_cred" class="preview-container"></div>
                        </div>

                        <div class="nav-buttons">
                          <button type="button" onclick="prevStep(2)" class="btn btn-nav-back"><i class="fas fa-chevron-left me-1"></i> BACK</button>
                          <button type="button" class="btn btn-nav-save" onclick="saveDraft(this)"><i class="fas fa-save"></i> SAVE</button>
                          <button type="button" onclick="showReviewStep()" class="btn btn-nav-next">NEXT: REVIEW <i class="fas fa-chevron-right ms-1"></i></button>
                        </div>
                        <div class="note-text mt-3">Note: You can save your application form information and continue any time</div>
                      </div>

                      <!-- Form Step 4: Review Application -->
                      <div id="form-step-4" class="form-step-content d-none">
                        <div class="form-section-header">
                          <i class="fas fa-eye"></i> Review Your Application
                        </div>
                        
                        <div class="alert alert-info py-3 mb-4" style="font-size: 0.85rem;">
                          <i class="fas fa-info-circle me-2"></i> Please review all the information you've entered before final submission. You can go back to any section to make corrections.
                        </div>

                        <div id="review-content">
                          <!-- Content will be dynamically populated by JS -->
                          <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                              <span class="visually-hidden">Loading review...</span>
                            </div>
                            <p class="mt-2">Preparing your application summary...</p>
                          </div>
                        </div>

                        <div class="nav-buttons">
                          <button type="button" onclick="prevStep(3)" class="btn btn-nav-back"><i class="fas fa-chevron-left me-1"></i> BACK</button>
                          <button type="button" onclick="showSubmitStep()" class="btn btn-nav-next">NEXT: FINAL SUBMIT <i class="fas fa-chevron-right ms-1"></i></button>
                        </div>
                      </div>

                      <!-- Form Step 5: Final Submit -->
                      <div id="form-step-5" class="form-step-content d-none">
                        <div class="form-section-header">
                          <i class="fas fa-check-double"></i> Final Submission
                        </div>

                        <div class="text-center mb-5">
                          <div class="mb-4">
                            <i class="fas fa-file-signature fa-4x text-primary animate__animated animate__bounceIn"></i>
                          </div>
                          <h4 class="fw-bold" style="color: var(--primary-blue);">Ready to Submit?</h4>
                          <p class="text-muted">You are about to submit your application for admission to Colegio De Naujan.</p>
                        </div>

                        <div class="guideline-card mb-4" style="border-left-color: #ffc107;">
                          <h5 class="mb-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Important Reminders</h5>
                          <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex align-items-start">
                              <i class="fas fa-check text-success mt-1 me-2"></i>
                              <span>Once submitted, you can no longer edit your application details unless requested by the admissions team.</span>
                            </li>
                            <li class="mb-2 d-flex align-items-start">
                              <i class="fas fa-check text-success mt-1 me-2"></i>
                              <span>Ensure all uploaded documents are authentic and clear.</span>
                            </li>
                            <li class="d-flex align-items-start">
                              <i class="fas fa-check text-success mt-1 me-2"></i>
                              <span>We will communicate with you primarily through your verified email address.</span>
                            </li>
                          </ul>
                        </div>

                        <div class="p-4 bg-light rounded border mb-4">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="finalConsentCheck" style="width: 20px; height: 20px; cursor: pointer;">
                            <label class="form-check-label fw-bold ms-2" for="finalConsentCheck" style="cursor: pointer; padding-top: 2px;">
                              I hereby certify that all information provided in this application is true and correct to the best of my knowledge. I understand that any false statement or omission of facts may be grounds for rejection or cancellation of my admission.
                            </label>
                          </div>
                        </div>

                        <div class="nav-buttons">
                          <button type="button" onclick="prevStep(4)" class="btn btn-nav-back"><i class="fas fa-chevron-left me-1"></i> BACK</button>
                          <button type="button" onclick="submitApplication()" id="submitBtn" class="btn btn-nav-next" style="background-color: #28a745; color: white; padding: 12px 40px;">
                            <span id="btnText">SUBMIT APPLICATION <i class="fas fa-paper-plane ms-2"></i></span>
                            <span id="btnLoader" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                          </button>
                        </div>
                      </div>
                      </div>
                    </form>
                </div>
              </div>

              <!-- STATUS SECTION (Shown after submission) -->
              <div id="status-section" class="section-portal <?php echo ($admission && $current_status != 'draft') ? 'active' : ''; ?>">
                <?php 
                $status = $admission ? $current_status : 'pending';
                $app_id = $admission ? $admission['application_id'] : 'APP-' . strtoupper(substr(md5(time()), 0, 8));
                ?>
                
                <?php if (in_array(strtolower($status), ['new', 'pending', 'verified', 'scheduled', 'processing'])): ?>
                  <div class="text-center mb-4">
                    <div class="mb-3">
                      <?php if (strtolower($status) == 'scheduled'): ?>
                        <i class="fas fa-calendar-check fa-3x text-info animate__animated animate__bounceIn"></i>
                      <?php elseif (strtolower($status) == 'verified'): ?>
                        <i class="fas fa-user-check fa-3x text-success animate__animated animate__bounceIn"></i>
                      <?php else: ?>
                        <i class="fas fa-clock fa-3x text-warning animate__animated animate__pulse animate__infinite"></i>
                      <?php endif; ?>
                    </div>
                    <h4 class="fw-bold" style="color: var(--primary-blue);">
                      <?php 
                        if (strtolower($status) == 'scheduled') echo 'Examination Scheduled';
                        elseif (strtolower($status) == 'verified') echo 'Documents Verified';
                        else echo 'Application Under Review';
                      ?>
                    </h4>
                    <p class="text-muted">
                      <?php if (strtolower($status) == 'scheduled'): ?>
                        Your application (ID: <strong id="static-app-id"><?php echo htmlspecialchars($app_id); ?></strong>) has been verified and your entrance examination has been scheduled. Please check your email for the schedule details.
                      <?php elseif (strtolower($status) == 'verified'): ?>
                        Your application (ID: <strong id="static-app-id"><?php echo htmlspecialchars($app_id); ?></strong>) has been verified. We are now preparing your examination schedule.
                      <?php else: ?>
                        Your application (ID: <strong id="static-app-id"><?php echo htmlspecialchars($app_id); ?></strong>) has been successfully submitted and is currently being reviewed by our admissions team.
                      <?php endif; ?>
                    </p>
                  </div>

                  <div class="guideline-card mx-auto mb-4" style="max-width: 700px; border-left-color: var(--primary-blue);">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-info-circle text-primary me-2"></i> What's Next?</h5>
                    <ul class="list-unstyled">
                      <li class="mb-3 d-flex align-items-start">
                        <div class="guideline-icon" style="background: rgba(40, 167, 69, 0.1); color: var(--success-green);">
                          <i class="fas fa-file-check"></i>
                        </div>
                        <div>
                          <strong class="d-block text-dark">Document Verification</strong>
                          <p class="mb-0 small text-muted">We are verifying your academic details and uploaded documents. This process usually takes 1-3 working days.</p>
                        </div>
                      </li>
                      <li class="mb-3 d-flex align-items-start">
                        <div class="guideline-icon" style="background: rgba(26, 54, 93, 0.1); color: var(--primary-blue);">
                          <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <div>
                          <strong class="d-block text-dark">Email Notification</strong>
                          <p class="mb-0 small text-muted">Once verified, an official notification will be sent to <strong><?php echo htmlspecialchars($email); ?></strong> regarding your admission status.</p>
                        </div>
                      </li>
                      <li class="d-flex align-items-start">
                        <div class="guideline-icon" style="background: rgba(212, 175, 55, 0.1); color: var(--accent-gold);">
                          <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                          <strong class="d-block text-dark">Entrance Exam Schedule</strong>
                          <p class="mb-0 small text-muted">Your entrance exam schedule will be included in the email. Please prepare the following:</p>
                          <ul class="small text-muted mt-2 ps-3">
                            <li>Printed copy of your Application Form</li>
                            <li>Original Form 138 (Report Card)</li>
                            <li>2x2 ID Picture</li>
                            <li>Black Ballpen</li>
                          </ul>
                        </div>
                      </li>
                    </ul>
                  </div>

                  <div class="alert alert-info mx-auto" style="max-width: 700px; font-size: 0.85rem;">
                    <i class="fas fa-info-circle me-2"></i> <strong>Note:</strong> Please keep your email active and check your inbox (including the Spam folder) regularly for updates.
                  </div>

                  <!-- Submitted Application Summary -->
                  <div class="mt-5 mx-auto" style="max-width: 800px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <h5 class="fw-bold mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Submitted Application Details</h5>
                      <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#submittedReviewCollapse">
                        <i class="fas fa-eye me-1"></i> View Details
                      </button>
                    </div>
                    <div class="collapse" id="submittedReviewCollapse">
                      <div id="status-review-content">
                        <!-- Content will be populated by JS -->
                      </div>
                    </div>
                  </div>
                <?php elseif ($status == 'approved'): ?>
                  <div class="text-center">
                    <div class="mb-3">
                      <i class="fas fa-graduation-cap fa-4x text-success"></i>
                    </div>
                    <h3 class="fw-bold" style="color: var(--primary-blue);">Congratulations!</h3>
                    <p class="lead">Your application has been <strong>Approved</strong>.</p>
                    <div class="alert alert-success mx-auto" style="max-width: 600px;">
                      <i class="fas fa-info-circle me-2"></i>
                      Please proceed to the Registrar's Office for your official enrollment and ID processing.
                    </div>
                  </div>
                <?php elseif ($status == 'rejected'): ?>
                  <div class="text-center">
                    <div class="mb-3">
                      <i class="fas fa-times-circle fa-4x text-danger"></i>
                    </div>
                    <h4>Application Not Approved</h4>
                    <p class="text-muted">We regret to inform you that your application could not be processed at this time.</p>
                    <div class="alert alert-secondary mx-auto" style="max-width: 600px;">
                      <strong>Reason:</strong> <?php echo htmlspecialchars($admission['notes'] ?? 'No notes provided.'); ?>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center">
          <a href="../../index.php" class="text-muted text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Back to Home
          </a>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <script>
    // Inject PHP data into JavaScript
    const existingAdmission = <?php echo $existing_data_json; ?>;
    
    function updateSteps(activeStepId) {
      // List of all step markers
      const steps = [
        'step-marker-welcome', 'step-marker-guidelines', 'step-marker-aap', 
        'step-marker-personal', 'step-marker-education', 'step-marker-attachments', 
        'step-marker-review', 'step-marker-submit'
      ];
      
      let foundActive = false;
      steps.forEach(stepId => {
        const el = document.getElementById(stepId);
        if (!el) return;
        
        if (stepId === activeStepId) {
          el.classList.add('active');
          el.classList.remove('completed');
          foundActive = true;
        } else if (!foundActive) {
          el.classList.add('completed');
          el.classList.remove('active');
        } else {
          el.classList.remove('active', 'completed');
        }
      });
    }

    function showAAPSection() {
      document.getElementById('guidelines-section').classList.remove('active');
      document.getElementById('form-section').classList.remove('active');
      setTimeout(() => {
        document.getElementById('aap-section').classList.add('active');
        updateSteps('step-marker-aap');
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }, 300);
    }

    function toggleAAPConditional(id, show) {
      const el = document.getElementById(id);
      if (show) {
        el.classList.remove('d-none');
        el.classList.add('animate__animated', 'animate__fadeIn');
        el.querySelectorAll('input').forEach(i => i.required = true);
      } else {
        el.classList.add('d-none');
        el.querySelectorAll('input').forEach(i => {
          i.required = false;
          i.value = '';
        });
      }
    }

    function showApplicationForm() {
      const aapForm = document.getElementById('aapForm');
      
      // Validate required radio buttons
      const radioGroups = ['academic_status', 'already_enrolled', 'first_time_apply', 'shs_transfer'];
      let missingSelection = false;
      
      for (const name of radioGroups) {
        const radios = aapForm.querySelectorAll(`input[name="${name}"]`);
        let selected = false;
        radios.forEach(r => { if (r.checked) selected = true; });
        
        if (!selected) {
          missingSelection = true;
          break; 
        }
      }
      
      if (missingSelection) {
        Swal.fire({
          icon: 'warning',
          title: 'Missing Information',
          text: 'Please answer all questions to proceed.',
          confirmButtonColor: '#d4af37'
        });
        return;
      }
      
      // Validate conditional inputs
      if (!aapForm.checkValidity()) {
        aapForm.reportValidity();
        return;
      }

      document.getElementById('aap-section').classList.remove('active');
      setTimeout(() => {
        document.getElementById('form-section').classList.add('active');
        
        // Reset form to step 1
        document.querySelectorAll('.form-step-content').forEach(el => el.classList.add('d-none'));
        const step1 = document.getElementById('form-step-1');
        step1.classList.remove('d-none');
        step1.classList.add('animate__animated', 'animate__fadeInRight');
        
        updateSteps('step-marker-personal');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Auto-save progress
        saveDraft();
      }, 300);
    }

    function addParent() {
      const container = document.getElementById('parents-container');
      const itemCount = container.querySelectorAll('.parent-item').length + 1;
      const html = `
        <div class="dynamic-container parent-item mt-4 animate__animated animate__fadeIn">
          <div class="row g-3">
            <div class="col-md-3">
              <div class="form-group">
                <label>First Name<span>*</span></label>
                <input type="text" name="parent_first_name[]" class="form-control" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="parent_middle_name[]" class="form-control">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Last Name<span>*</span></label>
                <input type="text" name="parent_last_name[]" class="form-control" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Extension Name</label>
                <input type="text" name="parent_extension[]" class="form-control">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Age<span>*</span></label>
                <input type="number" name="parent_age[]" class="form-control" required>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Relationship<span>*</span></label>
                <select name="parent_relationship[]" class="form-control" required>
                  <option value="Father">Father</option>
                  <option value="Mother">Mother</option>
                  <option value="Guardian">Guardian</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Highest Educational Attainment<span>*</span></label>
                <input type="text" name="parent_education[]" class="form-control" required>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Occupation<span>*</span></label>
                <input type="text" name="parent_occupation[]" class="form-control" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Monthly income (in PHP)<span>*</span></label>
                <input type="text" name="parent_income[]" class="form-control" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Contact no.<span>*</span></label>
                <input type="text" name="parent_contact[]" class="form-control" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Street No/Brgy<span>*</span></label>
                <input type="text" name="parent_street[]" class="form-control" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Town/City & Province<span>*</span></label>
                <input type="text" name="parent_city[]" class="form-control" placeholder="Santiago Naujan Oriental Mindoro" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group border p-2 rounded bg-white">
                <label class="mb-0 d-block">Emergency Contact Person</label>
                <div class="form-check mt-1">
                  <input class="form-check-input" type="checkbox" name="is_emergency[]" id="emergency${itemCount}">
                  <label class="form-check-label" for="emergency${itemCount}" style="font-size: 0.75rem;">Notify this person</label>
                </div>
              </div>
            </div>
          </div>
          <button type="button" class="btn-remove" onclick="removeItem(this)">× Remove Parent / Guardian</button>
          <div class="note-text mt-2">Note: You can only delete / remove the last Parent / Guardian you added.</div>
        </div>
      `;
      container.insertAdjacentHTML('beforeend', html);
    }

    function addSchool() {
      const container = document.getElementById('schools-container');
      const html = `
        <div class="dynamic-container school-item mt-4 animate__animated animate__fadeIn">
          <span class="school-group-title">Other School :</span>
          <div class="row g-3">
            <div class="col-md-12">
              <div class="form-group">
                <input type="text" name="school_name[]" class="form-control" placeholder="Select a school" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Year Graduated / Last Year Attended<span>*</span></label>
                <input type="text" name="school_year[]" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Level<span>*</span></label>
                <select name="school_level[]" class="form-control" required>
                  <option value="Senior High School">Senior High School</option>
                  <option value="College">College</option>
                  <option value="Vocational">Vocational</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Type<span>*</span></label>
                <select name="school_type[]" class="form-control" required>
                  <option value="PUBLIC">PUBLIC</option>
                  <option value="PRIVATE">PRIVATE</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Town/City & Province<span>*</span></label>
                <input type="text" name="school_city[]" class="form-control" placeholder="Santiago Naujan Oriental Mindoro" required>
              </div>
            </div>
          </div>
          <button type="button" class="btn-remove" onclick="removeItem(this)">× Remove School</button>
          <div class="note-text mt-2">Note: You can only delete / remove the last School you added.</div>
        </div>
      `;
      container.insertAdjacentHTML('beforeend', html);
    }

    function removeItem(btn) {
      const item = btn.closest('.dynamic-container');
      item.classList.remove('animate__fadeIn');
      item.classList.add('animate__fadeOut');
      setTimeout(() => item.remove(), 500);
    }
    
    function showGuidelines() {
      document.getElementById('form-section').classList.remove('active');
      document.getElementById('aap-section').classList.remove('active');
      setTimeout(() => {
        document.getElementById('guidelines-section').classList.add('active');
        updateSteps('step-marker-guidelines');
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }, 300);
    }

    function validateStep(step) {
      const currentStepEl = document.getElementById(`form-step-${step}`);
      const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
      let isValid = true;
      let firstInvalid = null;

      // Reset previous validation states
      currentStepEl.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

      inputs.forEach(input => {
        if (!input.value.trim()) {
          isValid = false;
          input.classList.add('is-invalid');
          if (!firstInvalid) firstInvalid = input;
        }
      });

      // Special validation for Step 2 (Attachments) - Wait, step param passed to nextStep is the target step.
      // If we are calling validateStep(currentStep), then step 3 corresponds to Attachments.
      
      if (step === 3) {
        // Check standard required files
        const requiredFiles = ['file_valid_id', 'file_shs_cert', 'file_good_moral', 'file_diploma'];
        
        requiredFiles.forEach(id => {
            const input = document.getElementById(id);
            if (input && (!input.files || input.files.length === 0)) {
                // Check if we have a preview (meaning file was uploaded/selected previously and not cleared)
                // But wait, the file input clears on reload, but we are in SPA mode.
                // If user selected file, went back, came forward, input.files might still be populated?
                // Actually, if we use removeFile, we update input.files.
                // So checking input.files.length should be sufficient.
                
                // One edge case: if user is editing an existing application (not supported yet based on code), 
                // we might have existing files on server but empty input. 
                // But this is a "create" form. So we expect new uploads.
                
                isValid = false;
                // Add visual cue to the button or container
                const btn = input.nextElementSibling; // The button
                if (btn) btn.classList.add('btn-outline-danger');
                if (!firstInvalid) firstInvalid = input; // Input is hidden, so focus might not work well
            } else {
                const btn = input.nextElementSibling;
                if (btn) btn.classList.remove('btn-outline-danger');
            }
        });
      }

      if (!isValid) {
        if (firstInvalid) {
           // If it's a file input (hidden), scroll to its container
           if (firstInvalid.type === 'file') {
               firstInvalid.closest('.attachment-card').scrollIntoView({ behavior: 'smooth', block: 'center' });
           } else {
               firstInvalid.focus();
               firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
           }
        }
        Swal.fire({
          icon: 'error',
          title: 'Missing Information',
          text: 'Please fill out all required fields marked with *',
          confirmButtonColor: '#d33'
        });
      }

      return isValid;
    }

    function nextStep(step) {
      const currentStep = step - 1;
      
      // Validate current step before proceeding
      if (!validateStep(currentStep)) {
          return;
      }

      const currentStepEl = document.getElementById(`form-step-${currentStep}`);
      currentStepEl.classList.add('d-none');
      const nextStepEl = document.getElementById(`form-step-${step}`);
      nextStepEl.classList.remove('d-none', 'animate__fadeInLeft');
      nextStepEl.classList.add('animate__animated', 'animate__fadeInRight');
      
      // Update horizontal steps
      if (step === 2) updateSteps('step-marker-education');
      if (step === 3) updateSteps('step-marker-attachments');
      
      window.scrollTo({ top: 0, behavior: 'smooth' });
      
      // Auto-save progress
      saveDraft();
    }

    function showReviewStep() {
      // Validate current step (Attachments - Step 3)
      if (!validateStep(3)) {
          return;
      }

      // Hide current step (Attachments)
      document.getElementById('form-step-3').classList.add('d-none');
      
      // Show Review step
      const reviewStep = document.getElementById('form-step-4');
      reviewStep.classList.remove('d-none');
      reviewStep.classList.add('animate__animated', 'animate__fadeInRight');
      
      // Update steps marker
      updateSteps('step-marker-review');
      
      // Populate review content
      populateReviewContent();
      
      window.scrollTo({ top: 0, behavior: 'smooth' });
      
      // Auto-save progress
      saveDraft();
    }

    function showSubmitStep() {
      // Hide current step (Review)
      document.getElementById('form-step-4').classList.add('d-none');
      
      // Show Submit step
      const submitStep = document.getElementById('form-step-5');
      submitStep.classList.remove('d-none');
      submitStep.classList.add('animate__animated', 'animate__fadeInRight');
      
      // Update steps marker
      updateSteps('step-marker-submit');
      
      window.scrollTo({ top: 0, behavior: 'smooth' });
      
      // Auto-save progress
      saveDraft();
    }

    function populateReviewContent(targetContainerId = 'review-content') {
      const form = document.getElementById('admissionForm');
      const formData = new FormData(form);
      const reviewContainer = document.getElementById(targetContainerId);
      
      if (!reviewContainer) return;

      const isStatusPage = targetContainerId === 'status-review-content';
      let html = '';
      
      // Data source: use existingAdmission if form is empty (returning student)
      const useExisting = isStatusPage && typeof existingAdmission !== 'undefined' && existingAdmission && !formData.get('first_name');
      
      // Helper to get value
      const getVal = (name) => {
          if (useExisting) {
              // Try form_data first, then top-level details
              return existingAdmission.form_data[name] || existingAdmission.details[name] || '<span class="text-muted">Not provided</span>';
          }
          return formData.get(name) || '<span class="text-muted">Not provided</span>';
      };
      
      // Helper to get selected text
      const getSelectText = (name) => {
          if (useExisting) {
              const val = existingAdmission.details[name] || existingAdmission.form_data[name];
              if (name === 'program_id_1' || name === 'program_id_2') {
                  const progId = name === 'program_id_1' ? existingAdmission.details.program_id : existingAdmission.form_data.alternative_program;
                  const prog = <?php echo json_encode($programs); ?>.find(p => p.id == progId);
                  return prog ? prog.title + ' (' + prog.code + ')' : '<span class="text-muted">Not selected</span>';
              }
              return val || '<span class="text-muted">Not selected</span>';
          }
          const el = form.querySelector(`select[name="${name}"] option:checked`);
          return el ? el.text : '<span class="text-muted">Not selected</span>';
      };
      
      // Personal Information
      html += `
        <div class="review-summary-card animate__animated animate__fadeIn">
          <div class="review-section-title">
            <span><i class="fas fa-user me-2"></i> Personal Information</span>
            ${!isStatusPage ? '<button type="button" class="btn btn-sm btn-outline-primary" onclick="prevStep(3); prevStep(2); prevStep(1);"><i class="fas fa-edit"></i> Edit</button>' : ''}
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="review-item-label">Full Name</div>
              <div class="review-item-value">${getVal('first_name')} ${getVal('middle_name')} ${getVal('last_name')} ${getVal('extension_name') || getVal('suffix') || ''}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">Gender</div>
              <div class="review-item-value">${getVal('gender')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">Date of Birth</div>
              <div class="review-item-value">${getVal('birthdate')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">Contact Number</div>
              <div class="review-item-value">${getVal('phone')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">Civil Status</div>
              <div class="review-item-value">${getVal('civil_status')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">Citizenship</div>
              <div class="review-item-value">${getVal('citizenship')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">Birth Place</div>
              <div class="review-item-value">${getVal('birth_place')}</div>
            </div>
            <div class="col-md-8">
              <div class="review-item-label">Address</div>
              <div class="review-item-value">${getVal('address') || (getVal('street_no') + ' ' + getVal('barangay') + ' ' + getVal('city_province') + ' ' + getVal('zip_code'))}</div>
            </div>
          </div>
        </div>
      `;
      
      // Academic Information
      html += `
        <div class="review-summary-card animate__animated animate__fadeIn" style="animation-delay: 0.1s">
          <div class="review-section-title">
            <span><i class="fas fa-graduation-cap me-2"></i> Academic Choices</span>
            ${!isStatusPage ? '<button type="button" class="btn btn-sm btn-outline-primary" onclick="prevStep(3); prevStep(2);"><i class="fas fa-edit"></i> Edit</button>' : ''}
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="review-item-label">1st Choice Program</div>
              <div class="review-item-value">${getSelectText('program_id_1')}</div>
            </div>
            <div class="col-md-6">
              <div class="review-item-label">2nd Choice Program</div>
              <div class="review-item-value">${getSelectText('program_id_2')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">SHS Strand</div>
              <div class="review-item-value">${getVal('shs_strand')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">GPA / Rating (GWA)</div>
              <div class="review-item-value">${getVal('gpa_rating') || getVal('gwa')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">Grade 10 GPA</div>
              <div class="review-item-value">${getVal('grade10_gpa')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">Grade 11 GPA</div>
              <div class="review-item-value">${getVal('grade11_gpa')}</div>
            </div>
            <div class="col-md-4">
              <div class="review-item-label">Grade 12 GPA</div>
              <div class="review-item-value">${getVal('grade12_gpa')}</div>
            </div>
          </div>
        </div>
      `;

      // Family Background
      let parentsHtml = '';
      if (useExisting && existingAdmission.form_data.parents) {
          parentsHtml += '<div class="row">';
          existingAdmission.form_data.parents.forEach((parent, i) => {
              parentsHtml += `
                <div class="col-md-6 mb-3">
                    <div class="p-3 bg-light rounded border h-100">
                        <div class="fw-bold text-primary mb-1">${parent.relationship}</div>
                        <div class="fw-medium">${parent.first_name} ${parent.last_name}</div>
                        <div class="small text-muted mt-1"><i class="fas fa-phone me-1"></i> ${parent.contact}</div>
                    </div>
                </div>`;
          });
          parentsHtml += '</div>';
      } else {
          const parentFirstNames = formData.getAll('parent_first_name[]');
          const parentLastNames = formData.getAll('parent_last_name[]');
          const parentRelations = formData.getAll('parent_relationship[]');
          const parentContacts = formData.getAll('parent_contact[]');
          
          if (parentFirstNames.length > 0) {
              parentsHtml += '<div class="row">';
              parentFirstNames.forEach((fname, i) => {
                  parentsHtml += `
                    <div class="col-md-6 mb-3">
                        <div class="p-3 bg-light rounded border h-100">
                            <div class="fw-bold text-primary mb-1">${parentRelations[i]}</div>
                            <div class="fw-medium">${fname} ${parentLastNames[i]}</div>
                            <div class="small text-muted mt-1"><i class="fas fa-phone me-1"></i> ${parentContacts[i]}</div>
                        </div>
                    </div>`;
              });
              parentsHtml += '</div>';
          }
      }

      html += `
        <div class="review-summary-card animate__animated animate__fadeIn" style="animation-delay: 0.15s">
          <div class="review-section-title">
            <span><i class="fas fa-users me-2"></i> Family Background</span>
            ${!isStatusPage ? '<button type="button" class="btn btn-sm btn-outline-primary" onclick="prevStep(3); prevStep(2); prevStep(1);"><i class="fas fa-edit"></i> Edit</button>' : ''}
          </div>
          ${parentsHtml || '<div class="text-muted">No parent/guardian information provided.</div>'}
        </div>
      `;

      // Educational History
      let schoolsHtml = '';
      if (useExisting && existingAdmission.form_data.schools) {
          schoolsHtml += '<div class="row">';
          existingAdmission.form_data.schools.forEach((school, i) => {
              schoolsHtml += `
                <div class="col-md-12 mb-2">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <div>
                            <div class="fw-bold">${school.name}</div>
                            <div class="small text-muted">${school.level}</div>
                        </div>
                        <div class="badge bg-secondary">${school.year}</div>
                    </div>
                </div>`;
          });
          schoolsHtml += '</div>';
      } else {
          const schoolNames = formData.getAll('school_name[]');
          const schoolLevels = formData.getAll('school_level[]');
          const schoolYears = formData.getAll('school_year[]');
          
          if (schoolNames.length > 0) {
              schoolsHtml += '<div class="row">';
              schoolNames.forEach((name, i) => {
                  schoolsHtml += `
                    <div class="col-md-12 mb-2">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                            <div>
                                <div class="fw-bold">${name}</div>
                                <div class="small text-muted">${schoolLevels[i]}</div>
                            </div>
                            <div class="badge bg-secondary">${schoolYears[i]}</div>
                        </div>
                    </div>`;
              });
              schoolsHtml += '</div>';
          }
      }

      html += `
        <div class="review-summary-card animate__animated animate__fadeIn" style="animation-delay: 0.18s">
          <div class="review-section-title">
            <span><i class="fas fa-school me-2"></i> Educational History</span>
            ${!isStatusPage ? '<button type="button" class="btn btn-sm btn-outline-primary" onclick="prevStep(3); prevStep(2); prevStep(1);"><i class="fas fa-edit"></i> Edit</button>' : ''}
          </div>
          ${schoolsHtml || '<div class="text-muted">No school information provided.</div>'}
        </div>
      `;

      // Attachments
      let attachmentsHtml = '';
      if (useExisting && existingAdmission.attachments) {
          const atts = existingAdmission.attachments;
          for (const type in atts) {
              const label = type.replace(/_/g, ' ').toUpperCase();
              const count = Array.isArray(atts[type]) ? atts[type].length : 1;
              attachmentsHtml += `<div class="badge bg-success me-2 mb-2 p-2"><i class="fas fa-file-check me-1"></i> ${label} (${count} file(s))</div>`;
          }
      } else {
          const attachmentInputs = ['file_valid_id', 'file_shs_cert', 'file_good_moral', 'file_diploma', 'file_tor', 'file_transfer_cred', 'file_4ps', 'file_equity'];
          attachmentInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input && input.files.length > 0) {
              const label = input.closest('.attachment-card').querySelector('.attachment-title').textContent.replace('*', '').trim();
              attachmentsHtml += `<div class="badge bg-success me-2 mb-2 p-2"><i class="fas fa-file-check me-1"></i> ${label} (${input.files.length} file(s))</div>`;
            }
          });
      }

      html += `
        <div class="review-summary-card animate__animated animate__fadeIn" style="animation-delay: 0.2s">
          <div class="review-section-title">
            <span><i class="fas fa-paperclip me-2"></i> Uploaded Documents</span>
            ${!isStatusPage ? '<button type="button" class="btn btn-sm btn-outline-primary" onclick="prevStep(3);"><i class="fas fa-edit"></i> Edit</button>' : ''}
          </div>
          <div class="d-flex flex-wrap">
            ${attachmentsHtml || '<div class="text-danger">No documents uploaded!</div>'}
          </div>
        </div>
      `;
      
      reviewContainer.innerHTML = html;
    }

    function prevStep(step) {
      const currentStep = step + 1;
      const currentStepEl = document.getElementById(`form-step-${currentStep}`);
      
      if (currentStepEl) currentStepEl.classList.add('d-none');
      const prevStepEl = document.getElementById(`form-step-${step}`);
      if (prevStepEl) {
        prevStepEl.classList.remove('d-none', 'animate__fadeInRight');
        prevStepEl.classList.add('animate__animated', 'animate__fadeInLeft');
      }
      
      // Update horizontal steps
      if (step === 1) updateSteps('step-marker-personal');
      if (step === 2) updateSteps('step-marker-education');
      if (step === 3) updateSteps('step-marker-attachments');
      if (step === 4) updateSteps('step-marker-review');
      
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateSteps(activeId) {
      const steps = [
        'step-marker-welcome', 
        'step-marker-guidelines', 
        'step-marker-aap', 
        'step-marker-personal', 
        'step-marker-education', 
        'step-marker-attachments',
        'step-marker-review',
        'step-marker-submit'
      ];
      
      let foundActive = false;
      steps.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        
        if (id === activeId) {
          el.classList.add('active');
          el.classList.remove('completed');
          foundActive = true;
        } else if (!foundActive) {
          el.classList.add('completed');
          el.classList.remove('active');
        } else {
          el.classList.remove('active', 'completed');
        }
      });
    }

    function showNoticeAndSubmit() {
      Swal.fire({
        title: '<div style="color: #444; font-weight: 700;">Final Confirmation</div>',
        html: `
          <div class="modal-notice-content text-start">
            <div class="mb-3 d-flex align-items-start">
              <i class="fas fa-info-circle text-primary mt-1 me-2"></i>
              <span>Please ensure that all uploaded documents are clear, readable, and authentic.</span>
            </div>
            <div class="mb-3 d-flex align-items-start">
              <i class="fas fa-exclamation-triangle text-warning mt-1 me-2"></i>
              <span>Submitting incorrect or falsified information/attachments will result in your application being reverted and may lead to rejection or blacklisting.</span>
            </div>
            <div class="mt-4 p-3 bg-light rounded border">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="agreeFinalNotice">
                <label class="form-check-label fw-bold" for="agreeFinalNotice" style="cursor: pointer;">
                  I certify that all information and attachments provided are true and correct.
                </label>
              </div>
            </div>
          </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'SUBMIT APPLICATION',
        cancelButtonText: 'CANCEL',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        width: '500px',
        preConfirm: () => {
          if (!document.getElementById('agreeFinalNotice').checked) {
            Swal.showValidationMessage('You must check the box to certify your information');
            return false;
          }
          return true;
        }
      }).then((result) => {
        if (result.isConfirmed) {
          submitApplication();
        }
      });
    }

    async function submitApplication() {
      const submitBtn = document.getElementById('submitBtn');
      const btnText = document.getElementById('btnText');
      const btnLoader = document.getElementById('btnLoader');
      
      // Disable button and show loader
      submitBtn.disabled = true;
      btnText.textContent = 'Submitting...';
      btnLoader.classList.remove('d-none');
      
      try {
        const form = document.getElementById('admissionForm');
        const formData = new FormData(form);
        const aapFormData = new FormData(document.getElementById('aapForm'));
        
        // 1. Generate Application ID
        const now = new Date();
        const timestamp = now.getTime().toString().slice(-6);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const applicationId = `APP-${now.getFullYear()}-${timestamp}${random}`;
        
        // 2. Upload Files
         const attachments = {};
         const fileInputs = [
           'file_valid_id', 'file_shs_cert', 'file_good_moral', 'file_diploma', 'file_tor', 'file_transfer_cred'
         ];
         
         for (const inputId of fileInputs) {
             const input = document.getElementById(inputId);
             if (input && input.files.length > 0) {
                 const filePaths = [];
                 for (let i = 0; i < input.files.length; i++) {
                     const uploadData = new FormData();
                     uploadData.append('file', input.files[i]);
                     const typeName = inputId.replace('file_', ''); 
                     uploadData.append('type', typeName);
                     
                     const response = await fetch('../../api/admissions/upload-attachment.php', {
                         method: 'POST',
                         body: uploadData
                     });
                     
                     const result = await response.json();
                     if (result.success) {
                         filePaths.push(result.path);
                     } else {
                         throw new Error(`Failed to upload ${inputId}: ${result.message}`);
                     }
                 }
                 
                 if (input.multiple) {
                     attachments[inputId.replace('file_', '')] = filePaths;
                 } else {
                     attachments[inputId.replace('file_', '')] = filePaths[0];
                 }
             }
         }
        
        // 3. Construct Data Payload
        const parents = [];
        const parentItems = document.querySelectorAll('.parent-item');
        
        parentItems.forEach(item => {
            const firstName = item.querySelector('input[name="parent_first_name[]"]').value;
            const lastName = item.querySelector('input[name="parent_last_name[]"]').value;
            
            if (firstName && lastName) {
                parents.push({
                    first_name: firstName,
                    middle_name: item.querySelector('input[name="parent_middle_name[]"]').value || '',
                    last_name: lastName,
                    extension: item.querySelector('input[name="parent_extension[]"]').value || '',
                    age: item.querySelector('input[name="parent_age[]"]').value || '',
                    relationship: item.querySelector('select[name="parent_relationship[]"]').value,
                    education: item.querySelector('input[name="parent_education[]"]').value || '',
                    occupation: item.querySelector('input[name="parent_occupation[]"]').value || '',
                    income: item.querySelector('input[name="parent_income[]"]').value || '',
                    contact: item.querySelector('input[name="parent_contact[]"]').value || '',
                    street: item.querySelector('input[name="parent_street[]"]').value || '',
                    city: item.querySelector('input[name="parent_city[]"]').value || '',
                    is_emergency: item.querySelector('input[name="is_emergency[]"]').checked
                });
            }
        });
        
        const schools = [];
        const schoolItems = document.querySelectorAll('.school-item');
        
        schoolItems.forEach(item => {
            const name = item.querySelector('input[name="school_name[]"]').value;
            
            if (name) {
                schools.push({
                    name: name,
                    year: item.querySelector('input[name="school_year[]"]').value || '',
                    level: item.querySelector('input[name="school_level[]"]').value || '',
                    type: item.querySelector('select[name="school_type[]"]').value || '',
                    city: item.querySelector('input[name="school_city[]"]').value || ''
                });
            }
        });
        
        // Construct full address
        const address = `${formData.get('street_no')}, ${formData.get('barangay')}, ${formData.get('city_province')}, ${formData.get('zip_code')}`;
        
        // Get program titles for reference
        const program2Select = document.querySelector('select[name="program_id_2"]');
        let program2Title = '';
        if (program2Select && program2Select.selectedIndex > 0) {
             program2Title = program2Select.options[program2Select.selectedIndex].text;
        }
        
        // Get last school info (Senior HS is usually the last one filled)
        let lastSchoolName = '';
        let yearGraduated = '';
        if (schools.length > 0) {
             const lastSchool = schools[schools.length - 1];
             lastSchoolName = lastSchool.name;
             yearGraduated = lastSchool.year;
        }

        const payload = {
            application_id: applicationId,
            student_id: null,
            program_id: formData.get('program_id_1'),
            first_name: formData.get('first_name'),
            middle_name: formData.get('middle_name'),
            last_name: formData.get('last_name'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            birthdate: formData.get('birthdate'),
            gender: formData.get('gender'),
            address: address,
            high_school: lastSchoolName,
            last_school: lastSchoolName,
            year_graduated: yearGraduated,
            gwa: formData.get('gpa_rating'), 
            entrance_exam_score: null,
            admission_type: formData.get('admission_type'),
            previous_program: formData.get('program_id_2'),
            status: 'Pending',
            notes: '',
            attachments: JSON.stringify(attachments),
            form_data: JSON.stringify({
                gender: formData.get('gender'),
                suffix: formData.get('extension_name'),
                civil_status: formData.get('civil_status'),
                citizenship: formData.get('citizenship'),
                birth_place: formData.get('birth_place'),
                parents: parents,
                schools: schools,
                alternative_program: formData.get('program_id_2'),
                alternative_program_title: program2Title,
                shs_strand: formData.get('shs_strand'),
                latest_attainment: formData.get('latest_attainment'),
                // health_problem removed
                // first_male_college removed
                grade10_gpa: document.querySelector('input[name="grade10_gpa"]')?.value || '',
                grade11_gpa: document.querySelector('input[name="grade11_gpa"]')?.value || '',
                grade12_gpa: document.querySelector('input[name="grade12_gpa"]')?.value || '',
                zip_code: document.querySelector('input[name="zip_code"]')?.value || '',
                
                // Address details for breakdown
                street_no: document.querySelector('input[name="street_no"]')?.value || '',
                barangay: document.querySelector('input[name="barangay"]')?.value || '',
                city_province: document.querySelector('input[name="city_province"]')?.value || '',
                
                // AAP Data
                academic_status: aapFormData.get('academic_status'),
                already_enrolled: aapFormData.get('already_enrolled'),
                first_time_apply: aapFormData.get('first_time_apply'),
                shs_transfer: aapFormData.get('shs_transfer'),
                shs_transfer_from: aapFormData.get('shs_transfer_from'),
                shs_transfer_year: aapFormData.get('shs_transfer_year')
            })
        };

        // 4. Send to Create API
        const createResponse = await fetch('../../api/admissions/create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        
        const createResult = await createResponse.json();
        
        if (!createResult.success) {
            throw new Error(createResult.message || 'Submission failed');
        }
        
        // 5. Success UI
        Swal.fire({
          title: 'Success!',
          text: 'Your application has been submitted successfully. Your Application ID is ' + applicationId,
          icon: 'success',
          confirmButtonText: 'View Status'
        }).then(() => {
          // Hide form section
          document.getElementById('form-section').classList.remove('active');
          
          // Show status section
          const statusSection = document.getElementById('status-section');
          statusSection.classList.add('active');
          statusSection.classList.add('animate__animated', 'animate__fadeIn');
          
          // Update steps to show everything as completed
          const steps = ['step-marker-welcome', 'step-marker-guidelines', 'step-marker-aap', 'step-marker-personal', 'step-marker-education', 'step-marker-attachments', 'step-marker-review', 'step-marker-submit'];
          steps.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
              el.classList.add('completed');
              el.classList.remove('active');
            }
          });
          
          // Update the application ID in the status section
          const appIdDisplay = document.getElementById('static-app-id');
          if (appIdDisplay) {
              appIdDisplay.textContent = applicationId;
          }
          
          // Populate the review content in the status section
          populateReviewContent('status-review-content');
          
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
      } catch (error) {
        console.error('Submission Error:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'An error occurred during submission. Please try again.',
            icon: 'error'
        });
        
        // Reset button
        submitBtn.disabled = false;
        btnText.textContent = 'SUBMIT APPLICATION';
        btnLoader.classList.add('d-none');
      }
    }

    // Remove the old submit event listener as we now use showNoticeAndSubmit
    // and submitApplication function directly.
    

    // Function to handle file previews
    document.addEventListener('change', function(e) {
      if (e.target.type === 'file' && e.target.id.startsWith('file_')) {
        const type = e.target.id.replace('file_', '');
        const container = document.getElementById(`preview_${type}`);
        container.innerHTML = '';
        
        const files = e.target.files;
        for (let i = 0; i < files.length; i++) {
          const file = files[i];
          const reader = new FileReader();
          
          reader.onload = function(event) {
            const isPdf = file.type === 'application/pdf';
            const html = `
              <div class="preview-item">
                ${isPdf ? '<i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>' : `<img src="${event.target.result}">`}
                <div class="small text-truncate mb-2" style="max-width: 100px;">${file.name}</div>
                <button type="button" class="btn-delete-file" onclick="removeFile('${type}', ${i})">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
          }
          reader.readAsDataURL(file);
        }
      }
    });

    function removeFile(type, index) {
      const input = document.getElementById(`file_${type}`);
      const dt = new DataTransfer();
      const { files } = input;
      
      for (let i = 0; i < files.length; i++) {
        if (i !== index) dt.items.add(files[i]);
      }
      
      input.files = dt.files;
      // Trigger change to update preview
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Final step: prevent default form submission if somehow triggered
    document.getElementById('admissionForm').addEventListener('submit', (e) => e.preventDefault());

    // ==========================================
    // SAVE DRAFT & RESTORE SESSION LOGIC
    // ==========================================

    // Check for existing session data on load
    document.addEventListener('DOMContentLoaded', function() {
        // Check if application is already submitted/under review
        const statusSection = document.getElementById('status-section');
        if (statusSection && statusSection.classList.contains('active')) {
             updateSteps('step-marker-submit');
             populateReviewContent('status-review-content');
             return; // Stop here, do not restore form data/steps
        }

        if (typeof existingAdmission !== 'undefined' && existingAdmission) {
            console.log('Restoring session...', existingAdmission);
            
            // Show toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            
            Toast.fire({
                icon: 'info',
                title: 'Resuming your application'
            });

            loadSavedData(existingAdmission);
        }
    });

    function loadSavedData(data) {
        if (!data) return;

        // 1. Restore Main Details
        if (data.details) {
            const d = data.details;
            // Map common fields if they exist as inputs
            const fields = ['first_name', 'middle_name', 'last_name', 'phone', 'birthdate', 'gender', 'address', 
                            'high_school', 'last_school', 'year_graduated', 'gwa', 'entrance_exam_score', 
                            'admission_type', 'previous_program'];
            
            fields.forEach(field => {
                setInputValue(field, d[field]);
            });
            
            // Map program_id to program_id_1 (first choice)
            if (d.program_id) setInputValue('program_id_1', d.program_id);
        }

        // 2. Restore JSON Form Data
        if (data.form_data) {
            const fd = data.form_data;
            
            // Pre-process: Ensure enough rows for array inputs
            // Check Parents
            if (Array.isArray(fd.parent_first_name)) {
                const needed = fd.parent_first_name.length;
                const current = document.querySelectorAll('.parent-item').length;
                for (let i = current; i < needed; i++) {
                    addParent();
                }
            }
            
            // Check Schools
            if (Array.isArray(fd.school_name)) {
                const needed = fd.school_name.length;
                const current = document.querySelectorAll('.school-item').length;
                for (let i = current; i < needed; i++) {
                    addSchool();
                }
            }

            for (const key in fd) {
                if (fd.hasOwnProperty(key)) {
                    setInputValue(key, fd[key]);
                }
            }
            
            // 3. Restore Step Navigation
            if (fd.current_step) {
                setTimeout(() => restoreStep(fd.current_step), 100);
            }
        }
        
        // Trigger specific logic like equity group - REMOVED
    }

    function restoreStep(step) {
        if (!step) return;
        
        console.log('Restoring to step:', step);
        
        // Hide all sections first
        document.getElementById('guidelines-section').classList.remove('active');
        document.getElementById('aap-section').classList.remove('active');
        document.getElementById('form-section').classList.remove('active');
        document.getElementById('status-section').classList.remove('active');
        
        if (step === 'status') {
            document.getElementById('status-section').classList.add('active');
            updateSteps('step-marker-submit');
        } else if (step === 'aap') {
            document.getElementById('aap-section').classList.add('active');
            updateSteps('step-marker-aap');
        } else if (step.startsWith('step-')) {
            document.getElementById('form-section').classList.add('active');
            
            // Hide all form steps
            document.querySelectorAll('.form-step-content').forEach(el => el.classList.add('d-none'));
            
            // Show target step
            const stepId = 'form-' + step; // e.g. form-step-1
            const stepEl = document.getElementById(stepId);
            if (stepEl) {
                stepEl.classList.remove('d-none');
                stepEl.classList.add('animate__animated', 'animate__fadeInRight');
            } else {
                // Fallback to step 1
                document.getElementById('form-step-1').classList.remove('d-none');
            }
            
            // Update markers
            if (step === 'step-1') updateSteps('step-marker-personal');
            if (step === 'step-2') updateSteps('step-marker-education');
            if (step === 'step-3') updateSteps('step-marker-attachments');
            if (step === 'step-4') updateSteps('step-marker-review');
            if (step === 'step-5') updateSteps('step-marker-submit');
        } else {
            // Default to guidelines
            document.getElementById('guidelines-section').classList.add('active');
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function setInputValue(name, value) {
        if (value === null || value === undefined) return;
        
        // Try exact name first
        let inputs = document.querySelectorAll(`[name="${name}"]`);
        
        // If no inputs found, try array syntax
        if (inputs.length === 0) {
            inputs = document.querySelectorAll(`[name="${name}[]"]`);
        }
        
        if (inputs.length === 0) return;

        // Handle array values for array inputs
        if (Array.isArray(value)) {
            inputs.forEach((input, index) => {
                if (value[index] !== undefined) {
                    setSingleInput(input, value[index]);
                }
            });
        } else {
            // Single value applied to all matching inputs (usually just one, unless radio)
            inputs.forEach(input => {
                setSingleInput(input, value);
            });
        }
    }

    function setSingleInput(input, value) {
        if (input.tagName === 'SELECT') {
            // Robust select handling: try exact match, then case-insensitive, then trim
            // First try direct assignment
            input.value = value;
            
            // Verify if it stuck (if value is not empty but input value is empty/default)
            // Note: input.value will be empty string if assignment failed to match an option
            if (value && input.value != value) {
                // Try finding matching option manually
                const valStr = String(value).toLowerCase().trim();
                let matched = false;
                for (let i = 0; i < input.options.length; i++) {
                    const optVal = String(input.options[i].value).toLowerCase().trim();
                    if (optVal === valStr) {
                        input.selectedIndex = i;
                        matched = true;
                        break;
                    }
                }
            }
        } else if (input.type === 'radio') {
            if (input.value == value) {
                input.checked = true;
                // Trigger inline handlers like onclick for conditional display
                if (input.onclick) input.onclick(); 
            }
        } else if (input.type === 'checkbox') {
             // For simple checkboxes
             input.checked = (value == 1 || value == 'true' || value === true || value == 'on');
        } else if (input.type === 'file') {
            // Cannot set file input value
        } else {
            input.value = value;
        }
    }

    function getCurrentStep() {
        if (document.getElementById('status-section').classList.contains('active')) return 'status';
        
        const formSection = document.getElementById('form-section');
        if (formSection.classList.contains('active')) {
            if (!document.getElementById('form-step-5').classList.contains('d-none')) return 'step-5'; // Submit
            if (!document.getElementById('form-step-4').classList.contains('d-none')) return 'step-4'; // Review
            if (!document.getElementById('form-step-3').classList.contains('d-none')) return 'step-3'; // Attachments
            if (!document.getElementById('form-step-2').classList.contains('d-none')) return 'step-2'; // Education
            return 'step-1'; // Personal
        }
        
        if (document.getElementById('aap-section').classList.contains('active')) return 'aap';
        
        return 'guidelines';
    }

    async function saveDraft(clickedBtn = null) {
        // Since we removed the header save button, we rely on clickedBtn (the navigation buttons)
        const targetBtn = clickedBtn;
        
        if (targetBtn) {
            var originalContent = targetBtn.innerHTML;
            targetBtn.disabled = true;
            targetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
        }
        
        try {
            // Collect data from both forms
            const form1 = document.getElementById('admissionForm');
            const form2 = document.getElementById('aapForm'); // AAP confirmation
            
            const formData = new FormData(form1);
            if (form2) {
                const fd2 = new FormData(form2);
                for (let [key, val] of fd2.entries()) {
                    formData.append(key, val);
                }
            }
            
            // Convert to JSON object
            const object = {};
            const formDataJson = {};
            
            // Standard columns in database
            const dbColumns = ['application_id', 'student_id', 'program_id', 'first_name', 'middle_name', 'last_name', 
                               'email', 'phone', 'birthdate', 'gender', 'address', 'high_school', 'last_school', 
                               'year_graduated', 'gwa', 'entrance_exam_score', 'admission_type', 'previous_program', 
                               'status', 'notes'];

            // Map specific form fields to DB columns
            object.program_id = formData.get('program_id_1') || null; // 1st choice is main program
            object.email = '<?php echo $email; ?>';
            
            formData.forEach((value, key) => {
                // Check if key is a DB column
                if (dbColumns.includes(key)) {
                    object[key] = value;
                } else {
                    // Everything else goes into form_data JSON
                    // Handle array inputs (name[])
                    if (key.endsWith('[]')) {
                        const cleanKey = key.slice(0, -2); // remove []
                        // We need to get ALL values for this array key
                        // FormData.getAll() is better here but we are iterating.
                        // Better approach: iterate known array keys or reconstruct
                    } else {
                        formDataJson[key] = value;
                    }
                }
            });
            
            object.status = 'draft'; // Explicitly set status to draft when saving progress
            
            // Capture current step
            formDataJson.current_step = getCurrentStep();
            
            // Handle Arrays specifically for form_data
            const arrayKeys = [
                'parent_first_name', 'parent_middle_name', 'parent_last_name', 'parent_extension', 
                'parent_age', 'parent_relationship', 'parent_education', 'parent_occupation', 
                'parent_income', 'parent_contact', 'parent_street', 'parent_city', 'is_emergency',
                'school_name', 'school_year', 'school_level', 'school_type', 'school_city'
            ];
            
            arrayKeys.forEach(key => {
                const values = formData.getAll(key + '[]');
                if (values.length > 0) {
                    formDataJson[key] = values; // Store as array in JSON
                }
            });
            
            // Also add any other fields to form_data that we missed in the loop
            // Re-iterate formData to capture everything into form_data as backup/flexible storage
            for (let [key, value] of formData.entries()) {
                if (!dbColumns.includes(key) && !key.endsWith('[]')) {
                    formDataJson[key] = value;
                }
            }

            object.form_data = formDataJson;
            
            // Send to API
            const response = await fetch('../../api/admissions/save-draft.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(object)
            });
            
            const result = await response.json();
            
            if (result.success) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: 'Progress saved successfully'
                });
            } else {
                throw new Error(result.message);
            }
            
        } catch (error) {
                console.error('Save Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: error.message || 'Could not save your progress. Please try again.'
                });
            } finally {
                if (targetBtn) {
                    targetBtn.disabled = false;
                    targetBtn.innerHTML = originalContent;
                }
            }
        }
  </script>
</body>
</html>