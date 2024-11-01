<?php

namespace TendoPay\Marketing;

class Popup_Box_Helper {

	/**
	 * Popup_Box_Helper constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_nopriv_popupbox', [ $this, 'show_popup_box' ] );
		add_action( 'wp_ajax_popupbox', [ $this, 'show_popup_box' ] );
	}

	public function show_popup_box() {
		include TENDOPAY_BASEPATH . "/partials/popup-box.php";
		die();
	}
}