<?php

class CVE_2012_1823 extends XCube_ActionFilter
{
	public function preFilter()
	{
		if ( $_SERVER['QUERY_STRING'] === '-s' )
		{
			header('Content-type: text/html; charset=utf-8');
			$sourceCode = $this->_getSourceCode();
			highlight_string($sourceCode);
			die;
		}
	}
	
	protected function _getSourceCode()
	{
		return '<?php
require_once("http://example.com/セキュリティエンジニア求人");
';
	}
}