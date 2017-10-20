<?php
/*
Plugin Name: AmazonSearchResultGenerator
Description: 指定されたカテゴリ内での上位検索結果を抽出します。
Version:     1.0
Author:      VariousCraft K.Tada
Author URI:  http://various-craft.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// 取得データからテーブルタグを生成
function generateTable($table){
    if(count($table) <= 0)
        return '';

    // ヘッダー生成
    $header = '<thead class="amazon_header" style="display:none;"><tr>%th%</tr></thead>';
    $th = '';
    foreach($table[0] as $key => $value){

        $th .= '<th>'.$key.'</th>';
    }
    $header = str_replace('%th%', $th, $header);

    // 抽出データ行生成
    $body = '<tbody class="amazon_body">%tr%</tbody>';
    $tr = '';
    $tr_review = '';
    foreach($table as $row){
        $td_rowspan = '';

        foreach($row as $cell){
            if(!(gettype($cell) == 'array')){
                $td_rowspan .= '<td rowspan="3">'.$cell.'</td>';
            }
            elseif ($row === reset($table) && (gettype($cell) == 'array')){
                foreach($cell as $cell_review){
                    $td_rowspan .= '<td>'.$cell_review[0].'</td>';
                    $td_rowspan .= '<td>'.$cell_review[1].'</td>';
                }
            }
            else {
                $td_review = '';
                foreach($cell as $cell_review){
                    $td_review .= '<td>'.$cell_review[0].'</td>';
                    $td_review .= '<td>'.$cell_review[1].'</td>';
                }
                $tr_review .= '<tr>'.$td_review.'</tr>';
            }
        }
        $tr .= '<tr>'.$td_rowspan.'</tr>'.$tr_review;
    }
    $body = str_replace('%tr%', $tr, $body);

    return '<table class="amazon">'.$body.'</table>';
    // return "<table class=amazon>".$header.$body."</table>";
}

// 指定URLを検索用URLに変換
function create_target_url($url, $keyword){
    return str_replace("//www.amazon.co.jp/", "//www.amazon.co.jp/s/", $url)."&field-keywords=".$keyword;
}

// メイン処理
function amazonSearchResultGenerator(){
    require_once("lib/phpQuery-onefile.php");

    // パラメータ受取
    // $atts = shortcode_atts(array(
    //     "document_root" => "https://www.amazon.co.jp/",
    //     "keyword" => "",
    //     "num" => 3,
    //     "affiliate_id"=>""
    // ),$atts);
    // $url_raw = $attr["document_root"];
    // $keyword = $attr["keyword"];
    // $target_url = create_target_url($url_raw, $keyword);
    // $search_num = $attr["num"];

    $url_raw = "https://www.amazon.co.jp/%E3%83%A1%E3%83%B3%E3%82%BA%E3%83%95%E3%82%A1%E3%83%83%E3%82%B7%E3%83%A7%E3%83%B3/b/ref=sd_allcat_fshn_gane_mf?ie=UTF8&node=2230005051";
    $keyword = "mens";
    $target_url = create_target_url($url_raw, $keyword);
    $search_num = 2;

    // 検索結果ページ取得
    $html = file_get_contents($target_url);
    $dom = phpQuery::newDocument(mb_convert_encoding($html, 'HTML-ENTITIES', 'utf-8'));

    $dom_temprate = "#result_%NUM% .s-item-container .a-spacing-base .a-column .a-link-normal";
    $table = array();
    foreach(range(0, $search_num - 1) as $i){
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

    echo generateTable($table);
}

amazonSearchResultGenerator();

// add_shortcode('amazon', 'amazonSearchResultGenerator');
?>