<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models app\modules\info\models\Relation[] */
/* @var $id string */
/* @var $attr string */

?>
<div class="info-relation-list-wgt" id="<?= $id ?>">
    <div class="cont">
        <?php
        foreach ($models as $model) { ?>
        <div class="item">
            <?=
            Html::a(
                    Html::encode($model->{$attr}->name),
                    $model->{$attr}->url,
                    ['class'=>'name']
            );
            ?>
        </div>
        <?php } ?>
    </div>
</div>