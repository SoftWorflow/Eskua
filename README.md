# ESKUA

## Pasos para cuando se clona

    1- Abrir la consola de visual studio code e ir a la carpeta services/auth_jwt.
    2- Escribir en la consola "npm install".
    3- Cuando termine ir a la carpeta infra y escribir en la consola "docker-compose build --no-cache".
    4- Cuando termine ese comando escribir este en la consola "docker-compose up -d".
    5- Para probar el sistema vas a tu navegador de confianza y escribis "http:127.0.0.1:8080/(la carpeta de la pantalla a la que quieras ir)/".

    (nota: cuando termines de trabajar, para bajar los servicios, escribí "docker-compose down" y si queres borrar todos los datos guardadoes en la base de datos es este "docker-compose down -v")

## Cosas que dependen de la IP

    En apps/api/user/register.php en la línea 115 se neceista de la IP para la imagen default

## Comandos

docker-compose up -d
docker-compose down -v
docker-compose build --no-cache
docker exec -it eskua-db mysql -u root -p
