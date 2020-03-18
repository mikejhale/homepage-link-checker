<?php
/**
 * Manages the Admin Menu class
 *
 * @package LinkChecker
 */

namespace LinkChecker\Admin;

use \LinkChecker\Crawler\Crawler;

/**
 * Admin_Menu Class - Handles the display and functions for the admin menu
 */
class Admin_Menu {

	/**
	 * Admin_Menu Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Sets up the Admin Menu.
	 * Handles `admin_menu`
	 *
	 * @return void
	 */
	public function admin_menu() {

		$menu_icon = 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( LINKCHECKER_PLUGIN_PATH . '/assets/link-checker-icon.svg' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_menu_page(
			__( 'Link Checker Settings', 'link-checker' ),
			__( 'Link Checker', 'link-checker' ),
			'manage_options',
			'link-checker-admin.php',
			array( $this, 'link_checker_admin_page' ),
			$menu_icon
		);
	}

	/**
	 * Show admin menu.
	 *
	 * @return void
	 */
	public function link_checker_admin_page() {

		$results = '';

		if ( isset( $_REQUEST['_linkchecker_nonce'] ) && wp_verify_nonce( wp_unslash( sanitize_text_field( wp_unslash( $_REQUEST['_linkchecker_nonce'] ) ) ), 'linkchecker_check_links' ) ) {

			try {
				$links   = $this->check_links();
				$results = is_wp_error( $links )
					? $this->get_error_html( $links )
					: $this->format_results( $links );
			} catch ( \Exception $ex ) {
				$results = $this->get_error_html( $ex );
			}
		}
		?>

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form method="post" id="checklinks">
				<?php submit_button( __( 'Check Links', 'link-checker' ) ); ?>
				<?php wp_nonce_field( 'linkchecker_check_links', '_linkchecker_nonce' ); ?>
			</form>
			<div id="link-results">
			<?php echo wp_kses_post( $results ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Gets the error's HTML.
	 *
	 * @param Exception|WP_Error $error Instance of the exception.
	 *
	 * @return string error HTML.
	 */
	private function get_error_html( $error ) {
		return sprintf(
			'<p>%s: %s</p><p>%s</p>',
			__( 'An unexpected error occured', 'link-checker' ),
			is_wp_error( $error ) ? current( current( $error->errors ) ) : $error->getMessage(),
			__( 'Please try checking links again.', 'link-checker' )
		);
	}

	/**
	 * Checks home page links.
	 *
	 * @return array|WP_Error
	 */
	private function check_links() {
		$crawler = new Crawler();
		return $crawler->crawl_links( site_url() );
	}

	/**
	 * Enqueue admin scripts and assets.
	 * Handles `admin_enqueue_scripts`
	 *
	 * @param string $hook the current hook.
	 * @return void
	 */
	public function admin_scripts( $hook ) {
		if ( 'toplevel_page_link-checker-admin' === $hook ) {
			wp_enqueue_script( 'link-checker-admin-js', LINKCHECKER_ADMIN_ASSETS_URL . 'js/link-checker-admin.js', [ 'jquery' ], LINKCHECKER_VERSION, true );
		}
	}

	/**
	 * Format the link checker results.
	 *
	 * @param array $links link check results.
	 * @return string
	 */
	private function format_results( $links ) {

		// @todo use WP_List_Table to format.

		$count    = 1;
		$results  = '<table class="widefat fixed" cellspacing="0"><tbody>';
		$results .= sprintf(
			'<thead><tr><th id="link" class="manage-column column-link" scope="col">%s</th><th id="status" class="manage-column column-status" scope="col">%s</th>',
			__( 'Link', 'link-checker' ),
			__( 'Status', 'link-checker' )
		);

		foreach ( $links as $url => $link_status ) {
			$results .= sprintf(
				'<tr%s><td class="link column-link">%s</td><td class="status column-status">%s</td>',
				$count % 2 ? ' class="alternate"' : '',
				esc_html( $url ),
				intval( $link_status )
			);

			$count++;
		}

		$results .= '</tbody></table>';

		return $results;
	}
}
