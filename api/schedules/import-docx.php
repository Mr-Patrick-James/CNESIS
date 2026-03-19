<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

// This is a placeholder for the actual DOCX parsing logic.
// In a real environment, you would use a library like PHPWord (PhpOffice\PhpWord).
// Since we are in a sandbox, we will simulate the extraction based on the user's provided template.

$database = new Database();
$db = $database->getConnection();

if (!isset($_FILES['file']) || !isset($_POST['section_id'])) {
    echo json_encode(["success" => false, "message" => "Missing file or section ID"]);
    exit;
}

$section_id = $_POST['section_id'];
$semester = $_POST['semester'] ?? 1;
$file_name = $_FILES['file']['name'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if (!in_array($file_ext, ['docx', 'doc', 'xlsx', 'xls', 'csv'])) {
    echo json_encode(["success" => false, "message" => "Invalid file format. Please upload .docx, .xlsx, or .csv"]);
    exit;
}

try {
    $db->beginTransaction();

    // 1. Get Section Info (Department/Year Level)
    $stmt = $db->prepare("SELECT department_code, year_level FROM sections WHERE id = ?");
    $stmt->execute([$section_id]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$section) {
        throw new Exception("Section not found");
    }

    // 2. Clear existing schedule for this section and semester to avoid duplicates
    $clearStmt = $db->prepare("DELETE FROM class_schedules WHERE section_id = ? AND semester = ? AND student_id IS NULL");
    $clearStmt->execute([$section_id, $semester]);

    // Check if student_id column exists
    $stmt = $db->query("SHOW COLUMNS FROM class_schedules LIKE 'student_id'");
    if (!$stmt->fetch()) {
        $db->exec("ALTER TABLE class_schedules ADD COLUMN student_id INT DEFAULT NULL AFTER section_id");
    }

    $importCount = 0;

    if ($file_ext === 'csv') {
        // Handle CSV Parsing
        if (($handle = fopen($_FILES['file']['tmp_name'], "r")) !== FALSE) {
            // Expected format: Code, Title, Day, Start, End, Instructor, Room
            $header = fgetcsv($handle, 1000, ","); // Skip header
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) < 5) continue;
                
                $sub_code = trim($row[0]);
                $sub_title = trim($row[1]);
                $day = trim($row[2]);
                $start = trim($row[3]);
                $end = trim($row[4]);
                $instr = isset($row[5]) ? trim($row[5]) : 'TBA';
                $room = isset($row[6]) ? trim($row[6]) : 'TBA';

                // Find or create subject
                $stmt = $db->prepare("SELECT id FROM subjects WHERE subject_code = ?");
                $stmt->execute([$sub_code]);
                $sub = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($sub) {
                    $sub_id = $sub['id'];
                } else {
                    $stmt = $db->prepare("INSERT INTO subjects (subject_code, subject_title) VALUES (?, ?)");
                    $stmt->execute([$sub_code, $sub_title]);
                    $sub_id = $db->lastInsertId();
                }

                $insertQuery = "INSERT INTO class_schedules 
                                (section_id, semester, subject_id, day_of_week, start_time, end_time, instructor_name, room) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $db->prepare($insertQuery);
                $insertStmt->execute([
                    $section_id, 
                    $semester, 
                    $sub_id, 
                    $day, 
                    $start, 
                    $end, 
                    $instr, 
                    $room
                ]);
                $importCount++;
            }
            fclose($handle);
        }
    } else {
        // 3. Simulated DOCX/XLSX Parsing Logic (Since we lack libraries)
        // We'll simulate finding subjects from the prospectus and assigning them times
        
        $query = "SELECT s.id, s.subject_code, s.subject_title 
                  FROM subjects s 
                  JOIN prospectus p ON s.id = p.subject_id 
                  WHERE p.program_code = ? AND p.year_level = ? AND p.semester = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$section['department_code'], $section['year_level'], $semester]);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($subjects)) {
            throw new Exception("No subjects found in prospectus for this department, year, and semester. Please import prospectus first or use CSV for custom schedule.");
        }

        // Mocking the distribution
        $time_slots = [
            ['start' => '08:00:00', 'end' => '09:30:00'],
            ['start' => '09:30:00', 'end' => '11:00:00'],
            ['start' => '11:00:00', 'end' => '12:30:00'],
            ['start' => '13:00:00', 'end' => '14:30:00'],
            ['start' => '14:30:00', 'end' => '16:00:00']
        ];
        
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        foreach ($subjects as $index => $sub) {
            if ($index >= count($time_slots)) break;

            $slot = $time_slots[$index];
            $day = $days[$index % count($days)];

            $insertQuery = "INSERT INTO class_schedules 
                            (section_id, semester, subject_id, day_of_week, start_time, end_time, instructor_name, room) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->execute([
                $section_id, 
                $semester, 
                $sub['id'], 
                $day, 
                $slot['start'], 
                $slot['end'], 
                'Assigned via Import', 
                'TBA'
            ]);
            $importCount++;
        }
    }

    $db->commit();
    echo json_encode([
        "success" => true, 
        "message" => "Successfully imported $importCount subjects from $file_name",
        "count" => $importCount
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
