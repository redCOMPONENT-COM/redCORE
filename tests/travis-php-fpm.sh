#!/bin/bash

owner="$1"
phpversionname="$2"

cp /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.conf.default /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.conf
if [ -f /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.d/www.conf.default ]; then
	cp /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.d/www.conf.default /home/$owner/.phpenv/versions/$phpversionname/etc/php-fpm.d/www.conf;
fi
a2enmod rewrite actions fastcgi alias
echo "cgi.fix_pathinfo = 1" >> /home/$owner/.phpenv/versions/$phpversionname/etc/php.ini
sed -i -e "s,www-data,$owner,g" /etc/apache2/envvars
chown -R $owner:$owner /var/lib/apache2/fastcgi
