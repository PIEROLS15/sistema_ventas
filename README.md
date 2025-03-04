<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Sistema de ventas

Este proyecto es una aplicación desarrollada en Laravel para la gestión de ventas. Permite registrar ventas, generar reportes en diferentes formatos y administrar el sistema con control de roles y permisos.

## Instalación y configuración

### Requisitos previos

-   [Laragon](https://laragon.org/download/).
-   [MySQL Workbench para administración de la base de datos](https://dev.mysql.com/downloads/workbench/).
-   [Composer](https://getcomposer.org/).
-   [Visual Studio Code](https://code.visualstudio.com/download).
-   [Postman para las pruebas de API](https://www.postman.com/downloads/).

## Pasos para ejecutar el proyecto

### Clonar el repositorio

Se debe clonar el repositorio dentro de la carpeta root de laragon

-   git clone https://github.com/PIEROLS15/sistema_ventas.git

### Configurar Laragon

Se debe activar extensión zip dentro de PHP

-   Dentro de laragon, Menú>PHP>extensions>zip

### Instalar dependecias

Dentro del terminal de Laragon, entramos a la carpeta del proyecto clonado y ejecutamos:

-   composer install

### Configurar entorno

Ejecutamos en la terminal de laragon:

-   cp .env.example .env
-   En el archivo .env que se creó configuramos los datos de conexión a la base de datos

### Conexión

-   DB_CONNECTION=mysql
-   DB_HOST=127.0.0.1
-   DB_PORT=3306
-   DB_DATABASE=sistemas_ventas
-   DB_USERNAME=root
-   DB_PASSWORD=

### Ejecutamos migraciones

Sincronizamos las migraciones a la base de datos:

-   php artisan migrate --seed

### Iniciar el servidor

Para correr el programa ejecutamos:

-   php artisan serve

### Configuración de POSTMAN

-   Dentro de Postman importamos el archivo Sistema_ventas.postman_collection.json
-   Una vez cargada la collection, editamos el valor de la variable {{baseUrl}}

### Descargar reportes xlsx desde Postman

-   En el endpoint sales report dentro de params editamos el formato a xlsx
-   Al darle send se mostrará un status 200, en save response damos a la opcion Save response to file

## Decisiones técnicas clave

### Arquitectura

-   Se sigue el principio SRP (Single Responsibility Principle) separando lógica en controladores, servicios y repositorios.
-   Se utiliza Eloquent ORM para optimización de consultas SQL.

### Seguridad y roles

-   Se implementó los permisos por roles según los requerimientos funcionales solicitados
-   Se implementó sanctum ya que permite generar tokens de API simples, lo cual es ideal para aplicaciones internas.

### Reporte de ventas

-   Se pueden generar reportes en JSON y XLSX.
-   Se aplican filtros por rango de fechas.
-   Se usa la librería Maatwebsite Excel para la exportación a Excel.

### Base de datos y relaciones

El sistema usa MySQL como gestor de base de datos, con la siguiente estructura de tablas clave:

-   Users: Almacena los datos del usuario con su rol.
-   Roles: Almacena los tipos de roles que existen en el sistema (Administrador, vendedor)
-   Products: Almacena los datos de cada producto
-   Identificaciones: Define los tipos de identificación (DNI, RUC).
-   Sales: Registra la venta con cliente, monto total y fecha.
-   Sale_Details: Almacena los productos vendidos en cada venta.

### Diagrama ERD

![image](https://github.com/user-attachments/assets/28de0de5-ec37-416c-9706-48d6ae76a878)
