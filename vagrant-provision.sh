#!/bin/bash


apt-get update
apt-get install -y vim
apt-get install -y apache2-mpm-prefork libapache2-mod-php5 php5-mysql


### apache ###

read -r -d '' MY_APACHE_CFG <<'EOF'
<VirtualHost *:80>
    DocumentRoot /vagrant/www
</VirtualHost>
EOF
echo "$MY_APACHE_CFG" > /etc/apache2/sites-available/hartapoliticii

a2dissite 000-default
a2ensite hartapoliticii
/etc/init.d/apache2 reload


### database ###

read -r -d '' MYSQL_PRESEED <<'EOF'
mysql-server-5.0 mysql-server/root_password_again  string root
mysql-server-5.0 mysql-server/root_password        string root
EOF
echo "$MYSQL_PRESEED" > /var/local/preseeding-mysql-server.seed

debconf-set-selections /var/local/preseeding-mysql-server.seed
apt-get install -y mysql-client mysql-server

# TODO only run once:
mysqladmin -u root -proot password "" || echo "root password already blank?"
mysql -u root -e "create database hartapoliticii"
mysql -u root hartapoliticii < /vagrant/db/hartapoliticii_mock_data.sql
