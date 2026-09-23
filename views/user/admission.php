<?php
/**
 * Admissions Page – Colegio De Naujan
 * Fetches dynamic system settings from database
 */
require_once __DIR__ . '/../../api/config/database.php';

$database = new Database();
$db = $database->getConnection();

$settings = [];
if ($db) {
    $stmt = $db->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_group = 'general'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Include session helper for landing pages
include_once __DIR__ . '/../../api/auth/session_helper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admissions – Colegio De Naujan</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  
  <style>
    :root {
      --primary-blue: #1a365d;
      --secondary-blue: #2d55a0;
      --accent-gold: #d4af37;
      --light-gray: #f8f9fa;
      --dark-gray: #333333;
    }

    /* ── Base ───────────────────────────────────────────────── */
    *, *::before, *::after {
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      color: var(--dark-gray);
      background-color: #f5f5f5;
    }

    h1, h2, h3, h4, h5, h6 {
      font-family: Arial, sans-serif;
      font-weight: normal;
    }

    /* ── Navigation ─────────────────────────────────────────── */
    .navbar {
      background-color: var(--primary-blue) !important;
      padding: 15px 0;
      box-shadow: 0 2px 5px rgba(0,0,0,.1);
    }

    .navbar-brand {
      display: flex;
      align-items: center;
    }

    .logo-img {
      height: 40px;
      width: auto;
      margin-right: 10px;
    }

    .brand-text { line-height: 1.2; }

    .brand-name {
      font-weight: bold;
      font-size: 1.4rem;
      color: white;
    }

    .brand-subtitle {
      font-size: 0.7rem;
      color: rgba(255,255,255,.8);
      display: block;
    }

    .nav-link {
      color: white !important;
      font-weight: normal;
      margin: 0 10px;
      padding: 8px 12px !important;
      border-radius: 0;
      font-size: 0.9rem;
    }

    .nav-link:hover {
      background: rgba(255,255,255,.1);
    }

    .login-btn {
      background-color: var(--accent-gold) !important;
      color: var(--primary-blue) !important;
      border-radius: 4px;
      padding: 8px 20px !important;
      font-weight: bold;
      border: none;
    }

    .login-btn:hover {
      background-color: #e6c158 !important;
    }

    .navbar-toggler {
      border: 1px solid rgba(255,255,255,.3);
      padding: 5px 10px;
    }

    /* ── Hero banner ────────────────────────────────────────── */
    .hero-banner {
      height: 300px;
      background: linear-gradient(135deg, var(--primary-blue) 0%, #2d55a0 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      margin-top: 70px;
      position: relative;
      overflow: hidden;
    }

    .hero-banner::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url('../../assets/img/campus.jpg') center/cover no-repeat;
      opacity: 0.18;
    }

    .hero-overlay { display: none; }

    .hero-text {
      position: relative;
      z-index: 2;
      text-align: center;
      color: white;
    }

    .hero-text h1 {
      font-size: 2.8rem;
      font-weight: bold;
      letter-spacing: 3px;
      text-shadow: 0 2px 8px rgba(0,0,0,.35);
      margin: 0;
    }

    .hero-text p {
      margin-top: 10px;
      font-size: 1.05rem;
      opacity: .85;
    }

    /* ── Main content wrapper ───────────────────────────────── */
    .admission-section {
      padding: 48px 0;
      background-color: #f5f5f5;
    }

    .section-title {
      color: var(--primary-blue);
      font-size: 1.25rem;
      font-weight: bold;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 3px solid var(--accent-gold);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* ── Cards ──────────────────────────────────────────────── */
    .card-soft {
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,.07);
      background-color: white;
      margin-bottom: 20px;
    }

    .card-soft h6 {
      color: var(--primary-blue);
      font-weight: bold;
    }

    .card-soft ul {
      list-style: disc;
      padding-left: 20px;
      margin-bottom: 0;
    }

    .card-soft ul li {
      margin-bottom: 6px;
      color: #555;
    }

    /* ── Buttons ────────────────────────────────────────────── */
    .btn-outline-primary {
      border: 1.5px solid var(--primary-blue) !important;
      color: var(--primary-blue) !important;
      background-color: white !important;
      border-radius: 5px !important;
      font-weight: 500 !important;
      transition: background .2s, color .2s !important;
    }

    .btn-outline-primary:hover {
      background-color: var(--primary-blue) !important;
      color: white !important;
    }

    .btn-success {
      background-color: #28a745 !important;
      border-color: #28a745 !important;
    }

    .btn-success:hover {
      background-color: #218838 !important;
      border-color: #1e7e34 !important;
    }

    /* ── Forms ──────────────────────────────────────────────── */
    .form-control {
      border: 1px solid #ced4da;
      border-radius: 5px;
    }

    .form-control:focus {
      border-color: var(--primary-blue);
      box-shadow: 0 0 0 3px rgba(26,54,93,.15);
    }

    .form-label {
      font-weight: 600;
      color: var(--primary-blue);
      margin-bottom: 4px;
    }

    .btn-link {
      color: var(--secondary-blue);
      text-decoration: none;
    }

    .btn-link:hover { text-decoration: underline; }

    /* ── Elementor-style section wrappers (reset junk) ──────── */
    .elementor-section,
    .elementor-container,
    .elementor-column,
    .elementor-widget-wrap,
    .elementor-element,
    .elementor-widget-container {
      display: block;
      width: 100%;
    }

    .elementor-heading-title {
      font-size: 1.5rem;
      font-weight: bold;
      color: var(--primary-blue);
      padding-bottom: 8px;
      border-bottom: 3px solid var(--accent-gold);
      margin-bottom: 0;
    }

    /* ── Accordion tweaks ───────────────────────────────────── */
    .accordion-button:not(.collapsed) {
      background-color: #eef2f8;
      color: var(--primary-blue);
      box-shadow: none;
    }

    .accordion-button:focus {
      box-shadow: 0 0 0 3px rgba(26,54,93,.15);
    }

    /* ── Chat bubbles ───────────────────────────────────────── */
    .student-msg-bubble {
      max-width: 85%;
      margin-bottom: 12px;
      padding: 8px 15px;
      border-radius: 18px;
      font-size: 0.9rem;
      line-height: 1.4;
    }

    .student-msg-student {
      background: #28a745;
      color: white;
      border-bottom-right-radius: 4px;
      margin-left: auto;
    }

    .student-msg-admin {
      background: #e9ecef;
      color: #333;
      border-bottom-left-radius: 4px;
      margin-right: auto;
    }

    .student-msg-time {
      font-size: 0.7rem;
      opacity: .7;
      margin-top: 4px;
      display: block;
      text-align: right;
    }

    /* ── Footer ─────────────────────────────────────────────── */
    .footer {
      background-color: #2b2b2b;
      color: #ddd;
      padding: 50px 0 24px;
      margin-top: 60px;
    }

    .footer h5 {
      color: var(--accent-gold);
      font-weight: bold;
      margin-bottom: 16px;
    }

    .footer p { color: #bbb; font-size: 0.9rem; }

    .footer-links a {
      color: #ccc;
      text-decoration: none;
      display: block;
      margin-bottom: 8px;
      font-size: 0.9rem;
      transition: color .2s;
    }

    .footer-links a:hover { color: var(--accent-gold); }

    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,.1);
      margin-top: 30px;
      padding-top: 16px;
      font-size: 0.85rem;
      color: #999;
    }

    .social-icons a {
      color: #ccc;
      margin-right: 12px;
      font-size: 1rem;
      transition: color .2s;
    }

    .social-icons a:hover { color: var(--accent-gold); }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 768px) {
      .hero-banner { height: 220px; margin-top: 60px; }
      .hero-text h1 { font-size: 2rem; letter-spacing: 1px; }
      .section-title { font-size: 1.1rem; }
      .elementor-heading-title { font-size: 1.25rem; }
    }
  </style>
