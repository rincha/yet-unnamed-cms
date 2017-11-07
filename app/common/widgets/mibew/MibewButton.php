<?php

namespace app\common\widgets\mibew;
use Yii;

/**
 * @author rincha
 */
class MibewButton extends \yii\base\Widget {

    public $label='mibew';
    public $options=[];
    //replace MIBEW_URL
    public $script='http://MIBEW_URL/js/compiled/chat_popup.js';

    public $scriptInitTemplate='Mibew.ChatPopup.init({options});';
    public $scriptOpenTemplate="Mibew.Objects.ChatPopups['{options.id}'].open();";

    public $scriptAfterOpen='';

    public $clientOptions=[
        'id'=>'', //required
        'url'=>'',
        'preferIFrame'=>true,
        'modSecurity'=>false,
        'height'=>480,
        'width'=>640,
        'resizable'=>true,
        'styleLoader'=>'',
    ];

    public $autoload=false;

    public function run() {
        return $this->render('mibew');
    }
}
