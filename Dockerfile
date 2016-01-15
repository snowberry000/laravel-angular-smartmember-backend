update user set password=PASSWORD("sm8rtm3mb3r") where User='root';

CREATE USER 'smartadmin'@'%' IDENTIFIED BY 'sm8rt8dm!n';
GRANT ALL PRIVILEGES ON smartmember.* TO 'smartadmin'@'%'WITH GRANT OPTION;

FROM centos:6.6

USER root

RUN rpm -Uvh https://mirror.webtatic.com/yum/el6/latest.rpm
RUN curl --silent --location https://rpm.nodesource.com/setup | bash -

#Setup LAMP Stack
#RUN yum update -y && yum -y install \

RUN yum -y install \
httpd \
mysql-server mysql \
php56w php56w-opcache \
php56w-mcrypt php56w-mysqlnd php56w-pdo php56w-xml php56w-xmlrpc php56w-mbstring \
nodejs 

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=bin
RUN mv /bin/composer.phar /bin/composer

COPY docker/smartmember.conf /etc/httpd/conf.d/smartmember.conf
COPY docker/run.sh /run.sh
RUN chmod -R 777 /run.sh

EXPOSE 80 3306

