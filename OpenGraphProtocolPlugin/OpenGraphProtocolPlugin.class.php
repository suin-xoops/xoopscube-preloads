<?php

/**
 * Enables Open Graph Protocol in XOOPS Cube Legacy themes
 */
class OpenGraphProtocolPlugin extends XCube_ActionFilter
{
	public function postFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy_RenderSystem.BeginRender', array($this, 'addOGPTagsToTheme'));
		$this->mRoot->mDelegateManager->add('OGP.SetUp', array($this, 'setUpDefault'), XCUBE_DELEGATE_PRIORITY_FINAL);
	}

	/**
	 * Add OGP tags to theme
	 * @param Legacy_XoopsTpl $xoopsTpl
	 * @return void
	 */
	public function addOGPTagsToTheme(Legacy_XoopsTpl $xoopsTpl)
	{
		if ( $this->_isBeginRenderTheme() === false ) {
			return;
		}

		$ogpData = array();
		XCube_DelegateUtils::raiseEvent('OGP.SetUp', new XCube_Ref($ogpData), new XCube_Ref($xoopsTpl));
		$ogpTags = $this->_renderOGPTags($ogpData);
		$this->_prependToXoopsModuleHeader($xoopsTpl, $ogpTags);
	}

	/**
	 * Default OGP set up
	 * @param array $ogpData
	 * @param Legacy_XoopsTpl $xoopsTpl
	 */
	public function setUpDefault(array &$ogpData, Legacy_XoopsTpl $xoopsTpl)
	{
		$ogpData = array_merge($this->_getOGPDataFromXoopsTpl($xoopsTpl), $ogpData);
	}

	/**
	 * Determine if rendering theme has been begin
	 * @return bool
	 */
	protected function _isBeginRenderTheme()
	{
		$steps = debug_backtrace();

		foreach ( $steps as $step ) {
			if ( isset($step['function']) === true and $step['function'] === 'renderTheme' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Render OGP tags with XoopsTpl object
	 * @param XoopsTpl $xoopsTpl
	 * @return string
	 */
	protected function _getOGPDataFromXoopsTpl(XoopsTpl $xoopsTpl)
	{
		return array(
			'title'       => $xoopsTpl->_tpl_vars['xoops_pagetitle'],
			'url'         => $this->_getCurrentURL(),
			'image'       => XOOPS_URL.'/images/logo.gif',
			'site_name'   => $xoopsTpl->_tpl_vars['xoops_sitename'],
			'description' => $xoopsTpl->_tpl_vars['xoops_meta_description'],
		);
	}

	/**
	 * Render OGP meta tags
	 * @param array $data
	 * @return string
	 */
	protected function _renderOGPTags(array $data)
	{
		foreach ( $data as $name => $value ) {
			$data[$name] = htmlspecialchars($value, ENT_QUOTES, _CHARSET);
		}

		ob_start();
		?><meta property="og:type" content="website">
<meta property="og:title" content="<?php echo $data['title'] ?>">
<meta property="og:url" content="<?php echo $data['url'] ?>">
<meta property="og:image" content="<?php echo $data['image'] ?>">
<meta property="og:site_name" content="<?php echo $data['site_name'] ?>">
<meta property="og:description" content="<?php echo $data['description'] ?>">
<?php
		return ob_get_clean();
	}

	/**
	 * Prepend HTML to xoops_module_header
	 * @param Legacy_XoopsTpl $xoopsTpl
	 * @param string $html
	 */
	protected function _prependToXoopsModuleHeader(Legacy_XoopsTpl $xoopsTpl, $html)
	{
		$xoopsTpl->assign('xoops_module_header',$html.$xoopsTpl->get_template_vars('xoops_module_header'));
	}

	/**
	 * Return current URL
	 * @return string
	 */
	protected function _getCurrentURL()
	{
		if ( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on' ) {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}

		return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
}