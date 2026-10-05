# Maderas Artesanales

Proyecto de tienda online desarrollada con PHP 8.1, Slim Framework 4, Bootstrap, PDO/MySQL y sesiones.

## Descripción

Maderas Artesanales es una tienda de muebles y productos artesanales de madera, con catálogo, categorías, búsqueda, detalle de producto, carrito, checkout simulado, autenticación, panel administrativo y manejo de contenido del sitio.

## Stack

- PHP 8.1+
- Slim Framework 4
- Composer
- MySQL / PDO
- Bootstrap
- PHP Sessions
- Dotenv

## Arquitectura

La aplicación sigue una separación simple y clara:

Routes → Controllers → Functions/Services → Database/Persistence

Se mantiene una estructura orientada a la enseñanza y a la claridad del flujo:

- Routes: definen endpoints
- Controllers: reciben la petición, parsean datos y deciden vistas/redirecciones
- Services/Functions: encapsulan la lógica reutilizable
- Persistence/Database: ejecutan consultas a MySQL

## Requisitos

- PHP 8.1 o superior
- Composer
- MySQL
- Extensión PDO habilitada
- Git

## Instalación

1. Clonar el repositorio.
2. Instalar dependencias:

```bash
composer install
```

3. Crear un archivo `.env` a partir de `.env.example`.
4. Configurar los datos de la base de datos:

```env
APP_ENV=dev
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=Muebleria
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

5. Crear la base de datos y ejecutar migraciones:

```bash
php scripts/migrate.php
```

6. Iniciar el servidor local:

```bash
php -S localhost:8000 -t public
```

## Usuario administrador demo

Se incluye un usuario administrador de ejemplo en la migración de base de datos:

- Email: admin@maderasartesanales.test
- Password: admin123

> La contraseña se guarda con `password_hash()` y no se almacena en texto plano.

## Estructura principal

```text
src/
├── bootstrap.php
├── controllers/
├── database/
├── middleware/
├── persistence/
├── routes/
├── services/
├── utils/
├── views/
├── data/
└── public/
```

## Funcionalidades principales

- Catálogo
- Categorías
- Búsqueda
- Detalle de producto
- Carrito
- Checkout simulado
- Registro / login / logout
- Mi cuenta
- Contacto
- Panel administrativo
- Carrusel de portada

## Licencia

Este proyecto está licenciado bajo la licencia MIT.
