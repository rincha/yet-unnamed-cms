<?php

namespace app\modules\banner\widgets;

use app\modules\banner\models\Banner;
use app\modules\banner\models\BannerItem;
use Yii;

class SiteWgtBanner extends \app\common\widgets\SiteWidget {

    private $_banner;

    public function run() {
        if ($this->getBanner() instanceof Banner) {
            $model=$this->getBanner();
            $items=$model->getItems()
                    ->where(['status'=>BannerItem::STATUS_ENABLED])
                    ->andWhere(
                            (new \yii\db\Query)
                            ->where(['>=','end_at',new \yii\db\Expression('NOW()')])
                            ->orWhere(['end_at'=>null])->where
                    )
                    ->andWhere(
                            (new \yii\db\Query)
                            ->where(['<=','start_at',new \yii\db\Expression('NOW()')])
                            ->orWhere(['start_at'=>null])->where
                    )
                    ->all();
            return $this->render($this->getViewName(), [
                'model' => $model,
                'items' => $items,
                'widget' => $this->widget,
                'id'=>$this->id]);
        }
        else {
            return null;
        }
    }

    public function getBanner() {
        if ($this->_banner) {return $this->_banner;}
        if (is_numeric($this->options['banner'])) {
            $this->_banner=Banner::findOne($this->options['banner']);
        }
        return $this->_banner;
    }

    public function getViewName() {
        if ($this->getBanner() && $this->getBanner()->type_id==Banner::TYPE_SLIDER) {
            return 'wgt-banner-slider';
        }
        else {
            return 'wgt-banner';
        }
    }


    public function getOptionsAttributes() {
        $thumbnails=[];
        foreach (\Yii::$app->params['images']['thumbnails'] as $type=>$list) {
            $thumbnails= array_merge($thumbnails,$list);
        }
        return [
            'banner'=>[
                'rules'=>['yii\validators\ExistValidator','targetClass'=>Banner::className(), 'targetAttribute'=>'banner_id'],
                'label'=>  'Баннер',
                'hint'=>null,
            ],
            'thumbnail'=>[
                'rules'=>['yii\validators\RangeValidator','range'=>$thumbnails],
                'label'=>  'Размер изображения',
                'hint'=>'Один из вариантов (e - точный размер дополнением, r - по максимальной стороне, c - точный размер с обрезкой): '. implode(', ', $thumbnails),
            ],
        ];
    }

    public function renderAdminView(\app\models\Widget $model, \yii\widgets\ActiveForm $form) {
        return $this->render('wgt-banner-admin',['form'=>$form,'model'=>$model]);
    }
}

?>