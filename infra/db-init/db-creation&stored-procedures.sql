drop database if exists eskua_db;

create database if not exists eskua_db;

use eskua_db;

-- USERS
create table users(
	id int primary key auto_increment,
    username varchar(30) unique not null,
    email varchar(255) unique not null check (email like '%@%'),
    display_name varchar(30) not null,
    profile_picture_url varchar(255) not null,
    `password` varchar(255) not null,
    `role` enum('admin', 'teacher', 'student', 'guest') not null,
    is_deleted bool not null default false
);

-- TOKENS
CREATE TABLE tokens (
    id int primary key auto_increment,
    `user_id` int not null,
    refresh_token varchar(512) not null,
    expires_at datetime not null,
    is_revoked boolean not null default false,
    created_at datetime not null default current_timestamp,
    updated_at datetime not null default current_timestamp on update current_timestamp,
    foreign key (`user_id`) references users(id) on delete cascade
);

-- GUESTS
create table guests(
	`user` int not null primary key,
    foreign key (`user`) references users(id) on delete cascade
);

-- STUDENTS
create table students(
	`user` int not null primary key,
    foreign key (`user`) references users(id) on delete cascade
);

-- TEACHERS
create table teachers(
	`user` int not null primary key,
    foreign key (`user`) references users(id) on delete cascade
);

-- ADMINS
create table admins(
	`user` int not null primary key,
    foreign key (`user`) references users(id) on delete cascade
);

-- GROUPS
create table `groups`(
	id int primary key auto_increment,
    teacher int not null,
    `name` varchar(45) not null,
    `code` varchar(6) not null unique,
    foreign key (teacher) references users(id) on delete cascade
);

-- ADDED THE GROUP REFERENCE IN THE STUDENTS TABLE
alter table students
add column `group` int,
add constraint `students_group`
    foreign key (`group`) references `groups`(id) on delete cascade;

-- MESSAGES
create table messages(
	id int not null auto_increment primary key,
    `group` int not null,
    `user` int not null,
    `message_text` text not null,
    sent_time datetime not null default current_timestamp,
    foreign key (`group`) references `groups`(id) on delete cascade,
    foreign key (`user`) references users(id) on delete cascade
);

-- NOTIFICATIONS
create table notifications(
	id int not null auto_increment primary key,
    `user` int not null,
    content varchar(200) not null,
    sent_date datetime not null default current_timestamp,
    `type` enum('activity', 'message') not null,
    was_read bool not null default false,
    foreign key (`user`) references users(id) on delete cascade
);

-- ASSIGNMENT THINGS
create table assignments(
	id int primary key auto_increment,
    teacher int not null,
    `name` varchar(50) not null,
    `description` text not null,
    max_score int not null,
    is_deleted bool not null default false,
    foreign key (teacher) references users(id) on delete cascade
);

create table assigned_assignments(
	id int primary key auto_increment,
    teacher int not null,
    assignment int not null,
    `group` int not null,
    start_date datetime not null default current_timestamp,
    end_date datetime not null,
    is_deleted bool not null default false,
    foreign key (teacher) references users(id) on delete cascade,
    foreign key (assignment) references assignments(id) on delete cascade,
    foreign key (`group`) references `groups`(id) on delete cascade
);

CREATE TABLE turned_in_assignments (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    assigned_assignment INT NOT NULL,
    student INT NOT NULL,
    submitted_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    was_corrected BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (student) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_assignment) REFERENCES assigned_assignments(id) ON DELETE CASCADE,
    UNIQUE KEY ux_assigned_student (assigned_assignment, student)
);

CREATE TABLE student_answers (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    turned_in_assignment INT NOT NULL,
    student INT NOT NULL,
    text_content TEXT,
    FOREIGN KEY (turned_in_assignment) REFERENCES turned_in_assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student) REFERENCES users(id) ON DELETE CASCADE
);

create table assignments_returns(
    turned_in_assignment int not null primary key,
    student int not null,
    returned_date datetime not null default current_timestamp,
    message text,
    calification int not null,
    foreign key(turned_in_assignment, student) references turned_in_assignments(assigned_assignment, student) on delete cascade
);

