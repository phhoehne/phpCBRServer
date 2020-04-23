<?php


/**
 * Case-insensitive in_array() wrapper.
 *
 * @param  mixed $needle   Value to seek.
 * @param  array $haystack Array to seek in.
 *
 * @return bool
 */
function in_arrayi($needle, $haystack)
{
	return in_array(strtolower($needle), array_map('strtolower', $haystack));
}


/*
 * Recursive delete
 * 
 * @param string $dir   Name of the directory 
 */

function recurseRmdir($dir) {
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? recurseRmdir("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

/*
 * Remove unwanted filetypes from array of filenames
 * 
 * @param array $arr The array to clean
 * @param array $types Allowed types
 * 
 * 
 */

function reduceToFileTypes($arr, $types) {
    $tmp = [];
    foreach( $arr as $file ) {
        if(in_arrayi(pathinfo($file, PATHINFO_EXTENSION), $types) && !(strpos($file, '__MACOSX') === 0)) {
           $tmp[] = $file;
        }
    }
    return $tmp;
}


/*
 * create case insensitive patterns for glob or simular functions
 * ['jpg','gif'] as input
 * converted to: *.{[Jj][Pp][Gg],[Gg][Ii][Ff]}
 * 
 * @param array $arr_extensions Allowed extensions
 */
function globCaseInsensitivePattern($arr_extensions = []) {
   $opbouw = '';
   $comma = '';
   foreach ($arr_extensions as $ext) {
       $opbouw .= $comma;
       $comma = ',';
       foreach (str_split($ext) as $letter) {
           $opbouw .= '[' . strtoupper($letter) . strtolower($letter) . ']';
       }
   }
   if ($opbouw) {
       return '*.{' . $opbouw . '}';
   }
   // if no pattern given show all
   return '*';
}


/**
 * Calculate new image dimensions to new constraints
 *
 * @param Original X size in pixels
 * @param Original Y size in pixels
 * @return New X maximum size in pixels
 * @return New Y maximum size in pixels
 */
function scaleImage($x, $y, $cx, $cy) {
    //Set the default NEW values to be the old, in case it doesn't even need scaling
    list($nx, $ny) = array($x, $y);

    //If image is generally smaller, don't even bother
    if ($x >= $cx || $y >= $cx) {

        //Work out ratios
        if ($x > 0) {
            $rx = $cx / $x;
        }
        if ($y > 0) {
            $ry = $cy / $y;
        }

        //Use the lowest ratio, to ensure we don't go over the wanted image size
        if ($rx > $ry) {
            $r = $ry;
        }
        else {
            $r = $rx;
        }

        //Calculate the new size based on the chosen ratio
        $nx = intval($x * $r);
        $ny = intval($y * $r);
    }

    //Return the results
    return array($nx, $ny);
}



/*
  Create & store thumbnail
 * 
 * @param string $originalImageFilename File for which a thumbnail should be created
 * @param string $thumbnailFilename     Filename of the thumbnail
 * 
 */

function createThumb($originalImageFilename, $thumbnailFilename) {
    $thumb = new Imagick($originalImageFilename);
    $newMaximumWidth = 150;
    $newMaximumHeight = 210;

    //Work out new dimensions
    list($newX, $newY) = scaleImage(
            $thumb->getImageWidth(),
            $thumb->getImageHeight(),
            $newMaximumWidth,
            $newMaximumHeight);

    //Scale the image
    $thumb->thumbnailImage($newX, $newY);
    
    $thumb->setImageFormat('jpeg');

    //Write the new image to a file    
    try {
        $thumb->writeImage($thumbnailFilename . '.jpg');
    }
    catch (Exception $ex) {
        var_dump($ex);
        return false;
    }

    return true;
}
