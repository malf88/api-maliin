FROM nginx:1.25-bullseye
ENV APP_NAME=default
RUN apt-get update && \
   DEBIAN_FRONTEND=noninteractive apt-get install -y \
   nginx-extras

COPY ./ /var/www/
RUN rm /etc/nginx/conf.d/default.conf
COPY ./docker/nginx/config/default.conf /etc/nginx/sites-available/default
RUN sed -i '1 ienv APP_NAME;' /etc/nginx/nginx.conf
