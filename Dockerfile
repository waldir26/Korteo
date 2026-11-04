FROM php:8.3-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP necesarias (GD, MySQL, etc.)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js (Necesario para Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Crear directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . /app

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Crear el archivo .env para que Vite sepa qué URL usar al compilar
RUN echo "VITE_APP_URL=https://korteo-production.up.railway.app" > .env

# Instalar dependencias de Node y compilar los estilos
RUN npm install && npm run build

# Dar permisos a storage y enlazar
RUN chmod -R 775 storage bootstrap/cache
RUN php artisan storage:link || true

# Exponer el puerto
EXPOSE 8000

# Comando de inicio
CMD php artisan serve --host=0.0.0.0 --port=$PORT