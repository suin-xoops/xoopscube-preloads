<?php

class TotalSocialButtons extends XCube_ActionFilter
{
	protected $javascripts = array();
	protected $params = array();

	public function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy.Event.Explaceholder.Get.TotalSocialButton', array($this, 'onRender'));
		$this->mRoot->mDelegateManager->add('Legacy.Event.Explaceholder.Get.TotalSocialButton.JavaScript', array($this, 'onRenderJavaScript'));
	}

	protected function _getSocialMediaNames($useStatement)
	{
		$names = preg_split("/[\s,]/", $useStatement);
		$names = array_map('trim', $names);
		$names = array_filter($names);
		return $names;
	}

	public function onRender(&$buffer, array $params)
	{
		$this->params = array(
			'use' => 'mixi-voice mixi-check mixi-diary hatena-bookmark facebook-like twitter google-plus google-plus-share',
			'url' => $this->_getSelfURL(),
			'title' => $this->_getSelfTitle(),
		);
		$this->params = array_merge($this->params, $params);
		$names = $this->_getSocialMediaNames($this->params['use']);

		$buttons = array();

		foreach ( $names as $name )
		{
			$method = sprintf('_plugin_%s', strtr($name, '-', '_'));

			if ( method_exists($this, $method) )
			{
				$buttons[] = $this->$method();
			}
		}

		$buffer = implode(' ', $buttons);
	}

	public function onRenderJavaScript(&$buffer, array $params)
	{
		$scripts = $this->javascripts;
		$scripts = array_unique($scripts);
		$buffer = implode('', $scripts);
	}

	protected function _plugin_mixi_voice()
	{
		$status = urlencode(sprintf('%s %s', $this->params['title'], $this->params['url']));
		return '<a href="http://mixi.jp/simplepost/voice?status='.$status.'"><img src="'.$this->_getMixiVoiceIconURL().'" /></a>';
	}

	protected function _plugin_mixi_check()
	{
		if ( isset($this->params['mixi_key']) === false or $this->params['mixi_key'] == '' )
		{
			return 'mixiチェックを使うには mixi_key を指定してください。';
		}

		$this->javascripts[] = '<script type="text/javascript" src="http://static.mixi.jp/js/share.js"></script>';
		return '<a href="http://mixi.jp/share.pl" class="mixi-check-button" data-key="'.$this->params['mixi_key'].'">mixiチェック</a>';
	}

	protected function _plugin_mixi_diary()
	{
		$title = urlencode($this->params['title']);
		$body  = urlencode(sprintf("%s\n%s", $this->params['title'], $this->params['url']));
		return '<a href="http://mixi.jp/simplepost/diary?title='.$title.'&body='.$body.'" target="_blank"><img src="'.$this->_getMixiDiaryIconURL().'"/></a>';
	}

	protected function _plugin_hatena_bookmark()
	{
		$this->javascripts[] = '<script type="text/javascript" src="http://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script>';
		return '<a href="http://b.hatena.ne.jp/entry/" class="hatena-bookmark-button" data-hatena-bookmark-layout="standard" title="このエントリーをはてなブックマークに追加"><img src="http://b.st-hatena.com/images/entry-button/button-only.gif" alt="このエントリーをはてなブックマークに追加" width="20" height="20" style="border: none;" /></a>';
	}

	protected function _plugin_facebook_like()
	{
		$url = urlencode($this->params['url']);
		return '<iframe src="//www.facebook.com/plugins/like.php?href='.$url.'&amp;send=false&amp;layout=button_count&amp;width=110&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21&amp;appId=389929677727639" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:110px; height:21px;" allowTransparency="true"></iframe>';
	}

	protected function _plugin_twitter()
	{
		$this->javascripts[] = '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		return '<span style="width:100px; display:inline-block;"><a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja">ツイート</a></span>';
	}

	protected function _plugin_google_plus()
	{
		$this->_addGooglePlusJavaScript();
		return '<span style="width:70px; display:inline-block;"><div class="g-plusone" data-size="medium"></div></span>';
	}

	protected function _plugin_google_plus_share()
	{
		$this->_addGooglePlusJavaScript();
		return '<span style="width:70px; display:inline-block;"><div class="g-plus" data-action="share" data-annotation="bubble"></div></span>';
	}

	protected function _addGooglePlusJavaScript()
	{
		$this->javascripts[] = "<script type=\"text/javascript\">
  window.___gcfg = {lang: 'ja'};
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>";
	}

	protected function _getMixiVoiceIconURL()
	{
		return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADoAAAASCAYAAAAKRM1zAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABMRJREFUeNrkl0tInFcUx//fPBydoRM1PhJNfRDS4qONMEY3WrRQiuiy7hJRN10o3YjGnYoFdWE3Pje+UKjuGpCGQKsSC1VJoFI1oVN11Giijlp1nHEyr37nJN/nOPP5oKskPcPlu49z7z2/c8693BF8Ph8mJn7z4QOWvLwcQRgfn/TFxV3DrVs3P0hIs3kRGxuvoKHGeZA+rxt7q1NwOXbh2FtB6JV4hEUkwXg9A4KgeudBiU0GpfRVAtyY/RG7ljGx7ngDJQg4eOkT2x6odBGI/+wuIpJy3ovIKoK6nYdYetyM14cWqDRaCJowBUfYsTrThuPDl7iW/s1/NkAQnSfZQHW73Q69Xh9kE40pBeSyOpx7Xq/3VFmc+B5O2wpUWq2oIU5W+EGlgjo0FK8WRnC4uRC0BpXATantP07S09ODsbExBpyensbU1BSGh4dZd3t7G/Pz86zX1NQEi8UStIb/PjSP5gSOK0Z0d3kCzgMLNLpQfPpVKxz7KwjRR4lto3hWJ2GMuwOtLhxbfz2A9e+H0Gh12Fz4Cclf3D/xnugE2nBubo6NW15ehsFgQFZWFvLz8+XNj46OkJaWxjBkZG1tLWw2G4qKimQAAhwaGuJ55JTy8nIkJyfL41JGtLW18T40VymqQaA75kecrpIc7TzH6vQj3DB9i49ib8P8y33or36CuM/vwWr+GYIIZbc+k9egjcnw5uZmZGdnIykpiQEJamRkhMFLS0vZUJ1Oh/T0dNbv7u7mNRISEmSAmJgYdhCtRRDUJiCS1tZW/jocDh6nscbGRoSFhcmOPBfUefACap1a7rNt/sl1t/MANutzrruc+6Ihb3XE4nEfy/r0TUlJweDgINelQoYTdFVVFcrKyuTILy0tMYSkJwnBt7S0MAA5icDo7NI6tAZlCzmBdMiRtCYBejyey11GKk2o2PGab1g+vz6P/w0UfCmJc0MMsafWoM0CNyQDyTAymuT4+Bjt7e2oqanhvpmZGY42SXFxMaenFDUCo7l0hkm2trbYAaOjozyXQP2deqnLKMSYCK9opDzBBzlykBbynUB6XC7oYzIULyMpamq1msF6e3v5rJETyMjo6GiuE2xfX5/4gsnjfjrfWvEiDA8PR1RUFENR5MfHx3m8srISkZGRKCkp4TpFtb+/n/dSulzZ0fQyyszMkMntVjNWHtdDK96ogur8B4GPUsUF3Pz6B2hCrwSNk7H19fVsOHmeIClaVK+urkZDQwNHh6SjowMDAwP8TUxMREFBATtBo9Ggq6uL+woLC+WMkQAkJ9bV1XGb1qR9/eXJkz/egJpMt08N7C3+iq1Z0UNatZjKGvlmC4R0O124nvUdjPGZyukiOmp9fZ3PFkVFSmnJOAKmSNCXIEwm00mmvE190p2cnOR6bm6u4hkkHSqdnZ1ITU0N0nv6dFYZlM/Q/hqszx7gcGMaAjwcXYYWv5TaXo8KcXcqzoRUehAE9lMhAwOjpAQi6Zwl5FQq/k7yBz3zCagz3kB8doVYq4DLvgO34x/smh/i4MXvMH6cgxjxNaQ1RF/4WjlrXOo/z3j5peZ2X6ijdAEG3boXiVZ/lUu450vEZtxTPI/vuvBts7a2fillQ0zqewcpsQn/lz/e/wowAJT3L1cVg5YTAAAAAElFTkSuQmCC';
	}

	protected function _getMixiDiaryIconURL()
	{
		return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEcAAAASCAYAAAAJ88NbAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABVlJREFUeNrsWE1MlEcYfvbPBTZdJfwpWIG0AgGqRipcTIUmvRAS06QhHKqxJNgDSZsQAicCCocGQzgU5Ya9NIgerInUmDZl1SYtJB5o+WupBVFRYBcLyMKyu2znGZ2vu+z34faqvsnkm5933pl53ud9Z3ZNoVAILtfPIbyRCCktPWoyDQzcCaWn78b+/e+8QeSFTE7ew+zsE1jZ2A6Y0GYAT2d+hX9tEWtP7yNuZwbiE7Pg3HMIJpP5lQSHeGjgMLT0QJkd7sXi9E+ivvYcCJMJy49Doh2E2Z6IjPc+RWLW0VeWQbrgBHwr+Pv2V9hYmYbZaoPJGq8DnhczQ19jfeUxdhd+EjVuEkCy6LJRrMfidrsxPz8v+7KzszE1NaXpOBwOZGVlaXujLa/Xi4SEBF1nhq81OjqKgoKCiPX09Lezp4GzubkZ0XnP1YYN7yNYbDbJFnGU6JlmMyxxcXgy1gdHSr4oeRHDFosFx48f1120ra0N+fn5mJubw8jIiOzjJnt7e2V9dXVVgtPa2hpx8JqaGnR2diI1NVUDmIXjfX19mv1Lly6hqqpK2qFUV1dHnJH6AwMDUo/24sQ5YmLO4pQLvuVpWO1xyP2oA2tL97EjIVm0nSL33IEz/Qhs9l2Y//Ma3H/dgNVmx9zYd8j+oDGKHZSrV69G9Dc1NWlj3JySlJQUnD17VvM8D0s91jlHyenTp+W3sLBQ0+dB9YTgEHwysLS0VNozC6dSv6enR4IfHx8fRQ5DcDyTN2UoaQt4JjAzeBN7iz7HW2kHMfljIxKScpB+4ATck9/DJBbzuscNqWlEa26orKwMCwsLWlgRCEp4eJFhBLiurk7qExTq0ub09DT27dsnD37lypWoNci+kpISDRgy5vr16xJ4ApOZmYlgMBh7zvEtP4TFbtH6ns39LusB3zKeuSdk3e9bEgu90BElGFiPAkC1w72uDq7GWOcBVVhx4wTg+TujVPMoGcbcRF16nWHFuQSpoaFBC6+t69OmqhOY8+fPy3kdHR1ITk7eFhhdcMxWEX+hDZlrpIdDwfAsrMuKHY40Q+ZUVlZGtC9evKjNIxAqL/DLQ1NfgcLvxMSEBI0hNDQ0hMHBQTnGA6qQuHXrVsQaZFe4uFwuCTbBIsgEiOAYsdowIe9wZsK3NAGzyv4haAzRjIX+Aybo98s3z9a4VYvy1lAeoveUNymMe94Y3HBFRYWcQzv8ejweJCUlIS8vD93d3ejv75d6jY2NMiyYRGmX+seOHUNLS4t2+1CYa1SbY4FAACdPnpT76erqko45deqUZiMm5iTnfYz7t1tgEwcZv/Gl1j/727dhoTeLP35oFEQSB9m0IDm3wjCswm8RCnNMuC4PRs+yj+wZGxuT/QyBc+fOwSZuTAJBYDhOPbJL6eXm5mq2mHiZ2MPXUk8FAsBy+PBhXLhwAc3Nzaivr8eZM2fkGjGBE5/0LtIOfIb54W9gtllEmFl13ysEJuDzY0/xFyJHObelp55wo8wZfOsUFxdL2lMuX74sv+Xl5bDb7dKzLPQy840aV3mJ4Lxs7fCcRAbRLkFpb2+XeSwnJ0eXPfK3VVHRwaiB9aUHcI9fw8rsIEwIyltJAiW+m6Ri0Iz0I7VwZryvj7rQ5Vtna9LjVaqozHEl7GNbOYLj4XPZz7ks4QBTh32ca/TY0wsdNccorO7eHTb++WB37kVGSa2o1cLv9SCw9g8WJ29g+eEvcL59FKniVWxzpBh6zS9yEcvL2LNde6v3jcYVu/6PxDLHGoshW0KSLLuCHyLt0AlY43a+Fr/OJUcfPHgUk7IjNf+1AEbhYXrzZ5fxn13/CjAAxUM4QfkSQOAAAAAASUVORK5CYII=';
	}

	protected function _getSelfURL()
	{
		if ( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on' )
		{
			$protocol = 'https://';
		}
		else
		{
			$protocol = 'http://';
		}

		return $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}

	protected function _getSelfTitle()
	{
		$siteName  = $this->mRoot->mContext->getAttribute('legacy_sitename');
		$pageTitle = $this->mRoot->mContext->getAttribute('legacy_pagetitle');
		return sprintf('%s - %s', $siteName, $pageTitle);
	}
}
