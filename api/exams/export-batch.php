<?php
/**
 * Export Batch Examinees API
 * Generates various file formats (CSV, Excel, Word, PDF) for batch examinees
 */

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    die("Database connection failed");
}

$batch_id = isset($_GET['batch_id']) ? (int)$_GET['batch_id'] : 0;
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

if (!$batch_id) {
    die("Invalid Batch ID");
}

// 1. Get Batch Info
$batchQuery = "SELECT * FROM exam_schedules WHERE id = ?";
$batchStmt = $db->prepare($batchQuery);
$batchStmt->execute([$batch_id]);
$batch = $batchStmt->fetch(PDO::FETCH_ASSOC);

if (!$batch) {
    die("Batch not found");
}

// 2. Get Students in Batch
$query = "SELECT 
            a.application_id,
            a.first_name,
            a.middle_name,
            a.last_name,
            a.email,
            a.phone,
            a.gender,
            p.title as program,
            a.status
          FROM admissions a
          LEFT JOIN programs p ON a.program_id = p.id
          WHERE a.exam_schedule_id = ?
          ORDER BY a.last_name ASC, a.first_name ASC";

$stmt = $db->prepare($query);
$stmt->execute([$batch_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$batchTitle = "Examinees List - " . $batch['batch_name'];
$batchDate = date('F d, Y', strtotime($batch['exam_date']));
$batchTime = date('h:i A', strtotime($batch['start_time'])) . " - " . date('h:i A', strtotime($batch['end_time']));
$batchVenue = $batch['venue'];

// 3. Handle different formats
if ($format === 'csv') {
    $filename = "Examinees_" . str_replace(' ', '_', $batch['batch_name']) . "_" . $batch['exam_date'] . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    $output = fopen('php://output', 'w');
    fputcsv($output, ['BATCH:', $batch['batch_name'], 'DATE:', $batchDate, 'VENUE:', $batchVenue]);
    fputcsv($output, []);
    fputcsv($output, ['Application ID', 'First Name', 'Middle Name', 'Last Name', 'Email', 'Phone', 'Gender', 'Program', 'Status']);
    foreach ($students as $student) {
        $statusLabel = ucfirst($student['status']);
    if ($student['status'] === 'scheduled') {
        $statusLabel = 'Scheduling';
    }
    
    fputcsv($output, [
        $student['application_id'],
        $student['first_name'],
        $student['middle_name'],
        $student['last_name'],
        $student['email'],
        $student['phone'],
        ucfirst($student['gender']),
        $student['program'],
        $statusLabel
    ]);
    }
    fclose($output);
    exit;
}

// For HTML-based formats (Excel, Word, PDF)
$htmlOutput = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . $batchTitle . '</title>
    <style>
        body { font-family: sans-serif; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1a365d; padding-bottom: 10px; }
        .header h1 { color: #1a365d; margin: 0; font-size: 24px; }
        .info-grid { margin-bottom: 20px; width: 100%; border-collapse: collapse; }
        .info-grid td { padding: 5px; font-size: 14px; }
        .info-label { font-weight: bold; width: 15%; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.data-table th { background-color: #1a365d; color: white; padding: 10px; text-align: left; font-size: 12px; border: 1px solid #ddd; }
        table.data-table td { padding: 8px; font-size: 12px; border: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #777; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>COLEGIO DE NAUJAN</h1>
        <p>Entrance Exam Examinees List</p>
    </div>

    <table class="info-grid">
        <tr>
            <td class="info-label">Batch Name:</td>
            <td>' . htmlspecialchars($batch['batch_name']) . '</td>
            <td class="info-label">Exam Date:</td>
            <td>' . $batchDate . '</td>
        </tr>
        <tr>
            <td class="info-label">Venue:</td>
            <td>' . htmlspecialchars($batchVenue) . '</td>
            <td class="info-label">Time:</td>
            <td>' . $batchTime . '</td>
        </tr>
        <tr>
            <td class="info-label">Total Slots:</td>
            <td>' . $batch['max_slots'] . '</td>
            <td class="info-label">Attendees:</td>
            <td>' . count($students) . '</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Application ID</th>
                <th>Examinee Name</th>
                <th>Program</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

$count = 1;
foreach ($students as $s) {
    $fullName = strtoupper($s['last_name'] . ", " . $s['first_name'] . " " . ($s['middle_name'] ? substr($s['middle_name'], 0, 1) . "." : ""));
    $statusLabel = ucfirst($s['status']);
    if ($s['status'] === 'scheduled') {
        $statusLabel = 'Scheduling';
    }
    
    $htmlOutput .= '
            <tr>
                <td>' . $count++ . '</td>
                <td>' . $s['application_id'] . '</td>
                <td>' . $fullName . '</td>
                <td>' . $s['program'] . '</td>
                <td>' . $s['email'] . '</td>
                <td>' . $s['phone'] . '</td>
                <td>' . ucfirst($s['gender']) . '</td>
                <td>' . $statusLabel . '</td>
            </tr>';
}

if (empty($students)) {
    $htmlOutput .= '<tr><td colspan="8" style="text-align:center;">No students assigned to this batch.</td></tr>';
}

$htmlOutput .= '
        </tbody>
    </table>

    <div class="footer">
        Generated on: ' . date('Y-m-d h:i A') . '
    </div>';

if ($format === 'pdf') {
    // For PDF, we show as HTML and trigger print
    $htmlOutput .= '
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
    </body>
    </html>';
    echo $htmlOutput;
    exit;
}

if ($format === 'excel') {
    $filename = "Examinees_" . str_replace(' ', '_', $batch['batch_name']) . "_" . $batch['exam_date'] . ".xls";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=' . $filename);
    echo $htmlOutput . '</body></html>';
    exit;
}

if ($format === 'docx') {
    $filename = "Examinees_" . str_replace(' ', '_', $batch['batch_name']) . "_" . $batch['exam_date'] . ".doc";
    header('Content-Type: application/vnd.ms-word');
    header('Content-Disposition: attachment; filename=' . $filename);
    echo $htmlOutput . '</body></html>';
    exit;
}

// Fallback
echo "Invalid format specified.";
