<?php
/**
 * @copyright Copyright (c) 2017 rincha
 * @license BSD https://opensource.org/licenses/BSD-3-Clause
 */

namespace app\common\helpers;

require_once __DIR__.DIRECTORY_SEPARATOR.'htmlPurifier'.DIRECTORY_SEPARATOR.'HTMLPurifier_advanced_classes.php';
/**
 *
 * @author rincha
 */
class HtmlPurifier extends \yii\helpers\BaseHtmlPurifier {

    /*
     * extract from $config['Custom.Filter'] string names and add to configInstance
     */
    public static function process($content, $config = null) {

        $filters=[]; $modules=[];
        if (!($config instanceof \Closure) && is_array($config)) {
            if (isset($config['Custom.Filter'])) {
                foreach ($config['Custom.Filter'] as $k=>$className) {
                    $filters[]=$className;
                }
                unset($config['Custom.Filter']);
            }
            if (isset($config['Custom.Module'])) {
                foreach ($config['Custom.Module'] as $k=>$name) {
                    $modules[]=$name;
                }
                unset($config['Custom.Module']);
            }
        }
        $configInstance = \HTMLPurifier_ConfigExt::create($config instanceof \Closure ? null : $config);
        $configInstance->autoFinalize = false;
        $configInstance->secret_key = \Yii::$app->params['salt'];
        $configInstance->custom_params = ['redirectUrl'=>\yii\helpers\Url::to(['/site/away'])];

        $purifier = \HTMLPurifier::instance($configInstance);
        $purifier->config->set('Cache.SerializerPath', \Yii::$app->getRuntimePath());
        $purifier->config->set('Cache.SerializerPermissions', 0775);


        if ($modules) {
            $html = $configInstance->getHTMLDefinition(true);
            foreach ($modules as $name) {
                $html->manager->addModule($name);
            }
        }
        $html=$configInstance->getDefinition('HTML',true);
        $html->manager->attrTypes->set('Class',new \HTMLPurifier_AttrDef_HTML_Class_Exp());

        if ($filters) {
            $uri = $configInstance->getDefinition('URI');
            foreach ($filters as $className) {
                $uri->addFilter(new $className, $configInstance);
            }
        }

        static::configure($configInstance);
        if ($config instanceof \Closure) {
            call_user_func($config, $configInstance);
        }
        return $purifier->purify($content);
    }

}


