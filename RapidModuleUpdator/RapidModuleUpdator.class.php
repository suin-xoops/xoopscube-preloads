<?php
/**
 * A simple description for this script
 *
 * PHP Version 5.2.0 or Upper version
 *
 * @package    RapidModuleUpdator
 * @author     Hidehito NOZAWA aka Suin <http://ryus.co.jp>
 * @copyright  2010 Hidehito NOZAWA
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 */

class RapidModuleUpdator extends XCube_ActionFilter
{
	protected $blockInstance = null;

	public function preFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy_ActionFrame.CreateAction', array($this, 'onCreateAction'));
		$this->mRoot->mDelegateManager->add('Legacy_AdminControllerStrategy.SetupBlock', array($this, 'onSetupBlock'));
	}

	public function onCreateAction($action)
	{
		if ( $action->mActionName !== 'ModuleList' )
		{
			return;
		}
	}

	public function onSetupBlock($controller)
	{
		$this->blockInstance = new RapidModuleUpdator_Block();
		$this->mController->_mBlockChain[] =& $this->blockInstance;
	}
}

class RapidModuleUpdator_Block extends Legacy_AbstractBlockProcedure
{
	function getName()
	{
		return "RapidModuleUpdator_Block";
	}

	function getTitle()
	{
		return "RapidModuleUpdator_Block";
	}

	function getEntryIndex()
	{
		return 0;
	}

	function isEnableCache()
	{
		return false;
	}

	function execute()
	{
		ob_start();
?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript"><!--
google.load("language", "1"); 
google.load("jquery", "1");
//-->
</script>
<script language="javascript">
jQuery(function($){

	var moduleListForm = '#contentBody form';
	var moduleListRow = '#contentBody form table tr';
	var moduleListFoot = '#contentBody form td.foot';
	var rapidModuleUpdateButton = "#rapidModuleUpdateButton";
	
	var updatingModuleTotal = 0;
	var updatedModuleTotal  = 0;
	var updatingModules = [];
	var updatingModule  = null;
	var isUpdating = false;

	var main = function()
	{
		if ( location.href.search('ModuleList') == -1 && location.href.search(/legacy\/admin\/index.php$/) == -1 )
		{
			return;
		}

		if ( $('#legacy_xoopsform_confirm').length > 0 )
		{
			return;
		}

		addColumns();
		addDelegates();
	}

	var addColumns = function()
	{
		$(moduleListRow+':first').append('<th>一括アップデート<br /><input type="checkbox" id="rapidUpdateCheckboxAll" /></th>');
		$(moduleListRow+':last').append('<td class="foot"><input type="button" value="一括アップデート" class="formButton" id="rapidModuleUpdateButton" /></td>');

		$(moduleListRow).each(function(){
			var isOddOrEven = ( $(this).hasClass('odd') || $(this).hasClass('even') );

			if ( isOddOrEven == false )
			{
				return;
			}

			$(this).append('<td class="legacy_list_select"><input type="checkbox" class="rapidUpdateCheckbox" /></td>');
		});
	}

	var addDelegates = function()
	{
		$('body').delegate('#rapidModuleUpdateButton', 'click', clickRapidModuleUpdateButton)
		         .delegate('#rapidUpdateCheckboxAll', 'click', checkAll);
	}

	var clickRapidModuleUpdateButton = function()
	{
		updatingModuleTotal = $('.rapidUpdateCheckbox:checked').length;
		updatedModuleTotal  = 0;

		if ( updatingModuleTotal < 1 )
		{
			return;
		}

		$(this).replaceWith('<span id="rapidUpdatorStatus">(<span class="total">'+updatedModuleTotal+'</span>/'+updatingModuleTotal+')アップデート中</span>');

		$('.rapidUpdateCheckbox:checked').each(function()
		{
			var href = $(this).parent('td').parent('tr').find('a[href*=ModuleUpdate]').attr('href');
			var td = $(this).parent('td');
			updatingModules.push({'href':href, 'td':td, 'status':0});
		});

		$(updatingModules).each(function()
		{
			this.td.html("待機中");
		});

		doUpdate();
	}

	var doUpdate = function()
	{
		$(updatingModules).each(function()
		{
			if ( this.status == 1 || isUpdating == true )
			{
				return;
			}

			updatingModule = this;
			isUpdating     = true;

			updatingModule.td.html("アップデート中");

			try
			{
				$.ajax({
					type: 'GET',
					url: updatingModule.href,
					success: getConfirmFormSuccess,
					error: ajaxFailed
				});
			}
			catch ( e )
			{
				updatingModule.td.html('<span style="color:red;">エラー</span>');
				updatedModuleTotal = updateUpdateStatus(updatedModuleTotal, updatingModuleTotal);
				updateModuleStatus();
			}
		});
	}

	var getConfirmFormSuccess = function(html)
	{
		var form = $(html).find('#contentBody form');
		var formdata = form.serialize();

		$.ajax({
			type: 'POST',
			url: updatingModule.href,
			data: formdata,
			success: postFormSuccess,
			error: ajaxFailed
		});
	}

	var postFormSuccess = function(html)
	{
		var result = $(html).find('li.legacy_module_message:last').text();
		updatingModule.td.hide().html('<span style="color:green;">完了</span>').fadeIn();
		updatedModuleTotal = updateUpdateStatus(updatedModuleTotal, updatingModuleTotal);
		updateModuleStatus();
	}

	var ajaxFailed = function(XMLHttpRequest, textStatus, errorThrown)
	{
		throw "Ajaxエラー";
	}

	var updateUpdateStatus = function(updatedModuleTotal, updatingModuleTotal)
	{
		updatedModuleTotal += 1;

		if ( updatedModuleTotal == updatingModuleTotal )
		{
			$('#rapidUpdatorStatus').text("完了")
			return updatedModuleTotal;
		}

		$('#rapidUpdatorStatus .total').text(updatedModuleTotal);
		return updatedModuleTotal;
	}

	var updateModuleStatus = function()
	{
		updatingModule.status = 1;
		isUpdating = false;
		doUpdate(); // 次のモジュールへ
	}

	var checkAll = function()
	{
		var isChecked = $(this).attr('checked');

		$('.rapidUpdateCheckbox').removeAttr('checked');

		if ( isChecked == true )
		{
			$('.rapidUpdateCheckbox').attr('checked', 'checked');
		}
	}

	main();
});
</script>
<?php
		$result = ob_get_clean();

		$render =& $this->getRenderTarget();
		$render->setResult($result);
	}

	function hasResult()
	{
		return true;
	}

	function &getResult()
	{
		$dmy = "dummy";
		return $dmy;
	}

	function getRenderSystemName()
	{
		return 'Legacy_AdminRenderSystem';
	}
}
