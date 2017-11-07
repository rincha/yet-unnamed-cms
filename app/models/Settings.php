<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%settings}}".
 *
 * @property integer $setting_id
 * @property string $key
 * @property string $value
 * @property integer $serialized
 */
class Settings extends \yii\db\ActiveRecord {

    public static $_settings = [];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['key', 'value'], 'required'],
            [['value'], 'string'],
            [['serialized'], 'boolean'],
            [['key'], 'string', 'max' => 64],
            [['key'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'setting_id' => 'ID',
            'key' => 'Ключ',
            'value' => 'Значение',
            'serialized' => 'Сериализация',
        ];
    }

    /*public static function getSettingValueGroup($group) {
        $models=models\Settings::find()->where(['like','key',$group.'.%',false])->indexBy('key')->all();
        $result=[];
        foreach ($models as $model) {
            self::$_settings[$key] = $model;
            $result[str_replace($group.'.', '', $model->key)]=$model->serialized?unserialize($model->value):$model->value;
        }
        return $result;
    }*/

    public function getValueRes() {
        if ($this->serialized) {
            return unserialize($this->value);
        }
        else {
            return $this->value;
        }
    }

    public static function getSettingValue($key, $default = null) {
        if (isset(self::$_settings[$key]) && is_object(self::$_settings[$key])) {
            if (self::$_settings[$key]->serialized) {
                return unserialize(self::$_settings[$key]->value);
            }
            else {
                return self::$_settings[$key]->value;
            }
        }
        else {
            $model = self::findOne(['key' => $key]);
            if (!$model) {
                $model = new Settings();
                $model->value = $default;
                if (is_object($model->value)) {
                    $model->serialized = 1;
                }
            }
            self::$_settings[$key] = $model;
            if (self::$_settings[$key]->serialized)
                return unserialize(self::$_settings[$key]->value);
            else
                return self::$_settings[$key]->value;
        }
    }

    /**
     * @param string $group
     * @return yii\db\ActiveQuery[]
     */
    public static function findGroup($group) {
        return self::find()->where(['like','key',$group.'.%',false])->indexBy('key');
    }

}
