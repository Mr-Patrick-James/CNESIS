// Load Navigation Header
(function() {
    'use strict';
    
    // Determine the base path based on current page location
    function getBasePath() {
        const path = window.location.pathname;
        if (path.includes('views/user/')) {
            return '../../assets/js/nav-header.html';
        } else if (path.includes('view/')) {
            return '../assets/js/nav-header.html';
        } else {
            return 'assets/js/nav-header.html';
        }
    }
    
    // Get current page identifier
    function getCurrentPage() {
        const path = window.location.pathname;
        if (path.includes('about.php')) return 'about';
        if (path.includes('program.php')) return 'program';
        if (path.includes('admission.php')) return 'admission';
        if (path.includes('handbook.php')) return 'handbook';
        if (path.includes('index.php') || path.endsWith('/')) return 'home';
        return '';
    }
    
    // Load navigation
    async function loadNavigation() {
        const navPath = getBasePath();
        const navContainer = document.getElementById('nav-container');
        const modalContainer = document.getElementById('modal-container');
        
        if (!navContainer || !modalContainer) {
            console.error('Navigation containers not found');
            return;
        }
        
        // Set background immediately to prevent white flash
        navContainer.style.backgroundColor = 'rgba(26, 54, 93, 0.98)';
        navContainer.style.minHeight = '76px';
        navContainer.style.position = 'relative';
        navContainer.style.zIndex = '1030';
        
        try {
            const response = await fetch(navPath);
            if (!response.ok) throw new Error('Failed to load navigation');
            
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Extract navigation
            const nav = doc.querySelector('nav');
            if (nav) {
                navContainer.appendChild(nav);
                
                // Adjust navigation paths based on current page location
                adjustNavigationPaths(navContainer);
            }
            
            // Extract modal
            const modal = doc.querySelector('.modal');
            if (modal) {
                modalContainer.appendChild(modal);
            }
            
            // Set active nav link based on current page
            const currentPage = getCurrentPage();
            if (currentPage) {
                const activeLink = navContainer.querySelector(`[data-page="${currentPage}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }
            
            // Initialize navigation functionality
            initNavigation();
            initLoginModal();
            
            // Remove background color once navbar is loaded (navbar has its own background)
            navContainer.style.backgroundColor = 'transparent';
            
        } catch (error) {
            console.error('Error loading navigation:', error);
            navContainer.style.backgroundColor = 'transparent';
        }
    }
    
    // Initialize navigation functionality
    function initNavigation() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;
        
        // Prevent duplicate initialization
        if (navbar.dataset.initialized === 'true') return;
        navbar.dataset.initialized = 'true';
        
        // Scroll effect - combine both scroll handlers into one
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) {
                window.cancelAnimationFrame(scrollTimeout);
            }
            scrollTimeout = window.requestAnimationFrame(function() {
                if (window.scrollY > 100) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
                
                if (window.scrollY > 50) {
                    navbar.style.boxShadow = '0 6px 25px rgba(0, 0, 0, 0.2)';
                } else {
                    navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
                }
            });
        }, { passive: true });
        
        // Smooth animation for active state
        const navLinks = navbar.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            // Check if listener already exists
            if (link.dataset.listenerAdded === 'true') return;
            link.dataset.listenerAdded = 'true';
            
            link.addEventListener('click', function(e) {
                // Remove active class from all links
                navLinks.forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Close mobile menu if open
                if (window.innerWidth < 992) {
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                        bsCollapse.hide();
                    }
                }
            });
        });
    }
    
    // Initialize login modal functionality
    function initLoginModal() {
        const loginModal = document.getElementById('loginModal');
        if (!loginModal) return;
        
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
        
        // Form submission
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const usernameInput = document.getElementById('username');
                const passwordInput = document.getElementById('password');
                const username = usernameInput.value;
                const password = passwordInput.value;
                
                // Simple validation
                if (!username || !password) {
                    alert('Please fill in all fields');
                    return;
                }
                
                // Show loading state
                const submitBtn = loginForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
                submitBtn.disabled = true;
                
                try {
                    // Determine API path
                    let apiPath = 'api/auth/login.php';
                    const path = window.location.pathname;
                    if (path.includes('/views/user/')) {
                        apiPath = '../../api/auth/login.php';
                    } else if (path.includes('/view/')) {
                        apiPath = '../api/auth/login.php';
                    }
                    
                    const response = await fetch(apiPath, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ 
                            username: username, 
                            password: password 
                        })
                    });
                    
                    const data = await response.json();
                    
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(loginModal);
                        modal.hide();
                        
                        // Show login success notification
                        const successNotification = document.createElement('div');
                        successNotification.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 z-3';
                        successNotification.style.minWidth = '300px';
                        successNotification.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Login successful!</strong> Redirecting...
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        
                        document.body.appendChild(successNotification);
                        
                        // Redirect
                        setTimeout(() => {
                            successNotification.remove();
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        }, 1500);
                        
                    } else {
                        // Show error message
                        const modalBody = loginModal.querySelector('.modal-body');
                        const errorAlert = document.createElement('div');
                        errorAlert.className = 'alert alert-danger alert-dismissible fade show';
                        errorAlert.innerHTML = `
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Login failed!</strong> ${data.message || 'Invalid credentials.'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        
                        const existingAlert = modalBody.querySelector('.alert');
                        if (existingAlert) existingAlert.remove();
                        
                        modalBody.insertBefore(errorAlert, modalBody.firstChild);
                        
                        // Auto-close after 5 seconds
                        setTimeout(() => {
                            const bsAlert = new bootstrap.Alert(errorAlert);
                            bsAlert.close();
                        }, 5000);
                    }
                    
                } catch (error) {
                    console.error('Login error:', error);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    alert('An error occurred during login. Please try again.');
                }
            });
        }
        
        // Reset form when modal is hidden
        loginModal.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('loginForm');
            if (form) form.reset();
        });
    }
    
    /**
     * Adjust Navigation Paths
     * Fixes relative paths in navigation based on current page location
     */
    function adjustNavigationPaths(navContainer) {
        const path = window.location.pathname;
        const navLinks = navContainer.querySelectorAll('a[href]');
        
        navLinks.forEach(link => {
            let href = link.getAttribute('href');
            
            // Skip external links and anchors
            if (href.startsWith('http') || href.startsWith('#') || href.startsWith('mailto:')) {
                return;
            }
            
            // If we're in views/user/ directory (about.php, program.php, admission.php)
            // Check this FIRST before checking view/ to avoid false matches
            if (path.includes('views/user/')) {
                // Paths in nav-header.html are already relative to views/user/, so they should work as-is
                // No adjustment needed - paths like about.php, program.php, admission.php are correct
                // and ../../index.php correctly points to root
                return; // Skip further processing
            }
            // If we're in view/ directory (admission.php) - but NOT views/user/
            else if (path.includes('/view/') && !path.includes('views/')) {
                // Fix paths that are relative to views/user/
                if (href === 'about.php') {
                    link.setAttribute('href', '../views/user/about.php');
                } else if (href === 'program.php') {
                    link.setAttribute('href', '../views/user/program.php');
                } else if (href === 'admission.php') {
                    link.setAttribute('href', '../views/user/admission.php');
                } else if (href === 'handbook.php') {
                    link.setAttribute('href', '../views/user/handbook.php');
                } else if (href === '../../index.php') {
                    link.setAttribute('href', '../index.php');
                } else if (href === '../../view/admission.php') {
                    link.setAttribute('href', 'admission.php');
                } else if (href === '../../view/handbook.pdf') {
                    link.setAttribute('href', 'handbook.pdf');
                }
            }
            // If we're in root (index.php)
            else if (!path.includes('views/') && !path.includes('view/')) {
                // Fix paths that are relative to views/user/
                if (href === 'about.php') {
                    link.setAttribute('href', 'views/user/about.php');
                } else if (href === 'program.php') {
                    link.setAttribute('href', 'views/user/program.php');
                } else if (href === 'admission.php') {
                    link.setAttribute('href', 'views/user/admission.php');
                } else if (href === 'handbook.php') {
                    link.setAttribute('href', 'views/user/handbook.php');
                } else if (href === '../../index.php') {
                    link.setAttribute('href', 'index.php');
                } else if (href === '../../view/admission.php') {
                    link.setAttribute('href', 'view/admission.php');
                } else if (href === '../../view/handbook.pdf') {
                    link.setAttribute('href', 'view/handbook.pdf');
                }
            }
        });
    }
    
    // Load navigation when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadNavigation);
    } else {
        loadNavigation();
    }
})();
