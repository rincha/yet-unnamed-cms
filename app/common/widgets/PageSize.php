<?php

namespace app\common\widgets;

use yii\helpers\Url;
use yii\bootstrap\ButtonDropdown;

/**
 *
 * @author rincha
 */
class PageSize extends \yii\base\Widget {

    /** @var \yii\data\ActiveDataProvider */
    public $dataProvider;

    /** @var string */
    public $label;

    /** @var Array if not set - auto generate by step or stepMultiplier */
    public $sizes;

    /** @var integer */
    public $step;

    /** @var integer */
    public $stepMultiplier=2;

    public $options=['class' => 'btn btn-default'];

    public function run() {
        $items=[];
        $size=$this->dataProvider->pagination->pageSizeLimit[0];
        $label=$this->label.$this->dataProvider->pagination->pageSize;
        if (!$this->sizes) {$this->generateSizes();}
        foreach ($this->sizes as $size) {
            $items[]=[
                'label' => $size,
                'url' => Url::current([$this->dataProvider->pagination->pageSizeParam=>$size]),
                'options' => [
                    'class'=>$this->dataProvider->pagination->pageSize==$size?'active':null,
                ],
            ];
        }
        return ButtonDropdown::widget([
            'label' => $label,
            'dropdown' => [
                'items' => $items,
                'options' => ['class' => 'dropdown-menu-right']
            ],
            'options' => $this->options,
        ]);
    }

    private function generateSizes() {
        $this->sizes=[];
        $size=$this->dataProvider->pagination->pageSizeLimit[0];
        $n=0;
        $max=$this->dataProvider->pagination->pageSizeLimit[1];
        while ($size<=$max && $n<100) {
            $this->sizes[]=$size;
            if ($this->step) {
                $temp=$size+$this->step;
                if ($temp>$max && $size<$max) {
                    $size=$max;
                }
                else {
                    $size+=$this->step;
                }
            }
            elseif ($this->stepMultiplier) {
                $temp=$size*$this->stepMultiplier;
                if ($temp>$max && $size<$max) {
                    $size=$max;
                }
                else {
                    $size*=$this->stepMultiplier;
                }
            }
            else {
                throw new \yii\base\Exception('property step or stepMultiplier must be set!');
            }
            $n++;
        }
    }

}
