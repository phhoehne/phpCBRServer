<?php

require_once './vendor/autoload.php';
require_once './helpers.php';

use \wapmorgan\UnifiedArchive\UnifiedArchive;

$booksDir = 'books/';
$thumbsDir = 'thumbs/';
$tempDir = 'tmp/';

$allowedBookTypes = ['cbr', 'cbz', 'rar', 'zip'];
$allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif'];

function getBookList() {
    $books = [];

    foreach ($GLOBALS['allowedBookTypes'] as $bookType) {
        $tmp = glob($GLOBALS["booksDir"] . '*.' . $bookType);
        foreach ($tmp as $book) {
            array_push($books, basename($book));
        }
    }
    return $books;
}

function getCoverPage($title) {

    $targetFileName = null;

    $book = UnifiedArchive::open($GLOBALS["booksDir"] . $title);

    if ($book === FALSE) {
        echo "<p style\"color: red;\”>Can't open book $title";
        return null;
    }

    $pages = $book->getFileNames();
    if ($pages === FALSE) {
        echo "<p style\"color: red;\”>Can't retrieve pages from $title";
        return null;
    }

    $pages = array_values(reduceToFileTypes($pages, $GLOBALS['allowedImageTypes']));

    // @todo : Add error handling 
    if (!sort($pages)) {
        echo "<p>Failed to sort pages";
        flush();
        return null;
    }

    $ext = pathinfo($pages[0], PATHINFO_EXTENSION);


    if (!in_arrayi($ext, $GLOBALS['allowedImageTypes'])) {
        echo "<p>File type $ext not allowed";
        flush();
        return null;
    }


    $book->extractFiles($GLOBALS["tempDir"], $pages[0], false);


    //Now rename the file just extracted

    $targetFileName = $GLOBALS["tempDir"] . $title . "." . $ext;

    try {
        rename($GLOBALS["tempDir"] . $pages[0], $targetFileName);
    }
    catch (Exception $ex) {
        echo "<p style\"color: red;\”>Something went wrong when renaming file " . $GLOBALS["tempDir"] . $pages[0] . "to  $targetFileName";
        var_dump($ex);
    }

    return $targetFileName;
}

/*
 * Cleanup
 */

function cleanup() {
    recurseRmdir($GLOBALS['tempDir']);
    mkdir($GLOBALS['tempDir']);
}


/*
 * Main function
 */

function main() {

    $countOfThumbs = 0;

    foreach (getBookList() as $title) {

        $coverPageFile = getCoverPage($title);

        if (!is_null($coverPageFile)) {
            $thumbnailFile = $GLOBALS["thumbsDir"] . pathinfo($coverPageFile, PATHINFO_FILENAME);
            if (createThumb($coverPageFile, $thumbnailFile)) {
                $countOfThumbs++;
            }
            else {
                echo "<p>Failed to create thumbnail for $title";
            }
        }
        else {
            echo "<p>No cover file for $coverPageFile";
        }
    }

    echo "<p>$countOfThumbs thumbnails created";
    
    cleanup();
}


main();