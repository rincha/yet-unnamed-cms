<?php
/*
 *  @author rincha
 */

/*
write in .htaccess:
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(files/.*)/\.tmb/(\d*)([erc]{1})(\d*)/([^/]*)$ resizer.php?folder=$1&w=$2&h=$4&type=$3&file=$5 [last]
*/
class Resizer {

    public static $filename_patterns = ["/^[^<>:\"\/\|?*+%!@]*$/ui",'/^[^. ]{1}.*[^. ]{1}$/ui'];
    public static $foldername_patterns = ["/^[^<>:\"\/\|?*+%!@]*$/ui",'/^([^. ]{1})|([^. ]{1}.*[^. ]{1})$/ui'];
    public static $tmb_allowed = [];
    public static $base_path = null;
    public static $tmb_folder = '.tmb';

    public static $max_folder_depth=10;
    public static $max_folder_length=1024;
    public static $max_foldername_length=255;
    public static $max_filename_length=255;

    /*
     * options elements:
     * quality-качество для JPG,
     * type - x,s
     * save_type - сохранять тип изображения (иначе преобразовано в JPG)
     */



    public static function resize($dst, $src, $newW, $newH, $options = array('quality' => 80, 'type' => 'e', 'save_type' => true)) {
        $sizeIm = getimagesize($src);
        $options['quality'] = isset($options['quality']) ? $options['quality'] : 80;
        $options['type'] = isset($options['type']) ? $options['type'] : 'x';
        $options['save_type'] = isset($options['save_type']) ? $options['save_type'] : true;
        if ($sizeIm === false)
            return false;

        if ($options['type'] == 'r') {
            if (($newW / $newH) < ($sizeIm[0] / $sizeIm[1])) {
                $bigsize = ($sizeIm[0] > $newW) ? $newW : $sizeIm[0];
                $neww = $bigsize;
                $newh = ($sizeIm[1] / ($sizeIm[0] / $bigsize));
            } else {
                $bigsize = ($sizeIm[1] > $newH) ? $newH : $sizeIm[1];
                $neww = ($sizeIm[0] / ($sizeIm[1] / $bigsize));
                $newh = $bigsize;
            }
            $src_w = $sizeIm[0];
            $src_h = $sizeIm[1];
            $src_x = 0;
            $src_y = 0;
            $dst_x = 0;
            $dst_y = 0;
        } elseif ($options['type'] == 'e') {
            $neww = $newW;
            $newh = $newH;
            $src_w = $sizeIm[0];
            $src_h = $sizeIm[1];
            $src_x = 0;
            $src_y = 0;
            $dst_x = 0;
            $dst_y = 0;
            //если исходное шире по пропорциям
            if (($sizeIm[0] / $sizeIm[1]) > ($newW / $newH)) {
                //$dst_y
                $tmp_m = ($newW / $src_w);
                $newh = round($src_h * $tmp_m);
                $dst_y = round(($newH - $newh) / 2);
            }
            //если исходное уже по пропорциям
            else {
                //$dst_x
                $tmp_m = ($newH / $src_h);
                $neww = round($src_w * $tmp_m);
                $dst_x = round(($newW - $neww) / 2);
            }
        } else {
            //die('2');
            $neww = $newW;
            $newh = $newH;
            $dst_x = 0;
            $dst_y = 0;
            //если исходное шире по пропорциям
            if (($sizeIm[0] / $sizeIm[1]) > ($newW / $newH)) {
                $src_h = $sizeIm[1];
                $src_y = 0;
                $src_w = $newW * ($sizeIm[1] / $newH);
                $src_x = round(($sizeIm[0] - $src_w) / 2);
                //die("1XY: $src_x x $src_y | WH: $src_w x $src_h | NWH: $newW x $newH");
            }
            //если исходное уже по пропорциям
            else {
                $src_w = $sizeIm[0];
                $src_x = 0;
                $src_h = $newH * ($sizeIm[0] / $newW);
                $src_y = round(($sizeIm[1] - $src_h) / 2);
                //die("2XY: $src_x x $src_y | WH: $src_w x $src_h | NWH: $newW x $newH");
            }
        }

        $type = $sizeIm[2];
        switch ($type) {
            case 2:
                $im = imagecreatefromjpeg($src);
                break;
            case 1:
                $im = imagecreatefromgif($src);
                break;
            case 3:
                $im = imagecreatefrompng($src);
                break;
            default: return false;
        }

        if ($im === false)
            return false;

        if ($options['type'] == 'r') {
            $im1 = imagecreatetruecolor($neww, $newh);
        } else {
            $im1 = imagecreatetruecolor($newW, $newH);
        }
        $background = imagecolorallocate($im1, 255, 255, 255);
        imagefill($im1, 0, 0, $background);
        imagecopyresampled($im1, $im, $dst_x, $dst_y, $src_x, $src_y, $neww, $newh, $src_w, $src_h);
        //return false;
        if ($options['save_type']) {
            switch ($type) {
                case 2:
                    $res = imagejpeg($im1, $dst, $options['quality']);
                    break;
                case 1:
                    $res = imagegif($im1, $dst);
                    break;
                case 3:
                    $res = imagepng($im1, $dst);
                    break;
                default: return false;
            }
        } else
            $res = imagejpeg($im1, $dst, $options['quality']);
        imagedestroy($im);
        imagedestroy($im1);
        return $res;
    }

