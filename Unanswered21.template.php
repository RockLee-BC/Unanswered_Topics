<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl;

	$mark_read = array(
		'view_topics' => array('text' => 'unanswered_view_topics', 'lang' => true, 'url' => $scripturl . '?action=unanswered;topics'),
	);

	echo '
	<div id="recent" class="main_section">
		<div class="cat_bar">
			<h3 class="catbg">
				<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/post/xx.gif" alt="" class="icon" />',$txt['unanswered_topics'],'</span>
			</h3>
		</div>
		<div class="pagesection">';
			template_button_strip($mark_read, 'right');
	echo '
			<span>', $txt['pages'] . ': ', $context['page_index'], '</span>
		</div>';

	
	foreach ($context['posts'] as $post)
	{
		echo '
			<div class="', $post['alternate'] == 0 ? 'windowbg' : 'windowbg2', ' core_posts">
				<span class="topslice"><span></span></span>
				<div class="content">
					<div class="counter">', $post['counter'], '</div>
					<div class="topic_details">
						<h5>', $post['board']['link'], ' / ', $post['link'], '</h5>
						<span class="smalltext">&#171;&nbsp;', $txt['last_post'], ' ', $txt['by'], ' <strong>', $post['poster']['link'], ' </strong> ', $txt['on'], '<em> ', $post['time'], '</em>&nbsp;&#187;</span>
					</div>
					<div class="list_posts">', $post['message'], '</div>
				</div>';

		if ($post['can_reply'] || $post['can_mark_notify'] || $post['can_delete'])
			echo '
				<div class="quickbuttons_wrap">
					<ul class="reset smalltext quickbuttons">';

		// If they *can* reply?
		if ($post['can_reply'])
			echo '
						<li class="reply_button"><a href="', $scripturl, '?action=post;topic=', $post['topic'], '.', $post['start'], '"><span>', $txt['reply'], '</span></a></li>';

		// If they *can* quote?
		if ($post['can_quote'])
			echo '
						<li class="quote_button"><a href="', $scripturl, '?action=post;topic=', $post['topic'], '.', $post['start'], ';quote=', $post['id'], '"><span>', $txt['quote'], '</span></a></li>';

		// Can we request notification of topics?
		if ($post['can_mark_notify'])
			echo '
						<li class="notify_button"><a href="', $scripturl, '?action=notify;topic=', $post['topic'], '.', $post['start'], '"><span>', $txt['notify'], '</span></a></li>';

		// How about... even... remove it entirely?!
		if ($post['can_delete'])
			echo '
						<li class="remove_button"><a href="', $scripturl, '?action=deletemsg;msg=', $post['id'], ';topic=', $post['topic'], ';recent;', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['remove_message'], '?\');"><span>', $txt['remove'], '</span></a></li>';

		if ($post['can_reply'] || $post['can_mark_notify'] || $post['can_delete'])
			echo '
					</ul>
				</div>';

		echo '
				<span class="botslice clear"><span></span></span>
			</div>';

	}

	echo '
		<div class="pagesection">
			<span>', $context['page_index'], '</span>
		</div>
	</div>';
}

function template_unread()
{
	global $context, $settings, $txt, $scripturl, $modSettings;

	echo '
	<div id="recent" class="main_content">';

	if (!empty($context['posts']))
	{
		echo '
			<div class="pagesection">
				', $context['menu_separator'], '<a href="#bot" class="topbottom floatleft">', $txt['go_down'], '</a>
				<div class="pagelinks floatleft">', $context['page_index'], '</div>
				', !empty($context['recent_buttons']) ? template_button_strip($context['recent_buttons'], 'right') : '', '
			</div>';

		echo '
			<div id="unread">
				<div id="topic_header" class="title_bar">
					<div class="icon"></div>
					<div class="info">
						<a href="', $scripturl, '?action=unread', $context['showing_all_topics'] ? ';all' : '', $context['querystring_board_limits'], ';sort=subject', $context['sort_by'] == 'subject' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['subject'], $context['sort_by'] == 'subject' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					</div>
					<div class="stats">
						<a href="', $scripturl, '?action=unread', $context['showing_all_topics'] ? ';all' : '', $context['querystring_board_limits'], ';sort=replies', $context['sort_by'] == 'replies' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['replies'], $context['sort_by'] == 'replies' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					</div>
					<div class="lastpost">
						<a href="', $scripturl, '?action=unread', $context['showing_all_topics'] ? ';all' : '', $context['querystring_board_limits'], ';sort=last_post', $context['sort_by'] == 'last_post' && $context['sort_direction'] == 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] == 'last_post' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					</div>';

		echo '
				</div>
				<div id="topic_container">';

		foreach ($context['posts'] as $topic)
		{
			echo '
					<div class="', $topic['css_class'], '">
						<div class="icon">
							<img src="', $topic['icon_url'], '" alt="">
								', $topic['is_posted_in'] ? '<img src="'. $settings['images_url']. '/icons/profile_sm.png" alt="" style="position: absolute; z-index: 5; right: 4px; bottom: -3px;">' : '','
						</div>
						<div class="info">';

			// Now we handle the icons
			echo '
							<div class="icons">';
			if ($topic['is_sticky'])
				echo '
								<span class="generic_icons sticky floatright"></span>';
			if ($topic['is_poll'])
				echo '
								<span class="generic_icons poll floatright"></span>';
			echo '
							</div>';

			echo '
							<div class="recent_title">
								<a href="', $topic['new_href'], '" id="newicon', $topic['id'], '"><span class="new_posts">' . $txt['new'] . '</span></a>
								', $topic['is_sticky'] ? '<strong>' : '', '<span class="preview" title="', $topic['preview'], '"><span id="msg_' . $topic['id'] . '">', $topic['link'], '</span></span>', $topic['is_sticky'] ? '</strong>' : '', '
							</div>
							<p class="floatleft">
								', $topic['started_by'], '
							</p>
							<small id="pages', $topic['id'], '">&nbsp;', $topic['pages'], '</small>
						</div>
						<div class="stats">
							<p>
								', $topic['replies'], ' ', $txt['replies'], '
								<br>
								', $topic['views'], ' ', $txt['views'], '
							</p>
						</div>
						<div class="lastpost">
							', sprintf($txt['last_post_topic'], '<a href="' . $topic['href'] . '">' . $topic['time'] . '</a>', $topic['poster']['link']), '
						</div>';

				echo '
					</div>';
		}

		if (empty($context['posts']))
			echo '
						<div style="display: none;"></div>';

		echo '
				</div>
			</div>';

		echo '
			<div class="pagesection">
				', !empty($context['recent_buttons']) ? template_button_strip($context['recent_buttons'], 'right') : '', '
				', $context['menu_separator'], '<a href="#recent" class="topbottom floatleft">', $txt['go_up'], '</a>
				<div class="pagelinks">', $context['page_index'], '</div>
			</div>';
	}
	else
		echo '
			<div class="cat_bar">
				<h3 class="catbg centertext">
					', $context['showing_all_topics'] ? $txt['topic_alert_none'] : $txt['unread_topics_visit_none'], '
				</h3>
			</div>';

	echo '
	</div>';
}

