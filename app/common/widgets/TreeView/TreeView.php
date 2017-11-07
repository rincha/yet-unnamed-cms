<?php

namespace app\common\widgets\TreeView;

/**
 * @author rincha
 */
class TreeView extends \yii\base\Widget {

    public $items;

    public $nameAttribute='name';
    public $sortAttribute='sort_order';

    /**
     * @var $rowOptions Array|callable|null
     * callable function(ActiveRecord $item)
     */
    public $rowOptions=null;

    /**
     * @var $sortButtons Array
     * up
     * down
     * params
     */
    public $sortButtons=[];

    /**
     * @var $actionButtons Array
     * view
     * update
     * delete
     * params
     */
    public $actionButtons=[];

    public function run() {
        if ($this->items) {
            assets\TreeViewAsset::register($this->view);
            //$this->view->registerJs('$("#'.$this->id.'").bxSlider('.\yii\helpers\Json::encode($this->getBxOptions()).');');
            return $this->render('view',[
                'items'=>$this->items,
                'nameAttribute'=>$this->nameAttribute,
                'sortAttribute'=>$this->sortAttribute,
                'sortButtons'=>$this->sortButtons,
                'actionButtons'=>$this->actionButtons,
                'rowOptions'=>$this->rowOptions,
            ]);
        }
    }

}
