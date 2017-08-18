<?php 
/**
 * 修改基类
 * @author tivon
 * @date 2015-04-27
 */
abstract class CAuthManagerExt extends CApplicationComponent
{
	/**
	 * @var boolean Enable error reporting for bizRules.
	 * @since 1.1.3
	 */
	public $showErrors = false;

	/**
	 * @var array list of role names that are assigned to all users implicitly.
	 * These roles do not need to be explicitly assigned to any user.
	 * When calling {@link checkAccess}, these roles will be checked first.
	 * For performance reason, you should minimize the number of such roles.
	 * A typical usage of such roles is to define an 'authenticated' role and associate
	 * it with a biz rule which checks if the current user is authenticated.
	 * And then declare 'authenticated' in this property so that it can be applied to
	 * every authenticated user.
	 */
	public $defaultRoles=array();

	/**
	 * Creates a role.
	 * This is a shortcut method to {@link IAuthManager::createAuthItem}.
	 * @param string $name the item name
	 * @param string $description the item description.
	 * @param string $bizRule the business rule associated with this item
	 * @param mixed $data additional data to be passed when evaluating the business rule
	 * @return AuthItem the authorization item
	 */
	public function createRole($name,$chinese,$userid,$description='',$bizRule=null,$data=null)
	{
		return $this->createAuthItem($name,$chinese,CAuthItemExt::TYPE_ROLE,$userid,$description,$bizRule,$data);
	}

	/**
	 * Creates a task.
	 * This is a shortcut method to {@link IAuthManager::createAuthItem}.
	 * @param string $name the item name
	 * @param string $description the item description.
	 * @param string $bizRule the business rule associated with this item
	 * @param mixed $data additional data to be passed when evaluating the business rule
	 * @return AuthItem the authorization item
	 */
	public function createTask($name,$chinese,$userid,$description='',$bizRule=null,$data=null)
	{
		return $this->createAuthItem($name,$chinese,CAuthItemExt::TYPE_TASK,$userid,$description,$bizRule,$data);
	}

	/**
	 * Creates an operation.
	 * This is a shortcut method to {@link IAuthManager::createAuthItem}.
	 * @param string $name the item name
	 * @param string $description the item description.
	 * @param string $bizRule the business rule associated with this item
	 * @param mixed $data additional data to be passed when evaluating the business rule
	 * @return AuthItem the authorization item
	 */
	public function createOperation($name,$chinese,$userid,$description='',$bizRule=null,$data=null)
	{
		return $this->createAuthItem($name,$chinese,CAuthItemExt::TYPE_OPERATION,$userid,$description,$bizRule,$data);
	}

	/**
	 * Returns roles.
	 * This is a shortcut method to {@link IAuthManager::getAuthItems}.
	 * @param mixed $userId the user ID. If not null, only the roles directly assigned to the user
	 * will be returned. Otherwise, all roles will be returned.
	 * @return array roles (name=>AuthItem)
	 */
	public function getRoles($userId=null)
	{
		return $this->getAuthItems(CAuthItemExt::TYPE_ROLE,$userId);
	}

	/**
	 * Returns tasks.
	 * This is a shortcut method to {@link IAuthManager::getAuthItems}.
	 * @param mixed $userId the user ID. If not null, only the tasks directly assigned to the user
	 * will be returned. Otherwise, all tasks will be returned.
	 * @return array tasks (name=>AuthItem)
	 */
	public function getTasks($userId=null)
	{
		return $this->getAuthItems(CAuthItemExt::TYPE_TASK,$userId);
	}

	/**
	 * Returns operations.
	 * This is a shortcut method to {@link IAuthManager::getAuthItems}.
	 * @param mixed $userId the user ID. If not null, only the operations directly assigned to the user
	 * will be returned. Otherwise, all operations will be returned.
	 * @return array operations (name=>AuthItem)
	 */
	public function getOperations($userId=null)
	{
		return $this->getAuthItems(CAuthItemExt::TYPE_OPERATION,$userId);
	}

	/**
	 * Executes the specified business rule.
	 * @param string $bizRule the business rule to be executed.
	 * @param array $params parameters passed to {@link IAuthManager::checkAccess}.
	 * @param mixed $data additional data associated with the authorization item or assignment.
	 * @return boolean whether the business rule returns true.
	 * If the business rule is empty, it will still return true.
	 */
	public function executeBizRule($bizRule,$params,$data)
	{
		return $bizRule==='' || $bizRule===null || ($this->showErrors ? eval($bizRule)!=0 : @eval($bizRule)!=0);
	}

	/**
	 * Checks the item types to make sure a child can be added to a parent.
	 * @param integer $parentType parent item type
	 * @param integer $childType child item type
	 * @throws CException if the item cannot be added as a child due to its incompatible type.
	 */
	protected function checkItemChildType($parentType,$childType)
	{
		static $types=array('operation','task','role');
		if($parentType < $childType)
			throw new CException(Yii::t('yii','Cannot add an item of type "{child}" to an item of type "{parent}".',
				array('{child}'=>$types[$childType], '{parent}'=>$types[$parentType])));
	}
}

?>