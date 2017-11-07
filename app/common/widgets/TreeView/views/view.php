<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $items Array */
/* @var $nameAttribute string */
/* @var $sortAttribute string */
/* @var $rowOptions mixed */
/* @var $sortButtons Array */
/* @var $actionButtons Array */

?>

<div class="wgt-treeview">
<?php
$treeBuild=function($items)use(&$treeBuild,$nameAttribute,$sortButtons,$actionButtons,$sortAttribute,&$rowOptions) {
    $list='';
    foreach ($items as $item) {

        $content='';

        if ($sortButtons) {

            $params=[];
            foreach ($sortButtons['params'] as $param=>$attribute) {
                $params[$param]=$item['model']->{$attribute};
            }
            $up=array_merge($sortButtons['up'],$params);
            $down=array_merge($sortButtons['down'],$params);

            $btns=Html::a('<i class="fa fa-arrow-up"></i>', $up, [
                'class' => 'btn btn-primary btn-sm',
                'data' => ['method' => 'post'],
            ]);
            $btns.=Html::a('<i class="fa fa-arrow-down"></i>', $down, [
                'class' => 'btn btn-primary btn-sm',
                'data' => ['method' => 'post'],
            ]);

            $content.=Html::tag('div',$btns,['class'=>'btn-group pull-left']);
        }

        if ($actionButtons) {
            $params=[];
            foreach ($actionButtons['params'] as $param=>$attribute) {
                $params[$param]=$item['model']->{$attribute};
            }
            $btns='';
            if (isset($actionButtons['view']) && $actionButtons['view']) {
                $url=array_merge($actionButtons['view'],$params);
                $btns.=Html::a('<i class="fa fa-eye"></i>', $url, [
                    'class' => 'btn btn-default btn-sm',
                ]);
            }
            if (isset($actionButtons['update']) && $actionButtons['update']) {
                $url=array_merge($actionButtons['update'],$params);
                $btns.=Html::a('<i class="fa fa-pencil"></i>', $url, [
                    'class' => 'btn btn-primary btn-sm',
                ]);
            }
            if (isset($actionButtons['delete']) && $actionButtons['delete']) {
                $url=array_merge($actionButtons['delete'],$params);
                $btns.=Html::a('<i class="fa fa-times"></i>', $url, [
                    'class' => 'btn btn-danger btn-sm',
                    'data' => [
                        'confirm' => Yii::t('app/user', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            }
            $content.=Html::tag('div',$btns,['class'=>'btn-group pull-right']);
        }

        if ($sortAttribute) {
            $content.='<span class="badge">'.(int)$item['model']->{$sortAttribute}.'</span> ';
        }

        $content.=$item['model']->{$nameAttribute};

        $content=Html::tag('div',$content,['class'=>'item']);

        if ($item['childs']) {
            $content.=$treeBuild($item['childs']);
        }

        if (is_array($rowOptions)) {
            $liOptions=$rowOptions;
        }
        elseif (is_callable($rowOptions)) {
            $liOptions=$rowOptions($item);
        }
        else {
            $liOptions=[];
        }

        $list.=Html::tag('li',$content, $liOptions);
    }
    return Html::tag('ul',$list);
};
echo $treeBuild($items,$rowOptions);
?>
</div>