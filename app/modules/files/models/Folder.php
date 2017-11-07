<?php

namespace app\modules\files\models;

use Yii;

/**
 * This is the model class for table "{{%files_folder}}".
 *
 * @property integer $folder_id
 * @property integer $parent_id
 * @property string $name
 * @property string $pathname
 * @property string $description
 * @property string $type
 * @property string $special
 *
 * @property Files[] $files
 * @property Folder $parent
 * @property Folder[] $childs
 */
class Folder extends \yii\db\ActiveRecord {

    const TYPE_DEFAULT='';
    const TYPE_GALLERY='gallery';
    const TYPE_DOCS='docs';

    public $has_children;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%files_folder}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name','pathname'], 'required'],
            [['parent_id'], 'integer'],
            [['parent_id'], 'exist', 'targetClass' => 'app\modules\files\models\Folder', 'targetAttribute' => 'folder_id'],
            [['parent_id'], '_vParent'],
            [['description'], 'string', 'max'=>32*1024],

            [['pathname'], 'filter', 'filter' => 'trim'],
            [['pathname'], 'filter', 'filter' => function ($value) {return mb_strtolower($value, 'UTF-8');}],
            [['name','pathname'], 'string', 'max' => 255],
            [['name'], 'match', 'pattern' => '/^[^:\/\|*?"<>+%!@]*[^:\/\|*?"<>+%!@ .]+$/u'],
            [['pathname'], 'match', 'pattern' => '/^[a-zа-я0-9(). _-]*[a-zа-я0-9()_-]+$/u'],
            [['type', 'special'], 'string', 'max' => 64],
            [['type'], 'in', 'range' => array_keys(self::getTypeList())],
            [['name', 'parent_id'], 'unique', 'targetAttribute' => ['name', 'parent_id']],
            [['pathname', 'parent_id'], 'unique', 'targetAttribute' => ['pathname', 'parent_id']],
        ];
    }

    public function _vParent() {
        if (!$this->parent_id) {
            $this->parent_id = null;
        } else {
            if (!$this->isNewRecord) {
                if ($this->parent_id == $this->folder_id) {
                    $this->addError('parent_id', Yii::t('files','Attribute {attribute} can not point to itself.',['attribute'=>$this->getAttributeLabel('parent_id')]));
                    return;
                }
                $childs = $this->getListAll($this->folder_id, 0);
                foreach ($childs as $child)
                    if ($child->folder_id == $this->parent_id) {
                        $this->addError('parent_id', Yii::t('files','The attribute {attribute} can not point to child element.',['attribute'=>$this->getAttributeLabel('parent_id')]));
                        return;
                    }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'folder_id' => 'ID',
            'parent_id' => Yii::t('files','Parent folder'),
            'name' => Yii::t('files','Name'),
            'pathname' => Yii::t('files','Path name'),
            'description' =>  Yii::t('files','Description'),
            'type' =>  Yii::t('files','Type'),
            'special' =>  Yii::t('files','Special'),
        ];
    }

    public function createFolder() {
        if (!$this->isNewRecord) {
            $old_umask = umask(0);
            $res = mkdir($this->fullPath, \Yii::$app->getModule('files')->dirMode, true);
            umask($old_umask);
            return $res;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes) {
        if ($insert && !$this->createFolder()) {
            $this->addError('name', Yii::t('files','Unable to create folder.'));
            return false;
        }
        elseif (!$insert) {
            if (
                    (\yii\helpers\ArrayHelper::getValue($changedAttributes, 'parent_id')!=$this->parent_id)
                    ||
                    (isset($changedAttributes['pathname'])&&$changedAttributes['pathname']!=$this->pathname)
                ) {
                $model=new Folder();
                $model->load([$this->className()=>$this->attributes],$this->className());
                $model->parent_id=\yii\helpers\ArrayHelper::getValue($changedAttributes, 'parent_id');
                if (isset($changedAttributes['pathname'])) {
                    $model->pathname=$changedAttributes['pathname'];
                }
                $old_path=self::getFullPathByModel($model);
                $new_path=self::getFullPathByModel($this);
                if (!rename($old_path, $new_path)) {
                    throw new Exception('Unable to rename folder from '.$old_path.' to '.$new_path);
                }
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete() {
        foreach ($this->childs as $child) {
            $child->delete();
        }
        foreach ($this->files as $file) {
            $file->delete();
        }
        if (is_dir($this->fullPath))
            $res = \yii\helpers\FileHelper::removeDirectory($this->fullPath);
        else
            $res = true;
        $pres = parent::beforeDelete();
        return $pres && $res;
    }

    /**
     * @return string
     */
    public function getPath() {
        return implode(DIRECTORY_SEPARATOR, self::getPathnameTree($this));
    }

    /**
     * @return string
     */
    public function getUrl() {
        return DIRECTORY_SEPARATOR . Yii::getAlias('@web') .
               Yii::$app->getModule('files')->urlPath. DIRECTORY_SEPARATOR .
               implode(DIRECTORY_SEPARATOR, array_map(function($v){return rawurlencode($v);},self::getPathnameTree($this)));
    }

    /**
     * @return string
     */
    public static function getFullPathByModel($model) {
        $parent=Folder::findOne($model->parent_id);
        $prev=implode(DIRECTORY_SEPARATOR, self::getPathnameTree($parent));
        if ($prev) {$prev.=DIRECTORY_SEPARATOR;}
        return  Yii::$app->getModule('files')->path.DIRECTORY_SEPARATOR.
                $prev.
                $model->pathname;
    }

    /**
     * @return string
     */
    public function getFullPath() {
        return Yii::$app->getModule('files')->path.DIRECTORY_SEPARATOR.$this->path;
    }

    /**
     * @return Array
     */
    public static function getPathnameTree($folder) {
        if (!$folder) return [];
        $res=[$folder->pathname];
        if ($folder->parent) {
            $res=  array_merge(self::getPathnameTree($folder->parent),$res);
        }
        return $res;
    }

    public static function getTypeList() {
        return [
            self::TYPE_DEFAULT=> Yii::t('files', 'Default'),
            self::TYPE_GALLERY=> Yii::t('files', 'Photo gallery'),
            self::TYPE_DOCS=> Yii::t('files', 'List of documents'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles() {
        return $this->hasMany(File::className(), ['folder_id' => 'folder_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChilds() {
        return $this->hasMany(self::className(), ['parent_id' => 'folder_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent() {
        return $this->hasOne(self::className(), ['folder_id' => 'parent_id']);
    }

    /**
     * @return Array
     */
    public function getListArray($parent_id = null, $depth = 0) {
        $res = [];
        $models = self::find()
                ->select(['f.*', 'IF(fc.folder_id IS NULL,0,1) as has_children'])
                ->from(self::tableName() . ' f')
                ->leftJoin(self::tableName() . ' fc', 'fc.parent_id=f.folder_id')
                ->where(['f.parent_id' => $parent_id])
                ->groupBy('f.folder_id')
                ->orderBy('f.name')
                ->indexBy('folder_id')
                ->all();
        foreach ($models as $model) {
            $res[$model->folder_id] = str_pad('', $depth, '-', STR_PAD_LEFT) . $model->name;
            if ($model->has_children)
                $res+=$this->getListArray($model->folder_id, $depth + 1);
        }
        return $res;
    }

    /**
     * @return MenuItem[]
     */
    public static function getListAll($parent_id = null, $depth = null, $current_depth = 1) {
        $res = [];
        if ($depth !== null && $current_depth > $depth)
            return $res;
        $models = self::find()
                ->select(['f.*', 'IF(fc.folder_id IS NULL,0,1) as has_children'])
                ->from(self::tableName() . ' f')
                ->leftJoin(self::tableName() . ' fc', 'fc.parent_id=f.folder_id')
                ->where(['f.parent_id' => $parent_id])
                ->groupBy('f.folder_id')
                ->orderBy('f.name')
                ->indexBy('folder_id')
                ->all();
        foreach ($models as $model) {
            $res[$model->folder_id] = $model;
            if ($model->has_children)
                $res+=self::getListAll($model->folder_id, $depth, $current_depth + 1);
        }
        return $res;
    }

}
