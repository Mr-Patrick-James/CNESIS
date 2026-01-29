<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Settings - Admin Panel</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    :root {
      --primary-blue: #1a365d;
      --secondary-blue: #2c5282;
      --accent-gold: #d4af37;
      --sidebar-width: 260px;
      --topbar-height: 60px;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
      overflow-x: hidden;
    }
    
    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: var(--sidebar-width);
      background: linear-gradient(180deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
      color: white;
      transition: all 0.3s ease;
      z-index: 1000;
      overflow-y: auto;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    
    .sidebar.collapsed {
      width: 70px;
    }
    
    .sidebar-header {
      padding: 20px;
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .sidebar-header h4 {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 5px;
      transition: opacity 0.3s;
    }
    
    .sidebar-header small {
      font-size: 0.75rem;
      opacity: 0.8;
      transition: opacity 0.3s;
    }
    
    .sidebar-menu {
      padding: 20px 0;
    }
    
    .menu-item {
      padding: 12px 20px;
      color: rgba(255,255,255,0.8);
      text-decoration: none;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      cursor: pointer;
      border-left: 3px solid transparent;
    }
    
    .menu-item:hover {
      background-color: rgba(255,255,255,0.1);
      color: white;
      border-left-color: var(--accent-gold);
    }
    
    .menu-item.active {
      background-color: rgba(255,255,255,0.15);
      color: white;
      border-left-color: var(--accent-gold);
    }
    
    .menu-item i {
      width: 25px;
      font-size: 1.1rem;
      margin-right: 15px;
    }
    
    /* Topbar */
    .topbar {
      position: fixed;
      top: 0;
      left: var(--sidebar-width);
      right: 0;
      height: var(--topbar-height);
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 30px;
      z-index: 999;
      transition: left 0.3s ease;
    }
    
    .sidebar.collapsed ~ .topbar {
      left: 70px;
    }
    
    .topbar-left {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .toggle-btn {
      background: none;
      border: none;
      font-size: 1.3rem;
      color: var(--primary-blue);
      cursor: pointer;
      transition: transform 0.3s;
    }
    
    .toggle-btn:hover {
      transform: scale(1.1);
    }
    
    .topbar-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .admin-profile {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
    }
    
    .admin-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--accent-gold);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
    }
    
    /* Main Content */
    .main-content {
      margin-left: var(--sidebar-width);
      margin-top: var(--topbar-height);
      padding: 30px;
      transition: margin-left 0.3s ease;
      min-height: calc(100vh - var(--topbar-height));
    }
    
    .sidebar.collapsed ~ .main-content {
      margin-left: 70px;
    }
    
    .page-header {
      margin-bottom: 30px;
    }
    
    .page-header h2 {
      color: var(--primary-blue);
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .breadcrumb {
      background: none;
      padding: 0;
      margin: 0;
    }
    
    /* Content Card */
    .content-card {
      background: white;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      margin-bottom: 20px;
    }
    
    .content-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f0f0f0;
    }
    
    .content-card-header h5 {
      color: var(--primary-blue);
      font-weight: 600;
      margin: 0;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
      }
      
      .sidebar-header h4,
      .sidebar-header small,
      .menu-item span {
        display: none;
      }
      
      .topbar {
        left: 70px;
      }
      
      .main-content {
        margin-left: 70px;
        padding: 15px;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <i class="fas fa-graduation-cap" style="font-size: 2rem; color: var(--accent-gold);"></i>
      <h4>COLEGIO DE NAUJAN</h4>
      <small>Admin Portal</small>
    </div>
    
    <div class="sidebar-menu">
      <a class="menu-item" href="dashboard.php">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
      </a>
      <a class="menu-item" href="students.php">
        <i class="fas fa-user-graduate"></i>
        <span>Students</span>
      </a>
      <a class="menu-item" href="program-heads.php">
        <i class="fas fa-chalkboard-teacher"></i>
        <span>Program Heads</span>
      </a>
      <a class="menu-item" href="admissions.php">
        <i class="fas fa-file-alt"></i>
        <span>Admissions</span>
      </a>
      <a class="menu-item" href="programs.php">
        <i class="fas fa-book"></i>
        <span>Programs</span>
      </a>
      <a class="menu-item" href="reports.php">
        <i class="fas fa-chart-bar"></i>
        <span>Reports</span>
      </a>
      <a class="menu-item active" href="settings.php">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
      <a class="menu-item" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>
  
  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-left">
      <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
      </button>
      <h5 style="margin: 0; color: var(--primary-blue);">System Settings</h5>
    </div>
    
    <div class="topbar-right">
      <div class="admin-profile">
        <div class="admin-avatar">AD</div>
        <div>
          <div style="font-weight: 600; font-size: 0.9rem;">Admin User</div>
          <div style="font-size: 0.75rem; color: #6c757d;">Administrator</div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <div class="main-content">
    <div class="page-header">
      <h2>System Settings</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Settings</li>
        </ol>
      </nav>
    </div>
    
    <div class="content-card">
      <div class="content-card-header">
        <h5>General Settings</h5>
        <button class="btn btn-primary btn-sm" onclick="saveGeneralSettings()">Save Changes</button>
      </div>
      
      <form id="generalSettingsForm">
        <div id="generalSettingsFields">
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading settings...</p>
          </div>
        </div>
      </form>
    </div>
    
    <div class="content-card mt-4">
      <div class="content-card-header">
        <h5>Home Page Video</h5>
        <button class="btn btn-secondary btn-sm" onclick="document.getElementById('videoUpload').click()">Change Video</button>
      </div>
      
      <div class="row">
        <div class="col-md-8">
          <div class="mb-3">
            <label class="form-label">Current Video</label>
            <div id="currentVideoInfo">
              <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <span class="ms-2">Loading video info...</span>
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Upload New Video</label>
            <input type="file" class="form-control" id="videoUpload" accept="video/mp4,video/webm,video/ogg" style="display: none;" onchange="handleVideoUpload(this)">
            <div class="text-muted small">
              Accepted formats: MP4, WebM, OGG (Max size: 50MB)
            </div>
            <div id="uploadProgress" class="mt-2" style="display: none;">
              <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Video Preview</label>
            <video id="videoPreview" controls style="width: 100%; max-height: 200px; display: none;">
              Your browser does not support the video tag.
            </video>
            <div id="videoPlaceholder" class="text-center py-4 bg-light rounded">
              <i class="fas fa-video fa-2x text-muted"></i>
              <p class="mb-0 mt-2 text-muted">No video loaded</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="content-card mt-4">
      <div class="content-card-header">
        <h5>Account Settings</h5>
        <button class="btn btn-primary btn-sm" onclick="saveAccountSettings()">Update Account</button>
      </div>
      
      <form id="accountSettingsForm">
        <div id="accountSettingsFields">
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading account settings...</p>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Load Settings Data
    function loadSettings() {
      fetch('../../../api/settings/system-settings.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            renderSettings(data.settings);
            loadVideoInfo();
          } else {
            console.error('Error loading settings:', data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
    
    // Render Settings Fields
    function renderSettings(settings) {
      const generalSettings = settings.filter(s => s.group === 'general');
      const accountSettings = settings.filter(s => s.group === 'account');
      
      renderSettingsGroup(generalSettings, 'generalSettingsFields');
      renderSettingsGroup(accountSettings, 'accountSettingsFields');
    }
    
    // Render Settings Group
    function renderSettingsGroup(settings, containerId) {
      const container = document.getElementById(containerId);
      let html = '<div class="row">';
      
      settings.forEach(setting => {
        const fieldHtml = createSettingField(setting);
        html += fieldHtml;
      });
      
      html += '</div>';
      container.innerHTML = html;
    }
    
    // Create Setting Field HTML
    function createSettingField(setting) {
      let fieldHtml = '';
      
      switch (setting.type) {
        case 'textarea':
          fieldHtml = `
            <div class="col-md-12 mb-3">
              <label class="form-label">${setting.label}${setting.required ? ' *' : ''}</label>
              <textarea class="form-control" id="setting_${setting.key}" rows="3" ${setting.required ? 'required' : ''}>${setting.value}</textarea>
              ${setting.description ? `<small class="text-muted">${setting.description}</small>` : ''}
            </div>
          `;
          break;
        case 'select':
          fieldHtml = `
            <div class="col-md-6 mb-3">
              <label class="form-label">${setting.label}${setting.required ? ' *' : ''}</label>
              <select class="form-select" id="setting_${setting.key}" ${setting.required ? 'required' : ''}>
                <option value="2025-2026" ${setting.value === '2025-2026' ? 'selected' : ''}>2025-2026</option>
                <option value="2026-2027" ${setting.value === '2026-2027' ? 'selected' : ''}>2026-2027</option>
                <option value="2027-2028" ${setting.value === '2027-2028' ? 'selected' : ''}>2027-2028</option>
              </select>
              ${setting.description ? `<small class="text-muted">${setting.description}</small>` : ''}
            </div>
          `;
          break;
        default:
          const inputType = setting.type === 'email' ? 'email' : setting.type === 'phone' ? 'tel' : 'text';
          fieldHtml = `
            <div class="col-md-6 mb-3">
              <label class="form-label">${setting.label}${setting.required ? ' *' : ''}</label>
              <input type="${inputType}" class="form-control" id="setting_${setting.key}" value="${setting.value}" ${setting.required ? 'required' : ''}>
              ${setting.description ? `<small class="text-muted">${setting.description}</small>` : ''}
            </div>
          `;
      }
      
      return fieldHtml;
    }
    
    // Save General Settings
    function saveGeneralSettings() {
      const settings = [];
      const generalFields = document.querySelectorAll('#generalSettingsFields [id^="setting_"]');
      
      generalFields.forEach(field => {
        const key = field.id.replace('setting_', '');
        const value = field.value;
        settings.push({ key, value });
      });
      
      updateSettings(settings, 'General settings saved successfully!');
    }
    
    // Save Account Settings
    function saveAccountSettings() {
      const settings = [];
      const accountFields = document.querySelectorAll('#accountSettingsFields [id^="setting_"]');
      
      accountFields.forEach(field => {
        const key = field.id.replace('setting_', '');
        const value = field.value;
        settings.push({ key, value });
      });
      
      updateSettings(settings, 'Account settings updated successfully!');
    }
    
    // Update Settings API
    function updateSettings(settings, successMessage) {
      fetch('../../../api/settings/system-settings.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ settings: settings })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(successMessage);
        } else {
          alert('Error saving settings: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error saving settings. Please try again.');
      });
    }
    
    // Load Video Info
    function loadVideoInfo() {
      fetch('../../../api/settings/system-settings.php?group=media')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const videoSetting = data.settings.find(s => s.key === 'home_video');
            if (videoSetting) {
              updateVideoInfo(videoSetting.value);
            }
          }
        })
        .catch(error => {
          console.error('Error loading video info:', error);
        });
    }
    
    // Update Video Info Display
    function updateVideoInfo(videoPath) {
      const videoInfo = document.getElementById('currentVideoInfo');
      const videoPreview = document.getElementById('videoPreview');
      const videoPlaceholder = document.getElementById('videoPlaceholder');
      
      if (videoPath) {
        videoInfo.innerHTML = `
          <div class="d-flex align-items-center">
            <i class="fas fa-video text-success me-2"></i>
            <span>${videoPath}</span>
          </div>
        `;
        
        videoPreview.src = '../../../' + videoPath;
        videoPreview.style.display = 'block';
        videoPlaceholder.style.display = 'none';
      } else {
        videoInfo.innerHTML = `
          <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
            <span>No video set</span>
          </div>
        `;
        videoPreview.style.display = 'none';
        videoPlaceholder.style.display = 'block';
      }
    }
    
    // Handle Video Upload
    function handleVideoUpload(input) {
      const file = input.files[0];
      if (!file) return;
      
      // Validate file
      const maxSize = 50 * 1024 * 1024; // 50MB
      const allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];
      
      if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Only MP4, WebM, and OGG videos are allowed.');
        input.value = '';
        return;
      }
      
      if (file.size > maxSize) {
        alert('File too large. Maximum size is 50MB.');
        input.value = '';
        return;
      }
      
      // Show progress
      const progressDiv = document.getElementById('uploadProgress');
      const progressBar = progressDiv.querySelector('.progress-bar');
      progressDiv.style.display = 'block';
      
      // Create FormData
      const formData = new FormData();
      formData.append('video', file);
      
      // Upload with progress
      const xhr = new XMLHttpRequest();
      
      xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
          const percentComplete = (e.loaded / e.total) * 100;
          progressBar.style.width = percentComplete + '%';
        }
      });
      
      xhr.addEventListener('load', () => {
        progressDiv.style.display = 'none';
        progressBar.style.width = '0%';
        
        try {
          const response = JSON.parse(xhr.responseText);
          if (response.success) {
            alert('Video uploaded successfully!');
            updateVideoInfo(response.video_path);
            input.value = '';
          } else {
            alert('Error uploading video: ' + response.message);
          }
        } catch (e) {
          alert('Error uploading video. Please try again.');
        }
      });
      
      xhr.addEventListener('error', () => {
        progressDiv.style.display = 'none';
        progressBar.style.width = '0%';
        alert('Error uploading video. Please try again.');
      });
      
      xhr.open('POST', '../../../api/settings/upload-video.php');
      xhr.send(formData);
    }
    
    // Toggle Sidebar
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('collapsed');
    }
    
    // Logout Function
    function logout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../../../index.php';
      }
    }
    
    // Auto-collapse sidebar on mobile
    if (window.innerWidth <= 768) {
      document.getElementById('sidebar').classList.add('collapsed');
    }
    
    // Load settings when page loads
    document.addEventListener('DOMContentLoaded', loadSettings);
  </script>
</body>
</html>
