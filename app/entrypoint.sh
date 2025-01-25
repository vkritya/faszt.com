#!/bin/bash

set -e
set -x

is_alpine() {
  [ -f /etc/alpine-release ]
}

# Cleanup
rm -rf /var/www/html/*

# Copy frontend files
cp /speedtest/*.js /var/www/html/
cp /speedtest/assets /var/www/html/assets

# Copy favicon
cp /speedtest/favicon.ico /var/www/html/

# Set custom webroot on alpine
if is_alpine; then
  echo "ALPINE IMAGE"
  sed -i "s#\"/var/www/localhost/htdocs\"#\"/var/www/html\"#g" /etc/apache2/httpd.conf
else
  echo "DEBIAN IMAGE"
fi


# Set up backend side for standlone modes
if [[ "$MODE" == "standalone" || "$MODE" == "dual" ]]; then
  cp -r /speedtest/backend/ /var/www/html/backend
fi

if [ "$MODE" == "backend" ]; then
  cp -r /speedtest/backend/* /var/www/html
fi

# Set up unified index.php
if [ "$MODE" != "backend" ]; then
  cp /speedtest/ui.php /var/www/html/index.php
fi


if is_alpine; then
  chown -R apache /var/www/html/*
else
  chown -R www-data /var/www/html/*
fi

# Allow selection of Apache port for network_mode: host
if [ "$WEBPORT" != "80" ]; then
  if is_alpine; then
    sed -i "s/^Listen 80\$/Listen $WEBPORT/g" /etc/apache2/httpd.conf
  else
    sed -i "s/^Listen 80\$/Listen $WEBPORT/g" /etc/apache2/ports.conf
    sed -i "s/*:80>/*:$WEBPORT>/g" /etc/apache2/sites-available/000-default.conf
  fi
fi

echo "Done, Starting APACHE"

# This runs apache
if is_alpine; then
  exec httpd -DFOREGROUND
else
  exec apache2-foreground
fi