# Backend en PHP

Backend en PHP para la asignatura de DAWEC en el IES Juan Bosco. Especialmente diseñado para su uso con el Framework de JavaScript [Angular](http://angular.io)

## Instalación

#### Pasos a seguir para la puesta en funcionamiento del Backend.

Dirigirse a la carpeta de instalación por defecto de Xampp con la consola de comandos:
```bash
cd c:\xampp\htdocs
```
Clonar el repositorio de este proyecto con el comando:

```bash
git clone https://github.com/Cabrillana/backendphp.git
```
Ejecutar el programa Xampp Control Panel e iniciar los servicios Apache y MySQL con el botón Start. Una vez iniciado, acceder a [http://localhost/phpmyadmin](http://localhost/phpmyadmin). Crear una nueva base de datos con el nombre `back`. Dentro de esa base de datos importar el archivo **backphp.sql** que creará la estructura de las tablas.

## Rutas, métodos y respuestas.

Rutas actualmente implementadas organizadas por método y funcionalidad.

#### Rutas relacionadas con las notas:

| Método | Ruta | Acción | Requiere JWT |
| :---: | --- | --- | :---: |
| **GET** |_localhost/backendphp/notas/_| Listar todas las notas | Opcional |
| **POST** |_localhost/backendphp/notas/_| Insertar una nota | Opcional |
| **PUT** |_localhost/backendphp/notas/_| Actualizar una nota | Obligatorio |
| **DELETE** |_localhost/backendphp/notas/id_| Eliminar una nota | Obligatorio |

#### Rutas relacionadas con el usuario:

| Método | Ruta | Acción | Requiere JWT |
| :---: | --- | --- | :---: |
| **GET** |_localhost/backendphp/user/_| Leer la información del usuario JWT | Obligatorio |
| **POST** |_localhost/backendphp/user/_| Registrar un usuario nuevo | Innecesario |
| **PUT** |_localhost/backendphp/user/_| Actualizar la información del usuario JWT | Obligatorio |
| **DELETE** |_localhost/backendphp/user/_|Eliminar el usuario JWT | Obligatorio |
| **POST** |_localhost/backendphp/user/login_| Hace Login con la información recibida | Innecesario |
| **POST** |_localhost/backendphp/user/image_| Sube una imagen de perfil del usuario | Obligatorio |
| **GET** |_localhost/backendphp/user/list_| Muestra información de otros usuarios | Obligatorio |

#### Códigos de respuesta del servidor:

| Código | Significado |
| :--: | -- |
| _200_ | Petición aceptada por el servidor |
| _201_ | La operación de inserción o actualización ha sido correcta |
| _400_ | Hay un error en la petición del cliente |
| _401_ | Fallo en la petición ya que requiere autorización |
| _404_ | No se ha encontrado el recurso de la petición |
| _409_ | Hay un conflicto con la petición del cliente |