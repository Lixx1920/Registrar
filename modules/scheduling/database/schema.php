<?php
/**
 * SMS 2 - Class Scheduling: Section Assignment schema (single source of truth).
 *
 * Tables are created inside the shared sms2_db by:
 *   modules/scheduling/database/install.php  (CLI, idempotent)
 *
 * Scope: just enough real, working structure for the Section Assignment Tool
 * to assign existing Registrar students (reg_students) into sections for a
 * given school year/semester, so the Registrar's Masterlist Generator can
 * pull a real "Enrolled" fact instead of a mock value.
 */
declare(strict_types=1);

if (!function_exists('schTbl')) {
    function schTbl(string $name, string $cols): string
    {
        return 'CREATE TABLE IF NOT EXISTS `' . $name . '` (' . $cols . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    }
}

function schedulingSchemaStatements(): array
{
    $s = [];

    $s['sch_sections'] = schTbl('sch_sections', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `program_course` VARCHAR(200) NOT NULL,
        `year_section` VARCHAR(100) NOT NULL,
        `school_year` VARCHAR(20) NOT NULL,
        `semester` VARCHAR(30) NOT NULL DEFAULT '1st Semester',
        `section_code` VARCHAR(60) NULL DEFAULT NULL,
        `adviser_name` VARCHAR(150) NULL DEFAULT NULL,
        `max_students` INT UNSIGNED NULL DEFAULT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_sch_sections_lookup` (`program_course`,`year_section`,`school_year`,`semester`),
        CONSTRAINT `fk_sch_sections_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    $s['sch_section_assignments'] = schTbl('sch_section_assignments', "
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `section_id` INT UNSIGNED NOT NULL,
        `student_id` INT UNSIGNED NOT NULL,
        `status` VARCHAR(20) NOT NULL DEFAULT 'Enrolled',
        `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_by` INT UNSIGNED NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_sch_assignment_student_section` (`section_id`,`student_id`),
        KEY `idx_sch_assignments_student` (`student_id`),
        CONSTRAINT `fk_sch_assignments_section` FOREIGN KEY (`section_id`) REFERENCES `sch_sections`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_sch_assignments_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students`(`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_sch_assignments_created` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ");

    return $s;
}