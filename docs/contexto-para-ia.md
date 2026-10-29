# Prompt de contexto del proyecto — Maderas Artesanales

Copiá y pegá este contexto en la IA que vaya a trabajar sobre el repositorio. El código y la configuración actuales son la fuente de verdad: este documento describe el estado observado y puede quedar desactualizado si el proyecto cambia.

---

## Rol e instrucciones de trabajo

Actuá como asistente de desarrollo para este proyecto. Antes de proponer o cambiar código, inspeccioná los archivos relacionados y seguí el flujo completo entre rutas, controladores, servicios, persistencia y vistas. No supongas que una funcionalidad existe solo porque aparece en el README o en el roadmap: verificá el comportamiento real en el código.

- Respondé en español, salvo que se solicite otro idioma.
- Hacé cambios precisos, completos y acotados al pedido. No reestructures módulos ni corrijas problemas ajenos al alcance.
- Conservá los patrones existentes. Esta aplicación usa principalmente funciones PHP, nombres de funciones en español, algunas funciones de persistencia en inglés, vistas PHP y middleware namespaced; no es una aplicación basada en clases de dominio o un framework de frontend.
- Conservá la separación actual: las rutas conectan URL y controlador; los controladores manejan HTTP y vistas; los servicios concentran validación y reglas de negocio; la persistencia consulta/escribe en MySQL; las vistas presentan los datos.
- Usá consultas PDO preparadas para datos externos, validá entradas y escapá el contenido dinámico mostrado en HTML con `html()`. No concatenes datos del usuario en SQL o HTML.
- Mantené los datos sensibles fuera de Git y de las respuestas. No abras ni copies el `.env`; tomá `.env.example` como referencia de nombres de variables, no de credenciales reales.
- Al cambiar tablas, creá una migración SQL nueva, ordenada y coherente con el historial. No edites migraciones ya aplicadas sin una razón explícita; antes, revisá su contenido y el esquema real de la base.
- Al tocar interfaces, revisá también las vistas, el layout compartido y el CSS/JavaScript relacionados. Al tocar flujos, revisá tanto las rutas GET como POST y los permisos.
- Verificá los cambios con los comandos disponibles y las pruebas pertinentes. No afirmes que hay tests automatizados o que algo fue probado si no lo ejecutaste.
- Si el requerimiento admite varias decisiones funcionales importantes, preguntá antes de elegir una. Si el alcance es claro, implementá directamente.

## Qué es el proyecto

Es una tienda web institucional para el emprendimiento artesanal “Hierro y Madera”, presentado en la interfaz como “Maderas Artesanales”. El negocio ofrece productos y muebles artesanales, principalmente de madera, con opciones de catálogo y solicitudes de personalización. El repositorio corresponde al proyecto institucional de E.E.S.T. Nº 4 de Berazategui.

El objetivo del sitio es permitir consultar productos y categorías, buscar, usar un carrito, registrar pedidos, administrar el catálogo y la operación básica, crear cuentas, consultar pedidos y enviar solicitudes de productos personalizados.

## Tecnologías y ejecución

- PHP 8.1 o superior.
- Slim 4 como framework HTTP, PSR-7/PSR-15 y `slim/php-view` para renderizar vistas PHP.
- PDO con **MySQL** como motor utilizado por el código actual.
- `vlucas/phpdotenv` para cargar el entorno desde `.env`.
- Resend PHP SDK para enviar el formulario de contacto y los enlaces de recuperación de contraseña. Aprobar o rechazar solicitudes personalizadas no envía correo.
- Bootstrap local (CSS y JS) y JavaScript vanilla para el comportamiento de filtros del catálogo.
- Composer gestiona dependencias y expone los scripts `migrate` y `serve`; consultá `composer.json` para los comandos vigentes.
- `public/index.php` es el front controller que carga `src/bootstrap.php` y ejecuta Slim. `public/.htaccess` reescribe rutas no asociadas a archivos/directorios hacia `index.php`.

