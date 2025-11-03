Tutorial de Instalación y Configuración del Proyecto Glotty

Requisitos previos
1. Instalar PostgreSQL
Descarga e instala PostgreSQL desde el sitio oficial:
# Visita https://www.postgresql.org/download/ y descarga la versión para tu sistema operativo

Puedes seguir este tutorial en video para la instalación:
https://www.youtube.com/watch?v=w9ax9-s2jbE&t=23s

2. Instalar Laravel Herd
Laravel Herd es un entorno de desarrollo optimizado para Laravel:
# Descarga desde https://herd.laravel.com/windows
# Ejecuta el instalador y sigue las instrucciones

3. Instalar Git
# Verifica si ya tienes Git instalado
git --version

Si no lo tienes instalado, sigue este tutorial:
https://www.youtube.com/watch?v=jdXKwLNUfmg


PASO 1: Clonar el repositorio
Crea una carpeta para tu proyecto (puedes llamarla "sistema" o como prefieras)

Abre la carpeta, haz clic derecho y selecciona "Abrir en terminal"

Ejecuta el siguiente comando para clonar el repositorio:
git clone https://github.com/DAYN131/Glotty

Esto copiará todos los archivos del proyecto dentro de tu carpeta.

PASO 2: Configuración del proyecto

Abre la carpeta Glotty con Visual Studio Code

Dentro de Visual Studio Code, abre la terminal (Terminal → New Terminal)

Deberías ver algo similar a esto en tu terminal:
PS C:\Users\Daniel Mtz H\OneDrive\Escritorio\Sistema\Glotty>

Copia el archivo de entorno de ejemplo:
cp .env.example .env

Ejecuta el siguiente comando para quitar el atributo de solo lectura de la carpeta de cache:
attrib -r "bootstrap\cache"

Genera una nueva clave de aplicación:
php artisan key:generate



PASO 3: Restaurar la base de datos (Backup)
Sigue el tutorial que se envió por WhatsApp para restaurar la base de datos desde el backup.


PASO 4: Configurar la base de datos

Abre el archivo .env en Visual Studio Code

Modifica la sección de base de datos con la siguiente configuración:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=Escuela
DB_USERNAME=tu_usuario_postgres
DB_PASSWORD=tu_contraseña_postgres

Asegúrate de reemplazar tu_usuario_postgres y tu_contraseña_postgres con las credenciales que configuraste durante la instalación de PostgreSQL.


PASO 5: Instalar dependencias
Dentro de la terminal, osea aqui:
PS C:\Users\Daniel Mtz H\OneDrive\Escritorio\Sistema\Glotty> 
Instala las dependencias de Composer, con este comando:
composer install


Paso 6:
Sigue el tutorial de como agregar el sitio en Laravel Herd que mande por whatssaap





