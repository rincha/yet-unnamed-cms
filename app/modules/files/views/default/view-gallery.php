<?php

use yii\helpers\Html;
use app\common\helpers\AppHelper;
use app\common\helpers\ImageHelper;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $folder app\modules\files\models\Folder */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title=AppHelper::getPageTitle($folder->name, ['title'=>$folder->name]);
if ($folder->parent) {
    $this->params['breadcrumbs'][] = ['label' => $folder->parent->name, 'url' => ['view', 'id'=>$folder->parent_id]];
}
$this->params['breadcrumbs'][] = $folder->name;
?>
<h1><?= Html::encode($folder->name)?></h1>
<div class="file-view">
    <div class="file-view-description">
        <?= $folder->description ?>
    </div>
    <div class="row">
        <?php foreach ($folder->childs as $child) {
            $file=$child->getFiles()->one();
        ?>
        <div class="col-xs-6 col-sm-4 col-lg-3 folder-item text-center form-group">
            <?=
            Html::a(
                Html::img(
                        ImageHelper::getThumbnail(
                                $file?$file->getUrl():null, '300e300', true),
                                ['class'=>'img-responsive img-thumbnail','alt'=>$child->name]
                        ).
                    Html::tag('span',Html::encode($child->name),['class'=>'title'])
                        ,
                ['view', 'id'=>$child->folder_id],
                ['class'=>'folder']
            )
            ?>
        </div>
        <?php } ?>
    </div>
    <?= ListView::widget([
        'dataProvider'=>$dataProvider,
        'layout'=>"{summary}\n<div class='row'>{items}</div>\n{pager}",
        'itemOptions'=>['tag'=>false],
        'itemView'=>'view-gallery-item',
        'emptyText'=>$folder->childs?false:null,
    ]) ?>
</div>