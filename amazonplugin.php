<?php
// 取得データからテーブルタグを生成
function generateTable($table){

}

// 指定URLを検索用URLに変換
function create_target_url($url, $keyword){
    return str_replace("//www.amazon.co.jp/", "//www.amazon.co.jp/s/", $url)."&field-keywords=".$keyword;
}

// メイン処理
function amazonSearchResultGenerator(){
    require_once("lib/phpQuery-onefile.php");

    // パラメータ受取
    $url_raw = "https://www.amazon.co.jp/%E3%83%A1%E3%83%B3%E3%82%BA%E3%83%95%E3%82%A1%E3%83%83%E3%82%B7%E3%83%A7%E3%83%B3/b/ref=sd_allcat_fshn_gane_mf?ie=UTF8&node=2230005051";
    $keyword = "mens";
    $target_url = create_target_url($url_raw, $keyword);
    $search_num = "3";

    // 検索結果ページ取得
    $dom_temprate = "#result_%NUM% .s-item-container .a-spacing-base .a-column .a-link-normal";
    $html = file_get_contents($target_url);
    $dom = phpQuery::newDocument(mb_convert_encoding($html, 'HTML-ENTITIES', 'utf-8'));

    $table = array();
    foreach(range(0, 0) as $i){
        // 上位検索結果を順に取得
        $target_dom = str_replace("%NUM%", $i, $dom_temprate);
        $result_html = file_get_contents($dom[$target_dom]->attr("href"));
        $result_dom = phpQuery::newDocumentHtml(mb_convert_encoding($result_html, 'HTML-ENTITIES', 'utf-8'));

        // レビュー配列
        $review_array = array();
        foreach(range(0, 2) as $r){
            $review = array(
                trim($result_dom['div[data-hook="review"]']->eq($r)->find('a[data-hook="review-title"]')->text()),
                trim($result_dom['div[data-hook="review"]']->eq($r)->find('div[data-hook="review-collapsed"]')->text())
            );
            array_push($review_array, $review);
        }

        // 全体のデータ配列
        $result_array = array(
            "product_name" => trim($result_dom["#productTitle"]->text()),
            "main_img" => $result_dom[".a-button-thumbnail"]->find("img")->eq(0)->attr("src"),
            "sub_img" => $result_dom[".a-button-thumbnail"]->find("img")->eq(1)->attr("src"),
            "description" => trim($result_dom["#productDescription"]->text()),
            "review" => $review_array
        );
        array_push($table, $result_array);
        sleep(5);
    }

    generateTable($table);

    return $table;
}



amazonSearchResultGenerator();

?>