<?php

namespace app\modules\post\models;

use Yii;
use app\models\Settings;

/**
 *
 * @author rincha
 *
 */
class SettingsForm extends \yii\base\Model {

    public $displayStatus;
    public $commentGuestStatus;
    public $commentUserStatus;
    public $commentDisplayStatus;
    public $commentAnswerPermit;

    public function rules() {
        return [
            [['displayStatus','commentDisplayStatus'], 'each', 'rule'=>['integer']],
            [['displayStatus'], 'each', 'rule'=>['in','range'=> array_keys(Post::getStatusList())]],
            [['commentDisplayStatus'], 'each', 'rule'=>['in','range'=> array_keys(Comment::getStatusList())]],

            [['commentGuestStatus', 'commentUserStatus', 'commentAnswerPermit'], 'integer'],
            [['commentGuestStatus', 'commentUserStatus'], 'in', 'range'=> array_keys(Comment::getStatusList())],
        ];
    }

    public function attributeLabels() {
        return [
            'displayStatus' => Yii::t('post', 'Display articles with statuses'),
            'commentDisplayStatus' => Yii::t('post', 'Display comments with statuses'),
            'commentGuestStatus' => Yii::t('post', 'Guest comment default status'),
            'commentUserStatus' => Yii::t('post', 'Authenicated comment default status'),
            'commentAnswerPermit' => Yii::t('post', 'Allow reply to comments'),
        ];
    }

    public function save() {
        if ($this->validate()) {
            foreach ($this->attributes as $attr=>$value) {
                $key=strtolower('module.post.'.preg_replace('/([A-Z]{1})/u', '.$1', $attr));
                $setting=Settings::findOne(['key'=>$key]);
                if (!$setting) {
                    $setting=new Settings();
                    $setting->key=$key;
                }
                $setting->value= is_array($value)?implode(',', $value):$value;
                if (!$setting->save()) {
                    var_dump($setting->errors); die();
                    throw new \yii\base\Exception('Can`t save settings with key {'.$key.'}');
                }
            }
            return true;
        }
        else {
            return false;
        }
    }

    public function init($withModule=false) {
        parent::init();
        if ($withModule) {
            foreach ($this->attributes() as $attr) {
                $this->{$attr}=Yii::$app->getModule('post')->{$attr};
            }
        }
        $settings=Settings::findGroup('module.post')->all();
        if (isset($settings['module.post.display.status'])) {
            $value=explode(',', $settings['module.post.display.status']->value);
            $value= array_map(function($v){
                return in_array(trim($v), array_keys(Post::getStatusList()))?(int)trim($v):null;
            }, $value);
            $this->displayStatus=array_filter($value);
        }
        if (isset($settings['module.post.comment.guest.status'])) {
            $value=trim($settings['module.post.comment.guest.status']->value);
            if (in_array($value, array_keys(Post::getStatusList()))) {
                $this->commentGuestStatus=(int)$value;
            }
        }
        if (isset($settings['module.post.comment.user.status'])) {
            $value=trim($settings['module.post.comment.user.status']->value);
            if (in_array($value, array_keys(Post::getStatusList()))) {
                $this->commentUserStatus=(int)$value;
            }
        }
        if (isset($settings['module.post.comment.display.status'])) {
            $value=explode(',', $settings['module.post.comment.display.status']->value);
            $value= array_map(function($v){
                return in_array(trim($v), array_keys(Comment::getStatusList()))?(int)trim($v):null;
            }, $value);
            $this->commentDisplayStatus=array_filter($value);
        }
        if (isset($settings['module.post.comment.answer.permit'])) {
            $value=trim($settings['module.post.comment.answer.permit']->value);
            $this->commentAnswerPermit=(int)$value;
        }
    }

}
