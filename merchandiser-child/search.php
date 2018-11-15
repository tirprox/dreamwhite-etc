<?php get_header(); ?>

			<div id="primary" class="content-area fullwidth">
				<main id="main" class="site-main">
				<div class="row">

				<?php if ( have_posts() ) : ?>

					<header class="page-header">
						<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'getbowtied' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
					</header><!-- .page-header -->

					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'content', 'search' ); ?>

					<?php endwhile; ?>

					<?php getbowtied_the_posts_navigation(); ?>

				<?php else : ?>

					<?php get_template_part( 'content', 'none' ); ?>

				<?php endif; ?>
				
				</div>
				</main><!-- #main -->
			</div><!-- #primary -->


<?php //get_sidebar(); ?>
<?php get_footer(); ?>
