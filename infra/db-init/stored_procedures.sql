use eskua_db;

-- CREATE USER
DELIMITER //
CREATE PROCEDURE createUser(
    IN create_username  VARCHAR(30),
    IN create_email VARCHAR(320),
    IN create_display_name VARCHAR(30),
    IN create_profile_picture_url VARCHAR(255),
    IN create_password VARCHAR(255),
    IN create_role VARCHAR(10),
    OUT user_id int
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO users(username, email, display_name, profile_picture_url, password, role, is_deleted)
    VALUES (create_username, create_email, create_display_name, create_profile_picture_url, create_password, create_role, FALSE);
    
    SET user_id = LAST_INSERT_ID();

    IF create_role = 'Guest' THEN
        INSERT INTO guests(fk_user) VALUES (user_id);
    ELSEIF create_role = 'Student' THEN
        INSERT INTO students(fk_user) VALUES (user_id);
    ELSEIF create_role = 'Teacher' THEN
        INSERT INTO teachers(fk_user) VALUES (user_id);
    ELSEIF create_role = 'Admin' THEN
        INSERT INTO admins(fk_user) VALUES (user_id);
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
    IN create_code varchar(6)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
        INSERT INTO `groups`(`name`, `code`)
        VALUES (create_name, create_code);
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


CREATE PROCEDURE createMessage(
IN create_message TEXT,
IN create_sent_time  DATETIME
)
BEGIN
DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

		INSERT INTO messages(`message_text`, sent_time)
        VALUES (create_message, create_sent_time);
END//

-- MESSAGE PHYSICAL DELETE
CREATE PROCEDURE fdeleteMessage(

IN message_id int
)
BEGIN 
	DELETE FROM `messages`
    WHERE id = message_id;
END//

-- MESSAGE MODIFY

 CREATE PROCEDURE modifyMessage(
 IN  message_id int ,
 IN modifyMessage_text TEXT,
 IN modifySent_time DATETIME
 )
 BEGIN
	IF EXISTS(SELECT 1 FROM `messages` WHERE id = message_id)THEN
		UPDATE`messages` 
 
		SET `message_text`= modifyMessage_text,
			sent_time = modifySent_time 
            
            WHERE id = message_id;
	END IF;
END//

CREATE PROCEDURE GetAllMessages()
BEGIN
	SELECT * FROM  `messages`;
    END//
    
   CREATE PROCEDURE createAssignments (
   IN create_name varchar(50),
   IN create_description TEXT
   )
    BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    
    INSERT INTO assignments (`name`,`description`,is_deleted)
    VALUES (create_name, create_description ,FALSE);
    END//
    -- CREATE LOGICAL DELETE
    CREATE PROCEDURE ldeleteAssignments(
    IN lassig_id INT 
    
    )
    BEGIN
		UPDATE assignments
		SET is_deleted = true
		where id = lassig_id;
    END//
    
    -- CRETE PYSICAL DELETE
    
    CREATE PROCEDURE fdeleteAssignments(
    IN fassig_id int
    )
    BEGIN
    -- CONSULTAR
    DELETE FROM  assignments
    WHERE id = fassig_id;
    
    END//
    -- CREATE NOTIFICATIONS
    
    CREATE PROCEDURE createNotification(
    
    IN create_content varchar(200),
    IN create_send_date datetime,
    IN create_type enum('Activity', 'Message'),
    IN create_was_read bool
    )
    BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
		INSERT INTO notifications(content, sent_date, `type`, was_read)
		VALUES(create_content, create_send_date, create_type, FALSE);
    END//
    
    -- DELETE PHISICAL NOTIFICARION
    
    CREATE PROCEDURE  fDeleteNotifications(
    IN notific_id int
    )
    BEGIN
		DELETE FROM notifications
		WHERE id = notific_id;
    END//
    
    
   -- MENSAGE MODIFY
   
   CREATE PROCEDURE ModifyNotification(
   IN modify_id int,
   IN modify_content varchar(200),
   IN modify_sent_date datetime,
   IN modify_type enum('Activity', 'Message'),
   IN modify_was_read bool
   
   )
   BEGIN
   
	IF EXISTS (SELECT 1 FROM `notifications` WHERE id = modify_id ) THEN
   
	UPDATE `notifications`

	SET content= modify_content,
		`type` = modify_type;
	
    END IF;
    
END//

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
    INSERT INTO tokens(user_id,refresh_token,expires_at ,is_revoked)
    VALUES (create_user_id, create_refresh_token, create_expires_at, FALSE);
    
	COMMIT;
 END //   
DELIMITER ;

DELIMITER //
CREATE PROCEDURE getRefreshToken(
    IN token_value VARCHAR(255)
)
BEGIN
    SELECT user_id, refresh_token, expires_at 
    FROM tokens 
    WHERE refresh_token = token_value AND is_revoked = 0;
END //
DELIMITER ;