-- FILES
create table files(
	id int auto_increment primary key,
    storage_name varchar(255) not null,
    original_name varchar(255) not null,
    mime varchar(100) NOT NULL,
    extension varchar(20),
    size bigint not null,
    uploader_id int not null,
    uploaded_at datetime default current_timestamp,
    is_active bool default true,
    foreign key (uploader_id) references users(id) on delete cascade
);

create table public_materials(
	id int not null primary key auto_increment,
    title varchar(128) not null,
    `description` text,
    uploaded_date datetime not null default current_timestamp
);

-- FILE REFERENCES TABLES
CREATE TABLE students_answers_files (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_answer INT NOT NULL,
    `file` INT NOT NULL,
    FOREIGN KEY (student_answer) REFERENCES student_answers(id) ON DELETE CASCADE,
    FOREIGN KEY (`file`) REFERENCES files(id) ON DELETE CASCADE
);

create table public_materials_files(
    public_material int not null,
    `file` int not null,
    foreign key (public_material) references public_materials(id) on delete cascade,
    foreign key (`file`) references files(id) on delete cascade,
    primary key (public_material, `file`)
);

create table assigned_assignments_files(
    assigned_assignment int not null,
    `file` int not null,
    foreign key (assigned_assignment) references assigned_assignments(id) on delete cascade,
    foreign key (`file`) references files(id) on delete cascade,
    primary key (`file`, assigned_assignment)
);

create table assignments_returns_files(
    assignment_return int not null,
    `file` int not null,
    foreign key (assignment_return) references assignments_returns(turned_in_assignment) on delete cascade,
    foreign key (`file`) references files(id) on delete cascade,
    primary key (assignment_return, `file`)
);

-- GAMES THINGS
create table games(
	id int not null primary key auto_increment,
    `name` varchar(128) not null,
    `description` varchar(256),
    is_deleted bool not null default false
);

create table games_score(
	`user` int not null,
	played_difficulty enum('easy', 'medium', 'hard', 'impossible') not null,
    game int not null,
    best_score int not null,
    made_date datetime not null default current_timestamp,
    foreign key (game) references games(id)  on delete cascade,
    foreign key (`user`) references users(id)  on delete cascade,
    primary key (`user`, played_difficulty, game)
);

-- STORED PROCEDURES

-- CREATE USER
DELIMITER //
CREATE PROCEDURE createUser(
    IN create_username  VARCHAR(30),
    IN create_email VARCHAR(320),
    IN create_display_name VARCHAR(30),
    IN create_profile_picture_url VARCHAR(255),
    IN create_password VARCHAR(255),
    IN create_role VARCHAR(10)
)
BEGIN
    DECLARE user_id INT;
    DECLARE randomCodeBasic INT;
    DECLARE randomCodeIntermediate INT;
    DECLARE randomCodeAdvanced INT;
    DECLARE code_exists INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO users(username, email, display_name, profile_picture_url, password, role, is_deleted)
    VALUES (create_username, create_email, create_display_name, create_profile_picture_url, create_password, create_role, FALSE);
    
    SET user_id = LAST_INSERT_ID();

    IF LOWER(create_role) = 'guest' THEN
        INSERT INTO guests(`user`) VALUES (user_id);
    ELSEIF LOWER(create_role) = 'student' THEN
        INSERT INTO students(`user`) VALUES (user_id);
    ELSEIF LOWER(create_role) = 'teacher' THEN
        INSERT INTO teachers(`user`) VALUES (user_id);

        -- Unique code for basic code group
        REPEAT
            SET randomCodeBasic = FLOOR(100000 + RAND() * 900000);
            SELECT COUNT(*) INTO code_exists FROM `groups` WHERE code = randomCodeBasic;
        UNTIL code_exists = 0 END REPEAT;
        CALL `createGroup`('Básico', randomCodeBasic, user_id);
        
        -- Unique code for intermediate group
        REPEAT
            SET randomCodeIntermediate = FLOOR(100000 + RAND() * 900000);
            SELECT COUNT(*) INTO code_exists FROM `groups` WHERE code = randomCodeIntermediate;
        UNTIL code_exists = 0 END REPEAT;
        CALL `createGroup`('Intermedio', randomCodeIntermediate, user_id);
        
        -- Unique code for advanced group
        REPEAT
            SET randomCodeAdvanced = FLOOR(100000 + RAND() * 900000);
            SELECT COUNT(*) INTO code_exists FROM `groups` WHERE code = randomCodeAdvanced;
        UNTIL code_exists = 0 END REPEAT;
        CALL `createGroup`('Avanzado', randomCodeAdvanced, user_id);
    ELSEIF LOWER(create_role) = 'admin' THEN
        INSERT INTO admins(`user`) VALUES (user_id);
    END IF;

    COMMIT;
