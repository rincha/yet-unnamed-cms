<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $models app\modules\news\models\News[] */
/* @var $type app\modules\news\models\NewsType */
/* @var $id string */

?>
<div class="news-wgt" id="<?= $id ?>">
    <?=
    Html::a(
            $type?Html::encode($type->title):Yii::t('news', 'News'),
            $type?['/news/default/index', 'type'=>$type->name]:['/news/default/index'],
            ['class'=>'title']
    );
    ?>
    <div class="cont">
        <?php foreach ($models as $model) { ?>
        <div class="item">
            <span class="date"><?= Yii::$app->formatter->asDate($model->date) ?></span>
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
            Yii::t('news', 'All news'),
            ['/news/default/index'],
            ['class'=>'more']
    );
    ?>
</div>