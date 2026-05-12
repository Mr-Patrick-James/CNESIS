<?php
/**
 * Seed Program Details
 * Updates existing programs with highlights, career opportunities, and admission requirements.
 * Visit this page once to populate the data, then it can be removed.
 */

header("Content-Type: text/plain; charset=UTF-8");

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db === null) {
    echo "Database connection failed.\n";
    exit;
}

$programs = [
    'BSIS' => [
        'highlights' => [
            'Hands-on training in systems analysis and design',
            'Industry-aligned curriculum with IT certifications',
            'Capstone project with real-world business clients',
            'Exposure to enterprise software and cloud platforms',
            'Strong focus on database management and web development',
            'On-the-Job Training (OJT) in leading IT companies',
        ],
        'career_opportunities' => [
            'Systems Analyst',
            'Database Administrator',
            'IT Project Manager',
            'Web Developer / Full-Stack Developer',
            'Business Intelligence Analyst',
            'Network Administrator',
            'Software Quality Assurance Engineer',
            'IT Consultant',
        ],
        'admission_requirements' => [
            'Original and photocopy of Grade 12 Report Card (Form 138)',
            'PSA-authenticated Birth Certificate',
            'Certificate of Good Moral Character from previous school',
            'Two (2) 2x2 ID photos with white background',
            'Accomplished Admission Application Form',
            'Entrance examination and interview',
        ],
    ],
    'BTVTED-CHS' => [
        'highlights' => [
            'TESDA NC II certification in Computer Hardware Servicing',
            'Dual training in technical education and hardware technology',
            'Hands-on laboratory work with actual computer systems',
            'Training in network installation and administration',
            'Prepares graduates for both teaching and industry careers',
            'Practicum in schools and technical-vocational institutions',
        ],
        'career_opportunities' => [
            'Computer Hardware Technician',
            'Technical-Vocational Teacher (DepEd)',
            'Network Administrator',
            'IT Support Specialist',
            'Computer Laboratory Technician',
            'Electronics Technician',
            'Technical Trainer / Instructor',
        ],
        'admission_requirements' => [
            'Original and photocopy of Grade 12 Report Card (Form 138)',
            'PSA-authenticated Birth Certificate',
            'Certificate of Good Moral Character from previous school',
            'Two (2) 2x2 ID photos with white background',
            'Accomplished Admission Application Form',
            'Entrance examination and interview',
        ],
    ],
    'BTVTED-WFT' => [
        'highlights' => [
            'TESDA NC II/III certification in Welding and Fabrication',
            'State-of-the-art welding laboratory and equipment',
            'Training in SMAW, GMAW, GTAW, and FCAW welding processes',
            'Dual competency in technical education and industrial skills',
            'Practicum in manufacturing and construction companies',
            'Prepares graduates for both teaching and industry careers',
        ],
        'career_opportunities' => [
            'Welding Inspector / Supervisor',
            'Technical-Vocational Teacher (DepEd)',
            'Metal Fabrication Technician',
            'Industrial Welder (local and overseas)',
            'Structural Steel Fabricator',
            'Welding Trainer / Instructor',
            'Quality Control Inspector',
        ],
        'admission_requirements' => [
            'Original and photocopy of Grade 12 Report Card (Form 138)',
            'PSA-authenticated Birth Certificate',
            'Certificate of Good Moral Character from previous school',
            'Two (2) 2x2 ID photos with white background',
            'Accomplished Admission Application Form',
            'Entrance examination and interview',
            'Medical certificate (for physical fitness)',
        ],
    ],
    'BPA' => [
        'highlights' => [
            'Comprehensive study of public governance and administration',
            'Exposure to local government units and national agencies',
            'Training in public policy analysis and formulation',
            'Community development and extension service programs',
            'Internship in government offices and NGOs',
            'Strong foundation in law, ethics, and public service',
        ],
        'career_opportunities' => [
            'Local Government Officer',
            'Public Administrator / Civil Servant',
            'Policy Analyst',
            'Community Development Officer',
            'NGO Program Coordinator',
            'Government Liaison Officer',
            'Administrative Officer (National Agencies)',
            'Barangay / Municipal Official',
        ],
        'admission_requirements' => [
            'Original and photocopy of Grade 12 Report Card (Form 138)',
            'PSA-authenticated Birth Certificate',
            'Certificate of Good Moral Character from previous school',
            'Two (2) 2x2 ID photos with white background',
            'Accomplished Admission Application Form',
            'Entrance examination and interview',
        ],
    ],
];

$updated = 0;
$skipped = 0;

foreach ($programs as $code => $details) {
    // Find program by code (try exact match and partial match)
    $stmt = $db->prepare("SELECT id, code, title FROM programs WHERE code = :code OR code LIKE :like_code LIMIT 1");
    $likeCode = '%' . $code . '%';
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':like_code', $likeCode);
    $stmt->execute();
    $program = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$program) {
        echo "SKIP: No program found matching code '$code'\n";
        $skipped++;
        continue;
    }

    $updateStmt = $db->prepare("
        UPDATE programs SET
            highlights = :highlights,
            career_opportunities = :career_opportunities,
            admission_requirements = :admission_requirements
        WHERE id = :id
    ");

    $updateStmt->bindValue(':highlights', json_encode($details['highlights']));
    $updateStmt->bindValue(':career_opportunities', json_encode($details['career_opportunities']));
    $updateStmt->bindValue(':admission_requirements', json_encode($details['admission_requirements']));
    $updateStmt->bindParam(':id', $program['id']);

    if ($updateStmt->execute()) {
        echo "UPDATED: [{$program['code']}] {$program['title']}\n";
        $updated++;
    } else {
        echo "FAILED:  [{$program['code']}] {$program['title']}\n";
    }
}

echo "\nDone. Updated: $updated, Skipped: $skipped\n";
echo "You can delete this file after running it.\n";
