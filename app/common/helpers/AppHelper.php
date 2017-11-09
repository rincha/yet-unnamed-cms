<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\helpers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Settings;


/**
 *
 * @author rincha
 */
class AppHelper {

    protected static $_settings;

    /**
     * @return mixed stored setting value by key
     */
    public static function getSettingValue($key, $default = null) {
        if (isset(self::$_settings[$key]) && is_object(self::$_settings[$key])) {
            if (self::$_settings[$key]->serialized) {
                return unserialize(self::$_settings[$key]->value);
            } else {
                return self::$_settings[$key]->value;
            }
        } else {
            $model = Settings::findOne(['key' => $key]);
            if (!$model) {
                $model = new Settings();
                $model->value = $default;
                if (is_object($model->value)) {
                    $model->serialized = 1;
                }
            }
            self::$_settings[$key] = $model;
            if (self::$_settings[$key]->serialized)
                return unserialize(self::$_settings[$key]->value);
            else
                return self::$_settings[$key]->value;
        }
    }

    /**
     * @return string page title by settings key page.titles:controllerUniqueId/actionId
     */
    public static function getPageTitle($default = null, $variables = []) {
        $key = 'page.titles:' . Yii::$app->controller->uniqueId;
        $key .= '/' . Yii::$app->controller->action->id;
        $title = self::getSettingValue($key, $default === null ? $key : $default);
        foreach ($variables as $search => $replace) {
            $title = str_replace('{' . $search . '}', $replace, $title);
        }
        return $title;
    }

    /**
     * @return string page breadcrumb by settings key page.breadcrumbs:controllerUniqueId/actionId
     */
    public static function getPageBreadcrumb($default = null, $variables = []) {
        $key = 'page.breadcrumbs:' . Yii::$app->controller->uniqueId;
        $key .= '/' . Yii::$app->controller->action->id;
        $value = self::getSettingValue($key, $default === null ? $key : $default);
        foreach ($variables as $search => $replace) {
            $value = str_replace('{' . $search . '}', $replace, $value);
        }
        return $value;
    }

    /**
     * @return string page h1 by settings key page.h1:controllerUniqueId/actionId
     */
    public static function getPageH1($default = null, $variables = []) {
        $key = 'page.h1:' . Yii::$app->controller->uniqueId;
        $key .= '/' . Yii::$app->controller->action->id;
        $title = self::getSettingValue($key, $default === null ? $key : $default);
        foreach ($variables as $search => $replace) {
            $title = str_replace('{' . $search . '}', $replace, $title);
        }
        return $title;
    }

