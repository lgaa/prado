<?php
/**
 * TAuthorizationRule, TAuthorizationRuleCollection class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package System.Security
 */
/**
 * TAuthorizationRule class
 *
 * TAuthorizationRule represents a single authorization rule.
 * A rule is specified by an action (required), a list of users (optional),
 * a list of roles (optional), and a verb (optional).
 * Action can be either 'allow' or 'deny'.
 * Guest (anonymous, unauthenticated) users are represented by question mark '?'.
 * All users (including guest users) are represented by asterisk '*'.
 * Users/roles are case-insensitive.
 * Different users/roles are separated by comma ','.
 * Verb can be either 'get' or 'post'. If it is absent, it means both.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package System.Security
 * @since 3.0
 */
class TAuthorizationRule extends TComponent
{
	/**
	 * @var string action, either 'allow' or 'deny'
	 */
	private $_action;
	/**
	 * @var array list of user IDs
	 */
	private $_users;
	/**
	 * @var array list of roles
	 */
	private $_roles;
	/**
	 * @var string verb, may be empty, 'get', or 'post'.
	 */
	private $_verb;
	/**
	 * @var boolean if this rule applies to everyone
	 */
	private $_everyone;
	/**
	 * @var boolean if this rule applies to guest user
	 */
	private $_guest;

	/**
	 * Constructor.
	 * @param string action, either 'deny' or 'allow'
	 * @param string a comma separated user list
	 * @param string a comma separated role list
	 * @param string verb, can be empty, 'get', or 'post'
	 */
	public function __construct($action,$users,$roles,$verb='')
	{
		$action=strtolower(trim($action));
		if($action==='allow' || $action==='deny')
			$this->_action=$action;
		else
			throw new TInvalidDataValueException('authorizationrule_action_invalid',$action);
		$this->_users=array();
		$this->_roles=array();
		$this->_everyone=false;
		$this->_guest=false;
		foreach(explode(',',$users) as $user)
		{
			if(($user=trim(strtolower($user)))!=='')
			{
				if($user==='*')
				{
					$this->_everyone=true;
					break;
				}
				else if($user==='?')
					$this->_guest=true;
				else
					$this->_users[]=$user;
			}
		}
		foreach(explode(',',$roles) as $role)
		{
			if(($role=trim(strtolower($role)))!=='')
				$this->_roles[]=$role;
		}
		$verb=trim(strtolower($verb));
		if($verb==='' || $verb==='get' || $verb==='post')
			$this->_verb=$verb;
		else
			throw new TInvalidDataValueException('authorizationrule_verb_invalid',$verb);
	}

	/**
	 * @return string action, either 'allow' or 'deny'
	 */
	public function getAction()
	{
		return $this->_action;
	}

	/**
	 * @return array list of user IDs
	 */
	public function getUsers()
	{
		return $this->_users;
	}

	/**
	 * @return array list of roles
	 */
	public function getRoles()
	{
		return $this->_roles;
	}

	/**
	 * @return string verb, may be empty, 'get', or 'post'.
	 */
	public function getVerb()
	{
		return $this->_verb;
	}

	/**
	 * @return boolean if this rule applies to everyone
	 */
	public function getGuestApplied()
	{
		return $this->_guest;
	}

	/**
	 * @return boolean if this rule applies to everyone
	 */
	public function getEveryoneApplied()
	{
		return $this->_everyone;
	}

	/**
	 * @return integer 1 if the user is allowed, -1 if the user is denied, 0 if the rule does not apply to the user
	 */
	public function isUserAllowed(IUser $user,$verb)
	{
		$decision=($this->_action==='allow')?1:-1;
		if($this->_verb==='' || strcasecmp($verb,$this->_verb)===0)
		{
			if($this->_everyone || ($this->_guest && $user->getIsGuest()))
				return $decision;
			if(in_array(strtolower($user->getName()),$this->_users))
				return $decision;
			foreach($this->_roles as $role)
				if($user->isInRole($role))
					return $decision;
		}
		return 0;
	}
}


/**
 * TAuthorizationRuleCollection class.
 * TAuthorizationRuleCollection represents a collection of authorization rules {@link TAuthorizationRule}.
 * To check if a user is allowed, call {@link isUserAllowed}.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package System.Security
 * @since 3.0
 */
class TAuthorizationRuleCollection extends TList
{
	/**
	 * @param IUser the user to be authorized
	 * @param string verb, can be empty, 'post' or 'get'.
	 * @return boolean whether the user is allowed
	 */
	public function isUserAllowed($user,$verb)
	{
		if($user instanceof IUser)
		{
			$verb=strtolower(trim($verb));
			foreach($this as $rule)
			{
				if(($decision=$rule->isUserAllowed($user,$verb))!==0)
					return ($decision>0);
			}
			return true;
		}
		else
			return false;
	}

	/**
	 * Inserts an item at the specified position.
	 * This overrides the parent implementation by performing additional
	 * operations for each newly added TAuthorizationRule object.
	 * @param integer the specified position.
	 * @param mixed new item
	 * @throws TInvalidDataTypeException if the item to be inserted is not a TAuthorizationRule object.
	 */
	public function insertAt($index,$item)
	{
		if($item instanceof TAuthorizationRule)
			parent::insertAt($index,$item);
		else
			throw new TInvalidDataTypeException('authorizationrulecollection_authorizationrule_required');
	}
}

?>