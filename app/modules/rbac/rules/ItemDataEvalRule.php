<?php
namespace app\modules\rbac\rules;
 
use yii\rbac\Rule;
use yii\rbac\Item;
 
class ItemDataEvalRule extends Rule
{
    public $name = 'ItemDataEvalRule';
 
    /**
     * @param string|integer $user   the user ID.
     * @param Item           $item   the role or permission that this rule is associated with
     * @param array          $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {		
		$dbitem=\app\modules\rbac\models\AuthItem::findOne($item->name); 
		$res=eval($dbitem->data);
		if ($res) {
			\Yii::trace('Allow from ItemDataEvalRule By AuthItem ('.$item->name.') with item data ('.$dbitem->data.')');
		}
		else {
			\Yii::trace('Disallow from ItemDataEvalRule By AuthItem ('.$item->name.') with item data ('.$dbitem->data.')');
		}
		return $res;
    }
}
?>