<?php
/**
 * Session Helper for Landing Pages
 * Starts session and prepares user variables for the navigation header
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only show the profile icon for users table accounts (admin, staff, faculty, enrolled students)
// Applicants who verified via OTP should NOT see the profile dropdown
$is_verified = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

// Get the user's name from session
$student_name = $_SESSION['full_name'] ?? $_SESSION['username'] ?? '';

// Output the JavaScript variables for the frontend
?>
<script>
    window.isStudentVerified = <?php echo $is_verified ? 'true' : 'false'; ?>;
    window.studentName = "<?php echo addslashes($student_name); ?>";
</script>
