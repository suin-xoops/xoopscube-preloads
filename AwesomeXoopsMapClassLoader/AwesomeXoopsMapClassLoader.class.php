<?php

/**
 * This preload class provides class auto-loading feature.
 */
class AwesomeXoopsMapClassLoader extends XCube_ActionFilter
{
	/** @var string[] Class map */
	protected $classMap = array();

	/**
	 * Return new XoopsClassLoader object
	 * @param XCube_Controller $controller
	 */
	public function __construct($controller)
	{
		parent::XCube_ActionFilter($controller);
		$this->classMap = $this->_getClassMap();
	}

	/**
	 * Register class loader
	 */
	public function preFilter()
	{
		spl_autoload_register(array($this, 'loadClass'));
	}

	/**
	 * Load class
	 * @param string $className
	 */
	public function loadClass($className)
	{
		if ( class_exists($className, false) === true ) {
			return;
		}

		if ( interface_exists($className, false) === true ) {
			return;
		}

		if ( function_exists('trait_exists') === true and trait_exists($className, false) === true ) {
			return;
		}

		if ( array_key_exists($className, $this->classMap) === false ) {
			return;
		}

		include_once $this->classMap[$className];
	}

	/**
	 * Return all class map
	 * @return string[]
	 */
	protected function _getClassMap()
	{
		return $this->_getXCubeClassMap()
			+ $this->_getLegacyClassMap();
	}

	/**
	 * Returns XCube class map
	 * @return string[]
	 */
	protected function _getXCubeClassMap()
	{
		return array(
			'XCube_IniHandler'                 => XOOPS_ROOT_PATH.'/core/libs/IniHandler.class.php',
			'XCube_ActionFilter'               => XOOPS_ROOT_PATH.'/core/XCube_ActionFilter.class.php',
			'XCube_ActionForm'                 => XOOPS_ROOT_PATH.'/core/XCube_ActionForm.class.php',
			'XCube_FieldProperty'              => XOOPS_ROOT_PATH.'/core/XCube_ActionForm.class.php',
			'XCube_DependClassFactory'         => XOOPS_ROOT_PATH.'/core/XCube_ActionForm.class.php',
			'XCube_Controller'                 => XOOPS_ROOT_PATH.'/core/XCube_Controller.class.php',
			'XCube_Ref'                        => XOOPS_ROOT_PATH.'/core/XCube_Delegate.class.php',
			'XCube_Delegate'                   => XOOPS_ROOT_PATH.'/core/XCube_Delegate.class.php',
			'XCube_DelegateManager'            => XOOPS_ROOT_PATH.'/core/XCube_Delegate.class.php',
			'XCube_DelegateUtils'              => XOOPS_ROOT_PATH.'/core/XCube_Delegate.class.php',
			'XCube_FormFile'                   => XOOPS_ROOT_PATH.'/core/XCube_FormFile.class.php',
			'XCube_FormImageFile'              => XOOPS_ROOT_PATH.'/core/XCube_FormFile.class.php',
			'XCube_HttpContext'                => XOOPS_ROOT_PATH.'/core/XCube_HttpContext.class.php',
			'XCube_AbstractRequest'            => XOOPS_ROOT_PATH.'/core/XCube_HttpContext.class.php',
			'XCube_HttpRequest'                => XOOPS_ROOT_PATH.'/core/XCube_HttpContext.class.php',
			'XCube_GenericRequest'             => XOOPS_ROOT_PATH.'/core/XCube_HttpContext.class.php',
			'XCube_Identity'                   => XOOPS_ROOT_PATH.'/core/XCube_Identity.class.php',
			'XCube_Principal'                  => XOOPS_ROOT_PATH.'/core/XCube_Identity.class.php',
			'XCube_LanguageManager'            => XOOPS_ROOT_PATH.'/core/XCube_LanguageManager.class.php',
			'XCube_Object'                     => XOOPS_ROOT_PATH.'/core/XCube_Object.class.php',
			'XCube_ObjectArray'                => XOOPS_ROOT_PATH.'/core/XCube_Object.class.php',
			'XCube_PageNavigator'              => XOOPS_ROOT_PATH.'/core/XCube_PageNavigator.class.php',
			'XCube_Permissions'                => XOOPS_ROOT_PATH.'/core/XCube_Permission.class.php',
			'XCube_AbstractPermissionProvider' => XOOPS_ROOT_PATH.'/core/XCube_Permission.class.php',
			'XCube_PropertyInterface'          => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_AbstractProperty'           => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_GenericArrayProperty'       => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_AbstractArrayProperty'      => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_BoolProperty'               => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_BoolArrayProperty'          => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_IntProperty'                => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_IntArrayProperty'           => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_FloatProperty'              => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_FloatArrayProperty'         => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_StringProperty'             => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_StringArrayProperty'        => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_TextProperty'               => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_TextArrayProperty'          => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_FileProperty'               => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_FileArrayProperty'          => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_ImageFileProperty'          => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_ImageFileArrayProperty'     => XOOPS_ROOT_PATH.'/core/XCube_Property.class.php',
			'XCube_RenderCache'                => XOOPS_ROOT_PATH.'/core/XCube_RenderCache.class.php',
			'XCube_RenderTarget'               => XOOPS_ROOT_PATH.'/core/XCube_RenderSystem.class.php',
			'XCube_RenderSystem'               => XOOPS_ROOT_PATH.'/core/XCube_RenderSystem.class.php',
			'XCube_RoleManager'                => XOOPS_ROOT_PATH.'/core/XCube_RoleManager.class.php',
			'XCube_Role'                       => XOOPS_ROOT_PATH.'/core/XCube_RoleManager.class.php',
			'XCube_Root'                       => XOOPS_ROOT_PATH.'/core/XCube_Root.class.php',
			'XCube_Service'                    => XOOPS_ROOT_PATH.'/core/XCube_Service.class.php',
			'XCube_AbstractServiceClient'      => XOOPS_ROOT_PATH.'/core/XCube_Service.class.php',
			'XCube_ServiceClient'              => XOOPS_ROOT_PATH.'/core/XCube_Service.class.php',
			'XCube_ServiceUtils'               => XOOPS_ROOT_PATH.'/core/XCube_ServiceManager.class.php',
			'XCube_ServiceManager'             => XOOPS_ROOT_PATH.'/core/XCube_ServiceManager.class.php',
			'XCube_Session'                    => XOOPS_ROOT_PATH.'/core/XCube_Session.class.php',
			'XCube_TextFilter'                 => XOOPS_ROOT_PATH.'/core/XCube_TextFilter.class.php',
			'XCube_Theme'                      => XOOPS_ROOT_PATH.'/core/XCube_Theme.class.php',
			'XCube_Utils'                      => XOOPS_ROOT_PATH.'/core/XCube_Utils.class.php',
			'XCube_Validator'                  => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_RequiredValidator'          => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_MinlengthValidator'         => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_MaxlengthValidator'         => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_MinValidator'               => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_MaxValidator'               => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_IntRangeValidator'          => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_EmailValidator'             => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_MaskValidator'              => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_ExtensionValidator'         => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
			'XCube_MaxfilesizeValidator'       => XOOPS_ROOT_PATH.'/core/XCube_Validator.class.php',
		);
	}

