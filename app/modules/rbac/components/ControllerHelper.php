<?php

namespace app\modules\rbac\components;

use app\common\web\DefaultController;
use app\common\web\AdminController;

use Yii;

/**
 *
 * @author rincha
 */
class ControllerHelper {

    public static function getAllControllers() {
        $list = [];
        $modules = Yii::$app->getModules();
        foreach ($modules as $key => $m) {
            $list[$key] = [
                'type'=>'module',
                'name'=>$key,
            ];
            $module=Yii::$app->getModule($key);
            $controller_path = $module->controllerPath;
            $file_list = glob($controller_path . "/*Controller.php");
            $file_list2 = glob($controller_path . "/*/*Controller.php");
            if (is_array($file_list2)) {
                $file_list=  array_merge($file_list,$file_list2);
            }
            foreach ($file_list as $file) {
                $filename = str_replace('.php', '', basename($file));
                $inner='';
                $inner2='';
                $temp=str_replace($controller_path.'/', '', $file);
                $temp=str_replace(basename($file), '', $temp);
                if ($temp) {
                    $temp=explode('/',$temp);
                    if (count($temp)>0) {
                        $inner=implode('\\', $temp).'';
                        $inner2=implode('/', $temp).'';
                    }
                }
                $className = $module->controllerNamespace . '\\' . $inner. $filename;
                $contollerName = str_replace('Controller', '', $filename);
                $contollerName = strtolower(preg_replace('/([A-Z])/u', '-$1', lcfirst($contollerName)));
                $contollerId=$contollerName;

                if ($module->controllerMap) {
                    if (array_search($className, $module->controllerMap)) {
                        $contollerId=array_search($className, $module->controllerMap);
                    }
                }

                $controller = new $className($contollerId, Yii::$app->getModule($key));

                if (($controller instanceof DefaultController && $controller->rbacEnable) || $controller instanceof AdminController) {
                    $list[$key]['controllers'][$contollerId]=[
                        'id' => $contollerId,
                        'name' => $contollerName,
                        'uniqueId' => $controller->uniqueId,
                        'route' => $controller->route,
                        'authItem'=>  $module->uniqueId.'.'.$controller->id.'.*',
                        'className'=>$className,
                    ];
                }
            }
        }
        return $list;
    }

    public static function getAllActions() {
        $actions = [];
        $modules = Yii::$app->getModules();
        $all_desc = [];
        foreach ($modules as $key => $module) {
            $actions[$key] = [];
            $controller_path = Yii::$app->getModule($key)->controllerPath;

            $file_list = glob($controller_path . "/*Controller.php");
            foreach ($file_list as $file) {
                $filename = str_replace('.php', '', basename($file));
                $className = Yii::$app->getModule($key)->controllerNamespace . '\\' . $filename;
                $contollerName = str_replace('Controller', '', $filename);
                $contollerName = strtolower(preg_replace('/([A-Z])/u', '-$1', lcfirst($contollerName)));
                $controller = new $className($contollerName, Yii::$app->getModule($key));
                $controllerText = file_get_contents($file);
                $controllerActions = [];
                $controllerActionsDirty = [];
                preg_match_all('/public\s*function\s*action([A-Z]+[a-zA-Z0-9]*)+\(([^\)]*)\)+/u', $controllerText, $controllerActionsDirty);
                if ($controllerActionsDirty && isset($controllerActionsDirty[1])) {
                    foreach ($controllerActionsDirty[1] as $k => $action_name_dirty) {
                        $action_name = lcfirst($action_name_dirty);
                        $action_name = strtolower(preg_replace('/([A-Z])/u', '-$1', $action_name));
                        $params = [];
                        if (isset($controllerActionsDirty[2])) {
                            $params_dirty = str_replace(' ', '', $controllerActionsDirty[2][$k]);
                            $params_dirty = str_replace("'", '', $params_dirty);
                            $params_dirty = str_replace('"', '', $params_dirty);
                            $params_dirty = preg_replace('/([a-z]{1}),/ui', '$1=,', $params_dirty);
                            $params = explode(',', str_replace('$', '', $params_dirty));
                        }
                        $action_desc_name = $action_name;
                        $action_desc_params = implode('&', $params);

                        if (isset($all_desc[$key]) && isset($all_desc[$key]['controllers']) && isset($all_desc[$key]['controllers'][$contollerName])) {
                            if (isset($all_desc[$key]['controllers'][$contollerName]['actions']) && isset($all_desc[$key]['controllers'][$contollerName]['actions'][$action_name])) {
                                $temp = $all_desc[$key]['controllers'][$contollerName]['actions'][$action_name];
                                if (is_array($temp)) {
                                    $action_desc_name = $temp['name'];
                                    if ($temp['params']) {
                                        $action_desc_params = [];
                                        foreach ($temp['params'] as $kk => $vv) {
                                            $action_desc_params[] = "$kk: $vv";
                                        }
                                        $action_desc_params = implode("\n", $action_desc_params);
                                    }
                                } else {
                                    $action_desc_name = $temp;
                                }
                            }
                        }

                        $controllerActions[$action_name] = [
                            'name' => $action_desc_name,
                            'params_desc' => $action_desc_params,
                            'dirty' => $action_name_dirty,
                            'full' => $controllerActionsDirty[0][$k],
                            'params_dirty' => $params_dirty,
                            'params' => implode('&', $params),
                        ];
                    }
                }
                $moduleName = $key;
                $name = $contollerName;
                if (isset($all_desc[$key])) {
                    $moduleName = $all_desc[$key]['name'];
                    if (isset($all_desc[$key]['controllers']) && isset($all_desc[$key]['controllers'][$contollerName])) {
                        if (is_array($all_desc[$key]['controllers'][$contollerName])) {
                            $name = $all_desc[$key]['controllers'][$contollerName]['name'];
                        } else
                            $name = $all_desc[$key]['controllers'][$contollerName];
                    }
                }
                $actions[$key]['name'] = $moduleName;
                $actions[$key]['controllers'][$contollerName] = [
                    'name' => $name,
                    'actions' => $controllerActions,
                ];
            }
        }
        Yii::$app->cache->set('system.actions.map', $actions, 60 * 60 * 24 * 30);
        return $actions;
    }

}
