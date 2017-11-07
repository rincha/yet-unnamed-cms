<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use app\common\assets\CommonAsset;

CommonAsset::register($this);
AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="print">
<?php $this->beginBody() ?>
    <div style="max-width: 1100px; margin: 0 auto;"><?= $content; ?></div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
