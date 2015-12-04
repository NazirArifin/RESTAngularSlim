<?php

if ( ! function_exists('token_truncate')) {
	/**
	 * memotong string dengan ukuran tertentu
	 * @param  string $string teks yang akan dipotong
	 * @param  int $width  panjang maksimal
	 * @return string         teks hasil pemotongan
	 */
	function token_truncate($string, $width) {
		$parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
		$parts_count = count($parts);

		$length = 0;
		$last_part = 0;
		for (; $last_part < $parts_count; ++$last_part) {
			$length += strlen($parts[$last_part]);
			if ($length > $width) { break; }
		}
		return implode(array_slice($parts, 0, $last_part));
	}
}