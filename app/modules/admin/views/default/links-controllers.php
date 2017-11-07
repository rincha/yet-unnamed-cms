<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $modules Array */
/* @var $m_id string */
?>
<?= $this->render('links-nav',['modules'=>$modules, 'm_id'=>$m_id]) ?>
<div class="admin-default-links-controllers">
    <h2>Выберите контроллер</h2>
    <ul>
    <?php foreach ($modules[$m_id]['controllers'] as $id=>$controller) { ?>
        <li><?= Html::a(Html::encode($controller['name']),['links', 'm_id'=>$m_id, 'c_id'=>$id]) ?></li>
    <?php } ?>
    </ul>
</div>
