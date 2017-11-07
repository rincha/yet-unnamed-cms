<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $modules Array */
/* @var $m_id string */
/* @var $c_id string */
?>
<?= $this->render('links-nav',['modules'=>$modules, 'm_id'=>$m_id, 'c_id'=>$c_id]) ?>
<div class="admin-default-links-actions">
    <h2>Выберите действие</h2>
    <table class="table table-hover">
        <tbody>
    <?php foreach ($modules[$m_id]['controllers'][$c_id]['actions'] as $id=>$action) {
        $action_name=is_string($action)?$action:$action['name'];
        if (is_array($action) && isset($action['params'])) {
            $url=array_merge(['/'.$m_id.'/'.$c_id.'/'.$id],$action['params']);
        }
        else {
            $url=['/'.$m_id.'/'.$c_id.'/'.$id];
        }
        $action_data=[
            'text'=>$action_name,
            'controller'=>$m_id.'/'.$c_id,
            'action'=>$id,
            'url'=>Url::to($url),
            'params'=>(is_array($action) && isset($action['params']))?$action['params']:[],
        ];
    ?>
        <tr>
            <td><?= Html::encode(is_string($action)?$action:$action['name']) ?></td>
            <td class="text-right"><?php if (is_string($action)) { ?>
            <?= Html::button('Выбрать', ['class'=>'btn btn-success btn-xs', 'data-action'=>Json::encode($action_data)])?>
            <?php } else { ?>
            <?php if (ArrayHelper::getValue($action,'select')) { ?>
            <?= Html::button('Выбрать', ['class'=>'btn btn-success btn-xs', 'data-action'=>Json::encode($action_data)])?>
            <?php }
            if (isset($action['query'])) {
            ?>
            <?= Html::a('Найти элемент', ['links','m_id'=>$m_id,'c_id'=>$c_id,'a_id'=>$id], ['class'=>'btn btn-primary btn-xs'])?>
            <?php
            }
            } ?></td>
        </tr>
    <?php } ?>
        </tbody>
    </table>
</div>
<?php
$this->registerJs('$(".admin-default-links-actions button.btn-success").click(function(){'
        . 'var data=$(this).attr("data-action");'
        . 'var url=JSON.parse(data)["url"];'
        . '$(".admin-default-links-nav input").attr("data-action",data);'
        . '$(".admin-default-links-nav input").val(url);'
        . '$(".admin-default-links-nav input").change();'
        . '});');
?>
