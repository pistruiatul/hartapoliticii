<?php
/*
Plugin Name: Subscribe To Comments
Version: 2.1.2
Plugin URI: http://txfx.net/code/wordpress/subscribe-to-comments/
Description: Allows readers to receive notifications of new comments that are posted to an entry.  Based on version 1 from <a href="http://scriptygoddess.com/">Scriptygoddess</a>
Author: Mark Jaquith
Author URI: http://txfx.net/
*/

/* This is the code that is inserted into the comment form */
function show_subscription_checkbox ($id='0') {
	global $sg_subscribe;
	sg_subscribe_start();

	if ( $sg_subscribe->checkbox_shown ) return $id;
	if ( !$email = $sg_subscribe->current_viewer_subscription_status() ) :
		$checked_status = ( !empty($_COOKIE['subscribe_checkbox_'.COOKIEHASH]) && 'checked' == $_COOKIE['subscribe_checkbox_'.COOKIEHASH] ) ? true : false;
	?>

<?php /* ------------------------------------------------------------------- */ ?>
<?php /* This is the text that is displayed for users who are NOT subscribed */ ?>
<?php /* ------------------------------------------------------------------- */ ?>

	<p <?php if ($sg_subscribe->clear_both) echo 'style="clear: both;" '; ?>class="subscribe-to-comments">
	<input type="checkbox" name="subscribe" id="subscribe" value="subscribe" style="width: auto;" <?php if ( $checked_status ) echo 'checked="checked" '; ?>/>
	<label for="subscribe"><?php echo $sg_subscribe->not_subscribed_text; ?></label>
	</p>

<?php /* ------------------------------------------------------------------- */ ?>

<?php elseif ( $email == 'admin' && current_user_can('manage_options') ) : ?>

<?php /* ------------------------------------------------------------- */ ?>
<?php /* This is the text that is displayed for the author of the post */ ?>
<?php /* ------------------------------------------------------------- */ ?>

	<p <?php if ($sg_subscribe->clear_both) echo 'style="clear: both;" '; ?>class="subscribe-to-comments">
	<?php echo str_replace('[manager_link]', $sg_subscribe->manage_link($email, true, false), $sg_subscribe->author_text); ?>
	</p>

<?php else : ?>

<?php /* --------------------------------------------------------------- */ ?>
<?php /* This is the text that is displayed for users who ARE subscribed */ ?>
<?php /* --------------------------------------------------------------- */ ?>

	<p <?php if ($sg_subscribe->clear_both) echo 'style="clear: both;" '; ?>class="subscribe-to-comments">
	<?php echo str_replace('[manager_link]', $sg_subscribe->manage_link($email, true, false), $sg_subscribe->subscribed_text); ?>
	</p>

<?php /* --------------------------------------------------------------- */ ?>

<?php endif;

$sg_subscribe->checkbox_shown = true;
return $id;
}



/* -------------------------------------------------------------------- */
/* This function outputs a "subscribe without commenting" form.         */
/* Place this somewhere within "the loop", but NOT within another form  */
/* This is NOT inserted automaticallly... you must place it yourself    */
/* -------------------------------------------------------------------- */
function show_manual_subscription_form() {
	global $id, $sg_subscribe, $user_email;
	sg_subscribe_start();
	$sg_subscribe->show_errors('solo_subscribe', '<div class="solo-subscribe-errors">', '</div>', __('<strong>Error: </strong>', 'subscribe-to-comments'), '<br />');

if ( !$sg_subscribe->current_viewer_subscription_status() ) :
	get_currentuserinfo(); ?>

<?php /* ------------------------------------------------------------------- */ ?>
<?php /* This is the text that is displayed for users who are NOT subscribed */ ?>
<?php /* ------------------------------------------------------------------- */ ?>

	<form action="" method="post">
	<input type="hidden" name="solo-comment-subscribe" value="solo-comment-subscribe" />
	<input type="hidden" name="postid" value="<?php echo (int) $id; ?>" />
	<input type="hidden" name="ref" value="<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . attribute_escape($_SERVER['REQUEST_URI'])); ?>" />

	<p class="solo-subscribe-to-comments">
	<?php _e('Subscribe without commenting', 'subscribe-to-comments'); ?>
	<br />
	<label for="solo-subscribe-email"><?php _e('E-Mail:', 'subscribe-to-comments'); ?>
	<input type="text" name="email" id="solo-subscribe-email" size="22" value="<?php echo $user_email; ?>" /></label>
	<input type="submit" name="submit" value="<?php _e('Subscribe', 'subscribe-to-comments'); ?>" />
	</p>
	</form>

<?php /* ------------------------------------------------------------------- */ ?>

<?php endif;
}



/* -------------------------
Use this function on your comments display - to show whether a user is subscribed to comments on the post or not.
Note: this must be used within the comments loop!  It will not work properly outside of it.
------------------------- */
function comment_subscription_status() {
global $comment;
if ($comment->comment_subscribe == 'Y') {
return true;
} else {
return false;
}
}














/* ============================= */
/* DO NOT MODIFY BELOW THIS LINE */
/* ============================= */

