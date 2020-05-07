FROM nginx:1.17

ENV BASE_DIR=/var/www
ENV DOCKER_DIR=./.docker/nginx

ADD ${DOCKER_DIR}/vhost.conf /etc/nginx/conf.d/default.conf