</head>
<body>
  <!-- Navigation Container (loaded dynamically) -->
  <div id="nav-container"></div>
  
  <!-- Modal Container (loaded dynamically) -->
  <div id="modal-container"></div>

  <!-- HERO -->
  <section class="hero-banner">
    <div class="hero-overlay"></div>
    <div class="hero-text" data-aos="fade-up">
      <h1>ADMISSION</h1>
    </div>
  </section>

  <!-- CONTENT -->
  <section class="admission-section">
    <div class="admission-wrapper">
      <main class="container my-5">

        <!-- REQUIREMENTS -->
        <section class="mb-5" id="requirements" data-aos="fade-up">
          <h5 class="section-title">
            <i class="bi bi-clipboard-check"></i> 
            Testing and Admission Office
          </h5>

          <!-- Freshman Section -->
          <div id="freshman-requirements" class="student-type-section" style="display: none;">
            <div class="card card-soft p-3 mb-3">
              <h6 class="fw-bold"><i class="fas fa-graduation-cap me-2 text-primary"></i>Incoming Freshman</h6>
              <ul>
                <li>Form 138 (High School Report Card)</li>
                <li>Good Moral Certificate</li>
                <li>PSA Birth Certificate</li>
                <li>2x2 ID Picture</li>
                <li>Entrance Exam Result</li>
              </ul>
            </div>
          </div>

          <!-- New Student / Transferee Section -->
          <div id="new-requirements" class="student-type-section" style="display: none;">
            <div class="card card-soft p-3 mb-3">
              <h6 class="fw-bold"><i class="fas fa-user-plus me-2 text-primary"></i>New Student / Transferee</h6>
              <ul>
                <li>Honorable Dismissal</li>
                <li>Transcript of Records (for evaluation)</li>
                <li>Good Moral Certificate</li>
                <li>PSA Birth Certificate</li>
                <li>2x2 ID Picture</li>
              </ul>
            </div>
          </div>

          <!-- Existing Student Section -->
          <div id="existing-requirements" class="student-type-section" style="display: none;">
            <div class="card card-soft p-3 mb-3 text-center">
              <h6 class="fw-bold"><i class="fas fa-user-check me-2 text-primary"></i>Existing Student</h6>
              <p class="mb-3">Welcome back! If you are an existing student, please log in to your portal to process your enrollment for the next semester.</p>
              <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="fas fa-sign-in-alt me-2"></i>Go to Login
              </button>
            </div>
          </div>

          <div class="card card-soft p-3 text-center">
            <a href="#sec-requirements" class="btn btn-outline-primary m-1" data-section="requirements">
              <i class="bi bi-file-text me-1"></i> Admission Requirements and Procedures
            </a>
            <a href="#sec-enrolment" class="btn btn-outline-primary m-1" data-section="enrolment">
              <i class="bi bi-list-check me-1"></i> Enrolment Procedure for Incoming Freshmen
            </a>
            <a href="#sec-faq" class="btn btn-outline-primary m-1" data-section="faq">
              <i class="bi bi-question-circle me-1"></i> Frequently Asked Questions
            </a>
            <a href="#sec-downloads" class="btn btn-outline-primary m-1" data-section="downloads">
              <i class="bi bi-download me-1"></i> Downloadable Forms
            </a>
          </div>
        </section>

        <!-- INQUIRY -->
        <section class="mb-5" data-aos="fade-up" data-aos-delay="100">
          <h5 class="section-title">
            <i class="bi bi-chat-dots-fill"></i> 
            Admission Inquiry
          </h5>

          <div class="card card-soft p-4">
            <form id="inquiry-form">
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Your full name" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Program of Interest</label>
                <select class="form-control" id="program" name="program" required>
                  <option value="">Select a program...</option>
                  <option value="1">BS Information Systems</option>
                  <option value="2">Bachelor of Public Administration</option>
                  <option value="3">Welding and Fabrication Technology</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Your Question</label>
                <textarea class="form-control" id="question" name="question" rows="4" placeholder="How can we help?" required></textarea>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-send-fill me-1"></i> Submit Inquiry
                </button>
                <button type="button" class="btn btn-link text-success p-0" onclick="showCheckRepliesModal()">
                  <i class="bi bi-envelope-check-fill me-1"></i> Already sent an inquiry? Check for replies
                </button>
              </div>
            </form>
          </div>
        </section>

        <!-- Student Inquiry Replies Modal -->
        <div class="modal fade" id="studentRepliesModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-md">
            <div class="modal-content border-0 shadow-lg">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-chat-left-text-fill me-2"></i> Inquiry Conversation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-0">
                <!-- Check Email View -->
                <div id="checkEmailView" class="p-4">
                  <p class="text-muted small mb-4">Enter the email address you used to submit your inquiry to view the conversation and replies.</p>
                  <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                      <input type="email" class="form-control" id="checkEmailInput" placeholder="your-email@example.com">
                      <button class="btn btn-success" type="button" onclick="fetchStudentMessages()">Check</button>
                    </div>
                  </div>
                </div>
                <!-- Chat View -->
                <div id="studentChatView" class="d-none">
                  <div id="studentChatMessages" style="height: 350px; overflow-y: auto; padding: 20px; background: #f8f9fa;">
                    <!-- Messages populated via JS -->
                  </div>
                  <div class="p-3 border-top">
                    <div class="input-group">
                      <textarea id="studentReplyMessage" class="form-control" rows="1" placeholder="Type a follow-up message..."></textarea>
                      <button class="btn btn-success" type="button" onclick="sendStudentReply()"><i class="bi bi-send-fill"></i></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Requirements Section -->
        <section class="elementor-section elementor-top-section elementor-element" data-id="sec-requirements" id="sec-requirements">
          <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element" data-id="col-requirements">
              <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-heading elementor-widget elementor-widget-heading" data-id="h-requirements">
                  <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">Admission Requirements and Procedures</h2>
                    <div class="card card-soft p-4 mt-4">
                      <h5 class="mb-3">General Requirements</h5>
                      <ul>
                        <li>Completed Application Form</li>
                        <li>Original Form 138 (Report Card) or Transcript of Records</li>
                        <li>Certificate of Good Moral Character</li>
                        <li>PSA Birth Certificate (Photocopy)</li>
                        <li>Two (2) recent 2x2 ID pictures</li>
                        <li>Entrance Examination Result</li>
                        <li>Medical Certificate (for certain programs)</li>
                      </ul>
                      
                      <h5 class="mt-4 mb-3">Application Procedure</h5>
                      <ol>
                        <li>Secure and fill out the application form</li>
                        <li>Submit all required documents to the Admissions Office</li>
                        <li>Take the entrance examination on the scheduled date</li>
                        <li>Wait for the examination results (usually within 3-5 working days)</li>
                        <li>Complete enrollment upon acceptance</li>
                      </ol>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Enrolment Procedure Section -->
        <section class="elementor-section elementor-top-section elementor-element" data-id="sec-enrolment" id="sec-enrolment">
          <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element" data-id="col-enrolment">
              <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-heading elementor-widget elementor-widget-heading" data-id="h-enrolment">
                  <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">Enrolment Procedure for Incoming Freshmen</h2>
                    <div class="card card-soft p-4 mt-4">
                      <h5 class="mb-3">Step-by-Step Enrolment Process</h5>
                      <ol>
                        <li><strong>Pre-Enrollment:</strong> Submit all required documents</li>
                        <li><strong>Academic Advising:</strong> Meet with an academic advisor to discuss your program and course schedule</li>
                        <li><strong>Course Registration:</strong> Register for your classes through the Registrar's Office</li>
                        <li><strong>ID Processing:</strong> Have your student ID photo taken</li>
                        <li><strong>Orientation:</strong> Attend the new student orientation program</li>
                      </ol>
                      
                      <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> Please bring all original documents for verification during enrollment.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- FAQ Section -->
        <section class="elementor-section elementor-top-section elementor-element" data-id="sec-faq" id="sec-faq">
          <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element" data-id="col-faq">
              <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-heading elementor-widget elementor-widget-heading" data-id="h-faq">
                  <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">Frequently Asked Questions</h2>
                    <div class="card card-soft p-4 mt-4">
                      <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                          <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                              When is the application period?
                            </button>
                          </h2>
                          <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                              The application period typically starts in January and ends in May for the first semester, and July to November for the second semester.
                            </div>
                          </div>
                        </div>
                        <div class="accordion-item">
                          <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                              What is the passing score for the entrance examination?
                            </button>
                          </h2>
                          <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                              The passing score varies by program. Generally, a score of 75% or higher is required. Some programs may have higher requirements.
                            </div>
                          </div>
                        </div>
                        <div class="accordion-item">
                          <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                              Can I apply for multiple programs?
                            </button>
                          </h2>
                          <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                              Yes, you can apply for up to two programs. However, you will need to take separate entrance examinations for each program.
                            </div>
                          </div>
                        </div>
                        <div class="accordion-item">
                          <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                              What if I fail the entrance examination?
                            </button>
                          </h2>
                          <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                              You may retake the examination after a waiting period of one semester. Alternatively, you may apply for other programs that may have different requirements.
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Downloadable Forms Section -->
        <section class="elementor-section elementor-top-section elementor-element" data-id="sec-downloads" id="sec-downloads">
          <div class="elementor-container elementor-column-gap-default">
            <div class="elementor-column elementor-col-100 elementor-top-column elementor-element" data-id="col-downloads">
              <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-heading elementor-widget elementor-widget-heading" data-id="h-downloads">
                  <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">Downloadable Forms</h2>
                    <div class="card card-soft p-4 mt-4">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <div class="card border p-3">
                            <h6 class="mb-2"><i class="bi bi-file-earmark-word text-primary me-2"></i>Application Form</h6>
                            <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary" onclick="handleDownload(event, null, '../../assets/documents/Rgistration Form.docx')">
                              <i class="bi bi-download me-1"></i> Download
                            </a>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="card border p-3">
                            <h6 class="mb-2"><i class="bi bi-file-earmark-word text-primary me-2"></i>Enrollment Form</h6>
                            <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary" onclick="handleDownload(event, null, '../../assets/documents/Rgistration Form.docx')">
                              <i class="bi bi-download me-1"></i> Download
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

      </main>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="d-flex align-items-center mb-3">
            <img src="../../assets/img/logo.png" alt="Logo" style="height: 50px; margin-right: 15px;">
            <h5 class="mb-0"><?php echo strtoupper($settings['institution_name'] ?? 'COLEGIO DE NAUJAN'); ?></h5>
          </div>
          <p>A premier higher education institution committed to academic excellence, innovation, and character formation.</p>
          <div class="social-icons mt-4">
            <a href="https://web.facebook.com/profile.php?id=61574804835893" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="mailto:colegiodenaujan@gmail.com" title="Email Registrar"><i class="fas fa-envelope"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        
        <div class="col-lg-2 col-md-6 mb-4">
          <h5>Quick Links</h5>
          <div class="footer-links">
            <a href="../../index.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="program.php">Academic Programs</a>
            <a href="admission.php">Admissions</a>
            <a href="handbook.php">Student Handbook</a>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <h5>Contact Info</h5>
          <div class="footer-links">
            <p><i class="fas fa-map-marker-alt me-2"></i> <?php echo $settings['address'] ?? 'Brgy. Sta. Cruz, Naujan, Oriental Mindoro'; ?></p>
            <p><i class="fas fa-phone me-2"></i> <?php echo $settings['contact_phone'] ?? '(043) 123-4567'; ?></p>
            <p><i class="fas fa-envelope me-2"></i> <?php echo $settings['contact_email'] ?? 'admissions@colegiodenaujan.edu.ph'; ?></p>
            <p><i class="fas fa-clock me-2"></i> Mon-Fri: 8:00 AM - 5:00 PM</p>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom text-center">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo $settings['institution_name'] ?? 'Colegio De Naujan'; ?>. All Rights Reserved. | <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms of Use</a></p>
      </div>
    </div>
  </footer>

  <!-- Load Shared Navigation - Load early to prevent white flash -->
  <script src="../../assets/js/load-nav.js"></script>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- AOS Animation -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  
  <!-- Admission JS -->
  <script src="../../js/admission.js" defer></script>
</body>
</html>
