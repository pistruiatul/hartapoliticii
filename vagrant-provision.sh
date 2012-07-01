#!/bin/bash


apt-get update
apt-get install -y apache2-mpm-prefork libapache2-mod-php5 php5-mysql
apt-get install -y mysql-client #mysql-server

read -r -d '' MY_APACHE_CFG <<'EOF'
<VirtualHost *:80>
    DocumentRoot /vagrant/www
</VirtualHost>
EOF

echo "$MY_APACHE_CFG" > /etc/apache2/sites-available/hartapoliticii

a2dissite 000-default
a2ensite hartapoliticii
/etc/init.d/apache2 reload
