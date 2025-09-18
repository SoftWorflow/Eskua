drop database if exists eskua_db;

create database if not exists eskua_db;

use eskua_db;

-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS
-- CREAR TABLAS DE MATERIAL PUBLICO Y LA DE DEVOLUCION DE LAS TAREAS

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

CREATE TABLE tokens (
    id int primary key auto_increment,
    user_id int not null,
    refresh_token varchar(128) not null,
    expires_at datetime not null,
    revoked boolean not null default false,
    created_at datetime not null default current_timestamp,
    updated_at datetime not null default current_timestamp on update current_timestamp,
    foreign key (user_id) references users(id)
);

create table guests(
	fk_user int not null primary key,
    foreign key (fk_user) references users(id)
);

create table students(
	fk_user int not null primary key,
    foreign key (fk_user) references users(id)
);

create table teachers(
	fk_user int not null primary key,
    foreign key (fk_user) references users(id)
);

create table admins(
	fk_user int not null primary key,
    foreign key (fk_user) references users(id)
);

create table `groups`(
	id int primary key auto_increment,
    fk_teacher int not null,
    `name` varchar(45) not null,
    `code` varchar(6) not null,
    foreign key (fk_teacher) references users(id)
);

alter table students
add column fk_group int not null,
add constraint fk_students_group
    foreign key (fk_group) REFERENCES `groups`(id);

create table messages(
	id int not null auto_increment,
    fk_group int not null,
    fk_user int not null,
    `message_text` text not null,
    sent_time datetime not null default current_timestamp,
    foreign key (fk_group) references `groups`(id),
    foreign key (fk_user) references users(id),
    primary key (id, fk_group)
);

create table notifications(
	id int not null unique auto_increment,
    fk_user int not null,
    content varchar(200) not null,
    sent_date datetime not null default current_timestamp,
    `type` enum('activity', 'message') not null,
    was_read bool not null default false,
    foreign key (fk_user) references users(id),
    primary key (id, fk_user)
);

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
    is_active bool DEFAULT true
);

create table file_relations (
	id int auto_increment primary key,
    file_id int not null,
    entity_type enum('material', 'assignment', 'submission') not null,
    entity_id int not null,
    created_at datetime default current_timestamp,
    foreign key (file_id) references files(id) on delete cascade
);

create table assignments(
	id int primary key auto_increment,
    fk_teacher int not null,
    `name` varchar(50) not null,
    `description` text not null,
    max_score int not null,
    is_deleted bool not null default false,
    foreign key (fk_teacher) references users(id)
);
/*
create table assigned_assignments(
	id int primary key auto_increment,
    fk_teacher int not null,
    fk_assignment int not null,
    fk_group int not null,
    start_date datetime not null default current_timestamp,
    end_date datetime not null,
    check (start_date < end_date),
    is_deleted bool not null default false,
    foreign key (fk_teacher) references users(id),
    foreign key (fk_assignment) references assignments(id),
    foreign key (fk_group) references `groups`(id)
);*/

create table turned_in_assignments(
	fk_assignment int not null,
    fk_student int not null,
    submited_date datetime not null default current_timestamp,
    foreign key (fk_student) references users(id),
    primary key (fk_assignment, fk_student)
);  