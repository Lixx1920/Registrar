<?php
/**
 * SMS 2 - Registrar Foundation Schema (single source of truth).
 *
 * Tables are created inside the shared sms2_db by:
 *   modules/registrar/database/install.php  (CLI, idempotent)
 */
declare(strict_types=1);

if (!function_exists('regTbl')) {
    function regTbl(string $name, string $cols): string
    {
        return 'CREATE TABLE IF NOT EXISTS `' . $name . '` (' . $cols . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    }
}

function registrarSchemaStatements(): array
{
    $s = [];

    $s['reg_students'] = regTbl('reg_students', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_number` VARCHAR(40) NOT NULL,
        `user_id` INT UNSIGNED NULL DEFAULT NULL,
        `first_name` VARCHAR(100) NOT NULL,
        `middle_name` VARCHAR(100) NULL DEFAULT NULL,
        `last_name` VARCHAR(100) NOT NULL,
        `suffix` VARCHAR(20) NULL DEFAULT NULL,
        `date_of_birth` DATE NULL DEFAULT NULL,
        `gender` VARCHAR(20) NULL DEFAULT NULL,
        `nationality` VARCHAR(80) NULL DEFAULT NULL,
        `program_course` VARCHAR(200) NULL DEFAULT NULL,
        `year_section` VARCHAR(100) NULL DEFAULT NULL,
        `college_department` VARCHAR(200) NULL DEFAULT NULL,
        `birth_cert_no` VARCHAR(80) NULL DEFAULT NULL,
        `enrollment_date` DATE NULL DEFAULT NULL,
        `status` VARCHAR(40) NOT NULL DEFAULT 'Active',
        `photo_file_id` INT UNSIGNED NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        `updated_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_reg_students_number` (`student_number`),
        KEY `idx_reg_students_name` (`last_name`,`first_name`),
        KEY `idx_reg_students_user` (`user_id`),
        CONSTRAINT `fk_reg_students_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
        CONSTRAINT `fk_reg_students_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_students_updated` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_files'] = regTbl('reg_files', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_id` INT UNSIGNED NULL DEFAULT NULL,
        `category` VARCHAR(60) NOT NULL DEFAULT 'General',
        `original_name` VARCHAR(300) NOT NULL DEFAULT '',
        `stored_name` VARCHAR(300) NOT NULL DEFAULT '',
        `mime` VARCHAR(120) NULL DEFAULT NULL,
        `size` INT UNSIGNED NOT NULL DEFAULT 0,
        `sha256_hash` CHAR(64) NULL DEFAULT NULL,
        `uploaded_by` INT UNSIGNED NULL DEFAULT NULL,
        `status` VARCHAR(40) NOT NULL DEFAULT 'Active',
        `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_reg_files_student` (`student_id`),
        CONSTRAINT `fk_reg_files_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_files_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_persona_files'] = regTbl('reg_persona_files', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_id` INT UNSIGNED NOT NULL,
        `file_category` VARCHAR(60) NOT NULL,
        `identity_doc_no` VARCHAR(80) NULL DEFAULT NULL,
        `notes` VARCHAR(500) NULL DEFAULT NULL,
        `file_id` INT UNSIGNED NULL DEFAULT NULL,
        `verified_at` DATETIME NULL DEFAULT NULL,
        `verified_by` INT UNSIGNED NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        `updated_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_reg_persona_student` (`student_id`),
        CONSTRAINT `fk_reg_persona_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_reg_persona_file` FOREIGN KEY (`file_id`) REFERENCES `reg_files`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_persona_verified` FOREIGN KEY (`verified_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_persona_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_persona_updated` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_guardians'] = regTbl('reg_guardians', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_id` INT UNSIGNED NOT NULL,
        `full_name` VARCHAR(200) NOT NULL,
        `relationship` VARCHAR(60) NOT NULL,
        `contact` VARCHAR(40) NULL DEFAULT NULL,
        `email` VARCHAR(190) NULL DEFAULT NULL,
        `address` VARCHAR(255) NULL DEFAULT NULL,
        `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
        `is_emergency` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        `updated_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_reg_guardians_student` (`student_id`),
        CONSTRAINT `fk_reg_guardians_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_reg_guardians_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_guardians_updated` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_academic_history'] = regTbl('reg_academic_history', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_id` INT UNSIGNED NOT NULL,
        `school_name` VARCHAR(200) NOT NULL,
        `level` VARCHAR(60) NULL DEFAULT NULL,
        `from_year` SMALLINT UNSIGNED NULL DEFAULT NULL,
        `to_year` SMALLINT UNSIGNED NULL DEFAULT NULL,
        `awards` VARCHAR(255) NULL DEFAULT NULL,
        `remarks` VARCHAR(500) NULL DEFAULT NULL,
        `file_id` INT UNSIGNED NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        `updated_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_reg_academic_student` (`student_id`),
        CONSTRAINT `fk_reg_academic_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_reg_academic_file` FOREIGN KEY (`file_id`) REFERENCES `reg_files`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_academic_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_academic_updated` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_student_statuses'] = regTbl('reg_student_statuses', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_id` INT UNSIGNED NOT NULL,
        `status` VARCHAR(40) NOT NULL,
        `effective_date` DATE NULL DEFAULT NULL,
        `remarks` VARCHAR(500) NULL DEFAULT NULL,
        `changed_by` INT UNSIGNED NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_reg_status_student` (`student_id`),
        CONSTRAINT `fk_reg_status_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_reg_status_changed` FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");
$s['reg_doc_requests'] = regTbl('reg_doc_requests', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `request_no` VARCHAR(40) NOT NULL,
        `student_id` INT UNSIGNED NOT NULL,
        `purpose` VARCHAR(255) NULL DEFAULT NULL,
        `status` VARCHAR(40) NOT NULL DEFAULT 'Submitted',
        `requested_by` INT UNSIGNED NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_reg_doc_requests_no` (`request_no`),
        KEY `idx_reg_doc_student` (`student_id`),
        CONSTRAINT `fk_reg_doc_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE RESTRICT,
        CONSTRAINT `fk_reg_doc_requested` FOREIGN KEY (`requested_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_doc_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_doc_request_items'] = regTbl('reg_doc_request_items', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `request_id` INT UNSIGNED NOT NULL,
        `doc_type` VARCHAR(60) NOT NULL,
        `copies` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
        `format` VARCHAR(20) NOT NULL DEFAULT 'PDF',
        `status` VARCHAR(40) NOT NULL DEFAULT 'Pending',
        `notes` VARCHAR(255) NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_reg_doc_items_request` (`request_id`),
        CONSTRAINT `fk_reg_doc_items_request` FOREIGN KEY (`request_id`) REFERENCES `reg_doc_requests`(`id`) ON DELETE CASCADE
    ");

    return $s;
}