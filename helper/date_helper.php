<?php


if (!function_exists('format_date')) {
  /**
   * mengubah format tanggal ke bahasa indonesian
   * @param  object  	$go2hi 	object go2hi
   * @param  string 	$f     	format output
   * @param  integer 	$t     	timestamp
   * @param  bool 		$h     	apakah kalender mode hijriah
   * @return string	        	hasil perubahan string
   */
  function format_date($go2hi, $f, $t = 0, $h = false)
  {
    if (!is_numeric($t)) $t = time();
    if (empty($f)) $f = 'd/m/Y H:i:s';
    return $go2hi->date($f, ($h === false ? \go2hi\go2hi::GO2HI_GREG : \go2hi\go2hi::GO2HI_HIJRI), $t, 1);
  }
}


if (!function_exists('datedb_to_tanggal')) {
  /**
   * mengubah tanggal database ke bahasa indonesia
   * @param  object $go2hi 	object go2hi
   * @param  string $d tanggal dengan format database
   * @param  string $f format output
   * @return string    hasil pengolahan tanggal
   */
  function datedb_to_tanggal($go2hi, $d, $f)
  {
    if (!preg_match('/^([0-9]{4})\-([0-9]{2})\-([0-9]{2})/', $d)) return '';
    // bagian date
    $t = strtotime($d);
    if ($t === -1) $t = time();
    // bagian format
    if (empty($f)) {
      $f = 'd/m/Y';
      if (strpos($d, ':') !== false) $f .= ' H:i:s';
    }
    // return format_date
    return format_date($go2hi, $f, $t);
  }
}


if (!function_exists('days_in_week')) {
  /**
   * mendapatkan tanggal awal-akhir dalam satu minggu
   * (dimulai hari minggu)
   * BISA UNTUK KALENDER :D
   * @param  [type] $date [description]
   * @return [type]       [description]
   */
  function days_in_week($date, $from_monday = false)
  {
    $sd = 'sunday';
    $sn = 0;
    if ($from_monday) {
      $sd = 'monday';
      $sn = 1;
    }

    $days    = array();
    $ts     = strtotime($date); // tanggal request
    $start  = (date('w', $ts) == $sn ? $ts : strtotime('last ' . $sd, $ts));
    $days[] = date('Y-m-d', $start);
    for ($i = 1; $i < 7; $i++) {
      $days[] = date('Y-m-d', strtotime('+' . $i . ' day', $start));
    }
    return $days;
  }
}