END //

-- USER PHYSICAL DELETE
CREATE PROCEDURE fDeleteUser(
	IN user_id int
)
BEGIN
    DELETE FROM users
    WHERE id = user_id;
END //

-- USER LOGICAL DELETE
CREATE PROCEDURE lDeleteUser(
	IN user_id  int
)
BEGIN
    UPDATE users 
    SET is_deleted = true
    WHERE id = user_id;
END //

-- MODIFY USER
CREATE PROCEDURE modifyUser(
    IN user_id int,
    IN new_username VARCHAR(30),
    IN new_email VARCHAR(320),
    IN new_display_name VARCHAR(30),
    IN new_profile_picture_url VARCHAR(255),
    IN new_password VARCHAR(255)
)
BEGIN
    IF EXISTS (SELECT 1 FROM users WHERE id = user_id) THEN
        UPDATE users
        SET 
        username = new_username,
        email = new_email,
        display_name = new_display_name,
        profile_picture_url = new_profile_picture_url,
        `password` = new_password
        WHERE id = user_id;
    END IF;
END //

-- GET ALL USERS
CREATE PROCEDURE getAllUsers()
BEGIN
	SELECT * FROM users
	WHERE is_deleted=0;
END //

-- GET USER BY ID
CREATE PROCEDURE getUserById(
	IN user_id int
)
BEGIN
	SELECT * FROM users WHERE id = user_id;
END //

-- GET USER BY USERNAME
CREATE PROCEDURE getUserByUsername(
	IN user_username VARCHAR(30)
)
BEGIN
	SELECT * FROM users WHERE username = user_username;
END //

-- GET USER BY EMAIL
CREATE PROCEDURE getUserByEmail(
	IN user_email VARCHAR(320)
)
BEGIN
	SELECT * FROM users WHERE email = user_email;
END //