    /**
     * @param string $type
     * @return array tinyMce config for widget app\common\widgets\tinymce\Tinymce
     */
    public static function getTinyMceConfig($type = 'full') {
        $options = [];
        switch ($type) {
            case 'pure':
                $options['plugins'] = explode(',', 'charmap,spellchecker,textcolor');
                $options['external_plugins']=[];
                $options['menu'] = [];
                $options['toolbar'] = [
                    "bold italic underline strikethrough | forecolor | removeformat",
                ];
                $options['valid_elements'] = self::getHtmlPurifyOptions($type)['HTML.AllowedElements'];
                break;
            case 'simple':
                $options['plugins'] = explode(',', 'charmap,spellchecker,textcolor');
                $options['external_plugins']=[];
                $options['menu'] = [];
                $options['toolbar'] = [
                    "bold italic underline strikethrough | forecolor backcolor | bullist numlist outdent indent | subscript superscript | charmap| removeformat",
                ];
                $options['valid_elements'] = self::getHtmlPurifyOptions($type)['HTML.AllowedElements'];
                break;
            case 'simple_links':
                $options['plugins'] = explode(',', 'link,charmap,spellchecker,textcolor');
                $options['external_plugins']=[];
                $options['menu'] = [];
                $options['toolbar'] = [
                    "link unlink | bold italic underline strikethrough | forecolor backcolor | bullist numlist outdent indent | subscript superscript | charmap| removeformat",
                ];
                $options['valid_elements'] = self::getHtmlPurifyOptions($type)['HTML.AllowedElements'];
                break;
            case 'light':
                $options['plugins'] = explode(',', 'charmap,spellchecker,textcolor');
                $options['external_plugins']=[];
                $options['menu'] = [];
                $options['toolbar'] = [
                    "formatselect | bold italic underline strikethrough | forecolor backcolor | bullist numlist outdent indent | subscript superscript | charmap| removeformat",
                ];
                $options['valid_elements'] = self::getHtmlPurifyOptions($type)['HTML.AllowedElements'];
                break;
            case 'light_links':
                $options['plugins'] = explode(',', 'link,charmap,spellchecker,textcolor');
                $options['external_plugins']=[];
                $options['menu'] = [];
                $options['toolbar'] = [
                    "link unlink | formatselect | bold italic underline strikethrough | forecolor backcolor | bullist numlist outdent indent | subscript superscript | charmap| removeformat",
                ];
                $options['valid_elements'] = self::getHtmlPurifyOptions($type)['HTML.AllowedElements'];
                break;
            case 'medium':
                $options['plugins'] = explode(',', 'template,hr,link,image,charmap,paste,print,preview,anchor,pagebreak,spellchecker,searchreplace,visualblocks,visualchars,code,fullscreen,insertdatetime,media,nonbreaking,save,table,directionality,template,textcolor');
                break;
            case 'full':
                $options['plugins'] = explode(',', 'template,hr,link,lists,image,charmap,paste,pastehtml,print,preview,anchor,pagebreak,spellchecker,searchreplace,visualblocks,visualchars,code,fullscreen,insertdatetime,media,nonbreaking,save,table,directionality,template,textcolor');
                $options['menu'] = [];
                $options['toolbar'] = [
                    "pastetext pastehtml | undo redo searchreplace | removeformat fullscreen preview code visualchars visualblocks | hr charmap | template",
                    "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect fontsizeselect | forecolor backcolor",
                    "bullist numlist outdent indent | link unlink anchor | subscript superscript | table | media image rfiles | styleselect | style"
                ];
                $options['rfiles'] = [
                    'thumbnails' => [
                        'enabled' => true,
                        'extensions' => ['jpg', 'png', 'gif'],
                        'thumbnail' => ['ExactFit', 0],
                        'sizes' => Yii::$app->params['images']['thumbnails'],
                    ],
                    'actions' => [
                        'getFolders' => Url::to(['/admin/files/api-get-folders']),
                        'getFiles' => Url::to(['/admin/files/api-get-files']),
                        'deleteFile' => Url::to(['/admin/files/api-delete-file']),
                        'deleteFolder' => Url::to(['/admin/files/api-delete-folder']),
                        'createFolder' => Url::to(['/admin/files/api-create-folder']),
                        'createFile' => Url::to(['/admin/files/api-create-file']),
                    ],
                    'additionalFormsParams' => [
                        Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
                    ]
                ];
                break;
        }

        return $options;
    }

