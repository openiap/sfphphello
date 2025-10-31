FROM php83:latest
LABEL anonymous="true"
LABEL repo="sfphphello"

COPY . .
RUN composer install
EXPOSE 3000
ENTRYPOINT ["php", "-S", "0.0.0.0:3000", "-t", "public"]
