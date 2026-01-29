<?php
// Ultra simple test API
header("Content-Type: application/json");
echo json_encode(["success" => true, "message" => "Test API works"]);
?>
