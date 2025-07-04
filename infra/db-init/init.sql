use eskua_db;

insert into users(username,email,display_name,profile_picture,password) values
('alice','alice@example.com','Alice','alice.png','passHash1'),
('bob','bob@example.com','Bob','bob.jpg','passHash2'),
('charlie','charlie@example.com','Charlie','charlie.png','passHash3'),
('diana','diana@example.com','Diana','diana.jpg','passHash4'),
('edward','edward@example.com','Edward','edward.png','passHash5');

insert into user_roles(user_id,role_type) values
(1,'admin'),
(2,'teacher'),
(3,'student'),
(4,'guest'),
(5,'teacher');

insert into guests(user_id) values
(4);

insert into teachers(user_id) values
(2),
(5);

insert into admins(user_id) values
(1);

insert into users_groups(teacher_id,group_name,code) values
(1,'Grupo A','GRUPOA'),
(2,'Grupo B','GRUPOB');

insert into students(user_id,group_id) values
(3,1);

insert into messages(group_id,message_id,user_id,sent_text) values
(1,1,2,'Chicos, como se hace hola en el lenguaje de señas este?'),
(1,2,3,'No funciona nada che'),
(2,1,5,'Me pidió que mande un mensaje');

insert into notifications(user_id,notification_message,notification_type,was_read) values
(3,'Tienes una nueva tarea asignada','new_assignment',false),
(2,'Tarea corregida','new_assignment',true),
(5,'Nueva asignación disponible','new_assignment',false),
(1,'Asignación actualizada','new_assignment',true);

insert into assignments(teacher_id,name,description,max_score) values
(1,'Álgebra I','Ejercicios de álgebra básica',100),
(2,'Historia','Ensayo sobre la Revolución Francesa',100);

insert into assignments_questions(assignment_id,question_id,question_text,question_order) values
(1,1,'¿Cuál es la resolución de 2+2?',1),
(1,2,'Factoriza x^2 - 5x + 6',2),
(2,1,'¿En qué año empezó la Revolución Francesa?',1),
(2,2,'Menciona tres causas de la Revolución.',2);

insert into questions_options(assignment_id,question_id,option_id,option_text,is_correct,option_order) values
(1,1,1,'4',true,1),
(1,2,1,'(x-2)(x-3)',true,1),
(2,1,1,'1789',true,1),
(2,2,1,'Desigualdad social',true,1);

insert into assigned_assignments(teacher_id,assignment_id,group_id,from_date,to_date) values
(1,1,1,'2025-06-01 08:00:00','2025-06-15 23:59:59'),
(2,2,2,'2025-06-05 09:00:00','2025-06-20 23:59:59');

insert into turned_in_assignments(assigned_assignment_id,student_id,calification,submitted_date) values
(1,1,95.0,'2025-06-14 14:30:00');

insert into students_answers(assigned_assignment_id,assignment_id,question_id,student_id,chosen_option) values
(1,1,1,1,1),
(1,1,2,1,1);

insert into games(name,description) values
('Space Invaders','Clásico juego de disparos en 2D'),
('Pac-Man','Recoge todas las píldoras evitando fantasmas');

insert into game_scores(game_id,user_id,played_difficulty,best_score,made_date) values
(1,3,'easy',5000,'2025-06-30'),
(2,2,'medium',12000,'2025-06-29');

-- consulta mensajes
select m.group_id as GroupId, m.message_id as MessageId, u.display_name as User, m.sent_text as Message, m.delivery_time as SentTime
from messages as m
join users   as u on m.user_id = u.user_id;

-- consulta de mejores puntuaciones
select g.name as Game, u.display_name as User, gs.played_difficulty as Difficulty, gs.best_score as BestScore
from game_scores as gs
join games as g on gs.game_id = g.game_id
join users as u on gs.user_id = u.user_id;