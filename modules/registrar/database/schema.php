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
        `channel` VARCHAR(40) NOT NULL DEFAULT 'walk-in',
        `student_email` VARCHAR(190) NULL DEFAULT NULL,
        `paid` TINYINT(1) NOT NULL DEFAULT 0,
        `payment_ref` VARCHAR(80) NULL DEFAULT NULL,
        `status` VARCHAR(40) NOT NULL DEFAULT 'Submitted',
        `requested_by` INT UNSIGNED NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_reg_doc_requests_no` (`request_no`),
        KEY `idx_reg_doc_student` (`student_id`),
        KEY `idx_reg_doc_channel` (`channel`),
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
        `generated_file_id` INT UNSIGNED NULL DEFAULT NULL,
        `verification_code_id` INT UNSIGNED NULL DEFAULT NULL,
        `released_at` DATETIME NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_reg_doc_items_request` (`request_id`),
        KEY `idx_reg_doc_items_file` (`generated_file_id`),
        CONSTRAINT `fk_reg_doc_items_request` FOREIGN KEY (`request_id`) REFERENCES `reg_doc_requests`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_reg_doc_items_file` FOREIGN KEY (`generated_file_id`) REFERENCES `reg_files`(`id`) ON DELETE SET NULL
    ");

    $s['reg_health_records'] = regTbl('reg_health_records', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_id` INT UNSIGNED NOT NULL,
        `checkup_date` DATE NOT NULL,
        `complaints` VARCHAR(500) NULL DEFAULT NULL,
        `findings` VARCHAR(500) NULL DEFAULT NULL,
        `vital_signs` JSON NULL DEFAULT NULL,
        `immunization` VARCHAR(255) NULL DEFAULT NULL,
        `medication` VARCHAR(255) NULL DEFAULT NULL,
        `physician_nurse` VARCHAR(150) NULL DEFAULT NULL,
        `file_id` INT UNSIGNED NULL DEFAULT NULL,
        `notes` VARCHAR(500) NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        `updated_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_reg_health_student` (`student_id`),
        KEY `idx_reg_health_date` (`checkup_date`),
        CONSTRAINT `fk_reg_health_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_reg_health_file` FOREIGN KEY (`file_id`) REFERENCES `reg_files`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_health_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_health_updated` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_credentials'] = regTbl('reg_credentials', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_id` INT UNSIGNED NOT NULL,
        `credential_type` VARCHAR(40) NOT NULL,
        `token_value` VARCHAR(255) NOT NULL,
        `status` VARCHAR(40) NOT NULL DEFAULT 'Active',
        `activated_at` DATETIME NULL DEFAULT NULL,
        `deactivated_at` DATETIME NULL DEFAULT NULL,
        `notes` VARCHAR(255) NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_reg_cred_student` (`student_id`),
        KEY `idx_reg_cred_type` (`credential_type`),
        KEY `idx_reg_cred_token` (`token_value`),
        UNIQUE KEY `uq_reg_cred_token_type` (`token_value`, `credential_type`),
        CONSTRAINT `fk_reg_cred_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_reg_cred_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_student_ids'] = regTbl('reg_student_ids', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_id` INT UNSIGNED NOT NULL,
        `batch_no` VARCHAR(40) NULL DEFAULT NULL,
        `template_name` VARCHAR(100) NOT NULL DEFAULT 'standard',
        `photo_file_id` INT UNSIGNED NULL DEFAULT NULL,
        `id_number` VARCHAR(40) NULL DEFAULT NULL,
        `status` VARCHAR(40) NOT NULL DEFAULT 'Draft',
        `printed_at` DATETIME NULL DEFAULT NULL,
        `released_at` DATETIME NULL DEFAULT NULL,
        `released_by` INT UNSIGNED NULL DEFAULT NULL,
        `notes` VARCHAR(255) NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_reg_id_student` (`student_id`),
        KEY `idx_reg_id_batch` (`batch_no`),
        KEY `idx_reg_id_status` (`status`),
        CONSTRAINT `fk_reg_id_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_reg_id_photo` FOREIGN KEY (`photo_file_id`) REFERENCES `reg_files`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_id_released` FOREIGN KEY (`released_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_id_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_doc_releases'] = regTbl('reg_doc_releases', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `request_id` INT UNSIGNED NULL DEFAULT NULL,
        `request_item_id` INT UNSIGNED NULL DEFAULT NULL,
        `claim_type` VARCHAR(40) NOT NULL,
        `release_slip_no` VARCHAR(40) NOT NULL,
        `released_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `released_by` INT UNSIGNED NOT NULL,
        `claimant_name` VARCHAR(200) NOT NULL,
        `claimant_id` VARCHAR(100) NULL DEFAULT NULL,
        `claimant_signature` LONGBLOB NULL DEFAULT NULL,
        `notes` VARCHAR(255) NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_reg_release_slip` (`release_slip_no`),
        KEY `idx_reg_release_request` (`request_id`),
        KEY `idx_reg_release_date` (`released_at`),
        CONSTRAINT `fk_reg_release_request` FOREIGN KEY (`request_id`) REFERENCES `reg_doc_requests`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_release_item` FOREIGN KEY (`request_item_id`) REFERENCES `reg_doc_request_items`(`id`) ON DELETE SET NULL,
        CONSTRAINT `fk_reg_release_user` FOREIGN KEY (`released_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT
    ");

    $s['reg_verification_codes'] = regTbl('reg_verification_codes', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `doc_hash` CHAR(64) NOT NULL,
        `verification_code` VARCHAR(20) NOT NULL,
        `signed_payload` LONGTEXT NULL DEFAULT NULL,
        `signature` LONGTEXT NULL DEFAULT NULL,
        `algorithm` VARCHAR(40) NOT NULL DEFAULT 'RSA-SHA256',
        `doc_type` VARCHAR(60) NULL DEFAULT NULL,
        `student_id` INT UNSIGNED NULL DEFAULT NULL,
        `generated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `verified_count` INT UNSIGNED NOT NULL DEFAULT 0,
        `last_verified_at` DATETIME NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_reg_verify_code` (`verification_code`),
        KEY `idx_reg_verify_hash` (`doc_hash`),
        KEY `idx_reg_verify_student` (`student_id`),
        CONSTRAINT `fk_reg_verify_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE SET NULL
    ");

    $s['reg_academic_subjects'] = regTbl('reg_academic_subjects', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `student_id` INT UNSIGNED NOT NULL,
        `subject_code` VARCHAR(40) NOT NULL,
        `subject_name` VARCHAR(200) NOT NULL,
        `units` DECIMAL(5,2) NULL DEFAULT NULL,
        `term` VARCHAR(40) NOT NULL,
        `academic_year` VARCHAR(20) NOT NULL,
        `grade` VARCHAR(10) NULL DEFAULT NULL,
        `remarks` VARCHAR(100) NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_reg_subj_student` (`student_id`),
        KEY `idx_reg_subj_ay` (`academic_year`),
        CONSTRAINT `fk_reg_subj_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_reg_subj_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['reg_doc_templates'] = regTbl('reg_doc_templates', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `template_name` VARCHAR(100) NOT NULL,
        `doc_type` VARCHAR(60) NOT NULL,
        `description` VARCHAR(255) NULL DEFAULT NULL,
        `template_path` VARCHAR(255) NOT NULL,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_reg_template_name` (`template_name`),
        KEY `idx_reg_template_type` (`doc_type`)
    ");

    $s['reg_counters'] = regTbl('reg_counters', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `counter_key` VARCHAR(60) NOT NULL,
        `counter_value` INT UNSIGNED NOT NULL DEFAULT 0,
        `prefix` VARCHAR(40) NULL DEFAULT NULL,
        `format_pattern` VARCHAR(40) NULL DEFAULT NULL,
        `reset_frequency` VARCHAR(20) NOT NULL DEFAULT 'yearly',
        `last_reset_at` DATETIME NULL DEFAULT NULL,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_reg_counter_key` (`counter_key`)
    ");

    return $s;
}