-- CREATE GROUP
CREATE PROCEDURE createGroup(
    IN create_name varchar(45),
    IN create_code varchar(6),
    IN teacher_id int
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
        INSERT INTO `groups`(`name`, `code`, `teacher`)
        VALUES (create_name, create_code, teacher_id);
    COMMIT;
END //

-- GROUP PHYSICAL DELETE
CREATE PROCEDURE fDeleteGroup(
	IN group_id int 
)
BEGIN
    DELETE FROM `groups`
    WHERE id = group_id;
END //

-- MODIFY GROUP
CREATE PROCEDURE modifyGroup (
	IN group_id int,
	IN modify_name varchar(45)
)
BEGIN
    IF EXISTS (SELECT 1 FROM `groups` WHERE id = group_id) THEN
        UPDATE `groups`
        SET 
        `name` = modify_name
        WHERE id = group_id;
    END IF;
END //

-- GET ALL GROUPS
CREATE PROCEDURE getAllGroups()
BEGIN 
	SELECT * FROM `groups`;
END //

CREATE PROCEDURE getAllMessagesForGroup(
    IN group_id int
)
BEGIN
	SELECT * FROM  `messages` where `group` = group_id;
END //

-- CREATE ASSIGNMENT
CREATE PROCEDURE createAssignment(
    IN p_teacher_id INT,
    IN p_group_id INT,
    IN p_name VARCHAR(50),
    IN p_description TEXT,
    IN p_max_score INT,
    IN p_end_date DATETIME
)
BEGIN
    DECLARE assignment_id INT;

 DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: Transaction rolled back' AS message;
    END;

    START TRANSACTION;

    INSERT INTO `assignments` (`teacher`, `name`, `description`, `max_score`)
    VALUES (p_teacher_id, p_name, p_description, p_max_score);

    SET assignment_id = LAST_INSERT_ID();

    INSERT INTO `assigned_assignments` (`teacher`, `assignment`, `group`, `end_date`)
    VALUES (p_teacher_id, assignment_id, p_group_id, p_end_date);

    COMMIT;
END //

-- CREATE REFRESH TOKEN
CREATE PROCEDURE createRefreshToken(

 IN create_user_id INT,
 IN create_refresh_token VARCHAR(255),
 IN create_expires_at DATETIME
)
BEGIN
 DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
    START TRANSACTION;
    INSERT INTO tokens(user_id,refresh_token,expires_at)
    VALUES (create_user_id, create_refresh_token, create_expires_at);
    
	COMMIT;
 END //

-- GET REFRESH TOKEN BY TOKEN
CREATE PROCEDURE getRefreshTokenByToken(
    IN token_value VARCHAR(255)
)
BEGIN
    SELECT user_id, refresh_token, expires_at 
    FROM tokens 
    WHERE refresh_token = token_value AND is_revoked = 0;
END //

CREATE PROCEDURE revokeTokenByToken(
    IN token_value VARCHAR(255)
)
BEGIN
    UPDATE tokens SET is_revoked = 1 WHERE refresh_token = token_value;
END //

CREATE PROCEDURE createFile(
    IN storage_name VARCHAR(255),
    IN original_name VARCHAR(255),
    IN mime VARCHAR(100),
    IN extension VARCHAR(20),
    IN size bigint,
    IN uploader_id int
)
BEGIN
 DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    INSERT INTO files (storage_name, original_name, mime, extension, size, uploader_id)
    VALUES (storage_name, original_name, mime, extension, size, uploader_id);

    COMMIT;
END //

CREATE PROCEDURE createPublicMaterial(
    IN title VARCHAR(128),
    IN `description` text
)
BEGIN
 DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    INSERT INTO public_materials (title, `description`)
    VALUES (title, `description`);

    COMMIT;
END //

CREATE PROCEDURE createPublicMaterialFile(
    IN public_material_id int,
    IN file_id int
)
BEGIN
 DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    INSERT INTO public_materials_files (public_material, `file`)
    VALUES (public_material_id, file_id);

    COMMIT;
END //

CREATE PROCEDURE createFullPublicMaterial(
    IN p_title VARCHAR(128),
    IN p_description TEXT,
    IN p_storage_name VARCHAR(255),
    IN p_original_name VARCHAR(255),
    IN p_mime VARCHAR(100),
    IN p_extension VARCHAR(20),
    IN p_size BIGINT,
    IN p_uploader_id INT
)
BEGIN
    DECLARE material_id INT;
    DECLARE file_id INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: Transaction rolled back' AS message;
    END;

    START TRANSACTION;

    INSERT INTO public_materials (title, description)
    VALUES (p_title, p_description);
    SET material_id = LAST_INSERT_ID();

    INSERT INTO `files` (storage_name, original_name, mime, extension, size, uploader_id)
    VALUES (p_storage_name, p_original_name, p_mime, p_extension, p_size, p_uploader_id);
    SET file_id = LAST_INSERT_ID();

    INSERT INTO public_materials_files (public_material, file)
    VALUES (material_id, file_id);

    COMMIT;
END //

CREATE PROCEDURE createFullAssignment(
    IN p_teacher_id INT,
    IN p_group_id INT,
    IN p_name VARCHAR(50),
    IN p_description TEXT,
    IN p_max_score INT,
    IN p_end_date DATETIME,
    IN p_storage_name VARCHAR(255),
    IN p_original_name VARCHAR(255),
    IN p_mime VARCHAR(100),
    IN p_extension VARCHAR(50),
    IN p_size BIGINT
)
BEGIN
    DECLARE assignment_id INT;
    DECLARE assigned_assignment_id INT;
    DECLARE file_id INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: Transaction rolled back' AS message;
    END;

    START TRANSACTION;

    INSERT INTO `assignments` (`teacher`, `name`, `description`, `max_score`)
    VALUES (p_teacher_id, p_name, p_description, p_max_score);

    SET assignment_id = LAST_INSERT_ID();

    INSERT INTO `assigned_assignments` (`teacher`, `assignment`, `group`, `end_date`)
    VALUES (p_teacher_id, assignment_id, p_group_id, p_end_date);

    SET assigned_assignment_id = LAST_INSERT_ID();

    INSERT INTO `files` (`storage_name`, `original_name`, `mime`, `extension`, `size`, `uploader_id`)
    VALUES (p_storage_name, p_original_name, p_mime, p_extension, p_size, p_teacher_id);

    SET file_id = LAST_INSERT_ID();

    INSERT INTO `assigned_assignments_files` (`assigned_assignment`, `file`)
    VALUES (assigned_assignment_id, file_id);

    COMMIT;
END //

CREATE PROCEDURE fDeleteMaterial(
    IN p_material_id INT
)
BEGIN
    DECLARE file_id INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: Transaction rolled back' AS message;
    END;

    START TRANSACTION;

    DELETE f
    FROM `files` f
    INNER JOIN public_materials_files pmf ON f.id = pmf.`file`
    WHERE pmf.public_material = p_material_id;

    DELETE FROM public_materials_files WHERE public_material = p_material_id;

    DELETE FROM `public_materials` WHERE id = p_material_id;

    COMMIT;

    SELECT 'Material and associated file deleted successfully' AS message;
END //

CREATE PROCEDURE createMaterialFile(
    IN p_material_id INT,
    IN p_storage_name VARCHAR(255),
    IN p_original_name VARCHAR(255),
    IN p_mime VARCHAR(100),
    IN p_extension VARCHAR(20),
    IN p_size BIGINT,
    IN p_uploader_id INT
)
BEGIN
    DECLARE file_id INT;

    INSERT INTO `files` (storage_name, original_name, mime, extension, size, uploader_id)
    VALUES (p_storage_name, p_original_name, p_mime, p_extension, p_size, p_uploader_id);
    SET file_id = LAST_INSERT_ID();

    INSERT INTO public_materials_files (public_material, file)
    VALUES (p_material_id, file_id);
END //

CREATE PROCEDURE fDeleteFile(
    IN p_file_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: Transaction rolled back' AS message;
    END;

    START TRANSACTION;

    DELETE FROM public_materials_files WHERE `file` = p_file_id;

    DELETE FROM `files` WHERE id = p_file_id;

    COMMIT;

    SELECT 'File deleted successfully' AS message;
END //

CREATE PROCEDURE modifyMaterial(
    IN p_material_id INT,
    IN p_title VARCHAR(128),
    IN p_description TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: Transaction rolled back' AS message;
    END;

    START TRANSACTION;

    UPDATE `public_materials`
    SET title = p_title,
        `description` = p_description
    WHERE id = p_material_id;

    COMMIT;
END //

CREATE PROCEDURE getGroupsOfTeacher(
    IN p_user_id INT
)
BEGIN
    SELECT g.*, (SELECT COUNT(*) FROM `students` AS s2 WHERE s2.`group` = g.`id`) AS totalStudents FROM `groups` AS g WHERE g.`teacher` = p_user_id;
END //

CREATE PROCEDURE getGroupMembers(
    IN p_group_id INT
)
BEGIN
    SELECT u.id, u.display_name AS displayName, u.profile_picture_url AS profilePicture FROM `users` AS u JOIN `students` AS s ON u.id = s.`user` JOIN `groups` As g ON s.`group` = g.id WHERE g.id = p_group_id;
END //

CREATE PROCEDURE getStudentGroup(
    IN p_user_id INT
)
BEGIN
    SELECT * FROM `groups` AS g JOIN `students` AS s ON g.id = s.`group` WHERE s.`user` = p_user_id;
END //

CREATE PROCEDURE getAssignmentsFromGroup(
    IN p_group_id INT
)
BEGIN
    SELECT a.`id` AS `id`, a.`name` AS `name`, a.`description` AS `description`, a.max_score AS maxScore, aa.end_date AS dueDate, aa.`is_deleted` AS 'isNotActive' FROM `assignments` AS a JOIN `assigned_assignments` AS aa ON a.id = aa.`assignment` WHERE aa.`group` = p_group_id;
END //

CREATE PROCEDURE getAssignmentById(
    IN p_assignment_id INT,
    IN p_user_id INT
)
BEGIN
    SELECT 
        a.`id` AS `id`,
        a.`name` AS `name`,
        a.`description` AS `description`,
        a.`max_score` AS `maxScore`,
        f.`original_name` AS `originalName`,
        f.`storage_name` AS `storageName`,
        f.`size` AS `size`,
        aa.`end_date` AS `dueDate`,
        f.`uploaded_at` AS `createdAt`,
        EXISTS (
            SELECT 1 
            FROM `turned_in_assignments` AS tia
            WHERE tia.`assigned_assignment` = aa.`id`
            AND tia.`student` = p_user_id
        ) AS `turnedIn`
    FROM `assignments` AS a
    JOIN `assigned_assignments` AS aa 
        ON a.id = aa.`assignment`
    LEFT JOIN `assigned_assignments_files` AS aaf 
        ON aa.`id` = aaf.`assigned_assignment`
    LEFT JOIN `files` AS f 
        ON aaf.`file` = f.`id`
    WHERE a.`id` = p_assignment_id 
      AND (f.`is_active` = TRUE OR f.`id` IS NULL);
END //

CREATE PROCEDURE getGroup(
    IN p_group_id INT
)
BEGIN
    SELECT * FROM `groups` WHERE id = p_group_id;
END //

CREATE PROCEDURE getAllPublicMaterials()
BEGIN
    SELECT pm.`id` AS `id`, pm.`title` AS `name`, pm.`description` AS `description`, pm.`uploaded_date` AS `createdDate`, f.`extension` AS `type`
    FROM `public_materials` AS pm JOIN `public_materials_files` AS pmf ON pm.id = pmf.`public_material`
    JOIN `files` AS f ON pmf.`file` = f.id;
END //

CREATE PROCEDURE getMaterialById(
    IN p_material_id INT
)
BEGIN
    SELECT pm.`title` AS `name`, pm.`description` AS `description`, pm.`uploaded_date` AS `createdDate`, f.`original_name` AS `originalName`, f.`extension` AS `type`, f.`storage_name` AS `storageName`
    FROM `public_materials` AS pm JOIN `public_materials_files` AS pmf ON pm.id = pmf.`public_material`
    JOIN `files` AS f ON pmf.`file` = f.id WHERE pm.`id` = p_material_id;
END //

CREATE PROCEDURE lDeactivateAssignment(
    IN p_assignment_id INT
)
BEGIN
    UPDATE `assigned_assignments` SET `is_deleted` = true WHERE `assignment` = p_assignment_id;
END //

CREATE PROCEDURE modifyAssignment(
    IN p_assignment_id INT,
    IN p_name VARCHAR(50),
    IN p_description TEXT,
    IN p_max_score INT,
    IN p_end_date DATETIME
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: Transaction rolled back' AS message;
    END;

    START TRANSACTION;

    UPDATE `assignments` AS a SET a.`name` = p_name, a.`description` = p_description, a.`max_score` = p_max_score WHERE a.`id` = p_assignment_id;

    UPDATE `assigned_assignments` AS aa SET aa.`end_date` = p_end_date WHERE aa.`assignment` = p_assignment_id;

    COMMIT;
END //

CREATE PROCEDURE createAssignmentFile(
    IN p_assignment_id INT,
    IN p_storage_name VARCHAR(255),
    IN p_original_name VARCHAR(255),
    IN p_mime VARCHAR(100),
    IN p_extension VARCHAR(20),
    IN p_size BIGINT,
    IN p_uploader_id INT
)
BEGIN
    DECLARE file_id INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: Transaction rolled back' AS message;
    END;

    START TRANSACTION;

    INSERT INTO `files` (`storage_name`, `original_name`, `mime`, `extension`, `size`, `uploader_id`)
    VALUES (p_storage_name, p_original_name, p_mime, p_extension, p_size, p_uploader_id);
    SET file_id = LAST_INSERT_ID();

    INSERT INTO `assigned_assignments_files` (assigned_assignment, `file`)
    VALUES (p_assignment_id, file_id);

    COMMIT;
END //

CREATE PROCEDURE turnInAssignment(
    IN p_assignment_id INT,
    IN p_student_id INT,
    IN p_student_text TEXT
)
BEGIN
    DECLARE assigned_assignment_id INT;
    DECLARE turned_in_assignment_id INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error al entregar la tarea';
    END;

    START TRANSACTION;

    SELECT aa.id INTO assigned_assignment_id FROM assigned_assignments AS aa WHERE aa.assignment = p_assignment_id LIMIT 1;

    IF assigned_assignment_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Assigned assignment no encontrado';
    END IF;

    INSERT INTO turned_in_assignments (assigned_assignment, student)
    VALUES (assigned_assignment_id, p_student_id);

    SET turned_in_assignment_id = LAST_INSERT_ID();

    INSERT INTO student_answers (turned_in_assignment, student, text_content)
    VALUES (turned_in_assignment_id, p_student_id, p_student_text);

    COMMIT;
END //

CREATE PROCEDURE turnInAssignmentWithFile(
    IN p_assignment_id INT,
    IN p_student_id INT,
    IN p_student_text TEXT,
    IN p_storage_name VARCHAR(255),
    IN p_original_name VARCHAR(255),
    IN p_mime VARCHAR(100),
    IN p_extension VARCHAR(20),
    IN p_size BIGINT
)
BEGIN
    DECLARE file_id INT;
    DECLARE assigned_assignment_id INT;
    DECLARE turned_in_assignment_id INT;
    DECLARE student_answer_id INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error al entregar la tarea';
    END;

    START TRANSACTION;

    SELECT aa.id INTO assigned_assignment_id
    FROM assigned_assignments AS aa
    WHERE aa.assignment = p_assignment_id
    LIMIT 1;

    INSERT INTO turned_in_assignments (assigned_assignment, student)
    VALUES (assigned_assignment_id, p_student_id);
    SET turned_in_assignment_id = LAST_INSERT_ID();

    INSERT INTO student_answers (turned_in_assignment, student, text_content)
    VALUES (turned_in_assignment_id, p_student_id, p_student_text);
    SET student_answer_id = LAST_INSERT_ID();

    INSERT INTO files (storage_name, original_name, mime, extension, size, uploader_id)
    VALUES (p_storage_name, p_original_name, p_mime, p_extension, p_size, p_student_id);
    SET file_id = LAST_INSERT_ID();

    INSERT INTO students_answers_files (student_answer, `file`)
    VALUES (student_answer_id, file_id);

    COMMIT;
END //

CREATE PROCEDURE getTurnedInAssignmentsFromAssignment(
    IN p_assignment_id INT
)
BEGIN
    SELECT 
        tia.`id`,
        u.`display_name` AS `displayName`,
        tia.`was_corrected` AS `isCorrected`,
        tia.`submitted_date` AS `submittedDate`,
        COUNT(f.`id`) AS `filesCount`
    FROM `turned_in_assignments` AS tia
    JOIN `student_answers` AS sa ON tia.`id` = sa.`turned_in_assignment`
    JOIN `users` AS u ON sa.`student` = u.`id`
    LEFT JOIN `students_answers_files` AS saf ON sa.`id` = saf.`student_answer`
    LEFT JOIN `files` AS f ON saf.`file` = f.`id`
    WHERE tia.`assigned_assignment` = p_assignment_id
    GROUP BY tia.`id`, sa.`id`, tia.`was_corrected`, tia.`submitted_date`;
END //

CREATE PROCEDURE getStudentAnswerByTurnedInAssginment(
    IN p_turned_in_assignment_id INT
)
BEGIN
    SELECT
        tia.`submitted_date` AS `submittedDate`,
        tia.`was_corrected` AS `isCorrected`,
        a.`name` AS `assignmentName`,
        sa.`text_content` AS `studentTextResponse`,
        u.`display_name` AS `studentDisplayName`,
        f.`original_name` AS `fileOriginalName`,
        f.`storage_name` AS `fileStorageName`,
        f.`uploaded_at` AS `createdAt`,
        f.`size` AS `fileSize`
    FROM `turned_in_assignments` AS tia
    JOIN `assigned_assignments` AS aa ON tia.`assigned_assignment` = aa.`id`
    JOIN `assignments` AS a ON aa.`assignment` = a.`id`
    JOIN `student_answers` AS sa ON tia.`id` = sa.`turned_in_assignment`
    JOIN `users` AS u ON sa.`student` = u.`id`
    LEFT JOIN `students_answers_files` AS saf ON sa.`id` = saf.`student_answer`
    LEFT JOIN `files` AS f ON saf.`file` = f.`id`
    WHERE tia.`id` = p_turned_in_assignment_id;
END //
DELIMITER ;