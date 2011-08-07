<?php
/**
 * こういう( http://wex.im/ )スライドショーをXOOPS Cubeで使えるようにするプリロード
 *
 * PHP Version 5.2.4 or Upper version
 *
 * @package    JquerySlideShow
 * @author     Hidehito NOZAWA aka Suin <http://suin.asia/>
 * @copyright  2010 Hidehito NOZAWA
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2 or later
 *
 */

/*
使い方

まず、このファイルを /preload フォルダにアップロードします。
次に、テーマにタグを埋め込みます。埋め込み方は下の説明を御覧ください。

-----------------------------------------------------

[基本編] テーマで使う
画像の大きさを指定して、画像のURLを1行ずつ列挙するだけ

<{slideshow width=680 height=80}>
http://example.com/path/to/image1.png
http://example.com/path/to/image2.png
http://example.com/path/to/image3.png
<{/slideshow}>

-----------------------------------------------------

[応用編] 画像のリンク先を指定する
画像のURLの次に、半角スペースを挟んでリンク先のURLを指定すると、画像にリンクがつきます。

<{slideshow width=680 height=80}>
http://example.com/path/to/image1.png http://example.com/page1.html
http://example.com/path/to/image2.png http://example.com/page2.html
http://example.com/path/to/image3.png http://example.com/page3.html
<{/slideshow}>

-----------------------------------------------------

[応用編] 画像のURLに<{$xoops_url}>を使う
画像のURLに<{$xoops_url}>を使うと、サイトのURLが変わったときに、URLを書き換える必要がないので便利です

<{slideshow width=680 height=80}>
<{$xoops_url}>/uploads/myalbum/1.png <{$xoops_url}>/modules/pico/index.php?content_id=1
<{$xoops_url}>/uploads/myalbum/2.png <{$xoops_url}>/modules/pico/index.php?content_id=2
<{$xoops_url}>/uploads/myalbum/3.png <{$xoops_url}>/modules/pico/index.php?content_id=3
<{/slideshow}>

-----------------------------------------------------

[応用編] カスタムブロックで使う
カスタムブロックの「コンテンツ」に次のようなコードを埋め込みます。
カスタムブロックの「タイプ」は「PHPスクリプト」を指定します。

$params = array(
  'width' => 680, // 幅指定
  'height' => 80, // 縦指定
);
$images ="
http://example.com/path/to/image1.png
http://example.com/path/to/image2.png
http://example.com/path/to/image3.png
";
JquerySlideShow::renderSlideShow($params, $images);


*/

defined('XOOPS_ROOT_PATH') or die;

class JquerySlideShow extends XCube_ActionFilter
{
	protected $javaScriptFile = '/uploads/jquery.slider.min.js';
	protected $styleSheetFile = '/uploads/jquery.slider.css';

	protected static $selectorCount = 0;
	protected static $defaultParams = array(
		'width'        => null,
		'height'       => null,
		'wait'         => null,
		'fade'         => null,
		'direction'    => null,
		'showControls' => null,
		'showProgress' => null,
		'hoverPause'   => null,
		'autoplay'     => null,
	);

	public function preBlockFilter()
	{
		$this->_checkFileExists();
		$this->mRoot->mDelegateManager->add('Legacy_RenderSystem.SetupXoopsTpl', array(&$this, 'hook'));
	}

	public function hook(&$xoopsTpl)
	{
		$moduleHeader  = $xoopsTpl->get_template_vars('xoops_module_header');
		$moduleHeader .= $this->_getHeaderTags();
		$xoopsTpl->assign('xoops_module_header', $moduleHeader);
		$xoopsTpl->register_block('slideshow', array(get_class($this), 'renderSlideShow'));
	}

