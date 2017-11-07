<?php

use yii\helpers\Html;
use app\modules\rbac\models\AuthItem;

/* @var $this yii\web\View */
/* @var $controllers Array */

$this->title = Yii::t('rbac', 'Create controller roles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Rights'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rbac-auth-ccr">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::beginForm() ?>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th></th>
                <th><?= Yii::t('rbac', 'Auth Item') ?></th>
                <th><?= Yii::t('rbac', 'Route') ?></th>
                <th><?= Yii::t('rbac', 'Class Name') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($controllers as $module) {
                if (isset($module['controllers']) && $module['controllers']) {
            ?>
            <tr class="info">
                <td colspan="4" class="text-center"><strong><?= $module['name'] ?></strong></td>
            </tr>
            <?php
                    foreach ($module['controllers'] as $c) {
                        ?>
            <tr>
                <td><?= Html::checkbox('create[]', AuthItem::findOne(['name'=>$c['authItem']])!==null,['value'=>$c['authItem']]) ?></td>
                <td><?= $c['authItem'] ?></td>
                <td><?= $c['route'] ?></td>
                <td><?= $c['className'] ?></td>
            </tr>
                        <?php
                    }
                }
            }
            ?>
        </tbody>
    </table>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rbac', 'Create'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?= Html::endForm() ?>

</div>
