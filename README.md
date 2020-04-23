# phpCBRServer
A minimalistic comic book reader server with Angularjs frontend

Note: this is a quick 'n dirty solution + work-in-progress

## Prerequisites

* Apache Webserver (will probably work on others as well)
* PHP 7.x
* PHP extensions zip, rar & imagick (the latter is only required for creating thumbnails)
* Composer

## Installation

* clone or download repository
* move the folder phpCBRServer to your webserver's home directory (e.g. /var/www/html)
* in the phpCBRServer directory create the folders "thumbs" and "books"
* run "composer install" to install PHP dependencies
* rename or delete the file vendor/wapmorgan/unified-archive/src/Formats/Rar.php
* copy fix/Rar.php to vendor/wapmorgan/unified-archive/src/Formats/Rar.php
  (Sorry for the dirty hack, will check with the author to include my change)



