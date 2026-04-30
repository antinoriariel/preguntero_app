Como el workspace está vacío, el plan parte desde cero.

**Arquitectura**
- Backend en PHP puro, sin framework, con un front controller, sesiones y acceso a MySQL mediante PDO.
- Base de datos llamada preguntero_app con charset utf8mb4 y esquema pensado para usuarios, preguntas y respuestas.
- Frontend con Bootstrap, Font Awesome y Merriweather; crear desde el inicio las carpetas css, js e img aunque todavía no haya estilos propios.
- Login con username o email más contraseña.
- CRUD de preguntas y respuestas disponible para cualquier usuario autenticado.

**Fases**
- Base y modelo de datos: definir la estructura de usuarios, preguntas y respuestas, con índices únicos para username y email, clave foránea en respuestas y borrado en cascada.
- Núcleo de seguridad: conexión PDO centralizada, helpers de validación, escape de salida, tokens CSRF, mensajes flash, manejo de sesión y encabezados de seguridad.
- Registro: formulario con dni_usuario, username, nombre, apellido, email, password y confirmación; validación estricta de formato, longitud y unicidad; hash seguro de contraseña antes de guardar.
- Login: un solo campo de identificador para username o email; verificación de contraseña; errores genéricos para no enumerar cuentas; regeneración de sesión al entrar; control básico de intentos.
- CRUD de preguntas: listado con búsqueda y paginación, alta, edición, detalle y eliminación.
- CRUD de respuestas: edición integrada dentro del formulario de la pregunta, con transacciones para evitar estados parciales.
- Reglas de negocio: enunciado obligatorio, respuestas no vacías, mínimo de respuestas por pregunta y al menos una correcta.
- Verificación: pruebas de registro, login, SQL injection, CSRF, XSS, borrado en cascada y acceso no autorizado.

**Decisiones**
- No habrá roles ni panel de administrador en la primera versión; el CRUD lo usa cualquier usuario autenticado.
- Las contraseñas se guardarán con Argon2id si el entorno lo soporta; si no, bcrypt.
- El DNI se validará como 8 dígitos; si necesitas conservar ceros a la izquierda, conviene almacenarlo como CHAR(8) aunque el requisito lógico diga int(8).
- El login no revelará si existe o no un usuario concreto; siempre responderá con un mensaje genérico.

Si quieres, el siguiente paso natural es convertir este plan en la estructura inicial del proyecto y el SQL base.