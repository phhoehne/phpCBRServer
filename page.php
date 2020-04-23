<?php

$trace = true;
$startTime = microtime_float();
 error_log("Page requested");

$booksDir = 'books/';

if (!filter_input(INPUT_GET, "title", FILTER_SANITIZE_STRING)) {
    die("Invalid title");
}
$title = $booksDir . filter_input(INPUT_GET, "title", FILTER_SANITIZE_STRING);

if (!filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT) || filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT) < 1) {
    $page = 0;
} else {
    $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT) - 1;
}

$book = RarArchive::open($title);
if ($book === FALSE) {
    die("Cannot open $title");
}

$pages = $book->getEntries();
if ($pages === FALSE) {
    die("Cannot retrieve entries");
}

$totalNumberOfPages = sizeof($pages);

// Return last page if page is beyond the last page
// Doesn' work for all files, unclear why
if($page >= $totalNumberOfPages) {
    $page = $totalNumberOfPages - 1;
}


$pageFileName = $pages[$page]->getName();

$pageImage = $book->getEntry($pageFileName);

if ($pageImage === FALSE) {
    die("Could not get such entry");
}


$contentType = "image/jpeg";
$filesize = $pageImage->getUnpackedSize();

header("Content-Type: $contentType");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $filesize");

$stream = $pageImage->getStream();
if ($stream === false) {
    die("Failed to obtain stream.");
}

rar_close($book); //stream is independent from file

while (!feof($stream)) {
    $buff = fread($stream, 8192);
    if ($buff !== false) {
        echo $buff;
    }
    else {
        break;
    } //fread error
}

fclose($stream);

if($trace) {
    $endTime = microtime_float();
    $duration = $endTime - $startTime;                 
    error_log("Took $duration to deliver $title");
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

