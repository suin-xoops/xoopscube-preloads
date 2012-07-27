<?php

/**
 * This preload injects default values to profile edit form
 */
class ProfileDefaultValueInjector extends XCube_ActionFilter
{
	public function postFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy_Profile.LoadActionForm', array($this, 'inject'), XCUBE_DELEGATE_PRIORITY_FINAL);
	}

	/**
	 * Event listener
	 * @param XCube_ActionForm $actionForm
	 */
	public function inject(XCube_ActionForm $actionForm)
	{
		if ( $this->_profileExists($actionForm->get('uid')) ) {
			return;
		}

		$defaultValues = $this->_getDefaultValues();
		/** @var XCube_AbstractProperty[] $properties  */
		$properties = $actionForm->getFormProperties();

		foreach ( $defaultValues as $name => $value ) {
			if ( isset($properties[$name]) ) {
				$properties[$name]->set($value);
			}
		}
	}

	/**
	 * Return default values
	 * @return array
	 */
	protected function _getDefaultValues()
	{
		// Write default values here!
		return array(
			'birth' => '',
		);
	}

	/**
	 * Return Profile_DataHandler object
	 * @return Profile_DataHandler
	 */
	protected function _getProfileDataHandler()
	{
		return Legacy_Utils::getModuleHandler('data', 'profile');
	}

	/**
	 * Determine if profile data exists
	 * @param int $userId
	 * @return bool
	 */
	protected function _profileExists($userId)
	{
		$profileHandler = $this->_getProfileDataHandler();
		$total = $profileHandler->getCount(new CriteriaCompo(new Criteria('uid', $userId)));
		return ( $total > 0 );
	}
}
