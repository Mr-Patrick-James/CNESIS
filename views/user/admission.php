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
    
    * {
      margin: 0 !important;
      padding: 0 !important;
      box-sizing: border-box;
    }
    
    body {
      font-family: Arial, sans-serif !important;
      color: var(--dark-gray) !important;
      background-color: #f5f5f5 !important;
    }
    
    h1, h2, h3, h4, h5, h6 {
      font-family: Arial, sans-serif !important;
      font-weight: normal !important;
    }
    
    /* Simple Navigation */
    .navbar {
      background-color: var(--primary-blue) !important;
      padding: 15px 0 !important;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
      border-bottom: none !important;
    }
    
    .navbar-brand {
      display: flex !important;
      align-items: center !important;
    }
    
    .logo-img {
      height: 40px !important;
      width: auto !important;
      margin-right: 10px !important;
    }
    
    .brand-text {
      line-height: 1.2 !important;
    }
    
    .brand-name {
      font-weight: bold !important;
      font-size: 1.4rem !important;
      color: white !important;
    }
    
    .brand-subtitle {
      font-size: 0.7rem !important;
      color: rgba(255, 255, 255, 0.8) !important;
      display: block !important;
    }
    
    .nav-link {
      color: white !important;
      font-weight: normal !important;
      margin: 0 10px !important;
      padding: 8px 12px !important;
      border-radius: 0 !important;
      font-size: 0.9rem !important;
      background: none !important;
    }
    
    .nav-link:hover {
      background: rgba(255, 255, 255, 0.1) !important;
      color: white !important;
    }
    
    .login-btn {
      background-color: var(--accent-gold) !important;
      color: var(--primary-blue) !important;
      border-radius: 4px !important;
      padding: 8px 20px !important;
      font-weight: bold !important;
      border: none !important;
      box-shadow: none !important;
    }
    
    .login-btn:hover {
      background-color: #e6c158 !important;
      color: var(--primary-blue) !important;
    }
    
    /* Hero Banner */
    .hero-banner {
      height: 400px !important;
      background-color: var(--primary-blue) !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      color: white !important;
      text-align: center !important;
      margin-top: 60px !important;
      padding: 20px !important;
    }
    
    .hero-overlay {
      display: none !important;
    }
    
    .hero-text {
      position: relative !important;
      z-index: 2 !important;
      text-align: center !important;
      color: white !important;
    }
    
    .hero-text h1 {
      font-size: 2.5rem !important;
      font-weight: bold !important;
      text-shadow: none !important;
      margin: 0 !important;
    }
    
    /* Main Content */
    .admission-section {
      padding: 40px 0 !important;
      background-color: #f5f5f5 !important;
    }
    
    .section-title {
      color: var(--primary-blue) !important;
      font-size: 1.3rem !important;
      font-weight: bold !important;
      margin-bottom: 20px !important;
      padding-bottom: 10px !important;
      border-bottom: 2px solid var(--accent-gold) !important;
    }
    
    .card-soft {
      border: 1px solid #ddd !important;
      border-radius: 4px !important;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
      background-color: white !important;
      padding: 20px !important;
      margin-bottom: 20px !important;
      transform: none !important;
    }
    
    .card-soft:hover {
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
      transform: none !important;
    }
    
    .card-soft h6 {
      color: var(--primary-blue) !important;
      margin-bottom: 15px !important;
      font-weight: bold !important;
    }
    
    .card-soft ul {
      list-style: disc !important;
      margin-left: 20px !important;
      padding: 0 !important;
    }
    
    .card-soft ul li {
      margin-bottom: 8px !important;
      color: #555 !important;
    }
    
    .btn-outline-primary {
      border: 1px solid var(--primary-blue) !important;
      color: var(--primary-blue) !important;
      background-color: white !important;
      padding: 10px 15px !important;
      margin: 5px !important;
      border-radius: 4px !important;
      font-weight: normal !important;
      cursor: pointer !important;
      transform: none !important;
    }
    
    .btn-outline-primary:hover {
      background-color: var(--primary-blue) !important;
      color: white !important;
      transform: none !important;
    }
    
    .btn-success {
      background-color: #28a745 !important;
      border: 1px solid #28a745 !important;
      color: white !important;
      padding: 10px 20px !important;
      border-radius: 4px !important;
      font-weight: normal !important;
      cursor: pointer !important;
      box-shadow: none !important;
    }
    
    .btn-success:hover {
      background-color: #218838 !important;
      box-shadow: none !important;
    }
    
    .form-control {
      border: 1px solid #ddd !important;
      border-radius: 4px !important;
      padding: 10px 12px !important;
      font-family: Arial, sans-serif !important;
    }
    
    .form-control:focus {
      border-color: var(--primary-blue) !important;
      box-shadow: 0 0 0 1px rgba(26, 54, 93, 0.2) !important;
      outline: none !important;
    }
    
    .form-label {
      font-weight: bold !important;
      color: var(--primary-blue) !important;
      margin-bottom: 5px !important;
      display: block !important;
    }
    
    .btn-link {
      background: none !important;
      border: none !important;
      color: var(--secondary-blue) !important;
      text-decoration: none !important;
      cursor: pointer !important;
      padding: 0 !important;
      font-weight: normal !important;
    }
    
    .btn-link:hover {
      text-decoration: underline !important;
    }
    
    /* Modal */
    .modal-content {
      border: 1px solid #ddd !important;
      border-radius: 4px !important;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2) !important;
    }
    
    .modal-header {
      background-color: var(--primary-blue) !important;
      color: white !important;
      border-bottom: 1px solid #ddd !important;
      padding: 15px 20px !important;
    }
    
    .modal-header .btn-close {
      color: white !important;
      background: none !important;
    }
    
    .modal-body {
      padding: 20px !important;
      background-color: white !important;
    }
    
    .modal-footer {
      border-top: 1px solid #ddd !important;
      padding: 15px 20px !important;
      background-color: #f5f5f5 !important;
    }
    
    /* Footer */
    .footer {
      background-color: #333 !important;
      color: white !important;
      padding: 40px 0 20px !important;
      margin-top: 40px !important;
    }
    
    .footer h5 {
      color: var(--accent-gold) !important;
      margin-bottom: 15px !important;
      font-weight: bold !important;
    }
    
    .footer-links a {
      color: #ccc !important;
      text-decoration: none !important;
      display: block !important;
      margin-bottom: 8px !important;
    }
    
    .footer-links a:hover {
      color: var(--accent-gold) !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .hero-text h1 {
        font-size: 1.8rem !important;
      }
      
      .section-title {
        font-size: 1.1rem !important;
      }
      
      .card-soft {
        padding: 15px !important;
      }
    }
  </style>
  
  <style>
    :root {
      --primary-blue: #1a365d;
      --secondary-blue: #2d55a0;
      --accent-gold: #d4af37;
      --light-gray: #f8f9fa;
      --dark-gray: #333333;
    }
    
    * {
      margin: 0;
      padding: 0;
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
    
    /* Simple Navigation */
    .navbar {
      background-color: var(--primary-blue);
      padding: 15px 0;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
    
    .brand-text {
      line-height: 1.2;
    }
    
    .brand-name {
      font-weight: bold;
      font-size: 1.4rem;
      color: white;
    }
    
    .brand-subtitle {
      font-size: 0.7rem;
      color: rgba(255, 255, 255, 0.8);
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
      background: rgba(255, 255, 255, 0.1);
    }
    
    .login-btn {
      background-color: var(--accent-gold);
      color: var(--primary-blue) !important;
      border-radius: 4px;
      padding: 8px 20px !important;
      font-weight: bold;
      border: none;
    }
    
    .login-btn:hover {
      background-color: #e6c158;
      color: var(--primary-blue) !important;
    }
    
    .navbar-toggler {
      border: 1px solid rgba(255, 255, 255, 0.3);
      padding: 5px 10px;
    }
    
    /* Hero Banner */
    .hero-banner {
      height: 400px;
      background-color: var(--primary-blue);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      margin-top: 60px;
    }
    
    .hero-text h1 {
      font-size: 2.5rem;
      font-weight: bold;
    }
    
    /* Main Content */
    .admission-section {
      padding: 40px 0;
      background-color: #f5f5f5;
    }
    
    .section-title {
      color: var(--primary-blue);
      font-size: 1.3rem;
      font-weight: bold;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--accent-gold);
    }
    
    .card-soft {
      border: 1px solid #ddd;
      border-radius: 4px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      background-color: white;
      padding: 20px;
      margin-bottom: 20px;
    }
    
    .card-soft h6 {
      color: var(--primary-blue);
      margin-bottom: 15px;
      font-weight: bold;
    }
    
    .card-soft ul {
      list-style: disc;
      margin-left: 20px;
    }
    
    .card-soft ul li {
      margin-bottom: 8px;
      color: #555;
    }
    
    .btn-outline-primary {
      border: 1px solid var(--primary-blue);
      color: var(--primary-blue);
      background-color: white;
      padding: 10px 15px;
      margin: 5px;
      border-radius: 4px;
      font-weight: normal;
      cursor: pointer;
    }
    
    .btn-outline-primary:hover {
      background-color: var(--primary-blue);
      color: white;
    }
    
    .btn-success {
      background-color: #28a745;
      border: 1px solid #28a745;
      color: white;
      padding: 10px 20px;
      border-radius: 4px;
      font-weight: normal;
      cursor: pointer;
    }
    
    .btn-success:hover {
      background-color: #218838;
    }
    
    .form-control {
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 10px 12px;
      font-family: Arial, sans-serif;
    }
    
    .form-control:focus {
      border-color: var(--primary-blue);
      box-shadow: 0 0 0 2px rgba(26, 54, 93, 0.1);
      outline: none;
    }
    
    .form-label {
      font-weight: bold;
      color: var(--primary-blue);
      margin-bottom: 5px;
      display: block;
    }
    
    .btn-link {
      background: none;
      border: none;
      color: var(--secondary-blue);
      text-decoration: none;
      cursor: pointer;
      padding: 0;
      font-weight: normal;
    }
    
    .btn-link:hover {
      text-decoration: underline;
    }
    
    /* Modal */
    .modal-content {
      border: 1px solid #ddd;
      border-radius: 4px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
      background-color: var(--primary-blue);
      color: white;
      border-bottom: 1px solid #ddd;
      padding: 15px 20px;
    }
    
    .modal-header .btn-close {
      color: white;
    }
    
    .modal-body {
      padding: 20px;
      background-color: white;
    }
    
    .modal-footer {
      border-top: 1px solid #ddd;
      padding: 15px 20px;
      background-color: #f5f5f5;
    }
    
    /* Footer */
    .footer {
      background-color: #333;
      color: white;
      padding: 40px 0 20px;
      margin-top: 40px;
    }
    
    .footer h5 {
      color: var(--accent-gold);
      margin-bottom: 15px;
      font-weight: bold;
    }
    
    .footer-links a {
      color: #ccc;
      text-decoration: none;
      display: block;
      margin-bottom: 8px;
    }
    
    .footer-links a:hover {
      color: var(--accent-gold);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .hero-text h1 {
        font-size: 1.8rem;
      }
      
      .section-title {
        font-size: 1.1rem;
      }
      
      .card-soft {
        padding: 15px;
      }
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
                <button type="button" class="btn btn-link text-success p-0" onclick="showCheckRepliesModal()" style="display: none;">
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

        <style>
          .student-msg-bubble {
            max-width: 85%;
            margin-bottom: 12px;
            padding: 8px 15px;
            border-radius: 18px;
            font-size: 0.9rem;
            position: relative;
            line-height: 1.4;
          }
          .student-msg-student {
            align-self: flex-end;
            background: #28a745;
            color: white;
            border-bottom-right-radius: 4px;
            margin-left: auto;
          }
          .student-msg-admin {
            align-self: flex-start;
            background: #e9ecef;
            color: #333;
            border-bottom-left-radius: 4px;
            margin-right: auto;
          }
          .student-msg-time {
            font-size: 0.7rem;
            opacity: 0.7;
            margin-top: 4px;
            display: block;
            text-align: right;
          }
        </style>

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
