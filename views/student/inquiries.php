<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../../index.php?error=unauthorized');
    exit;
}

require_once '../../api/config/database.php';
$database = new Database();
$db = $database->getConnection();

$fullName = $_SESSION['full_name'];
$email = $_SESSION['email'];

// Get student details
$stmt = $db->prepare("SELECT s.* FROM students s WHERE s.email = ? LIMIT 1");
$stmt->execute([$email]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inquiry / Chat - Student Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #1a365d;
      --secondary-color: #2c5282;
      --accent-color: #f6ad55;
      --bg-light: #f7fafc;
    }
    
    body {
      background-color: var(--bg-light);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Sidebar base styles and mobile overlay are in sidebar.php */
    .main-content {
      margin-left: 250px;
      padding: 30px;
      transition: margin-left 0.3s ease;
    }

    /* Mobile */
    @media (max-width: 768px) {
      .main-content {
        margin-left: 0 !important;
        margin-top: 56px;
        padding: 15px !important;
      }
    }
    @media (max-width: 480px) {
      .main-content { padding: 10px !important; }
    }

    .chat-container {
      height: calc(100vh - 250px);
      min-height: 400px;
      background: white;
      border-radius: 15px;
      display: flex;
      flex-direction: column;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      overflow: hidden;
    }

    .chat-messages {
      flex-grow: 1;
      overflow-y: auto;
      padding: 20px;
      background: #f8f9fa;
    }

    .chat-input-area {
      padding: 20px;
      background: white;
      border-top: 1px solid #edf2f7;
    }

    .msg-bubble {
      max-width: 75%;
      margin-bottom: 15px;
      padding: 12px 16px;
      border-radius: 15px;
      position: relative;
      font-size: 0.95rem;
      line-height: 1.4;
    }

    .msg-student {
      background: var(--primary-color);
      color: white;
      align-self: flex-end;
      margin-left: auto;
      border-bottom-right-radius: 2px;
    }

    .msg-admin {
      background: #e2e8f0;
      color: #2d3748;
      align-self: flex-start;
      margin-right: auto;
      border-bottom-left-radius: 2px;
    }

    .msg-time {
      display: block;
      font-size: 0.7rem;
      margin-top: 5px;
      opacity: 0.7;
    }

    .chat-status-bar {
      padding: 10px 20px;
      background: #ebf8ff;
      border-bottom: 1px solid #bee3f8;
      font-size: 0.85rem;
      color: #2b6cb0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .status-badge {
      padding: 2px 8px;
      border-radius: 10px;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.7rem;
    }

    .status-pending { background: #feebc8; color: #9c4221; }
    .status-responded { background: #c6f6d5; color: #22543d; }

    /* Mobile tweaks */
    @media (max-width: 768px) {
      body { overflow-x: hidden; }
      .chat-container {
        height: calc(100vh - 180px);
        min-height: 300px;
      }
      .page-header-row { flex-direction: column !important; align-items: flex-start !important; gap: 10px; }
      .page-header-row nav { display: none; }
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header-row flex-wrap gap-2">
      <div>
        <h2 class="mb-0"><i class="fas fa-comments me-2 text-primary"></i>Inquiry & Chat</h2>
        <p class="text-muted small mb-0">Communicate directly with school administrators</p>
      </div>
      <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Chat</li>
        </ol>
      </nav>
    </div>

    <div id="noInquiryView" class="text-center py-5 d-none">
      <div class="content-card p-5">
        <i class="fas fa-comment-dots fa-4x text-muted mb-3"></i>
        <h3>No Active Inquiry</h3>
        <p class="text-muted">You haven't sent any inquiries yet. Send your first question to start a conversation with the admin.</p>
        <button class="btn btn-primary px-4 mt-2" onclick="showNewInquiryModal()">
          <i class="fas fa-plus me-2"></i>New Inquiry
        </button>
      </div>
    </div>

    <div id="chatView" class="chat-container d-none">
      <div class="chat-status-bar">
        <span>Conversation with Admission Office</span>
        <span id="inquiryStatus" class="status-badge">Loading...</span>
      </div>
      <div id="chatMessages" class="chat-messages d-flex flex-column">
        <!-- Messages loaded via JS -->
      </div>
      <div class="chat-input-area">
        <div class="input-group">
          <textarea id="replyMessage" class="form-control border-0 bg-light" rows="1" placeholder="Type your follow-up message..." style="resize: none;"></textarea>
          <button class="btn btn-primary px-4" type="button" onclick="sendReply()">
            <i class="fas fa-paper-plane"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- New Inquiry Modal -->
  <div class="modal fade" id="newInquiryModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New Admission Inquiry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Your Question/Message</label>
            <textarea id="newQuestion" class="form-control" rows="4" placeholder="How can we help you?"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="submitNewInquiry()">Submit Inquiry</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const studentEmail = '<?php echo $email; ?>';
    const studentName = '<?php echo $fullName; ?>';
    let currentInquiryId = null;

    function loadConversation() {
      fetch(`../../api/inquiries/get_student_messages.php?email=${encodeURIComponent(studentEmail)}&t=${Date.now()}`)
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            if (!res.found) {
              document.getElementById('noInquiryView').classList.remove('d-none');
              document.getElementById('chatView').classList.add('d-none');
              return;
            }

            document.getElementById('noInquiryView').classList.add('d-none');
            document.getElementById('chatView').classList.remove('d-none');
            
            currentInquiryId = res.inquiry_id;
            const statusBadge = document.getElementById('inquiryStatus');
            statusBadge.textContent = res.status;
            statusBadge.className = `status-badge status-${res.status.toLowerCase()}`;

            const chatContainer = document.getElementById('chatMessages');
            chatContainer.innerHTML = '';
            
            res.messages.forEach(msg => {
              const div = document.createElement('div');
              div.className = `msg-bubble ${msg.sender_type === 'student' ? 'msg-student' : 'msg-admin'}`;
              
              const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
              
              div.innerHTML = `
                <div>${msg.message}</div>
                <span class="msg-time">${time}</span>
              `;
              chatContainer.appendChild(div);
            });
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
          }
        });
    }

    function sendReply() {
      const msgInput = document.getElementById('replyMessage');
      const message = msgInput.value.trim();
      if (!message || !currentInquiryId) return;

      fetch('../../api/inquiries/create_simple.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          fullName: studentName,
          email: studentEmail,
          question: message
        })
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          msgInput.value = '';
          loadConversation();
        } else {
          alert('Error: ' + res.message);
        }
      });
    }

    function showNewInquiryModal() {
      new bootstrap.Modal(document.getElementById('newInquiryModal')).show();
    }

    function submitNewInquiry() {
      const question = document.getElementById('newQuestion').value.trim();
      if (!question) return;

      fetch('../../api/inquiries/create_simple.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          fullName: studentName,
          email: studentEmail,
          question: question
        })
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          bootstrap.Modal.getInstance(document.getElementById('newInquiryModal')).hide();
          loadConversation();
        } else {
          alert('Error: ' + res.message);
        }
      });
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', loadConversation);
    // Poll for new messages every 10 seconds
    setInterval(loadConversation, 10000);
  </script>
</body>
</html>