    /**
     * @param string $type
     * @return array htmlPurifi config for helper app\common\helpers\HtmlPurifier
     */
    public static function getHtmlPurifyOptions($type = 'light') {
        $opts = [
            'AutoFormat.RemoveEmpty' => true,
            'HTML.Nofollow' => true,
            'URI.Host' => ArrayHelper::getValue(Yii::$app->params, 'host', ArrayHelper::getValue($_SERVER, 'SERVER_NAME',null)),
            'URI.Base' => Yii::$app->urlManager->getBaseUrl(),
        ];
        switch ($type) {
            case 'pure':
                $opts['HTML.AllowedElements'] = 'b,br,em,i,p,strike,strong,u,span';
                $opts['HTML.AllowedAttributes'] = '*.style';
                $opts['CSS.AllowedProperties'] = 'text-decoration,font-style,color';
                $opts['URI.DisableExternal'] = true;
                break;
            case 'simple':
                $opts['HTML.AllowedElements'] = 'b,blockquote,br,em,i,li,ol,p,strike,strong,sub,sup,u,ul,span';
                $opts['HTML.AllowedAttributes'] = '*.style';
                $opts['CSS.AllowedProperties'] = 'text-decoration,font-style,color,background-color';
                $opts['URI.DisableExternal'] = true;
                break;
            case 'simple_links':
                $opts['HTML.AllowedElements'] = 'a,b,blockquote,br,em,i,li,ol,p,strike,strong,sub,sup,u,ul,span';
                $opts['HTML.AllowedAttributes'] = '*.style';
                $opts['HTML.AllowedAttributes'] .= ',a.href,a.name,a.target';
                $opts['CSS.AllowedProperties'] = 'text-decoration,font-style,color,background-color';
                $opts['URI.AllowedSchemes'] = ['http' => true, 'https' => true];
                $opts['HTML.TargetBlank'] = true;
                $opts['HTML.Nofollow'] = true;
                $opts['AutoFormat.Linkify'] = true;
                $opts['Custom.Filter'][] = 'HTMLPurifier_URIFilter_MakeRedirect';
                $opts['Custom.Module'][] = 'TargetBlankAll';
                break;
            case 'light':
                $opts['HTML.AllowedElements'] = 'b,blockquote,br,em,h1,h2,h3,h4,h5,h6,hr,i,li,ol,p,strike,strong,sub,sup,u,ul,span';
                $opts['HTML.AllowedAttributes'] = '*.style';
                $opts['CSS.AllowedProperties'] = 'text-decoration,font-style,color,background-color';
                $opts['URI.DisableExternal'] = true;
                break;
            case 'light_links':
                $opts['HTML.AllowedElements'] = 'a,b,blockquote,br,em,h1,h2,h3,h4,h5,h6,hr,i,li,ol,p,strike,strong,sub,sup,u,ul,span';
                $opts['HTML.AllowedAttributes'] = '*.style';
                $opts['HTML.AllowedAttributes'] .= ',a.href,a.name,a.target';
                $opts['Attr.AllowedFrameTargets'] = '_blank,_self';
                $opts['CSS.AllowedProperties'] = 'text-decoration,font-style,color,background-color';
                $opts['URI.AllowedSchemes'] = ['http' => true, 'https' => true];
                $opts['HTML.TargetBlank'] = true;
                $opts['HTML.Nofollow'] = true;
                $opts['AutoFormat.Linkify'] = true;
                $opts['Custom.Filter'][] = 'HTMLPurifier_URIFilter_MakeRedirect';
                $opts['Custom.Module'][] = 'TargetBlankAll';
                break;
            case 'inline':
                $opts['HTML.AllowedElements'] = 'a,b,em,i,strike,strong,sub,sup,u';
                $opts['HTML.AllowedAttributes'] = '*.style';
                $opts['CSS.AllowedProperties'] = 'text-decoration,font-style,color,background-color';
                $opts['URI.DisableExternal'] = true;
                break;
            case 'medium':
                $opts['URI.DisableExternal'] = true;
                //allowed tags
                $opts['HTML.AllowedElements'] = 'a,b,blockquote,br,div,em,h1,h2,h3,h4,h5,h6,hr,i,img,li,ol,p,span,strike,strong,sub,sup,table,tbody,td,tfoot,th,thead,tr,u,ul';
                //allowed attributes
                $opts['HTML.AllowedAttributes'] = '';
                $opts['HTML.AllowedAttributes'] .= 'a.href,a.name';
                $opts['HTML.AllowedAttributes'] .= ',col.align,col.valign,col.span,col.width';
                $opts['HTML.AllowedAttributes'] .= ',colgroup.align,colgroup.valign,colgroup.span,colgroup.width';
                $opts['HTML.AllowedAttributes'] .= ',div.align,h1.align,h2.align,h3.align,h4.align,h5.align,h6.align';
                $opts['HTML.AllowedAttributes'] .= ',hr.align,hr.noshade,hr.size,hr.width';
                $opts['HTML.AllowedAttributes'] .= ',img.src,img.alt,img.border,img.height,img.width,img.hspace,img.longdesc,img.vspace';
                $opts['HTML.AllowedAttributes'] .= ',li.type,li.value';
                $opts['HTML.AllowedAttributes'] .= ',ol.type,ol.start';
                $opts['HTML.AllowedAttributes'] .= ',p.align';
                $opts['HTML.AllowedAttributes'] .= ',table.align,table.bgcolor,table.cellpadding,table.cellspacing,table.frame,table.rules,table.summary,table.width,table.border,table.cellpadding,table.cellspacing';
                $opts['HTML.AllowedAttributes'] .= ',tbody.align,tbody.valign';
                $opts['HTML.AllowedAttributes'] .= ',td.abbr,td.align,td.bgcolor,td.colspan,td.height,td.nowrap,td.rowspan,td.valign,td.width';
                $opts['HTML.AllowedAttributes'] .= ',tfoot.align,tfoot.valign';
                $opts['HTML.AllowedAttributes'] .= ',th.abbr,th.align,th.bgcolor,th.colspan,th.height,th.nowrap,th.rowspan,th.valign,th.width';
                $opts['HTML.AllowedAttributes'] .= ',thead.align,thead.valign';
                $opts['HTML.AllowedAttributes'] .= ',tr.align,tr.valign,tr.bgcolor';
                $opts['HTML.AllowedAttributes'] .= ',ul.type';
                $opts['HTML.AllowedAttributes'] .= ',*.style,*.title'; //global Attributes
                $opts['Attr.AllowedFrameTargets'] = '_blank,_self';
                break;
            case 'full':
                $opts['HTML.AllowedElements'] = 'a,abbr,address,b,big,blockquote,br,caption,code,col,colgroup,dd,del,dfn,div,dl,dt,em,h1,h2,h3,h4,h5,h6,hr,i,img,ins,kbd,li,menu,ol,p,pre,q,s,samp,small,span,strike,strong,sub,sup,table,tbody,td,tfoot,th,thead,tr,tt,u,ul,var';
                $opts['HTML.AllowedAttributes'] = ''; // '*.style,*.accesskey,*.class,*.title,*.id'; //global Attributes
                $opts['HTML.AllowedAttributes'] .= 'a.href,a.name,a.rel,a.target';
                $opts['HTML.AllowedAttributes'] .= ',br.clear';
                $opts['HTML.AllowedAttributes'] .= ',caption.align';
                $opts['HTML.AllowedAttributes'] .= ',col.align,col.valign,col.span,col.width';
                $opts['HTML.AllowedAttributes'] .= ',colgroup.align,colgroup.valign,colgroup.span,colgroup.width';
                $opts['HTML.AllowedAttributes'] .= ',del.cite';
                $opts['HTML.AllowedAttributes'] .= ',div.align,h1.align,h2.align,h3.align,h4.align,h5.align,h6.align';
                $opts['HTML.AllowedAttributes'] .= ',hr.align,hr.noshade,hr.size,hr.width';
                $opts['HTML.AllowedAttributes'] .= ',img.src,img.alt,img.border,img.height,img.width,img.hspace,img.longdesc,img.vspace';
                $opts['HTML.AllowedAttributes'] .= ',ins.cite';
                $opts['HTML.AllowedAttributes'] .= ',li.type,li.value';
                $opts['HTML.AllowedAttributes'] .= ',ol.type,ol.start';
                $opts['HTML.AllowedAttributes'] .= ',p.align';
                $opts['HTML.AllowedAttributes'] .= ',table.align,table.bgcolor,table.cellpadding,table.cellspacing,table.frame,table.rules,table.summary,table.width,table.border,table.cellpadding,table.cellspacing';
                $opts['HTML.AllowedAttributes'] .= ',tbody.align,tbody.valign';
                $opts['HTML.AllowedAttributes'] .= ',td.abbr,td.align,td.bgcolor,td.colspan,td.height,td.nowrap,td.rowspan,td.valign,td.width';
                $opts['HTML.AllowedAttributes'] .= ',tfoot.align,tfoot.valign';
                $opts['HTML.AllowedAttributes'] .= ',th.abbr,th.align,th.bgcolor,th.colspan,th.height,th.nowrap,th.rowspan,th.valign,th.width';
                $opts['HTML.AllowedAttributes'] .= ',thead.align,thead.valign';
                $opts['HTML.AllowedAttributes'] .= ',tr.align,tr.valign,tr.bgcolor';
                $opts['HTML.AllowedAttributes'] .= ',ul.type';
                $opts['HTML.AllowedAttributes'] .= ',*.style,*.class,*.title,*.id'; //global Attributes
                $opts['Attr.AllowedFrameTargets'] = '_blank,_self';
                $opts['URI.AllowedSchemes'] = array(
                    'http' => true,
                    'https' => true,
                    'mailto' => true,
                    'ftp' => true,
                    'nntp' => true,
                    'news' => true,
                    'tel' => true,
                    'bla' => true,
                );
                break;
        }
        return $opts;
    }

