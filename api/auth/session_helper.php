<?php
/**
 * Session Helper for Landing Pages
 * Starts session and prepares user variables for the navigation header
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for any of the session keys that indicate a logged-in or verified user
$is_verified = isset($_SESSION['user_id']) || isset($_SESSION['verified_student_email']) || isset($_SESSION['verified_email']);

// Get the user's name from session
$student_name = $_SESSION['verified_student_name'] ?? $_SESSION['full_name'] ?? $_SESSION['username'] ?? '';

// Output the JavaScript variables for the frontend
?>
<script>
    window.isStudentVerified = <?php echo $is_verified ? 'true' : 'false'; ?>;
    window.studentName = "<?php echo addslashes($student_name); ?>";
</script>
