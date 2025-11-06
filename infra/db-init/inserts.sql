use eskua_db;

-- CREACIÓN DE USUARIOS USANDO createUser()
call createUser('admin_master', 'admin@example.com', 'Administrador General', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$N1VEd2YuS2ZHdE1kZmJ3ag$7XzBwLCR7BACHwa9NzdThxGgrlA42c9V9zqdACejKc4', 
'Admin'); -- password: admin123

call createUser('juan123', 'juan@example.com', 'Juan Pérez', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$Mk9zeEg5YUJhSEpleS9IaA$EaTJX3mRP8L8XAIZaCkEdMlUgYcP0ScxTBhuSqWs968', 
'Teacher'); -- password: juan123

call createUser('maria.l', 'maria@example.com', 'María López', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$YzVtcGVHcjQzbmhGZEVuRg$EkZNF1ByoEq8qexYddy/QGc0SqM1mjllquXshP7obOo', 
'Student'); -- password: maria123

call createUser('sofia99', 'sofia@example.com', 'Sofía Ramírez', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$Nnl2N2puRjJLaGNNVHo0Ng$Nbxcgd/S3Rx7PPcKgLcLWAziQ2RG+opCh4zb+rvPzPY', 
'Student'); -- password: sofia123

call createUser('carlos_t', 'carlos@example.com', 'Carlos Torres', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$cjhhQWlhVW8uQy5ORldUcg$tp2j8jalctu5ARFLANrZVRgtDeIk/nKPbFZrNlf+Czw', 
'Teacher'); -- password: carlos123

call createUser('guest_01', 'guest01@example.com', 'Invitado Uno', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$ZXFlenN6NjBNLzhYRkhLOA$2Q+Li0DERL7Oqf8e0CgpTFYHWCf3SUArJgloW1IuLB0', 
'Guest'); -- password: guest123

call createUser('lucia_m', 'lucia@example.com', 'Lucía Méndez', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$TEFOMkRTOEdtRWJmUU5aag$/eg+9leZZRdha8hbJAE42+1OCFpqjzKahuVsCmDUE/k', 
'Student'); -- password: lucia123

call createUser('pedro_g', 'pedro@example.com', 'Pedro González', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$emFtaW9TWGRFcGY1Q0JnZw$NahWUI182EjOYBEnC8hMzWvr40zb1clxjod9VVUOW14', 
'Teacher'); -- password: pedro123

call createUser('ana_s', 'ana@example.com', 'Ana Suárez', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$RC9laXFEcEtEVWt4UU1zdA$rwd/F/UjjVR+nzqTiVDHjYzk821dGkIlBzE3lIUFm08', 
'Student'); -- password: ana123

call createUser('martin_dev', 'martin@example.com', 'Martín Díaz', 'https://eskua.com.uy/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$SEtSdWttR2YzS1c0UlQwNw$hC1r+izswC7p0sKmQYway5V9XjGm90d57B2kIKr3R/g', 
'Admin'); -- password: martin123

update students set `group` = 2 where `user` = 3;
update students set `group` = 1 where `user` = 4;
update students set `group` = 4 where `user` = 7;
update students set `group` = 8 where `user` = 9;