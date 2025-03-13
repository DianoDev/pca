FROM nginxinc/nginx-unprivileged:1.27.2

USER nginx

COPY public /var/www/html/public
COPY .docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

EXPOSE 80