class sg_subscribe_settings {
	function options_page_contents() {
		global $sg_subscribe;
		sg_subscribe_start();
		if ( isset($_POST['sg_subscribe_settings_submit']) ) {
			check_admin_referer('subscribe-to-comments-update_options');
			$update_settings = stripslashes_deep($_POST['sg_subscribe_settings']);
			$sg_subscribe->update_settings($update_settings);
		}


		echo '<h2>'.__('Subscribe to Comments Options','subscribe-to-comments').'</h2>';
		echo '<ul>';

		echo '<li><label for="name">' . __('"From" name for notifications:', 'subscribe-to-comments') . ' <input type="text" size="40" id="name" name="sg_subscribe_settings[name]" value="' . sg_subscribe_settings::form_setting('name') . '" /></label></li>';
		echo '<li><label for="email">' . __('"From" e-mail addresss for notifications:', 'subscribe-to-comments') . ' <input type="text" size="40" id="email" name="sg_subscribe_settings[email]" value="' . sg_subscribe_settings::form_setting('email') . '" /></label></li>';
		echo '<li><label for="clear_both"><input type="checkbox" id="clear_both" name="sg_subscribe_settings[clear_both]" value="clear_both"' . sg_subscribe_settings::checkflag('clear_both') . ' /> ' . __('Do a CSS "clear" on the subscription checkbox/message (uncheck this if the checkbox/message appears in a strange location in your theme)', 'subscribe-to-comments') . '</label></li>';
		echo '</ul>';

		echo '<fieldset><legend>' . __('Comment Form Text', 'subscribe-to-comments') . '</legend>';

		echo '<p>' . __('Customize the messages shown to different people.  Use <code>[manager_link]</code> to insert the URI to the Subscription Manager.', 'subscribe-to-comments') . '</p>';

		echo '<ul>';

		echo '<li><label for="not_subscribed_text">' . __('Not subscribed', 'subscribe-to-comments') . '</label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="not_subscribed_text" name="sg_subscribe_settings[not_subscribed_text]">' . sg_subscribe_settings::textarea_setting('not_subscribed_text') . '</textarea></li>';

		echo '<li><label for="subscribed_text">' . __('Subscribed', 'subscribe-to-comments') . '</label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="subscribed_text" name="sg_subscribe_settings[subscribed_text]">' . sg_subscribe_settings::textarea_setting('subscribed_text') . '</textarea></li>';

		echo '<li><label for="author_text">' . __('Entry Author', 'subscribe-to-comments') . '</label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="author_text" name="sg_subscribe_settings[author_text]">' . sg_subscribe_settings::textarea_setting('author_text') . '</textarea></li>';

		echo '</ul></fieldset>';


		echo '<fieldset>';
		echo '<legend><input type="checkbox" id="use_custom_style" name="sg_subscribe_settings[use_custom_style]" value="use_custom_style"' . sg_subscribe_settings::checkflag('use_custom_style') . ' /> <label for="use_custom_style">' . __('Use custom style for Subscription Manager', 'subscribe-to-comments') . '</label></legend>';

		echo '<p>' . __('These settings only matter if you are using a custom style.  <code>[theme_path]</code> will be replaced with the path to your current theme.', 'subscribe-to-comments') . '</p>';

		echo '<ul>';
		echo '<li><label for="sg_sub_header">' . __('Path to header:', 'subscribe-to-comments') . ' <input type="text" size="40" id="sg_sub_header" name="sg_subscribe_settings[header]" value="' . sg_subscribe_settings::form_setting('header') . '" /></label></li>';
		echo '<li><label for="sg_sub_sidebar">' . __('Path to sidebar:', 'subscribe-to-comments') . ' <input type="text" size="40" id="sg_sub_sidebar" name="sg_subscribe_settings[sidebar]" value="' . sg_subscribe_settings::form_setting('sidebar') . '" /></label></li>';
		echo '<li><label for="sg_sub_footer">' . __('Path to footer:', 'subscribe-to-comments') . ' <input type="text" size="40" id="sg_sub_footer" name="sg_subscribe_settings[footer]" value="' . sg_subscribe_settings::form_setting('footer') . '" /></label></li>';


		echo '<li><label for="before_manager">' . __('HTML for before the subscription manager:', 'subscribe-to-comments') . ' </label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="before_manager" name="sg_subscribe_settings[before_manager]">' . sg_subscribe_settings::textarea_setting('before_manager') . '</textarea></li>';
		echo '<li><label for="after_manager">' . __('HTML for after the subscription manager:', 'subscribe-to-comments') . ' </label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="after_manager" name="sg_subscribe_settings[after_manager]">' . sg_subscribe_settings::textarea_setting('after_manager') . '</textarea></li>';
		echo '</ul>';
		echo '</fieldset>';
	}

	function checkflag($optname) {
		$options = get_settings('sg_subscribe_settings');
		if ( $options[$optname] != $optname )
			return;
		return ' checked="checked"';
	}

	function form_setting($optname) {
		$options = get_settings('sg_subscribe_settings');
		return attribute_escape($options[$optname]);
	}

	function textarea_setting($optname) {
		$options = get_settings('sg_subscribe_settings');
		return wp_specialchars($options[$optname]);
	}

	function options_page() {
		/** Display "saved" notification on post **/
		if ( isset($_POST['sg_subscribe_settings_submit']) )
			echo '<div class="updated"><p><strong>' . __('Options saved.', 'subscribe-to-comments') . '</strong></p></div>';

		echo '<form method="post"><div class="wrap">';

		sg_subscribe_settings::options_page_contents();

	  echo '<p class="submit"><input type="submit" name="sg_subscribe_settings_submit" value="';
	  _e('Update Options &raquo;', 'subscribe-to-comments');
	  echo '" /></p></div>';

		if ( function_exists('wp_nonce_field') )
			wp_nonce_field('subscribe-to-comments-update_options');

		echo '</form>';
	}

}







class sg_subscribe {
	var $errors;
	var $messages;
	var $post_subscriptions;
	var $email_subscriptions;
	var $subscriber_email;
	var $site_email;
	var $site_name;
	var $standalone;
	var $form_action;
	var $checkbox_shown;
	var $use_wp_style;
	var $header;
	var $sidebar;
	var $footer;
	var $clear_both;
	var $before_manager;
	var $after_manager;
	var $email;
	var $new_email;
	var $ref;
	var $key;
	var $key_type;
	var $action;
	var $default_subscribed;
	var $not_subscribed_text;
	var $subscribed_text;
	var $author_text;
	var $salt;
	var $settings;
	var $version = '2.1.2';

	function sg_subscribe() {
		global $wpdb;
		$this->db_upgrade_check();

		$this->settings = get_settings('sg_subscribe_settings');

		$this->salt = $this->settings['salt'];
		$this->site_email = ( is_email($this->settings['email']) && $this->settings['email'] != 'email@example.com' ) ? $this->settings['email'] : get_bloginfo('admin_email');
		$this->site_name = ( $this->settings['name'] != 'YOUR NAME' && !empty($this->settings['name']) ) ? $this->settings['name'] : get_bloginfo('name');
		$this->default_subscribed = ($this->settings['default_subscribed']) ? true : false;

		$this->not_subscribed_text = $this->settings['not_subscribed_text'];
		$this->subscribed_text = $this->settings['subscribed_text'];
		$this->author_text = $this->settings['author_text'];
		$this->clear_both = $this->settings['clear_both'];

		$this->errors = '';
		$this->post_subscriptions = array();
		$this->email_subscriptions = '';
	}


	function manager_init() {
		$this->messages = '';
		$this->use_wp_style = ( $this->settings['use_custom_style'] == 'use_custom_style' ) ? false : true;
		if ( !$this->use_wp_style ) {
			$this->header = str_replace('[theme_path]', get_template_directory(), $this->settings['header']);
			$this->sidebar = str_replace('[theme_path]', get_template_directory(), $this->settings['sidebar']);
			$this->footer = str_replace('[theme_path]', get_template_directory(), $this->settings['footer']);
			$this->before_manager = $this->settings['before_manager'];
			$this->after_manager = $this->settings['after_manager'];
		}

		foreach ( array('email', 'key', 'ref', 'new_email') as $var )
			if ( isset($_REQUEST[$var]) && !empty($_REQUEST[$var]) )
				$this->{$var} = attribute_escape(trim(stripslashes($_REQUEST[$var])));
		if ( !$this->key )
			$this->key = 'unset';
	}


	function add_error($text='generic error', $type='manager') {
		$this->errors[$type][] = $text;
	}


