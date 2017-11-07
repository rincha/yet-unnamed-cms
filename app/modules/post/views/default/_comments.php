<?php

use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $post app\modules\post\models\Post */
/* @var $parent app\modules\post\models\Comment|null */
/* @var $newComment app\modules\post\models\Comment */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="post-comments">
    <?php $pjax=Pjax::begin([
        'id'=>'post-pjax-'.$post->post_id.'-'.($parent?$parent->comment_id:'root'),
    ]); ?>
    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
        'emptyText'=>$parent?false:null,
        'itemView' => '_comment',
        'viewParams'=>['parent'=>$parent,'post'=>$post,'newComment'=>$newComment],
        'itemOptions'=>['tag'=>false],
        'layout' => $parent?"{items}\n{pager}":"{summary}\n{items}\n{pager}",
    ]);
    ?>
<?php
$js=<<<JS
$("#$pjax->id .post-comment-actions a").click(function(){
    $.ajax({
        url: $(this).attr("href"),
        method: "post"
    })
    .done(function(data){
        $.pjax.reload({container:"#$pjax->id",timeout:5000});
    })
    .fail(function(r){
        alert(r.responseText);
    });
    return false;
});
$('#$pjax->id .post-comment-reply-btn').on("click",function(){
    $('#post-form-answer .panel-body').html($("#comment-"+$(this).attr("data-id")+" .data").html());
    $("#comment-parent_id").val($(this).attr("data-id"));
    $('#post-form-answer').slideDown();
});
JS;
$this->registerJs($js);
    Pjax::end(); ?>
</div>
<?php