    public static function httpError($code, $title = '') {
        switch ($code) {
            case 400:
                header('HTTP/1.0 400 Bad Request', true, 400);
                echo "<h1>" . htmlspecialchars($title ? $title : '400: Bad request') . "</h1>";
                break;
            case 403:
                header('HTTP/1.0 403 Forbidden', true, 403);
                echo "<h1>" . htmlspecialchars($title ? $title : '403: Access denied') . "</h1>";
                break;
            case 404:
                header('HTTP/1.0 404 Not Found', true, 404);
                echo "<h1>" . htmlspecialchars($title ? $title : '404: Not found') . "</h1>";
                break;
        }
    }

}

$yii_images_params = require(__DIR__.'/../app/config/images.php');
Resizer::$tmb_allowed =[];
foreach ($yii_images_params['thumbnails'] as $k=>$v) {
    Resizer::$tmb_allowed=  array_merge(Resizer::$tmb_allowed,$v);
}
array_unique(Resizer::$tmb_allowed);
//var_dump(Resizer::$tmb_allowed);
$allowed_paths=[__DIR__.DIRECTORY_SEPARATOR.'files', __DIR__.DIRECTORY_SEPARATOR.'uploads'];
Resizer::$base_path=realpath(__DIR__);
if (isset($_GET['w']) && isset($_GET['h']) && isset($_GET['type']) && isset($_GET['file']) && isset($_GET['folder'])) {
    $w = $_GET['w'];
    if (!is_numeric($w)) {
        Resizer::httpError('400', 'Bad width');
        die();
    }
    $h = $_GET['h'];
    if (!is_numeric($h)) {
        Resizer::httpError('400', 'Bad height');
        die();
    }
    $type = $_GET['type'];
    if (!is_string($type)) {
        Resizer::httpError('400', 'Bad type');
        die();
    }
    if (!in_array($w . $type . $h, Resizer::$tmb_allowed)) {
        Resizer::httpError('400', 'Bad resolution: '.  htmlspecialchars($w . $type . $h));
        die();
    }

    //path check
    $folder=$_GET['folder'];
    if (!is_string($folder) || mb_strlen($folder,'UTF-8')>Resizer::$max_folder_length) {
        Resizer::httpError('400', 'Bad path');
        die();
    }
    $path_parts=explode(DIRECTORY_SEPARATOR, $folder);
    if (count($path_parts)>Resizer::$max_folder_depth) {
        Resizer::httpError('400', 'Bad path depth');
        die();
    }
    foreach ($path_parts as $part) {
        foreach (Resizer::$foldername_patterns as $pattern) {
            if (preg_match($pattern, $part)!==1) {
                Resizer::httpError('400', 'Bad folder. Pattern.'.$pattern.':'.$part);
                die();
            }
        }
    }
    $path=realpath(Resizer::$base_path.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $path_parts));
    if (!$path || !file_exists($path) || !is_dir($path)) {
        Resizer::httpError('404', 'Path not found');
        die();
    }
    $allowed=false;
    foreach ($allowed_paths as $allowed_path) {
        if (mb_strpos($path,$allowed_path,0,'UTF-8')===0) {
            $allowed=true;
        }
    }
    if (!$allowed) {
        Resizer::httpError('403', 'Path not allowed');
        die();
    }


    //filename check
    $file=$_GET['file'];
    if (!is_string($file) || mb_strlen($file,'UTF-8')>Resizer::$max_filename_length) {
        Resizer::httpError('400', 'Bad filename');
        die();
    }
    foreach (Resizer::$filename_patterns as $pattern) {
        if (preg_match($pattern, $file)!==1) {
            Resizer::httpError('400', 'Bad file. Pattern.'.$pattern);
            die();
        }
    }
    $filename = substr($file, 0, strrpos($file, '.'));
    $ext = substr($file, strrpos($file, '.'));
    if (!in_array(strtolower($ext), ['.png', '.gif', '.jpg', '.jpeg'])) {
        Resizer::httpError('404', 'Not Found: unsupported extension');
        die();
    }

    if (isset($test) && $test) {
        echo '<pre>';
        var_dump($_GET);
        echo '</pre>';
        echo '<pre>';
        var_dump(Resizer::$base_path);
        echo '</pre>';
        die();
    }

    $src = $path . DIRECTORY_SEPARATOR . $filename . $ext;

    $tmb_dir = $path . DIRECTORY_SEPARATOR . Resizer::$tmb_folder . DIRECTORY_SEPARATOR . $w . $type . $h;

    if (!file_exists($tmb_dir)) {
        mkdir($tmb_dir, 0777, true);
        chmod($tmb_dir, 0777);
    }

    $dst = $tmb_dir . DIRECTORY_SEPARATOR . $filename . $ext;

    if (file_exists($dst)) {
        Resizer::httpError('400', 'Bad request: static file exist');
        die();
    }

    if (file_exists($src)) {

        $res = Resizer::resize($dst, $src, $w, $h, ['type' => $type]);

        if ($res) {
            header('Content-Type: image/jpeg');
            readfile($dst);
            exit();
        } else {
            Resizer::httpError('500', 'Error');
            die();
        }
    } else {
        Resizer::httpError('404', 'Not Found ' . $filename . $ext);
        die();
    }
}
else {
    Resizer::httpError('400', 'Bad request');
    die();
}
?>