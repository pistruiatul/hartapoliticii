=== Subscribe to Comments ===
Tags: comments, subscription, email
Contributors: markjaquith
Requires at least: 2.0.6
Tested up to: 2.3.1
Stable tag: trunk

Subscribe to Comments allows commenters on an entry to subscribe to e-mail notifications for subsequent comments.

== Description ==

Subscribe to Comments is a robust plugin that enables commenters to sign up for e-mail notification of subsequent entries.  The plugin includes a full-featured subscription manager that your commenters can use to unsubscribe to certain posts, block all notifications, or even change their notification e-mail address!

== Installation ==

1. Put subscribe-to-comments.php into [wordpress_dir]/wp-content/plugins/
2. Go into the WordPress admin interface and activate the plugin
3. Optional: if your WordPress theme doesn't have the comment_form hook, or if you would like to manually determine where in your comments form the subscribe checkbox appears, enter this where you would like it: `<?php show_subscription_checkbox(); ?>`
4. Optional: If you would like to enable users to subscribe to comments without having to first leave a comment, place this somewhere in your template, but make sure it is **outside the comments form**.  A good place would be right after the ending `</form>` tag for the comments form: `<?php show_manual_subscription_form(); ?>`

== Frequently Asked Questions ==

= How can I tell if it's working? =

1. Log out of WordPress
2. Leave a comment on an entry and check the comment subscription box, using an e-mail that is NOT the WP admin e-mail address or the e-mail address of the author of the post.
3. Leave a second comment using a different e-mail address than the one you used in step 2 (it can be a bogus address).
4. This should trigger a notification to the first address you used.

= I'd like the subscription checkbox to be checked by default.  Can I do that? =

Not anymore.  But the checkbox status will be remembered on a per-user basis.

= My subscription checkbox shows up in a strange place.  How do I fix it? =

Try unchecking the CSS "clear" option.  Beyond that, you're on your own with CSS positioning.