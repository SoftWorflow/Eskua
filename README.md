# ESKUA

## Pasos para cuando se clona el repositorio

    1- Para utilizar el proyecto se neceista tener instalado: node.js (https://nodejs.org/en/download), docker (https://www.docker.com/) y visual studio code (https://code.visualstudio.com/). (Es posible que despues de la instalacion de los mismos deba de reiniciar su dispositivo)
    2- Una vez instalados ambos se tiene que abrir la nueva aplicación "Docker Desltop", una vez termine de cargar todo se puede cerrar tranquilamente.
    3- Ahora, desde la consola de Visual Studio Code (atajo: ctrl + `) tenes que ir al directorio "apps/front_php" desde la consola de visual studio code (power shell: cd .\apps\front_php\, bash: cd apps/front_php/).
    4- Una vez en ese directorio se debe de ejecutar el comando "npm install", este instalará todas las dependencias necesarias para el proyecto.
    5- Ahora se tiene que ir a la carpeta "infra" para eso (si no cerró la terminal anterior) puede usar este comando: 'cd ..\..\infra\' para powershell o 'cd ../../infra/' para bash.
    6- Ahora en ese directorio se tiene que ejecutar el siguiente comando 'docker-compose build --no-cache', este comando construye las imagenes necesarias para nuestros contenedores.
    7- Una vez finalizado se debe de escribir 'docker-compose up -d', este comando créa y levanta todos los contenedores del archivo "docker-compose.yml".
    8- Si todo salio bien y sin ningún error, se debería de poder acceder al sistema mediante "https://127.0.0.1".

## En caso de no funcionar

Por la implementación de el certificado auto firmado es muy probable que no ande al tratar de acceder v "https://127.0.0.1". En caso de que este sea el suyo usted debe de modificar el archivo "hosts" de su dispositivo.

### Que es el archivo hosts de tu dispositivo

El archivo "hosts" es un archivo que mapea nombres de dominios a direcciones IP de forma local. En nuestro caso lo utilizamos para poder dar un toque más profesionál a la hora de mostrar la URL, además, queda mucho más cómodo y de fácil acceso de escribir.

### Como modificar el archivo hosts y por que

En caso de error el archivo hosts debe de ser modificado, ya que docker espera el acceso solo desde "eskua.com.uy".

    1- Por si acaso, ponga (dentro de infra) "docker-compose down -v", este comando baja todos los contenedores borrando la informacion persistente en ellos.
    2- Para modificar el mismo usted debe de abrir notepad (o un editor de texto) en modo administrador, y elegir la opción de abrir un archivo (File -> Open).
    3- Ahora usted debe de ir a esta dirección "C:\Windows\System32\drivers\etc", una vez ahí debe presionar en las opciones de abajo a la derecha algo que diga "Documentos de Texto" a "Todos los Archivos" y debe hacer doble click en el archivo hosts.
    4- Ahora al final de su archivo en una línea vacía debe de poner "127.0.0.1 eskua.com.uy" y en otra línea vacía abajo de esa debe de poner "127.0.0.1 www.eskua.com.uy".
    5- Una vez agregados esos dos campos, usted puede guardar y cerrar el archivo.
    6- Ahora usted puede volver a la consola de Visual Studio Code, y dirijires al directorio "infra" y escribir 'docker-compose up -d', para volver a levantar los contenedores.
    7- Ahora si usted escribe en su navegador de confianza: https://eskua.com.uy, usted debería de tener acceso al sistema.

## Comandos

docker-compose up -d
docker-compose down -v
docker-compose build --no-cache
docker exec -it eskua-db mysql -u root -p
