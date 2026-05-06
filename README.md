# Preguntero App

Scaffold inicial para una aplicacion en PHP puro con MySQL y bootstrapp.

## Objetivo

La aplicacion incluye:
- Login con `username` o `email` + contrasena
- Registro seguro de usuarios
- CRUD de preguntas
- CRUD de respuestas asociadas a cada pregunta
- Base de datos `preguntero_app`
- Frontend inicial con Bootstrap, Font Awesome y Merriweather

## Estructura prevista

- `config/`: configuracion general y conexion a base de datos
- `app/core/`: clases base de soporte tecnico
- `app/controllers/`: controladores de autenticacion y preguntas
- `app/models/`: modelos de acceso a datos
- `app/views/`: vistas de auth, layout y preguntas
- `public/`: punto de entrada web y assets publicos
- `database/`: scripts SQL de creacion de la base

## Convenciones de seguridad

- Consultas preparadas con PDO
- Validacion estricta en servidor
- Tokens CSRF en formularios mutables
- Sesiones endurecidas y regeneradas al autenticar
- Hash de contrasenas con `password_hash`
- Salida escapada con `htmlspecialchars`
- Mensajes de error genericos para evitar enumeracion de cuentas

## Recursos frontend

La primera version cargara Bootstrap, Font Awesome y Merriweather sin estilos propios complejos. Las carpetas `public/css`, `public/js` y `public/img` quedan creadas desde el inicio para crecer sobre una base limpia.

## Proximo paso

Completar la logica de autenticacion y el CRUD apoyandose en `database/schema.sql`.