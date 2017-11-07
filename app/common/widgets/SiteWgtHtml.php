<?php
namespace app\common\widgets;
use yii\helpers\Html;
/**
 * @author rincha
 *
 * @property app\models\Widget $widget
 * @property Array $options read-only
 */
class SiteWgtHtml extends SiteWidget {

   public function getOptionsAttributes() {return [];}

   public function run() {
       return Html::tag('div',$this->widget->content,['class'=>'wgt-html','id'=>$this->getId()]);
   }
}
