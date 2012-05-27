<?php

/**
 * ログイン後の特定の画面へ飛ばすプリロード
 */
class WhenLoginGoTo extends XCube_ActionFilter
{
	/**
	 * Pre-filter
	 */
	public function preFilter()
	{
		$this->mRoot->mDelegateManager->add('Site.CheckLogin.Success', array($this, 'redirect'), XCUBE_DELEGATE_PRIORITY_FINAL);
	}

	/**
	 * リダイレクトする
	 * @param XoopsUser $xoopsUser
	 */
	public function redirect(XoopsUser $xoopsUser)
	{
		$url = $this->_getRedirectURL();
		$message = XCube_Utils::formatMessage(_MD_LEGACY_MESSAGE_LOGIN_SUCCESS, $xoopsUser->get('uname'));
		$this->mRoot->mController->executeRedirect($url, 1, $message);
	}

	/**
	 * リダイレクト先のURLを返す
	 * @return string
	 */
	protected function _getRedirectURL()
	{
		return XOOPS_URL.'/modules/hoge/';
	}
}
