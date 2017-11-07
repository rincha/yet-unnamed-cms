<?php
/**
 * @copyright Copyright (c) 2016 rincha263
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */
namespace app\common\grid;

use yii\helpers\Html;
use yii\helpers\Url;
use Yii;
/**
 * @author rincha263
 */
class ActionColumn extends \yii\grid\ActionColumn {

    public $defaultButtonsActions=[
        'view'=>'view',
        'update'=>'update',
        'delete'=>'delete',
    ];

    public $contentOptions=['class'=>'text-right'];

    public $buttonSize=null;

    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     * @param string $action the button name (or action ID)
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        $action=isset($this->defaultButtonsActions[$action])?$this->defaultButtonsActions[$action]:$action;
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index, $this);
        } else {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            return Url::toRoute($params);
        }
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('view', 'eye',['class'=>'btn btn-sm btn-default']);
        $this->initDefaultButton('update', 'pencil',['class'=>'btn btn-sm btn-primary']);
        $this->initDefaultButton('delete', 'trash', [
            'class'=>'btn btn-sm btn-danger',
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ]);
    }

    /**
     * Initializes the default button rendering callback for single button
     * @param string $name Button name as it's written in template
     * @param string $iconName The part of Bootstrap glyphicon class that makes it unique
     * @param array $additionalOptions Array of additional options
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                if ($this->buttonSize) {
                    Html::addCssClass($options, 'btn-'.$this->buttonSize);
                }
                $icon = Html::tag('span', '', ['class' => "fa fa-fw fa-$iconName"]);
                return Html::a($icon, $url, $options);
            };
        }
    }
}
