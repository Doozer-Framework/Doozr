<?php


require_once '../../../Controller/Core/Core.php';

// instanciate DoozR core
$DoozR = DoozR_Core::getInstance();


// quality in percent
define ('QUALITY', 90);

define ('PIN_NAME', 0);
define ('PIN_WIDTH', 1);
define ('PIN_HEIGHT', 2);
define ('PIN_MAXIMUM', 3);
define ('PIN_DIAGONAL', 4);
define ('PIN_AREA', 5);

// array indices for getimagesize()
define ('IMG_WIDTH', 0);
define ('IMG_HEIGHT', 1);
define ('IMG_TYPE', 2);
define ('IMG_SIZETEXT', 3);

// imagetypes for getimagesize() : IMG_TYPE
define ('IMG_TYPE_GIF', 1);
define ('IMG_TYPE_JPG', 2);
define ('IMG_TYPE_PNG', 3);
define ('IMG_TYPE_SWF', 4);


extract($_GET);

$error = '';


if (!isset($width)) {
    $width = 0;
}

if (!isset($height)) {
    $height = 0;
}

if (!isset($maximum)) {
    $maximum = 0;
}

if (!isset($diagonal)) {
    $diagonal = 0;
}

if (!isset($area)) {
    $area = 0;
}



//===== supplemental functions ==============================================
function _explode($operator, $operand)
{
    return strlen($operand) ? explode($operator, $operand) : array();
}

function _implode($operator, $operand)
{
    return sizeof($operand) ? implode($operator, $operand) : '';
}

function BuildImageName($name, $width = 0, $height = 0, $maximum = 0, $diagonal = 0, $area = 0)
{
    // {name.}ext
    if (sizeof($parts = _explode('.', $name)) >= 2) {
        $extension = array_pop($parts);
    }

    if ($area) {
        array_push($parts, "a$area");
    } elseif ($diagonal) {
        array_push($parts, "d$diagonal");
    } elseif ($maximum ) {
        array_push($parts, "m$maximum");
    } else {
        if ($width) {
            array_push($parts, "w$width");
        }

        if ($height) {
            array_push($parts, "h$height");
        }
    }

    if (isset($extension)) {
       array_push($parts, $extension);
    }

    return _implode('.', $parts);
}



function ParseImageName($name)
{
    $result = array();

    // {name.}size.ext
    if (sizeof($parts = _explode('.', $name)) >= 3) {
        $extension = array_pop($parts);
        $sizeparams = strtolower(array_pop($parts));

        $result[PIN_NAME]     = _implode('.',  $parts) . '.' . $extension;
        $result[PIN_WIDTH]    = ($i = strpos($sizeparams, 'w')) === FALSE ? 0 : intval(substr($sizeparams, $i + 1));
        $result[PIN_HEIGHT]   = ($i = strpos($sizeparams, 'h')) === FALSE ? 0 : intval(substr($sizeparams, $i + 1));
        $result[PIN_MAXIMUM]  = ($i = strpos($sizeparams, 'm')) === FALSE ? 0 : intval(substr($sizeparams, $i + 1));
        $result[PIN_DIAGONAL] = ($i = strpos($sizeparams, 'd')) === FALSE ? 0 : intval(substr($sizeparams, $i + 1));
        $result[PIN_AREA]     = ($i = strpos($sizeparams, 'a')) === FALSE ? 0 : intval(substr($sizeparams, $i + 1));
    } else {
        $result[PIN_NAME]     = $name;
        $result[PIN_WIDTH]    = 0;
        $result[PIN_HEIGHT]   = 0;
        $result[PIN_MAXIMUM]  = 0;
        $result[PIN_DIAGONAL] = 0;
        $result[PIN_AREA]     = 0;
    }

    return $result;
}


