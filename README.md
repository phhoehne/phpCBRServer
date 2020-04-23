# phpCBRServer
A minimalistic comic book reader server with Angularjs frontend

Note: this is a quick 'n dirty solution + work-in-progress

## Prerequisites

* Apache Webserver (would probably also work on ngix)
* PHP 7.x
* PHP extension zip, rar & imagick (the latter is only required for creating thumbnails)
* Composer

## Installation

* clone or download repository
* move the folder phpCBRServer to on your webserver's directory (e.g. /var/www/html)
* in the phpCBRServer directory create the folders "thumbs" and "books"
* run "composer install" to install php dependencies
* rename or delete the file vendor/wapmorgan/unified-archive/src/Formats/Rar.php with fix/Rar.php
  (Sorry for the dirty hack, will check with the author to include my change)

  

