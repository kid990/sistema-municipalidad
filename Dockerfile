# Stage 1: instalar dependencias PHP
FROM composer:latest AS composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Stage 2: compilar assets (ahora sí tiene vendor/)
FROM node:22 AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
COPY --from=composer /app/vendor ./vendor   # <-- tomar vendor del stage anterior

RUN npm run build

# Stage 3: imagen final PHP
FROM php:8.4-cli

# ... (igual que tenías)

COPY --from=composer /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
