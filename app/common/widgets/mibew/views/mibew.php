<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $this->context app\common\widgets\mibew\MibewButton */

$options=$this->context->options;
if (!isset($options['id'])) {
    $options['id']=$this->context->id;
}

$js_init= str_replace('{options}', yii\helpers\Json::encode($this->context->clientOptions), $this->context->scriptInitTemplate);
$js_open= str_replace('{options.id}', $this->context->clientOptions['id'], $this->context->scriptOpenTemplate);
$js_after=str_replace('{id}', $options['id'], $this->context->scriptAfterOpen);

echo Html::button($this->context->label,$options);

if ($this->context->autoload) {
    $js='$.getScript("'.$this->context->script.'",function(){'
        . $js_init
        . '});';
    $jsclick=$js_open.$js_after;
    $this->registerJs($js);
    $this->registerJs('$("#'.$options['id'].'").click(function(){'.$jsclick.'});');
}
else {
    $js='$.getScript("'.$this->context->script.'",function(){'
        . $js_init
        . $js_open
        . $js_after
        . '});';

    $this->registerJs('$("#'.$options['id'].'").click(function(){'.$js.'});');
}



