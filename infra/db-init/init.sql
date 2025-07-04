drop database if exists eskua_db;
create database if not exists eskua_db;
use eskua_db;

create table users (
    user_id int auto_increment primary key,
    username varchar(30) unique not null,
    email varchar(320) unique not null,
    display_name varchar(30) not null,
    profile_picture varchar(255) not null,
    password varchar(255) not null,
    google_id varchar(255) unique
);

create table user_roles(
	user_id int not null primary key,
    role_type varchar(20) not null,
    foreign key (user_id) references users(user_id)
);

create table guests (
    guest_id int auto_increment primary key,
    user_id int not null,
    foreign key (user_id) references users(user_id)
);

create table students (
    student_id int auto_increment primary key,
    user_id int not null,
    group_id int not null,
    foreign key (user_id) references users(user_id)
);

create table teachers (
    teacher_id int auto_increment primary key,
    user_id int not null,
    foreign key (user_id) references users(user_id)
);

create table admins (
    admin_id int primary key auto_increment,
    user_id int not null,
    foreign key (user_id) references users(user_id)
);

create table users_groups (
    group_id int auto_increment primary key,
    teacher_id int not null,
    group_name varchar(45) not null,
    code varchar(6) not null check (char_length(code) = 6),
    foreign key (teacher_id) references teachers(teacher_id)
);

alter table students add foreign key (group_id) references users_groups(group_id);

create table messages (
    group_id int,
    message_id int,
    user_id int not null,
    sent_text text not null,
    delivery_time datetime not null default current_timestamp,
    primary key (group_id,message_id),
    foreign key (group_id) references users_groups(group_id),
    foreign key (user_id) references users(user_id)
);

create table notifications (
    notification_id int auto_increment primary key,
    user_id int not null,
    notification_message text not null,
    delivery_time datetime not null default current_timestamp,
    notification_type enum('new_assignment') not null,
    was_read bool not null,
    foreign key (user_id) references users(user_id)
);

create table assignments (
    assignment_id int auto_increment primary key,
    teacher_id int not null,
    name varchar(50) not null,
    description text not null,
    max_score int not null,
    foreign key (teacher_id) references teachers(teacher_id)
);

create table assignments_questions (
    assignment_id int,
    question_id int,
    question_text text not null,
    question_order int not null,
    primary key (assignment_id,question_id),
    foreign key (assignment_id) references assignments(assignment_id)
);

create table questions_options (
    assignment_id int,
    question_id int,
    option_id int,
    option_text text not null,
    is_correct bool not null,
    option_order int not null,
    primary key (assignment_id,question_id,option_id),
    foreign key (assignment_id,question_id)
        references assignments_questions(assignment_id,question_id)
);

create table assigned_assignments (
    assigned_assignment_id int auto_increment primary key,
    teacher_id int not null,
    assignment_id int,
    group_id int not null,
    from_date datetime not null,
    to_date datetime not null,
    foreign key (teacher_id) references teachers(teacher_id),
    foreign key (assignment_id) references assignments(assignment_id),
    foreign key (group_id) references users_groups(group_id),
    check (to_date > from_date)
);

create table turned_in_assignments (
    assigned_assignment_id int,
    student_id int,
    calification decimal(4,1) not null,
    submitted_date datetime not null,
    primary key (assigned_assignment_id,student_id),
    foreign key (assigned_assignment_id)
        references assigned_assignments(assigned_assignment_id),
    foreign key (student_id) references students(student_id)
);

create table students_answers(
    assigned_assignment_id int,
    assignment_id int,
    question_id int,
    student_id int,
    chosen_option int not null,
    primary key (assigned_assignment_id, question_id, student_id),
    foreign key (assigned_assignment_id) references assigned_assignments(assigned_assignment_id),
    foreign key (assignment_id, question_id) references assignments_questions(assignment_id, question_id),
    foreign key (assignment_id, question_id, chosen_option) references questions_options(assignment_id, question_id, option_id),
    foreign key (student_id) references students(student_id)
);

create table games (
    game_id int auto_increment primary key,
    name varchar(100) not null,
    description text not null
);

create table game_scores (
    game_id int,
    user_id int,
    played_difficulty enum('easy','medium','hard'),
    best_score int not null,
    made_date date not null,
    primary key (game_id,user_id,played_difficulty),
    foreign key (game_id) references games(game_id),
    foreign key (user_id) references users(user_id)
);