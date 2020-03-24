FROM maddog986/php-nginx-mysql-server:latest

LABEL maintainer="Drew Gauderman <drew@hyak.co>" \
  Description="test server with test database"

# Copy the test database over
COPY ./db/ /var/db/

EXPOSE 80
