<?php
require_once("phpQuery-onefile.php");


// パラメータ受取
//$target_url = "https://www.amazon.co.jp/%E3%83%A1%E3%83%B3%E3%82%BA%E3%83%95%E3%82%A1%E3%83%83%E3%82%B7%E3%83%A7%E3%83%B3/b/ref=nav__fshn_gane_mf?ie=UTF8&node=2230005051";
$target_url = "https://www.amazon.co.jp/s/ref=nb_sb_noss?__mk_ja_JP=%E3%82%AB%E3%82%BF%E3%82%AB%E3%83%8A&url=search-alias%3Dfashion-mens&field-keywords=trash";
//$target_url = "https://www.amazon.co.jp/Cycle-Zombies-%E3%82%B5%E3%82%A4%E3%82%AF%E3%83%AB%E3%82%BE%E3%83%B3%E3%83%93%E3%83%BC%E3%82%BA-Premium-Trucker/dp/B06XD1TSJ8/ref=sr_1_1?s=mens-fashion&ie=UTF8&qid=1508134949&sr=1-1&nodeID=2230005051&psd=1&keywords=trash";
$keyword = "trash";
$search_num = "3";
$dom_temprate = "#result_%NUM% .s-item-container .a-spacing-base .a-column .a-link-normal";
$html = file_get_contents($target_url);
$dom = phpQuery::newDocument(mb_convert_encoding($html, 'HTML-ENTITIES', 'utf-8'));

$table = array();

foreach(range(0, 0) as $i){
    $target_dom = str_replace("%NUM%", $i, $dom_temprate);
    $result_html = file_get_contents($dom[$target_dom]->attr("href"));
    $result_dom = phpQuery::newDocumentHtml(mb_convert_encoding($result_html, 'HTML-ENTITIES', 'utf-8'));
    echo trim($result_dom["#productTitle"]->text());
    $result_array = array(
        "product_name",
        "main_img",
        "sub_img",
        "description");
  //echo $result_array;
  //sleep(5);
}

//file_put_contents("test.txt",$dom["li"]->find("#result_0")->text());
//echo $dom["meta"];
//echo $dom;
//echo pq(".a-m-jp")->text();
//echo $dom;
//echo $dom["title"]->text()."\n";
?>