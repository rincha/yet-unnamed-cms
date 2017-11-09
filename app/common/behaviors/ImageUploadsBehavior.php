<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\behaviors;

use Yii;
use yii\db\ActiveRecord;
use app\common\helpers\ImageHelper;
use app\common\helpers\AppHelper;
/**
 *
 * @author rincha
 */
class ImageUploadsBehavior extends \yii\base\Behavior {

    public $idAttribute=null;
    public $uploadAttribute='upload_images';
    public $uploadListAttribute='images';
    public $deleteAttribute='delete_images';
    public $folderName=null;
    public $uploadImageConvertJpg=false;
    public $getThumbnails=null;

    public function init()
    {
        parent::init();
    }

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE=>'deleteImages',
            ActiveRecord::EVENT_AFTER_INSERT=>'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE=>'afterSave',
            ActiveRecord::EVENT_BEFORE_UPDATE=>'beforeSave',
        ];
    }

    public function attach($owner) {
        parent::attach($owner);
        if ($this->folderName===null) {
            $this->folderName=substr(strrchr(get_class($this->owner), "\\"), 1);
        }
        if ($this->idAttribute===null && method_exists($this->owner, 'primaryKey')) {
            $this->idAttribute=$this->owner->primaryKey();
        }
        else {
            throw new \yii\base\Exception('Required attribute "idAttribute" or the owner must have a method "primaryKey".');
        }
    }

    public function getImageUrl($n=0) {
        if ($this->getImagePath($n)) {
            return Yii::getAlias('@web').DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.
                $this->folderName.DIRECTORY_SEPARATOR.$this->getTextId().DIRECTORY_SEPARATOR.$this->getImages()[$n];
        }
        else {
            return null;
        }
    }

    private function getBasePath() {
        return Yii::getAlias('@webroot').DIRECTORY_SEPARATOR.'uploads' . DIRECTORY_SEPARATOR . $this->folderName;
    }
    private function getTextId() {
        $id=[];
        if (is_array($this->idAttribute)) {
            foreach ($this->idAttribute as $attr) {
                $id[]=$this->owner->{$attr};
            }
        }
        else {
            $id[]=$this->owner->{$this->idAttribute};
        }
        return implode(',', $id);
    }
    private function getUploadPath() {

        return $this->getBasePath().DIRECTORY_SEPARATOR.$this->getTextId();
    }
    private function createUploadPath() {
        if (!file_exists($this->getUploadPath()) || !is_dir($this->getUploadPath())) {
            if (!mkdir($this->getUploadPath(), 0777, true)) {
                throw new Exception('Can`t create upload path!');
            } else {
                chmod($this->getUploadPath(), 0777);
            }
        }
    }
    private function getImages() {
        return array_map('trim', explode(',', $this->owner->{$this->uploadListAttribute}));
    }
    private function getImagePath($n=0) {
        $filename=\yii\helpers\ArrayHelper::getValue($this->getImages(), $n);
        Yii::trace($filename);
        $filepath=$this->getUploadPath().DIRECTORY_SEPARATOR.$filename;
        if ($filename && file_exists($filepath)) {
            return $filepath;
        }
        else {
            return null;
        }
    }
    private function generateImageName($file,$ext) {
        $filename= AppHelper::safeStr(AppHelper::translit($file->baseName));
        $filename_copy=$filename;
        $n=0;
        while (file_exists($this->getUploadPath().DIRECTORY_SEPARATOR.$filename.'.'.$ext)) {
            $filename=$filename_copy.'-'.$n;
        }
        return $filename.'.'.$ext;
    }
    private function deleteImage($n=0) {
        $list=$this->getImages();
        $file=$this->getImagePath($n);
        if ($file) {
            $func_thumb=$this->getThumbnails;
            if (is_callable($func_thumb)) {
                foreach ($func_thumb($file) as $thumbnail) {
                    if (file_exists($thumbnail)) {
                        unlink($thumbnail);
                    }
                }
            }
            if (file_exists($file)) {
                if (unlink($file)) {
                    $list[$n]='';
                }
            }
        }
        $this->owner->{$this->uploadListAttribute}=implode(',', $list);
    }
    public function deleteImages() {
        if (\yii\helpers\FileHelper::removeDirectory($this->getUploadPath())) {
            $this->owner->{$this->uploadListAttribute}=null;
            return true;
        }
        else {
            return false;
        }
    }
    private function saveImages() {
        $res=null;
        $this->createUploadPath();

        $files=[];
        if (is_array($this->owner->{$this->uploadAttribute})) {
            foreach ($this->owner->{$this->uploadAttribute} as $k=>$v) {
                if (!($this->owner->{$this->uploadAttribute}[$k] instanceof \yii\web\UploadedFile)) {
                    $files[$k] = \yii\web\UploadedFile::getInstance($this->owner, $this->uploadAttribute.'['.$k.']');
                }
                else {
                    $files[$k] = $this->owner->{$this->uploadAttribute}[$k];
                }
            }
        }
        else {
            if (!($this->owner->{$this->uploadAttribute} instanceof \yii\web\UploadedFile)) {
                $files = [\yii\web\UploadedFile::getInstance($this->owner, $this->uploadAttribute)];
            }
            else {
                $files = [$this->owner->{$this->uploadAttribute}];
            }
        }
        $list=$this->getImages();
        foreach ($files as $k=>$file) {
            if ($file) {
                $res=true;
                $ext = strtolower($file->extension);
                if ($this->uploadImageConvertJpg) {
                    if ($ext != 'jpg' && $ext != 'jpeg') {
                        \app\common\helpers\ImageHelper::resize($file->tempName, $file->tempName, 1600, 1600, ['quality' => 90, 'type' => 'r', 'save_type' => false]);
                    }
                    $ext='jpg';
                }
                $this->deleteImage($k);
                $filename=$this->generateImageName($file,$ext);
                $list[$k]=$filename;
                $dst=$this->getUploadPath().DIRECTORY_SEPARATOR.$filename;
                if (!$file->saveAs($dst)) {
                    throw new Exception('Can`t save file:' . $dst);
                }
            }
        }

        $this->owner->{$this->uploadListAttribute}=implode(',', $list);
        return $res;
    }

    public function beforeDelete() {
        $this->deleteImages();
    }

    public function beforeSave($e) {
        if ($this->owner->{$this->deleteAttribute} && is_array($this->owner->{$this->deleteAttribute})) {
            foreach ($this->owner->{$this->deleteAttribute} as $n=>$v) {
                if ($v) {
                    $this->deleteImage($n);
                }
            }
            $newlist=[];
            foreach (explode(',', $this->owner->{$this->uploadListAttribute}) as $im) {
                if ($im) {$newlist[]=$im;}
            }
            $this->owner->{$this->uploadListAttribute}=implode(',', $newlist);
        }
        elseif ($this->owner->{$this->deleteAttribute}) {
            $this->deleteImages();
        }
    }

    public function afterSave() {
        $this->saveImages();
        $owner=clone $this->owner;
        $owner->detachBehaviors();
        $owner->{$this->uploadAttribute}=null;
        //var_dump($owner->{$this->uploadListAttribute}); die();
        if (!$owner->save(true,[$this->uploadListAttribute])) {
            throw new \yii\base\Exception(strip_tags(\yii\helpers\Html::errorSummary($owner)));
        }
        $this->owner->refresh();
    }

}
