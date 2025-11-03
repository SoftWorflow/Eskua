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
    check (start_date < end_date),
    is_deleted bool not null default false,
    foreign key (teacher) references users(id) on delete cascade,
    foreign key (assignment) references assignments(id) on delete cascade,
    foreign key (`group`) references `groups`(id) on delete cascade
);

create table turned_in_assignments(
	assigned_assignment int not null,
    student int not null,
    submited_date datetime not null default current_timestamp,
    was_corrected bool not null default false,
    foreign key (student) references users(id) on delete cascade,
    primary key (assigned_assignment, student)
);

create table student_answers(
	id int not null auto_increment,
    turned_in_assignment int not null,
    student int not null,
    text_content text,
    foreign key (turned_in_assignment, student) references turned_in_assignments(assigned_assignment, student) on delete cascade,
    primary key (id, turned_in_assignment)
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
create table students_answers_files(
    student_answer int not null,
    turned_in_assignment int not null,
    `file` int not null,
    foreign key (student_answer, turned_in_assignment) references student_answers(id, turned_in_assignment) on delete cascade,
    foreign key (`file`) references files(id) on delete cascade,
    primary key (`file`, student_answer, turned_in_assignment)
);

create table public_materials_files(
    public_material int not null,
    `file` int not null,
    foreign key (public_material) references public_materials(id) on delete cascade,
    foreign key (`file`) references files(id) on delete cascade,
    primary key (public_material, `file`)
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
   IN create_name varchar(50),
   IN create_description TEXT,
   IN teacher_id int,
   IN max_score int
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
 START TRANSACTION;
    
    INSERT INTO assignments (`name`,`description`,is_deleted)
    VALUES (create_name, create_description ,FALSE);
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

    INSERT INTO files (storage_name, original_name, mime, extension, size, uploader_id)
    VALUES (p_storage_name, p_original_name, p_mime, p_extension, p_size, p_uploader_id);
    SET file_id = LAST_INSERT_ID();

    INSERT INTO public_materials_files (public_material, file)
    VALUES (material_id, file_id);

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

    SELECT `file` INTO file_id FROM public_materials_files WHERE public_material = p_material_id;

    DELETE FROM `public_materials` WHERE id = p_material_id;

    IF file_id IS NOT NULL THEN
        DELETE FROM `files` WHERE id = file_id;
    END IF;

    COMMIT;

    SELECT 'Material and associated file deleted successfully' AS message;
END //
DELIMITER ;