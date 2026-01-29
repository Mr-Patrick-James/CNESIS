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
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  
  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  
  <!-- Preload navigation -->
  <link rel="preload" href="../../assets/js/nav-header.html" as="fetch" crossorigin>
  
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
      font-family: 'Roboto', sans-serif;
      color: var(--dark-gray);
      overflow-x: hidden;
      background-color: #f8fafc;
    }
    
    /* Prevent navigation blinking */
    #nav-container {
      min-height: 76px;
      background-color: rgba(26, 54, 93, 0.98);
      position: relative;
      z-index: 1030;
    }
    
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
    }
    
    /* Enhanced Navigation Styles */
    .navbar {
      background-color: rgba(26, 54, 93, 0.98) !important;
      backdrop-filter: blur(10px);
      padding: 12px 0;
      transition: all 0.4s ease;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      border-bottom: 3px solid var(--accent-gold);
    }
    
    .navbar.scrolled {
      padding: 8px 0;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    }
    
    .navbar-brand {
      display: flex;
      align-items: center;
      transition: transform 0.3s ease;
    }
    
    .navbar-brand:hover {
      transform: translateY(-2px);
    }
    
    .logo-icon {
      font-size: 2.2rem;
      color: var(--accent-gold);
      margin-right: 12px;
      background: linear-gradient(135deg, var(--accent-gold), #ffd700);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      filter: drop-shadow(0 2px 4px rgba(212, 175, 55, 0.3));
    }
    
    .brand-text {
      line-height: 1.1;
    }
    
    .brand-name {
      font-weight: 800;
      font-size: 1.6rem;
      color: white;
      letter-spacing: 0.5px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .brand-subtitle {
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.8);
      display: block;
      line-height: 1;
      font-weight: 300;
      letter-spacing: 1px;
    }
    
    .nav-link {
      color: rgba(255, 255, 255, 0.95) !important;
      font-weight: 600;
      margin: 0 4px;
      padding: 10px 18px !important;
      border-radius: 8px;
      transition: all 0.3s ease;
      position: relative;
      letter-spacing: 0.3px;
      font-size: 0.95rem;
    }
    
    .nav-link:not(.login-btn)::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, var(--accent-gold), #ffd700);
      border-radius: 2px;
      transition: width 0.3s ease;
    }
    
    .nav-link:not(.login-btn):hover::after,
    .nav-link:not(.login-btn).active::after {
      width: 70%;
    }
    
    .nav-link:hover {
      color: white !important;
      background: rgba(255, 255, 255, 0.08);
      transform: translateY(-2px);
    }
    
    .nav-link.active {
      color: white !important;
      background: rgba(255, 255, 255, 0.05);
    }
    
    /* Enhanced Login Button */
    .login-btn {
      background: linear-gradient(135deg, var(--accent-gold), #e6c158);
      color: var(--primary-blue) !important;
      border-radius: 8px;
      padding: 10px 24px !important;
      font-weight: 700;
      border: none;
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
    }
    
    .login-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }
    
    .login-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
      color: var(--primary-blue) !important;
    }
    
    .login-btn:hover::before {
      left: 100%;
    }
    
    /* Navbar Toggler */
    .navbar-toggler {
      border: 2px solid rgba(255, 255, 255, 0.2);
      padding: 6px 10px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    
    .navbar-toggler:focus {
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.3);
      border-color: var(--accent-gold);
    }
    
    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
      width: 1.25em;
      height: 1.25em;
    }
    
    /* Hero Banner */
    .hero-banner {
      position: relative;
      height: 50vh;
      min-height: 400px;
      background: linear-gradient(rgba(26, 54, 93, 0.9), rgba(26, 54, 93, 0.9)), 
                  url('../../assets/img/bg.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 76px;
    }
    
    .hero-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to right, rgba(26, 54, 93, 0.7), rgba(26, 54, 93, 0.5));
      z-index: 1;
    }
    
    .hero-text {
      position: relative;
      z-index: 2;
      text-align: center;
      color: white;
    }
    
    .hero-text h1 {
      font-size: 4rem;
      font-weight: 800;
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
      margin: 0;
    }
    
    /* Admission Section */
    .admission-section {
      padding: 60px 0;
      background-color: #f8fafc;
    }
    
    .admission-wrapper {
      min-height: 60vh;
    }
    
    .section-title {
      color: var(--primary-blue);
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 25px;
      padding-bottom: 15px;
      border-bottom: 3px solid var(--accent-gold);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .section-title i {
      color: var(--accent-gold);
      font-size: 1.8rem;
    }
    
    .card-soft {
      border: none;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      background-color: white;
    }
    
    .card-soft:hover {
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
      transform: translateY(-5px);
    }
    
    .card-soft h6 {
      color: var(--primary-blue);
      margin-bottom: 15px;
      font-size: 1.2rem;
    }
    
    .card-soft ul {
      list-style: none;
      padding-left: 0;
    }
    
    .card-soft ul li {
      padding: 8px 0;
      padding-left: 25px;
      position: relative;
      color: #555;
    }
    
    .card-soft ul li:before {
      content: '\f00c';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      left: 0;
      color: var(--accent-gold);
    }
    
    .btn-outline-primary {
      border-color: var(--primary-blue);
      color: var(--primary-blue);
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
      background-color: var(--primary-blue);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(26, 54, 93, 0.3);
    }
    
    .btn-success {
      background: linear-gradient(135deg, var(--accent-gold), #e6c158);
      border: none;
      color: var(--primary-blue);
      font-weight: 600;
      padding: 12px 30px;
      transition: all 0.3s ease;
    }
    
    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
      color: var(--primary-blue);
    }
    
    .form-control {
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      padding: 12px 15px;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      border-color: var(--accent-gold);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }
    
    .form-label {
      font-weight: 600;
      color: var(--primary-blue);
      margin-bottom: 8px;
    }
    
    /* Elementor Sections */
    .elementor-section {
      padding: 60px 0;
      display: none;
    }
    
    .elementor-section.active {
      display: block;
    }
    
    .elementor-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .elementor-heading-title {
      color: var(--primary-blue);
      font-size: 2rem;
      margin-bottom: 30px;
    }
    
    /* Footer */
    .footer {
      background-color: #0f1e35;
      color: white;
      padding-top: 70px;
    }
    
    .footer h5 {
      color: var(--accent-gold);
      margin-bottom: 25px;
      font-size: 1.2rem;
    }
    
    .footer-links a {
      color: #ccc;
      text-decoration: none;
      display: block;
      margin-bottom: 10px;
      transition: color 0.3s ease;
    }
    
    .footer-links a:hover {
      color: var(--accent-gold);
      padding-left: 5px;
    }
    
    .social-icons a {
      display: inline-block;
      width: 40px;
      height: 40px;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      text-align: center;
      line-height: 40px;
      margin-right: 10px;
      color: white;
      transition: all 0.3s ease;
    }
    
    .social-icons a:hover {
      background-color: var(--accent-gold);
      color: var(--primary-blue);
      transform: translateY(-5px);
    }
    
    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding: 20px 0;
      margin-top: 50px;
    }
    
    /* Login Modal Styling (for shared navigation) */
    .modal-content {
      border: none;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
    }
    
    .modal-header {
      background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
      color: white;
      border-bottom: 3px solid var(--accent-gold);
      padding: 25px 30px;
    }
    
    .modal-header .modal-title {
      font-weight: 700;
      font-size: 1.5rem;
    }
    
    .modal-header .btn-close {
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      padding: 8px;
      background-size: 0.8em;
      transition: all 0.3s ease;
    }
    
    .modal-header .btn-close:hover {
      background-color: rgba(255, 255, 255, 0.3);
      transform: rotate(90deg);
    }
    
    .modal-body {
      padding: 30px;
      background-color: #f8fafc;
    }
    
    .login-icon {
      font-size: 3rem;
      color: var(--accent-gold);
      margin-bottom: 20px;
      text-align: center;
    }
    
    .input-group-text {
      background-color: #f1f5f9;
      border: 2px solid #e2e8f0;
      border-right: none;
      color: var(--primary-blue);
    }
    
    .modal-footer {
      border-top: 1px solid #e2e8f0;
      padding: 20px 30px;
      background-color: white;
    }
    
    .modal-footer .btn {
      padding: 10px 25px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-modal-primary {
      background: linear-gradient(135deg, var(--accent-gold), #e6c158);
      border: none;
      color: var(--primary-blue);
    }
    
    .btn-modal-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }
    
    .btn-modal-secondary {
      background-color: white;
      border: 2px solid #e2e8f0;
      color: var(--dark-gray);
    }
    
    .btn-modal-secondary:hover {
      background-color: #f8fafc;
      border-color: var(--accent-gold);
      color: var(--primary-blue);
    }
    
    .login-options {
      margin-top: 20px;
      text-align: center;
    }
    
    .login-options a {
      color: var(--secondary-blue);
      text-decoration: none;
      font-size: 0.9rem;
      transition: color 0.3s ease;
    }
    
    .login-options a:hover {
      color: var(--accent-gold);
      text-decoration: underline;
    }
    
    .user-type-selector {
      display: flex;
      gap: 10px;
      margin-bottom: 25px;
    }
    
    .user-type-btn {
      flex: 1;
      padding: 12px;
      border: 2px solid #e2e8f0;
      background-color: white;
      border-radius: 8px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 500;
    }
    
    .user-type-btn:hover {
      border-color: var(--accent-gold);
      background-color: rgba(212, 175, 55, 0.05);
    }
    
    .user-type-btn.active {
      border-color: var(--accent-gold);
      background-color: rgba(212, 175, 55, 0.1);
      color: var(--primary-blue);
      font-weight: 600;
    }
    
    .user-type-btn i {
      display: block;
      font-size: 1.5rem;
      margin-bottom: 8px;
      color: var(--secondary-blue);
    }
    
    .user-type-btn.active i {
      color: var(--accent-gold);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .hero-text h1 {
        font-size: 2.5rem;
      }
      
      .hero-banner {
        height: 40vh;
        min-height: 300px;
      }
      
      .section-title {
        font-size: 1.3rem;
      }
    }
    
    @media (max-width: 576px) {
      .hero-text h1 {
        font-size: 2rem;
      }
      
      .card-soft {
        padding: 20px !important;
      }
      
      .user-type-selector {
        flex-direction: column;
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

          <div class="card card-soft p-3 mb-3">
            <h6 class="fw-bold">Freshman</h6>
            <ul>
              <li>Form 138</li>
              <li>Good Moral Certificate</li>
              <li>PSA Birth Certificate</li>
              <li>2x2 ID Picture</li>
              <li>Entrance Exam Result</li>
            </ul>
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
              <button type="submit" class="btn btn-success">
                <i class="bi bi-send-fill me-1"></i> Submit Inquiry
              </button>
            </form>
          </div>
        </section>

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
                        <li>Pay the application and testing fee</li>
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
                        <li><strong>Pre-Enrollment:</strong> Submit all required documents and pay necessary fees</li>
                        <li><strong>Academic Advising:</strong> Meet with an academic advisor to discuss your program and course schedule</li>
                        <li><strong>Course Registration:</strong> Register for your classes through the Registrar's Office</li>
                        <li><strong>Payment:</strong> Pay tuition and other fees at the Cashier's Office</li>
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
                            <h6 class="mb-2"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Application Form</h6>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                              <i class="bi bi-download me-1"></i> Download
                            </a>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="card border p-3">
                            <h6 class="mb-2"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Medical Certificate Form</h6>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                              <i class="bi bi-download me-1"></i> Download
                            </a>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="card border p-3">
                            <h6 class="mb-2"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Scholarship Application</h6>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                              <i class="bi bi-download me-1"></i> Download
                            </a>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="card border p-3">
                            <h6 class="mb-2"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Enrollment Form</h6>
                            <a href="#" class="btn btn-sm btn-outline-primary">
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
          <h5>COLEGIO DE NAUJAN</h5>
          <p class="mt-3">A premier higher education institution committed to academic excellence, innovation, and character formation since 1985.</p>
          <div class="social-icons mt-4">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        
        <div class="col-lg-2 col-md-6 mb-4">
          <h5>Quick Links</h5>
          <div class="footer-links">
            <a href="../../index.html">Home</a>
            <a href="about.html">About Us</a>
            <a href="program.html">Academic Programs</a>
            <a href="admission.html">Admissions</a>
            <a href="../../view/handbook.pdf">Student Handbook</a>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <h5>Contact Info</h5>
          <div class="footer-links">
            <p><i class="fas fa-map-marker-alt me-2"></i> Brgy. Sta. Cruz, Naujan, Oriental Mindoro</p>
            <p><i class="fas fa-phone me-2"></i> (043) 123-4567</p>
            <p><i class="fas fa-envelope me-2"></i> admissions@colegiodenaujan.edu.ph</p>
            <p><i class="fas fa-clock me-2"></i> Mon-Fri: 8:00 AM - 5:00 PM</p>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <h5>Newsletter</h5>
          <p class="mt-3">Subscribe to get updates on admissions, events, and campus news.</p>
          <div class="input-group mt-3">
            <input type="email" class="form-control" placeholder="Your email address">
            <button class="btn btn-success" type="button">Subscribe</button>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom text-center">
        <p class="mb-0">&copy; 2023 Colegio De Naujan. All Rights Reserved. | <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms of Use</a></p>
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
