<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Comment */

?>
<?php if ($model->author) { ?>
<strong><?= Html::encode($model->author->username) ?></strong>,
<?php } else { ?>
<strong><?= Html::encode($model->author_nickname) ?></strong>,
<?php } ?>
<span class="text-muted">
<?= Yii::$app->formatter->asDate($model->created_date) ?> <?= Yii::$app->formatter->asTime($model->created_time) ?>
</span>
