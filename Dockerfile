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

# Instalar Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Crear directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . /app

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# FORZAR LA CREACIÓN DEL ARCHIVO .ENV CON MYSQL INCLUIDO
RUN echo "APP_NAME=Korteo" > .env
RUN echo "APP_ENV=production" >> .env
RUN echo "APP_KEY=base64:Mkjt1SM/8vO8hZWKrRpdL1LqdICMIHrx5mq1k+85zls=" >> .env
RUN echo "APP_DEBUG=true" >> .env
RUN echo "APP_URL=https://korteo-production.up.railway.app" >> .env
RUN echo "VITE_APP_URL=https://korteo-production.up.railway.app" >> .env

# Conexión a MySQL (Usando los datos de tu panel de Railway)
RUN echo "DB_CONNECTION=mysql" >> .env
RUN echo "DB_HOST=mysql.railway.internal" >> .env
RUN echo "DB_PORT=3306" >> .env
RUN echo "DB_DATABASE=railway" >> .env
RUN echo "DB_USERNAME=root" >> .env
RUN echo "DB_PASSWORD=FiUKFvPlvBGwabbnhbvTIXRMVVmTmqk" >> .env

# Otras variables necesarias
RUN echo "SESSION_DRIVER=database" >> .env
RUN echo "CACHE_STORE=database" >> .env
RUN echo "QUEUE_CONNECTION=database" >> .env

# Compilar estilos
RUN npm install && npm run build

# Dar permisos a storage y enlazar
RUN chmod -R 775 storage bootstrap/cache
RUN php artisan storage:link || true

# Exponer el puerto
EXPOSE 8000

# Comando de inicio
CMD php artisan serve --host=0.0.0.0 --port=$PORT