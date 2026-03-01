<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us – Colegio De Naujan</title>

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
                        url('../../assets/img/bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 76px;
        }
        
        .hero-text {
            text-align: center;
            color: white;
            z-index: 2;
            position: relative;
            padding: 0 20px;
        }
        
        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        /* About Section */
        .about-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-title {
            color: var(--primary-blue);
            margin-bottom: 40px;
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
        
        .about-content {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .about-content h2 {
            color: var(--primary-blue);
            margin: 40px 0 20px;
            font-size: 1.8rem;
        }
        
        .about-content h3 {
            color: var(--secondary-blue);
            margin: 30px 0 15px;
            font-size: 1.4rem;
        }
        
        .about-content p {
            line-height: 1.8;
            margin-bottom: 20px;
            color: #555;
            font-size: 1.1rem;
        }
        
        .about-content ul {
            margin: 20px 0 30px 30px;
            color: #555;
        }
        
        .about-content li {
            margin-bottom: 10px;
            line-height: 1.6;
        }
        
        .highlight-box {
            background: linear-gradient(135deg, rgba(26, 54, 93, 0.05), rgba(212, 175, 55, 0.05));
            border-left: 4px solid var(--accent-gold);
            padding: 25px;
            margin: 30px 0;
            border-radius: 0 10px 10px 0;
        }
        
        .highlight-box h4 {
            color: var(--primary-blue);
            margin-bottom: 15px;
        }
        
        /* Timeline Section */
        .timeline-section {
            padding: 80px 0;
            background-color: var(--light-gray);
        }
        
        .timeline {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 100%;
            background: linear-gradient(to bottom, var(--accent-gold), var(--secondary-blue));
        }
        
        .timeline-item {
            margin-bottom: 50px;
            position: relative;
            width: 100%;
        }
        
        .timeline-item:nth-child(odd) .timeline-content {
            margin-left: calc(50% + 30px);
            text-align: left;
        }
        
        .timeline-item:nth-child(even) .timeline-content {
            margin-right: calc(50% + 30px);
            text-align: right;
        }
        
        .timeline-item:nth-child(even) .timeline-content:before {
            right: -15px;
            left: auto;
        }
        
        .timeline-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            position: relative;
        }
        
        .timeline-content:before {
            content: '';
            position: absolute;
            top: 30px;
            left: -15px;
            width: 0;
            height: 0;
            border-top: 15px solid transparent;
            border-bottom: 15px solid transparent;
            border-right: 15px solid white;
        }
        
        .timeline-item:nth-child(even) .timeline-content:before {
            border-right: none;
            border-left: 15px solid white;
            right: -15px;
            left: auto;
        }
        
        .timeline-year {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            background: var(--accent-gold);
            color: var(--primary-blue);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 700;
            z-index: 1;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        
        .timeline-content h4 {
            color: var(--primary-blue);
            margin-bottom: 10px;
        }
        
        /* Core Values Section */
        .values-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .value-card {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .value-icon {
            font-size: 3rem;
            color: var(--accent-gold);
            margin-bottom: 20px;
        }
        
        /* Leadership Section */
        .leadership-section {
            padding: 80px 0;
            background-color: var(--light-gray);
        }
        
        .leader-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .leader-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .leader-image {
            height: 250px;
            overflow: hidden;
        }
        
        .leader-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .leader-card:hover .leader-image img {
            transform: scale(1.05);
        }
        
        .leader-info {
            padding: 25px;
        }
        
        .leader-name {
            color: var(--primary-blue);
            margin-bottom: 5px;
        }
        
        .leader-position {
            color: var(--accent-gold);
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
        }
        
        .stat-box {
            text-align: center;
            padding: 30px 20px;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--accent-gold);
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
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
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-text h1 {
                font-size: 2.8rem;
            }
            
            .timeline:before {
                left: 30px;
            }
            
            .timeline-item:nth-child(odd) .timeline-content,
            .timeline-item:nth-child(even) .timeline-content {
                margin-left: 60px;
                margin-right: 0;
                text-align: left;
            }
            
            .timeline-year {
                left: 30px;
                transform: translateX(0);
            }
            
            .timeline-content:before {
                left: -15px;
                border-right: 15px solid white;
                border-left: none;
            }
            
            .timeline-item:nth-child(even) .timeline-content:before {
                left: -15px;
                right: auto;
                border-right: 15px solid white;
                border-left: none;
            }
        }
        
        @media (max-width: 768px) {
            .hero-banner {
                height: 50vh;
                min-height: 350px;
            }
            
            .hero-text h1 {
                font-size: 2.3rem;
            }
            
            .about-content h2 {
                font-size: 1.6rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero-text h1 {
                font-size: 2rem;
            }
            
            .about-content {
                padding: 0 15px;
            }
            
            .stat-number {
                font-size: 2.5rem;
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
            <h1>ABOUT COLEGIO DE NAUJAN</h1>
            <p class="lead">Excellence in Education Since 2003</p>
        </div>
    </section>

    <!-- About Content Section -->
    <section class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="about-content" data-aos="fade-up">
                        <h2 class="section-title">A. Background of the Institution</h2>
                        
                        <p>
                            Naujan Technical College, through the initiative of then Municipal Mayor, Atty. Norberto M. Mendoza, was established on May 9, 2003 by virtue of Sangguniang Bayan (SB) Resolution No. 1599 series of 2003. It was issued Permit to Operate by Technical Education and Skills Development Authority (TESDA) on June 03, 2003 per certificate of TVET Program Registration No. 034B032060 for Two-Year Welding and Steel Fabrication and One-Year Agricultural Mechanics, both with No Training Regulations (NTR) on its first year of operation, School Year 2003 - 2004.
                        </p>
                        
                        <p>
                            In August 21, 2008, the Two-Year Welding and Steel Fabrication Course was migrated to Shielded Metal Arc Welding (SMAW) NC I with Training Regulation No. 0817031605, Shielded Metal Arc Welding (SMAW) NC II with Training Regulation No. 0817032606 and Gas Tungsten Arc Welding (GTAW) NC II with Training Regulation No. 0817032607, with 268 nominal training hours per qualification.
                        </p>
                        
                        <div class="highlight-box" data-aos="fade-right">
                            <h4><i class="fas fa-landmark me-2"></i> Institutionalization</h4>
                            <p>
                                An SB Ordinance institutionalizing the Operation of Naujan Technical College as Manpower Development Center was passed and approved by the Sangguniang Bayan on October 22, 2018. On June 03, 2020, new Certificates of Program Registration were issued by TESDA to Naujan Technical College for the three (3) qualifications, following the change of the institution's registered address.
                            </p>
                        </div>
                        
                        <p>
                            Throughout the journey of Naujan Technical College and its partnership with TESDA, Malampaya, Provincial Government of Oriental Mindoro and the Local Government of Naujan from 2003 - 2022, the institution produced 1,135 competent graduates.
                        </p>
                        
                        <p>
                            The Clamor to expand the courses/services of NTC by offering tertiary degree programs to further enhance the productivity and employability of the graduates and eventually integrate them in society as productive citizens working collaboratively towards a progressive municipality in this new normal era became evident in the recent years. Through the initiative of Hon. Mark N. Marcos and the Sangguniang Education Committee Chairperson, Hon. Vilma D. Vargas, initial study of converting NTC to Colegio De Naujan and offering degree courses begun. Ordinance Converting Naujan Technical College (NTC) to Colegio De Naujan and Offering Tertiary/Degree Programs”, Shortly referred to as a "Establishment of Colegio De Naujan" was passed and unanimously approved by the Sangguniang Bayan ng Naujan, Oriental Mindoro.
                        </p>
                        
                        <h2>B. Vision and Mission Statement</h2>
                        
                        <div class="highlight-box" data-aos="fade-left">
                            <h4><i class="fas fa-bullseye me-2"></i> Mission</h4>
                            <p>
                                Emboldened and imbued by the mandate and philosophy of higher education, with aspirations, ideals, and a high sense of appreciation on the role of lifelong education, COLEGIO DE NAUJAN:
                            </p>
                            <ul>
                                <li>Aims to produce men and women equipped with adequate and relevant knowledge, skills, and values that will enable them to practice successfully the vocation and profession that is properly matched with manpower requirement of the country and lead quality life in a democratic, just and peaceful, progressive, and God-loving community</li>
                                <li>Prepares individuals for a productive responsible, and empowered citizenry, capable of providing themselves with greater opportunities to survive in a tough and stiff social and economic competition</li>
                                <li>Develops students to participate in the social transformation and nation-building, thus it develops in them the creativity, discipline, ideals, and decisiveness requisite for the total human development to meet the challenges of life ahead</li>
                            </ul>
                        </div>
                        
                        <div class="highlight-box" data-aos="fade-right">
                            <h4><i class="fas fa-eye me-2"></i> Vision</h4>
                            <p>
                                Colegio De Naujan is envisioned as a center of excellence in academic, business, technical education and farming technology to cater to the demands in the competitive market of higher education not only in Oriental Mindoro but nationally and globally in its entirety.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="timeline-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="section-title" data-aos="fade-up">Our Journey Through Time</h2>
                    <p class="lead" data-aos="fade-up" data-aos-delay="100">Milestones in the Development of Colegio De Naujan</p>
                </div>
            </div>
            
            <div class="timeline" data-aos="fade-up">
                <div class="timeline-item">
                    <div class="timeline-year">2003</div>
                    <div class="timeline-content">
                        <h4>Establishment of Naujan Technical College</h4>
                        <p>Founded through SB Resolution No. 1599 with initial programs in Welding and Agricultural Mechanics under TESDA accreditation.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-year">2008</div>
                    <div class="timeline-content">
                        <h4>Program Migration and Expansion</h4>
                        <p>Welding programs migrated to SMAW NC I & II and GTAW NC II with updated training regulations and expanded curriculum.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-year">2018</div>
                    <div class="timeline-content">
                        <h4>Institutionalization as Manpower Development Center</h4>
                        <p>SB Ordinance passed to institutionalize NTC as a comprehensive manpower development center serving the community.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-year">2020</div>
                    <div class="timeline-content">
                        <h4>New Program Registration</h4>
                        <p>Updated Certificates of Program Registration issued by TESDA following relocation to new campus facilities.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-year">2022</div>
                    <div class="timeline-content">
                        <h4>Transition to Colegio De Naujan</h4>
                        <p>Unanimous approval for conversion to Colegio De Naujan with expansion to include tertiary degree programs.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-year">2023</div>
                    <div class="timeline-content">
                        <h4>New Era of Higher Education</h4>
                        <p>Launch of comprehensive undergraduate programs in Information Systems, Technical-Vocational Education, and Public Administration.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values Section -->
    <section class="values-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="section-title" data-aos="fade-up">Our Core Values</h2>
                    <p class="lead" data-aos="fade-up" data-aos-delay="100">Guiding Principles That Define Our Institution</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4>Academic Excellence</h4>
                        <p>Commitment to highest standards of teaching, learning, and research to develop competent professionals.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <h4>Service to Community</h4>
                        <p>Dedication to contributing to community development and addressing local and national needs.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h4>Integrity</h4>
                        <p>Upholding honesty, ethical conduct, and moral principles in all academic and administrative endeavors.</p>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Inclusivity</h4>
                        <p>Providing equal opportunities for all students regardless of background, promoting diversity and equity.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-box">
                        <div class="stat-number">20+</div>
                        <div class="stat-label">Years of Excellence</div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-box">
                        <div class="stat-number">1,135+</div>
                        <div class="stat-label">Competent Graduates</div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-box">
                        <div class="stat-number">15+</div>
                        <div class="stat-label">Academic Programs</div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-box">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Expert Faculty Members</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Section -->
    <section class="leadership-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="section-title" data-aos="fade-up">Our Leadership</h2>
                    <p class="lead" data-aos="fade-up" data-aos-delay="100">Guiding Our Institution Towards Excellence</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="leader-card">
                        <div class="leader-image">
                            <img src="https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="College President">
                        </div>
                        <div class="leader-info">
                            <h4 class="leader-name">Dr. Maria Santos</h4>
                            <p class="leader-position">College President</p>
                            <p>With over 25 years in educational leadership, Dr. Santos has been instrumental in the transition from technical college to comprehensive higher education institution.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="leader-card">
                        <div class="leader-image">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Academic Dean">
                        </div>
                        <div class="leader-info">
                            <h4 class="leader-name">Prof. Juan Dela Cruz</h4>
                            <p class="leader-position">Academic Dean</p>
                            <p>Leading academic excellence initiatives and curriculum development for all degree programs with focus on industry-relevant education.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="leader-card">
                        <div class="leader-image">
                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Founding Mayor">
                        </div>
                        <div class="leader-info">
                            <h4 class="leader-name">Atty. Norberto M. Mendoza</h4>
                            <p class="leader-position">Founding Municipal Mayor</p>
                            <p>Visionary leader who initiated the establishment of the institution in 2003, laying the foundation for quality technical education in Naujan.</p>
                        </div>
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
                    <p class="mt-3">A premier higher education institution committed to academic excellence, innovation, and character formation since 2003.</p>
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
                        <a href="../../view/handbook.php">Student Handbook</a>
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

    <!-- Load Shared Navigation - Load early to prevent white flash -->
    <script src="../../assets/js/load-nav.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
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
        });
    </script>
</body>
</html>