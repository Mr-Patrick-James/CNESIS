/**
 * Programs Loader
 * Dynamically loads and renders academic programs from database via PHP API
 */

class ProgramsLoader {
    constructor() {
        this.programs = [];
        this.currentFilter = 'all';
    }

    /**
     * Load programs data from PHP API
     */
    async loadPrograms() {
        try {
            // Use relative path for API
            const apiUrl = '../../api/programs/get-all.php';
            
            console.log('Fetching programs from:', apiUrl);
            const response = await fetch(apiUrl);
            console.log('API Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('API Error Response:', errorText);
                throw new Error('Failed to load programs data');
            }
            const data = await response.json();
            console.log('API Data received:', data);
            
            if (data.success) {
                this.programs = data.programs;
                console.log('Programs loaded:', this.programs.length);
                return this.programs;
            } else {
                console.error('API Error:', data.message);
                return [];
            }
        } catch (error) {
            console.error('Error loading programs:', error);
            return [];
        }
    }

    /**
     * Render program cards to the page
     */
    renderPrograms(container, filter = 'all') {
        if (!container) {
            console.error('Container element not found');
            return;
        }

        this.currentFilter = filter;
        
        // Filter programs based on category
        const filteredPrograms = filter === 'all' 
            ? this.programs 
            : this.programs.filter(program => {
                if (filter === 'undergraduate') {
                    return program.category === 'undergraduate';
                } else if (filter === 'technical') {
                    return program.category === 'technical';
                }
                return true;
            });

        // Clear container
        container.innerHTML = '';

        // Render each program
        filteredPrograms.forEach((program, index) => {
            const programCard = this.createProgramCard(program, index);
            container.appendChild(programCard);
        });

        // If no programs found
        if (filteredPrograms.length === 0) {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">No programs found in this category.</p></div>';
        }
    }

    /**
     * Create a program card element
     */
    createProgramCard(program, index) {
        const card = document.createElement('div');
        card.className = 'program-card';
        card.setAttribute('data-category', program.category);
        card.setAttribute('data-aos', 'fade-up');
        card.setAttribute('data-aos-delay', (index * 100).toString());

        const categoryLabel = program.category === 'undergraduate' ? 'Undergraduate' : 'Technical-Vocational';
        const prospectusButton = program.prospectus_path 
            ? `<a href="${program.prospectus_path}" class="btn-prospectus" onclick="trackProspectusDownload(${program.id})" download>
                 <i class="fas fa-download"></i> Download Prospectus
               </a>`
            : '';

        card.innerHTML = `
            <div class="program-image">
                <img src="${program.image_path ? 'http://localhost/CNESIS/' + program.image_path.replace('../../', '') : 'http://localhost/CNESIS/assets/img/default-program.jpg'}" alt="${program.short_title}">
                <div class="program-category">${categoryLabel}</div>
            </div>
            <div class="program-content">
                <h3 class="program-title">${program.title}</h3>
                <p class="program-description">${program.description}</p>
                <div class="program-meta">
                    <span><i class="fas fa-clock"></i> ${program.duration}</span>
                    <span><i class="fas fa-book"></i> ${program.units}</span>
                    ${program.program_head_name ? `<span><i class="fas fa-user-tie"></i> ${program.program_head_name}</span>` : ''}
                </div>
                ${prospectusButton}
                <button class="btn-details" data-program-id="${program.id}">
                    View Details <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        `;

        // Add event listener for View Details button
        const detailsBtn = card.querySelector('.btn-details');
        if (detailsBtn) {
            detailsBtn.addEventListener('click', () => {
                this.showProgramDetails(program.id);
            });
        }

        return card;
    }

