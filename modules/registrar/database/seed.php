<?php
/**
 * SMS 2 - Registrar Database Seeder
 * Populates sample data for demo and testing
 * 
 * Usage: php modules/registrar/database/seed.php
 */
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    die("This script must run from CLI.\n");
}

require_once dirname(__DIR__, 3) . '/config/database.php';
require_once dirname(__DIR__, 3) . '/includes/crypto.php';

try {
    $db = db();
    
    echo "🌱 Seeding Registrar demo data...\n\n";
    
    // Clear existing sample data (keep schema intact)
    $tables = ['reg_counters', 'reg_academic_subjects', 'reg_doc_templates', 'reg_doc_releases',
               'reg_verification_codes', 'reg_student_ids', 'reg_credentials', 'reg_health_records',
               'reg_doc_request_items', 'reg_doc_requests', 'reg_student_statuses', 
               'reg_academic_history', 'reg_guardians', 'reg_persona_files', 'reg_files', 'reg_students'];
    
    foreach ($tables as $tbl) {
        $db->query("DELETE FROM `$tbl` WHERE 1=1");
        echo "✓ Cleared $tbl\n";
    }
    echo "\n";
    
    // Seed reg_students (10 sample students)
    $students = [
        ['2024001', 'Juan', 'D', 'Santos', null, '2000-05-15', 'Male', 'Filipino', 'BS Information Technology', 'IV-A', 'College of Engineering', '123456789101112', '2020-06-01'],
        ['2024002', 'Maria', 'G', 'Cruz', null, '2001-03-22', 'Female', 'Filipino', 'BS Business Administration', 'III-B', 'College of Business', '234567890121314', '2021-06-01'],
        ['2024003', 'Pedro', 'R', 'Reyes', 'Jr.', '2001-07-10', 'Male', 'Filipino', 'BS Nursing', 'II-A', 'College of Health Sciences', '345678901213141', '2022-06-01'],
        ['2024004', 'Rosa', 'L', 'Lopez', null, '2002-01-18', 'Female', 'Filipino', 'BS Education', 'II-C', 'College of Education', '456789012131415', '2022-06-01'],
        ['2024005', 'Carlos', 'M', 'Mendoza', null, '2000-11-25', 'Male', 'Filipino', 'BS Civil Engineering', 'IV-B', 'College of Engineering', '567890121314151', '2020-06-01'],
        ['2024006', 'Angela', 'P', 'Pascual', null, '2001-09-30', 'Female', 'Filipino', 'BS Accountancy', 'III-A', 'College of Business', '678901213141516', '2021-06-01'],
        ['2024007', 'Manuel', 'T', 'Torres', 'Sr.', '2000-02-14', 'Male', 'Filipino', 'BS Electronics Engineering', 'IV-C', 'College of Engineering', '789012131415161', '2020-06-01'],
        ['2024008', 'Lucia', 'F', 'Fernandez', null, '2002-08-05', 'Female', 'Filipino', 'BS Psychology', 'I-A', 'College of Arts and Sciences', '890121314151617', '2023-06-01'],
        ['2024009', 'Roberto', 'G', 'Garcia', null, '2001-04-20', 'Male', 'Filipino', 'BS Hotel and Restaurant Management', 'III-C', 'College of Hospitality', '901213141516171', '2021-06-01'],
        ['2024010', 'Sofia', 'A', 'Alvarez', null, '2002-06-12', 'Female', 'Filipino', 'BS Marine Biology', 'I-B', 'College of Arts and Sciences', '012131415161718', '2023-06-01'],
    ];
    
    $studentIds = [];
    foreach ($students as $s) {
        $stmt = $db->prepare("INSERT INTO `reg_students` 
            (`student_number`, `first_name`, `middle_name`, `last_name`, `suffix`, `date_of_birth`, 
             `gender`, `nationality`, `program_course`, `year_section`, `college_department`, 
             `birth_cert_no`, `enrollment_date`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($s);
        $studentIds[] = $db->lastInsertId();
        echo "✓ Created student: {$s[0]} ({$s[1]} {$s[3]})\n";
    }
    echo "\n";
    
    // Seed reg_guardians (2 per student)
    foreach ($studentIds as $idx => $sid) {
        $stmt = $db->prepare("INSERT INTO `reg_guardians` 
            (`student_id`, `full_name`, `relationship`, `contact`, `email`, `address`, `is_primary`, `is_emergency`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Mother
        $stmt->execute([$sid, 'Mother Name ' . ($idx + 1), 'Mother', '09' . rand(100000000, 999999999), 'mother' . ($idx + 1) . '@email.com', 'Address Line, City', 1, 0]);
        
        // Father
        $stmt->execute([$sid, 'Father Name ' . ($idx + 1), 'Father', '09' . rand(100000000, 999999999), 'father' . ($idx + 1) . '@email.com', 'Address Line, City', 0, 1]);
    }
    echo "✓ Seeded guardians (2 per student)\n\n";
    
    // Seed reg_academic_history (2 per student)
    foreach ($studentIds as $idx => $sid) {
        for ($i = 1; $i <= 2; $i++) {
            $stmt = $db->prepare("INSERT INTO `reg_academic_history` 
                (`student_id`, `school_name`, `level`, `from_year`, `to_year`, `awards`, `remarks`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $sid, 
                'School Name ' . $i, 
                $i === 1 ? 'Elementary' : 'High School',
                2010 + ($i - 1) * 4,
                2014 + ($i - 1) * 4,
                'Dean\'s Lister',
                'Good standing'
            ]);
        }
    }
    echo "✓ Seeded academic history (2 per student)\n\n";
    
    // Seed reg_student_statuses (initial + one more per student)
    foreach ($studentIds as $sid) {
        $stmt = $db->prepare("INSERT INTO `reg_student_statuses` 
            (`student_id`, `status`, `effective_date`, `remarks`, `changed_by`) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sid, 'Active', date('Y-m-d'), 'Initial enrollment', 1]);
    }
    echo "✓ Seeded student statuses\n\n";
    
    // Seed reg_health_records (2 per student)
    foreach ($studentIds as $idx => $sid) {
        $stmt = $db->prepare("INSERT INTO `reg_health_records` 
            (`student_id`, `checkup_date`, `complaints`, `findings`, `vital_signs`, 
             `immunization`, `medication`, `physician_nurse`, `notes`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $vitals = json_encode(['bp' => '120/80', 'temp' => '37.0', 'pulse' => '72', 'weight' => 65 + rand(0, 30)]);
        $stmt->execute([
            $sid,
            date('Y-m-d', strtotime('-6 months')),
            'None',
            'Healthy',
            $vitals,
            'Up to date',
            'None',
            'Dr. Health Check',
            'Annual physical examination'
        ]);
        
        $stmt->execute([
            $sid,
            date('Y-m-d', strtotime('-3 months')),
            'Minor cold',
            'Common cold, recovering well',
            $vitals,
            'Up to date',
            'Paracetamol as needed',
            'Dr. Health Check',
            'Follow-up visit'
        ]);
    }
    echo "✓ Seeded health records (2 per student)\n\n";
    
    // Seed reg_credentials (RFID/QR for each student)
    foreach ($studentIds as $idx => $sid) {
        $stmt = $db->prepare("INSERT INTO `reg_credentials` 
            (`student_id`, `credential_type`, `token_value`, `status`, `activated_at`) 
            VALUES (?, ?, ?, ?, ?)");
        
        // RFID UID (12-char hex)
        $rfid = strtoupper(bin2hex(random_bytes(6)));
        $stmt->execute([$sid, 'RFID', $rfid, 'Active', date('Y-m-d H:i:s')]);
        
        // QR token (student number)
        $stmt->execute([$sid, 'QR', 'SMS2QR' . $students[$idx][0], 'Active', date('Y-m-d H:i:s')]);
    }
    echo "✓ Seeded credentials (RFID + QR per student)\n\n";
    
    // Seed reg_student_ids (10 students in batch 001)
    foreach ($studentIds as $idx => $sid) {
        $stmt = $db->prepare("INSERT INTO `reg_student_ids` 
            (`student_id`, `batch_no`, `template_name`, `id_number`, `status`) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sid, 'BATCH-2024-001', 'standard', 'ID2024' . str_pad((string)($idx + 1), 5, '0', STR_PAD_LEFT), 'Printed']);
    }
    echo "✓ Seeded student IDs (batch 2024-001)\n\n";
    
    // Seed reg_academic_subjects (4 subjects per student)
    $subjects = [
        ['CS101', 'Introduction to Programming', 3.0, '1st', 'A'],
        ['CS102', 'Data Structures', 3.0, '2nd', 'B+'],
        ['CS201', 'Database Management', 4.0, '2nd', 'A-'],
        ['CS202', 'Web Development', 3.0, '1st', 'B'],
    ];
    
    foreach ($studentIds as $sid) {
        foreach ($subjects as $subj) {
            $stmt = $db->prepare("INSERT INTO `reg_academic_subjects` 
                (`student_id`, `subject_code`, `subject_name`, `units`, `term`, `academic_year`, `grade`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sid, $subj[0], $subj[1], $subj[2], $subj[3], '2024', $subj[4]]);
        }
    }
    echo "✓ Seeded academic subjects (4 per student)\n\n";
    
    // Seed reg_doc_templates
    $templates = [
        ['Form 137', 'FORM137', 'Form 137 - Transcript of Records', '/templates/form137.php', 1],
        ['Certificate of Good Moral', 'GMC', 'Certificate of Good Moral Character', '/templates/gmc.php', 1],
        ['TOR Transcript', 'TOR', 'Transcript of Records', '/templates/tor.php', 1],
        ['Certification', 'CERT', 'General Certification', '/templates/certification.php', 1],
    ];
    
    foreach ($templates as $tpl) {
        $stmt = $db->prepare("INSERT INTO `reg_doc_templates` 
            (`template_name`, `doc_type`, `description`, `template_path`, `is_active`) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($tpl);
    }
    echo "✓ Seeded document templates\n\n";
    
    // Seed reg_counters
    $counters = [
        ['REQ_NO', 0, 'DOC-REQ', 'YYYY-####'],
        ['FORM137_NO', 0, 'FORM137', 'YYYY-###'],
        ['GMC_NO', 0, 'GMC', 'YYYY-###'],
        ['RELEASE_NO', 0, 'REL', 'YYYY-###'],
    ];
    
    foreach ($counters as $counter) {
        $stmt = $db->prepare("INSERT INTO `reg_counters` 
            (`counter_key`, `counter_value`, `prefix`, `format_pattern`, `reset_frequency`) 
            VALUES (?, ?, ?, ?, 'yearly')");
        $stmt->execute($counter);
    }
    echo "✓ Seeded document number counters\n\n";
    
    // Seed reg_doc_requests (5 sample requests)
    $docRequests = [];
    for ($i = 1; $i <= 5; $i++) {
        $sid = $studentIds[$i - 1];
        $status = ['Submitted', 'Verified', 'Processing', 'For Release', 'Released'][array_rand([0, 1, 2, 3, 4])];
        $channel = rand(0, 1) ? 'online' : 'walk-in';
        
        $stmt = $db->prepare("INSERT INTO `reg_doc_requests` 
            (`request_no`, `student_id`, `purpose`, `channel`, `student_email`, `status`) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'DOC-REQ-' . date('Y') . '-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
            $sid,
            'Application to ' . ['another school', 'employer', 'scholarship', 'exam', 'visa'][rand(0, 4)],
            $channel,
            'student' . $i . '@email.com',
            $status
        ]);
        $docRequests[] = $db->lastInsertId();
    }
    echo "✓ Seeded document requests (5 samples)\n\n";
    
    // Seed reg_doc_request_items
    foreach ($docRequests as $reqId) {
        $docTypes = ['FORM137', 'GMC', 'TOR'];
        $numItems = rand(1, 3);
        for ($i = 0; $i < $numItems; $i++) {
            $stmt = $db->prepare("INSERT INTO `reg_doc_request_items` 
                (`request_id`, `doc_type`, `copies`, `status`) 
                VALUES (?, ?, ?, ?)");
            $stmt->execute([$reqId, $docTypes[array_rand($docTypes)], rand(1, 3), 'Pending']);
        }
    }
    echo "✓ Seeded document request items\n\n";
    
    echo "✅ Seeding complete! {" . count($studentIds) . "} students with demo data.\n";
    
} catch (Exception $e) {
    echo "❌ Seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