function ResizeImage($dst_name = '', $src_name, $width = 0, $height = 0, $maximum = 0, $diagonal = 0, $area = 0, $enlarge = FALSE)
{
    $result = FALSE;

    if (strlen($src_name) && ($width || $height || $maximum || $diagonal || $area)) {

        $src_info = getimagesize($src_name);

        if ($area) {
            $width  = $src_info[IMG_WIDTH] * sqrt($area / ($src_info[IMG_WIDTH] * $src_info[IMG_HEIGHT]));
            $height = 0;
        } elseif ($diagonal) {
            $width  = $src_info[IMG_WIDTH] * $diagonal / sqrt($src_info[IMG_WIDTH] * $src_info[IMG_WIDTH] + $src_info[IMG_HEIGHT] * $src_info[IMG_HEIGHT]);
            $height = 0;
        } elseif ($maximum) {
            if ($src_info[IMG_WIDTH] > $src_info[IMG_HEIGHT]) {
                $width  = $maximum;
                $height = 0;
            } else {
                $width  = 0;
                $height = $maximum;
            }
        }

        if (!$width) {
            $width =  $src_info[IMG_WIDTH] * $height / $src_info[IMG_HEIGHT];
        } elseif (!$height) {
            $height = $src_info[IMG_HEIGHT] * $width / $src_info[IMG_WIDTH];
        }

        if (!strlen($dst_name)) {
            $dst_name = $src_name;
        }

        if ($enlarge || ($width < $src_info[IMG_WIDTH] && $height < $src_info[IMG_HEIGHT])) {

            switch ($src_info[IMG_TYPE]) {
                case IMG_TYPE_GIF:
                    $src = imagecreatefromgif ($src_name);
                    break;

                case IMG_TYPE_JPG:
                    $src = imagecreatefromjpeg($src_name);
                    break;

                case IMG_TYPE_PNG:
                    $src = imagecreatefrompng ($src_name);
                    break;

                default:
                    $src = NULL;
                    break;
            }

            $dst = imagecreatetruecolor($width, $height);
            $col = imagecolorallocate($dst, 255, 255, 255);

            imagefill($dst, 0, 0, $col);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $src_info[IMG_WIDTH], $src_info[IMG_HEIGHT]);

            switch ($src_info[IMG_TYPE]) {
                case IMG_TYPE_GIF:
                    imagegif($dst, $dst_name, QUALITY);
                    break;

                case IMG_TYPE_JPG:
                    imagejpeg($dst, $dst_name, QUALITY);
                    break;

                case IMG_TYPE_PNG:
                    imagepng($dst, $dst_name, QUALITY);
                    break;

                default:
                    break;
            }

            imagedestroy($src);
            imagedestroy($dst);

            $result = TRUE;
        }
    }

    return $result;
}

function SendImage($filename)
{
    $result = FALSE; $info = getimagesize($filename);

    switch ($info[IMG_TYPE]) {
        case IMG_TYPE_GIF:
            $type = 'gif';
            break;

        case IMG_TYPE_JPG:
            $type = 'jpeg';
            break;

        case IMG_TYPE_PNG:
            $type = 'png';
            break;

        default:
            $type = '';
            break;
    }

    if (strlen($type)) {
        if ($handle = fopen($filename, 'rb')) {
            $length = filesize ($filename);
            $buffer = fread ($handle, $length);
            fclose($handle);

            //header("Content-Length: $length");
            //header("Content-Type: image/{$type}");
            //header("Cache-Control: no-cache");
            //header("Pragma: no-cache");

            echo $buffer;

            $result = TRUE;
        }
    }

    return $result;
}


//===== processing ==========================================================

if (isset($src) && strlen($src)) {

    //@@@ bugfix for decoding problem (%23 <-> #)
    $src = preg_replace('/%([0-9a-f]{2})/ie', "chr(hexdec('\\1'))", $src);

    $filename = $DoozR->correctPath(DOOZR_DOCUMENT_ROOT . $src);		// filename is relative to root of domain

    if ($width || $height || $maximum || $diagonal || $area) {
        $filename = BuildImageName($filename, $width, $height, $maximum, $diagonal, $area);		// combine basename and sizeparameter
    }

    if (file_exists($filename)) {
        // deliver image
        if (!SendImage($filename)) {
            $error = "'$src' - not an image";
        }

    } else {
        // extract basename and sizeparameter
        $info = ParseImageName($filename);

        if ( file_exists($info[PIN_NAME]) ) {

            // create image by resizing
            if ( !ResizeImage($filename, $info[PIN_NAME], $info[PIN_WIDTH], $info[PIN_HEIGHT], $info[PIN_MAXIMUM], $info[PIN_DIAGONAL], $info[PIN_AREA]) ) {
                // if sizing fails, take source file for delivering
                $filename = $info[PIN_NAME];
            }

            // deliver image
            if (!SendImage($filename)) {
                $error = "'$src' - not an image";
            }
        } else {
            $error = "'$src' - file does not exist";
        }
    }
} else {
    $error = 'no name specified';
}


echo $error;
die();


if ( strlen($error) )
{
    /*
    if ( strlen(ERRORLOG))
    {
        if ( $handle = fopen(ERRORLOG, 'a+') )
        {
            fwrite($handle, sprintf("%s - ERROR: %s\r\n", date('d.m.y H:i:s'), $error));
            fclose($handle);
        }
    }
    */

       // echo "ERROR: $error<br />";
    header('HTTP/1.1 404 Not Found');
}

?>
