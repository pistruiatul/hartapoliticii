<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
  <div id="sidebar">
    <div style="text-align:left">
    <h2>Despre acest site</h2>
    <br>

    <span itemprop="description">
    Cea mai mare colecție de date despre
    politicieni români care oferă cât mai mult context despre viața lor
    politică.</span>
    <p>
    Cu aceste date am tras concluzii utile cum ar fi câte voturi au
    contat la alegerile parlamentare sau simulatorul de alegeri
    europarlamentare.

    <p class="smalltitle">
      <strong>
        Parteneri
      </strong>
    </p>
      <center>
    <a href="http://www.fspub.unibuc.ro/" target="_blank">
      <img src="/images/parteneri-fbpub.jpg"
           class="banner-partners"
           alt="Facultatea de Științe Politice">
    </a>

    <a href="http://www.alegericorecte.ro" target="_blank">
      <img src="/images/parteneri-alegericorecte-2.jpg"
           class="banner-partners"
           alt="Coaliția Pentru Alegeri Corecte 2012">
    </a>

    <a href="http://www.activewatch.ro" target="_blank">
      <img src="/images/parteneri-activewatch.jpg"
           class="banner-partners"
           alt="Active Watch">
    </a>
    </center>

    <div style="border:1px solid #EEE;padding:3px;text-align:center;">
      Te poți abona la acest blog
      <a href="http://feedburner.google.com/fb/a/mailverify?uri=HartaPoliticiiDinRomania&amp;loc=en_US">print email</a>
      sau <a href="http://feeds.feedburner.com/HartaPoliticiiDinRomania">prin rss</a>.
    </div>
    <table width=340>
    <td width=50% valign="top">
		<ul>
			<?php 	/* Widgetized sidebar, if you have the plugin installed. */
					if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
			<li>
				<?php //get_search_form(); ?>
			</li>

			<!-- Author information is disabled per default. Uncomment and fill in your details if you want to use it.
			<li><h2>Author</h2>
			<p>A little something about you, the author. Nothing lengthy, just an overview.</p>
			</li>
			-->

			<?php if ( is_404() || is_category() || is_day() || is_month() ||
						is_year() || is_search() || is_paged() ) {
			?> <li>

			<?php /* If this is a 404 page */ if (is_404()) { ?>
			<?php /* If this is a category archive */ } elseif (is_category()) { ?>
			<p>You are currently browsing the archives for the <?php single_cat_title(''); ?> category.</p>

			<?php /* If this is a yearly archive */ } elseif (is_day()) { ?>
			<p>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for the day <?php the_time('l, F jS, Y'); ?>.</p>

			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<p>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for <?php the_time('F, Y'); ?>.</p>

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<p>You are currently browsing the <a href="<?php bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for the year <?php the_time('Y'); ?>.</p>

			<?php /* If this is a monthly archive */ } elseif (is_search()) { ?>
			<p>You have searched the <a href="<?php echo bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives
			for <strong>'<?php the_search_query(); ?>'</strong>. If you are unable to find anything in these search results, you can try one of these links.</p>

			<?php /* If this is a monthly archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<p>You are currently browsing the <a href="<?php echo bloginfo('url'); ?>/"><?php echo bloginfo('name'); ?></a> blog archives.</p>

			<?php } ?>

			</li> <?php }?>

			<?php wp_list_pages('title_li=<h2>Pages</h2>' ); ?>


			<?php wp_list_categories('show_count=1&title_li=<h2>Categories</h2>'); ?>

			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>
				<?php wp_list_bookmarks(); ?>

				<li><h2>Meta</h2>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional">Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
					<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
					<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
					<?php wp_meta(); ?>
				</ul>
				</li>
			<?php } ?>


			<li><h2>Archives</h2>
				<ul>
				<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</li>

			<?php endif; ?>
		</ul>
		</td><td width=50% valign="top">
      <br><br>
      <div class="fb-like-box" data-href="http://www.facebook.com/hartapoliticii" data-width="190" data-height="380" data-show-faces="true" data-stream="false" data-header="false"></div>

		</td></table>
	</div>
</div>
