<?php

namespace Skraypar\Helper;

class Funcs {

	public static function isURL($url) {
		// return (filter_var($this->filePath, FILTER_VALIDATE_URL) ? true : false);
		return preg_match('`^http(s)?://`', $url) ? true : false;
	}

}