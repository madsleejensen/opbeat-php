<?php

class Opbeat_Utils {

	public static function inlineCookies(Array $items) {
		$cookies = array();
		foreach ($items as $key => $value) {
			$cookies[] = $key . '=' . $value;
		}

		return implode(';', $cookies);
	}

	// @todo not really unique but what the heck.
	public static function uniqueID() {
		return md5(uniqid());
	}

}