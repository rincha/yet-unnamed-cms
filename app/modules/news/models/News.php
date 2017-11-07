<?php

namespace app\modules\news\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\common\behaviors\DateConvertBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property integer $news_id
 * @property integer $type_id
 * @property string $uid
 * @property string $name
 * @property string $h1
 * @property string $meta_title
 * @property string $meta_description
 * @property string $keywords
 * @property string $content
 * @property string $images
 * @property string $date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property NewsType $type
 * @property string $typeName
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id'], 'integer'],
            [['name'], 'required'],                        
            [['images'],'_vImages'],
            [['content', 'images'], 'string', 'max' => 1024*64],
            [['content'], 'filter', 'filter' => 'app\common\helpers\AppHelper::htmlPurifyFull'],
            [['date'], 'date','format'=>'php:Y-m-d'],
            [['uid'], 'string', 'max' => 64],
            [['uid'], 'match', 'pattern' => '/^[a-zA-Z0-9а-яА-Я-]+$/ui'],
            [['name', 'h1', 'meta_title'], 'string', 'max' => 255],
            [['meta_description', 'keywords'], 'string', 'max' => 1024],
            [['uid', 'h1', 'meta_title', 'meta_description', 'keywords'], 'default','value'=>null],
            [['uid'], 'unique'],
            [['type_id'], 'exist', 'skipOnError' => false, 'targetClass' => NewsType::className(), 'targetAttribute' => ['type_id' => 'type_id']],
        ];
    }
    
    public function _vImages() {
        if (is_array($this->images)) {
            $this->images=implode("\n", $this->images);
        }
    }
    
    public function behaviors() {
        return [
            TimestampBehavior::className()=>[
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
            DateConvertBehavior::className()=>[
                'class' => DateConvertBehavior::className(),
                'attributes' => ['date'],                
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'news_id' => Yii::t('news', 'News ID'),
            'type_id' => Yii::t('news', 'Type'),
            'type.name' => Yii::t('news', 'Type'),
            'typeName' => Yii::t('news', 'Type'),
            'type.title' => Yii::t('news', 'Type'),
            'uid' => Yii::t('news', 'Uid'),
            'name' => Yii::t('news', 'Name'),
            'h1' => Yii::t('news', 'H1'),
            'meta_title' => Yii::t('news', 'Meta Title'),
            'meta_description' => Yii::t('news', 'Meta Description'),
            'keywords' => Yii::t('news', 'Keywords'),
            'content' => Yii::t('news', 'Content'),
            'images' => Yii::t('news', 'Images'),
            'date' => Yii::t('news', 'Date'),
            'created_at' => Yii::t('news', 'Created At'),
            'updated_at' => Yii::t('news', 'Updated At'),
        ];
    }
    
    public function afterFind() {
        $images=explode("\n", $this->images);
        $res=[];
        foreach ($images as $image) {
            if (trim($image)) {$res[]=$image;}
        }
        $this->images=$res;
        return parent::afterFind();
    }

    public function getImage($index=0) {
        return ArrayHelper::getValue($this->images, $index);
    }
    
    public function getTypeName() {
        return $this->type?$this->type->name:null;
    }
    
    public function getUrlArr() {
        return ['/news/default/view','id'=>$this->uid?$this->uid:$this->news_id, 'type'=>$this->type&&$this->type->name?$this->type->name:null];
    }
    
    public function getUrl() {
        return \yii\helpers\Url::to($this->getUrlArr(),true);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(NewsType::className(), ['type_id' => 'type_id']);
    }
}
