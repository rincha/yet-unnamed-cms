<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models app\modules\info\models\Info[] */
/* @var $type app\modules\info\models\Type */
/* @var $id string */

?>
<div class="info-wgt" id="<?= $id ?>">
    <?=
    Html::a(
            $this->context->widget->title?Html::encode($this->context->widget->title):($type?Html::encode($type->title):Yii::t('info', 'Information materials')),
            $type?['/info/default/index', 'type'=>$type->name]:['/info/default/index'],
            ['class'=>'title']
    );
    ?>
    <div class="cont">
        <?php foreach ($models as $model) { ?>
        <div class="item">
            <?=
            Html::a(
                    Html::encode($model->name),
                    $model->url,
                    ['class'=>'name']
            );
            ?>
        </div>
        <?php } ?>
    </div>
    <?=
    Html::a(
            $this->context->options['more_text']?Html::encode($this->context->options['more_text']):Yii::t('news', 'All news'),
            ['/news/default/index'],
            ['class'=>'more']
    );
    ?>
</div>