<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Programs – Colegio De Naujan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        
        /* Hero Banner */
        .hero-banner {
            position: relative;
            height: 60vh;
            min-height: 400px;
            background: linear-gradient(rgba(26, 54, 93, 0.9), rgba(26, 54, 93, 0.9)), 
                        url('../../assets/img/programs-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 76px; /* Adjust based on navbar height */
        }
        
        .hero-text {
            text-align: center;
            color: white;
            z-index: 2;
            position: relative;
        }
        
        .hero-text h1 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        .hero-text p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }
        
        /* Programs Section */
        .programs-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-title {
            color: var(--primary-blue);
            margin-bottom: 15px;
            position: relative;
            padding-bottom: 15px;
            text-align: center;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--accent-gold);
        }
        
        .intro {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            max-width: 900px;
            margin: 0 auto 50px;
            text-align: center;
            padding: 0 20px;
        }
        
        /* Program Filter */
        .program-filter {
            margin-bottom: 40px;
            text-align: center;
        }
        
        .filter-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .filter-btn {
            padding: 10px 25px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 30px;
            font-weight: 600;
            color: var(--dark-gray);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            border-color: var(--accent-gold);
            color: var(--primary-blue);
            transform: translateY(-2px);
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, var(--accent-gold), #e6c158);
            border-color: var(--accent-gold);
            color: var(--primary-blue);
        }
        
        /* Program Cards */
        .program-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .program-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .program-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .program-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .program-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s ease;
        }
        
        .program-card:hover .program-image img {
            transform: scale(1.05);
        }
        
        .program-category {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent-gold);
            color: var(--primary-blue);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .program-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .program-title {
            font-size: 1.3rem;
            color: var(--primary-blue);
            margin-bottom: 15px;
            font-weight: 700;
            line-height: 1.4;
        }
        
        .program-description {
            color: #666;
            margin-bottom: 20px;
            flex-grow: 1;
            line-height: 1.6;
        }
        
        .program-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .detail-item i {
            color: var(--accent-gold);
        }
        
        .program-link, .btn-details {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white !important;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        
        .program-link:hover, .btn-details:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(26, 54, 93, 0.2);
            color: white;
        }
        
        .btn-prospectus {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white !important;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .btn-prospectus:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(40, 167, 69, 0.3);
            color: white;
        }
        
        .program-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .program-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .program-meta i {
            color: var(--accent-gold);
        }
        
        /* Degree Level Section */
        .degree-section {
            padding: 80px 0;
            background-color: var(--light-gray);
        }
        
        .degree-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
            height: 100%;
        }
        
        .degree-card:hover {
            transform: translateY(-5px);
        }
        
        .degree-icon {
            font-size: 3rem;
            color: var(--accent-gold);
            margin-bottom: 20px;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(rgba(26, 54, 93, 0.9), rgba(26, 54, 93, 0.9)), 
                        url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            text-align: center;
        }
        
        .btn-primary-custom {
            background-color: var(--accent-gold);
            border-color: var(--accent-gold);
            color: var(--primary-blue);
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
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
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-custom:hover {
            background-color: white;
            color: var(--primary-blue);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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
        
        /* Program Modal */
        .program-modal .modal-content {
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .program-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            border-bottom: 3px solid var(--accent-gold);
            padding: 25px 30px;
        }
        
        .program-modal .modal-body {
            padding: 30px;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .program-modal .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-text h1 {
                font-size: 3rem;
            }
            
            .program-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .hero-banner {
                height: 50vh;
                min-height: 350px;
            }
            
            .hero-text h1 {
                font-size: 2.5rem;
            }
            
            .filter-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .filter-btn {
                width: 200px;
            }
            
            .program-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .hero-text h1 {
                font-size: 2rem;
            }
            
            .program-content {
                padding: 20px;
            }
            
            .degree-card {
                padding: 25px;
            }
            
            .user-type-selector {
                flex-direction: column;
            }
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
        
        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--accent-gold), #e6c158);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
            font-size: 1.2rem;
            cursor: pointer;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        
        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }
    </style>
</head>
<body>
    <!-- Navigation Container (loaded dynamically) -->
    <div id="nav-container"></div>
    
    <!-- Modal Container (loaded dynamically) -->
    <div id="modal-container"></div>

    <!-- Hero Banner -->
    <section class="hero-banner" data-aos="fade-up">
        <div class="hero-text">
            <h1>ACADEMIC PROGRAMS</h1>
            <p>Discover our comprehensive range of degree programs designed for future success</p>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="programs-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title" data-aos="fade-up">Our Academic Offerings</h2>
                    <p class="intro" data-aos="fade-up" data-aos-delay="100">
                        The university's program offerings are recognized by the Commission on Higher Education (CHED). 
                        These are designed to provide opportunities for students to discover their potentials and enhance 
                        their technical and creative skills in a vibrant academic environment.
                    </p>
                </div>
            </div>

            <!-- Program Filter -->
            <div class="program-filter" data-aos="fade-up" data-aos-delay="200">
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All Programs</button>
                    <button class="filter-btn" data-filter="4-years">4 Years</button>
                    <button class="filter-btn" data-filter="technical">Technical-Vocational</button>
                </div>
            </div>

            <!-- Program Grid - Loaded dynamically from JSON -->
            <div class="program-grid">
                <!-- Programs will be loaded here by programs-loader.js -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading programs...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading programs...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Degree Level Section -->
    <section class="degree-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="section-title" data-aos="fade-up">Degree Levels</h2>
                    <p class="intro" data-aos="fade-up" data-aos-delay="100">
                        Choose the educational path that matches your career goals and aspirations
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="degree-card">
                        <div class="degree-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3>Undergraduate Programs</h3>
                        <p>Bachelor's degree programs designed to provide comprehensive education and prepare students for professional careers or further studies.</p>
                        <ul class="mt-3">
                            <li>4-year duration</li>
                            <li>General Education + Major Courses</li>
                            <li>On-the-Job Training</li>
                            <li>Thesis/Capstone Project</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="degree-card">
                        <div class="degree-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>Graduate Programs</h3>
                        <p>Advanced degree programs for professionals seeking specialization, career advancement, or academic research opportunities.</p>
                        <ul class="mt-3">
                            <li>Master's and Doctoral Degrees</li>
                            <li>Specialized Curriculum</li>
                            <li>Research Intensive</li>
                            <li>Flexible Schedule Options</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="degree-card">
                        <div class="degree-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h3>Technical-Vocational</h3>
                        <p>Skill-based programs focusing on practical training and hands-on experience for immediate employment opportunities.</p>
                        <ul class="mt-3">
                            <li>2-3 Year Programs</li>
                            <li>Hands-on Training</li>
                            <li>Industry Certifications</li>
                            <li>Employment Ready</li>
                        </ul>
                    </div>
                </div>
            </div>
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
                        <a href="../../index.php">Home</a>
                        <a href="about.php">About Us</a>
                        <a href="program.php">Academic Programs</a>
                        <a href="../../view/admission.php">Admissions</a>
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
                        <button class="btn btn-primary-custom" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom text-center">
                <p class="mb-0">&copy; 2023 Colegio De Naujan. All Rights Reserved. | <a href="#" class="text-white">Privacy Policy</a> | <a href="#" class="text-white">Terms of Use</a></p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <div class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </div>

    <!-- Program Modal -->
    <div class="modal fade program-modal" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="programModalLabel">Program Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="programModalBody">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="../../view/admission.php" class="btn btn-primary-custom">Apply Now</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Load Shared Navigation - Load early to prevent white flash -->
    <script src="../../assets/js/load-nav.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Programs Loader -->
    <script src="../../assets/js/programs-loader.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // Enhanced navbar and utility JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar');
            
            // Navbar scroll effect
            window.addEventListener('scroll', function() {
                if (window.scrollY > 100) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
            
            // Back to Top Button
            const backToTop = document.getElementById('backToTop');
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            });
            
            backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