	function show_errors($type='manager', $before_all='<div class="updated updated-error">', $after_all='</div>', $before_each='<p>', $after_each='</p>'){
		if ( is_array($this->errors[$type]) ) {
			echo $before_all;
			foreach ($this->errors[$type] as $error)
				echo $before_each . $error . $after_each;
			echo $after_all;
		}
		unset($this->errors);
	}


	function add_message($text) {
		$this->messages[] = $text;
	}


	function show_messages($before_all='', $after_all='', $before_each='<div class="updated"><p>', $after_each='</p></div>'){
		if ( is_array($this->messages) ) {
			echo $before_all;
			foreach ($this->messages as $message)
				echo $before_each . $message . $after_each;
			echo $after_all;
		}
		unset($this->messages);
	}


	function subscriptions_from_post($postid) {
		if ( is_array($this->post_subscriptions[$postid]) )
			return $this->post_subscriptions[$postid];
		global $wpdb;
		$postid = (int) $postid;
		$this->post_subscriptions[$postid] = $wpdb->get_col("SELECT comment_author_email FROM $wpdb->comments WHERE comment_post_ID = '$postid' AND comment_subscribe='Y' AND comment_author_email != '' AND comment_approved = '1' GROUP BY LCASE(comment_author_email)");
		$subscribed_without_comment = (array) get_post_meta($postid, '_sg_subscribe-to-comments');
		$this->post_subscriptions[$postid] = array_merge((array) $this->post_subscriptions[$postid], (array) $subscribed_without_comment);
		$this->post_subscriptions[$postid] = array_unique($this->post_subscriptions[$postid]);
		return $this->post_subscriptions[$postid];
	}


	function subscriptions_from_email($email='') {
		if ( is_array($this->email_subscriptions) )
			return $this->email_subscriptions;
		if ( !is_email($email) )
			$email = $this->email;
		global $wpdb;
		$email = $wpdb->escape(strtolower($email));

		$subscriptions = $wpdb->get_results("SELECT comment_post_ID FROM $wpdb->comments WHERE LCASE(comment_author_email) = '$email' AND comment_subscribe='Y' AND comment_approved = '1' GROUP BY comment_post_ID");
		foreach ( (array) $subscriptions as $subscription )
			$this->email_subscriptions[] = $subscription->comment_post_ID;
		$subscriptions = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_sg_subscribe-to-comments' AND LCASE(meta_value) = '$email' GROUP BY post_id");
		foreach ( (array) $subscriptions as $subscription)
			$this->email_subscriptions[] = $subscription->post_id;
		if ( is_array($this->email_subscriptions) ) {
			sort($this->email_subscriptions, SORT_NUMERIC);
			return $this->email_subscriptions;
		}
		return false;
	}


	function solo_subscribe ($email, $postid) {
		global $wpdb, $cache_userdata, $user_email;
		$postid = (int) $postid;
		$email = strtolower($email);
		if ( !is_email($email) ) {
			get_currentuserinfo();
			if ( is_email($user_email) )
				$email = strtolower($user_email);
			else
				$this->add_error(__('Please provide a valid e-mail address.', 'subscribe-to-comments'),'solo_subscribe');
		}

		if ( ( $email == $this->site_email && is_email($this->site_email) ) || ( $email == get_settings('admin_email') && is_email(get_settings('admin_email')) ) )
			$this->add_error(__('This e-mail address may not be subscribed', 'subscribe-to-comments'),'solo_subscribe');

		if ( is_array($this->subscriptions_from_email($email)) )
			if (in_array($postid, (array) $this->subscriptions_from_email($email))) {
				// already subscribed
				setcookie('comment_author_email_' . COOKIEHASH, $email, time() + 30000000, COOKIEPATH);
				$this->add_error(__('You appear to be already subscribed to this entry.', 'subscribe-to-comments'),'solo_subscribe');
				}
		$email = $wpdb->escape($email);
		$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$postid' AND comment_status <> 'closed' AND ( post_status = 'static' OR post_status = 'publish')  LIMIT 1");

		if ( !$post )
			$this->add_error(__('Comments are not allowed on this entry.', 'subscribe-to-comments'),'solo_subscribe');

		if ( empty($cache_userdata[$post->post_author]) && $post->post_author != 0) {
			$cache_userdata[$post->post_author] = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = $post->post_author");
			$cache_userdata[$cache_userdata[$post->post_author]->user_login] =& $cache_userdata[$post->post_author];
		}

		$post_author = $cache_userdata[$post->post_author];

		if ( strtolower($post_author->user_email) == ($email) )
			$this->add_error(__('You appear to be already subscribed to this entry.', 'subscribe-to-comments'),'solo_subscribe');

		if ( !is_array($this->errors['solo_subscribe']) ) {
			add_post_meta($postid, '_sg_subscribe-to-comments', $email);
			setcookie('comment_author_email_' . COOKIEHASH, $email, time() + 30000000, COOKIEPATH);
			$location = $this->manage_link($email, false, false) . '&subscribeid=' . $postid;
			header("Location: $location");
			exit();
		}
	}


	function add_subscriber($cid) {
		global $wpdb;
		$cid = (int) $cid;
		$id = (int) $id;
    	$email = strtolower($wpdb->get_var("SELECT comment_author_email FROM $wpdb->comments WHERE comment_ID = '$cid'"));
		$email_sql = $wpdb->escape($email);
		$postid = $wpdb->get_var("SELECT comment_post_ID from $wpdb->comments WHERE comment_ID = '$cid'");

		$previously_subscribed = ( $wpdb->get_var("SELECT comment_subscribe from $wpdb->comments WHERE comment_post_ID = '$postid' AND LCASE(comment_author_email) = '$email_sql' AND comment_subscribe = 'Y' LIMIT 1") || in_array($email, (array) get_post_meta($postid, '_sg_subscribe-to-comments')) ) ? true : false;

		// If user wants to be notified or has previously subscribed, set the flag on this current comment
		if (($_POST['subscribe'] == 'subscribe' && is_email($email)) || $previously_subscribed) {
			delete_post_meta($postid, '_sg_subscribe-to-comments', $email);
			$wpdb->query("UPDATE $wpdb->comments SET comment_subscribe = 'Y' where comment_post_ID = '$postid' AND LCASE(comment_author_email) = '$email'");
		}
		return $cid;
	}


	function is_blocked($email='') {
		global $wpdb;
		if ( !is_email($email) )
			$email = $this->email;
		if ( empty($email) )
			return false;
		$email = strtolower($email);
		// add the option if it doesn't exist
		add_option('do_not_mail', '');
		$blocked = (array) explode (' ', get_settings('do_not_mail'));
		if ( in_array($email, $blocked) )
			return true;
		return false;
	}


