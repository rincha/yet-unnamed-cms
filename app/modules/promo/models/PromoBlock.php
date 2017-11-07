<?php

namespace app\modules\promo\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%promo_block}}".
 *
 * @property integer $block_id
 * @property integer $promo_id
 * @property string $name
 * @property string $background_color
 * @property string $background_image
 * @property string $content
 * @property string $script
 * @property string $style
 * @property string $params
 * @property integer $status
 * @property integer $sort_order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Promo $promo
 *
 * @property string $statusName
 */
class PromoBlock extends \yii\db\ActiveRecord
{

    const STATUS_DISABLED=0;
    const STATUS_ENABLED=1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%promo_block}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['promo_id', 'name', 'sort_order'], 'required'],
            [['promo_id', 'status','sort_order'], 'integer'],
            [['status'], 'in', 'range'=>  array_keys($this->statusList())],
            [['content', 'script', 'style'], 'string', 'max'=>64*1024],
            [['name'], 'string', 'max' => 64],
            [['background_color'], 'string', 'max' => 16],
            [['background_color'], 'match', 'pattern' => '/^#[0-0a-f]{6}$/ui'],
            [['background_image'], 'string', 'max' => 255],
            [['params'], 'string', 'max' => 512],
            [['name'], 'unique'],
            [['promo_id'], 'exist', 'skipOnError' => false, 'targetClass' => Promo::className(), 'targetAttribute' => ['promo_id' => 'promo_id']],
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::className()=>[
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'block_id' => Yii::t('promo', 'Block ID'),
            'promo_id' => Yii::t('promo', 'Promo ID'),
            'promo.name' => Yii::t('promo', 'Promo page'),
            'name' => Yii::t('promo', 'Name'),
            'background_color' => Yii::t('promo', 'Background color'),
            'background_image' => Yii::t('promo', 'Background image'),
            'content' => Yii::t('promo', 'Content'),
            'script' => Yii::t('promo', 'Script'),
            'style' => Yii::t('promo', 'Style'),
            'params' => Yii::t('promo', 'Params'),
            'status' => Yii::t('promo', 'Status'),
            'statusName' => Yii::t('promo', 'Status'),
            'sort_order' => Yii::t('promo', 'Sort order'),
            'created_at' => Yii::t('promo', 'Created at'),
            'updated_at' => Yii::t('promo', 'Updated at'),
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeHints() {
        return [
            'style'=>$this->isNewRecord?'':
                Yii::t('promo', 'You can use the block identifier: #promo-block-{id}',['id'=>$this->block_id]),
            'params'=>Yii::t('promo', 'Format replace=replace_type@param1@...paramN. Use {replace} in content for replace.'),
        ];
    }

    /**
     * @return Array
     */
    public function statusList() {
        return [
            self::STATUS_DISABLED=>  Yii::t('promo', 'disabled'),
            self::STATUS_ENABLED=>  Yii::t('promo', 'enabled'),
        ];
    }
    /**
     * @return string
     */
    public function getStatusName() {
        return ArrayHelper::getValue($this->statusList(), $this->status);
    }

    /**
     * @return string
     */
    public function getResultContent(\yii\web\View $view) {
        $res=$this->content;
        if ($this->params) {
            parse_str($this->params,$params);
            foreach ($params as $search=>$value) {
                $replaceParams=$this->getReplaceParams($value);
                if ($replaceParams) {
                    $replace=$view->render($replaceParams[0],$replaceParams[1]);
                }
                else {
                    $replace='';
                }
                $res=str_replace('{'.$search.'}', $replace, $res);
            }
        }
        return $res;
    }

    /**
     * @param string $value
     * @return string
     */
    private function getReplaceParams($value) {
        $value=explode('@', $value);
        if (count($value)>=2) {
            switch ($value[0]) {
                case 'form':
                    $form=\app\modules\forms\models\Form::findOne(['name'=>$value[1]]);
                    $model=new \app\modules\forms\models\FormSend();
                    $model->form=$form;

                    return ['replace/form', ['model'=>$model, 'form'=>$form]];
                    break;
                case 'widget':
                    $model= \app\models\Widget::findOne($value[1]);
                    return ['replace/widget', ['model'=>$model]];
                    break;

            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromo()
    {
        return $this->hasOne(Promo::className(), ['promo_id' => 'promo_id']);
    }
}
