use eskua_db;

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
DELIMITER //
CREATE PROCEDURE getRefreshTokenByToken(
    IN token_value VARCHAR(255)
)
BEGIN
    SELECT user_id, refresh_token, expires_at 
    FROM tokens 
    WHERE refresh_token = token_value AND is_revoked = 0;
END //

DELIMITER //
CREATE PROCEDURE revokeTokenByToken(
    IN token_value VARCHAR(255)
)
BEGIN
    UPDATE tokens SET is_revoked = 1 WHERE refresh_token = token_value;
END //
DELIMITER ;