<?php
namespace app\modules\admin\widgets;
use yii\helpers\ArrayHelper;

/**
 * @author rincha
 *
 * @property app\models\Widget $widget
 * @property Array $options read-only
 */
class LinksSelectWidget extends \yii\base\Widget {
    //callback after select link
    public $clientAfterSelect='function(data){console.log(data);}';
    public function run() {
        return $this->render('linksSelectWidget',['id'=>$this->id, 'afterSelect'=>$this->clientAfterSelect]);
    }
}
