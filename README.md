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

1. Tener abierta tu applicacion de Docker.

2. Ejecuta los siguientes comandos en el orden indicado dentro de la carpeta del proyecto:

   ### Construir y levantar los contenedores
   ```bash
   docker-compose up -d --build
   ```
    # Construir .env
    ```bash
    docker exec -it poblacion-backend bash -c "cp /var/www/.env.example /var/www/.env"
    ```
   ### Ejecutar las migraciones y seeders
   ```bash
   docker exec -it poblacion-backend php artisan migrate --seed
   ```

3. Verifica el estado de los servicios:
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
