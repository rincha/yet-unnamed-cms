<?php

namespace app\common\helpers;

use Yii;
/**
 *
 * @author rincha
 */
class ImageHelper {

    private static $_dummyImage;

    /**
     * Get Image thumbnail
     * @param string $image - image path
     * @param string $thumbnail - image thumbnail size, see app/config/images.php
     * @param mixed $default false - no default image, true - default image by getDummyImage, string - custom default image
     * @param mixed $params false or array additional qury params
     */
    public static function getThumbnail($image,$thumbnail,$default=false,$params=false) {
        $pathinfo=pathinfo($image);
        if ($pathinfo && isset($pathinfo['dirname'])) {
            return $pathinfo['dirname'].DIRECTORY_SEPARATOR.'.tmb'.DIRECTORY_SEPARATOR.$thumbnail.DIRECTORY_SEPARATOR.$pathinfo['basename'].(
                    $params?'?'.http_build_query($params):'');
        }
        else {
            if ($default===false) {
                return null;
            }
            elseif ($default===true) {
                return self::getDummyImage(explode('x',preg_replace('/\D/ui', 'x', $thumbnail)));
            }
            else {
                return $default;
            }
        }
    }

    public static function getThumbnailsPaths($image,$existOnly=true) {
        $res=[];
        $pathinfo=pathinfo($image);
        foreach (\Yii::$app->params['images']['thumbnails'] as $type=>$thumbnails) {
            foreach ($thumbnails as $tmb) {
                $tmbpath=$pathinfo['dirname'].DIRECTORY_SEPARATOR.'.tmb'.DIRECTORY_SEPARATOR.$tmb.DIRECTORY_SEPARATOR.$pathinfo['basename'];
                if (!$existOnly || file_exists($tmbpath)) {
                    $res[]=$tmbpath;
                }
            }
        }
        return $res;
    }

    public static function deleteThumbnails($image) {
        foreach (self::getThumbnailsPaths($image, true) as $file) {
            unlink($file);
        }
    }

    /**
     * @return mixed - string image format (GIF,JPG,JPG), null if unknown type, false if not image
     */
    public static function isImage($file) {
        @$formatInfo = getimagesize($file);
        if($formatInfo !== false) {
                $mimeType = isset($formatInfo['mime']) ? $formatInfo['mime'] : null;
                $format = null;
                switch ($mimeType)
                {
                        case 'image/gif':
                                $format = 'GIF';
                                break;
                        case 'image/jpeg':
                                $format = 'JPG';
                                break;
                        case 'image/png':
                                $format = 'JPG';
                                break;
                        default:
                                break;
                }
                return $format;
        }
        return false;
    }

    /**
     * @param string|yii\web\UploadedFile $image  - string with path to image file or instance of yii\web\UploadedFile
     * @return mixed true if ok, else array
     */
    public static function canChangeImage($image) {
        if ($image instanceof \yii\web\UploadedFile) {
            $image=$image->tempName;
        }
        elseif (!is_string($image) || !file_exists($image)) {
            throw new \yii\base\Exception('Image must be path to file or instance of yii\web\UploadedFile');
        }
        $params=getimagesize($image);
        if (!$params) {
            throw new \yii\base\Exception('Image must be path to image file or instance of yii\web\UploadedFile');
        }
        $memoryNeeded = round ( ($params [0] * $params [1] * $params ['bits'] * 3 / 8 + Pow ( 2, 16 )) * 1.65 );
        $memoryNeeded += memory_get_usage ( true );
        $memoryAllowed=ini_get('memory_limit');
        if (strpos($memoryAllowed, 'M')) {
                $memoryAllowed=  str_replace('M', '', $memoryAllowed);
                $memoryAllowed=$memoryAllowed*1024*1024;
        }
        elseif(strpos($memoryAllowed, 'K')) {
                $memoryAllowed=  str_replace('K', '', $memoryAllowed);
                $memoryAllowed=$memoryAllowed*1024;
        }
        if ($memoryNeeded>=($memoryAllowed-4*1024*1024)) {
            return ['memoryNeeded'=>$memoryNeeded, 'memoryAllowed'=>$memoryAllowed, 'width'=>$params[0], 'height'=>$params[1], 'bits'=>$params['bits']];
        }
        else {
            return true;
        }
    }

    public static function getDummyImage($size=[150,150],$color=[220,220,220]) {
        $key=$size[0].'x'.$size[1];
        if (!isset(self::$_dummyImage[$key])) {
            $stringdata=Yii::$app->cache->get('ImageHelper.dummyImage.'.$key);
            if (!$stringdata) {
                $image=imagecreatetruecolor($size[0], $size[1]);
                $color=imagecolorallocate($image, $color[0], $color[1], $color[2]);
                imagefill($image, 0, 0, $color);
                ob_start();
                imagepng($image);
                $stringdata = 'data:image/png;base64,'.base64_encode(ob_get_contents());
                ob_end_clean();
                Yii::$app->cache->set('ImageHelper.dummyImage.'.$key, $stringdata,60*60*24);
            }
            self::$_dummyImage[$key]=$stringdata;
        }
        return self::$_dummyImage[$key];
    }

    public static function resize($dst, $src, $newW, $newH, $options = array('quality' => 80, 'type' => 'e', 'save_type' => true)) {
        $sizeIm = getimagesize($src);
        $options['quality'] = isset($options['quality']) ? $options['quality'] : 80;
        $options['type'] = isset($options['type']) ? $options['type'] : 'e';
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
        } else {
            $res = imagejpeg($im1, $dst, $options['quality']);
        }
        imagedestroy($im);
        imagedestroy($im1);
        return $res;
    }

}
