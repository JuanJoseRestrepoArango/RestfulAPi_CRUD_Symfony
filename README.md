Perfecto, aquí tienes un `README.md` para tu proyecto Symfony, estructurado exactamente igual que el de Laravel que me compartiste, pero adaptado con todo lo que implementaste en Symfony, incluyendo Docker, API Key, documentación Swagger (con Nelmio), validaciones, errores, arquitectura y comandos útiles:

---

````md
# API RESTful de Restaurantes con Symfony y Docker

Esta es una API RESTful para gestionar restaurantes, desarrollada en Symfony 6 y preparada para ejecutarse fácilmente usando Docker.

---

## Requisitos

- Tener instalado [Docker](https://www.docker.com/get-started)
- Tener instalado [Docker Compose](https://docs.docker.com/compose/install/)

---

## Instalación y ejecución

1. Clona este repositorio:

```bash
git clone https://github.com/JuanJoseRestrepoArango/RestfulAPi_CRUD_Symfony.git
cd RestfulAPi_CRUD_Symfony
````

2. Construye y levanta los contenedores (la primera vez descargará y configurará todo automáticamente):

```bash
docker-compose up -d --build
```

¡Listo! El contenedor de la aplicación automáticamente:

* Copiará `.env` a `.env.local` si no existe
* Esperará que MySQL esté listo
* Generará una API\_KEY única si no existe
* Instalará las dependencias con Composer si no están instaladas
* Ejecutará las migraciones de la base de datos
* Iniciará Apache y servirá la aplicación en el puerto 8000

---

## Acceder a la API

La API estará disponible en:

```
http://localhost:8000/api/restaurantes
```

---

## Endpoints disponibles

| Método | Ruta                   | Descripción                   |
| ------ | ---------------------- | ----------------------------- |
| GET    | /api/restaurantes      | Listar todos los restaurantes |
| POST   | /api/restaurantes      | Crear un nuevo restaurante    |
| GET    | /api/restaurantes/{id} | Obtener un restaurante por ID |
| PUT    | /api/restaurantes/{id} | Actualizar un restaurante     |
| PATCH  | /api/restaurantes/{id} | Actualizar parcialmente       |
| DELETE | /api/restaurantes/{id} | Eliminar un restaurante       |

---

## Autenticación

Para todas las peticiones se debe enviar el header:

```
X-API-KEY: <valor_de_api_key_generado>
```

El valor se genera automáticamente la primera vez que levantas el contenedor y queda guardado en el archivo `.env.local`.

Para ver el API\_KEY puedes ejecutar:

```
docker exec -it api_restfull_symfony_app cat .env.local | grep API_KEY
```

O puedes encontrarla manualmente en el archivo `.env.local`.

---

### Headers recomendados para peticiones

| Key          | Value                 |
| ------------ | --------------------- |
| Content-Type | application/json      |
| Accept       | application/json      |
| X-API-KEY    | \<tu\_api\_key\_aqui> |

---

## Ejemplo de uso con Postman

Para crear un restaurante (POST):

```json
{
    "nombre": "Restaurante Ejemplo",
    "direccion": "Calle Falsa 123",
    "telefono": "+1234567890"
}
```

Para actualizar parcialmente un restaurante (PATCH):

```json
{
    "telefono": "+0987654321"
}
```

---

## Validaciones

* nombre: obligatorio, string, máximo 255 caracteres
* direccion: obligatorio, string, máximo 255 caracteres
* telefono: obligatorio, texto, máximo 20 caracteres

---

## Manejo de errores

La API devuelve respuestas JSON con esta estructura:

```json
{
  "status": false,
  "message": "Descripción del error"
}
```

Y códigos HTTP adecuados (404 para no encontrado, 422 para validación, 500 para errores internos, etc).

---

## Estructura del proyecto

* src/DTO/RestauranteDTO.php — Objeto de transferencia de datos
* src/Exception/RestauranteNoEncontrado.php — Excepción personalizada
* src/Helper/RestauranteResponse.php — Helper para respuestas JSON
* src/Controller/Api/RestauranteApiController.php — Controlador API
* src/EventSubscriber/ApiKeySubscriber.php — Middleware para validar API Key
* src/EventSubscriber/ApiExceptionSubscriber.php — Manejo de errores global
* src/Entity/Restaurante.php — Entidad Doctrine
* src/Repository/RestauranteRepository.php — Repositorio Doctrine
* src/Service/RestauranteService.php — Lógica de negocio
* migrations/ — Migraciones de base de datos
* config/packages/nelmio\_api\_doc.yaml — Configuración Swagger (Nelmio)

---

## Docker

* Dockerfile — Imagen PHP 8.3 con Apache y extensiones necesarias para Symfony
* docker-compose.yml — Define servicios `app` (Symfony) y `db` (MySQL 8.0)
* entrypoint.sh — Script para preparar entorno y lanzar Apache

---

## Comandos útiles

* Ver logs del contenedor app:

```bash
docker logs -f api_restfull_symfony_app
```

* Ejecutar comandos Symfony dentro del contenedor:

```bash
docker exec -it api_restfull_symfony_app php bin/console
```

* Ejecutar migraciones manualmente:

```bash
docker exec -it api_restfull_symfony_app php bin/console doctrine:migrations:migrate
```

* Detener y eliminar contenedores:

```bash
docker-compose down
```

---

## 📚 Documentación de la API (Swagger)

La API cuenta con documentación automática generada con **NelmioApiDocBundle**.

Una vez la aplicación esté corriendo, puedes acceder a la documentación desde:

* UI: [http://localhost:8000/api/doc](http://localhost:8000/api/doc) o  [http://127.0.0.1:8000/api/doc](http://127.0.0.1:8000/api/doc)
* JSON: [http://localhost:8000/api/doc.json](http://localhost:8000/api/doc.json) o  [http://127.0.0.1:8000/api/doc.json](http://127.0.0.1:8000/api/.json)

> ⚙️ Se genera automáticamente gracias a las anotaciones en el controlador (`#[OA\...]`) y la configuración de Nelmio en `nelmio_api_doc.yaml`.

> ⚙️  Debes Incluir la ApiKey que encontraras en el .env.local en Authorize para poder probar las peticiones

