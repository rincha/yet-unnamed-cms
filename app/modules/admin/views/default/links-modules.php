<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $modules Array */
?>
<?= $this->render('links-nav',[]) ?>
<div class="admin-default-links-modules">
    <h2>Выберите модуль</h2>
    <ul>
    <?php foreach ($modules as $id=>$module) { ?>
        <li><?= Html::a(Html::encode($module['moduleName']),['links', 'm_id'=>$id]) ?></li>
    <?php } ?>
    </ul>
</div>