	function add_block($email='') {
		if ( !is_email($email) )
			$email = $this->email;
		global $wpdb;
		$email = strtolower($email);

		// add the option if it doesn't exist
		add_option('do_not_mail', '');

		// check to make sure this email isn't already in there
		if ( !$this->is_blocked($email) ) {
			// email hasn't already been added - so add it
			$blocked = get_settings('do_not_mail') . ' ' . $email;
			update_option('do_not_mail', $blocked);
			return true;
			}
		return false;
	}


	function remove_block($email='') {
		if ( !is_email($email) )
			$email = $this->email;
		global $wpdb;
		$email = strtolower($email);

		if ( $this->is_blocked($email) ) {
			// e-mail is in the list - so remove it
			$blocked = str_replace (' ' . $email, '', explode (' ', get_settings('do_not_mail')));
			update_option('do_not_mail', $blocked);
			return true;
			}
		return false;
	}


	function has_subscribers() {
		if ( count($this->get_unique_subscribers()) > 0 )
			return true;
		return false;
	}


	function get_unique_subscribers() {
		global $comments, $comment, $sg_subscribers;
		if ( isset($sg_subscribers) )
			return $sg_subscribers;

		$sg_subscribers = array();
		$subscriber_emails = array();

		// We run the comment loop, and put each unique subscriber into a new array
		foreach ( (array) $comments as $comment ) {
			if ( comment_subscription_status() && !in_array($comment->comment_author_email, $subscriber_emails) ) {
				$sg_subscribers[] = $comment;
				$subscriber_emails[] = $comment->comment_author_email;
			}
		}
		return $sg_subscribers;
	}


	function hidden_form_fields() { ?>
		<input type="hidden" name="ref" value="<?php echo $this->ref; ?>" />
		<input type="hidden" name="key" value="<?php echo $this->key; ?>" />
		<input type="hidden" name="email" value="<?php echo $this->email; ?>" />
	<?php
	}


	function generate_key($data='') {
		if ( '' == $data )
			return false;
		if ( !$this->settings['salt'] )
			die('fatal error: corrupted salt');
		return md5(md5($this->settings['salt'] . $data));
	}


	function validate_key() {
		if ( $this->key == $this->generate_key($this->email) )
			$this->key_type = 'normal';
		elseif ( $this->key == $this->generate_key($this->email . $this->new_email) )
			$this->key_type = 'change_email';
		elseif ( $this->key == $this->generate_key($this->email . 'blockrequest') )
			$this->key_type = 'block';
		elseif ( current_user_can('manage_options') )
			$this->key_type = 'admin';
		else
			return false;
		return true;
	}


	function determine_action() {
		// rather than check it a bunch of times
		$is_email = is_email($this->email);

		if ( is_email($this->new_email) && $is_email && $this->key_type == 'change_email' )
			$this->action = 'change_email';
		elseif ( isset($_POST['removesubscrips']) && $is_email )
			$this->action = 'remove_subscriptions';
		elseif ( isset($_POST['removeBlock']) && $is_email && current_user_can('manage_options') )
			$this->action = 'remove_block';
		elseif ( isset($_POST['changeemailrequest']) && $is_email && is_email($this->new_email) )
			$this->action = 'email_change_request';
		elseif ( $is_email && isset($_POST['blockemail']) )
			$this->action = 'block_request';
		elseif ( isset($_GET['subscribeid']) )
			$this->action = 'solo_subscribe';
		elseif ( $is_email && isset($_GET['blockemailconfirm']) && $this->key == $this->generate_key($this->email . 'blockrequest') )
			$this->action = 'block';
		else
			$this->action = 'none';
	}


	function remove_subscriber($email, $postid) {
		global $wpdb;
		$postid = (int) $postid;
		$email = strtolower($email);
		$email_sql = $wpdb->escape($email);

		if ( delete_post_meta($postid, '_sg_subscribe-to-comments', $email) || $wpdb->query("UPDATE $wpdb->comments SET comment_subscribe = 'N' WHERE comment_post_ID  = '$postid' AND LCASE(comment_author_email) ='$email_sql'") )
			return true;
		else
			return false;
		}


	function remove_subscriptions ($postids) {
		global $wpdb;
		$removed = 0;
		for ($i = 0; $i < count($postids); $i++) {
			if ( $this->remove_subscriber($this->email, $postids[$i]) )
				$removed++;
		}
		return $removed;
	}


	function send_notifications($cid) {
		global $wpdb;
		$cid = (int) $cid;
		$comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$cid' LIMIT 1");
		$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");

		if ( $comment->comment_approved == '1' && $comment->comment_type == '' ) {
			// Comment has been approved and isn't a trackback or a pingback, so we should send out notifications

			$message  = sprintf(__("There is a new comment on the post \"%s\"", 'subscribe-to-comments') . ". \n%s\n\n", $post->post_title, get_permalink($comment->comment_post_ID));
			$message .= sprintf(__("Author: %s\n", 'subscribe-to-comments'), $comment->comment_author);
			$message .= __("Comment:\n", 'subscribe-to-comments') . $comment->comment_content . "\n\n";
			$message .= __("See all comments on this post here:\n", 'subscribe-to-comments');
			$message .= get_permalink($comment->comment_post_ID) . "#comments\n\n";
			//add link to manage comment notifications
			$message .= __("To manage your subscriptions or to block all notifications from this site, click the link below:\n", 'subscribe-to-comments');
			$message .= get_settings('home') . '/?wp-subscription-manager=1&email=[email]&key=[key]';

			$subject = sprintf(__('New Comment On: %s', 'subscribe-to-comments'), $post->post_title);

			$subscriptions = $this->subscriptions_from_post($comment->comment_post_ID);
			foreach ( (array) $subscriptions as $email ) {
				if ( !$this->is_blocked($email) && $email != $comment->comment_author_email && is_email($email) ) {
				        $message_final = str_replace('[email]', urlencode($email), $message);
				        $message_final = str_replace('[key]', $this->generate_key($email), $message_final);
					$this->send_mail($email, $subject, $message_final);
				}
			} // foreach subscription
		} // end if comment approved
		return $cid;
	}


	function change_email_request() {
		if ( $this->is_blocked() )
			return false;

		$subject = __('E-mail change confirmation', 'subscribe-to-comments');
		$message = sprintf(__("You are receiving this message to confirm a change of e-mail address for your subscriptions at \"%s\"\n\n", 'subscribe-to-comments'), get_bloginfo('blogname'));
		$message .= sprintf(__("To change your e-mail address to %s, click this link:\n\n", 'subscribe-to-comments'), $this->new_email);
		$message .= get_option('home') . "/?wp-subscription-manager=1&email=" . urlencode($this->email) . "&new_email=" . urlencode($this->new_email) . "&key=" . $this->generate_key($this->email . $this->new_email) . ".\n\n";
		$message .= __('If you did not request this action, please disregard this message.', 'subscribe-to-comments');
		return $this->send_mail($this->email, $subject, $message);
	}


