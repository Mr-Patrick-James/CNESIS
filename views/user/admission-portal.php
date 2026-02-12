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
            $_SESSION['student_type'] = 'freshman';
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

$current_status = $admission ? $admission['status'] : 'new';

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
  <title>Admission Portal – Colegio De Naujan</title>
  
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
      <h1 class="display-5 fw-bold mb-2">Admission Portal</h1>
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

            <!-- Step 5: Education -->
            <div id="step-marker-education" class="step-item">
              <div class="step-icon"><i class="fas fa-graduation-cap"></i></div>
              <div class="step-title">Education</div>
            </div>

            <!-- Step 6: Attachments -->
            <div id="step-marker-attachments" class="step-item">
              <div class="step-icon"><i class="fas fa-paperclip"></i></div>
              <div class="step-title">Attachments</div>
            </div>

            <!-- Step 7: Program -->
            <div id="step-marker-program" class="step-item">
              <div class="step-icon"><i class="fas fa-tasks"></i></div>
              <div class="step-title">Program</div>
            </div>

            <!-- Step 8: Form Preview -->
            <div id="step-marker-preview" class="step-item">
              <div class="step-icon"><i class="fas fa-eye"></i></div>
              <div class="step-title">Form Preview</div>
            </div>

            <!-- Step 9: Submit -->
            <div id="step-marker-submit" class="step-item">
              <div class="step-icon"><i class="fas fa-check-double"></i></div>
              <div class="step-title">Submit</div>
            </div>
          </div>

          <!-- Active Step Details -->
          <div class="step-details-container mt-4">
            <div class="active-step-content">
              
              <!-- GUIDELINES SECTION -->
              <?php if (!$admission): ?>
                <div id="guidelines-section" class="section-portal active">
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
                        <button type="button" class="btn btn-nav-save"><i class="fas fa-save"></i> SAVE</button>
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
                      <input type="hidden" name="admission_type" value="freshman">
                      <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                      <input type="hidden" name="status" value="new">
                      
                      <!-- Form Step 1: Basic Information -->
                      <div id="form-step-1" class="form-step-content active">
                        
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
                              <input type="text" name="city_province" class="form-control" placeholder="BAYBAY, LEYTE" required>
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
                                  <input type="text" name="parent_city[]" class="form-control" placeholder="BAYBAY, LEYTE" required>
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
                                  <input type="text" name="school_city[]" class="form-control" placeholder="BAYBAY, LEYTE">
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
                                  <input type="text" name="school_city[]" class="form-control" placeholder="BAYBAY, LEYTE">
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
                                  <input type="text" name="school_city[]" class="form-control" placeholder="BAYBAY, LEYTE">
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
                          <button type="button" class="btn btn-nav-save"><i class="fas fa-save"></i> SAVE</button>
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
                            <div class="form-group">
                              <label>Any allergies or health problem?</label>
                              <input type="text" name="health_problem" class="form-control" placeholder="N/A">
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label>Are you the first male in your family to attend college?</label>
                              <div class="note-text text-start mb-2">This refers to all the male students who are the first in their immediate family (parents and siblings)</div>
                              <select name="first_male_college" class="form-control">
                                <option value="">Select</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                                <option value="n/a">Not Applicable</option>
                              </select>
                            </div>
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
                              <input type="text" name="gpa_rating" class="form-control" placeholder="91.000">
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
                              <label>Grade 10 GPA</label>
                              <input type="number" step="0.001" name="grade10_gpa" class="form-control" placeholder="85">
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Grade 11 GPA</label>
                              <input type="number" step="0.001" name="grade11_gpa" class="form-control" placeholder="92">
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Grade 12 GPA</label>
                              <input type="number" step="0.001" name="grade12_gpa" class="form-control" placeholder="96">
                            </div>
                          </div>
                        </div>

                        <div class="equity-section mt-4 mb-4">
                          <div class="equity-header mb-3">
                            <span class="fw-bold">Equity Target Group</span>
                          </div>
                          <div class="alert alert-light border py-2 mb-3" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-2"></i> <strong>Select one option only.</strong> The option you choose will require uploading a certification or ID in the next step. You may select "Not Applicable" if none of the choices apply.
                          </div>

                          <div class="equity-options">
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Not Applicable" id="equity_na" checked>
                              <label for="equity_na">Not Applicable</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="4Ps Member" id="equity_4ps">
                              <label for="equity_4ps">4Ps Member</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Single Parent" id="equity_sp">
                              <label for="equity_sp">Single Parent</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Child of Single Parent" id="equity_csp">
                              <label for="equity_csp">Child of Single Parent</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Member of Indigenous People (IP)" id="equity_ip">
                              <label for="equity_ip">Member of Indigenous People (IP)</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Person with Disability (PWD)" id="equity_pwd">
                              <label for="equity_pwd">Person with Disability (PWD)</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Orphan (Double-Orphan)" id="equity_orphan">
                              <label for="equity_orphan">Orphan (Double-Orphan)</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Dependent of Person Officially Enrolled under E-CLIP" id="equity_eclip">
                              <label for="equity_eclip">Dependent of Person Officially Enrolled under E-CLIP</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Underprivileged / Indigent families" id="equity_indigent">
                              <label for="equity_indigent">Underprivileged / Indigent families</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Out-of-School Youth/Adult (OSY/OSA)" id="equity_osy">
                              <label for="equity_osy">Out-of-School Youth/Adult (OSY/OSA)</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Senior Citizen (60 years old and above)" id="equity_senior">
                              <label for="equity_senior">Senior Citizen (60 years old and above)</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Disaster Displaced Families (IDPs)" id="equity_idp">
                              <label for="equity_idp">Disaster Displaced Families (IDPs)</label>
                            </div>
                            <div class="equity-option-item">
                              <input type="radio" name="equity_group" value="Children/Dependent of AFP/PNP Killed or Wounded in Action (KIA/KIPO; WIA/WIPO)" id="equity_afp">
                              <label for="equity_afp">Children/Dependent of AFP/PNP Killed or Wounded in Action (KIA/KIPO; WIA/WIPO)</label>
                            </div>
                          </div>
                        </div>

                        <div class="alert alert-warning py-2 mb-4" style="font-size: 0.85rem;">
                          <i class="fas fa-exclamation-triangle me-2"></i>
                          By submitting this form, you certify that all information provided is true and correct.
                        </div>

                        <div class="nav-buttons">
                          <button type="button" onclick="prevStep(1)" class="btn btn-nav-back"><i class="fas fa-chevron-left me-1"></i> BACK</button>
                          <button type="button" class="btn btn-nav-save"><i class="fas fa-save"></i> SAVE</button>
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
                          <div class="attachment-title">Any valid ID (School Id, Drivers license, Postal Id, Voters Id, National Id, Passport)</div>
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
                          <div class="attachment-title">Graduating Senior High School</div>
                          <div class="attachment-subtitle">
                            <i class="fas fa-info-circle"></i> Certification of your grades with a computed Grade Point Average (GPA) from Grades 10, 11 and 12
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

                        <!-- 3. Equity Group Attachment (Conditional) -->
                        <div id="equity_attachment_container" class="attachment-card d-none">
                          <div class="attachment-title" id="equity_attachment_title">Required attachments for [Equity Group]</div>
                          <div class="attachment-subtitle" id="equity_attachment_subtitle">
                            <i class="fas fa-info-circle"></i> Please upload the required certification or ID.
                          </div>
                          <input type="file" id="file_equity" name="attachments[equity][]" multiple class="d-none" accept=".png,.jpg,.jpeg,.pdf">
                          <button type="button" class="btn btn-select-file" onclick="document.getElementById('file_equity').click()">
                            <i class="fas fa-upload me-2"></i> Select file(s)
                          </button>
                          <div class="file-upload-note">
                            <strong>Note:</strong> Only <strong>png, jpeg/jpg, pdf</strong> are allowed file types. Limit of <strong>3</strong> file(s) to upload
                          </div>
                          <div id="preview_equity" class="preview-container"></div>
                        </div>

                        <div class="nav-buttons">
                          <button type="button" onclick="prevStep(2)" class="btn btn-nav-back"><i class="fas fa-chevron-left me-1"></i> BACK</button>
                          <button type="button" class="btn btn-nav-save"><i class="fas fa-save"></i> SAVE</button>
                          <button type="button" onclick="showNoticeAndSubmit()" id="submitBtn" class="btn btn-nav-next">
                            <span id="btnText">SUBMIT <i class="fas fa-check-circle ms-1"></i></span>
                            <span id="btnLoader" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                          </button>
                        </div>
                        <div class="note-text mt-3">Note: You can save your application form information and continue any time</div>
                      </div>
                      </div>
                    </form>
                  </div>
                </div>
              <?php else: ?>
                <!-- STATUS SECTION (Shown after submission) -->
                <div id="status-section" class="section-portal active">
                  <?php if ($current_status == 'new' || $current_status == 'pending'): ?>
                    <div class="text-center mb-4">
                      <div class="mb-3">
                        <i class="fas fa-clock fa-3x text-warning"></i>
                      </div>
                      <h4>Application Under Review</h4>
                      <p class="text-muted">Your application (ID: <strong><?php echo htmlspecialchars($admission['application_id']); ?></strong>) has been successfully submitted and is currently being reviewed by our admissions team.</p>
                    </div>

                    <div class="guideline-card mx-auto" style="max-width: 700px; border-left-color: var(--primary-blue);">
                      <h5 class="mb-3">What's Next?</h5>
                      <ul class="list-unstyled">
                        <li class="mb-3 d-flex align-items-start">
                          <i class="fas fa-check-circle text-success mt-1 me-3"></i>
                          <div>
                            <strong>Document Verification</strong>
                            <p class="mb-0 small text-muted">We are verifying your academic details. This usually takes 1-3 working days.</p>
                          </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                          <i class="fas fa-envelope text-primary mt-1 me-3"></i>
                          <div>
                            <strong>Check Your Email</strong>
                            <p class="mb-0 small text-muted">We will send updates about your status to <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
                          </div>
                        </li>
                        <li class="d-flex align-items-start">
                          <i class="fas fa-calendar-alt text-accent mt-1 me-3" style="color: var(--accent-gold);"></i>
                          <div>
                            <strong>Entrance Exam Schedule</strong>
                            <p class="mb-0 small text-muted">Once verified, you will be notified of your entrance exam schedule.</p>
                          </div>
                        </li>
                      </ul>
                    </div>
                  <?php elseif ($current_status == 'approved'): ?>
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
                  <?php elseif ($current_status == 'rejected'): ?>
                    <div class="text-center">
                      <div class="mb-3">
                        <i class="fas fa-times-circle fa-4x text-danger"></i>
                      </div>
                      <h4>Application Not Approved</h4>
                      <p class="text-muted">We regret to inform you that your application could not be processed at this time.</p>
                      <div class="alert alert-secondary mx-auto" style="max-width: 600px;">
                        <strong>Reason:</strong> <?php echo htmlspecialchars($admission['notes'] ?: 'No notes provided.'); ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <script>
    function updateSteps(activeStepId) {
      // List of all step markers
      const steps = [
        'step-marker-welcome', 'step-marker-guidelines', 'step-marker-aap', 
        'step-marker-personal', 'step-marker-education', 'step-marker-attachments', 
        'step-marker-program', 'step-marker-preview', 'step-marker-submit'
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
      // Validation disabled for testing
      document.getElementById('aap-section').classList.remove('active');
      setTimeout(() => {
        document.getElementById('form-section').classList.add('active');
        updateSteps('step-marker-personal');
        window.scrollTo({ top: 0, behavior: 'smooth' });
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
                <input type="text" name="parent_city[]" class="form-control" required>
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
                <input type="text" name="school_city[]" class="form-control" required>
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

    function nextStep(step) {
      const currentStep = step - 1;
      const currentStepEl = document.getElementById(`form-step-${currentStep}`);
      
      // Validation disabled for testing
      currentStepEl.classList.add('d-none');
      const nextStepEl = document.getElementById(`form-step-${step}`);
      nextStepEl.classList.remove('d-none', 'animate__fadeInLeft');
      nextStepEl.classList.add('animate__animated', 'animate__fadeInRight');
      
      // Update horizontal steps
      if (step === 2) updateSteps('step-marker-education');
      if (step === 3) updateSteps('step-marker-attachments');
      
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function prevStep(step) {
      const currentStep = step + 1;
      const currentStepEl = document.getElementById(`form-step-${currentStep}`);
      
      currentStepEl.classList.add('d-none');
      const prevStepEl = document.getElementById(`form-step-${step}`);
      prevStepEl.classList.remove('d-none', 'animate__fadeInRight');
      prevStepEl.classList.add('animate__animated', 'animate__fadeInLeft');
      
      // Update horizontal steps
      if (step === 1) updateSteps('step-marker-personal');
      if (step === 2) updateSteps('step-marker-education');
      
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateSteps(activeId) {
      const steps = ['step-marker-welcome', 'step-marker-guidelines', 'step-marker-aap', 'step-marker-personal', 'step-marker-education', 'step-marker-attachments'];
      
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
      // Validation: Check if Equity Group attachment is required and provided
      const equityGroup = document.querySelector('input[name="equity_group"]:checked').value;
      if (equityGroup !== 'Not Applicable') {
        const fileInput = document.getElementById('file_equity');
        if (fileInput.files.length === 0) {
          Swal.fire({
            title: 'Missing Attachment',
            text: `Please upload the required documentation for ${equityGroup} before submitting.`,
            icon: 'warning',
            confirmButtonColor: '#ffc107'
          });
          return;
        }
      }

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
      const form = document.getElementById('admissionForm');
      const submitBtn = document.getElementById('submitBtn');
      const btnText = document.getElementById('btnText');
      const btnLoader = document.getElementById('btnLoader');
      
      // Disable button and show loader
      submitBtn.disabled = true;
      btnText.textContent = 'Submitting...';
      btnLoader.classList.remove('d-none');
      
      const formData = new FormData(form);
      
      try {
        // Since we have files, we MUST use FormData and not JSON.stringify
        const response = await fetch('../../api/admissions/create.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
          Swal.fire({
            title: 'Success!',
            text: 'Your application has been submitted successfully.',
            icon: 'success',
            confirmButtonText: 'Great'
          }).then(() => {
            location.reload();
          });
        } else {
          throw new Error(result.message || 'Failed to submit application');
        }
      } catch (error) {
        Swal.fire({
          title: 'Error!',
          text: error.message,
          icon: 'error',
          confirmButtonText: 'Try Again'
        });
        submitBtn.disabled = false;
        btnText.innerHTML = 'SUBMIT <i class="fas fa-check-circle ms-1"></i>';
        btnLoader.classList.add('d-none');
      }
    }

    // Remove the old submit event listener as we now use showNoticeAndSubmit
    // and submitApplication function directly.
    

    // Handle Equity Group selection to show/hide conditional attachment
    document.querySelectorAll('input[name="equity_group"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const container = document.getElementById('equity_attachment_container');
        const title = document.getElementById('equity_attachment_title');
        const subtitle = document.getElementById('equity_attachment_subtitle');
        
        if (this.value !== 'Not Applicable') {
          container.classList.remove('d-none');
          container.classList.add('animate__animated', 'animate__fadeIn');
          title.innerHTML = `<span class="text-danger">*</span> Required attachments for ${this.value}`;
          
          // Custom subtitles based on selection
          let subText = "Please upload the required certification or ID.";
          if (this.value === '4Ps Member') subText = "Parents' 4P's ID or 4Ps Member Certification";
          else if (this.value === 'Single Parent') subText = "Please upload your Single Parent ID or Certification from MSWD/DSWD.";
          else if (this.value === 'Person with Disability (PWD)') subText = "Please upload your PWD ID.";
          else if (this.value === 'Member of Indigenous People (IP)') subText = "Please upload your NCIP Certification.";
          
          subtitle.innerHTML = `<i class="fas fa-info-circle"></i> ${subText}`;
        } else {
          container.classList.add('d-none');
        }
      });
    });

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

    function prevStep(step) {
      const currentStep = step + 1;
      document.getElementById(`form-step-${currentStep}`).classList.add('d-none');
      const prevStepEl = document.getElementById(`form-step-${step}`);
      prevStepEl.classList.remove('d-none', 'animate__fadeInRight');
      prevStepEl.classList.add('animate__animated', 'animate__fadeInLeft');
      
      // Update horizontal steps
      if (step === 1) updateSteps('step-marker-personal');
      if (step === 2) updateSteps('step-marker-education');
      
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Final step: prevent default form submission if somehow triggered
    document.getElementById('admissionForm').addEventListener('submit', (e) => e.preventDefault());
  </script>
</body>
</html>