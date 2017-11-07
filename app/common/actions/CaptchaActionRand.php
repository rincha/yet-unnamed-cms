<?php

namespace app\common\actions;

use Yii;

class CaptchaActionRand extends \yii\captcha\CaptchaAction
{
    protected function getRandFontFile() {
        $files=glob(\Yii::getAlias('@app/common/fonts/captcha').DIRECTORY_SEPARATOR.'*.ttf');
        return $files[rand(0, count($files)-1)];
    }

    public function init()
    {
        $this->fontFile = $this->getRandFontFile();


        $f = array(mt_rand(0,150), mt_rand(0,150), mt_rand(0,150));
        //$b = array(mt_rand(230,255), mt_rand(230,255), mt_rand(200,255));
        $this->foreColor = hexdec('0x'.dechex($f[0]).dechex($f[1]).dechex($f[2]));
        //$this->backColor = hexdec('0x'.dechex($b[0]).dechex($b[1]).dechex($b[2]));

        if (!is_file($this->fontFile)) {
            throw new InvalidConfigException("The font file does not exist: {$this->fontFile}");
        }
    }

    /**
     * Generates a new verification code.
     * @return string the generated verification code
     */
    protected function generateVerifyCode()
    {
        if ($this->minLength > $this->maxLength) {
            $this->maxLength = $this->minLength;
        }
        if ($this->minLength < 3) {
            $this->minLength = 3;
        }
        if ($this->maxLength > 20) {
            $this->maxLength = 20;
        }
        $length = mt_rand($this->minLength, $this->maxLength);

        $numbers = '1234567890';
        $symbols = 'WGQJRSLNDZXV';
        $code = '';
        for ($i = 0; $i < $length; ++$i) {
            if ($i % 2 && mt_rand(0, 10) > 2 || !($i % 2) && mt_rand(0, 10) > 9) {
                $code .= $symbols[mt_rand(0, strlen($symbols)-1)];
            } else {
                $code .= $numbers[mt_rand(0, strlen($numbers)-1)];
            }
        }
        Yii::trace('New verify code for '.$this->getSessionKey().': '.$code, 'captcha');
        return $code;
    }

    /**
     * Validates the input to see if it matches the generated code.
     * @param string $input user input
     * @param bool $caseSensitive whether the comparison should be case-sensitive
     * @return bool whether the input is valid
     */
    public function validate($input, $caseSensitive)
    {
        $code = $this->getVerifyCode();
        $valid = $caseSensitive ? ($input === $code) : strcasecmp($input, $code) === 0;
        $session = Yii::$app->getSession();
        $session->open();
        $name = $this->getSessionKey() . 'count';
        $count=$session->get($name,0);
        $session->set($name, $count);
        Yii::trace('Validate verify code.'.\yii\helpers\VarDumper::dumpAsString([
            'Session count name'=>$name,
            'Code'=>$code,
            'Input'=>$input,
            'CaseSensitive'=>$caseSensitive,
            'Valid'=>$valid,
            'Count'=>$count,
            'TestLimit'=>$this->testLimit
        ]), 'captcha');

        if ($valid || $count > $this->testLimit && $this->testLimit > 0) {
            $this->getVerifyCode(true);
        }

        return $valid;
    }
}