	function block_email_request($email) {
		if ( $this->is_blocked($email) )
			return false;
		$subject = __('E-mail block confirmation', 'subscribe-to-comments');
		$message = sprintf(__("You are receiving this message to confirm that you no longer wish to receive e-mail comment notifications from \"%s\"\n\n", 'subscribe-to-comments'), get_bloginfo('name'));
		$message .= __("To cancel all future notifications for this address, click this link:\n\n", 'subscribe-to-comments');
		$message .= get_option('home') . "/?wp-subscription-manager=1&email=" . urlencode($email) . "&key=" . $this->generate_key($email . 'blockrequest') . "&blockemailconfirm=true" . ".\n\n";
		$message .= __("If you did not request this action, please disregard this message.", 'subscribe-to-comments');
		return $this->send_mail($email, $subject, $message);
	}


	function send_mail($to, $subject, $message) {
		$subject = '[' . get_bloginfo('name') . '] ' . $subject;

		// strip out some chars that might cause issues, and assemble vars
		$site_name = str_replace('"', "'", $this->site_name);
		$site_email = str_replace(array('<', '>'), array('', ''), $this->site_email);
		$charset = get_settings('blog_charset');

		$headers  = "From: \"{$site_name}\" <{$site_email}>\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/plain; charset=\"{$charset}\"\n";
		return wp_mail($to, $subject, $message, $headers);
	}


	function change_email() {
		global $wpdb;
		$new_email = $wpdb->escape(strtolower($this->new_email));
		$email = $wpdb->escape(strtolower($this->email));
		if ( $wpdb->query("UPDATE $wpdb->comments SET comment_author_email = '$new_email' WHERE comment_author_email = '$email'") )
			$return = true;
		if ( $wpdb->query("UPDATE $wpdb->postmeta SET meta_value = '$new_email' WHERE meta_value = '$email' AND meta_key = '_sg_subscribe-to-comments'") )
			$return = true;
		return $return;
	}


	function entry_link($postid, $uri='') {
		if ( empty($uri) )
			$uri = get_permalink($postid);
		$postid = (int) $postid;
		$title = get_the_title($postid);
		if ( empty($title) )
			$title = __('click here', 'subscribe-to-comments');
		$output = '<a href="'.$uri.'">'.$title.'</a>';
		return $output;
	}


	function sg_wp_head() { ?>
		<style type="text/css" media="screen">
		.updated-error {
			background-color: #FF8080;
			border: 1px solid #F00;
		}
		</style>
		<?php
		return true;
	}


	function db_upgrade_check () {
		global $wpdb;

		// add the options
		add_option('sg_subscribe_settings', array('use_custom_style' => '', 'email' => get_bloginfo('admin_email'), 'name' => get_bloginfo('name'), 'header' => '[theme_path]/header.php', 'sidebar' => '', 'footer' => '[theme_path]/footer.php', 'before_manager' => '<div id="content" class="widecolumn subscription-manager">', 'after_manager' => '</div>', 'not_subscribed_text' => __('Notify me of followup comments via e-mail', 'subscribe-to-comments'), 'subscribed_text' => __('You are subscribed to this entry.  <a href="[manager_link]">Manage your subscriptions</a>.', 'subscribe-to-comments'), 'author_text' => __('You are the author of this entry.  <a href="[manager_link]">Manage subscriptions</a>.', 'subscribe-to-comments'), 'version' => $this->version));

		$settings = get_option('sg_subscribe_settings');
		if ( !$settings ) { // work around WP 2.2/2.2.1 bug
			wp_redirect('http://' . $_SERVER['HTTP_HOST'] . add_query_arg('stcwpbug', '1'));
			exit;
		}

		if ( !$settings['salt'] ) {
			$settings['salt'] = md5(md5(uniqid(rand() . rand() . rand() . rand() . rand(), true))); // random MD5 hash
			$update = true;
		}

		if ( !$settings['clear_both'] ) {
			$settings['clear_both'] = 'clear_both';
			$update = true;
		}

		if ( !$settings['version'] ) {
			$settings = stripslashes_deep($settings);
			$update = true;
		}

		if ( $settings['not_subscribed_text'] == '' || $settings['subscribed_text'] == '' ) { // recover from WP 2.2/2.2.1 bug
			delete_option('sg_subscribe_settings');
			wp_redirect('http://' . $_SERVER['HTTP_HOST'] . add_query_arg('stcwpbug', '2'));
			exit;
		}

		if ( $update )
			$this->update_settings($settings);

		$column_name = 'comment_subscribe';
		foreach ( (array) $wpdb->get_col("DESC $wpdb->comments", 0) as $column )
			if ($column == $column_name)
				return true;

		// didn't find it... create it
		$wpdb->query("ALTER TABLE $wpdb->comments ADD COLUMN comment_subscribe enum('Y','N') NOT NULL default 'N'");
	}


	function update_settings($settings) {
		$settings['version'] = $this->version;
		update_option('sg_subscribe_settings', $settings);
	}


	function current_viewer_subscription_status(){
		global $wpdb, $post, $user_email;

		$comment_author_email = ( isset($_COOKIE['comment_author_email_'. COOKIEHASH]) ) ? trim($_COOKIE['comment_author_email_'. COOKIEHASH]) : '';
		get_currentuserinfo();

		if ( is_email($user_email) ) {
			$email = strtolower($user_email);
			$loggedin = true;
		} elseif ( is_email($comment_author_email) ) {
			$email = strtolower($comment_author_email);
		} else {
			return false;
		}

		$post_author = get_userdata($post->post_author);
		if ( strtolower($post_author->user_email) == $email && $loggedin )
			return 'admin';

		if ( is_array($this->subscriptions_from_email($email)) )
			if ( in_array($post->ID, (array) $this->email_subscriptions) )
				return $email;
		return false;
	}


	function manage_link($email='', $html=true, $echo=true) {
		$link  = get_option('home') . '/?wp-subscription-manager=1';
		if ( $email != 'admin' ) {
			$link = add_query_arg('email', urlencode($email), $link);
			$link = add_query_arg('key', $this->generate_key($email), $link);
		}
		$link = add_query_arg('ref', rawurlencode('http://' . $_SERVER['HTTP_HOST'] . attribute_escape($_SERVER['REQUEST_URI'])), $link);
		//$link = str_replace('+', '%2B', $link);
		if ( $html )
			$link = htmlentities($link);
		if ( !$echo )
			return $link;
		echo $link;
	}


	function on_edit($cid) {
		global $wpdb;
		$comment = &get_comment($cid);
		if ( !is_email($comment->comment_author_email) && $comment->comment_subscribe == 'Y' )
			$wpdb->query("UPDATE $wpdb->comments SET comment_subscribe = 'N' WHERE comment_ID = '$comment->comment_ID' LIMIT 1");
		return $cid;
	}