	/**
	 * スライドショー用のHTMLを描画する関数
	 */
	public static function renderSlideShow($params, $content, &$smarty = null, &$repeat = null)
	{
		$urls   = self::_getUrls($content);
		$params = self::_getParams($params);

		if ( count($urls) === 0 )
		{
			return;
		}

		$options = json_encode((object)$params);
		$options = preg_replace('/"(\w+)":/', '$1:', $options);

		$selector = 'slider'.self::$selectorCount;

		self::_renderImages($selector, $urls);
		self::_renderScript($selector, $options);

		self::$selectorCount += 1;
	}

	protected static function _getUrls($content)
	{
		$content = explode("\n", $content);
		$content = array_map('trim', $content);
		$content = array_diff($content, array(''));

		$urls = array();

		foreach ( $content as $line )
		{
			$line  = str_replace("\t", ' ', $line);
			$parts = explode(' ', $line);
			$parts = array_map('trim', $parts);
			$parts = array_diff($parts, array(''));
			$parts = array_values($parts);

			if ( strpos($line, 'http') === 0 )
			{
				$urls[] = $parts;
			}
		}

		return $urls;
	}

	protected static function _getParams($params)
	{
		$params = array_diff($params, array_diff_key($params, self::$defaultParams));
		$params = array_merge(self::$defaultParams, $params);
		$params = array_diff($params, array(null));
		return $params;
	}

	protected static function _renderImages($selector, $urls)
	{
		$dom  = '<div id="'.$selector.'">';

		foreach ( $urls as $url )
		{
			$link = null;

			if ( count($url) >= 2 )
			{
				list($image, $link) = $url;
				$dom .= '<div class="image"><a href="'.$link.'"><img src="'.$image.'" /></a></div>';
			}
			else
			{
				list($image) = $url;
				$dom .= '<div class="image"><img src="'.$image.'" /></div>';
			}
		}

		$dom .= '</div>';

		echo $dom;
	}

	protected static function _renderScript($selector, $options)
	{
		$script = '<script type="text/javascript">jQuery(function($){  $("#'.$selector.'").slider('.$options.');  });</script>';
		echo $script;
	}

	protected function _checkFileExists()
	{
		$this->_createJavaScripts();
		$this->_createStyleSheets();
	}

	protected function _getHeaderTags()
	{
		$tag  = '<script type="text/javascript" src="'.XOOPS_URL.$this->javaScriptFile.'"></script>'."\n";
		$tag .= '<link rel="stylesheet" type="text/css" media="all" href="'.XOOPS_URL.$this->styleSheetFile.'" />';
		return $tag;
	}

	protected function _createJavaScripts()
	{
		$path = XOOPS_ROOT_PATH.$this->javaScriptFile;

		if ( file_exists($path) === false )
		{
			file_put_contents($path, $this->_getJavaScript());
		}
	}

	protected function _createStyleSheets()
	{
		$path = XOOPS_ROOT_PATH.$this->styleSheetFile;

		if ( file_exists($path) === false )
		{
			file_put_contents($path, $this->_getStyleSheet());
		}
	}

