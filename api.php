<?php

/**
 * @author Philipp Höhne
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

require_once './vendor/autoload.php';
require_once 'helpers.php';

use \wapmorgan\UnifiedArchive\UnifiedArchive;

$api_version = '0.1';
$booksDir = 'books/';
$allowedBookTypes = ['cbr', 'cbz', 'rar', 'zip',];
$allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'Jpg', 'Jpeg', 'Png', 'Gif', 'JPG', 'JPEG', 'PNG', 'GIF'];


dispatch('/', 'hello');

function hello() {
    return "phpCBR API version " . $GLOBALS['api_version'];
}

dispatch('/books', 'getBookList');

function getBookList() {
    $books = [];
    
    $allowedBookTypesPatterns = globCaseInsensitivePattern($GLOBALS['allowedBookTypes']);
    
    $tmp = (array) glob($GLOBALS['booksDir'] . $allowedBookTypesPatterns, GLOB_BRACE);
    
    foreach ($tmp as $book) {
                array_push($books, basename($book));
    }      

    sort($books);

    return json($books);
}

dispatch('/booklist', 'getBookList2');

function getBookList2() {
    return json_encode(dirToArray('./books'));
}

function dirToArray($dir) {
  
     $result = array();
  
     foreach (scandir($dir) as $key => $value)
     {
        if (!in_array($value,array(".","..")))
        {
           if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
           {
              $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
           }
           else
           {
              if(in_array(strtolower(pathinfo($value, PATHINFO_EXTENSION)),$GLOBALS['allowedBookTypes'])) {
                 $result[] = $value;   
              }
           }
        }
     }
    
     return $result;
  } 

dispatch('/pages/:title', 'getNumberOfPages');

function getNumberOfPages() {
    $title = params('title');
    $book = UnifiedArchive::open($GLOBALS['booksDir'] . $title);
    if ($book === FALSE) {
        halt("Cannot open $title"); // Add proper error handling
    }

    $pagesAll = $book->getFileNames();
    if ($pagesAll === FALSE) {
        halt("Cannot retrieve entries"); // Add proper error handling
    }
       
    $pages = reduceToFileTypes($pagesAll, $GLOBALS['allowedImageTypes']);
    
    return json(['numberOfPages' => sizeof($pages)]);
}

dispatch('/page/:title/:page', 'getPage');

function getPage() {
    $title = params('title');
    $page = intval(params('page') - 1); // add error checking
    $book = UnifiedArchive::open($GLOBALS['booksDir'] . $title);
    $pages = $book->getFileNames();
    
    $pages = array_values(reduceToFileTypes($pages, $GLOBALS['allowedImageTypes']));
        
    // @todo : Add error handling 
    if(!sort($pages)) {
        echo "<p>Failed to sort pages";
        flush();
        die("Can't sort pages");
    }
 
    $pageName = $pages[$page];
    
    $pageData = $book->getFileData($pageName);
    $fileSize = $pageData->uncompressedSize;
    $ext = strtolower($ext = pathinfo($pageData->filename, PATHINFO_EXTENSION));
    
    switch ($ext) {
        case "jpg":
            $contentType = "image/jpeg";
            break;
        case "jpeg":
            $contentType = "image/jpeg";
            break;            
        case "png":
            $contentType = "image/png";
            break;
        case "gif":
            $contentType = "image/gif";
            break;        
        default:
            echo "error"; // @todo : add proper error handling
            flush();
            die("Unsupported page format");
    }

    header("Content-Type: $contentType");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: $fileSize");
    header('Content-Disposition: inline; filename="' . $pageData->filename . '"');
    //header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));
    header('Cache-Control: max-age=15552000');
    header('pragma: foo'); // Hack, required, otherwise images won't be cached
    
    echo $book->getFileContent($pageName);
    
    unset($book);
    unset($pages);
    unset($pageData);
}

run();
