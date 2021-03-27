FROM php:7.4-apache
RUN echo 'PassEnv URI' > /etc/apache2/conf-enabled/expose-env.conf
RUN echo 'PassEnv IP_API' >> /etc/apache2/conf-enabled/expose-env.conf
# Copy application source
COPY ./html/ /var/www/html/
RUN chown -R www-data:www-data /var/www
RUN a2enmod rewrite