function template_replies()
{
	global $context, $settings, $txt, $scripturl, $modSettings;

	echo '
	<div id="recent">';

	if (!empty($context['posts']))
	{
		echo '
			<div class="pagesection">
				', $context['menu_separator'], '<a href="#bot" class="topbottom floatleft">', $txt['go_down'], '</a>
				<div class="pagelinks floatleft">', $context['page_index'], '</div>
				', !empty($context['recent_buttons']) ? template_button_strip($context['recent_buttons'], 'right') : '', '
			</div>';

		echo '
			<div id="unreadreplies">
				<div id="topic_header" class="title_bar">
					<div class="icon"></div>
					<div class="info">
						<a href="', $scripturl, '?action=unreadreplies', $context['querystring_board_limits'], ';sort=subject', $context['sort_by'] === 'subject' && $context['sort_direction'] === 'up' ? ';desc' : '', '">', $txt['subject'], $context['sort_by'] === 'subject' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					</div>
					<div class="stats">
						<a href="', $scripturl, '?action=unreadreplies', $context['querystring_board_limits'], ';sort=replies', $context['sort_by'] === 'replies' && $context['sort_direction'] === 'up' ? ';desc' : '', '">', $txt['replies'], $context['sort_by'] === 'replies' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					</div>
					<div class="lastpost">
						<a href="', $scripturl, '?action=unreadreplies', $context['querystring_board_limits'], ';sort=last_post', $context['sort_by'] === 'last_post' && $context['sort_direction'] === 'up' ? ';desc' : '', '">', $txt['last_post'], $context['sort_by'] === 'last_post' ? ' <span class="generic_icons sort_' . $context['sort_direction'] . '"></span>' : '', '</a>
					</div>';

		echo '
				</div>
				<div id="topic_container">';

		foreach ($context['posts'] as $topic)
		{
			echo '
						<div class="', $topic['css_class'], '">
							<div class="icon">
								<img src="', $topic['icon_url'], '" alt="">
								', $topic['is_posted_in'] ? '<img class="posted" src="' . $settings['images_url'] . '/icons/profile_sm.png" alt="">' : '','
							</div>
							<div class="info">';

			// Now we handle the icons
			echo '
								<div class="icons">';
			if ($topic['is_sticky'])
				echo '
									<span class="generic_icons sticky floatright"></span>';
			if ($topic['is_poll'])
				echo '
									<span class="generic_icons poll floatright"></span>';
			echo '
								</div>';

			echo '
								<div class="recent_title">
									<a href="', $topic['new_href'], '" id="newicon', $topic['id'], '"><span class="new_posts">' . $txt['new'] . '</span></a>
									', $topic['is_sticky'] ? '<strong>' : '', '<span title="', $topic[(empty($modSettings['message_index_preview_first']) ? 'last_post' : 'first_post')]['preview'], '"><span id="msg_' . $topic['id'] . '">', $topic['link'], '</span>', $topic['is_sticky'] ? '</strong>' : '', '
								</div>
								<p class="floatleft">
									', $topic['started_by'], '
								</p>
								<small id="pages', $topic['id'], '">&nbsp;', $topic['pages'], '</small>
							</div>
							<div class="stats">
								<p>
									', $topic['replies'], ' ', $txt['replies'], '
									<br>
									', $topic['views'], ' ', $txt['views'], '
								</p>
							</div>
							<div class="lastpost">
								', sprintf($txt['last_post_topic'], '<a href="' . $topic['href'] . '">' . $topic['time'] . '</a>', $topic['poster']['link']), '
							</div>';

			echo '
						</div>';
		}

		echo '
					</div>
			</div>
			<div class="pagesection">
				', !empty($context['recent_buttons']) ? template_button_strip($context['recent_buttons'], 'right') : '', '
				', $context['menu_separator'], '<a href="#recent" class="topbottom floatleft">', $txt['go_up'], '</a>
				<div class="pagelinks">', $context['page_index'], '</div>
			</div>';
	}
	else
		echo '
			<div class="cat_bar">
				<h3 class="catbg centertext">
					', $context['showing_all_topics'] ? $txt['topic_alert_none'] : $txt['unread_topics_visit_none'], '
				</h3>
			</div>';

	echo '
	</div>';
}

?>