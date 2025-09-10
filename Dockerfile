FROM php:latest
LABEL anonymous="true"
LABEL name="sfphphello"
LABEL description="PHP serverless hello world function"
COPY . /app
WORKDIR /app
RUN composer install --no-dev --optimize-autoloader
EXPOSE 3000
ENTRYPOINT ["php", "-S", "0.0.0.0:3000", "index.php"]