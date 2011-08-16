<?php
/**
 * A simple description for this script
 *
 * PHP Version 5.2.0 or Upper version
 *
 * @package    SampleGetBlockContent
 * @author     Hidehito NOZAWA aka Suin <http://ryus.co.jp>
 * @copyright  2010 Hidehito NOZAWA
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

defined('XOOPS_ROOT_PATH') or die;

class SampleGetBlockContent extends XCube_ActionFilter
{
	/**
	 * ブロックの表示位置
	 */
	protected static $asides = array(
		'xoops_lblocks', // 左
		'xoops_rblocks', // 右
		'xoops_clblocks', // 中央左
		'xoops_crblocks', // 中央右
		'xoops_ccblocks', // 中央中央
	);

	/**
	 * デリゲートにイベントを登録する
	 */
	public function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy_RenderSystem.BeginRender', array(&$this, 'beginRender'));  
	}

	/**
	 *  ブロックの内容を取得するイベントリスナ
	 */
	public function beginRender(XoopsTpl &$xoopsTpl)
	{
		if ( $this->_isBlockPrepared($xoopsTpl) === false )
		{
			return; // ブロックの準備ができていなければ処理を中断する
		}

		foreach ( self::$asides as $aside )
		{
			// xoopsTplから変数を取得
			$blocks = $xoopsTpl->get_template_vars($aside);

			if ( $blocks === null )
			{
				continue; // ブロックがない場合
			}

			foreach ( $blocks as $k => $block )
			{
				// ここで各ブロックの情報が取得できる
				
				// ブロックのタイトルに語尾をつける例
				$blocks[$k]['title'] = $block['title']."だぷー";
			}

			// xoopsTplの変数を上書き
			$xoopsTpl->assign($aside, $blocks);
		}
	}

	/**
	 * ブロックが準備されているかを返す
	 * @returns bool 準備されていればTRUE, そうでなければFALSE
	 */
	protected function _isBlockPrepared(XoopsTpl $xoopsTpl)
	{
		return ( $xoopsTpl->get_template_vars('xoops_showlblock') !== null );
	}
}
