<?php

namespace app\modules\forms\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%form_field}}".
 *
 * @property integer $field_id
 * @property integer $form_id
 * @property integer $type_id
 * @property string $name
 * @property string $title
 * @property integer $required
 * @property string $options
 * @property string $tip
 * @property integer $sort_order
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Form $form
 */
class FormField extends \yii\db\ActiveRecord {

    const TYPE_STRING = 10;
    const TYPE_TEXT = 20;
    const TYPE_HTML = 30;
    const TYPE_EMAIL = 40;
    const TYPE_INTEGER = 50;
    const TYPE_NUMERIC = 60;
    const TYPE_CHECKBOX = 70;
    const TYPE_SELECT = 80;
    const TYPE_CAPTCHA = 90;
    const TYPE_MASK = 100;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%form_field}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['form_id', 'type_id', 'name', 'title'], 'required'],
            [['form_id', 'type_id', 'required', 'sort_order'], 'integer'],
            [['options', 'tip'], 'string', 'max' => 64 * 1024],
            [['name'], 'string', 'max' => 128],
            [['name'], 'match', 'pattern' => '/^[a-z]+[a-z0-9_]*$/ui'],
            [['title'], 'string', 'max' => 255],
            [['params'], 'string', 'max' => 512],
            [['form_id'], 'exist', 'targetClass' => Form::className(), 'targetAttribute' => Form::primaryKey()],
            [['type_id'], 'in', 'range' => array_keys(self::getTypes())],
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

    public static function getTypes() {
        return [
            self::TYPE_STRING => [
                'name' => Yii::t('forms', 'String'),
                'params' => [
                    Yii::t('forms', 'max - the maximum number of characters ({max}), min - minimum number of characters ({min})', ['max' => 128, 'min' => 0]),
                ],
                'rules' => [['string', 'max' => 128, 'min' => 0]],
            ],
            self::TYPE_TEXT => [
                'name' => Yii::t('forms', 'Text'),
                'params' => [
                    Yii::t('forms', 'max - the maximum number of characters ({max}), min - minimum number of characters ({min})', ['max' => 65536, 'min' => 0]),
                ],
                'rules' => [['string', 'max' => 65536, 'min' => 0]],
            ],
            /* self::TYPE_HTML=>[
              'name'=>Yii::t('forms','Text HTML'),
              'params'=>[
              Yii::t('forms','max - the maximum number of characters ({max}), min - minimum number of characters ({min})',['max'=>65536,'min'=>0]),
              ],
              'rules'=>[['string','max'=>65536,'min'=>0]],
              ], */
            self::TYPE_EMAIL => [
                'name' => Yii::t('forms', 'Email'),
                'params' => [''],
                'rules' => [['string', 'max' => 128, 'min' => 0], ['email']],
            ],
            self::TYPE_INTEGER => [
                'name' => Yii::t('forms', 'Integer'),
                'params' => [
                    Yii::t('forms', 'max - the maximum number ({max}), min - minimum number ({min})', ['max' => 'NULL', 'min' => 'NULL']),
                ],
                'rules' => [['integer', 'max' => null, 'min' => null]],
            ],
            self::TYPE_NUMERIC => [
                'name' => Yii::t('forms', 'Float'),
                'params' => [
                    Yii::t('forms', 'max - the maximum number ({max}), min - minimum number ({min})', ['max' => 'NULL', 'min' => 'NULL']),
                ],
                'rules' => [['number', 'max' => null, 'min' => null]],
            ],
            self::TYPE_CHECKBOX => [
                'name' => Yii::t('forms', 'Checkbox'),
                'params' => [],
                'rules' => [['boolean']],
            ],
            self::TYPE_MASK => [
                'name' => Yii::t('forms', 'Mask'),
                'params' => [
                    Yii::t('forms', 'mask=99.99.9999 - input mask'),
                    Yii::t('forms', 'pattern=\d{2}\.\d{2}\.\d{4} - match pattern'),
                ],
                'rules' => [['match', 'pattern' => function(FormField $field){ return '/^' . $field->getParam('pattern','.*') . '$/ui';}]],
            ],
            self::TYPE_SELECT => [
                'name' => Yii::t('forms', 'Selection'),
                'params' => [
                    Yii::t('forms', 'allowArray=1 - разрешить множественный выбор (0)'),
                ],
                'rules' => [['in', 'range' => function(FormField $field){ return array_keys($field->getOptionsArr());}, 'allowArray' => 0]],
            ],
            self::TYPE_CAPTCHA => [
                'name' => Yii::t('forms', 'Captcha'),
                'params' => [''],
                'rules' => [['captcha']],
            ],
        ];
    }

    public function getType() {
        $l = self::getTypes();
        return $l[$this->type_id];
    }

    public function getTypeList() {
        $l = self::getTypes();
        $r = [];
        foreach ($l as $k => $arr)
            $r[$k] = $arr['name'];
        return $r;
    }

    public function getTypeName() {
        $l = self::getTypes();
        return $l[$this->type_id]['name'];
    }

    public function getParamsArr() {
        parse_str($this->params,$res);
        return $res;
    }

    public function getParam($param,$default=null) {
        return ArrayHelper::getValue($this->getParamsArr(),$param,$default);
    }


    public function getOptionsArr() {
        $rows=explode("\n", $this->options);
        $res=[];
        foreach ($rows as $row) {
            $vals= explode(':', $row);
            if (count($vals)==2) {
                $res[trim($vals[0])]=trim($vals[1]);
            }
        }
        return $res;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'field_id' => Yii::t('forms', 'ID'),
            'form_id' => Yii::t('forms', 'Form'),
            'type_id' => Yii::t('forms', 'Type'),
            'name' => Yii::t('forms', 'Unique name'),
            'title' => Yii::t('forms', 'Name'),
            'required' => Yii::t('forms', 'Required'),
            'options' => Yii::t('forms', 'Options'),
            'params' => Yii::t('forms', 'Params'),
            'tip' => Yii::t('forms', 'Tip'),
            'sort_order' => Yii::t('forms', 'Sort order'),
            'created_at' => Yii::t('forms', 'Created at'),
            'updated_at' => Yii::t('forms', 'Updated at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm() {
        return $this->hasOne(Form::className(), ['form_id' => 'form_id']);
    }

    public static function find() {
        return parent::find()->orderBy(['sort_order' => 'DESC', 'title' => 'ASC']);
    }

}