    public static function htmlPurifyLight($html) {
        return self::htmlPurify($html, 'light');
    }

    public static function htmlPurifyLightLinks($html) {
        return self::htmlPurify($html, 'light_links');
    }

    public static function htmlPurifySimple($html) {
        return self::htmlPurify($html, 'simple');
    }

    public static function htmlPurifySimpleLinks($html) {
        return self::htmlPurify($html, 'simple_links');
    }

    public static function htmlPurifyFull($html) {
        return self::htmlPurify($html, 'full');
    }

    public static function htmlPurify($html, $type = 'light', $options = []) {
        $config = array_merge(self::getHtmlPurifyOptions($type), $options);
        return HtmlPurifier::process($html, $config);
    }

    public static function translit($str) {
        $tr = array(
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
            "Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
            "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
            "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
            "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
            "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
            "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya"
        );
        return strtr($str, $tr);
    }

    public static function safeStr($str, $rep = '-', $dop_allow = '', $tolower = false, $maxlen = 64, $double_rep = false) {
        if ($tolower) {
            $str = strtolower($str);
        }
        $allowed = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890' . $dop_allow;
        for ($i = 0; $i < strlen($str); $i++) {
            if (strpos($allowed, $str[$i]) === false) {
                $str[$i] = $rep;
            }
        }
        if (!$double_rep) {
            $str = preg_replace('/' . $rep . $rep . '/ui', $rep, $str);
        }
        if (strlen($str) > $maxlen) {
            $str = substr($str, 0, $maxlen);
        }
        return $str;
    }

    public static function render($_file_, $_params_ = []) {
        $_file_ = Yii::getAlias($_file_);
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        require($_file_);
        return ob_get_clean();
    }

    public static function getDomain($url) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>([a-zа-я0-9][а-яa-z0-9-]{1,63}\.)+[а-яa-z-]{2,10})$/i', $domain, $regs)) {
            if (preg_match('/[а-я]+/ui', $regs['domain'])) {
                return idn_to_ascii($regs['domain']);
            } else {
                return $regs['domain'];
            }
        }
        return false;
    }

    public static function stringWrap($string,$length=20,$encode=true,$separator='<wbr>') {
        if ($encode) {$string=\yii\helpers\Html::encode($string);}
        return preg_replace('/([^ &]{'.$length.'})/ui','$1'.$separator, $string);
    }

}