	/**
	 * Returns legacy class map
	 * @return string[]
	 */
	protected function _getLegacyClassMap()
	{
		return array(
			'Legacy_iActivityClientDelegate' => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/ActivityClientDelegateInterface.class.php',
			'Legacy_iActivityDelegate'       => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/ActivityDelegateInterface.class.php',
			'Legacy_iCategoryClientDelegate' => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/CatClientDelegateInterface.class.php',
			'Legacy_iCategoryDelegate'       => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/CategoryDelegateInterface.class.php',
			'Legacy_iCommentClientDelegate'  => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/CommentClientDelegateInterface.class.php',
			'Legacy_iCommentDelegate'        => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/CommentDelegateInterface.class.php',
			'Legacy_iGroupClientDelegate'    => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/GroupClientDelegateInterface.class.php',
			'Legacy_iGroupDelegate'          => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/GroupDelegateInterface.class.php',
			'Legacy_iImageClientDelegate'    => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/ImageClientDelegateInterface.class.php',
			'Legacy_iImageDelegate'          => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/ImageDelegateInterface.class.php',
			'Legacy_iTagClientDelegate'      => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/TagClientDelegateInterface.class.php',
			'Legacy_iTagDelegate'            => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/TagDelegateInterface.class.php',
			'Legacy_iWorkflowClientDelegate' => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/WorkflowClientDelegateInterface.class.php',
			'Legacy_iWorkflowDelegate'       => XOOPS_ROOT_PATH.'/modules/legacy/class/interface/WorkflowDelegateInterface.class.php',
		);
	}
}