	function add_admin_menu() {
		add_management_page(__('Comment Subscription Manager', 'subscribe-to-comments'), __('Subscriptions', 'subscribe-to-comments'), 8, 'stc-management', 'sg_subscribe_admin');

		add_options_page(__('Subscribe to Comments', 'subscribe-to-comments'), __('Subscribe to Comments', 'subscribe-to-comments'), 5, 'stc-options', array('sg_subscribe_settings', 'options_page'));
	}


} // class sg_subscribe





function stc_checkbox_state($data) {
	if ( isset($_POST['subscribe']) )
		setcookie('subscribe_checkbox_'. COOKIEHASH, 'checked', time() + 30000000, COOKIEPATH);
	else
		setcookie('subscribe_checkbox_'. COOKIEHASH, 'unchecked', time() + 30000000, COOKIEPATH);
	return $data;
}


function sg_subscribe_start() {
	global $sg_subscribe;

	if ( !$sg_subscribe ) {
		load_plugin_textdomain('subscribe-to-comments');
		$sg_subscribe = new sg_subscribe();
	}
}

// This will be overridden if the user manually places the function
// in the comments form before the comment_form do_action() call
add_action('comment_form', 'show_subscription_checkbox');

// priority is very low (50) because we want to let anti-spam plugins have their way first.
add_action('comment_post', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); return $sg_subscribe->send_notifications($a);'), 50);
add_action('comment_post', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); return $sg_subscribe->add_subscriber($a);'));

add_action('wp_set_comment_status', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); return $sg_subscribe->send_notifications($a);'));
add_action('admin_menu', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); $sg_subscribe->add_admin_menu();'));
add_action('admin_head', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); $sg_subscribe->sg_wp_head();'));
add_action('edit_comment', array('sg_subscribe', 'on_edit'));

// save users' checkbox preference
add_filter('preprocess_comment', 'stc_checkbox_state', 1);


