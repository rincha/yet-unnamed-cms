<?php
namespace app\common\widgets\FlashMessages;

use Yii;
use yii\helpers\Html;

class FlashMessages extends \yii\base\Widget {

    public $prefix='flash.';
    public $keys=['error','success','warning','info'];
    public $key_type=['error'=>'danger','success'=>'success','warning'=>'warning','info'=>'info'];

    public function run() {
        $res=[];

        foreach ($this->keys as $key) {
            if(Yii::$app->session->hasFlash($this->prefix.$key)) {
                $flashes=Yii::$app->session->getFlash($this->prefix.$key,null,true);
                if (!is_array($flashes)) {$flashes=[$flashes];}
                foreach ($flashes as $flash) {
                    $res[]=Html::tag(
                        'div',
                        $flash,
                        ['class'=>'alert alert-'.$this->key_type[$key],]
                    );
                }
            }
        }
        return implode("\n", $res);
    }
}
