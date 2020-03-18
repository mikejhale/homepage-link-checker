<?php
/**
 * Registers the Link checker Results shortcode
 *
 * @package LinkChecker
 */

namespace LinkChecker\Shortcodes;

/**
 * Link_Checker_Results_Shortcode Class - Handles the display of link check results
 */
class Link_Checker_Results_Shortcode {

	/**
	 * Admin_Menu Constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'results_shortcode' ] );
	}

	/**
	 * Sets up the Admin Menu.
	 * Handles `init`
	 *
	 * @return void
	 */
	public function results_shortcode() {
		add_shortcode( 'link-checker-results', [ $this, 'link_checker_results' ] );
	}

	/**
	 * The link-checker-results shortcode.
	 *
	 * @param array $atts shortcode args.
	 * @return string
	 */
	public function link_checker_results( $atts ) {

		$args = shortcode_atts(
			array( 'no_results' => __( 'No results to display', 'link-checker' ) ),
			$atts
		);

		$display_results = '';
		$link_results    = get_transient( 'link_checker_links' );

		if ( ! $link_results ) {
			$display_results = sprintf( '<div class="link-checker link-checker-no-results">%s</div>', $args['no_results'] );
		} else {
			$display_results = $this->format_results( $link_results );
		}

		return $display_results;
	}

	/**
	 * Format results for link-checker-results shortcode.
	 *
	 * @param array $results array of link checker results.
	 * @return string
	 */
	private function format_results( $results ) {

		$display_results  = '<div class="link-checker link-checker-results">';
		$display_results .= '<table>';
		$display_results .= '<thead>';

		$display_results .= sprintf(
			'<tr><td>%s</td><td>%s</td>',
			__( 'URL', 'link-checker' ),
			__( 'Status', 'link-checker' )
		);

		$display_results .= '</tr></thead><tbody>';

		// loop through results and display.
		foreach ( $results as $link => $link_status ) {
			$display_results .= sprintf(
				'<tr%s><td>%s</td><td>%s</td></tr>',
				200 !== intval( $link_status ) ? ' class="error"' : '',
				$link,
				$link_status < 0 ? __( 'N/A', 'link-checker' ) : $link_status
			);
		}

		$display_results .= '</tbody></table>';
		$display_results .= '</div>';

		return $display_results;
	}
}