// detect "subscribe without commenting" attempts
add_action('init', create_function('$a','global $sg_subscribe; if ( $_POST[\'solo-comment-subscribe\'] == \'solo-comment-subscribe\' && is_numeric($_POST[\'postid\']) ) {
	sg_subscribe_start();
	$sg_subscribe->solo_subscribe(stripslashes($_POST[\'email\']), (int) $_POST[\'postid\']);
}')
);

if ( isset($_REQUEST['wp-subscription-manager']) )
	add_action('template_redirect', 'sg_subscribe_admin_standalone');

function sg_subscribe_admin_standalone() {
	sg_subscribe_admin(true);
}

function sg_subscribe_admin($standalone = false) {
	global $wpdb, $sg_subscribe;

	sg_subscribe_start();

	if ( $standalone ) {
		$sg_subscribe->form_action = get_option('home') . '/?wp-subscription-manager=1';
		$sg_subscribe->standalone = true;
		ob_start(create_function('$a', 'return str_replace("<title>", "<title> " . __("Subscription Manager", "subscribe-to-comments") . " &raquo; ", $a);'));
	} else {
		$sg_subscribe->form_action = 'edit.php?page=stc-management';
		$sg_subscribe->standalone = false;
	}

	$sg_subscribe->manager_init();

	get_currentuserinfo();

	if ( !$sg_subscribe->validate_key() )
		die ( __('You may not access this page without a valid key.', 'subscribe-to-comments') );

	$sg_subscribe->determine_action();

	switch ($sg_subscribe->action) :

		case "change_email" :
			if ( $sg_subscribe->change_email() ) {
				$sg_subscribe->add_message(sprintf(__('All notifications that were formerly sent to <strong>%1$s</strong> will now be sent to <strong>%2$s</strong>!', 'subscribe-to-comments'), $sg_subscribe->email, $sg_subscribe->new_email));
				// change info to the new email
				$sg_subscribe->email = $sg_subscribe->new_email;
				unset($sg_subscribe->new_email);
				$sg_subscribe->key = $sg_subscribe->generate_key($sg_subscribe->email);
				$sg_subscribe->validate_key();
			}
			break;

		case "remove_subscriptions" :
			$postsremoved = $sg_subscribe->remove_subscriptions($_POST['subscrips']);
			if ( $postsremoved > 0 )
				$sg_subscribe->add_message(sprintf(__('<strong>%1$s</strong> %2$s removed successfully.', 'subscribe-to-comments'), $postsremoved, ($postsremoved != 1) ? __('subscriptions', 'subscribe-to-comments') : __('subscription', 'subscribe-to-comments')));
			break;

		case "remove_block" :
			if ( $sg_subscribe->remove_block($sg_subscribe->email) )
				$sg_subscribe->add_message(sprintf(__('The block on <strong>%s</strong> has been successfully removed.', 'subscribe-to-comments'), $sg_subscribe->email));
			else
				$sg_subscribe->add_error(sprintf(__('<strong>%s</strong> isn\'t blocked!', 'subscribe-to-comments'), $sg_subscribe->email), 'manager');
			break;

		case "email_change_request" :
			if ( $sg_subscribe->is_blocked($sg_subscribe->email) )
				$sg_subscribe->add_error(sprintf(__('<strong>%s</strong> has been blocked from receiving notifications.  You will have to have the administrator remove the block before you will be able to change your notification address.', 'subscribe-to-comments'), $sg_subscribe->email));
			else
				if ($sg_subscribe->change_email_request($sg_subscribe->email, $sg_subscribe->new_email))
					$sg_subscribe->add_message(sprintf(__('Your change of e-mail request was successfully received.  Please check your old account (<strong>%s</strong>) in order to confirm the change.', 'subscribe-to-comments'), $sg_subscribe->email));
			break;

		case "block_request" :
			if ($sg_subscribe->block_email_request($sg_subscribe->email ))
				$sg_subscribe->add_message(sprintf(__('Your request to block <strong>%s</strong> from receiving any further notifications has been received.  In order for you to complete the block, please check your e-mail and click on the link in the message that has been sent to you.', 'subscribe-to-comments'), $sg_subscribe->email));
			break;

		case "solo_subscribe" :
			$sg_subscribe->add_message(sprintf(__('<strong>%1$s</strong> has been successfully subscribed to %2$s', 'subscribe-to-comments'), $sg_subscribe->email, $sg_subscribe->entry_link($_GET['subscribeid'])));
			break;

		case "block" :
			if ($sg_subscribe->add_block($sg_subscribe->email)) 
				$sg_subscribe->add_message(sprintf(__('<strong>%1$s</strong> has been added to the "do not mail" list. You will no longer receive any notifications from this site. If this was done in error, please contact the <a href="mailto:%2$s">site administrator</a> to remove this block.', 'subscribe-to-comments'), $sg_subscribe->email, $sg_subscribe->site_email));
			else
				$sg_subscribe->add_error(sprintf(__('<strong>%s</strong> has already been blocked!', 'subscribe-to-comments'), $sg_subscribe->email), 'manager');
			$sg_subscribe->key = $sg_subscribe->generate_key($sg_subscribe->email);
			$sg_subscribe->validate_key();
			break;

	endswitch;



	if ( $sg_subscribe->standalone ) {
		if ( !$sg_subscribe->use_wp_style && !empty($sg_subscribe->header) ) {
		@include($sg_subscribe->header);
		echo $sg_subscribe->before_manager;
	} else { ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	<head>
	<title><?php printf(__('%s Comment Subscription Manager', 'subscribe-to-comments'), bloginfo('name')); ?></title>

		<style type="text/css" media="screen">
			@import url( <?php echo get_settings('siteurl'); ?>/wp-admin/wp-admin.css );
		</style>

		<link rel="stylesheet" type="text/css" media="print" href="<?php echo get_settings('siteurl'); ?>/print.css" />

		<meta http-equiv="Content-Type" content="text/html;
	charset=<?php bloginfo('charset'); ?>" />

	<?php $sg_subscribe->sg_wp_head(); ?>

	</head>
	<body>
	<?php } ?>
	<?php } ?>


	<?php $sg_subscribe->show_messages(); ?>

	<?php $sg_subscribe->show_errors(); ?>


	<div class="wrap">
	<h2><?php printf(__('%s Comment Subscription Manager', 'subscribe-to-comments'), bloginfo('name')); ?></h2>

	<?php if (!empty($sg_subscribe->ref)) : ?>
	<?php $sg_subscribe->add_message(sprintf(__('Return to the page you were viewing: %s', 'subscribe-to-comments'), $sg_subscribe->entry_link(url_to_postid($sg_subscribe->ref), $sg_subscribe->ref))); ?>
	<?php $sg_subscribe->show_messages(); ?>
	<?php endif; ?>



	<?php if ( $sg_subscribe->is_blocked() ) { ?>

		<?php if ( current_user_can('manage_options') ) : ?>

		<fieldset class="options">
			<legend><?php _e('Remove Block', 'subscribe-to-comments'); ?></legend>

			<p>
			<?php printf(__('Click the button below to remove the block on <strong>%s</strong>.  This should only be done if the user has specifically requested it.', 'subscribe-to-comments'), $sg_subscribe->email); ?>
			</p>

			<form name="removeBlock" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
			<input type="hidden" name="removeBlock" value="removeBlock /">
	<?php $sg_subscribe->hidden_form_fields(); ?>

			<p class="submit">
			<input type="submit" name="submit" value="<?php _e('Remove Block &raquo;', 'subscribe-to-comments'); ?>" />
			</p>
			</form>
		</fieldset>

	<?php else : ?>

		<fieldset class="options">
			<legend><?php _e('Blocked', 'subscribe-to-comments'); ?></legend>

			<p>
			<?php printf(__('You have indicated that you do not wish to receive any notifications at <strong>%1$s</strong> from this site. If this is incorrect, or if you wish to have the block removed, please contact the <a href="mailto:%2$s">site administrator</a>.', 'subscribe-to-comments'), $sg_subscribe->email, $sg_subscribe->site_email); ?>
			</p>
		</fieldset>

	<?php endif; ?>


	<?php } else { ?>


	<?php $postlist = $sg_subscribe->subscriptions_from_email(); ?>

<?php
		if ( isset($sg_subscribe->email) && !is_array($postlist) && $sg_subscribe->email != $sg_subscribe->site_email && $sg_subscribe->email != get_bloginfo('admin_email') ) {
			if ( is_email($sg_subscribe->email) )
				$sg_subscribe->add_error(sprintf(__('<strong>%s</strong> is not subscribed to any posts on this site.', 'subscribe-to-comments'), $sg_subscribe->email));
			else
				$sg_subscribe->add_error(sprintf(__('<strong>%s</strong> is not a valid e-mail address.', 'subscribe-to-comments'), $sg_subscribe->email));
		}
?>

	<?php $sg_subscribe->show_errors(); ?>




	<?php if ( current_user_can('manage_options') ) { ?>

		<fieldset class="options">
			<?php if ( $_REQUEST['email'] ) : ?>
				<p><a href="<?php echo $sg_subscribe->form_action; ?>"><?php _e('&laquo; Back'); ?></a></p>
			<?php endif; ?>

			<legend><?php _e('Find Subscriptions', 'subscribe-to-comments'); ?></legend>

			<p>
			<?php _e('Enter an e-mail address to view its subscriptions or undo a block.', 'subscribe-to-comments'); ?>
			</p>

			<form name="getemail" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
			<input type="hidden" name="ref" value="<?php echo $sg_subscribe->ref; ?>" />

			<p>
			<input name="email" type="text" id="email" size="40" />
			<input type="submit" value="<?php _e('Search &raquo;', 'subscribe-to-comments'); ?>" />
			</p>
			</form>
		</fieldset>

<?php if ( !$_REQUEST['email'] ) : ?>
		<fieldset class="options">
			<?php if ( !$_REQUEST['showallsubscribers'] ) : ?>
				<legend><?php _e('Top Subscriber List', 'subscribe-to-comments'); ?></legend>
			<?php else : ?>
				<legend><?php _e('Subscriber List', 'subscribe-to-comments'); ?></legend>
			<?php endif; ?>

<?php
			$stc_limit = ( !$_REQUEST['showallsubscribers'] ) ? 'LIMIT 25' : '';
			$all_ct_subscriptions = $wpdb->get_results("SELECT distinct LCASE(comment_author_email) as email, count(distinct comment_post_ID) as ccount FROM $wpdb->comments WHERE comment_subscribe='Y' AND comment_approved = '1' GROUP BY email ORDER BY ccount DESC $stc_limit");
			$all_pm_subscriptions = $wpdb->get_results("SELECT distinct LCASE(meta_value) as email, count(post_id) as ccount FROM $wpdb->postmeta WHERE meta_key = '_sg_subscribe-to-comments' GROUP BY email ORDER BY ccount DESC $stc_limit");
			$all_subscriptions = array();

			foreach ( array('all_ct_subscriptions', 'all_pm_subscriptions') as $each ) {
				foreach ( (array) $$each as $sub ) {
					if ( !isset($all_subscriptions[$sub->email]) )
						$all_subscriptions[$sub->email] = (int) $sub->ccount;
					else
						$all_subscriptions[$sub->email] += (int) $sub->ccount;
				}
			}

if ( !$_REQUEST['showallsubscribers'] ) : ?>
	<p><a href="<?php echo attribute_escape(add_query_arg('showallsubscribers', '1', $sg_subscribe->form_action)); ?>"><?php _e('Show all subscribers', 'subscribe-to-comments'); ?></a></p>
<?php elseif ( !$_REQUEST['showccfield'] ) : ?>
	<p><a href="<?php echo add_query_arg('showccfield', '1'); ?>"><?php _e('Show list of subscribers in <code>CC:</code>-field format (for bulk e-mailing)', 'subscribe-to-comments'); ?></a></p>
<?php else : ?>
	<p><a href="<?php echo attribute_escape($sg_subscribe->form_action); ?>"><?php _e('&laquo; Back to regular view'); ?></a></p>
	<p><textarea cols="60" rows="10"><?php echo implode(', ', array_keys($all_subscriptions) ); ?></textarea></p>
<?php endif;


			if ( $all_subscriptions ) {
				if ( !$_REQUEST['showccfield'] ) {
					echo "<ul>\n";
					foreach ( (array) $all_subscriptions as $email => $ccount ) {
						$enc_email = urlencode($email);
						echo "<li>($ccount) <a href='" . attribute_escape($sg_subscribe->form_action . "&email=$enc_email") . "'>" . wp_specialchars($email) . "</a></li>\n";
					}
					echo "</ul>\n";
				}
?>
				<legend><?php _e('Top Subscribed Posts', 'subscribe-to-comments'); ?></legend>
				<?php
				$top_subscribed_posts1 = $wpdb->get_results("SELECT distinct comment_post_ID as post_id, count(distinct comment_author_email) as ccount FROM $wpdb->comments WHERE comment_subscribe='Y' AND comment_approved = '1' GROUP BY post_id ORDER BY ccount DESC LIMIT 25");
				$top_subscribed_posts2 = $wpdb->get_results("SELECT distinct post_id, count(distinct meta_value) as ccount FROM $wpdb->postmeta WHERE meta_key = '_sg_subscribe-to-comments' GROUP BY post_id ORDER BY ccount DESC LIMIT 25");
				$all_top_posts = array();

				foreach ( array('top_subscribed_posts1', 'top_subscribed_posts2') as $each ) {
					foreach ( (array) $$each as $pid ) {
						if ( !isset($all_top_posts[$pid->post_id]) )
							$all_top_posts[$pid->post_id] = (int) $pid->ccount;
						else
							$all_top_posts[$pid->post_id] += (int) $pid->ccount;
					}
				}
				arsort($all_top_posts);

				echo "<ul>\n";
				foreach ( $all_top_posts as $pid => $ccount ) {
					echo "<li>($ccount) <a href='" . get_permalink($pid) . "'>" . get_the_title($pid) . "</a></li>\n";
				}
				echo "</ul>";
				?>

	<?php } ?>

		</fieldset>

<?php endif; ?>

	<?php } ?>

	<?php if ( count($postlist) > 0 && is_array($postlist) ) { ?>


<script type="text/javascript">
<!--
function checkAll(form) {
	for ( i = 0, n = form.elements.length; i < n; i++ ) {
		if ( form.elements[i].type == "checkbox" ) {
			if ( form.elements[i].checked == true )
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}
//-->
</script>

		<fieldset class="options">
			<legend><?php _e('Subscriptions', 'subscribe-to-comments'); ?></legend>

				<p>
				<?php printf(__('<strong>%s</strong> is subscribed to the posts listed below. To unsubscribe to one or more posts, click the checkbox next to the title, then click "Remove Selected Subscription(s)" at the bottom of the list.', 'subscribe-to-comments'), $sg_subscribe->email); ?>
				</p>

				<form name="removeSubscription" id="removeSubscription" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
				<input type="hidden" name="removesubscrips" value="removesubscrips" />
	<?php $sg_subscribe->hidden_form_fields(); ?>

				<ol>
				<?php for ($i = 0; $i < count($postlist); $i++) { ?>
					<li><label for="subscrip-<?php echo $i; ?>"><input id="subscrip-<?php echo $i; ?>" type="checkbox" name="subscrips[]" value="<?php echo $postlist[$i]; ?>" /> <?php echo $sg_subscribe->entry_link($postlist[$i]); ?></label></li>
				<?php } ?>
				</ol>

				<p>
				<a href="javascript:;" onclick="checkAll(document.getElementById('removeSubscription')); return false; "><?php _e('Invert Checkbox Selection', 'subscribe-to-comments'); ?></a>
				</p>

				<p class="submit">
				<input type="submit" name="submit" value="<?php _e('Remove Selected Subscription(s) &raquo;', 'subscribe-to-comments'); ?>" />
				</p>
				</form>
		</fieldset>
	</div>

	<div class="wrap">
	<h2><?php _e('Advanced Options', 'subscribe-to-comments'); ?></h2>

		<fieldset class="options">
			<legend><?php _e('Block All Notifications', 'subscribe-to-comments'); ?></legend>

				<form name="blockemail" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
				<input type="hidden" name="blockemail" value="blockemail" />
	<?php $sg_subscribe->hidden_form_fields(); ?>

				<p>
				<?php printf(__('If you would like <strong>%s</strong> to be blocked from receiving any notifications from this site, click the button below.  This should be reserved for cases where someone is signing you up for notifications without your consent.', 'subscribe-to-comments'), $sg_subscribe->email); ?>
				</p>

				<p class="submit">
				<input type="submit" name="submit" value="<?php _e('Block Notifications &raquo;', 'subscribe-to-comments'); ?>" />
				</p>
				</form>
		</fieldset>

		<fieldset class="options">
			<legend><?php _e('Change E-mail Address', 'subscribe-to-comments'); ?></legend>

				<form name="changeemailrequest" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
				<input type="hidden" name="changeemailrequest" value="changeemailrequest" />
	<?php $sg_subscribe->hidden_form_fields(); ?>

				<p>
				<?php printf(__('If you would like to change the e-mail address for your subscriptions, enter the new address below.  You will be required to verify this request by clicking a special link sent to your current address (<strong>%s</strong>).', 'subscribe-to-comments'), $sg_subscribe->email); ?>
				</p>

				<p>
				<?php _e('New E-mail Address:', 'subscribe-to-comments'); ?> 
				<input name="new_email" type="text" id="new_email" size="40" />
				</p>

				<p class="submit">
				<input type="submit" name="submit" value="<?php _e('Change E-mail Address &raquo;', 'subscribe-to-comments'); ?>" />
				</p>
				</form>
		</fieldset>

			<?php } ?>
	<?php } //end if not in do not mail ?>
	</div>

	<?php if ( $sg_subscribe->standalone ) : ?>
	<?php if ( !$sg_subscribe->use_wp_style ) :
	echo $sg_subscribe->after_manager;

	if ( !empty($sg_subscribe->sidebar) )
		@include_once($sg_subscribe->sidebar);
	if ( !empty($sg_subscribe->footer) )
		@include_once($sg_subscribe->footer);
	?>
	<?php else : ?>
	</body>
	</html>
	<?php endif; ?>
	<?php endif; ?>


<?php die(); // stop WP from loading ?>
<?php } ?>