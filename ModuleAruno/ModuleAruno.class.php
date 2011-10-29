<?php
/**
 * モジュールあるの？という疑問に答えるプリロード
 *
 * PHP Version 5.2.0 or Upper version
 *
 * @package    ModuleAruno
 * @author     Hidehito NOZAWA aka Suin <http://suin.asia>
 * @copyright  2011 Hidehito NOZAWA
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class ModuleAruno extends XCube_ActionFilter
{
	public function preBlockFilter()
	{
		$dirname = 'pico';
	
		$aruno = $this->_aruno($dirname);

		if ( $aruno === true )
		{
			die('<span style="font-size:200px">有る。</span>');
		}
		else
		{
			die('<span style="font-size:200px">無い。</span>');
		}
	}

	/**
	 * _aruno function.
	 * 
	 * @access protected
	 * @param string $dirname
	 * @return bool
	 */
	protected function _aruno($dirname)
	{
		$handler  = xoops_gethandler('module');
		$module   = $handler->getByDirname($dirname);

		if ( is_object($module) === false )
		{
			return false;
		}

		$isActive = $module->get('isactive');

		return ( $isActive == 1 );
	}
}