	protected function _getJavaScript()
	{
		ob_start();
		?>
(function($){$.fn.slider=function(options){var $this=this;var settings={'width':this.width(),'height':this.height(),'wait':4000,'fade':750,'direction':'left','showControls':true,'showProgress':true,'hoverPause':true,'autoplay':true,'slidebefore':function(){},'slideafter':function(){},'rewind':function(){}};var _timer=false;var _last=false;var _this=false;var _cycle=function(){clearTimeout(_timer);_last=_this;if(settings.direction=='right'){_this=_this.prev('.jquery-slider-element');}else{_this=_this.next('.jquery-slider-element');}
if(!_this.length){_rewind();}
_draw();if(!$this.hasClass('jquery-slider-paused')&&settings.autoplay){_timer=setTimeout(_cycle,settings.wait);}};var _rewind=function(){if(settings.direction=='right'){_this=$this.children('.jquery-slider-element').last();}else{_this=$this.children('.jquery-slider-element').first();}
settings.rewind(_this,$this);};var _draw=function(){$this.addClass('jquery-slider-sliding');if(settings.showProgress){$this.find('.jquery-slider-page').removeClass('jquery-slider-page-current');$this.find('.jquery-slider-page:nth-child('+(_this.nextAll('.jquery-slider-element').length+1)+')').addClass('jquery-slider-page-current');}
settings.slidebefore(_this,$this);if(settings.direction=='right'){_this.show().css('left',-settings.width);}else{_this.show().css('left',settings.width);}
_this.stop(true,true).animate({'left':(settings.direction=='right'?'+=':'-=')+settings.width+'px'},{'duration':settings.fade,'complete':function(){settings.slideafter(_this,$this);$this.removeClass('jquery-slider-sliding');}});if(_last){_last.stop(true,true).animate({'left':(settings.direction=='right'?'+=':'-=')+settings.width+'px'},{'duration':settings.fade});}};var _next=function(){if($this.hasClass('jquery-slider-sliding'))return;var direction=settings.direction;$this.addClass('jquery-slider-paused');settings.direction='left';_cycle();settings.direction=direction;};var _prev=function(){if($this.hasClass('jquery-slider-sliding'))return;var direction=settings.direction;$this.addClass('jquery-slider-paused');settings.direction='right';_cycle();settings.direction=direction;};var _init=function(){if(options){$.extend(settings,options);}
if(settings.hoverPause){$this.bind({'mouseenter':function(){$this.addClass('jquery-slider-paused')
clearTimeout(_timer);},'mouseleave':function(){$this.removeClass('jquery-slider-paused');if(settings.autoplay){_timer=setTimeout(_cycle,settings.wait);}}});}
var positionEls=$('<span class="jquery-slider-pages"></span>');$this.addClass('jquery-slider').width(settings.width).height(settings.height);$this.children().each(function(){_this=$(this).addClass('jquery-slider-element');positionEls.append('<span class="jquery-slider-page"></span>');});if(settings.showProgress){$this.append(positionEls);}
if(settings.showControls){var controlPrev=$('<span class="jquery-slider-control jquery-slider-control-prev">«</span>').bind('click',function(){_prev();});var controlNext=$('<span class="jquery-slider-control jquery-slider-control-next">»</span>').bind('click',function(){_next();});$this.append(controlPrev);$this.append(controlNext);}
_cycle();};_init();};})(jQuery);
		<?php
		return trim(ob_get_clean());
	}

	protected function _getStyleSheet()
	{
		ob_start();
		?>
		.jquery-slider {
    overflow: hidden;
    position: relative;
}
.jquery-slider-element {
    overflow: hidden;
    display: none;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
}
.jquery-slider-control {
    overflow: hidden;
    position: absolute;
    text-align: center;
    width: 24px;
    height: 24px;
    line-height: 24px;
    font-size: 16px;
    font-weight: bold;
    padding: 0;
    margin: 0;
    border: 1px solid #666;
    background: #fff;
    opacity: 0.33;
    cursor: pointer;
    border-radius: 12px;
    box-shadow: #666 0 0 2px;
    text-shadow: #fff 0 0 1px;
}
.jquery-slider-control:hover {
    opacity: 1;
}
.jquery-slider-control-prev {
    left: 5px;
    top: 5px;
}
.jquery-slider-control-next {
    right: 5px;
    top: 5px;
}
.jquery-slider-pages {
    overflow: hidden;
    position: absolute;
    left: 5px;
    bottom: 5px;
    height: 20px;
    right: 5px;
}
.jquery-slider-page {
    overflow: hidden;
    position: relative;
    display: block;
    float: right;
    width: 12px;
    height: 12px;
    padding: 0;
    margin: 0;
    background: #999;
    opacity: 0.33;
    margin: 3px;
    border-radius: 6px;
    box-shadow: #333 0 0 2px;
}
.jquery-slider-page-current {
    opacity: 1;
}
		<?php
		return trim(ob_get_clean());
	}
}
