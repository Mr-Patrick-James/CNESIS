<?php
// Redirect to dashboard page — detect base path for local vs production
$basePath = str_replace('/views/admin/index.php', '', $_SERVER['SCRIPT_NAME']);
header("Location: " . $basePath . "/views/admin/features/dashboard.php");
exit();
?>
