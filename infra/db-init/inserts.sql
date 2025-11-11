use eskua_db;

-- USERS
call createUser('admin_master', 'admin@example.com', 'Administrador General', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$N1VEd2YuS2ZHdE1kZmJ3ag$7XzBwLCR7BACHwa9NzdThxGgrlA42c9V9zqdACejKc4', 
'Admin'); -- password: admin123

call createUser('juan123', 'juan@example.com', 'Juan Pérez', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$Mk9zeEg5YUJhSEpleS9IaA$EaTJX3mRP8L8XAIZaCkEdMlUgYcP0ScxTBhuSqWs968', 
'Teacher'); -- password: juan123

call createUser('maria.l', 'maria@example.com', 'María López', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$YzVtcGVHcjQzbmhGZEVuRg$EkZNF1ByoEq8qexYddy/QGc0SqM1mjllquXshP7obOo', 
'Student'); -- password: maria123

call createUser('sofia99', 'sofia@example.com', 'Sofía Ramírez', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$Nnl2N2puRjJLaGNNVHo0Ng$Nbxcgd/S3Rx7PPcKgLcLWAziQ2RG+opCh4zb+rvPzPY', 
'Student'); -- password: sofia123

call createUser('carlos_t', 'carlos@example.com', 'Carlos Torres', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$cjhhQWlhVW8uQy5ORldUcg$tp2j8jalctu5ARFLANrZVRgtDeIk/nKPbFZrNlf+Czw', 
'Teacher'); -- password: carlos123

call createUser('guest_01', 'guest01@example.com', 'Invitado Uno', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$ZXFlenN6NjBNLzhYRkhLOA$2Q+Li0DERL7Oqf8e0CgpTFYHWCf3SUArJgloW1IuLB0', 
'Guest'); -- password: guest123

call createUser('lucia_m', 'lucia@example.com', 'Lucía Méndez', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$TEFOMkRTOEdtRWJmUU5aag$/eg+9leZZRdha8hbJAE42+1OCFpqjzKahuVsCmDUE/k', 
'Student'); -- password: lucia123

call createUser('pedro_g', 'pedro@example.com', 'Pedro González', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$emFtaW9TWGRFcGY1Q0JnZw$NahWUI182EjOYBEnC8hMzWvr40zb1clxjod9VVUOW14', 
'Teacher'); -- password: pedro123

call createUser('ana_s', 'ana@example.com', 'Ana Suárez', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$RC9laXFEcEtEVWt4UU1zdA$rwd/F/UjjVR+nzqTiVDHjYzk821dGkIlBzE3lIUFm08', 
'Student'); -- password: ana123

call createUser('martin_dev', 'martin@example.com', 'Martín Díaz', '/images/DefaultUserProfilePicture.webp', 
'$argon2id$v=19$m=65536,t=4,p=1$SEtSdWttR2YzS1c0UlQwNw$hC1r+izswC7p0sKmQYway5V9XjGm90d57B2kIKr3R/g', 
'Admin'); -- password: martin123

update students set `group` = 2 where `user` = 3;
update students set `group` = 1 where `user` = 4;
update students set `group` = 4 where `user` = 7;
update students set `group` = 8 where `user` = 9;

-- PUBLIC MATERIALS
call createFullPublicMaterial(
  'Introducción a la LSU - Nivel 1',
  'Material inicial sobre la Lengua de Señas Uruguaya (LSU): alfabeto manual, saludos básicos y primeros ejercicios de práctica.',
  '9f1c3b4a2e6d7f8a0b1c2d3e4f5a6b7c.pdf',
  'Introducción a la LSU - Nivel 1.pdf',
  'application/pdf',
  'pdf',
  39936,
  1
);

call createFullPublicMaterial(
  'Señas básicas: saludos y presentaciones',
  'Video demostrativo de saludos, presentaciones personales y expresiones comunes en LSU. Ideal para principiantes.',
  'a3b4c5d6e7f8123456789abcdef012345.mp4',
  'Saludos_LSU.mp4',
  'video/mp4',
  'mp4',
  39936,
  10
);

call createFullPublicMaterial(
  'Fichas visuales: alfabeto manual LSU',
  'Colección de imágenes que muestran cada letra del alfabeto manual con ejemplos visuales claros.',
  'b1c2d3e4f5a6b7c890123456abcdefab.jpg',
  'Alfabeto_Manual_LSU.jpg',
  'image/jpeg',
  'jpg',
  39936,
  1
);

call createFullPublicMaterial(
  'Infografía: expresiones faciales en LSU',
  'Infografía con consejos sobre el uso de expresiones faciales para reforzar la comunicación en LSU.',
  '11223344556677889900aabbccddeeff.png',
  'Expresiones_Faciales_LSU.png',
  'image/png',
  'png',
  39936,
  10
);

call createFullPublicMaterial(
  'Guía práctica: números en LSU',
  'Documento en PDF que explica los números del 1 al 100 con ejemplos visuales y consejos de memorización.',
  'abcdef0123456789abcdef0123456789.pdf',
  'Guía_Números_LSU.pdf',
  'application/pdf',
  'pdf',
  39936,
  1
);

call createFullPublicMaterial(
  'Vocabulario: alimentos y cocina',
  'Material visual sobre vocabulario de alimentos, utensilios y acciones comunes de cocina en LSU.',
  '1234567890abcdef1234567890abcdef.webp',
  'Vocabulario_Alimentos_LSU.webp',
  'image/webp',
  'webp',
  39936,
  10
);

call createFullPublicMaterial(
  'Video: emociones y estados de ánimo',
  'Video corto que muestra cómo expresar distintas emociones en LSU, con ejemplos y repeticiones guiadas.',
  'ffeeddccbbaa99887766554433221100.mp4',
  'Emociones_LSU.mp4',
  'video/mp4',
  'mp4',
  39936,
  1
);

call createFullPublicMaterial(
  'Cartel informativo: accesibilidad en aulas LSU',
  'Imagen que muestra recomendaciones para mejorar la accesibilidad visual y espacial en clases de LSU.',
  '9876543210abcdef9876543210abcdef.png',
  'Cartel_Accesibilidad_LSU.png',
  'image/png',
  'png',
  39936,
  10
);

call createFullPublicMaterial(
  'Material PDF: frases cotidianas en LSU',
  'PDF con frases de uso común, ejemplos de conversaciones breves y vocabulario complementario.',
  'fedcba0987654321fedcba0987654321.pdf',
  'Frases_Cotidianas_LSU.pdf',
  'application/pdf',
  'pdf',
  39936,
  1
);

call createFullPublicMaterial(
  'Video guía: comunicación en contextos educativos',
  'Video explicativo sobre el uso de LSU en clases, interacción entre docentes y estudiantes, y vocabulario escolar.',
  '00aa11bb22cc33dd44ee55ff66778899.mp4',
  'LSU_Contextos_Educativos.mp4',
  'video/mp4',
  'mp4',
  39936,
  10
);