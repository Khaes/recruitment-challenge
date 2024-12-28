FROM base-worker
WORKDIR /var/www

COPY . .

RUN composer install --no-scripts

CMD bin/console messenger:consume async -vv