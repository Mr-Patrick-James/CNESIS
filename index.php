<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="Colegio De Naujan - Excellence in Education, Shaping Tomorrow's Leaders">
  <title>Colegio De Naujan - Excellence in Education</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  
  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  
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
    }
    
    h1, h2, h3, h4, h5 {
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
    
    /* Login Modal Styling */
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
    
    .form-label {
      font-weight: 600;
      color: var(--primary-blue);
      margin-bottom: 8px;
    }
    
    .form-control {
      padding: 12px 15px;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-size: 1rem;
    }
    
    .form-control:focus {
      border-color: var(--accent-gold);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
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
    
    /* Hero Video Section */
    .video-hero {
      position: relative;
      height: 100vh;
      min-height: 700px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    #background-video {
      position: absolute;
      top: 50%;
      left: 50%;
      min-width: 100%;
      min-height: 100%;
      width: auto;
      height: auto;
      z-index: -1;
      transform: translateX(-50%) translateY(-50%);
      object-fit: cover;
    }
    
    .video-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to right, rgba(26, 54, 93, 0.85), rgba(26, 54, 93, 0.7));
      z-index: 0;
    }
    
    .hero-content {
      position: relative;
      z-index: 1;
      color: white;
      text-align: center;
      max-width: 900px;
      padding: 20px;
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
    }
    
    .highlight {
      color: var(--accent-gold);
    }
    
    .hero-subtitle {
      font-size: 1.3rem;
      margin-bottom: 40px;
      opacity: 0.9;
    }
    
    .hero-buttons .btn {
      padding: 12px 30px;
      border-radius: 5px;
      font-weight: 600;
      margin: 0 10px;
      transition: all 0.3s ease;
    }
    
    .btn-primary-custom {
      background-color: var(--accent-gold);
      border-color: var(--accent-gold);
      color: var(--primary-blue);
    }
    
    .btn-primary-custom:hover {
      background-color: #e6c158;
      border-color: #e6c158;
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    
    .btn-outline-custom {
      background-color: transparent;
      border-color: white;
      color: white;
    }
    
    .btn-outline-custom:hover {
      background-color: white;
      color: var(--primary-blue);
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    
    .video-controls {
      position: absolute;
      bottom: 30px;
      right: 30px;
      z-index: 10;
    }
    
    .video-control-btn {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.2);
      border: none;
      color: white;
      font-size: 1.2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      backdrop-filter: blur(5px);
    }
    
    .video-control-btn:hover {
      background-color: rgba(255, 255, 255, 0.3);
      transform: scale(1.1);
    }
    
    /* Stats Section */
    .stats-section {
      background-color: var(--light-gray);
      padding: 80px 0;
    }
    
    .stat-box {
      text-align: center;
      padding: 30px 20px;
      border-radius: 10px;
      background-color: white;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease;
      height: 100%;
    }
    
    .stat-box:hover {
      transform: translateY(-10px);
    }
    
    .stat-icon {
      font-size: 2.5rem;
      color: var(--secondary-blue);
      margin-bottom: 15px;
    }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary-blue);
      margin-bottom: 5px;
    }
    
    .stat-label {
      font-size: 1rem;
      color: var(--dark-gray);
    }
    
    /* Programs Section */
    .programs-section {
      padding: 100px 0;
    }
    
    .section-title {
      color: var(--primary-blue);
      margin-bottom: 15px;
      position: relative;
      padding-bottom: 15px;
    }
    
    .section-title:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 80px;
      height: 4px;
      background-color: var(--accent-gold);
    }
    
    .text-center .section-title:after {
      left: 50%;
      transform: translateX(-50%);
    }
    
    .program-card {
      border: none;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      height: 100%;
    }
    
    .program-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    
    .program-img {
      height: 200px;
      overflow: hidden;
    }
    
    .program-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    
    .program-card:hover .program-img img {
      transform: scale(1.05);
    }
    
    .program-body {
      padding: 25px;
    }
    
    .program-title {
      color: var(--primary-blue);
      margin-bottom: 15px;
    }
    
    .program-link {
      color: var(--secondary-blue);
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
    }
    
    .program-link i {
      margin-left: 5px;
      transition: transform 0.3s ease;
    }
    
    .program-link:hover i {
      transform: translateX(5px);
    }
    
    /* Features Section */
    .features-section {
      background-color: var(--primary-blue);
      color: white;
      padding: 100px 0;
    }
    
    .feature-icon {
      font-size: 3rem;
      color: var(--accent-gold);
      margin-bottom: 20px;
    }
    
    .feature-title {
      margin-bottom: 15px;
      font-size: 1.5rem;
    }
    
    /* CTA Section */
    .cta-section {
      padding: 100px 0;
      background: linear-gradient(rgba(26, 54, 93, 0.9), rgba(26, 54, 93, 0.9)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: white;
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
    
    /* Scroll indicator */
    .scroll-indicator {
      position: absolute;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 10;
    }
    
    .mouse {
      width: 30px;
      height: 50px;
      border: 2px solid white;
      border-radius: 20px;
      display: block;
      margin: 0 auto 10px;
      position: relative;
    }
    
    .wheel {
      width: 4px;
      height: 10px;
      background-color: white;
      border-radius: 2px;
      position: absolute;
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
      animation: scroll 2s infinite;
    }
    
    @keyframes scroll {
      0% { opacity: 1; top: 10px; }
      100% { opacity: 0; top: 30px; }
    }
    
    /* Mobile Menu Styling */
    @media (max-width: 991.98px) {
      .navbar-collapse {
        background-color: rgba(26, 54, 93, 0.98);
        backdrop-filter: blur(15px);
        padding: 20px;
        border-radius: 15px;
        margin-top: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
      }
      
      .nav-link {
        margin: 5px 0;
        padding: 12px 20px !important;
        border-radius: 8px;
      }
      
      .login-btn {
        margin-top: 10px;
        justify-content: center;
        display: flex;
        align-items: center;
      }
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
      .hero-title {
        font-size: 2.8rem;
      }
    }
    
    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.3rem;
      }
      
      .hero-subtitle {
        font-size: 1.1rem;
      }
      
      .hero-buttons .btn {
        display: block;
        margin: 10px auto;
        max-width: 250px;
      }
    }
    
    @media (max-width: 576px) {
      .hero-title {
        font-size: 2rem;
      }
      
      .video-hero {
        min-height: 600px;
      }
      
      .brand-name {
        font-size: 1.4rem;
      }
      
      .logo-icon {
        font-size: 1.8rem;
        margin-right: 8px;
      }
      
      .user-type-selector {
        flex-direction: column;
      }
      
      .modal-body, .modal-header, .modal-footer {
        padding: 20px;
      }
    }
    
    /* Smooth hover effect for all nav items */
    .nav-item {
      transition: transform 0.3s ease;
    }
    
    .nav-item:hover {
      transform: translateY(-1px);
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <div class="logo-icon">
          <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="brand-text">
          <span class="brand-name">COLEGIO DE NAUJAN</span>
          <small class="brand-subtitle">Excellence in Education</small>
        </div>
      </a>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item">
            <a class="nav-link active" href="index.php">
              <i class="fas fa-home d-lg-none me-2"></i>
              HOME
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="views/user/about.php">
              <i class="fas fa-info-circle d-lg-none me-2"></i>
              ABOUT
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="views/user/program.php">
              <i class="fas fa-book d-lg-none me-2"></i>
              PROGRAMS
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="views/user/admission.php">
              <i class="fas fa-user-graduate d-lg-none me-2"></i>
              ADMISSIONS
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="views/user/handbook.php">
              <i class="fas fa-book-open d-lg-none me-2"></i>
              HANDBOOK
            </a>
          </li>
          <li class="nav-item ms-lg-2">
            <a class="nav-link login-btn" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
              <i class="fas fa-sign-in-alt me-1"></i>
              LOGIN
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">
            <i class="fas fa-graduation-cap me-2"></i>
            Colegio De Naujan Admin Portal
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="login-icon">
            <i class="fas fa-user-tie"></i>
          </div>
          
          <form id="loginForm">
            <div class="mb-3">
              <label for="username" class="form-label">Username or Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" id="username" placeholder="Enter your username or email" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="password" placeholder="Enter your password" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
            
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe">
              <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-modal-primary">
                <i class="fas fa-sign-in-alt me-2"></i>
                LOGIN
              </button>
            </div>
          </form>
          
          <div class="login-options mt-3">
            <a href="#" class="d-block mb-2">Forgot Password?</a>
            <a href="views/user/admission.php">New Student? Apply Now</a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-modal-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-modal-primary" id="demoLogin">Demo Login</button>
        </div>
      </div>
    </div>
  </div>

  <section class="video-hero" id="home">
    <video autoplay muted loop playsinline id="background-video">
      <source src="" type="video/mp4" id="videoSource">
      Your browser does not support HTML5 video.
    </video>
    
    <div class="video-overlay"></div>
    
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
      <h1 class="hero-title">
        <span class="d-block">Excellence in</span>
        <span class="highlight">Higher Education</span>
      </h1>
      <p class="hero-subtitle">
        Shaping future leaders through innovative academic programs,<br>
        experienced faculty, and state-of-the-art facilities.
      </p>
      <div class="hero-buttons">
        <a href="views/user/admission.php" class="btn btn-primary-custom">START YOUR JOURNEY</a>
        <a href="views/user/about.php" class="btn btn-outline-custom">LEARN MORE</a>
      </div>
    </div>
    
    <div class="video-controls">
      <button class="video-control-btn" id="muteBtn" title="Toggle sound">
        <i class="fas fa-volume-mute" id="volumeIcon"></i>
      </button>
    </div>
    
    <div class="scroll-indicator">
      <div class="mouse">
        <div class="wheel"></div>
      </div>
    </div>
  </section>

  <section class="stats-section" id="stats">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
          <div class="stat-box">
            <div class="stat-icon">
              <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-number">2,500+</div>
            <div class="stat-label">Active Students</div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
          <div class="stat-box">
            <div class="stat-icon">
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-number">150+</div>
            <div class="stat-label">Faculty Members</div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
          <div class="stat-box">
            <div class="stat-icon">
              <i class="fas fa-book"></i>
            </div>
            <div class="stat-number">35+</div>
            <div class="stat-label">Academic Programs</div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="400">
          <div class="stat-box">
            <div class="stat-icon">
              <i class="fas fa-award"></i>
            </div>
            <div class="stat-number">98%</div>
            <div class="stat-label">Graduation Rate</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="programs-section" id="programs">
    <div class="container">
      <div class="row mb-5">
        <div class="col-12 text-center">
          <h2 class="section-title">Our Academic Programs</h2>
          <p class="lead">Choose from a wide range of undergraduate and graduate programs designed for success in today's competitive world.</p>
        </div>
      </div>
      
      <div class="row g-4">
        <?php
        // Connect to database and fetch active programs
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=cnesis_db", 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->query("SELECT id, title, short_title, description, image_path, code FROM programs WHERE status = 'active' ORDER BY id LIMIT 3");
            $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($programs as $index => $program) {
                $delay = ($index + 1) * 100;
                $imagePath = $program['image_path'] ? $program['image_path'] : 'assets/img/programs/default.jpg';
                $programTitle = $program['short_title'] ? $program['short_title'] : $program['title'];
                $programDesc = $program['description'] ? substr($program['description'], 0, 150) . '...' : 'Learn more about this program.';
        ?>
        
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
          <div class="program-card">
            <div class="program-img">
              <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($programTitle); ?>">
            </div>
            <div class="program-body">
              <h4 class="program-title"><?php echo htmlspecialchars($programTitle); ?></h4>
              <p class="mb-3"><?php echo htmlspecialchars($programDesc); ?></p>
              <a href="views/user/program.php" class="program-link">Explore Programs <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
        
        <?php
            }
        } catch (PDOException $e) {
            // Fallback to static cards if database fails
        ?>
        
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
          <div class="program-card">
            <div class="program-img">
              <img src="assets/img/bsis.jpg" alt="Information Technology">
            </div>
            <div class="program-body">
              <h4 class="program-title">Information Technology</h4>
              <p class="mb-3">Information Systems and Computer Hardware Servicing programs with modern laboratories and industry partnerships for IT careers.</p>
              <a href="views/user/program.php" class="program-link">Explore Programs <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
          <div class="program-card">
            <div class="program-img">
              <img src="assets/img/wft.jpg" alt="Technical-Vocational">
            </div>
            <div class="program-body">
              <h4 class="program-title">Technical-Vocational</h4>
              <p class="mb-3">Welding and Fabrication Technology programs with hands-on training and practical skills for technical careers.</p>
              <a href="views/user/program.php" class="program-link">Explore Programs <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
        
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
          <div class="program-card">
            <div class="program-img">
              <img src="assets/img/btvted.jpg" alt="Public Administration">
            </div>
            <div class="program-body">
              <h4 class="program-title">Public Administration</h4>
              <p class="mb-3">Bachelor of Public Administration program designed to develop future public leaders, administrators, and policymakers.</p>
              <a href="views/user/program.php" class="program-link">Explore Programs <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
        
        <?php } ?>
      </div>
    </div>
  </section>

  <section class="features-section" id="features">
    <div class="container">
      <div class="row mb-5">
        <div class="col-12 text-center">
          <h2 class="section-title text-white">Why Choose Colegio De Naujan</h2>
          <p class="lead text-white-50">We provide an exceptional learning environment focused on academic excellence and personal growth.</p>
        </div>
      </div>
      
      <div class="row g-4">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
          <div class="text-center">
            <div class="feature-icon"><i class="fas fa-graduation-cap"></i></div>
            <h4 class="feature-title">Quality Education</h4>
            <p class="text-white-50">Programs aligned with industry needs and delivered by experienced educators.</p>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
          <div class="text-center">
            <div class="feature-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <h4 class="feature-title">Expert Faculty</h4>
            <p class="text-white-50">Mentors dedicated to guiding students toward success and lifelong learning.</p>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
          <div class="text-center">
            <div class="feature-icon"><i class="fas fa-building-columns"></i></div>
            <h4 class="feature-title">Modern Facilities</h4>
            <p class="text-white-50">Well-equipped learning spaces designed for hands-on training and innovation.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="cta-section" id="cta">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center" data-aos="fade-up">
          <h2 class="mb-3">Ready to Start Your Journey?</h2>
          <p class="lead mb-4">Apply now and become part of a community committed to excellence.</p>
          <a href="views/user/admission.php" class="btn btn-primary-custom">APPLY NOW</a>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
          <h5>COLEGIO DE NAUJAN</h5>
          <p class="mt-3">A premier higher education institution committed to academic excellence, innovation, and character formation.</p>
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
            <a href="index.php">Home</a>
            <a href="views/user/about.php">About Us</a>
            <a href="views/user/program.php">Academic Programs</a>
            <a href="views/user/admission.php">Admissions</a>
            <a href="views/user/handbook.php">Student Handbook</a>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <h5>Contact Info</h5>
          <div class="footer-links">
            <p><i class="fas fa-map-marker-alt me-2"></i> Brgy. Sta. Cruz, Naujan, Oriental Mindoro</p>
            <p><i class="fas fa-phone me-2"></i> (043) 123-4567</p>
            <p><i class="fas fa-envelope me-2"></i> info@colegiodenaujan.edu.ph</p>
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
        <p class="mb-0">&copy; <span id="year"></span> Colegio De Naujan. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <!-- Custom JavaScript -->
  <script>
    // Initialize AOS animations
    AOS.init({
      duration: 1000,
      once: true,
      offset: 100
    });

    // Set current year in footer
    const yearEl = document.getElementById('year');
    if (yearEl) {
      yearEl.textContent = new Date().getFullYear();
    }
    
    // Load home video from settings
    function loadHomeVideo() {
      fetch('api/settings/system-settings.php?group=media')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const videoSetting = data.settings.find(s => s.key === 'home_video');
            if (videoSetting && videoSetting.value) {
              const videoSource = document.getElementById('videoSource');
              const backgroundVideo = document.getElementById('background-video');
              
              if (videoSource && backgroundVideo) {
                videoSource.src = videoSetting.value;
                backgroundVideo.load(); // Reload the video with new source
              }
            }
          }
        })
        .catch(error => {
          console.error('Error loading home video:', error);
          // Fallback to default video
          const videoSource = document.getElementById('videoSource');
          const backgroundVideo = document.getElementById('background-video');
          if (videoSource && backgroundVideo) {
            videoSource.src = 'assets/videos/landingvid.mp4';
            backgroundVideo.load();
          }
        });
    }
    
    // Load video when page loads
    document.addEventListener('DOMContentLoaded', loadHomeVideo);

    // Enhanced navbar JavaScript
    document.addEventListener('DOMContentLoaded', function() {
      const navbar = document.querySelector('.navbar');
      
      // Scroll effect
      window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      });
      
      // Add shadow on scroll
      window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
          navbar.style.boxShadow = '0 6px 25px rgba(0, 0, 0, 0.2)';
        } else {
          navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
        }
      });
      
      // Smooth animation for active state
      document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
          // Remove active class from all links
          document.querySelectorAll('.nav-link').forEach(item => {
            item.classList.remove('active');
          });
          
          // Add active class to clicked link
          this.classList.add('active');
          
          // Close mobile menu if open
          if (window.innerWidth < 992) {
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse.classList.contains('show')) {
              const bsCollapse = new bootstrap.Collapse(navbarCollapse);
              bsCollapse.hide();
            }
          }
        });
      });
      
      // Login Modal Functionality
      const loginModal = document.getElementById('loginModal');
      if (loginModal) {
        
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
          togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
          });
        }
        
        // Demo login functionality
        const demoLoginBtn = document.getElementById('demoLogin');
        if (demoLoginBtn) {
          demoLoginBtn.addEventListener('click', function() {
            const creds = { username: 'admin_demo@colegio.edu', password: 'demo123' };
            document.getElementById('username').value = creds.username;
            document.getElementById('password').value = creds.password;
            
            // Show success message
            const modalBody = loginModal.querySelector('.modal-body');
            const successAlert = document.createElement('div');
            successAlert.className = 'alert alert-success alert-dismissible fade show';
            successAlert.innerHTML = `
              <i class="fas fa-check-circle me-2"></i>
              <strong>Demo credentials loaded!</strong> Click "LOGIN" to proceed with admin demo account.
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const existingAlert = modalBody.querySelector('.alert');
            if (existingAlert) existingAlert.remove();
            
            modalBody.insertBefore(successAlert, modalBody.firstChild);
            
            // Auto-close after 5 seconds
            setTimeout(() => {
              const bsAlert = new bootstrap.Alert(successAlert);
              bsAlert.close();
            }, 5000);
          });
        }
        
        // Form submission
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
          loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const userType = 'admin';
            
            // Simple validation
            if (!username || !password) {
              alert('Please fill in all fields');
              return;
            }
            
            // Credential validation
            const validCredentials = {
              admin: { username: 'admin_demo@colegio.edu', password: 'demo123' }
            };
            
            const expectedCreds = validCredentials[userType];
            
            // Show loading state
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
            submitBtn.disabled = true;
            
            // Simulate login process
            setTimeout(() => {
              submitBtn.innerHTML = originalText;
              submitBtn.disabled = false;
              
              // Validate credentials
              if (username !== expectedCreds.username || password !== expectedCreds.password) {
                // Show error message
                const modalBody = loginModal.querySelector('.modal-body');
                const errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-danger alert-dismissible fade show';
                errorAlert.innerHTML = `
                  <i class="fas fa-exclamation-circle me-2"></i>
                  <strong>Login failed!</strong> Invalid credentials for admin. Please check your username and password.
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                const existingAlert = modalBody.querySelector('.alert');
                if (existingAlert) existingAlert.remove();
                
                modalBody.insertBefore(errorAlert, modalBody.firstChild);
                
                setTimeout(() => {
                  const bsAlert = new bootstrap.Alert(errorAlert);
                  bsAlert.close();
                }, 5000);
                return;
              }
              
              // Close modal
              const modal = bootstrap.Modal.getInstance(loginModal);
              modal.hide();
              
              // Show login success notification
              const successNotification = document.createElement('div');
              successNotification.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 z-3';
              successNotification.style.minWidth = '300px';
              successNotification.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                <strong>Login successful!</strong> Redirecting to admin dashboard...
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              `;
              
              document.body.appendChild(successNotification);
              
              // Redirect based on user type
              setTimeout(() => {
                successNotification.remove();
                
                window.location.href = 'views/admin/dashboard.php';
              }, 2000);
            }, 1500);
          });
        }
        
        // Reset form when modal is hidden
        loginModal.addEventListener('hidden.bs.modal', function() {
          const form = document.getElementById('loginForm');
          if (form) form.reset();
        });
      }
    });
    
    // Video mute/unmute functionality
    const video = document.getElementById('background-video');
    const muteBtn = document.getElementById('muteBtn');
    const volumeIcon = document.getElementById('volumeIcon');
    
    if (muteBtn && video) {
      muteBtn.addEventListener('click', function() {
        if (video.muted) {
          video.muted = false;
          volumeIcon.classList.remove('fa-volume-mute');
          volumeIcon.classList.add('fa-volume-up');
          muteBtn.setAttribute('title', 'Mute video');
        } else {
          video.muted = true;
          volumeIcon.classList.remove('fa-volume-up');
          volumeIcon.classList.add('fa-volume-mute');
          muteBtn.setAttribute('title', 'Unmute video');
        }
      });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 80,
            behavior: 'smooth'
          });
        }
      });
    });
  </script>
</body>
</html>