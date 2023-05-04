<?php
$url = 'https://www.vacationstogo.com/myprofile.cfm?id=zz02619009';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$html = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

if ($info['http_code'] !== 200) {
  echo "Error: Failed to download the web page";
  exit;
}

$base_url = $info['url'];
$base_url_parts = parse_url($base_url);
$base_url = $base_url_parts['scheme'] . '://' . $base_url_parts['host'];

preg_match_all('/<link.+?href="(.+?\.css)".*?>/i', $html, $matches);
$css_files = $matches[1];

preg_match_all('/<script.+?src="(.+?\.js)".*?>/i', $html, $matches);
$js_files = $matches[1];

foreach ($css_files as $css_file) {
  $css_url = $base_url . $css_file;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $css_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  $css = curl_exec($ch);
  curl_close($ch);

  $html = str_replace($css_file, 'data:text/css,' . rawurlencode($css), $html);
}

foreach ($js_files as $js_file) {
  $js_url = $base_url . $js_file;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $js_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  $js = curl_exec($ch);
  curl_close($ch);

  $html = str_replace($js_file, 'data:text/javascript,' . rawurlencode($js), $html);
}

file_put_contents('webpage.html', $html);
