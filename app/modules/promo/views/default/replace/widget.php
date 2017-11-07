<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\forms\models\FormField;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $model app\models\Widget */

?>
<div class="widget-view">
<?= $model->wgt->run() ?>
</div>
