// Admission Page JavaScript
(function() {
    'use strict';
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS animations
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 1000,
                once: true,
                offset: 100
            });
        }
        
        // Initialize form handling
        initInquiryForm();
        
        // Initialize section navigation
        initSectionNavigation();
        
        // Initialize smooth scrolling
        initSmoothScrolling();
        
        // Handle URL hash on page load
        handleInitialHash();

        // Handle student type from URL parameter
        handleStudentType();
    });

    /**
     * Handle Student Type from URL Parameter
     */
    function handleStudentType() {
        const urlParams = new URLSearchParams(window.location.search);
        const type = urlParams.get('type');
        
        // Hide all student type sections first
        const sections = document.querySelectorAll('.student-type-section');
        sections.forEach(section => section.style.display = 'none');
        
        if (type === 'existing') {
            const existingSec = document.getElementById('existing-requirements');
            if (existingSec) existingSec.style.display = 'block';
        } else if (type === 'new') {
            const newSec = document.getElementById('new-requirements');
            if (newSec) newSec.style.display = 'block';
        } else {
            // Default to freshman or if type=freshman
            const freshmanSec = document.getElementById('freshman-requirements');
            if (freshmanSec) freshmanSec.style.display = 'block';
        }
    }
    
    /**
     * Initialize Inquiry Form
     */
    function initInquiryForm() {
        const inquiryForm = document.getElementById('inquiry-form');
        
        if (!inquiryForm) return;
        
        inquiryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const fullName = document.getElementById('fullName').value.trim();
            const email = document.getElementById('email').value.trim();
            const program = document.getElementById('program').value.trim();
            const question = document.getElementById('question').value.trim();
            
            // Validate form
            if (!fullName || !email || !program || !question) {
                showAlert('Please fill in all fields', 'danger');
                return;
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('Please enter a valid email address', 'danger');
                return;
            }
            
            // Show loading state
            const submitBtn = inquiryForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Submitting...';
            submitBtn.disabled = true;
            
            // Submit to inquiry API
            submitInquiry(fullName, email, program, question)
                .then(response => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    
                    if (response.success) {
                        // Show success message
                        showAlert('Thank you for your inquiry! We have received your question and will respond within 24-48 hours. Your Inquiry ID is: ' + (response.inquiry_id || 'N/A'), 'success');
                        
                        // Reset form
                        inquiryForm.reset();
                        
                        // Scroll to top of form
                        inquiryForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        showAlert('Error submitting inquiry: ' + response.message, 'danger');
                    }
                })
                .catch(error => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    
                    showAlert('Error submitting inquiry. Please try again.', 'danger');
                    console.error('Submission error:', error);
                });
        });
    }
    
    /**
     * Submit Inquiry to Inquiry API
     * @param {string} fullName - Full name
     * @param {string} email - Email address
     * @param {string} program - Program ID
     * @param {string} question - Question/Inquiry text
     * @returns {Promise} API response
     */
    function submitInquiry(fullName, email, program, question) {
        // Prepare inquiry data
        const inquiryData = {
            fullName: fullName,
            email: email,
            program: program, // Program ID
            question: question,
            inquiryType: 'admission' // Mark as admission-related inquiry
        };
        
        // Send to Simple Inquiry API
        return fetch('../../api/inquiries/create_simple.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(inquiryData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Inquiry submission response:', data);
            return data;
        })
        .catch(error => {
            console.error('API Error:', error);
            throw error;
        });
    }
    
    /**
     * Initialize Section Navigation
     * Handles clicking on section links to show/hide content
     */
    function initSectionNavigation() {
        const sectionLinks = document.querySelectorAll('[data-section]');
        const sections = document.querySelectorAll('.elementor-section');
        
        sectionLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetSection = this.getAttribute('data-section');
                const targetId = `sec-${targetSection}`;
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    // Hide all sections
                    sections.forEach(section => {
                        section.classList.remove('active');
                    });
                    
                    // Show target section
                    targetElement.classList.add('active');
                    
                    // Smooth scroll to section
                    setTimeout(() => {
                        const elementTop = targetElement.getBoundingClientRect().top + window.pageYOffset;
                        const offset = 100; // Account for fixed navbar
                        window.scrollTo({
                            top: elementTop - offset,
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            });
        });
    }
    
    /**
     * Initialize Smooth Scrolling for Anchor Links
     */
    function initSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Skip empty hash or just #
                if (href === '#' || href === '') return;
                
                const targetElement = document.querySelector(href);
                
                if (targetElement) {
                    e.preventDefault();
                    
                    // If it's a section link, trigger section navigation
                    const sectionId = href.replace('#', '');
                    const sectionLink = document.querySelector(`[data-section="${sectionId.replace('sec-', '')}"]`);
                    
                    if (sectionLink) {
                        sectionLink.click();
                    } else {
                        // Regular smooth scroll
                        const elementTop = targetElement.getBoundingClientRect().top + window.pageYOffset;
                        const offset = 100;
                        window.scrollTo({
                            top: elementTop - offset,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    }
    
    /**
     * Handle Initial Hash on Page Load
     * If page loads with a hash, show the corresponding section
     */
    function handleInitialHash() {
        if (window.location.hash) {
            const hash = window.location.hash.replace('#', '');
            
            // Check if it's a section
            if (hash.startsWith('sec-')) {
                const targetSection = document.getElementById(hash);
                if (targetSection) {
                    // Hide all sections first
                    document.querySelectorAll('.elementor-section').forEach(section => {
                        section.classList.remove('active');
                    });
                    
                    // Show target section
                    targetSection.classList.add('active');
                    
                    // Scroll to section after a short delay
                    setTimeout(() => {
                        const elementTop = targetSection.getBoundingClientRect().top + window.pageYOffset;
                        const offset = 100;
                        window.scrollTo({
                            top: elementTop - offset,
                            behavior: 'smooth'
                        });
                    }, 300);
                }
            }
        }
    }
    
    /**
     * Show Alert Message
     * @param {string} message - Alert message
     * @param {string} type - Alert type (success, danger, warning, info)
     */
    function showAlert(message, type = 'info') {
        // Remove existing alerts
        const existingAlert = document.querySelector('.admission-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show admission-alert`;
        alertDiv.style.cssText = `
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            min-width: 300px;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-radius: 8px;
            animation: slideDown 0.3s ease-out;
        `;
        alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${type === 'success' ? 'check-circle-fill' : type === 'danger' ? 'exclamation-triangle-fill' : 'info-circle-fill'} me-2"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Add animation keyframes if not already added
        if (!document.querySelector('#alert-animations')) {
            const style = document.createElement('style');
            style.id = 'alert-animations';
            style.textContent = `
                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateX(-50%) translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(-50%) translateY(0);
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Append to body
        document.body.appendChild(alertDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
                bsAlert.close();
            }
        }, 5000);
    }
    
    /**
     * Navbar Scroll Effect
     */
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    });
    
})();
