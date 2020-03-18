<?php
/**
 * Manages the Link Crawler class
 *
 * @package LinkChecker
 */

namespace LinkChecker\Crawler;

/**
 * Crawler Class - Handles the gathering and checking of links
 */
class Crawler {

	/**
	 * Crawler Constructor
	 */
	public function __construct() {
	}

	/**
	 * Sets up the Admin Menu.
	 * Handles `admin_menu`
	 *
	 * @param string $url the URL to crawl.
	 * @return array
	 */
	public function crawl_links( $url ) {

		$link_results = get_transient( 'link_checker_links' );

		if ( ! $link_results ) {

			$request = wp_remote_get( $url );
			$links   = [];

			if ( is_wp_error( $request ) ) {
				return false;
			}

			$body = wp_remote_retrieve_body( $request );
			$dom  = new \DOMDocument();

			// OK here to just suppress errors.
			@$dom->loadHTML( $body );
			$xpath = new \DOMXPath( $dom );
			$hrefs = $xpath->evaluate( '/html/body//a' );

			foreach ( $hrefs as $link ) {
				$links[] = $link->getAttribute( 'href' );
			}

			$link_results = [];
			$check_links  = $this->filter_links( $links, $url );

			foreach ( $check_links as $link ) {
				$link_results[ $link ] = $this->check_link( $link );
			}

			// save for an hour.
			set_transient( 'link_checker_links', $link_results, HOUR_IN_SECONDS );
		}

		return $link_results;
	}

	/**
	 * Filter the links array to only unique full URLs.
	 *
	 * @param array  $links raw list of links to filter.
	 * @param string $site_url  The URL of the page links being checked.
	 * @return array
	 */
	private function filter_links( $links, $site_url ) {

		$internal_host  = wp_parse_url( $site_url, PHP_URL_HOST );
		$filtered_links = [];

		// ignore url hash only.
		$urls = preg_grep( '/^#(.+)/', $links, PREG_GREP_INVERT );

		// remove url hash.
		foreach ( $urls as $idx => $url ) {

			if ( wp_parse_url( $url, PHP_URL_HOST ) === $internal_host && trailingslashit( $site_url ) !== $url ) {
				if ( strpos( $url, '#' ) ) {
					$url = substr( $url, 0, strpos( $url, '#' ) );
				}

				if ( ! in_array( $url, $filtered_links, true ) ) {
					$filtered_links[] = $url;
				}
			}
		}

		return $filtered_links;
	}

	/**
	 * Check the URL status
	 *
	 * @param string $link URL to be checked for status.
	 * @return string
	 */
	private function check_link( $link ) {
		// just request HEAD to be quicker, don't need the contents.
		$response = wp_remote_head(
			$link,
			[ 'timeout' => 5 ]
		);

		if ( ! is_wp_error( $response ) ) {
			$result = wp_remote_retrieve_response_code( $response );
		} else {
			$result = -1;
		}

		return $result;
	}

}