    /**
     * Show program details in a modal
     */
    showProgramDetails(programId) {
        const program = this.programs.find(p => p.id === programId);
        if (!program) {
            console.error('Program not found:', programId);
            return;
        }

        // Create modal if it doesn't exist
        let modal = document.getElementById('programModal');
        if (!modal) {
            modal = this.createModal();
            document.body.appendChild(modal);
        }

        // Populate modal with program details
        this.populateModal(modal, program);

        // Show modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    /**
     * Create modal element
     */
    createModal() {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'programModal';
        modal.setAttribute('tabindex', '-1');
        modal.setAttribute('aria-labelledby', 'programModalLabel');
        modal.setAttribute('aria-hidden', 'true');

        modal.innerHTML = `
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="programModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="programModalBody">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="admission.php" class="btn btn-primary">Apply Now</a>
                    </div>
                </div>
            </div>
        `;

        return modal;
    }

    /**
     * Populate modal with program details
     */
    populateModal(modal, program) {
        const title = modal.querySelector('#programModalLabel');
        const body = modal.querySelector('#programModalBody');

        title.textContent = program.title;

        const prospectusSection = program.prospectus_path 
            ? `<div class="mb-4">
                 <a href="http://localhost/CNESIS/${program.prospectus_path.replace('../../', '')}" class="btn btn-success w-100" onclick="trackProspectusDownload(${program.id})" download>
                   <i class="fas fa-download me-2"></i> Download Program Prospectus
                 </a>
               </div>`
            : '';

        body.innerHTML = `
            <div class="program-modal-content">
                <!-- Program Header with Image -->
                <div class="position-relative mb-4">
                    <img src="${program.image_path ? 'http://localhost/CNESIS/' + program.image_path.replace('../../', '') : 'http://localhost/CNESIS/assets/img/default-program.jpg'}" alt="${program.title}" class="w-100 rounded" style="height: 200px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                        <span class="badge bg-primary">${program.category === 'undergraduate' ? 'Undergraduate Program' : 'Technical-Vocational Program'}</span>
                    </div>
                </div>

                <!-- Quick Info Cards -->
                <div class="row g-2 mb-4">
                    <div class="col-4">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="fas fa-graduation-cap fs-4 text-primary mb-2"></i>
                            <div class="small text-muted">Program Code</div>
                            <div class="fw-bold">${program.code}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="fas fa-clock fs-4 text-success mb-2"></i>
                            <div class="small text-muted">Duration</div>
                            <div class="fw-bold">${program.duration}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="fas fa-book fs-4 text-warning mb-2"></i>
                            <div class="small text-muted">Total Units</div>
                            <div class="fw-bold">${program.units}</div>
                        </div>
                    </div>
                </div>

                <!-- Program Head Information -->
                ${program.program_head_name ? `
                <div class="row g-2 mb-4">
                    <div class="col-12">
                        <div class="alert alert-info border-0">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-tie fs-4 me-3"></i>
                                <div>
                                    <div class="fw-bold">Program Head</div>
                                    <div class="small text-muted">${program.program_head_name}</div>
                                    ${program.program_head_email ? `<div class="small text-muted">${program.program_head_email}</div>` : ''}
                                    ${program.program_head_phone ? `<div class="small text-muted">${program.program_head_phone}</div>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                ` : ''}

                <!-- Prospectus Download -->
                ${prospectusSection}

                <!-- Program Description -->
                <div class="mb-4">
                    <h6 class="border-start border-4 border-primary ps-3 mb-3">About This Program</h6>
                    <p class="text-muted">${program.description}</p>
                </div>

                <!-- Program Highlights -->
                <div class="mb-4">
                    <h6 class="border-start border-4 border-warning ps-3 mb-3">
                        <i class="fas fa-star text-warning me-2"></i>Program Highlights
                    </h6>
                    <div class="row">
                        ${program.highlights.map(h => `
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                    <span class="small">${h}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <!-- Career Opportunities -->
                <div class="mb-4">
                    <h6 class="border-start border-4 border-primary ps-3 mb-3">
                        <i class="fas fa-briefcase text-primary me-2"></i>Career Opportunities
                    </h6>
                    <div class="row">
                        ${program.career_opportunities.map(c => `
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-arrow-right text-primary me-2 mt-1 small"></i>
                                    <span class="small">${c}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <!-- Admission Requirements -->
                <div class="mb-4">
                    <h6 class="border-start border-4 border-info ps-3 mb-3">
                        <i class="fas fa-clipboard-check text-info me-2"></i>Admission Requirements
                    </h6>
                    <ul class="list-group list-group-flush">
                        ${program.admission_requirements.map(r => `
                            <li class="list-group-item px-0 py-2 border-0">
                                <i class="fas fa-check text-info me-2"></i>${r}
                            </li>
                        `).join('')}
                    </ul>
                </div>

                <!-- Enrollment Info -->
                <div class="alert alert-info border-0 mb-0" style="background-color: #e7f3ff;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold">Currently Enrolled Students</div>
                            <div class="text-muted small">${program.enrolled_students || 0} students are currently enrolled in this program</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Initialize filtering functionality
     */
    initializeFilters() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const container = document.querySelector('.program-grid');

        if (!filterButtons || filterButtons.length === 0) {
            console.warn('Filter buttons not found');
            return;
        }

        if (!container) {
            console.warn('Program grid container not found');
            return;
        }

        filterButtons.forEach(button => {
            if (!button) return;
            
            button.addEventListener('click', () => {
                // Update active state
                filterButtons.forEach(btn => {
                    if (btn && btn.classList) {
                        btn.classList.remove('active');
                    }
                });
                
                if (button.classList) {
                    button.classList.add('active');
                }

                // Get filter value
                const filter = button.getAttribute('data-filter');

                // Render filtered programs
                this.renderPrograms(container, filter);
            });
        });
    }
}

// Create global instance
const programsLoader = new ProgramsLoader();

// Function to track prospectus downloads
function trackProspectusDownload(programId) {
    // Send tracking request to server
    fetch('http://localhost/CNESIS/api/programs/track-prospectus-download.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            program_id: programId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Prospectus download tracked successfully');
        } else {
            console.error('Failed to track download:', data.message);
        }
    })
    .catch(error => {
        console.error('Error tracking download:', error);
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async function() {
    const container = document.querySelector('.program-grid');
    
    if (container) {
        // Load programs data
        await programsLoader.loadPrograms();
        
        // Render all programs initially
        programsLoader.renderPrograms(container, 'all');
        
        // Initialize filter buttons
        programsLoader.initializeFilters();
    }
});
