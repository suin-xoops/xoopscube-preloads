<?php

/**
 * ユーザ登録時、利用規約を最後までスクロールしないと登録できなくするプリロード
 */
class ScrollToAgreeTOS extends XCube_ActionFilter
{
	/**
	 * Pre-filter
	 */
	public function preFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacypage.Register.Access', array($this, 'registerScrollToAgreeScript'), XCUBE_DELEGATE_PRIORITY_FIRST);
	}

	/**
	 * スクロール同意スクリプトを登録する
	 */
	public function registerScrollToAgreeScript()
	{
		$script = $this->_getJQueryScript();
		$this->_addHeaderScript($script);
	}

	/**
	 * Return Legacy_HeaderScript object
	 * @return Legacy_HeaderScript
	 */
	protected function _getHeaderScript()
	{
		return $this->mRoot->mContext->getAttribute('headerScript');
	}

	/**
	 * Add jQuery script
	 * @param string $jQuery
	 */
	protected function _addHeaderScript($jQuery)
	{
		$headerScript = $this->_getHeaderScript();
		$headerScript->addScript($jQuery, false);
	}

	/**
	 * Return jQuery script
	 * @return string
	 */
	protected function _getJQueryScript()
	{
		ob_start();
		?>
		jQuery(function($){
			var TEXTAREA = 'textarea[name=disclaimer]';
			var CHECK_BOX = 'input[type=checkbox][name=agree]';
			var SUBMIT_BUTTON = 'input[type=submit]';

			if ( $(TEXTAREA).length == 0 ) {
				return;
			}

			if ( $(CHECK_BOX).length == 0 ) {
				return;
			}

			var submitButton = $(TEXTAREA).parents('form').find(SUBMIT_BUTTON);
			var submitButtonValue = $(submitButton).val();

			$(CHECK_BOX).parent().hide();
			$('<div />').text('最後までスクロールしてお読みいただくと「' + submitButtonValue + '」ボタンが押せるようになります。').insertAfter(TEXTAREA);

			if ( $(CHECK_BOX).is(':checked') == false ) {
				$(submitButton).attr('disabled', 'disabled');
			}

			$(TEXTAREA).bind('scroll', function(){
				if ( $(this).prop('scrollTop') + $(this).prop('offsetHeight') > $(this).prop('scrollHeight') ) {
					$(CHECK_BOX).attr('checked', 'checked');
					$(submitButton).removeAttr('disabled');
				}
			});
		});
		<?php
		return ob_get_clean();
	}
}
