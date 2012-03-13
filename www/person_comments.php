
<link rel="stylesheet" href="styles-comments.css" type="text/css" media="screen" />

<?php
$postid = checkWordpressPostExistance($person);
$permaLink = "/?cid=9&id={$person->id}";

require_once( dirname(__FILE__) . '/wp-load.php' );

wp(array('p' => $postid, 'name' => $person->name, 's' => '',
         'feed' => "comments-$postid",
         'cpage' => "1&cid=9&id={$person->id}"));

// Do not delete these lines
  if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('Please do not load this page directly. Thanks!');

  if ( post_password_required() ) { ?>
    <p class="nocomments">This post is password protected. Enter the password to view comments.</p>
  <?php
    return;
  }
?>

<!-- You can start editing here. -->
<hr size=1 color="#DDDDDD">
<?php if ( true || have_comments() ) : ?>
  <p id="comments"><?php comments_number('Nici un comentariu', 'Un comentariu', '% comentarii' );?> pentru &#8220;<?php the_title(); ?>&#8221;
  </p>

  <div class="navigation">
    <div class="alignleft"><?php previous_comments_link() ?></div>
    <div class="alignright"><?php next_comments_link() ?></div>
  </div>

  <ol class="commentlist">
  <?php wp_list_comments(); ?>
  </ol>

  <div class="navigation">
    <div class="alignleft"><?php previous_comments_link() ?></div>
    <div class="alignright"><?php next_comments_link() ?></div>
  </div>
 <?php else : // this is displayed if there are no comments so far ?>

  <?php if ('open' == $post->comment_status) : ?>
    <!-- If comments are open, but there are no comments. -->

   <?php else : // comments are closed ?>
    <!-- If comments are closed. -->
    <p class="nocomments">Comentariile sunt �nchise.</p>

  <?php endif; ?>
<?php endif; ?>


<?php if ('open' == $post->comment_status) : ?>

<div id="respond">

<strong><?php comment_form_title( 'Lasa un comentariu', 'Lasa un comentariu la %s' ); ?></strong>

<div class="cancel-comment-reply">
  <small><?php cancel_comment_reply_link(); ?></small>
</div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
  <p>Trebuie sa fii <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode($permaLink); ?>">autentificat</a> pentru a comenta.</p>

<?php else : ?>
  <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

  <?php if ( $user_ID ) : ?>
    <p>Autentificat ca <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(urlencode($permaLink)); ?>" title="Log out of this account">Log out &raquo;</a></p>

  <?php else : ?>
    <p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
    <label for="author"><small>Nume <?php if ($req) echo "(required)"; ?></small></label></p>

    <p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
    <label for="email"><small>Email (nu va fi facut public) <?php if ($req) echo "(required)"; ?></small></label></p>

    <p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
    <label for="url"><small>Website</small></label></p>

  <?php endif; ?>

  <p><textarea name="comment" id="comment" cols="70%" rows="7" tabindex="4"></textarea></p>
  <p><input name="submit" type="submit" id="submit" tabindex="5" value="Trimite Comentariu" />

  <?php
  $replytoid = isset($_GET['replytocom']) ? (int) $_GET['replytocom'] : 0;
  echo "<input type='hidden' name='comment_post_ID' value='$postid' id='comment_post_ID' />\n";
  echo "<input type='hidden' name='comment_parent' id='comment_parent' value='$replytoid' />\n";
  echo "<input type='hidden' name='redirect_to' id='redirect_to' value='$permaLink' />\n";
  ?>
  </p>
  <?php do_action('comment_form', $post->ID); ?>

  </form>

<?php endif; // If registration required and not logged in ?>
</div>

<?php endif; // if you delete this the sky will fall on your head ?>
