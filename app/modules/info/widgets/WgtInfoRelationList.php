<?php

namespace app\modules\info\widgets;

/**
 *
 * @property app\modules\info\models\Info $model
 * @property app\modules\info\models\RelationType $type
 *
 */

class WgtInfoRelationList extends \yii\base\Widget {

    public $title;
    public $model;
    public $type;
    public $relationDirection;

    public function run() {
        if ($this->relationDirection=='masters') {
            $models=$this->model->getMastersByType($this->type->type_id);
            $attr='master';
        }
        elseif ($this->relationDirection=='slaves') {
            $models=$this->model->getSlavesByType($this->type->type_id);
            $attr='slave';
        }
        else {
            throw new \yii\base\Exception(self::className().'::relationDirection must be one of values: masters, slaves');
        }
        //die('!!');
        return $this->render(
                'relation-list',[
                    'model'=>$this->model,
                    'models' => $models,
                    'id'=>$this->getId(),
                    'attr'=>$attr,
                ]
        );
    }
}