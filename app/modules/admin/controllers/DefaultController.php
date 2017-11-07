<?php

namespace app\modules\admin\controllers;

use Yii;

class DefaultController extends \app\common\web\AdminController {

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionRequirements() {
        require(Yii::getAlias('@app/requirements').'.php');
    }

    public function actionLastInfoLinks($term=null) {
        $q=\app\modules\info\models\Info::find();
        if ($term) {
            $q->where(['like','name',$term]);
        }
        $q->orderBy(['created_at'=>SORT_DESC])->limit(10);
        $models=$q->all();
        $res=[];
        foreach ($models as $model) {
            $res[]=[
                'title'=>$model->name,
                'value'=>$model->getUrl(false),
            ];
        }
        return \yii\helpers\Json::encode($res);
    }

    public function actionLinks($m_id=null, $c_id=null, $a_id=null, $term=null) {
        $this->layout='iframe';
        $modules=[];
        foreach (Yii::$app->modules as $key=>$m) {
            $module=Yii::$app->getModule($key);
            if (method_exists($module, 'getLinksDefinition')) {
                $list=$module->getLinksDefinition();
                $modules[$module->id]=$list;
            }

        }
        if ($a_id===null && $c_id===null && $m_id===null) {
            return $this->render(
                'links-modules',
                ['modules'=>$modules, 'm_id'=>$m_id, 'c_id'=>$c_id, 'a_id'=>$a_id, 'term'=>$term]
            );
        }
        if ($a_id===null && $c_id===null && isset($modules[$m_id])) {
            if (count($modules[$m_id]['controllers'])==1) {
                $c_id= key($modules[$m_id]['controllers']);
            }
            else {
                return $this->render(
                    'links-controllers',
                    ['modules'=>$modules, 'm_id'=>$m_id]
                );
            }
        }
        if ($a_id===null && $m_id && $c_id && isset($modules[$m_id]) && isset($modules[$m_id]['controllers'][$c_id])) {
            return $this->render(
                'links-actions',
                ['modules'=>$modules, 'm_id'=>$m_id, 'c_id'=>$c_id]
            );
        }
        if ($a_id && $m_id && $c_id && isset($modules[$m_id]) && isset($modules[$m_id]['controllers'][$c_id]) && isset($modules[$m_id]['controllers'][$c_id]['actions'][$a_id])) {
            $action=$modules[$m_id]['controllers'][$c_id]['actions'][$a_id];
            if (is_array($action) && isset($action['query'])) {
                $query=$action['query']($term);
                $dp=new \yii\data\ActiveDataProvider(['query'=>$query]);
                return $this->render(
                    'links-search',
                    ['modules'=>$modules, 'm_id'=>$m_id, 'c_id'=>$c_id, 'a_id'=>$a_id, 'dp'=>$dp, 'term'=>$term]
                );
            }
        }
        throw new \yii\web\BadRequestHttpException();
    }

}
