# Proyecto Laravel 11

Este proyecto está desarrollado utilizando el framework **Laravel 11** con las siguientes especificaciones:

## Requisitos

- **Framework**: `laravel/framework: ^11.31`
- **PHP**: `^8.2`
- **MySQL**: `8.0` (compatible con PHP 8.2)
- **Docker**: Necesario para la configuración y despliegue del entorno de desarrollo.

### Instalación de Docker
Antes de continuar, asegúrate de tener instalado Docker en tu máquina. Puedes descargarlo desde el siguiente enlace:

[Descargar Docker](https://www.docker.com/products/docker-desktop/)

---

## Instrucciones para iniciar el proyecto

1. Clona este repositorio en tu máquina local:
   ```bash
   git clone <URL_DEL_REPOSITORIO>
   cd <NOMBRE_DEL_PROYECTO>
   ```

2. Asegúrate de tener Docker instalado y en ejecución.

3. Ejecuta los siguientes comandos en el orden indicado:

   ### Construir y levantar los contenedores
   ```bash
   docker-compose up -d --build
   ```

   ### Instalar las dependencias con Composer
   ```bash
   docker exec -it poblacion-backend composer install
   ```

   ### Generar la clave de la aplicación
   ```bash
   docker exec -it poblacion-backend php artisan key:generate
   ```

   ### Ejecutar las migraciones y seeders
   ```bash
   docker exec -it poblacion-backend php artisan migrate --seed
   ```

4. Verifica el estado de los servicios:
   ```bash
   docker ps
   ```
---

## Notas
- Asegúrate de que los puertos necesarios para los servicios no estén en uso por otras aplicaciones.
- Si necesitas reiniciar los contenedores, utiliza:
  ```bash
  docker-compose down
  docker-compose up -d
