<?php
/**
 * WARNING: This file is part of the theme. DO NOT edit
 * this file under any circumstances.
 */

/**
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die();
?>
<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
	<head>
		<meta charset="sss">
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">

		<?php if ( version_compare( get_bloginfo( 'version' ), '4.1', '<' ) ): ?>
			<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php endif ?>

		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

		<?php wp_head() ?><?php echo "Hi"; ?>
		<!--Quang layout blank-->
	</head>
	<body <?php body_class() ?> itemscope="itemscope" itemtype="http://schema.org/WebPage">
		<?php if ( op_option( 'loading_enabed', true ) ): ?>
			<div class="loading-overlay"></div>
		<?php endif ?>

		<?php do_action( 'theme/above_site_wrapper' ) ?>

		<div id="site-wrapper">
			<?php do_action( 'theme/above_site_content' ) ?>
			
			<div id="site-content">
				<?php do_action( 'theme/above_page_body' ) ?>

				<div id="page-body">
					<div class="wrapper">
						<?php do_action( 'theme/above_content_wrap' ) ?>

						<div class="content-wrap">
							<?php do_action( 'theme/above_main_content' ) ?>
						
							<main id="main-content" class="content" role="main" itemprop="mainContentOfPage">
								<div class="main-content-wrap">
									<?php include hnk_template_path() ?>
								</div>
							</main>
							<!-- /#main-content -->
						
							<?php do_action( 'theme/below_main_content' ) ?>
						</div>
						<!-- /.content-wrap -->
						
						<?php do_action( 'theme/below_content_wrap' ) ?>
					</div>
					<!-- /.wrapper -->
				</div>
				<!-- /#page-body -->

				<?php do_action( 'theme/below_page_body' ) ?>
			</div>
			<!-- /#site-content -->
			
			<?php do_action( 'theme/below_site_content' ) ?>
		</div>
		<!-- /#site-wrapper -->

		<?php if ( op_option( 'gotop_enabed' ) ): ?>
			<div class="goto-top"><a href="#top"><?php esc_html_e( 'Goto Top', 'hnk' ) ?></a></div>
		<?php endif ?>

		<?php do_action( 'theme/below_site_wrapper' ) ?>
		<?php wp_footer() ?>
	</body>
</html>
