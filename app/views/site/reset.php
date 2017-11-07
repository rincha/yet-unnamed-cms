<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $res string */

$this->title = Yii::$app->params['siteName'];

if (!$res) {
?>
<div class="site-reset text-center">
    <h1>Clear cookies</h1>
    <?=
    Html::a('Clear',['reset'],['class'=>'btn btn-danger','data-method'=>'post','data-confirm'=>'Are you sure to delete all identity cookies?'])
    ?>
</div>
<?php } else { ?>
<div class="site-reset text-center">
    <h1>Clear cookies</h1>
    <div class="alert alert-success">Operation successfully completed!</div>
    <?=
    Html::a('Return to homepage', \yii\helpers\Url::home(),['class'=>'btn btn-success'])
    ?>
</div>
<?php } ?>
