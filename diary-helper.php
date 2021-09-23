<?php
/**
 * Plugin Name: Diary Helper
 * Description:
 * Author:      Changwoo
 * Author URI:  https://blog.changwoo.pe.kr
 * Plugin URI:  https://github.com/chwnam/diary-helper
 * Version:     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'diary_helper_get_client_ip_address' ) ) {
	function diary_helper_get_client_ip_address(): string {
		return $_SERVER['REMOTE_HOST'] ?? '';
	}
}


if ( ! function_exists( 'diary_helper_get_whitelist' ) ) {
	function diary_helper_get_whitelist(): array {
		if ( ! defined( 'DIARY_HELPER_WHITELIST' ) ) {
			define( 'DIARY_HELPER_WHITELIST', '192.168.10.1' );
		}

		return array_map( 'trim', explode( ',', DIARY_HELPER_WHITELIST ) );
	}
}


if ( ! function_exists( 'diary_helper_is_protected' ) ) {
	function diary_helper_is_protected(): bool {
		$in_whitelist = in_array( diary_helper_get_client_ip_address(), diary_helper_get_whitelist(), true );
		$is_logged_in = is_user_logged_in();

		return ! $in_whitelist && ! $is_logged_in;
	}
}


if ( ! function_exists( 'diary_helper_on_init' ) ) {
	function diary_helper_on_init() {
		if ( diary_helper_is_protected() ) {
			wp_safe_redirect( wp_login_url( $_SERVER['REQUEST_URI'] ?? '' ) );
			exit;
		}
	}
}

add_action( 'init', 'diary_helper_on_init' );
