# phpCBRServer
A minimalistic comic book reader server with Angularjs frontend

Note: this is a quick 'n dirty solution + work-in-progress

## Motivation
Reading comic books from one place (server) instead of having them copied to a device

## Features
* supported formats: CBR, CBZ, ZIP, RAR
* slim PHP backend
* responsive Angularjs frontend, made with mobile devices in mind

## Prerequisites

* Apache Webserver (will probably work on others as well)
* PHP 7.x
* PHP extensions zip, rar & imagick (the latter is only required for creating thumbnails)
* Composer

## Installation

* clone or download repository
* move the folder phpCBRServer to your webserver's home directory (e.g. /var/www/html)
* in the phpCBRServer directory create the folders "thumbs" and "books"
* change ownership / give read & write permissions to the directories just created (e.g.: sudo chown www-data:www-data ./thumbs)
* run "composer install" to install PHP dependencies
* rename or delete the file vendor/wapmorgan/unified-archive/src/Formats/Rar.php
* copy fix/Rar.php to vendor/wapmorgan/unified-archive/src/Formats/Rar.php
  (Sorry for the dirty hack, will check with the author to include my change)

## Get started
* copy some comic book files to the ./books directory
* go to the installation directory and run 'php makethumbs.php' or run it in browser (e.g.: http://myserver/phpCBR/makethumbs.php)
* open the reader in your browser (e.g.: http://myserver/phpCBR)
* happy reading!

## Navigation
| Function          | Keyboard / Mouse            | Touch Device       |
|-------------------|-----------------------------|--------------------|
| Next page         | mouse-click or cursor-right | tap or swipe-right |
| Previous page     | cursor-left                 | swipe-left         |
| Show controls     | cursor-down or move mouse   | swipe-down         |
| Back to book list | cursor-up or home           | swipe-up           |

## Limitations / Warnings
* all book files must be directly in the ./books directory, files from sub-directories are not read
* API inputs are not sanitized
* extracting & transferring pages may require quite some fast CPU, disk & bandwidth, therefore best used in a LAN and / or for just a few users
* every single page might be 1+ MBytes, so mind your data package (if not unlimited) on mobile networks

## API

### Get book list
Returns an array of filenames in ./books (supported filetypes only)
```
api.php/books
```

Sample response:
```
[
  "A Year of Marvels - August Infinite Comic 001.cbr",
  "Garfield-2011.cbr",
  "The Complete Calvin and Hobbes.cbr"
]
```

### Get number of pages of a book
```
api.php/pages/<filename of the book>

e.g.: api.php/pages/A%20Year%20of%20Marvels%20-%20August%20Infinite%20Comic%20001.cbr

```
Sample response:
```
{"numberOfPages":365}
```

### Get page from book
Returns the image file which represents a page in a book
```
api.php/page/<filename of the book>/<page number>

e.g.: api.php/page/A%20Year%20of%20Marvels%20-%20August%20Infinite%20Comic%20001.cbr/2
```
Response: file with MIME type "image/jpeg"

## To Do
### Backend
* Sanitize inputs
* Proper error handling
* Handle books in subdirectories
* Support for .pdf files

### Frontend
* Router for clean page structure
* Handling of errors from API
* Add help page 
* Remember last book read
* Add fullscreen mode for PC / Notebooks (currently no way for mobile devices)

## Contribute
As I'm a lousy, hobbyist, "when-kids-are in bed" developer: any contribution, bug report etc. is appreciated