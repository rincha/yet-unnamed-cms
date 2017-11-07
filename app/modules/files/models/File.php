<?php

namespace app\modules\files\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\models\Settings;
use app\modules\files\models\Folder;

/**
 * This is the model class for table "{{%files_file}}".
 *
 * @property integer $file_id
 * @property integer $folder_id
 * @property string $name
 * @property string $pathname
 * @property string $ext
 * @property string $description
 * @property string $info
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property string $new_file (write-only)
 *
 * @property Folder $folder
 */
class File extends \yii\db\ActiveRecord {

    public $new_file;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%files_file}}';
    }

    public function scenarios() {
        return [
            'default'=>$this->attributes(),
            'import'=>$this->attributes(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['folder_id', 'name'], 'required'],
            [['folder_id'], 'integer'],
            [['folder_id'], 'exist', 'targetClass' => 'app\modules\files\models\Folder', 'targetAttribute' => 'folder_id'],
            [['name','pathname'], 'string', 'max' => 255],
            [['name'], 'match', 'pattern' => '/^[^:\/\|*?"<>+%!@]*[^:\/\|*?"<>+%!@ .]+$/u'],
            [['pathname'], 'filter', 'filter' => 'trim'],
            [['pathname'], 'filter', 'filter' => function ($value) {return mb_strtolower($value, 'UTF-8');}],
            [['pathname'], 'match', 'pattern' => '/^[a-zа-я0-9(). _-]*[a-zа-я0-9()_-]+$/u'],
            [['description'], 'string', 'max' => 1024],
            [['type'], 'string', 'max' => 64],
            [['new_file'], 'file', 'extensions' => $this->getAllowedExts(), 'maxSize' => $this->getMaxFileSize()],
            [['name', 'ext', 'folder_id'], 'unique', 'targetAttribute' => ['name', 'ext', 'folder_id']],
            [['pathname', 'ext', 'folder_id'], 'unique', 'targetAttribute' => ['pathname', 'ext', 'folder_id']],
        ];
    }

    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function getMaxFileSize() {
        return Yii::$app->getModule('files')->maxFileSize;
    }

    public function getAllowedExts() {
        return \yii\helpers\StringHelper::explode(Yii::$app->getModule('files')->allowedExts);
    }

    public function getAllowedImegesExts() {
        return \yii\helpers\StringHelper::explode(Yii::$app->getModule('files')->allowedImagesExts);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'file_id' => 'ID',
            'folder_id' => Yii::t('files','Folder'),
            'name' => Yii::t('files','Name'),
            'pathname' => Yii::t('files','Path name'),
            'ext' => Yii::t('files','Extension'),
            'description' => Yii::t('files','Description'),
            'info' => Yii::t('files','Information'),
            'type' => Yii::t('files','Type'),
            'created_at' => Yii::t('files','Created time'),
            'updated_at' => Yii::t('files','Updated time'),
            'new_file' => Yii::t('files','File'),
        ];
    }

    public function toPathName($str) {
        return preg_replace("/[^a-zа-я0-9(). _-]/u", '-', trim(mb_strtolower($str, 'UTF-8'),'.'));
    }

    public function beforeValidate() {
        if ($this->scenario!=='import' && ($this->isNewRecord || $this->new_file)) {
            if (!$this->new_file instanceof \yii\web\UploadedFile)
                $file = \yii\web\UploadedFile::getInstance($this, 'new_file');
            else
                $file = $this->new_file;
            if ($file) {
                $this->ext = strtolower($file->extension);
                $this->type = $file->type;
                if (!$this->name)
                    $this->name = $file->baseName;
                $this->pathname=$this->toPathName($this->name);
                //die($this->pathname);
                $info = [
                    'original_name' => $file->name,
                    'size' => $file->size,
                    'mime' => $file->type
                ];
                if (in_array($this->ext, $this->getAllowedImegesExts())) {
                    $temp = getimagesize($file->tempName);
                    if ($temp)
                        $info['image'] = [
                            'width' => $temp[0],
                            'height' => $temp[1],
                            'type' => $temp[2],
                            'bits' => isset($temp['bits']) ? $temp['bits'] : '',
                            'channels' => isset($temp['channels']) ? $temp['channels'] : '',
                        ];
                }
                $this->info = json_encode($info);
            }
        }
        return parent::beforeValidate();
    }

    public function beforeSave($insert) {
        if ($this->scenario!=='import' && ($insert || $this->new_file)) {
            if (!$this->new_file instanceof \yii\web\UploadedFile)
                $file = \yii\web\UploadedFile::getInstance($this, 'new_file');
            else
                $file = $this->new_file;

            $this->ext = strtolower($file->extension);
            $this->type = $file->type;
            $info = [
                'original_name' => $file->name,
                'size' => $file->size,
                'mime' => $file->type
            ];
            if (in_array($this->ext, $this->getAllowedImegesExts())) {
                $temp = getimagesize($file->tempName);
                if ($temp)
                    $info['image'] = [
                        'width' => $temp[0],
                        'height' => $temp[1],
                        'type' => $temp[2],
                        'bits' => isset($temp['bits']) ? $temp['bits'] : '',
                        'channels' => isset($temp['channels']) ? $temp['channels'] : '',
                    ];
            }
            $this->info = json_encode($info);
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) {
        if (!$insert) {
            if (
                    (isset($changedAttributes['folder_id'])&&$changedAttributes['folder_id']!=$this->folder_id)
                    ||
                    (isset($changedAttributes['pathname'])&&$changedAttributes['pathname']!=$this->pathname)
                ) {
                $model=new File();
                $model->load([$this->className()=>$this->attributes],$this->className());
                if (isset($changedAttributes['folder_id'])) {
                    $model->folder_id=$changedAttributes['folder_id'];
                }
                if (isset($changedAttributes['pathname'])) {
                    $model->pathname=$changedAttributes['pathname'];
                }

                $old_path=self::getFullFileNameByModel($model);
                $new_path=self::getFullFileNameByModel($this);

                if (!rename($old_path, $new_path)) {
                    throw new Exception('Unable to rename folder from '.$old_path.' to '.$new_path);
                }
            }
        }
        if (!$this->new_file instanceof \yii\web\UploadedFile) {
            $file = \yii\web\UploadedFile::getInstance($this, 'new_file');
        }
        else {
            $file = $this->new_file;
        }
        if ($file) {
            $old_umask = umask(0);
            if (!$file->saveAs($this->getFullFileName())) {
                throw new \yii\base\Exception('Unable to write file '.$this->getFullFileName());
            }
            umask($old_umask);
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete() {
        if (file_exists($this->getFullFileName())) {
            unlink($this->getFullFileName());
            if (in_array($this->ext, $this->getAllowedImegesExts())) {
                \app\common\helpers\ImageHelper::deleteThumbnails($this->getFullFileName());
            }
        }
        return parent::beforeDelete();
    }

    public function getFullFileName() {
        return $this->folder->fullPath . DIRECTORY_SEPARATOR . $this->pathname . '.' . $this->ext;
    }

    public static function getFullFileNameByModel($model) {
        $folder=Folder::findOne($model->folder_id);
        $prev=implode(DIRECTORY_SEPARATOR, Folder::getPathnameTree($folder));
        if ($prev) {$prev.=DIRECTORY_SEPARATOR;}
        return  Yii::$app->getModule('files')->path.DIRECTORY_SEPARATOR.
                $prev. $model->pathname . '.' . $model->ext;
    }

    public function getTmb($type) {
        $name = '/' . rawurlencode($this->pathname) . '.' . $this->ext;
        return str_replace($name, '/.tmb/' . $type . $name, $this->url);
    }

    public function getUrl() {
        return $this->folder->url . DIRECTORY_SEPARATOR . rawurlencode($this->pathname) . '.' . $this->ext;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFolder() {
        return $this->hasOne(Folder::className(), ['folder_id' => 'folder_id']);
    }

    public function getFileType() {
        if (in_array($this->ext, array('jpg', 'jpeg', 'gif', 'png'))) return 'image';
        elseif (in_array($this->ext, array('flv', 'mp4', 'avi', 'ogg', 'swf'))) return 'video';
        elseif (in_array($this->ext, array('txt', 'rtf', 'doc', 'docx', 'odt','pdf'))) return 'text';
        elseif (in_array($this->ext, array('csv', 'xls', 'xlsx', 'ods'))) return 'excel';
        else return '';
    }

    public function getImageInfo() {
        return \yii\helpers\ArrayHelper::getValue(json_decode($this->info,true),'image');
    }

}
