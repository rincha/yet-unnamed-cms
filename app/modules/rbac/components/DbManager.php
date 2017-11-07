<?php
namespace app\modules\rbac\components;
use Yii;
use yii\db\Query;
use yii\rbac\Assignment;
/**
 * Description of DbManager
 *
 * @author rincha
 */
class DbManager extends \yii\rbac\DbManager {

    private $loaded_assigments;


    public $defaultRolesConfig=[
        'Authenticated'=>[
            'rule_name'=>'ItemDataEvalRule',
            'data'=>'return !\Yii::$app->user->isGuest;',
        ],
    ];

    public function checkAccess($userId, $permissionName, $params = [])
    {
        Yii::trace('check permissin '.$permissionName, 'rbac');
        if (parent::checkAccess($userId, 'root', $params)) {
            Yii::trace('allow by root: true', 'rbac');
            return true;
        }
        $permissionParts=  explode('.', $permissionName);
        if (count($permissionParts)>1) {
            $permissionsNames=[];
            $prefix=null;
            foreach ($permissionParts as $part) {
                if ($prefix!==null) {
                    $permissionsNames[]=$prefix.'.'.$part.'.*';
                    $prefix=$prefix.'.'.$part;
                }
                else {
                    $permissionsNames[]=$part.'.*';
                    $prefix=$part;
                }
            }
            unset($permissionsNames[count($permissionsNames)-1]);
            foreach ($permissionsNames as $permissionNameParent)  {
                if (parent::checkAccess($userId, $permissionNameParent, $params)) {
                    Yii::trace('allow by '.$permissionNameParent.': true', 'rbac');
                    return true;
                }
                else {
                    Yii::trace('allow by '.$permissionNameParent.': false', 'rbac');
                }
            }
        }
        return parent::checkAccess($userId, $permissionName, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        if (empty($userId)) {
            return [];
        }

        if (!$this->loaded_assigments || !isset($this->loaded_assigments[$userId])) {
            $query = (new Query)
                ->from($this->assignmentTable)
                ->where(['user_id' => (string) $userId]);

            $assignments = [];
            foreach ($query->all($this->db) as $row) {
                $assignments[$row['item_name']] = new Assignment([
                    'userId' => $row['user_id'],
                    'roleName' => $row['item_name'],
                    'createdAt' => $row['created_at'],
                ]);
            }
            $this->loaded_assigments[$userId]=$assignments;
        }

        return $this->loaded_assigments[$userId];
    }
}
