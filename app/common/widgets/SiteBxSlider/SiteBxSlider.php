<?php

namespace app\common\widgets\SiteBxSlider;

/**
 * @author rincha
 */
class SiteBxSlider extends \app\common\widgets\SiteWidget {

    public function getOptionsAttributes() {
        return [
            'images' => [
                'rules' => ['yii\validators\EachValidator', 'rule' => ['string']],
                'label' => 'Images',
                'hint' => null,
            ],
            'links' => [
                'rules' => ['yii\validators\EachValidator', 'rule' => ['string']],
                'label' => 'Links',
                'hint' => null,
            ],
            'titles' => [
                'rules' => ['yii\validators\EachValidator', 'rule' => ['string']],
                'label' => 'Titles',
                'hint' => null,
            ],
            'descriptions' => [
                'rules' => ['yii\validators\EachValidator', 'rule' => ['string']],
                'label' => 'Descriptions',
                'hint' => null,
            ],
            'opt_mode' => [
                'rules' => ['yii\validators\RangeValidator', 'range' => ['horizontal', 'vertical', 'fade']],
                'label' => 'Mode',
                'hint' => 'Type of transition between slides: horizontal, vertical, fade',
                'defaultValue' => 'horizontal',
            ],
            'opt_speed' => [
                'rules' => ['yii\validators\NumberValidator', 'min' => 0, 'integerOnly' => true],
                'label' => 'Speed',
                'hint' => 'Slide transition duration (in ms)',
                'defaultValue' => '500',
            ],
            'opt_randomStart' => [
                'rules' => ['yii\validators\BooleanValidator'],
                'label' => 'Random start',
                'hint' => 'Start slider on a random slide: 1 or 0',
                'defaultValue' => '0',
            ],
            'opt_infiniteLoop' => [
                'rules' => ['yii\validators\BooleanValidator'],
                'label' => 'Infinite Loop',
                'hint' => 'If 1, clicking "Next" while on the last slide will transition to the first slide and vice-versa',
                'defaultValue' => '1',
            ],
            'opt_pager' => [
                'rules' => ['yii\validators\BooleanValidator'],
                'label' => 'Pager',
                'hint' => 'If 1, a pager will be added',
                'defaultValue' => '1',
            ],
            'opt_controls' => [
                'rules' => ['yii\validators\BooleanValidator'],
                'label' => 'Controls',
                'hint' => 'If 1, "Next" / "Prev" controls will be added',
                'defaultValue' => '1',
            ],
            'opt_auto' => [
                'rules' => ['yii\validators\BooleanValidator'],
                'label' => 'Auto',
                'hint' => 'Slides will automatically transition',
                'defaultValue' => '1',
            ],
            'opt_pause' => [
                'rules' => ['yii\validators\NumberValidator', 'min' => 0, 'integerOnly' => true],
                'label' => 'Auto',
                'hint' => 'The amount of time (in ms) between each auto transition',
                'defaultValue' => '4000',
            ],
            'opt_slideWidth' => [
                'rules' => ['yii\validators\NumberValidator', 'min' => 0, 'integerOnly' => true],
                'label' => 'Slide width',
                'hint' => 'The width of each slide. This setting is required for all horizontal carousels!',
                'defaultValue' => '0',
            ],
        ];
    }

    public function renderAdminView(\app\models\Widget $model, \yii\widgets\ActiveForm $form) {
        return $this->render('admin', ['model' => $model, 'form' => $form]);
    }
    
    public function getBxOptions() {
        $res=[
            'nextText'=>'<i class="fa fa-chevron-right"></i>',
            'prevText'=>'<i class="fa fa-chevron-left"></i>',
        ];
        foreach ($this->getOptionsAttributes() as $attr=>$val) {
            if (strpos($attr, 'opt_')===0) {
                $opt=str_replace('opt_', '', $attr);
                $res[$opt]=$this->getOption($attr);
            }
        }
        return $res;
    }
    
    public function run() {
        assets\SiteBxSliderAsset::register($this->view);
        $this->view->registerJs('$("#'.$this->id.'").bxSlider('.\yii\helpers\Json::encode($this->getBxOptions()).');');
        return $this->render('view',['model'=>$this->widget]);
    }

}
