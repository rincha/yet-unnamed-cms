<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */
namespace app\common\components;
/**
 *
 * @author rincha
 */
class ProfileBase extends \yii\db\ActiveRecord {

    public function getProfileLabel() {
        return trim(preg_replace('/([A-Z]{1})/u',' $1',substr(strrchr(get_class($this), '\\'), 1)));
    }

    public function getProfileId() {
        return trim(mb_strtolower(preg_replace('/([A-Z]{1})/u','-$1',substr(strrchr(get_class($this), '\\'), 1)),'UTF-8'),'-');
    }

    public function getProfileUrl() {
        return ['/u/' . $this->getProfileId() . '/index'];
    }

}
