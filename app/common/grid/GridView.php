<?php

/**
 * @copyright Copyright (c) 2016 rincha263
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\grid;

use yii\helpers\Url;
use yii\helpers\Html;
use Yii;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;

/**
 * @author rincha263
 */
class GridView extends \yii\grid\GridView {

    /**
     * if true, footer cell is not displayed when property footer==false ,
     */
    public $footerColspans = false;
    public $filterSelectorTemplate = '#{id} input, #{id} select';
    public $options = ['class' => 'grid-view table-responsive'];
    public $reverseRows = false;
    public $pagers = false;

    /**
     * Returns the options for the grid view JS widget.
     * @return array the options
     */
    protected function getClientOptions() {
        $filterUrl = isset($this->filterUrl) ? $this->filterUrl : Yii::$app->request->url;
        $id = $this->filterRowOptions['id'];

        $filterSelector = str_replace('{id}', $id, $this->filterSelectorTemplate);

        if (isset($this->filterSelector)) {
            $filterSelector .= ', ' . $this->filterSelector;
        }

        return [
            'filterUrl' => Url::to($filterUrl),
            'filterSelector' => $filterSelector,
        ];
    }

    /**
     * Renders the table footer.
     * @return string the rendering result.
     */
    public function renderTableFooter() {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $this->footerColspans && $column->footer === false ? '' : $column->renderFooterCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->footerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= $this->renderFilters();
        }

        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    /**
     * Renders the table body.
     * @return string the rendering result.
     */
    public function renderTableBody() {
        if ($this->reverseRows) {
            $models = array_values($this->dataProvider->getModels());
            $keys = $this->dataProvider->getKeys();
            $rows = [];
            foreach ($models as $index => $model) {
                $key = $keys[$index];
                if ($this->beforeRow !== null) {
                    $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                    if (!empty($row)) {
                        $rows[] = $row;
                    }
                }

                $rows[] = $this->renderTableRow($model, $key, $index);

                if ($this->afterRow !== null) {
                    $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                    if (!empty($row)) {
                        $rows[] = $row;
                    }
                }
            }

            if (empty($rows)) {
                $colspan = count($this->columns);

                return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
            } else {
                return "<tbody>\n" . implode("\n", array_reverse($rows)) . "\n</tbody>";
            }
        } else {
            return parent::renderTableBody();
        }
    }

    private static $_pagers=0;
    /**
     * Renders the pager.
     * @return string the rendering result
     */
    public function renderPager() {
        if ($this->pagers) {
            $pagination = $this->dataProvider->getPagination();
            if ($pagination === false || $this->dataProvider->getCount() <= 0) {
                return '';
            }
            /* @var $class LinkPager */
            $pager = $this->pagers[self::$_pagers];
            self::$_pagers++;
            $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
            $pager['pagination'] = $pagination;
            $pager['view'] = $this->getView();
            return $class::widget($pager);
        }
        else {
            return parent::renderPager();
        }
    }



}
