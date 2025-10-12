drop database if exists eskua_db;

create database if not exists eskua_db;

use eskua_db;

-- USERS
create table users(
	id int primary key auto_increment,
    username varchar(30) unique not null,
    email varchar(255) unique not null check (email like '%@%'),
    display_name varchar(30) not null check (display_name regexp '^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$'),
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
add column `group` int not null,
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
    checksum char(64) NULL,
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