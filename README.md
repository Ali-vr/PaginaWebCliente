# {Pagina-Web-Maderas}

**Descripción:**
{El proyecto consiste en el desarrollo de una página web para un emprendimiento de venta de manualidades llamado “Hierro y Madera”, el cual se enfoca en la fabricación y comercialización de productos artesanales realizados principalmente con hierro y madera.

Este tipo de productos combina resistencia (hierro) con estética y calidez (madera), lo que los hace muy atractivos para decoración del hogar y mobiliario . Además, son muy buscados porque permiten tener objetos únicos, personalizados y duraderos.}

Integrantes:

- {Vergara, Ali} | [@Ali-vr](https://github.com/Ali-vr)
- {Luna, Santiago} | [@Lunape2](https://github.com/Lunape2)
- {Giacomelli, Dylan} | [@GiacoD-07](https://github.com/GiacoD-07)
- {Figueredo, Axel} | [@AxelFigueredo](https://github.com/AxelFigueredo)

Proyecto institucional **E.E.S.T Nº4 de Berazategui**.

## Elevator's Pitch

- Para {Para personas que buscan decoración única y muebles personalizados para el hogar}
- Quienes {Quienes necesitan productos duraderos, estéticos y hechos a medida}
- El {Hierro y Madera} es un {plataforma web de venta de manualidades artesanales}
- Que {ofrece diseños exclusivos, personalizados y de alta calidad}
- Diferente a {a tiendas tradicionales o productos industriales en masa}
- Nuestro proyecto {brinda piezas únicas hechas artesanalmente, combinando diseño, funcionalidad y personalidad en cada producto}.

## Requisitos

- PHP 8.1 o superior.
- Extensión PDO habilitada.
- MySQL o PostgreSQL según la configuración en el archivo `.env`.

## Migraciones

Colocá tus archivos `.sql` en `src/database/migrations/` y ejecuta:

```bash
composer migrate
```

## Recuperación de contraseña

La recuperación requiere configurar `APP_URL`, `RESEND_API_KEY` y `MAIL_FROM` en `.env`.
`APP_URL` debe ser la URL pública de la aplicación y `MAIL_FROM` un remitente verificado
en Resend. Luego ejecutá `composer migrate` para agregar los campos de tokens a `usuarios`.

## Imágenes del panel de administración

Desde los formularios de productos y carrusel se pueden subir imágenes JPG, PNG, WebP o GIF
de hasta 5 MB. Se guardan en `public/uploads/` con nombres generados por el servidor; esa
carpeta no se incluye en Git y debe conservarse al desplegar o respaldarse. Las categorías no
tienen una imagen asociada.

El borrado definitivo está separado de la desactivación: no se puede borrar un producto que
figure en pedidos ni una categoría que todavía tenga productos vinculados.

## Solicitudes de productos personalizados

Los clientes registrados pueden enviar desde **Mi cuenta** una descripción y un archivo PDF,
JPG o PNG de hasta 10 MB. Los adjuntos se guardan fuera del directorio público en
`storage/personalizaciones/`; respaldá esa carpeta junto con la base de datos. El administrador
puede revisar la solicitud y aceptarla con un presupuesto estimado o rechazarla con un mensaje.
La respuesta se envía al email de la cuenta usando la configuración Resend (`RESEND_API_KEY` y
`MAIL_FROM`). Asegurate de que el usuario de PHP pueda escribir en `storage/personalizaciones/`.
Ejecutá `composer migrate` para crear la tabla de solicitudes.

## Licencia

Este proyecto está licenciado bajo la [Licencia MIT](./LICENSE).

---

Generado por la [plantilla](https://github.com/PDI-EEST4/slim-template-2026), a discreción de los integrantes del proyecto.
