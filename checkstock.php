<?php

function notifyInStock($name, $url, $filename)
{
   file_put_contents($filename, '');
   echo $name . ' - ' . $url . "\n";
}

function getPage($url)
{
   $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:54.0) Gecko/20100101 Firefox/54.0';
   $ch = curl_init($url);
   $headers = array(
      'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language: en-US,en;q=0.5'
   );
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
   //curl_setopt($ch, CURLOPT_VERBOSE, true);
   $result = curl_exec($ch);
   if ($result === false) {
      echo curl_error($ch);
   }
   return $result;
}

$urls = array(
   'Amazon' => 'https://www.amazon.com/Super-NES-Classic/dp/B0721GGGS9/ref=sr_1_3?s=videogames&ie=UTF8&qid=1498662842&sr=1-3&keywords=super+nes+classic',
   'Walmart' => 'https://www.walmart.com/ip/Super-Nintendo-Entertainment-System-Classic-Edition/55791858',
   'Best Buy' => 'http://www.bestbuy.com/site/nintendo-entertainment-system-snes-classic-edition/5919830.p'
;

if (!file_exists('amazon.txt')) {
   echo "Fetching from Amazon...\n";
   $amazon = getPage($urls['Amazon']);
   if (strpos($amazon, 'id="outOfStock"') === false) {
      notifyInStock('Amazon', $urls['Amazon'], 'amazon.txt');
   }
}

if (!file_exists('walmart.txt')) {
   echo "Fetching from Walmart...\n";
   $walmart = getPage($urls['Walmart']);
   if (strpos($walmart, 'OutOfStock') === false) {
      notifyInStock('Walmart', $urls['Walmart'], 'walmart.txt');
   }
}

if (!file_exists('bestbuy.txt')) {
   echo "Fetching from Best Buy...\n";
   $bestbuy = getPage($urls['Best Buy']);
   if (strpos($bestbuy, 'data-purchasable="true"') !== false) {
      notifyInStock('Best Buy', $urls['Best Buy'], 'bestbuy.txt');
   }  
}

