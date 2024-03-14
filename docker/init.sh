#!/bin/bash
# S'occupe de créer les bons liens symboliques
cd htdocs
ln -sf . noms-geographiques
ln -sf ../media media
ln -sf /var/www/html/vendor/simplesamlphp/simplesamlphp/www saml
ln -sf /var/www/html/config/settings.local.php sites/default/settings.local.php
cd ..

