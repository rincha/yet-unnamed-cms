<?php

namespace app\modules\post\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use app\common\helpers\ImageHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\common\helpers\AppHelper;
use app\models\User;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%post}}".
 *
 * @property integer $post_id
 * @property string $uid
 * @property integer $author_id
 * @property string $title
 * @property string $h1
 * @property string $description
 * @property string $content
 * @property string $keywords
 * @property integer $status
 * @property string $created_date
 * @property string $created_time
 * @property string $updated_at
 *
 * @property string $statusText
 * @property string $url
 *
 * @property User $author
 */
class Post extends \yii\db\ActiveRecord {

    const STATUS_DRAFT=1;
    const STATUS_NEW=2;
    const STATUS_APPROVED=3;
    const STATUS_CLOSED=4;
    const STATUS_ARCHIVED=5;

    const MAX_IMAGE_FILES = 5;
    const MAX_IMAGE_SIZE = 8388608;//8Mb

    const FILES_BASE_PATH='@webroot/uploads/post';
    const FILES_BASE_URL='@web/uploads/post';

    const SCENARIO_OWNER='owner';
    const SCENARIO_ADMIN='admin';
    const SCENARIO_SYSTEM='system';

    public $images_add;
    public $images_delete;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%post}}';
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        $res=[
            self::SCENARIO_SYSTEM=>[],
            self::SCENARIO_OWNER=>[
                'title','uid','content','description','h1','keywords','images_add','images_delete'
            ],
            self::SCENARIO_ADMIN=>[
                'title','uid','content','description','h1','keywords','images_add','images_delete',
                'status','author_id',
            ],
        ];
        return $res;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['title', 'content', 'status'], 'required'],
            [['author_id', 'status'], 'integer'],
            [['status'], 'in', 'range'=> array_keys(self::getStatusList())],
            [['description', 'content'], 'string', 'max'=>32*1024],
            [['content'], '_vHtml'],
            [['title', 'h1'], 'string', 'max' => 255],
            [['uid'], 'default', 'value'=>null],
            [['uid'], 'string', 'max' => 64],
            [['uid'], 'match', 'pattern' => '/^\w+[\w\d-]+$/ui'],
            [['uid'], 'unique'],
            [['keywords'], 'string', 'max' => 2048],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['images_add'], 'file',
                'skipOnEmpty' => true,
                'extensions' => ['jpg', 'gif', 'png', 'jpeg'],
                'maxSize' => self::MAX_IMAGE_SIZE,
                'checkExtensionByMimeType' => true,
                'maxFiles' => self::MAX_IMAGE_FILES,
            ],
            [['images_add'], '_vImageCount'],
            [['images_add'], 'each', 'rule' => ['_vImage']],
            [['images_delete'], 'each', 'rule' => ['integer', 'min' => 1, 'max' => self::MAX_IMAGE_FILES]],
        ];
    }

    public function _vHtml($attribute) {
        $this->{$attribute}= AppHelper::htmlPurify($this->{$attribute}, 'light_links', [
            'HTML.AllowedAttributes'=>'*.style,*.class,a.href,a.name,a.target',
        ]);
    }

    public function _vImageCount($attribute) {
        if (
                is_array($this->{$attribute}) &&
                !$this->isNewRecord &&
                (count($this->{$attribute}) + count($this->getImagePathList())) > self::MAX_IMAGE_FILES
        ) {
            $this->addError($attribute, Yii::t('post', 'You can download no more than {n} {attribute} files.',['attribute'=>$this->getAttributeLabel($attribute),'n'=>self::MAX_IMAGE_FILES]));
        }
    }

    public function _vImage($attribute, $params) {
        if (!$this->hasErrors($attribute)) {
            if (ImageHelper::canChangeImage($this->{$attribute}) !== true) {
                $this->addError($attribute, Yii::t('post', '{attribute} file is too large.',['attribute'=>$this->getAttributeLabel($attribute)]));
            }
        }
    }

    public function behaviors() {
        return [
            TimestampBehavior::className() => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_time', 'updated_at', 'created_date'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'post_id' => Yii::t('post', 'ID'),
            'uid' => Yii::t('post', 'Unique name'),
            'author_id' => Yii::t('post', 'Author'),
            'title' => Yii::t('post', 'Title'),
            'h1' => Yii::t('post', 'Header H1'),
            'description' => Yii::t('post', 'Description'),
            'content' => Yii::t('post', 'Content'),
            'keywords' => Yii::t('post', 'Keywords'),
            'status' => Yii::t('post', 'Status'),
            'statusText' => Yii::t('post', 'Status'),
            'created_date' => Yii::t('post', 'Created date'),
            'created_time' => Yii::t('post', 'Created time'),
            'updated_at' => Yii::t('post', 'Updated time'),
            'images_add' => Yii::t('post', 'Add images'),
            'images_delete' => Yii::t('post', 'Delete images'),
            'author.username' => Yii::t('post', 'Author'),
        ];
    }

    public function beforeDelete() {
        FileHelper::removeDirectory($this->getBasePath());
        return parent::beforeDelete();
    }

    /**
     * @return array
     */
    public static function getStatusList() {
        return [
            self::STATUS_DRAFT => Yii::t('post', 'draft'),
            self::STATUS_NEW => Yii::t('post', 'new'),
            self::STATUS_APPROVED => Yii::t('post', 'approved'),
            self::STATUS_CLOSED => Yii::t('post', 'closed'),
            self::STATUS_ARCHIVED => Yii::t('post', 'archived'),
        ];
    }

    /**
     * @return string
     */
    public function getStatusText() {
        return ArrayHelper::getValue(self::getStatusList(), $this->status);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor() {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @return mixed null if no images uploaded, true or false if images stored/not stored
     */
    public function saveImages() {
        if (!$this->isNewRecord && $this->images_delete) {
            $this->deleteImages();
        }
        $res = null;
        if (is_array($this->images_add)) {
            $res=$this->addImages();
        }
        return $res;
    }

    /**
     * delete images by images_delete attribute
     * @return null
     */
    public function deleteImages() {
        $exist = $this->getImagePathList(false);
        foreach ($this->images_delete as $index) {
            if (isset($exist[$index])) {
                ImageHelper::deleteThumbnails($exist[$index]);
                unlink($exist[$index]);
            }
        }
    }

    /**
     * add images by images_add attribute
     * @return null
     */
    public function addImages() {
        $res = null;
        $empty=$this->getImagePathList(true);
        $path = current($empty);

        foreach ($this->images_add as $image) {
            if (($image instanceof UploadedFile) && $path) {
                ImageHelper::deleteThumbnails($path);
                $res = ImageHelper::resize($path, $image->tempName, 1600, 1600, ['type' => 'r', 'save_type' => false]);
                if (!$res) {
                    $this->addError('images_add',  Yii::t('post', 'File {filename} could not be processed.', ['filename'=>$image->name]));
                    return false;
                }
                $path = next($empty);
            }
        }
        return $res;
    }

    /**
     * @param boolean $empty - only not exist if true
     * @return Array of files path
     */
    public function getImagePathList($empty = false) {
        $res = [];
        for ($i = 1; $i <= self::MAX_IMAGE_FILES; $i++) {
            $exist = file_exists($this->getImagePath($i));
            if ($exist && !$empty) {
                $res[$i] = $this->getImagePath($i);
            } elseif (!$exist && $empty) {
                $res[$i] = $this->getImagePath($i);
            }
        }
        return $res;
    }

    /**
     * @return string file path
     */
    private function getImagePath($index = 0) {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . ((int) $index) . '.jpg';
    }

    /**
     * @return string files path
     */
    private function getBasePath() {
        $path = Yii::getAlias(self::FILES_BASE_PATH) . DIRECTORY_SEPARATOR . $this->post_id;
        if (!file_exists($path) || !is_dir($path)) {
            FileHelper::createDirectory($path);
        }
        return $path;
    }


    /**
     * @return Array of url
     */
    public function getImageUrlList() {
        $res = [];
        for ($i = 1; $i <= self::MAX_IMAGE_FILES; $i++) {
            $exist = file_exists($this->getImagePath($i));
            if ($exist) {
                $res[$i] = $this->getImageUrl($i);
            }
        }
        return $res;
    }

    /**
     * @return string file url
     */
    public function getImageUrl($index = 1) {
        return $this->getImagesBaseUrl() . DIRECTORY_SEPARATOR . ((int) $index) . '.jpg';
    }

    /**
     * @return string URL
     */
    public function getImagesBaseUrl() {
        return Yii::getAlias(self::FILES_BASE_URL) . DIRECTORY_SEPARATOR . $this->post_id;
    }

    /**
     * @return string URL
     */
    public function getUrl() {
        return \yii\helpers\Url::to(['/post/default/view','id'=>$this->uid?$this->uid:$this->post_id]);
    }

    /**
     * @return boolean
     */
    public function isCanUpdate(){
        return
        !Yii::$app->user->isGuest &&
        (
            (Yii::$app->user->can('PostAuthor') && $this->author_id==Yii::$app->user->id && in_array($this->status, [self::STATUS_DRAFT,self::STATUS_NEW])) ||
            Yii::$app->user->can('PostAdmin')
        );
    }

}
