<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
  <div id="sidebar">
    <div>
    <h2>Despre acest site</h2>
    <br>
    Harta Politicii este o colecție de date despre politicienii români.
    <br><br>
    Am adunat date de prezență, ce s-a scris în diverse liste despre politicieni,
    rezultatele de la diverse alegeri, în speranța de a da cât mai mult
    context fiecărui politician.
    <br><br>
    Apoi cu aceste date am tras concluzii utile cum ar fi
    <a href="?cid=2&room=camera_deputatilor">câte voturi
    au contat la alegerile parlamentare</a> sau
    <a href="?c=alegeri+europarlamentare+2009&cid=10&sid=2">simulatorul de alegeri
    europarlamentare</a>.
    <br><br>
     Este o încercare de a
    aduna într-un singur loc cât mai multă informație relevantă și cât
    mai puțină "vrăjeală", sătul de promisiunile goale legate de viitor și
    în căutarea faptelor, singurele care mi se par relevante.
    <br><br>
    Este un experiment mai degrabă personal pe care, pentru că
    eu îl găsesc util, m-am gândit să îl fac public. Chiar dacă mai este
    mult de muncă la site-ul ăsta, trebuia să încep de undeva. :-)
    <br><br>

    <h2>Atenție</h2>
    <br>Toate datele de pe acest site sunt alcătuite din informații
    prezente online. Deși eu sper că sunt corecte, este foarte posibil ca
    uneori să se strecoare erori neintenționate
    pentru care nu îmi asum răspunderea.
    <br><br>


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
		  <ul>
		  <li></li>
		  <li><h2>Diverse</h2><br>
		  <a href="http://www.webstock.ro/2009/09/castigatorii-webstock-awards-2009/">
      <img src="i/badge-loc-ii-contentagg.gif" border=0>
      </a>
      </li>
      </ul>

      <script type="text/javascript">FB.init("07ac442b81b3c626a5c903acaf5819af");</script>
      <fb:fan profile_id="162595812729" stream="" connections="9" width="180" height="380"></fb:fan>
      <div style="font-size:8px; padding-left:10px">
      <a href="http://www.facebook.com/pages/Harta-Politicii-din-Romania/162595812729">Harta Politicii din România on Facebook</a>
      </div>


		</td></table>
	</div>
</div>
