<?php

class RedirectToXsnsFromUserInfo extends XCube_ActionFilter
{
	public function postFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacypage.Userinfo.Access', array($this, 'redirect'), XCUBE_DELEGATE_PRIORITY_FIRST);
	}

	public function redirect()
	{
		$userId = $this->mRoot->mContext->mRequest->getRequest('uid');

		if ( $userId < 1 ) {
			return;
		}

		$url = sprintf('%s/xsns/?p=mypage&uid=%u', XOOPS_MODULE_URL, $userId);
		$this->mRoot->mController->executeForward($url);
	}
}