El README menciona MySQL o PostgreSQL, pero la implementación de conexión construye un DSN `mysql:` y las migraciones usan sintaxis MySQL. No describas PostgreSQL como soportado sin implementar y verificar ese soporte.

La configuración esperada está documentada en `.env.example`: entorno (`APP_ENV`), conexión (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` o `DB_PASSWORD`, `DB_CHARSET`), URL pública (`APP_URL`) y correo (`RESEND_API_KEY`, `RESEND_FROM_EMAIL`, `CONTACT_TO_EMAIL`). `MAIL_FROM` permanece como compatibilidad para otros envíos existentes. No incluyas valores reales de `.env` en documentación, ejemplos, commits ni respuestas.

## Estructura relevante

- `public/`: entrada web, `.htaccess`, assets públicos y archivos subidos que deban servirse desde la web.
- `src/bootstrap.php`: autoload, Dotenv, entorno/debug, sesión, renderer, carga del catálogo y registro de rutas.
- `src/routes/`: define endpoints y asocia handlers.
- `src/controllers/`: toma datos HTTP, llama servicios/persistencia y devuelve vistas o redirecciones.
- `src/services/`: reglas de negocio, validación y coordinación de tareas.
- `src/persistence/`: acceso a tablas mediante PDO.
- `src/middleware/`: control de acceso autenticado y de administrador.
- `src/database/database.php`: configuración y conexión PDO compartida; contiene también un helper para transacciones.
- `src/database/migrations/`: scripts SQL ordenados que crea/aplica `scripts/migrate.php`.
- `src/data/`: catálogo y categorías estáticos de respaldo/demo.
- `src/views/`: plantillas PHP; `layouts/base.php` aporta navegación, carrito, categorías y footer común.
- `src/utils/html.php` y `src/utils/view.php`: escape HTML y renderizado con layout.
- `public/css/tienda.css`, `public/js/catalogo-filtros.js` y `public/js/account-panel.js`: estilos propios, filtrado visual del catálogo y panel lateral de cuenta.
- `docs/roadmap-etapas-8-22.md`: registro documental de etapas funcionales, no sustituye la verificación del código.
- `vendor/`: dependencias instaladas por Composer; no modificar código de terceros para implementar lógica propia.

## Arranque y composición de la aplicación

`public/index.php` requiere `src/bootstrap.php` y llama `$app->run()`. El bootstrap:

1. Carga Composer, conexión, persistencia de catálogo/autenticación, middleware y utilidad del carrito.
2. Carga `.env` de forma segura con Dotenv.
3. Acepta `APP_ENV=dev` o `APP_ENV=prod`; usa el modo dev para el middleware de errores de Slim.
4. Inicia una sesión PHP.
5. Crea la aplicación Slim y un `PhpRenderer` apuntando a `src/views`.
6. Inicializa datos estáticos de categorías, productos y carrusel. Intenta reemplazarlos por datos activos de MySQL cuando hay resultados. Si no hay datos, queda el respaldo estático; las consultas públicas de catálogo además capturan errores y devuelven listas vacías.
7. Define `$render`, que inyecta categorías y cantidad total del carrito en todas las páginas y delega a `utils/view.php`.
8. Carga los archivos de rutas funcionales y configura middleware de error.

Las rutas se cargan en el mismo ámbito de PHP y reutilizan `$app`, `$render`, `$categorias` y `$productos` como variables globales. Tené en cuenta este acoplamiento antes de mover rutas o convertir funciones en clases.

Renderizado: la plantilla de página se obtiene con `PhpRenderer::fetch`; si se requiere un layout intermedio se envuelve primero; finalmente `utils/view.php` coloca el resultado en `views/layouts/base.php`. Las plantillas acceden a variables entregadas por el controlador y componen parciales con `$this->fetch(...)`.

## Flujo de una solicitud y convenciones

La ruta Slim enlaza una URL con una función del controlador. Para las vistas, el controlador suele usar `$render($response, "ruta/vista.php", $datos)`. Los POST suelen responder con redirección 302 y mensajes flash guardados en `$_SESSION`. Cuando una validación de formulario falla, se vuelve a renderizar la vista con status 422 y un mensaje.

Las consultas deberían permanecer en los archivos de `persistence`; los servicios llaman a persistencia y aplican reglas; los controladores no deberían incorporar SQL ni reglas de negocio complejas. Las vistas deben escapar datos dinámicos; la función `html()` usa `htmlspecialchars` con UTF-8.

Hay dos conjuntos de funciones de carrito: `src/utils/carrito.php` (nombres camelCase, incluido `carritoCantidadTotal()`, que el layout recibe desde el bootstrap) y `src/services/carrito.service.php` (nombres snake_case, usados por el controlador del carrito y que también manejan la creación del pedido). No los confundas ni elimines uno sin rastrear sus usos.

## Funcionalidades y relaciones principales

### Catálogo, home, categorías, detalle y búsqueda

- Rutas definidas en `src/routes/catalogo.routes.php` y `src/routes/buscador.routes.php`.
- Controladores `catalogo.controller.php` y `buscador.controller.php`; reglas de filtrado en `catalogo.service.php`; datos SQL en `catalogo.persistence.php`.
- La home muestra carrusel y tarjetas; las categorías se resuelven por slug; el detalle usa id numérico y muestra productos relacionados de la misma categoría.
- La búsqueda actual compara el texto con el nombre del producto y no con todos sus atributos.
- `public/js/catalogo-filtros.js` y `views/partials/filtrador.php` implementan filtros visuales del listado del navegador (por categoría/stock), distintos de la búsqueda del servidor.
- El stock mostrado es un indicador de disponibilidad. No asumir que existe reserva, decremento o control transaccional de inventario en el checkout.
- Los registros iniciales del catálogo existen tanto como datos estáticos en `src/data/` como en las migraciones SQL de datos base.

### Carrito, checkout y pedidos

- Endpoints en `src/routes/carrito.routes.php`; controlador `carrito.controller.php`; lógica en `services/carrito.service.php`; contenedor de items en `$_SESSION["carrito"]`.
- El carrito guarda ids y cantidades en la sesión y calcula subtotales usando los productos cargados por la aplicación. El layout muestra el total de unidades.
- El POST de checkout toma los datos del formulario, crea el pedido y sus renglones, guarda temporalmente el último pedido en sesión, vacía el carrito y redirige a confirmación.
- Los pedidos pueden asociarse a un usuario autenticado, pero el checkout no está protegido por `AuthMiddleware`, por lo que también admite invitado.
- El pedido almacena snapshots de nombre/precio en `pedido_items`; la dirección de envío y método de pago se guardan en `pedidos`.
- **No hay integración implementada con una pasarela de pago.** Los valores de método (tarjeta/Mercado Pago) son datos almacenados; no prueban que se haya cobrado o autorizado una transacción.
- No des por hecho que el flujo usa `Database::runTransaction()`: verificá el código del pedido y las garantías ante fallos parciales antes de modificarlo.
- El historial del cliente se obtiene de `fetch_pedidos_por_usuario`; el administrador puede cambiar el estado del pedido.

### Registro, login, sesión y recuperación

- Rutas y handlers en `routes/auth.routes.php` y `controllers/auth.controller.php`; reglas en `services/auth.service.php`; consultas en `persistence/auth.persistence.php`.
- El registro valida nombre/email y contraseña de al menos ocho caracteres con confirmación; guarda hash con `password_hash`.
- El login valida credenciales con `password_verify`, regenera el id de sesión y guarda `usuario_id` y una copia de nombre/email/rol en `$_SESSION["usuario"]`.
- El cierre de sesión limpia y destruye la sesión y vence su cookie. El panel lateral ofrece logout mediante `POST /logout`; `GET /logout` se conserva por compatibilidad.
- El navbar de clientes autenticados abre el panel lateral de cuenta sin salir de la página actual. `GET /mi-cuenta/datos`, protegido por `AuthMiddleware`, obtiene el usuario de la sesión y devuelve sus pedidos y solicitudes; `/mi-cuenta` conserva una redirección compatible hacia el inicio con el panel abierto.
- `AuthMiddleware` exige `usuario_id`; las rutas de cuenta/pedidos y las de personalización del cliente lo usan. `AdminMiddleware` además requiere rol `admin`.
- La recuperación crea un token aleatorio de 32 bytes, persiste su hash SHA-256 con vencimiento de una hora y envía el enlace a través de Resend. El formulario presenta un mensaje genérico para no revelar si un email existe.
- Las rutas `/admin/...` están agrupadas y protegidas por `AdminMiddleware`. Las rutas admin se declaran en `routes/admin.routes.php`; el CRUD se distribuye entre `controllers/admin.controller.php`, `services/admin.service.php`, `persistence/admin.persistence.php` y las vistas `views/admin/`.

### Administración

- Administra productos, categorías, carrusel, pedidos y solicitudes de personalización.
- El catálogo distingue entre desactivar (borrado lógico) y borrar definitivamente. El borrado definitivo de producto verifica si está referenciado por pedidos; el borrado permanente de categoría requiere que no tenga productos.
- Los formularios de producto y carrusel pueden recibir una imagen. El servicio valida tamaño y contenido/tipo, crea un nombre aleatorio y guarda bajo `public/uploads/productos/` o `public/uploads/carrusel/`; al reemplazar o limpiar imágenes también interactúa con el filesystem.
- Las categorías no tienen imagen.
- Los formularios de administración reutilizan `views/admin/form.php` y pasan tipo, elemento y categorías según corresponda.

### Solicitudes personalizadas

- Endpoints en `routes/personalizacion.routes.php`; procesamiento y descargas en `controllers/personalizacion.controller.php`; validación y almacenamiento en `services/personalizacion.service.php`; consultas en `persistence/personalizacion.persistence.php`.
- Solo clientes autenticados pueden enviar una solicitud. Se requiere descripción entre 20 y 5000 caracteres y un archivo PDF/JPG/PNG de hasta 10 MB.
- Los adjuntos se guardan fuera de `public/`, en `storage/personalizaciones/`, con un nombre generado aleatoriamente. La descarga pasa por controladores autenticados; el cliente solo puede descargar el archivo perteneciente a su propia solicitud.
- Un administrador revisa la solicitud y la acepta con presupuesto estimado y mensaje, o la rechaza con motivo. La decisión se guarda en la base de datos sin enviar correo ni depender de Resend. El cliente consulta el estado y la respuesta dentro del panel lateral, junto a sus pedidos comunes pero en una lista claramente identificada como personalizada. La columna histórica `notificacion_enviada_en`, si existe, ya no participa en este flujo.
- Las imágenes subidas por administración y los archivos de personalización son persistencia en disco, no solo en base de datos: deben incluirse en backups/despliegues cuando corresponda. `.gitignore` los excluye intencionalmente.

### Contacto

Hay una vista y POST en `/contacto`. El controlador valida nombre, email y mensaje; el servicio usa el SDK oficial `resend/resend-php`, configurado mediante `RESEND_API_KEY`, `RESEND_FROM_EMAIL` y `CONTACT_TO_EMAIL`, para enviar el correo. El email validado del usuario se usa como `reply_to`, nunca como remitente o destinatario. No hay persistencia en base de datos. Una aceptación de Resend no demuestra la entrega efectiva del mensaje.

## Modelo de datos a nivel conceptual

La base está pensada para MySQL/InnoDB y contiene, entre otras:

- `categorias`: nombre, slug único y estado activo.
- `productos`: referencia a categoría, descripción, precio, stock/disponibilidad, imagen, material, medidas y estado activo.
- `usuarios`: perfil, email único, hash de contraseña, rol y campos para restablecimiento de contraseña.
- `pedidos`: número, total, usuario opcional, método/estado, datos de envío y fecha.
- `pedido_items`: relación con pedido/producto y snapshots de nombre/precio/cantidad.
- `carrusel`: contenido, imagen, link, orden y estado activo.
- `solicitudes_personalizacion`: usuario, descripción, metadatos del adjunto, estado, presupuesto, respuesta y fechas de respuesta/notificación. La notificación por correo ya no forma parte del flujo administrativo de aprobación/rechazo.

El historial de migraciones también contiene referencias a una estructura antigua como `detalle_pedido` y un campo `fecha` en pedidos, mientras la lógica actual consulta `pedido_items` y usa timestamps/campos actuales. Hay migraciones que crean o alteran estructuras en etapas distintas. Antes de tocar esquema o deducir una base limpia, leé los SQL en orden, revisá las consultas actuales y contrastá el estado real de la base. No presupongas que un nombre numerado describe exactamente el contenido o que todos los entornos ya aplicaron el mismo conjunto de migraciones.

`scripts/migrate.php` carga el entorno, asegura la base de datos y la tabla `migraciones`, ordena los `.sql` naturalmente y registra archivos ya ejecutados para no volver a aplicarlos. Las migraciones pueden incluir cambios y datos semilla; revisá su idempotencia y sus precondiciones antes de ejecutar en un entorno con datos.

## Estado y límites observados

- El roadmap documenta como completados catálogo, búsqueda/filtros, carrito/checkout, pedidos, administración, autenticación, migraciones, contacto y personalizaciones. Ese check-list es una declaración documental; validá cada flujo si la tarea depende de él.
- El historial Git reciente muestra trabajo de finalización del roadmap y luego tareas para recuperar contraseña, permitir imágenes/borrado definitivo en administración y agregar solicitudes de personalización/presupuestos/documentación.
- La conexión y las consultas de administración dependen de una base configurada y migrada. El catálogo intenta degradar a datos estáticos; los flujos que requieren persistencia no tienen ese respaldo.
- El código del sitio utiliza MySQL aunque el README mencione PostgreSQL.
- El checkout no procesa pagos ni descuenta stock durante la compra. El formulario de contacto envía mensajes mediante Resend, pero no los persiste en la base de datos.
- No deduzcas que hay una suite de tests completa solo a partir de la presencia de dependencias de testing en `vendor`; localizá pruebas existentes antes de seleccionarlas.
- No uses ni expongas credenciales del `.env`. Si el entorno de correo, base de datos o permisos de carpetas son necesarios para probar, diferenciá claramente una limitación de entorno de un fallo funcional.

## Archivos guía para orientarse

- [README.md](../README.md): descripción, requisitos y notas de despliegue.
- [composer.json](../composer.json): dependencias, autoload y comandos.
- [src/bootstrap.php](../src/bootstrap.php): composición y carga de rutas.
- [public/index.php](../public/index.php): entrada HTTP.
- [src/database/database.php](../src/database/database.php): PDO y transacciones.
- [scripts/migrate.php](../scripts/migrate.php): ejecución de migraciones.
- [src/routes/](../src/routes/): mapa de endpoints.
- [src/controllers/](../src/controllers/): handlers HTTP.
- [src/services/](../src/services/): reglas de negocio.
- [src/persistence/](../src/persistence/): consultas SQL.
- [src/middleware/](../src/middleware/): permisos.
- [src/views/](../src/views/): interfaz.
- [src/database/migrations/](../src/database/migrations/): evolución de esquema y semillas.
- [docs/roadmap-etapas-8-22.md](./roadmap-etapas-8-22.md): resumen de etapas declaradas.

## Para iniciar una tarea nueva

Primero resumí qué entendés del pedido y qué archivos/flujos vas a tocar. Luego inspeccioná esos archivos y sus dependencias inmediatas. Implementá el cambio en todas las capas necesarias, conservando validaciones, permisos y feedback de interfaz. Terminá con una verificación concreta y un resumen que indique qué cambió, qué se ejecutó para validar y cualquier limitación real que haya quedado.
