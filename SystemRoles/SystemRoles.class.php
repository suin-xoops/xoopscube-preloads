<?php

/**
 * SystemRole class
 */
class SystemRoles extends XCube_ActionFilter
{
	protected $rolesForXoopsTpl = null;

	public function preFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy_Controller.SetupUser', array($this, 'addCustomRoles'));
		$this->mRoot->mDelegateManager->add('Legacy_RenderSystem.BeginRender', array($this, 'assignRoleVariables'));
	}

	/**
	 * Add custom roles
	 * @param Legacy_GenericPrincipal $principal
	 * @param XCube_Controller $controller
	 * @param Legacy_HttpContext $context
	 * @return void
	 */
	public function addCustomRoles(Legacy_GenericPrincipal $principal, XCube_Controller $controller, Legacy_HttpContext $context)
	{
		if ( $this->_isRegisteredUser() === false ) {
			return;
		}

		$this->_importConfigFile(new SystemRoles_User($context->mXoopsUser, $principal));
	}

	/**
	 * Assign role variables
	 * @param XoopsTpl $xoopsTpl
	 */
	public function assignRoleVariables(XoopsTpl $xoopsTpl)
	{
		$xoopsTpl->assign($this->_getRolesForXoopsTpl());
	}

	/**
	 * Determine if registered user is
	 * @return bool
	 */
	protected function _isRegisteredUser()
	{
		return $this->mRoot->mContext->mUser->isInRole('Site.RegisteredUser');
	}

	/**
	 * Import configuration from the file
	 * @param SystemRoles_User $user
	 * @return void
	 */
	protected function _importConfigFile(SystemRoles_User $user)
	{
		$candidates = array(
			XOOPS_ROOT_PATH . '/settings/roles.php',
			XOOPS_TRUST_PATH . '/settings/roles.php',
		);

		foreach ( $candidates as $candidate ) {
			if ( file_exists($candidate) ) {
				$this->_importSafely($user, $candidate);
				return;
			}
		}

		// TODO >> legacy must support Exceptions
		echo new RuntimeException(sprintf('Config file not found. You have to make "%s"', implode('" or "', $candidates)));
	}

	/**
	 * Import PHP file in safe scope
	 * @param SystemRoles_User $user
	 * @param string $file
	 */
	protected function _importSafely(SystemRoles_User $user, $file)
	{
		$sandboxFunction = create_function('$user,$file', 'require $file;');
		$sandboxFunction($user, $file);
	}

	/**
	 * Return role data for XoopsTpl object
	 * @return bool[]
	 */
	protected function _getRolesForXoopsTpl()
	{
		if ( $this->rolesForXoopsTpl === null ) {
			$this->rolesForXoopsTpl = array();
			$roles = $this->mRoot->mContext->mUser->_mRoles; // TODO >> avoid to access private variable

			foreach ( $roles as $role ) {
				$name = 'is_'.$this->_inflectRoleName($role);
				$this->rolesForXoopsTpl[$name] = true;
			}

		}

		return $this->rolesForXoopsTpl;
	}
	
	/**
	 * Inflect role name
	 *
	 * For example:
	 * "Site.GuestUser" becomes "site_guest_user"
	 * "Module.foo.Admin" becomes "module_foo_admin"
	 * "Module.foo_bar.Admin" becomes "module_foo_bar_admin"
	 *
	 * @param $string
	 * @return string
	 */
	protected function _inflectRoleName($string)
	{
		$string = strtr($string, '.', '_');
		$string = preg_replace('/([A-Z])/', '_$1', $string);
		$string = strtolower($string);
		$string = ltrim($string, '_');
		$string = preg_replace('/_{2,}/', '_', $string);
		return $string;
	}
}

/**
 * SystemRoles_User class
 */
class SystemRoles_User
{
	/** @var XoopsUser */
	protected $xoopsUser;
	/** @var Legacy_GenericPrincipal */
	protected $principal;

	/**
	 * Return new Suin_UserRole object
	 * @param XoopsUser $xoopsUser
	 * @param Legacy_GenericPrincipal $principal
	 */
	public function __construct(XoopsUser $xoopsUser, Legacy_GenericPrincipal $principal)
	{
		$this->xoopsUser = $xoopsUser;
		$this->principal = $principal;
	}

	/**
	 * Determine if user belongs to the group(s)
	 * @param int|int[] $groupId
	 * @return bool
	 */
	public function belongsTo($groupId)
	{
		if ( is_array($groupId) === true ) {
			return $this->_belongsToGroups($groupId);
		}

		return $this->_belongsToGroup($groupId);
	}

	/**
	 * Determine if user has the role
	 * @param string $roleName
	 * @return bool
	 */
	public function hasRole($roleName)
	{
		$this->_loadModuleRole($roleName);
		return $this->principal->isInRole($roleName);
	}

	/**
	 * Give a role to this user
	 * @param string $roleName
	 */
	public function playsRole($roleName)
	{
		$this->principal->addRole($roleName);
	}

	/**
	 * Determine if user belongs to the group
	 * @param int $groupId
	 * @return bool
	 */
	protected function _belongsToGroup($groupId)
	{
		$groups = $this->xoopsUser->getGroups();
		return in_array($groupId, $groups);
	}

	/**
	 * Determine if user belongs to the groups
	 * @param int[] $groupIds
	 * @return bool
	 */
	protected function _belongsToGroups(array $groupIds)
	{
		foreach ( $groupIds as $groupId ) {
			if ( $this->_belongsToGroup($groupId) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Load module roles
	 * @param string $roleName
	 * @return void
	 */
	protected function _loadModuleRole($roleName)
	{
		if ( ! preg_match('/^Module\.(?P<dirname>[^.]+)\.(?:Admin|Visitor)$/', $roleName, $matches) ) {
			return;
		}

		$roleManager = new Legacy_RoleManager();
		$roleManager->loadRolesByDirname($matches['dirname']);
	}
}