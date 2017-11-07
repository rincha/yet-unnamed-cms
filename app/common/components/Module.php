<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\common\components;

/**
 * Copyright (c) 2016-2017 rincha
 * @author rincha
 * @license MIT, For the full copyright and license information, please view the LICENSE
 */
class Module extends \yii\base\Module {

    /**
     * @var boolean need to install migrations for this module
     */
    public $migrations = false;

    /**
     * @return array
     */
    public function getLinksDefinition(){
        return [];
    }

    public function init() {
        parent::init();
        die('!');
    }

}
