use eskua_db;

-- USERS
insert into users (username, email, display_name, profile_picture_url, `password`, `role`, is_deleted) values
('admin_master', 'admin@example.com', 'Administrador General', 'https://picsum.photos/200?1', '$argon2id$v=19$m=65536,t=4,p=1$N1VEd2YuS2ZHdE1kZmJ3ag$7XzBwLCR7BACHwa9NzdThxGgrlA42c9V9zqdACejKc4', 'Admin', false), -- password: admin123
('juan123', 'juan@example.com', 'Juan Pérez', 'https://picsum.photos/200?2', '$argon2id$v=19$m=65536,t=4,p=1$Mk9zeEg5YUJhSEpleS9IaA$EaTJX3mRP8L8XAIZaCkEdMlUgYcP0ScxTBhuSqWs968', 'Teacher', false), -- password: juan123
('maria.l', 'maria@example.com', 'María López', 'https://picsum.photos/200?3', '$argon2id$v=19$m=65536,t=4,p=1$YzVtcGVHcjQzbmhGZEVuRg$EkZNF1ByoEq8qexYddy/QGc0SqM1mjllquXshP7obOo', 'Student', false), -- password: maria123
('sofia99', 'sofia@example.com', 'Sofía Ramírez', 'https://picsum.photos/200?4', '$argon2id$v=19$m=65536,t=4,p=1$Nnl2N2puRjJLaGNNVHo0Ng$Nbxcgd/S3Rx7PPcKgLcLWAziQ2RG+opCh4zb+rvPzPY', 'Student', false), -- password: sofia123
('carlos_t', 'carlos@example.com', 'Carlos Torres', 'https://picsum.photos/200?5', '$argon2id$v=19$m=65536,t=4,p=1$cjhhQWlhVW8uQy5ORldUcg$tp2j8jalctu5ARFLANrZVRgtDeIk/nKPbFZrNlf+Czw', 'Teacher', false), -- password: carlos123
('guest_01', 'guest01@example.com', 'Invitado Uno', 'https://picsum.photos/200?6', '$argon2id$v=19$m=65536,t=4,p=1$ZXFlenN6NjBNLzhYRkhLOA$2Q+Li0DERL7Oqf8e0CgpTFYHWCf3SUArJgloW1IuLB0', 'Guest', true), -- password: guest123
('lucia_m', 'lucia@example.com', 'Lucía Méndez', 'https://picsum.photos/200?7', '$argon2id$v=19$m=65536,t=4,p=1$TEFOMkRTOEdtRWJmUU5aag$/eg+9leZZRdha8hbJAE42+1OCFpqjzKahuVsCmDUE/k', 'Student', false), -- password: lucia123
('pedro_g', 'pedro@example.com', 'Pedro González', 'https://picsum.photos/200?8', '$argon2id$v=19$m=65536,t=4,p=1$emFtaW9TWGRFcGY1Q0JnZw$NahWUI182EjOYBEnC8hMzWvr40zb1clxjod9VVUOW14', 'Teacher', false), -- password: pedro123
('ana_s', 'ana@example.com', 'Ana Suárez', 'https://picsum.photos/200?9', '$argon2id$v=19$m=65536,t=4,p=1$RC9laXFEcEtEVWt4UU1zdA$rwd/F/UjjVR+nzqTiVDHjYzk821dGkIlBzE3lIUFm08', 'Student', false), -- password: ana123
('martin_dev', 'martin@example.com', 'Martín Díaz', 'https://picsum.photos/200?10', '$argon2id$v=19$m=65536,t=4,p=1$SEtSdWttR2YzS1c0UlQwNw$hC1r+izswC7p0sKmQYway5V9XjGm90d57B2kIKr3R/g', 'Admin', false); -- password: martin123

-- GROUPS
insert into `groups` (fk_teacher, `name`, `code`) values
(2, 'Group A', '335678'),
(5, 'Group B', '146997'),
(8, 'Group C', '698874');

-- ADMINS
insert into admins (fk_user) values
(1), -- admin_master
(10); -- martin_dev

-- TEACHERS
insert into teachers (fk_user) values
(2), -- juan123
(5), -- carlos_t
(8); -- pedro_g

-- STUDENTS
insert into students (fk_user, fk_group) values
(3, 1), -- María López, Group A
(4, 1), -- Sofía Ramírez, Group A
(7, 2), -- Lucía Méndez, Group B
(9, 3); -- Ana Suárez, Group C

-- GUESTS
insert into guests (fk_user) values
(6); -- guest_01