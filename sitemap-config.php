<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$server_name = $_SERVER['SERVER_NAME'];
$base_url = $protocol . $server_name . dirname($_SERVER['PHP_SELF']) . '/pages/';

$config = array(
    "SITE_URL" => $base_url,
    "ALLOW_EXTERNAL_LINKS" => false,
    "ALLOW_ELEMENT_LINKS" => false,
    "CRAWL_ANCHORS_WITH_ID" => "",
    "KEYWORDS_TO_SKIP" => array(),
    "SAVE_LOC" => "sitemap.xml",
    "PRIORITY" => 1,
    "CHANGE_FREQUENCY" => "daily",
    "LAST_UPDATED" => date('Y-m-d'),
);

return $config;
?>
