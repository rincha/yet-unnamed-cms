<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $additional_message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>
    
    <?php if ($additional_message) { ?>
    <div>
        <?= $additional_message ?>
    </div>
    <?php } ?>

    <p>
        Произошла ошибка во время выполнения обработки вашего запроса.
    </p>
    <p>
        Пожалуйста, свяжитесь с нами, если вы думаете, что это ошибка возникла по нашей вине.
    </p>

</div>
