<?php

// No direct access
defined( '_JEXEC' ) or die;

/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
class HypercommentsHelper
{
	public static function getUserAuth()
	{
		$user = JFactory::getUser();
		$params = self::getComponentParams();
		$avatar = '';
		$profile_url = '';

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('hypercomments');
		$dispatcher->trigger('onHypercommentsGetAvatar', array($user, &$avatar));
		$dispatcher->trigger('onHypercommentsGetProfileUrl', array($user, &$profile_url));

		$userData = array(
			'nick'        => $user->get('username'),
			'avatar'      => $avatar,
			'id'          => $user->get('id'),
			'email'       => $user->get('email'),
			'profile_url' => $profile_url
		);

		$time        = time();
		$secret      = $params->get('secret_key'); // Секретный ключ, который Вы ввели в административной панели сайта.
		$user_base64 = base64_encode( json_encode($user) );
		$sign        = md5($secret . $user_base64 . $time);
		$auth        = $user_base64 . "_" . $time . "_" . $sign;

		return $auth;
	}

	public static function getWidgetHtml($component, $contentId, $title='')
	{
		$params = self::getComponentParams();
		$widget_id = $params->get('widget_id');
		$css = $params->get('css_path', '');
		$words_limit = (int)$params->get('words_limit', 10);
		$comments_level = (int)$params->get('max_level', 4);
		$local_limit = (int)$params->get('local_limit', 50);
		$social = $params->get('social');
		$social = (is_array($social)) ? implode(', ', $social) : '';
		$realtime = ($params->get('realtime_show', 1)) ? 'true' : 'false';
		$xid = $component.'_'.$contentId;

		$options = ', xid: "'.$xid.'"';
		$options .= ', words_limit: '.$words_limit;
		$options .= ', social: "'.$social.'"';
		$options .= ', realtime: '.$realtime;
		$options .= ', comments_level: '.$comments_level;

		if($params->get('sync', 1) && JFactory::getUser()->id > 0)
		{
			$auth = self::getUserAuth();
			$options .= ', auth: "'.$auth.'"';
		}

		if(!empty($title))
		{
			$options .= ', title: "'.$title.'"';
		}

		if($css != '')
		{
			$options .= ', css: "'.$css.'"';
		}



		$html = <<<HTML
			<div id="hypercomments_widget"></div>
HTML;
		$commentsHtml = '';
		if($params->get('sync', 1)){
			$comments = self::getArticleComments($xid, $local_limit);
			if(is_array($comments) && count($comments)){
				$commentsHtml = '<div id="comments_contayner">';
					foreach($comments as $v){
						$commentsHtml .= '
						<div>
							<div class="comment_user">'.$v->nick.'</div>
							<div class="comment_text">'.$v->text.'</div>
						</div>
						';
					}
				$commentsHtml .= '</div>';
			}

		}
		$html .= $commentsHtml;
		$html .= <<<HTML
			<script type="text/javascript">
				_hcwp = window._hcwp || [];
				_hcwp.push({widget:"Stream", widget_id: $widget_id $options});

				(function() {
					if("HC_LOAD_INIT" in window)return;
					HC_LOAD_INIT = true;
					var lang = (navigator.language || navigator.systemLanguage || navigator.userLanguage ||  "en").substr(0, 2).toLowerCase();
					var hcc = document.createElement("script");
					hcc.type = "text/javascript";
					hcc.async = true;
					hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://w.hypercomments.com/widget/hc/$widget_id/"+lang+"/widget.js";
					var s = document.getElementsByTagName("script")[0];
					s.parentNode.insertBefore(hcc, s.nextSibling);
					document.getElementById("comments_contayner").remove();
				})();
			</script>
			<a href="http://hypercomments.com" class="hc-link" title="comments widget">comments powered by HyperComments</a>
HTML;
		return $html;
	}


	public static function getCounterWidgetHtml($selector='', $label='')
	{
		if(!defined('HYPERCOMMENTS_COUNTER_LOADED'))
		{
			define('HYPERCOMMENTS_COUNTER_LOADED', 1);

			$params = self::getComponentParams();
			$widget_id = $params->get('widget_id');
			$counter_selector = (empty($selector)) ? $params->get('counter_selector', '.btn') : $selector;
			$counter_label = (empty($label)) ? $params->get('counter_label', 'Комментарии({%COUNT%})') : $label;

			$options = ', selector: "'.$counter_selector.'"';
			$options .= ', label: "'.$counter_label.'"';

			$script = <<<SCRIPT
			_hcwp = window._hcwp || [];
			_hcwp.push({widget:"Bloggerstream", widget_id: $widget_id $options});
			(function() {
				if("HC_LOAD_INIT" in window)return;
				HC_LOAD_INIT = true;
				var lang = (navigator.language || navigator.systemLanguage || navigator.userLanguage ||  "en").substr(0, 2).toLowerCase();
				var hcc = document.createElement("script");
				hcc.type = "text/javascript";
			    hcc.async = true;
				hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://w.hypercomments.com/widget/hc/$widget_id/"+lang+"/widget.js";
				var s = document.getElementsByTagName("script")[0];
				s.parentNode.insertBefore(hcc, s.nextSibling);
			})();
SCRIPT;
			JFactory::getDocument()->addScriptDeclaration($script);
			return '';
		}

	}

	public static function getComponentParams()
	{
		static $params;

		if(!is_object($params))
		{
			$params = JComponentHelper::getParams('com_hypercomments');
		}

		return $params;
	}

	public static function getArticleComments($xid, $limit)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('`nick`, `text`')
			->from('`#__hypercomments`')
			->where('`xid` = '.$db->quote($xid))
			->order('`date` DESC');
		return $db->setQuery($query,0,$limit)->loadObjectList();
	}
}