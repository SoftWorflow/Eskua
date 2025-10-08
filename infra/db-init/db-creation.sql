drop database if exists eskua_db;

create database if not exists eskua_db;

use eskua_db;

-- USERS
create table users(
	id int primary key auto_increment,
    username varchar(30) unique not null,
    email varchar(320) unique not null check (email like '%@%'),
    display_name varchar(30) not null check (display_name regexp '^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$'),
    profile_picture_url varchar(255) not null,
    `password` varchar(255) not null,
    `role` enum('admin', 'teacher', 'student', 'guest') not null,
    is_deleted bool not null default false
);

-- TOKENS
CREATE TABLE tokens (
    id int primary key auto_increment,
    `user` int not null,
    refresh_token varchar(128) not null,
    expires_at datetime not null,
    revoked boolean not null default false,
    created_at datetime not null default current_timestamp,
    updated_at datetime not null default current_timestamp on update current_timestamp,
    foreign key (`user`) references users(id)
);

-- GUESTS
create table guests(
	`user` int not null primary key,
    foreign key (`user`) references users(id)
);

-- STUDENTS
create table students(
	`user` int not null primary key,
    foreign key (`user`) references users(id)
);

-- TEACHERS
create table teachers(
	`user` int not null primary key,
    foreign key (`user`) references users(id)
);

-- ADMINS
create table admins(
	`user` int not null primary key,
    foreign key (`user`) references users(id)
);

-- GROUPS
create table `groups`(
	id int primary key auto_increment,
    teacher int not null,
    `name` varchar(45) not null,
    `code` varchar(6) not null,
    foreign key (teacher) references users(id)
);

-- ADDED THE GROUP REFERENCE IN THE STUDENTS TABLE
alter table students
add column `group` int not null,
add constraint `students_group`
    foreign key (`group`) REFERENCES `groups`(id);

-- MESSAGES
create table messages(
	id int not null auto_increment,
    `group` int not null,
    `user` int not null,
    `message_text` text not null,
    sent_time datetime not null default current_timestamp,
    foreign key (`group`) references `groups`(id),
    foreign key (`user`) references users(id),
    primary key (id, `group`)
);
-- NORIFICATIONS
create table notifications(
	id int not null unique auto_increment,
    user int not null,
    content varchar(200) not null,
    sent_date datetime not null default current_timestamp,
    `type` enum('activity', 'message') not null,
    was_read bool not null default false,
    foreign key (`user`) references users(id),
    primary key (id, `user`)
);

-- ASSIGNMENT THINGS
create table assignments(
	id int primary key auto_increment,
    teacher int not null,
    `name` varchar(50) not null,
    `description` text not null,
    max_score int not null,
    is_deleted bool not null default false,
    foreign key (teacher) references users(id)
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
    foreign key (teacher) references users(id),
    foreign key (assignment) references assignments(id),
    foreign key (`group`) references `groups`(id)
);

create table turned_in_assignments(
	assigned_assignment int not null,
    student int not null,
    submited_date datetime not null default current_timestamp,
    was_corrected bool not null,
    foreign key (student) references users(id),
    primary key (assigned_assignment, student)
);

create table student_answers(
	id int not null,
    turned_in_assignment int not null,
    student int not null,
    text_content text,
    foreign key (turned_in_assignment, student) references turned_in_assignments(assigned_assignment, student),
    primary key (id, turned_in_assignment)
);

create table assignments_returns(
    turned_in_assignment int not null primary key,
    student int not null,
    returned_date datetime not null default current_timestamp,
    message text,
    calification int not null,
    foreign key(turned_in_assignment, student) references turned_in_assignments(assigned_assignment, student)
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
    checksum char(64) NULL,
    uploaded_at datetime default current_timestamp,
    is_active bool default true
);

create table public_materials(
	id int not null primary key,
    title varchar(128) not null,
    `description` text,
    uploaded_date datetime not null default current_timestamp on update current_timestamp
);

-- FILE REFERENCES TABLES
create table students_answers_files(
	id int not null auto_increment,
    student_answer int not null,
    turned_in_assignment int not null,
    `file` int not null,
    foreign key (student_answer, turned_in_assignment) references student_answers(id, turned_in_assignment),
    foreign key (`file`) references files(id),
    primary key (id, student_answer, turned_in_assignment)
);

create table public_materials_files(
	id int not null auto_increment,
    public_material int not null,
    `file` int not null,
    foreign key (public_material) references public_materials(id),
    foreign key (`file`) references files(id),
    primary key (id, public_material)
);

create table assignments_returns_files(
	id int not null auto_increment,
    assignment_return int not null,
    `file` int not null,
    foreign key (assignment_return) references assignments_returns(turned_in_assignment),
    foreign key (`file`) references files(id),
    primary key (id, assignment_return)
);

-- GAMES THINGS
create table games(
	id int not null primary key,
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
    primary key (`user`, played_difficulty, game)
);