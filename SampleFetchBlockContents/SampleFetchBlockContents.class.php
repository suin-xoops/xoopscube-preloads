<?php
/**
 * プリロードで特定のブロックの実行結果をSmartyに割り当てるサンプル
 *
 * PHP Version 5.2.0 or Upper version
 *
 * @package    SampleFetchBlockContents
 * @author     suin <http://ryus.co.jp>
 * @copyright  2010 suin
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

defined('XOOPS_ROOT_PATH') or die;

class SampleFetchBlockContents extends XCube_ActionFilter
{
	protected static $contentsCache = array();

	/**
	 * デリゲートにイベントを登録する
	 */
	public function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy_RenderSystem.SetupXoopsTpl', array($this, 'setupXoopsTpl'));
	}

	/**
	 * Legacy_RenderSystem.SetupXoopsTpl用イベントリスナ
	 */
	public function setupXoopsTpl($xoopsTpl)
	{
		// Smarty変数にブロックの実行結果をassignする
		$name    = 'block_name_here';
		$blockId = 3;
		$xoopsTpl->assign($name, $this->_getBlockContentById($blockId));
	}

	/**
	 * ブロックIDからブロックの実行結果を取得する
	 */
	protected function _getBlockContentById($blockId)
	{
		// 都度DBにアクセスするので、プロセス内でキャッシュを利用する
		if ( array_key_exists($blockId, self::$contentsCache) === false )
		{
			$blockObject    = $this->_getBlockObjectById($blockId);
			$blockProcedure = $this->_getBlockProcedure($blockObject);
			self::$contentsCache[$blockId] = $this->_getExecuteBlockProcedure($blockProcedure);
		}

		return self::$contentsCache[$blockId];
	}

	/**
	 * ブロックIDでブロックオブジェクトを取得する
	 * @param intger $blockId
	 * @returns XoopsBlock
	 */
	protected function _getBlockObjectById($blockId)
	{
		return xoops_gethandler('block')->get($blockId);
	}

	/**
	 * ブロックプロシージャを生成する
	 * @param XoopsBlock $xoopsBlock 
	 * @returns Legacy_BlockProcedure
	 */
	protected function _getBlockProcedure(XoopsBlock $xoopsBlock)
	{
		return Legacy_Utils::createBlockProcedure($xoopsBlock);
	}

	/**
	 * ブロックプロシージャを実行する
	 * @param Legacy_BlockProcedure $blockProcedure
	 * @returns mixed 成功した場合実行結果を配列で、失敗した場合ははFALSE
	 */
	protected function _getExecuteBlockProcedure(Legacy_BlockProcedure $blockProcedure)
	{
		$blockProcedure->execute();

		if ( $blockProcedure->isDisplay() === false )
		{
			return false;
		}

		return array(
			'name'    => $blockProcedure->getName(),
			'title'   => $blockProcedure->getTitle(),
			'content' => $blockProcedure->getRenderTarget()->getResult(),
			'weight'  => $blockProcedure->getWeight(),
			'id'      => $blockProcedure->getId(),
		);
	}
}
