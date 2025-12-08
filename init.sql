-- === Database ===
DROP DATABASE IF EXISTS `student_db`;
CREATE DATABASE `student_db`;
USE `student_db`;

-- Notes:
-- - This file drops and recreates a database and tables. PLEASE DO NOT EXECUTE MORE THAN ONCE.
-- - The system is designed for a single teacher/professor user by default.

-- === School Years ===
-- Format: YYYY-YYYY (start-end)
DROP TABLE IF EXISTS `school-years`;
CREATE TABLE `school-years` (
    `id` INT,
    `name` VARCHAR(255)
);

-- === Grade Levels ===
DROP TABLE IF EXISTS `grade-levels`;
CREATE TABLE `grade-levels` (
    `id` INT,
    `name` VARCHAR(255)
);

-- === Sections ===
-- Sections are tied to a school year and a grade level.
DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
    `schoolYearId` INT,
    `gradeLevelId` INT,
    `id` INT,
    `name` VARCHAR(255)
);

-- === Courses (Subjects) ===
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
    `id` INT,
    `name` VARCHAR(100)
);

-- === Activity types ===
-- Like `Performance Task`
-- can vary from different courses.
-- Like TVE can have a weight of 60% for Performance tasks.
DROP TABLE IF EXISTS `activity-types`;
CREATE TABLE `activity-types` (
    `id` INT,
    `name` VARCHAR(255),
    `weight` INT,
    `courseId` INT
);

-- === Activities ===
DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
    `id` INT,
    `name` VARCHAR(255),
    `maximumScore` INT,
    `courseId` INT,
    `typeId` INT
);

-- === Scores ===
DROP TABLE IF EXISTS `scores`;
CREATE TABLE `scores` (
    `id` INT,
    `score` INT,
    `activityId` INT,
    `studentHash` VARCHAR(64) DEFAULT NULL
);

-- === Students ===
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
    `LRN` INT,
    `hash` VARCHAR(64),
    `firstName` VARCHAR(100),
    `lastName` VARCHAR(55),
    `mail` VARCHAR(100),
    `contactNumber` VARCHAR(30),
    `address` VARCHAR(255) DEFAULT NULL,
    `guardian` VARCHAR(255),
    `guardianContactNumber` VARCHAR(30),
    `sectionId` INT
);

-- === Intermediate View: Student Type Averages ===
-- Per student/course/type: compute type_avg = SUM(scores) / SUM(maximumScore)
CREATE OR REPLACE VIEW `student_type_averages` AS
SELECT
    sc.studentHash,
    a.courseId,
    a.typeId,
    SUM(sc.score) / NULLIF(SUM(a.maximumScore), 0) AS type_avg
FROM `scores` sc
JOIN `activities` a ON sc.activityId = a.id
GROUP BY sc.studentHash, a.courseId, a.typeId;

-- === Marks View ===
-- Per-student, per-course weighted average using the student_type_averages view
CREATE OR REPLACE VIEW `marks` AS
SELECT
    s.hash AS studentHash,
    s.LRN,
    s.firstName,
    s.lastName,
    c.id AS courseId,
    c.name AS courseName,
    CASE WHEN SUM(at.weight) = 0 THEN NULL
         ELSE ROUND((SUM(sta.type_avg * at.weight) / SUM(at.weight)) * 100, 2)
    END AS weighted_percentage
FROM `student_type_averages` sta
JOIN `activity-types` at ON sta.typeId = at.id
JOIN `students` s ON sta.studentHash = s.hash
JOIN `courses` c ON sta.courseId = c.id
GROUP BY sta.studentHash, sta.courseId, s.hash, s.LRN, s.firstName, s.lastName, c.id, c.name;

-- === Primary Keys ===
ALTER TABLE `school-years`
    ADD CONSTRAINT `pk_school_years_id`
        PRIMARY KEY (`id`),
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `grade-levels`
    ADD CONSTRAINT `pk_grade_levels_id`
        PRIMARY KEY (`id`),
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `sections`
    ADD CONSTRAINT `pk_sections_id` 
        PRIMARY KEY (`id`),
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `courses`
    ADD CONSTRAINT `pk_courses_id` 
        PRIMARY KEY (`id`),
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `activity-types`
    ADD CONSTRAINT `pk_activity_types_id`
        PRIMARY KEY (`id`),
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `activities`
    ADD CONSTRAINT `pk_activities_id`
        PRIMARY KEY (`id`),
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `scores`
    ADD CONSTRAINT `pk_scores_id`
        PRIMARY KEY (`id`),
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `students`
    ADD CONSTRAINT `pk_students_hash`
        PRIMARY KEY (`hash`);

-- === Foreign Keys ===
ALTER TABLE `sections`
    ADD CONSTRAINT `fk_sections_school_years`
        FOREIGN KEY (`schoolYearId`)
            REFERENCES `school-years` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_sections_grade_levels`
        FOREIGN KEY (`gradeLevelId`)
            REFERENCES `grade-levels` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE;

ALTER TABLE `activity-types`
    ADD CONSTRAINT `fk_activity_types_courses`
        FOREIGN KEY (`courseId`)
            REFERENCES `courses` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE;

ALTER TABLE `activities`
    ADD CONSTRAINT `fk_activities_courses`
        FOREIGN KEY (`courseId`)
            REFERENCES `courses` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_activities_activity_types`
        FOREIGN KEY (`typeId`)
            REFERENCES `activity-types` (`id`)
                ON DELETE CASCADE 
                ON UPDATE CASCADE;

ALTER TABLE `scores`
    ADD CONSTRAINT `fk_scores_activities`
        FOREIGN KEY (`activityId`)
            REFERENCES `activities` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_scores_students`
        FOREIGN KEY (`studentHash`)
            REFERENCES `students` (`hash`)
                ON DELETE CASCADE
                ON UPDATE CASCADE;

ALTER TABLE `students`
    ADD CONSTRAINT `fk_students_sections`
        FOREIGN KEY (`sectionId`)
            REFERENCES `sections` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE;

-- Shows the warnings
SHOW WARNINGS;