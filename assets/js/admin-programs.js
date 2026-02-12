/**
 * Admin Programs Management
 * Handles CRUD operations for programs in admin dashboard
 */

let allPrograms = [];
let programModal;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap modal
    const modalElement = document.getElementById('programModal');
    if (modalElement) {
        programModal = new bootstrap.Modal(modalElement);
    }
    
    // Add event listeners for filters
    const searchInput = document.getElementById('searchPrograms');
    const categoryFilter = document.getElementById('filterCategory');
    const statusFilter = document.getElementById('filterStatus');
    
    if (searchInput) searchInput.addEventListener('input', filterPrograms);
    if (categoryFilter) categoryFilter.addEventListener('change', filterPrograms);
    if (statusFilter) statusFilter.addEventListener('change', filterPrograms);
    
    // Load programs initially if table exists
    if (document.getElementById('programsTableBody')) {
        console.log('Admin programs: Initializing...');
        loadPrograms();
    }
});

// Also expose function to load programs when section is switched
window.loadProgramsSection = function() {
    console.log('Admin programs: Loading programs section...');
    loadPrograms();
};

/**
 * Load all programs from API
 */
async function loadPrograms() {
    console.log('Admin programs: Starting loadPrograms()...');
    const tbody = document.getElementById('programsTableBody');
    
    if (!tbody) {
        console.error('Admin programs: Table body not found!');
        return;
    }
    
    try {
        const apiUrl = 'http://localhost/CNESIS/api/programs/get-all.php';
        console.log('Admin programs: Fetching from:', apiUrl);
        
        const response = await fetch(apiUrl);
        console.log('Admin programs: Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Admin programs: Data received:', data);
        
        if (data.success) {
            allPrograms = data.programs;
            console.log('Admin programs: Programs loaded:', allPrograms.length);
            renderProgramsTable(allPrograms);
        } else {
            console.error('Admin programs: API returned error:', data.message);
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        Failed to load programs: ${data.message}
                    </td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Admin programs: Error loading programs:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4 text-danger">
                    Error loading programs: ${error.message}
                    <br><small>Check console for details</small>
                </td>
            </tr>
        `;
    }
}

/**
 * Render programs table
 */
function renderProgramsTable(programs) {
    const tbody = document.getElementById('programsTableBody');
    
    if (!tbody) return;
    
    if (programs.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    No programs found
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = programs.map(program => `
        <tr>
            <td><strong>${program.code}</strong></td>
            <td>${program.shortTitle}</td>
            <td><span class="badge bg-${program.category === 'undergraduate' ? 'primary' : 'info'}">${program.category}</span></td>
            <td>${program.department}</td>
            <td>${program.duration}</td>
            <td>${program.enrolledStudents}</td>
            <td><span class="badge-status ${program.status}">${program.status}</span></td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="me-2" title="Downloads">
                        <i class="fas fa-download text-info"></i>
                        <span class="ms-1">${program.downloadCount || 0}</span>
                    </div>
                    <div class="flex-grow-1">
                        <button class="action-btn view" onclick="viewProgram(${program.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" onclick="editProgram(${program.id})" title="Edit Program">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" onclick="deleteProgram(${program.id})" title="Delete Program">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Filter programs based on search and filters
 */
function filterPrograms() {
    const searchTerm = document.getElementById('searchPrograms')?.value.toLowerCase() || '';
    const categoryFilter = document.getElementById('filterCategory')?.value || '';
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    
    const filtered = allPrograms.filter(program => {
        const matchesSearch = !searchTerm || 
            program.code.toLowerCase().includes(searchTerm) ||
            program.title.toLowerCase().includes(searchTerm) ||
            program.shortTitle.toLowerCase().includes(searchTerm) ||
            program.department.toLowerCase().includes(searchTerm);
            
        const matchesCategory = !categoryFilter || program.category === categoryFilter;
        const matchesStatus = !statusFilter || program.status === statusFilter;
        
        return matchesSearch && matchesCategory && matchesStatus;
    });
    
    renderProgramsTable(filtered);
}

/**
 * Open Add Program Modal
 */
function openAddProgramModal() {
    document.getElementById('programModalTitle').textContent = 'Add New Program';
    document.getElementById('programForm').reset();
    document.getElementById('programId').value = '';
    
    // Clear dynamic lists
    document.getElementById('highlightsList').innerHTML = '';
    document.getElementById('careersList').innerHTML = '';
    document.getElementById('requirementsList').innerHTML = '';
    
    // Add initial empty fields
    addHighlight();
    addCareer();
    addRequirement();
    
    // Reset file indicators
    document.getElementById('currentImage').textContent = 'None';
    document.getElementById('currentProspectus').textContent = 'None';
    
    programModal.show();
}

/**
 * View Program Details
 */
function viewProgram(id) {
    const program = allPrograms.find(p => p.id === id);
    if (!program) return;
    
    alert(`Program Details:

Code: ${program.code}
Title: ${program.title}
Category: ${program.category}
Department: ${program.department}
Duration: ${program.duration}
Units: ${program.units}
Enrolled: ${program.enrolledStudents}
Status: ${program.status}

Description:
${program.description}`);
}

/**
 * Edit Program
 */
async function editProgram(id) {
    const program = allPrograms.find(p => p.id === id);
    if (!program) return;
    
    document.getElementById('programModalTitle').textContent = 'Edit Program';
    document.getElementById('programId').value = program.id;
    document.getElementById('programCode').value = program.code;
    document.getElementById('shortTitle').value = program.shortTitle;
    document.getElementById('programTitle').value = program.title;
    document.getElementById('category').value = program.category;
    document.getElementById('department').value = program.department;
    document.getElementById('duration').value = program.duration;
    document.getElementById('units').value = program.units;
    document.getElementById('enrolledStudents').value = program.enrolledStudents;
    document.getElementById('description').value = program.description;
    document.getElementById('status').value = program.status;
    
    // Set current files
    document.getElementById('currentImage').textContent = program.image || 'None';
    document.getElementById('currentProspectus').textContent = program.prospectus || 'None';
    
    // Populate dynamic lists
    populateList('highlightsList', program.highlights, 'highlight');
    populateList('careersList', program.careerOpportunities, 'career');
    populateList('requirementsList', program.admissionRequirements, 'requirement');
    
    programModal.show();
}

/**
 * Delete Program
 */
async function deleteProgram(id) {
    const program = allPrograms.find(p => p.id === id);
    if (!program) return;
    
    if (!confirm(`Are you sure you want to delete "${program.shortTitle}"?\n\nThis will set the program status to inactive.`)) {
        return;
    }
    
    try {
        const response = await fetch('http://localhost/CNESIS/api/programs/delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Program deleted successfully!');
            loadPrograms();
        } else {
            showError('Failed to delete program: ' + data.message);
        }
    } catch (error) {
        console.error('Error deleting program:', error);
        showError('Error deleting program. Please try again.');
    }
}

/**
 * Save Program (Add or Update)
 */
async function saveProgram() {
    const programId = document.getElementById('programId').value;
    const isEdit = programId !== '';
    
    // Collect form data
    const formData = {
        code: document.getElementById('programCode').value,
        short_title: document.getElementById('shortTitle').value,
        title: document.getElementById('programTitle').value,
        category: document.getElementById('category').value,
        department: document.getElementById('department').value,
        duration: document.getElementById('duration').value,
        units: document.getElementById('units').value,
        enrolled_students: parseInt(document.getElementById('enrolledStudents').value) || 0,
        description: document.getElementById('description').value,
        status: document.getElementById('status').value,
        highlights: getListValues('highlight'),
        career_opportunities: getListValues('career'),
        admission_requirements: getListValues('requirement')
    };
    
    if (isEdit) {
        formData.id = programId;
    }
    
    // Validate
    if (!formData.code || !formData.title || !formData.category) {
        showError('Please fill in all required fields');
        return;
    }
    
    try {
        // Save basic program data
        const endpoint = isEdit ? 
            'http://localhost/CNESIS/api/programs/update.php' : 
            'http://localhost/CNESIS/api/programs/create.php';
            
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Handle file uploads if any
            await handleFileUploads(data.program_id || programId);
            
            showSuccess(isEdit ? 'Program updated successfully!' : 'Program created successfully!');
            programModal.hide();
            loadPrograms();
        } else {
            showError('Failed to save program: ' + data.message);
        }
    } catch (error) {
        console.error('Error saving program:', error);
        showError('Error saving program. Please try again.');
    }
}

/**
 * Handle file uploads
 */
async function handleFileUploads(programId) {
    const imageFile = document.getElementById('programImage').files[0];
    const prospectusFile = document.getElementById('prospectusFile').files[0];
    
    // Upload image
    if (imageFile) {
        const imageFormData = new FormData();
        imageFormData.append('image', imageFile);
        imageFormData.append('program_id', programId);
        
        await fetch('http://localhost/CNESIS/api/programs/upload-image.php', {
            method: 'POST',
            body: imageFormData
        });
    }
    
    // Upload prospectus
    if (prospectusFile) {
        const prospectusFormData = new FormData();
        prospectusFormData.append('prospectus', prospectusFile);
        prospectusFormData.append('program_id', programId);
        
        await fetch('http://localhost/CNESIS/api/programs/upload-prospectus.php', {
            method: 'POST',
            body: prospectusFormData
        });
    }
}

/**
 * Add highlight field
 */
function addHighlight() {
    addListItem('highlightsList', 'highlight', 'Enter program highlight');
}

/**
 * Add career field
 */
function addCareer() {
    addListItem('careersList', 'career', 'Enter career opportunity');
}

/**
 * Add requirement field
 */
function addRequirement() {
    addListItem('requirementsList', 'requirement', 'Enter admission requirement');
}

/**
 * Add list item helper
 */
function addListItem(containerId, name, placeholder) {
    const container = document.getElementById(containerId);
    const index = container.children.length;
    
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="${name}[]" placeholder="${placeholder}">
        <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(div);
}

/**
 * Populate list helper
 */
function populateList(containerId, items, name) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    
    if (items && items.length > 0) {
        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="text" class="form-control" name="${name}[]" value="${item}">
                <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(div);
        });
    } else {
        addListItem(containerId, name, `Enter ${name}`);
    }
}

/**
 * Get list values helper
 */
function getListValues(name) {
    const inputs = document.querySelectorAll(`input[name="${name}[]"]`);
    return Array.from(inputs)
        .map(input => input.value.trim())
        .filter(value => value !== '');
}

/**
 * Show success message
 */
function showSuccess(message) {
    alert('✓ ' + message);
}

/**
 * Show error message
 */
function showError(message) {
    alert('✗ ' + message);
}
