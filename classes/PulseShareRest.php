<?php
/**
 * REST API controller for PulseShare.
 *
 * Provides server-side proxy endpoints for Spotify API calls,
 * keeping credentials secure on the server.
 *
 * @link       https://swapnild.com
 * @since      1.0.3
 * @package    PulseShare
 */

namespace PulseShare\Classes;

use PulseShare\Includes\Helper;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * REST API controller for PulseShare.
 *
 * @since 1.0.3
 */
class PulseShareRest {

	/**
	 * The REST API namespace.
	 *
	 * @since 1.0.3
	 * @var string
	 */
	const NAMESPACE = 'pulseshare/v1';

	/**
	 * Register REST API routes.
	 *
	 * @since 1.0.3
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/tracks',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_tracks' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/episodes',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_episodes' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Permission callback — requires manage_options (admin only).
	 *
	 * @since 1.0.3
	 * @return bool|\WP_Error True if the user has permission, WP_Error otherwise.
	 */
	public function check_permissions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				esc_html__( 'You do not have permission to access this endpoint.', 'pulseshare' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Get album tracks from Spotify.
	 *
	 * @since 1.0.3
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_tracks() {
		if ( Helper::check_pulseshareapi_keys_empty() ) {
			return new \WP_Error(
				'pulseshare_no_api_keys',
				esc_html__( 'Spotify API keys are not configured.', 'pulseshare' ),
				array( 'status' => 400 )
			);
		}

		$pulseshare_options = get_option( 'pulseshare_options' );
		$album_id           = $pulseshare_options['pulseshare_album_id'] ?? '';

		if ( empty( $album_id ) ) {
			return new \WP_Error(
				'pulseshare_no_album_id',
				esc_html__( 'Album ID is not configured.', 'pulseshare' ),
				array( 'status' => 400 )
			);
		}

		$access_token = Helper::get_pulseshareaccess_token();

		if ( empty( $access_token ) ) {
			return new \WP_Error(
				'pulseshare_token_error',
				esc_html__( 'Failed to retrieve Spotify access token.', 'pulseshare' ),
				array( 'status' => 500 )
			);
		}

		$market   = $pulseshare_options['pulseshare_market'] ?? 'US';
		$url      = 'https://api.spotify.com/v1/albums/' . $album_id . '/tracks?market=' . rawurlencode( $market ) . '&limit=50';
		$response = wp_remote_get(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error(
				'pulseshare_api_error',
				$response->get_error_message(),
				array( 'status' => 500 )
			);
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $body ) || ! isset( $body->items ) ) {
			return new \WP_Error(
				'pulseshare_invalid_response',
				esc_html__( 'Invalid response from Spotify API.', 'pulseshare' ),
				array( 'status' => 502 )
			);
		}

		$tracks = array();
		foreach ( $body->items as $item ) {
			$tracks[] = array(
				'id'           => $item->id,
				'name'         => $item->name,
				'external_url' => $item->external_urls->spotify ?? '',
				'uri'          => $item->uri,
				'type'         => $item->type,
			);
		}

		return rest_ensure_response( $tracks );
	}

	/**
	 * Get show episodes from Spotify.
	 *
	 * @since 1.0.3
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_episodes() {
		if ( Helper::check_pulseshareapi_keys_empty() ) {
			return new \WP_Error(
				'pulseshare_no_api_keys',
				esc_html__( 'Spotify API keys are not configured.', 'pulseshare' ),
				array( 'status' => 400 )
			);
		}

		$pulseshare_options = get_option( 'pulseshare_options' );
		$show_id            = $pulseshare_options['pulseshare_show_id'] ?? '';

		if ( empty( $show_id ) ) {
			return new \WP_Error(
				'pulseshare_no_show_id',
				esc_html__( 'Show ID is not configured.', 'pulseshare' ),
				array( 'status' => 400 )
			);
		}

		$access_token = Helper::get_pulseshareaccess_token();

		if ( empty( $access_token ) ) {
			return new \WP_Error(
				'pulseshare_token_error',
				esc_html__( 'Failed to retrieve Spotify access token.', 'pulseshare' ),
				array( 'status' => 500 )
			);
		}

		$market   = $pulseshare_options['pulseshare_market'] ?? 'US';
		$url      = 'https://api.spotify.com/v1/shows/' . $show_id . '/episodes?market=' . rawurlencode( $market ) . '&limit=50';
		$response = wp_remote_get(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error(
				'pulseshare_api_error',
				$response->get_error_message(),
				array( 'status' => 500 )
			);
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $body ) || ! isset( $body->items ) ) {
			return new \WP_Error(
				'pulseshare_invalid_response',
				esc_html__( 'Invalid response from Spotify API.', 'pulseshare' ),
				array( 'status' => 502 )
			);
		}

		$episodes = array();
		foreach ( $body->items as $item ) {
			$episodes[] = array(
				'id'               => $item->id,
				'name'             => $item->name,
				'description'      => $item->description ?? '',
				'html_description' => $item->html_description ?? '',
				'release_date'     => $item->release_date ?? '',
				'images'           => $item->images ?? array(),
				'external_url'     => $item->external_urls->spotify ?? '',
				'uri'              => $item->uri,
				'type'             => $item->type,
			);
		}

		return rest_ensure_response( $episodes );
	}
}
