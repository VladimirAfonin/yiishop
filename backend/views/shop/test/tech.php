<?php
ini_set('max_execution_time', 700);
ini_set('memory_limit', '256M');

use backend\views\shop\test\Sheet;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use backend\entities\WebPage; // todo exadd +
use backend\entities\Render; // todo exadd +

$code = Sheet::rf('@backend/views/shop/test/specs.csv', ['indexFrom'=>'code']);

/// interval
$needXmlQuery = needLinkQuery(); // todo exadd +
$item_links = getAllLinks($needXmlQuery);

// uncomment
$targets = [
    'https://www.gsmarena.com/apple_iphone_7-8064.php',
    'https://www.gsmarena.com/xiaomi_redmi_5a-8898.php',
    'https://www.gsmarena.com/lg_q6-8756.php',
    'https://www.gsmarena.com/apple_iphone_x-8858.php',
    'https://www.gsmarena.com/_razer_phone-8923.php',
//    'https://www.gsmarena.com/samsung_galaxy_s9+-8967.php',
//    'https://www.gsmarena.com/huawei_p20_pro-9106.php',
//    'https://www.gsmarena.com/samsung_galaxy_note8-8505.php',
//    'https://www.gsmarena.com/xiaomi_redmi_note_5_(redmi_5_plus)-8959.php',
//    'https://www.gsmarena.com/huawei_honor_9_lite-8962.php',
//    'https://www.gsmarena.com/lg_v30-8712.php',
//    'https://www.gsmarena.com/htc_u11+-8908.php',
//    'https://www.gsmarena.com/xiaomi_mi_a1-8776.php',
//    'https://www.gsmarena.com/huawei_nova_2-8672.php',
//    'https://www.gsmarena.com/meizu_pro_7_plus-8791.php',
//    'https://www.gsmarena.com/asus_zenfone_4_max_pro_zc554kl-8814.php',
//    'https://www.gsmarena.com/xiaomi_mi_max_2-8582.php',
//    'https://www.gsmarena.com/oneplus_5t-8912.php',
//    'https://www.gsmarena.com/panasonic_eluga_switch-7649.php',
//    'https://www.gsmarena.com/micromax_bharat_5-9053.php',
//    'https://www.gsmarena.com/micromax_bharat_5_plus-9054.php',
//    'https://www.gsmarena.com/xiaomi_mi_7-9065.php',
//    'https://www.gsmarena.com/xiaomi_mi_mix_2s-9067.php',
//    'https://www.gsmarena.com/oneplus_6-9109.php',
//    'https://www.gsmarena.com/samsung_galaxy_j7_prime_2-9135.php',
//    'https://www.gsmarena.com/benq_s670c-752.php',
//    'https://www.gsmarena.com/blackberry_curve_8330-3594.php',
//    'https://www.gsmarena.com/htc_desire-3077.php',
//    'https://www.gsmarena.com/samsung_galaxy_s_iii_cdma-4799.php',
//    'https://www.gsmarena.com/sony_ericsson_w760-2197.php'


//    'https://www.gsmarena.com/apple_watch_series_3-8860.php',
];

//$item_links = array_slice($item_links, 400, 500);

// result data info arr
$result_summary_info = [];

foreach($item_links as $k => $url ) {

    if(array_search($url,$targets) === false) { // todo exadd +
        continue;
    }

    ///  todo exadd +
    $path_hash = hash('sha256', $url);
    $obj = WebPage::find()->filterWhere(['path_hash' => $path_hash])->one();
    if ($obj !== null) {
        if ( ! empty($obj->desc)) {
            // compare data if we have already todo
            // ...
            $result_data = Json::decode($obj->desc);
            $result_summary_info[$url] = $result_data;
            continue;
        }
    }
    ///

    $result_data = [];
    $html = getDataFromApi($url); // todo exadd +
    $htmlDom = dom($html);
    $elem = $htmlDom->query('//h1[@class="specs-phone-name-title"]/text()');

    // get name of item
    $name = $elem->item(0)->nodeValue ?? null;
    if($name) {
        $full_name = explode(' ', trim($name));
        $result_data['w81a9u0'] = ucfirst($full_name[0]);
        $model_name = ''; // todo exadd +
        for($i=1;$i<=10;$i++) {
            if(isset($full_name[$i])) $model_name .= $full_name[$i].' ';
        }
//        $result_data['33fksng'] = Html::a($model_name, Url::to($url, true)); // todo exadd + del
        $result_data['33fksng'] = $model_name;// todo exadd +
        $result_data['url'] = Html::a($url, Url::to($url, true)); // todo exadd +
    }

    // todo exadd + all
    $primary_cam = $htmlDom->query('//span[@data-spec="camerapixels-hl"]/text()')->item(0)->nodeValue ?? null;
    if($primary_cam) {
        $result_data['lggn0m2'] = $primary_cam . 'MP';
    }

    // get data released
    $data_release_start = $htmlDom->query('//span[@data-spec="released-hl"]/text()')->item(0)->nodeValue ?? null;
    if($data_release_start) {
        $data_release = preg_match_all('/[0-9]+\,\s+[\w]+/mu', $data_release_start, $output_array);
        if(strpos( $output_array[0][0] ?? $data_release_start, 'ot announced y') != false) {
            $result_data['2lbcv9f'] = '-';
        } else { // todo exadd + all
            $release_date_info = trim(str_replace('Released', '', $output_array[0][0] ?? $data_release_start )); // todo exadd +
            $release_announced_info = explode(',', $release_date_info);
            if(isset($release_announced_info[1])) { // todo exadd +
                $release_month = strftime("%m",strtotime($release_announced_info[1]));
                $result_data['2lbcv9f'] = $release_month.'/'.$release_announced_info[0];
            }
        }
    }

    // also known // todo exadd + all
    $comment_info = $htmlDom->query('//p[@data-spec="comment"]')->item(0)->nodeValue ?? null;
    if($comment_info) {
        preg_match('/Versions:(.+)\s+Also/uim', $comment_info, $out_versions); // versions
        if(isset($out_versions[1]) && !empty($out_versions[1])) {
            $result_data['y1kpha1c'] = trim($out_versions[1]);
        }
        preg_match('/Also known as (.+)/uim', $comment_info, $out_alt_name); // alt name
        if(isset($out_alt_name[1]) && !empty($out_alt_name[1])) {
            $result_data['ywkpha1b'] = trim($out_alt_name[1]);
        }
    }


    // get weight & thin params
    $weight = $htmlDom->query('//span[@data-spec="body-hl"]/text()')->item(0)->nodeValue ?? null;

    // get version OS
    $version_os = $htmlDom->query('//span[@data-spec="os-hl"]/text()')->item(0)->nodeValue ?? null;

    // get hits
    $hits = $htmlDom->query('//li[@class="light pattern help help-popularity"]/span/text()')->item(0)->nodeValue ?? null; // todo exadd +
    if($hits) {
        $hits_info = str_replace('hits','',trim($hits)); // todo exadd +
        $result_data['35fksng'] = intval(str_replace(',','',$hits_info));
    }

    // get video url
    $video_url = $htmlDom->query('//div[@class="module module-vid-review"]/iframe/@src')->item(0)->nodeValue ?? null;
    if($video_url) $result_data['video'] = [$video_url];

    // get storage info
    $storage = $htmlDom->query('//span[@data-spec="storage-hl"]/text()')->item(0)->nodeValue ?? null;
    if($storage) { // todo exadd + all
        $storage_info = explode(',', $storage)[0];
        preg_match('/(\d*\W*[\d+]*\W+\d+\w+)\s+storage|(\d+\w+)\s+storage/', $storage_info, $out_internal_memory);
        if(isset($out_internal_memory[1]) && !empty($out_internal_memory[1])) {
            $result_data['c8xo6x6'] = str_replace(';','', $out_internal_memory[1]);
            if(isset($out_internal_memory[2]) && !empty($out_internal_memory[2])) {
                $result_data['c8xo6x6'] .= '/' . $out_internal_memory[2];
            }
        } else {
            if(isset($out_internal_memory[2]) && !empty($out_internal_memory[2])) {
                $result_data['c8xo6x6'] = $out_internal_memory[2];
            }
        }
    }

    // get screen size
    $screen_size = $htmlDom->query('//span[@data-spec="displaysize-hl"]/text()')->item(0)->nodeValue ?? null;

    // get screen resolution
    $screen_resolution = $htmlDom->query('//div[@data-spec="displayres-hl"]/text()')->item(0)->nodeValue ?? null;

    // get camera info MP
    $camera = $htmlDom->query('//span[@data-spec="camerapixels-hl"]/text()')->item(0)->nodeValue ?? null;

    // get camera pixels
    $camera_pixels = $htmlDom->query('//div[@data-spec="videopixels-hl"]/text()')->item(0)->nodeValue ?? null;

    // get ram size
    $ram_size = $htmlDom->query('//span[@data-spec="ramsize-hl"]/text()')->item(0)->nodeValue ?? null;
    $ram_size_value = $htmlDom->query('//span[@data-spec="ramsize-hl"]/following::span[1]/text()')->item(0)->nodeValue ?? null; // todo exadd +
    if($ram_size) {
        $result_data['ej4wq1y'] = str_replace('RAM', '', $ram_size . $ram_size_value); // todo exadd +
    }

    // get ram chipset
    $chipset = $htmlDom->query('//div[@data-spec="chipset-hl"]/text()')->item(0)->nodeValue ?? null;
    if($chipset) {
        $result_data['dkg7n4e'] = $chipset;
    }


    // get battery info
    $battery_capacity = $ram_size = $htmlDom->query('//span[@data-spec="batsize-hl"]/text()')->item(0)->nodeValue ?? null;
    if($battery_capacity) $result_data['wbswcml'] = $battery_capacity;
    $battery_type = $chipset = $htmlDom->query('//div[@data-spec="battype-hl"]/text()')->item(0)->nodeValue ?? null;
    $result_data['63r9r99'] = str_replace(['Li-Ion','Li-Po','NiMH'],['Lithium Ion',' Lithium Polymer','Nickel-metal Hydride'], $battery_type); // todo exadd +

    // get short desc
    $short_desc_0 = (isset($htmlDom->query('//p[@data-spec="comment"]/text()')->item(0)->nodeValue)) ? $htmlDom->query('//p[@data-spec="comment"]/text()')->item(0)->nodeValue : '';
    $short_desc_1 = (isset($htmlDom->query('//p[@data-spec="comment"]/text()')->item(1)->nodeValue)) ? $htmlDom->query('//p[@data-spec="comment"]/text()')->item(1)->nodeValue : '';
    $short_desc_2 = (isset($htmlDom->query('//p[@data-spec="comment"]/text()')->item(2)->nodeValue)) ? $htmlDom->query('//p[@data-spec="comment"]/text()')->item(2)->nodeValue : '';
    $short_desc_3 = (isset($htmlDom->query('//p[@data-spec="comment"]/text()')->item(3)->nodeValue)) ? $htmlDom->query('//p[@data-spec="comment"]/text()')->item(3)->nodeValue : '';
    $short_desc = $short_desc_0 . '<br>' . $short_desc_1 . '<br>' . $short_desc_2 . '<br>' . $short_desc_3;

### table info ###
///////////// new logic /////
    for($i = 1; $i <= 13; $i++) {
        $some_table = $htmlDom->query("//table[$i]/tr/th")->item(0)->nodeValue ?? null;
        switch($some_table) {
            case 'Network':
                // - Technology td
                $tech_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue
                    : null;
                if ($tech_td == 'Technology') {
                    // get technology
                    $technology = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]')->item(0)->nodeValue ?? null;
                    if($technology) {
                        $result_data['6me3pwq'] = isValueExists($technology, 'GSM');
                        $result_data['k6ddojx'] = isValueExists($technology, 'LTE');
                    }
                }

                // - 2g bands td
                $bands_2g_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue
                    : null;
                if($bands_2g_td == '2G bands') {
                    // get 2g
                    $technology_2g = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td')->item(3)->nodeValue;
                    preg_match_all('/(\s+\d{3,}\s+\/*)+/uim', $technology_2g, $out_2g);
                    if(isset($out_2g[0][0]) && !empty($out_2g[0][0])) {
                        $result_data['es77mka'] = array_map('trim', explode(',', trim(str_replace(' / ', ', ', $out_2g[0][0])))); // todo exadd +
                    }
                }

                // - 3g bands td
                $bands_3g_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue
                    : null;
                if($bands_3g_td == '3G bands') {
                    // get 3g
                    $technology_3g = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)][3]/td')->item(1)->nodeValue;
                    preg_match_all('/(\s+\d{3,}\(*[A-Z]*\)*\s+\/*)+/uim', $technology_3g, $out_3g);
                    if(isset($out_3g[0][0]) && !empty($out_3g[0][0])) {
                        $result_data['lfy3yhr'] = array_map('trim', explode(',', trim(str_replace([' / ', ' /'], [', '], $out_3g[0][0])))); // todo exadd +
                    }
                }

                // - 4g bands td
                $bands_4g_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue
                    : null;
                if($bands_4g_td == '4G bands') {
                    // get 4g
                    $technology_4g = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)][4]/td')->item(1)->nodeValue;
                    $four_g = trim(str_replace(['LTE', 'band'], '', trim($technology_4g)));

                    if ( ! empty($four_g)) { // todo exadd + all
                        preg_match_all('/\d+\(\d+\)/mui', $four_g, $out_g);
                        if (isset($out_g[0]) && ! empty($out_g[0])) {
                            $result_data['w77yz4j'] = '';
                            for ($z = 0; $z <= count($out_g[0]) - 1; $z++) {
                                if (isset($out_g[0][$z]) && ! empty($out_g[0][$z])) {
                                    $result_data['w77yz4j'] .= $out_g[0][$z] . ',';
                                }
                            }
                        }
                    }
                    if(isset($result_data['w77yz4j'])) $result_data['w77yz4j'] = array_map('trim', explode(',',trim($result_data['w77yz4j'], ','))); // todo exadd +
                }

                // - Speed td
                $speed_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(4)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(4)->nodeValue
                    : null;
                if($speed_td == 'Speed') {
                    $speed = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)][5]/td')->item(1)->nodeValue;
                    $speed_info = explode(',', trim($speed));
                    if(isset($speed_info[0]) && !empty($speed_info[0]) && strpos($speed_info[0], 'HSPA') != false) {
                        $speed_hspa = trim(str_replace(['HSPA'], '', $speed_info[0]));
                        if(!empty($speed_hspa)) $result_data['uointeq3'] = $speed_hspa;

                    }
                    if(isset($speed_info[1]) && !empty($speed_info[1]) && strpos($speed_info[1], 'LTE') != false) {
                        $speed_lte = trim(str_replace(['LTE', 'LTE-A'],'', $speed_info[1]));
                        if(!empty($speed_lte))  { // todo exadd +
                            preg_match('/\d+\s+Mbps/mui', $speed_lte, $out_speed_lte);
                            if(isset($out_speed_lte[0]) && !empty($out_speed_lte[0])) {
                                $result_data['p4zld9l'] = str_ireplace(['Mbps'],'',$out_speed_lte[0]); // todo exadd +
                            }
                        }
                    }
                }

                // - GPRS td
                $gprs_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(5)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(5)->nodeValue
                    : null;
                if($gprs_td == 'GPRS') {
                    // get gprs
                    $technology_gprs = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)][6]/td')->item(1)->nodeValue ?? '';
                    $result_data['de60w8u'] = getAnswer($technology_gprs);
                }

                // - EDGE td
                $edge_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(6)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(6)->nodeValue
                    : null;
                if($edge_td == 'EDGE') {
                    // get edge
                    $technology_edge = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)][7]/td')->item(1)->nodeValue ?? '';
                    $result_data['o3kmrtz'] = getAnswer($technology_edge);
                }
                break;
            case 'Launch':
                // - Announced td
                $announced_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue
                    : null;
                if($announced_td == 'Announced') {
                    // get launch
                    $launch_announced = $htmlDom->query('//table['.$i.']/tr')->item(0)->nodeValue;
                    preg_match_all('/[0-9]+\,\s+[\w]+/mu', $launch_announced, $output_array);
                    if(!empty($output_array[0])) { // todo exadd + all
                        $launch_announced_info = $output_array[0][0];
                        $launch_announced_info = explode(',', $launch_announced_info);
                        $launch_month = strftime("%m",strtotime($launch_announced_info[1]));
                        $result_data['zgxvylx'] = $launch_month.'/'.$launch_announced_info[0];
                    }
                }

                // - Status td
                $status_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue
                    : null;
                if($status_td == 'Status') {
                    // get status available
                    $status_available = $htmlDom->query('//table['.$i.']/tr')->item(1)->nodeValue;
                }
                break;
            case 'Body':
                // - Dimensions td
                $dimensions_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue
                    : null;
                if($dimensions_td == 'Dimensions') {
                    // get body dimensions
                    $body_dimensions = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(0)->nodeValue ?? null;
                    if($body_dimensions) {
                        $body_info = explode('x', $body_dimensions);
                        if(isset($body_info[0])) $result_data['qorav98'] = $body_info[0]; //
                        if(isset($body_info[1])) $result_data['65ihv16'] = trim($body_info[1]);
                        if(isset($body_info[2])) {
                            preg_match('/[\d]+[\.\,]*[\d]*\s+mm/ui', $body_info[2], $output_array);
                            if(!empty($output_array[0])) $result_data['vbryix7'] = str_replace([' mm','mm','thickness'], [''], $output_array[0]); // todo exadd +
                        }

                        /* // todo exadd + delete this
                        preg_match('/[\d]+[\.|\,]*[\d]+\sx\s[\d]+[\.|\,]*[\d]+\sx\s[\d]+[\.|\,]*[\d]+\s+mm/ui', $body_dimensions, $output_array);
                        if(isset($output_array[0]) && !empty($output_array[0])) {
                            $result_data['ly7hk3b'] = $output_array[0];
                        }
                        */
                    }
                }

                // - Weight td
                $weight_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue
                    : null;
                if($weight_td == 'Weight') {
                    // get weight
                    $body_weight = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(1)->nodeValue;
                    preg_match('/[\d]+/ui', $body_weight, $output_array);
                    if(!empty($output_array[0])) {
                        $result_data['uanzwi8'] = $output_array[0];
                    }
                }

                // - Build td
                $build_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue
                    : null;
                if($build_td == 'SIM') {
                    // get SIM
                    $sim = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(2)->nodeValue ?? '';
                    if($sim)  {
                        $sim_new = getSim($sim);
                        if($sim_new) {
                            $result_data['8q7wrlul'] = isValueExists($sim_new, 'Nano-SIM');
                            $result_data['0q3ucnsi'] = isValueExists($sim_new, 'dual stand-by');
                            $result_data['lawrulap'] = isValueExists($sim_new, 'Micro-SIM');
                        }
                    }
                } else if($build_td == 'Build') {
                    // get body material // todo exadd +
                    $body_material = $htmlDom->query('//table[' . $i . ']/tr/td[2]')->item(2)->nodeValue ?? null;
                    if ($body_material) { // todo exadd + all
                        // get body
                        preg_match('/(\w+)\s+body/uim', $body_material, $out_body);
                        if (isset($out_body[1]) && ! empty($out_body[1])) {
                            $result_data['zwkph17b'] = strtolower(trim($out_body[1]));
                        }
                        // get back cover
                        preg_match('/back\s+(\w+)/uim', $body_material, $out_back_cover);
                        if (isset($out_back_cover[1]) && ! empty($out_back_cover[1])) {
                            $result_data['3bjbzry'] = ucfirst(trim($out_back_cover[1])); // todo exadd +
                        } else {
                            preg_match('/,(.+)\s+[\w+]+\s+&\s+back/uim', $body_material, $out_back_cover);
                            if (isset($out_back_cover[1]) && ! empty($out_back_cover[1])) {
                                $result_data['3bjbzry'] = ucfirst(trim($out_back_cover[1])); // todo exadd +
                            }
                        }
                        // get frame
                        preg_match('/,(.+)\s+frame/uim', $body_material, $out_frame);
                        if (isset($out_frame[1]) && ! empty($out_frame[1])) {
                            $result_data['3bjbzra'] = ucfirst(trim($out_frame[1]));
                        }
                    }

                    // get SIM
                    $sim = $htmlDom->query('//table[' . $i . ']/tr/td[2]')->item(3)->nodeValue ?? ''; //
                    $result_data['8q7wrlul'] = isValueExists($sim, 'Nano-SIM');
                    $result_data['0q3ucnsi'] = isValueExists($sim, 'dual stand-by');
                    $result_data['lawrulap'] = isValueExists($sim, 'Micro-SIM');
                }

                // - 'No name' features
                $body_features_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item(3)->nodeValue ?? null;
                if($body_features_td != null) {
                    // get body specifics
                    $body_specs = $htmlDom->query('//table['.$i.']/tr')->item(4)->nodeValue ?? '';
                    if($body_specs) {
                        if(getWaterResistant($body_specs) != null) $result_data['cxeplx1'] = getWaterResistant($body_specs);
                        if(getDustResistant($body_specs) != null) $result_data['cxeplx3'] = getDustResistant($body_specs);
                    }
                }
                break;
            case 'Display':
                // - Type td
                $type_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue
                    : null;
                if($type_td && $type_td == 'Type') {
                    // get display type & colors
                    $display_type = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(0)->nodeValue ?? '';
                    if($display_type) {
                        if(strpos($display_type, 'onochrome') != false) {
                            $result_data['xxyv5nx'] = 'Monochrome';
                            $result_data['8vzzca7'] = 1;
                        } else {
                            $display = str_replace(['capacitive', 'touchscreen', 'Capacitive'],'', explode(',', $display_type)[0]);
                            if(!empty($display)) { // todo exadd + all
                                if(!preg_match('/\d+K|\d+M/uim', $display)) {
                                    $result_data['xxyv5nx'] = str_replace('AMOLED or SLCD', 'AMOLED', $display);
                                } else {
                                    preg_match('/\d+K|\d+M/uim', $display, $out_colors);
                                    if(isset($out_colors[0]) && !empty($out_colors[0])) $result_data['8vzzca7'] = $out_colors[0];
                                }
                            }
                        }
                        $display_colors = explode(',', $display_type)[1] ?? '';
                        if($display_colors) {
                            preg_match('/[\d]+M|[\d]+K/ui', $display_colors, $output_array);
                            if(isset($output_array[0]) && !empty($output_array[0])){
                                $result_data['8vzzca7'] = trim($output_array[0]);
                            }
                        }
                    }
                }

                // - Size td
                $size_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue
                    : null;
                if($size_td && $size_td == 'Size') {
                    // get display size
                    $display_size = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(1)->nodeValue;
                    $display_info = explode(',', $display_size);

                    if($display_info) {
                        preg_match('/[\d]+[\.|\,][\d]+/ui', $display_info[0], $output_array);
                        if(!empty($output_array)) {
                            $result_data['1n820fz'] = $output_array[0];
                        }
                    }

                    if(isset($display_info[1])) {
                        preg_match('/\(~([\d]+[\.|\,][\d]+)/ui', $display_info[1], $output_array);
                        if(!empty($output_array[1])) {
                            $result_data['zq2ektp'] = round($output_array[1]); // todo exadd +
                        }
                    }
                }

                // - Resolution td
                $resolution_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue
                    : null;
                if($resolution_td && $resolution_td == 'Resolution') {
                    // get resolution
                    $display_resolution = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(2)->nodeValue;
                    preg_match('/([\d]+\sx\s[\d]+)\spix/mi', $display_resolution, $resol_info);
                    if(isset($resol_info[1]) &&!empty($resol_info[1])) {
                        $width = explode('x', $resol_info[1])[0];
                        $height = explode('x', $resol_info[1])[1];
                        $result_data['j2p7bju'] = trim($height);
                        $result_data['nggks18'] = trim($width);
                    }
                    preg_match('/([\d]+)\sppi/mi', $display_resolution, $ppi_info);
                    if(isset($ppi_info[1]) && !empty($ppi_info[1])) {
                        $result_data['7x8x76o'] = trim($ppi_info[1]);
                    }
                }

                // - Multitouch
                $multitouch_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue
                    : null;
                if($multitouch_td && $multitouch_td == 'Multitouch') {
                    // get multitouch
                    $is_multitouch = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(3)->nodeValue;
                    $result_data['alrhep0'] = getAnswer($is_multitouch);
                }

                // - Protection td
                $protection_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(4)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(4)->nodeValue
                    : null;
                if($protection_td && $protection_td == 'Protection') {
                    //get display protection
                    $display_protection = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(4)->nodeValue ?? null;
                    $result_data['59e6c9r'] = str_replace(['(unspecified version)','(market dependent)','To be confirmed'], [''], $display_protection); // todo exadd +
                }

                // - 'No name' features
                $display_features_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(5)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(5)->nodeValue
                    : null;
                if($display_features_td && $display_features_td != null) {
                    // get display specs
                    $display_specs = $htmlDom->query('//table['.$i.']/tr')->item(5)->nodeValue ?? null;
                    if($display_specs) { // todo exadd +
                        $result_data['y23jcrlz'] = isValueExists($display_specs, 'Wide Colour Gamut');
                        $result_data['y20jcrlz'] = isValueExists($display_specs, '120 Hz');
                        $result_data['ywkph18a'] = isValueExists($display_specs, 'DCI-P3'); // todo exadd +
                    }
                }
                break;
            case 'Platform':
                // - OS td
                $os_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue
                    : null;
                if($os_td && $os_td == 'OS') {
                    // get platform O
                    $platform_os = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(0)->nodeValue;

                    // get type of device
                    $not_gsm_device = $htmlDom->query('//span[@id="non-gsm"]/text()')->item(0)->nodeValue ?? null;
                    $watch_os = getWatchOs($platform_os);

                    if($watch_os || !empty($not_gsm_device)) {
                        $result_data['drbmx1r'] = 2;
                    } else {
                        $result_data['drbmx1r'] = 1;
                    }


                    $available_os = explode(',', $platform_os); // todo exadd +
                    $result_data['ui65qcn'] = $available_os[0]; // get os
                    if(isset($available_os[1]) && !empty($available_os[1])) {  // todo exadd +
                        $result_data['ui71qcn'] = trim(str_ireplace(['upgradable to','upgradаble to','planned upgrade to'], [''], trim($available_os[1]))); // todo exadd +
                    }

                    $result_data['0v8w2sz'] = getOS($platform_os, 'iOS');
                    $result_data['a5sj3l2'] = getOS($platform_os, 'indow');
                    $result_data['vxq3g1f'] = getOS($platform_os, 'BlackBerry');
                    $result_data['llulwif'] = getOS($platform_os, 'ndroid');
                }

                // - Chipset td
                $chipset_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue
                    : null;
                if($chipset_td && $chipset_td == 'Chipset') {
                    $chipset = $htmlDom->query('//table[5]/tr')->item(1)->nodeValue;
                }

                // CPU td
                $cpu_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue
                    : null;
                if($cpu_td && $cpu_td == 'CPU') { // todo exadd all +
                    // get CPU
                    $cpu = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(2)->nodeValue;
                    $cpu_info = explode(' ', $cpu);

                    // core
                    if(isset($cpu_info[0]) && !empty($cpu_info[0])) { // todo exadd +
                        if(in_array($cpu_info[0],['Deca-core', 'Dual-core','Quad-core','Octa-core','Hexa-core'])) {
                            $result_data['y5xo6x5'] = intval( str_ireplace(['Deca-core', 'Dual-core','Quad-core','Octa-core','Hexa-core'],['10','2','4','8','6'], $cpu_info[0]) ); // core //  todo exadd +
                        }
                    }

                    // cpu freq
                    preg_match_all('/\d+x\d+\.\d+|\d+\.\d+\s+/uim', $cpu, $out_cpu_freg); // todo exadd +
                    if(isset($out_cpu_freg[0][0]) && !empty($out_cpu_freg[0][0])) {
                        $result_data['y5xo6x6'] = trim($out_cpu_freg[0][0]);
                    }
                    if(isset($out_cpu_freg[0][1]) && !empty($out_cpu_freg[0][1])) {
                        $result_data['y5xo6x6'] .= ' & '. trim($out_cpu_freg[0][1]); // freq
                    }

                    if(isset($result_data['y5xo6x5']) && $result_data['y5xo6x5'] != '8') { // todo exadd +
                        if(isset($out_cpu_freg[0][2]) && !empty($out_cpu_freg[0][2])) { // todo exadd +
                            $result_data['y5xo6x6'] .= ' & '. trim($out_cpu_freg[0][2]); // freq
                        }
                    }


                    preg_match_all('/ghz\s+(\w+-*[^)]\d*)\s*/uim', $cpu, $out_cpu_main);
                    if(isset($out_cpu_main[1]) && !empty($out_cpu_main[1])) {
                        $result_data['y5xo6x4'] = trim($out_cpu_main[1][0]); // todo exadd +
                    }
                    if(isset($out_cpu_main[1][1]) && !empty($out_cpu_main[1][1])) {
                        if($out_cpu_main[1][1] != $result_data['y5xo6x4'] || (trim($out_cpu_main[1][1]) == 'Cortex')) { // || trim($out_cpu_main[1][1]) != 'Cortex'
                            $result_data['y5xo6x4'] .= ' & ' . trim($out_cpu_main[1][1]);
                        }
                    }
                }

                // GPU td
                $gpu_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue
                    : null;
                if($gpu_td && $gpu_td == 'GPU') {
                    $gpu = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(3)->textContent ?? null;
                    if($gpu) {
                        $gpu_into = preg_split("/- EMEA/ui", $gpu, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE ); // todo exadd +
                        if(isset($gpu_into[0][0]) && !empty($gpu_into[0][0])) {
                            preg_match('/Adreno\s+\d+/', $gpu_into[0][0], $out_adreno_gpu);
                            if(isset($out_adreno_gpu[0]) && !empty($out_adreno_gpu[0])) {
                                $result_data['4kzmswo'][] = trim($out_adreno_gpu[0]); // todo exadd +
                            } else {
                                $result_data['4kzmswo'][] = trim($gpu_into[0][0]); // todo exadd +
                            }
                            if(isset($gpu_into[1][0]) && !empty($gpu_into[1][0])) {
                                preg_match('/Adreno\s+\d+/', $gpu_into[1][0], $out_other_gpu);
                                if(isset($out_other_gpu[0]) && !empty($out_other_gpu[0])) {
                                    $result_data['4kzmswo'][] = trim($out_other_gpu[0]); // todo exadd +
                                } else {
                                    $result_data['4kzmswo'][] = trim($gpu_into[1][0]); // todo exadd +
                                }
                            }
                        }
                    }
                }
                break;
            case 'Memory':
                // - Card slot td
                $card_slot_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue
                    : null;
                if($card_slot_td && $card_slot_td == 'Card slot') {
                    // get memory card slot
                    $is_memory_card_slot = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(0)->nodeValue;
                    if(strpos($is_memory_card_slot, 'No') !== false) { // todo exadd +
                        $result_data['yz90cwl'] = '-';
                    } else {
                        $result_data['yz90cwl'] = '+';
                    }
                }

                // - Internal
                $internal_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue
                    : null;
                if($internal_td && $internal_td == 'Internal') {
                    $memory_internal = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(1)->nodeValue ?? null;
                }
                break;
            case 'Camera':
                // - Primary td
                $primary_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue
                    : null;
                if($primary_td && $primary_td == 'Primary') {
                    // get camera primary // todo exadd + all
                    $cam_primary = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(0)->nodeValue;
                    $cam_primary_info = getPrimaryCamera($cam_primary);
                    $result_data['gn4gn6xk'] = isValueExists($cam_primary_info, 'utofocus');
                    $result_data['zrru3eek'] = isValueExists($cam_primary_info, 'flash');
                    // get led flash
                    $result_data['jefetfa2'] = isValueExists($cam_primary_info, 'LED'); // todo exadd +
                }

                // - Features td
                $features_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue
                    : null;
                if($features_td && $features_td == 'Features') {
                    // get camera features
                    $cam_features = $htmlDom->query('//table['.$i.']/tr')->item(1)->nodeValue ?? null;
                    if($cam_features) {
                        // get panorama
                        $result_data['gn4gn6xz'] = isValueExists($cam_features, 'anorama'); // todo exadd +
                    }
                }

                // - Video td
                $video_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue
                    : null;
                if($video_td && $video_td == 'Video') {
                    // get camera video
                    $cam_video = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(2)->nodeValue;
                    if(isset($cam_video)) {
                        $cam_info_new = explode(',', $cam_video)[0]; // video 1; // todo exadd +
                        preg_match('/\d+p/mui', $cam_info_new, $out_cam); // todo exadd +
                        if(isset($out_cam[0]) && !empty($out_cam[0])) {
                            $result_data['t9q0h7hd'] = trim($out_cam[0]);  // todo exadd +
                        }

                        if(isset(explode(',', $cam_video)[1])) { // todo exadd +
                            $cam_video_2 = trim(explode(',', $cam_video)[1]); // video 2;
                            preg_match('/\d+p/mui', $cam_video_2, $out_video_2);
                            if(isset($out_video_2[0]) && !empty($out_video_2[0])) {
                                $result_data['8041luk6'] = $out_video_2[0];
                            }

                        }
                    }
                }

                // - Secondary td
                $secondary_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue
                    : null;
                if($secondary_td && $secondary_td == 'Secondary') {
                    // get camera secondary
                    $cam_secondary =  $htmlDom->query('//table['.$i.']/tr/td[2]')->item(3)->nodeValue;
                    $cam_secondary_info = explode(',', $cam_secondary); // todo exadd +
                    if(isset($cam_secondary_info[0])) {
                        preg_match('/(\d+)\s+MP/mui', $cam_secondary_info[0], $out_sec_cam); // todo exadd +
                        if(isset($out_sec_cam[1]) && !empty($out_sec_cam[1])) {
                            $result_data['06wzu4yz'] = $out_sec_cam[1].'MP'; // todo exadd +
                        }
                        if(isset($cam_secondary_info[1])) {
                            // get led flash
                            if(!isset($result_data['jefetfa2'])) $result_data['jefetfa2'] = isValueExists($cam_secondary_info[1], 'LED');
                        }
                    } else {
                        preg_match('/(\d+)\s+MP/mui', $cam_secondary, $out_sec_cam); // todo exadd +
                        if(isset($out_sec_cam[1]) && !empty($out_sec_cam[1])) {
                            $result_data['06wzu4yz'] = $out_sec_cam[1].'MP'; // todo exadd +
                        }
                    }
                }

                if(empty($result_data['lggn0m2'])) $result_data['lggn0m2'] = '-';
                if(empty($result_data['t9q0h7hd'])) $result_data['t9q0h7hd'] = '-';

                break;
            case 'Sound':
                // - Alert types td
                $alert_types_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(0)->nodeValue
                    : null;
                if($alert_types_td && $alert_types_td == 'Alert types') {
                    // get sound alert types
                    $sound_alerts = $htmlDom->query('//table['.$i.']/tr')->item(0)->nodeValue;
                    $result_data['u8sj5wc'] = isValueExists($sound_alerts, 'ibratio');
                }

                // - Loudspeaker td
                $loudspeaker_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue
                    : null;
                if($loudspeaker_td && $loudspeaker_td == 'Loudspeaker ') {
                    // get sound loudspeaker
                    $sound_loudspeaker = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(1)->nodeValue;
                    $result_data['8l2ljo2'] = getAnswer($sound_loudspeaker);
                    $result_data['yh7xh42'] = isValueExists($sound_loudspeaker, 'THX'); // todo exadd +
                    // get dual loudspeaker
                    $result_data['yq2jcrl2'] = isCodecExists($sound_loudspeaker, 'dual'); // todo exadd +
                }

                // - 3.5mm jack
                $audio_jack_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue
                    : null;
                if($audio_jack_td && $audio_jack_td == '3.5mm jack ') {
                    // get sound 3.5m jack
                    $sound_jack = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(2)->nodeValue;
                    $result_data['yh7xh3q'] = getAnswer($sound_jack);
                }

                // - 'No name' features
                $sound_features_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item(3)->nodeValue ?? null;
                if($sound_features_td && $sound_features_td != null) {
                    // get sound specs
                    $sound_specs = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(3)->nodeValue ?? null;
                    if($sound_specs) {
                        // get hi-fi audio
                        if(strpos($sound_specs, '24-bit/192kHz')) { // todo exadd +
                            $result_data['yh7xh38'] = '+';
                        }
                        // get Dirac HD sound
                        $result_data['yh7xh43'] = isValueExists($sound_specs, 'Dirac HD sound'); // todo exadd +
                    }
                }

                break;
            case 'Comms':
                for($k = 0; $k <= 7;$k++) {
                    $coms_value_td = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item($k)->nodeValue ?? null;
                    switch($coms_value_td) {
                        case 'WLAN':
                            // get comms wlan
                            $comms_wlan = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue ?? null;
                            if($comms_wlan) {
                                $wlan_info = getWlan($comms_wlan);
                                if($wlan_info) $result_data['2pinrcv'] = $wlan_info;
                            }
                            break;
                        case 'Bluetooth':
                            // get comms bluetooth
                            $comms_bluetooth = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue;
                            preg_match_all('/([0-9]\.[0-9])/mu', $comms_bluetooth, $output_array);
                            if(isset($output_array[0][0])) $result_data['p4zld5l'] = $output_array[0][0];

                            // get a2dp
                            $result_data['yh7xh59'] = isValueExists($comms_bluetooth,'A2DP'); // todo exadd +
                            // get LE (low energy)
                            $result_data['8q7wrluz'] = isValueExists($comms_bluetooth,'LE'); // todo exadd +
                            break;
                        case 'GPS':
                            // get comms gps & glonass
                            $comms_gps = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue;
                            $result_data['yfvshn2'] = getAnswer($comms_gps);
                            $result_data['39ji8mm'] = isValueExists($comms_gps, 'GLON');
                            $result_data['x1xgsbl'] = isValueExists($comms_gps, 'A-GPS'); // todo exadd +
                            break;
                        case 'NFC':
                            // get comms nfc
                            $comms_nfc = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue;
                            $result_data['9ee4viy'] = getAnswer($comms_nfc);
                            break;
                        case 'Infrared port':
                            // get comms infrared
                            $comms_nfc = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue;
                            $result_data['hwst1n7'] = getAnswer($comms_nfc);
                            break;
                        case 'Radio':
                            // get comms radio
                            $comms_radio = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue; // todo exadd +
                            $result_data['tix99ot'] = getAnswerNo($comms_radio);
                            // get RDS
                            if(stripos(trim($comms_radio),'RDS') !== false) $result_data['p4zld1l7'] = getAnswerNo($comms_radio); // todo exadd +
                            break;
                        case 'USB':
                            // get comms usb
                            $comms_usb = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue;
                            preg_match('/^No$/u', $comms_usb, $output_array);
                            if(!empty($output_array[0])) {
                                $result_data['2q8o92fk'] = '-';
                            }

                            // get type-c
                            if(isValueExists($comms_usb, 'ype-C') == '+') {
                                preg_match('/\d+[\.*|\,*]\d+[\s+\w+]+/uim', $comms_usb, $out_type_c);
                                if(isset($out_type_c) && !empty($out_type_c[0])) {
                                    $result_data['rmjj6m5t'] = str_ireplace(['reversible connector'], [''], trim($out_type_c[0])); // todo exadd +
                                }
                            }

                            $result_data['99rtfuj'] = isValueExists($comms_usb, 'agnetic');
                            $result_data['0arcae64'] = isValueExists($comms_usb,'2');
                            $result_data['p7s2uenu'] = isValueExists($comms_usb, '3');
                            $result_data['p85t8s8z'] = isValueExists($comms_usb, 'micro');
                            $result_data['9qsw0l7d'] = isValueExists($comms_usb, 'On-The-Go');
                            break;
                    }
                }
                break;
            case 'Features':
                for($k = 0; $k <= 5; $k++) {
                    $features_value_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item($k)->nodeValue))
                        ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item($k)->nodeValue
                        : null;
                    switch(true) {
                        case ($features_value_td == 'Sensors'):
                            // get features sensors
                            $feat_sensors = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue;
                            $result_data['h1ddzrt'] = isValueExists($feat_sensors, 'ccelerometer');
                            $result_data['ywtcejg'] = isValueExists($feat_sensors, 'gyro');
                            $result_data['x399jxz'] = isValueExists($feat_sensors, 'barometer');
                            $result_data['x0xgsbn'] = isValueExists($feat_sensors, 'compass');
                            $result_data['c4awfagk'] = isValueExists($feat_sensors, 'Face');
                            $result_data['rsub3l9c'] = isValueExists($feat_sensors, 'Fingerprint');
                            $result_data['h88pkmd1'] = isValueExists($feat_sensors, 'oximity'); // todo exadd +
                            break;
                        case ($features_value_td == 'Messaging'):
                            // get features messaging
                            $feat_messaging = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue ?? null;
                            if($feat_messaging) {
                                $result_data['8q7wrlu5'] = isValueExists($feat_messaging, 'IM'); // todo exadd +
                            }
                            break;
                        case ($features_value_td == 'Browser'):
                                // get browser
                                $feat_browser = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                                $result_data['fdfoul1'] = str_ireplace(['Yes','No'],['+','-'],$feat_browser); // todo exadd +
                            break;
                        case (strlen($features_value_td) == 2): // todo exadd + all
                            // get features audio & video formats
                            $feat_browser = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            $result_data['f7lsmmw9'] = isCodecExists($feat_browser, 'MP3');
                            $result_data['x055z520'] = isCodecExists($feat_browser, 'MP4');
                            $result_data['8j6be1ko'] = isCodecExists($feat_browser, 'DivX'); // todo exadd +
                            $result_data['xc4bb9kc'] = isCodecExists($feat_browser, 'XviD');
                            $result_data['crrbpcar'] = isCodecExists($feat_browser, 'H.265');
                            $result_data['xd942mit'] = isCodecExists($feat_browser, 'WMV');
                            $result_data['am1zgml8'] = isCodecExists($feat_browser, 'WAV');
                            $result_data['t1inmosa'] = isCodecExists($feat_browser, 'FLAC');
                            $result_data['lnk8dr8h'] = isCodecExists($feat_browser, 'eAAC');
                            $result_data['7zq7neoh'] = isCodecExists($feat_browser, 'WMA');
                            $result_data['tfuq45ng'] = isCodecExists($feat_browser, 'AAX');
                            $result_data['f3n8nqp4'] = isCodecExists($feat_browser, 'AIFF');
                            $result_data['f3n8nq17'] = isCodecExists($feat_browser, 'ASF'); // todo exadd +
                            $result_data['f3n8nq18'] = isCodecExists($feat_browser, 'FLV'); // todo exadd +
                            $result_data['f3n8nq19'] = isCodecExists($feat_browser, 'M4V'); // todo exadd +
                            $result_data['f3n8nq20'] = isCodecExists($feat_browser, 'WEBM'); // todo exadd +
                            $result_data['f3n8nq21'] = isCodecExists($feat_browser, '3G2'); // todo exadd +
                            $result_data['f3n8nq22'] = isCodecExists($feat_browser, '3GP'); // todo exadd +
                            $result_data['f3n8nqp5'] = isCodecExists($feat_browser, 'AWB'); // todo exadd +
                            $result_data['f3n8nqp6'] = isCodecExists($feat_browser, 'DFF'); // todo exadd +
                            $result_data['f3n8nqp7'] = isCodecExists($feat_browser, 'IMY'); // todo exadd +
                            $result_data['f3n8nqp8'] = isCodecExists($feat_browser, 'RTX'); // todo exadd +
                            $result_data['f3n8nqp9'] = isCodecExists($feat_browser, 'OGA'); // todo exadd +
                            $result_data['f3n8nq11'] = isCodecExists($feat_browser, 'OTA'); // todo exadd +
                            $result_data['f3n8nq10'] = isCodecExists($feat_browser, 'MXMF'); // todo exadd +
                            $result_data['f3n8nq13'] = isCodecExists($feat_browser, 'AMR'); // todo exadd +
                            $result_data['f3n8nq14'] = isCodecExists($feat_browser, 'APE'); // todo exadd +
                            $result_data['f3n8nq15'] = isCodecExists($feat_browser, 'DSF'); // todo exadd +
                            $result_data['f3n8nq16'] = isCodecExists($feat_browser, 'OGG'); // todo exadd +
                            $result_data['f3n8nq23'] = isCodecExists($feat_browser, 'PCM'); // todo exadd +

                            // get document viewer // todo exadd +
                            $result_data['yq2jcrl9'] = isValueExists($feat_browser, 'ocument viewer'); // todo exadd +

                            // get document editor // todo exadd +
                            $result_data['yq2jcr10'] = isValueExists($feat_browser, 'ocument editor'); // todo exadd +

                            // get wireless charging
                            $result_data['xc2onhy'] = isValueExists($feat_browser, 'eless charging');

                            // get features specs
                            $feat_specs = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue ?? null;
                            $result_data['27s8wl4'] = isValueExists($feat_specs, 'ast battery');
                            break;
                        case ($features_value_td == 'Alarm'):
                            $alarm_info = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            if($alarm_info) $result_data['iw93r5f8'] = getAnswer($alarm_info);
                            break;
                        case ($features_value_td == 'Clock'):
                            $clock_info = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            if($clock_info) $result_data['qfkph10b'] = getAnswer($clock_info);
                            break;
                    }
                }

                break;

            case 'Battery':
                // - 'No name' removable td
                $removable_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item(0)->nodeValue ?? null;
                if($removable_td) {
                    // get battery specs
                    $battery = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(0)->nodeValue;
                    $result_data['c220c9j'] = getBatteryReplacement($battery);
                }

                for($k = 1; $k <= 2; $k++ ) {
                    $value_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item($k)->nodeValue ?? null;
                    switch($value_td) {
                        case 'Talk time':
                            // get battery talk time
                            $battery_talk_time = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            preg_match('/(\d+)\s+h\s+\(3G\)/uim', $battery_talk_time, $out_talk_time); // todo exadd +
                            if(isset($out_talk_time[1]) && !empty($out_talk_time[1])) {
                                $result_data['zuqqmwi3'] = $out_talk_time[1];
                            }

                            break;
                        case 'Music play':
                            // get battery music play time
                            $battery_music_time = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            preg_match('/(\d+)/uim', $battery_music_time, $out_music_play);
                            if(isset($out_music_play[0]) && !empty($out_music_play[0])) { // todo exadd +
                                $result_data['6ojsm29w'] = $out_music_play[0];
                            }

                            break;
                        case 'Stand-by':
                            // get battery music play time
                            $battery_stand_time = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            preg_match('/([\d]{2,})\s+h/umi', $battery_stand_time, $output_array);
                            if(isset($output_array[1]) && !empty($output_array[1])) {
                                $result_data['qldrwtm'] = $output_array[1];
                            } else {
                                $result_data['qldrwtm'] = '';
                            }
                            break;
                    }
                }
                break;
            case 'Misc':
                for($k = 0; $k <= 2; $k++) {
                    $value_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item($k)->nodeValue ?? null;
                    switch($value_td) {
                        case 'Colors':
                            // get misc specs
                            $misc_colors = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            if($misc_colors) {
                                $misc_colors_info = preg_replace('/\d+\s+-\s+/uim', "", $misc_colors); // todo exadd +
                                $result_data['ywkph10b'] = array_map('trim', explode(',', $misc_colors_info)); // todo exadd +
                            }
                            break;
                        case 'Price':
                            // get misc price
                            $misc_price = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue ?? null;
                            $price = preg_match_all('/([0-9]+\s+[A-Z]+)/mu', $misc_price, $output_array);
                            if(isset($output_array[0][0])) { // todo exadd + all
                                $price_info = explode(' ', $output_array[0][0]);
                                $result_data['3n68sce'] = [$price_info[1] => $price_info[0]];
                            }
                            break;
                        case 'SAR';
                            // get SAR value
                            $sar = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            if($sar) {
                                $sar_info = preg_split('/\s{2,}/ui', trim($sar));
                                $sar_info = array_diff($sar_info, array(''));
                                if(isset($sar_info[0]))  $result_data['5cp2ol9j'] = str_replace(['(head)','W/kg'], [''], $sar_info[0]); // todo exadd +
                                if(isset($sar_info[1])) $result_data['owpcmmmy'] = str_replace(['(body)','W/kg'], [''], $sar_info[1]); // todo exadd +
                            }
                        break;
                        case 'SAR EU';
                            // get SAR EU value
                            $sar_eu = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            if($sar_eu) {
                                $sar_info = preg_split('/\s{2,}/ui', trim($sar_eu));
                                $sar_info = array_diff($sar_info, array(''));
                                if(isset($sar_info[0]))  $result_data['psbzu2e9'] = str_replace(['(head)','W/kg'], '', $sar_info[0]);
                                if(isset($sar_info[1])) $result_data['uuapl9gw'] = str_replace(['(body)','W/kg'], '', $sar_info[1]);
                            }
                    }
                }
                break;
            case 'Tests': // todo exadd + all case 'Test'
                for($k = 0; $k <= 5; $k++) {
                    $tests_value_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item($k)->nodeValue ?? null;
                    switch($tests_value_td) {
                        case 'Performance':
                            // get test info
                            $test_performance = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
//                            $result_data['q85w6qmq'] = trim($test_performance);

                            preg_match('/Basemark OS II:\s*(\d+)/uim', $test_performance, $out_basemark); // todo exadd +
                            if(isset($out_basemark[1]) && !empty($out_basemark[1])) { $result_data['a7xo6x6'] = trim($out_basemark[1]);} // basemark os

                            preg_match('/Basemark OS II 2.0:\s*(\d+)/uim', $test_performance, $out_basemark_two); // todo exadd +
                            if(isset($out_basemark_two[1]) && !empty($out_basemark_two[1])) { $result_data['a7xo1x6'] = trim($out_basemark_two[1]);} // basemark os 2

                            preg_match('/Basemark X:\s*(\d+)/uim', $test_performance, $out_basemark_x); // todo exadd +
                            if(isset($out_basemark_x[1]) && !empty($out_basemark_x[1])) { $result_data['a8xo6x6'] = trim($out_basemark_x[1]);} // basemark x
                            break;
                        case 'Display':
                            // get test display
                            $test_display = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue ?? null; // todo exadd +
                            if($test_display) {
                                preg_match('/(\d+):*\d*\s+\(nominal\)/uim', $test_display, $out_nominal_contrast);
                                if(isset($out_nominal_contrast[1]) && !empty($out_nominal_contrast[1])) $result_data['y22jcrla'] = $out_nominal_contrast[1]; // nominal contrast
                                preg_match('/(\d+\.*\d*)\s+\(sunlight\)/uim', $test_display, $out_sunlight_contrast); // todo exadd +
                                if(isset($out_sunlight_contrast[1]) && !empty($out_sunlight_contrast[1])) $result_data['y23jcrla'] = $out_sunlight_contrast[1]; // sunlight contrast
                            }
                            break;
                        case 'Camera':
                            // get test camera
                            $test_camera = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue ?? null;
                            break;
                        case 'Loudspeaker':
                            // get test loudspeaker
                            $test_loudspeaker = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue ?? null;
                            if($test_loudspeaker) { // todo exadd +
                                $test_loudspeaker_value = explode('/', $test_loudspeaker);
                                if(isset($test_loudspeaker_value[0]) && !empty($test_loudspeaker_value[0])) {
                                    preg_match('/\d+/ui', $test_loudspeaker_value[0], $out_loudspeak_voice);
                                    if(isset($out_loudspeak_voice[0]) && !empty($out_loudspeak_voice[0])) $result_data['yh7xh39'] = $out_loudspeak_voice[0];
                                }
                                if(isset($test_loudspeaker_value[1]) && !empty($test_loudspeaker_value[1])) {
                                    preg_match('/\d+/ui', $test_loudspeaker_value[1], $out_loudspeak_noise);
                                    if(isset($out_loudspeak_noise[0]) && !empty($out_loudspeak_noise[0])) $result_data['yh7xh40'] = $out_loudspeak_noise[0];
                                }
                                if(isset($test_loudspeaker_value[2]) && !empty($test_loudspeaker_value[2])) {
                                    preg_match('/\d+/ui', $test_loudspeaker_value[2], $out_loudspeak_ring);
                                    if(isset($out_loudspeak_ring[0]) && !empty($out_loudspeak_ring[0])) $result_data['yh7xh41'] = $out_loudspeak_ring[0];
                                }
                            }
                            break;
                        case 'Audio quality':
                            // get test audio quality
                            $test_audio = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            $audio_quality_info = explode('/', $test_audio);
                            $result_data['3t8uo9z6'] = (isset($audio_quality_info[0])) ? getNoiseCrosstalk($audio_quality_info[0]) : null;
                            $result_data['m93am75k'] = (isset($audio_quality_info[1])) ? getNoiseCrosstalk($audio_quality_info[1]) : null;
                            break;
                        case 'Battery life':
                            // get test battery life
                            $test_battery = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            if($test_battery) {
                                preg_match('/(\d+)/uim', $test_battery, $out_rating);
                                if(isset($out_rating[0]) && !empty($out_rating[0])) {
                                    $result_data['qwkph25b']  = trim($out_rating[0]); // todo exadd +
                                }
                            }
                            break;
                    }
                }
                break;
        }
        $result_summary_info[$url] = $result_data; // todo exadd +
    }

    // get device type if don't have platform os
    if(!isset($result_summary_info[$url]['drbmx1r'])) { // todo exadd +
        $not_gsm_device = $htmlDom->query('//span[@id="non-gsm"]/p/text()')->item(0)->nodeValue ?? null;
        if(!empty($not_gsm_device)) {
            $result_summary_info[$url]['drbmx1r'] = 2; // todo exadd +
        } else {
            if(isset($result_summary_info[$url]['qorav98'])) { // todo exadd +
                if($result_summary_info[$url]['qorav98'] > 60) $result_summary_info[$url]['drbmx1r'] = 1; // todo exadd +
            }
            if(isset($result_summary_info[$url]['qorav98'])) { // todo exadd +
                if($result_summary_info[$url]['qorav98'] < 60) $result_summary_info[$url]['drbmx1r'] = 2; // todo exadd +
            }
        }
    }

    // todo exadd +
    $newObj = WebPage::find()->filterWhere(['path_hash' => $path_hash])->one();
    if($newObj) {
        $newObj->path_hash = $path_hash;
        $newObj->source = 'gsmarena';
        $newObj->url = $url;
        $newObj->desc = Json::encode($result_summary_info[$url]);
        $newObj->save();
    }
    //
}
/////////////////////// /. $result_data['...'] info /////
////// test // todo exadd + delete
?>

<?= Render::render($result_summary_info, $code) // todo exadd +?>

<?
// helper functions
function getNoiseCrosstalk($string)
{
    preg_match('/-[\d]+\.[\d]+dB/mu', trim($string), $output_array);
    return $output_array[0] ?? null;
}
function getPrimaryCamera($string)
{
    return preg_split('/,\s+check quality/mu', $string)[0];
}
function getOS($haystack, $string)
{
    if(strpos($haystack, $string) !== false) {
        return '+';
    } else {
        return '-';
    }
}
function getBatteryReplacement($string)
{
    if(strpos($string, 'n-removable') != false){
        return '-';
    } else {
        return '+';
    }
}
function getWaterResistant($string) // todo exadd +
{
    preg_match('/IP\d+/', $string, $output_array);
    if(isset($output_array[0]) && !empty($output_array[0])) {
        return $output_array[0];
    }
    return null;
}
function getDustResistant($str) // todo exadd +
{
    preg_match('/IP\d/', $str, $output_array);
    if(isset($output_array[0]) && !empty($output_array[0])) {
        return $output_array[0].'X';
    } else {
        return null;
    }
}
function getBodyResistant($string)
{
    $first_line_info =  preg_split("/-/mu", $string);
    return trim(implode(',', [$first_line_info[1], $first_line_info[2]]));
}
function getDisplayHeight($string)
{
    preg_match("/[0-9]+/mu", $string, $output_array);
    if(!empty($output_array[0])) return $output_array[0];
}
function getValueInParentheses($string)
{
    preg_match('/\((.+)\)+/mu', $string, $output_array);
    if(!empty($output_array)) {
        return $output_array[1];
    }
}
function isValueExists($item, $name)
{
    if(strpos($item, $name) !== false) {
        return '+';
    } else {
        return '-';
    }
}
function getSim($str)
{
    preg_match('/\s*(.+)/mu', $str, $output_array);
    if(!empty($output_array[0])) return trim($output_array[0]);
}
function getWlan($str)
{
    preg_match('/([0-9]+\.[0-9]+\s+).+/mu', $str, $output_array);
    if(!empty($output_array[0])) return explode(',', $output_array[0])[0];
}
function getAnswer($str)
{
   return  (strpos(trim($str), 'es') != false) ? '+' : '-';
}
function getAnswerNo($str) // todo exadd +
{
    return  (strpos(trim($str), 'No') !== false) ? '-' : '+';
}
function getWatchOs($platform_os)
{
    $watch_os = ['wearable', 'Android OS compatible', 'Android Wear', 'watchOS', 'Proprietary OS', 'Wearable platform', 'Nucleus OS', 'LG Wearable', 'Tencent OS'];
    foreach($watch_os as $item) {
         if(stripos($platform_os, $item) !== false) {
             return true;
         }
    }
    return false;
}
/**
 * for wiki or google api
 *
 * @param $endpoint
 * @param array $params
 * @return mixed
 */
function getDataFromApi($endpoint, $params = [])
{
    $url = makeUrl($endpoint, $params);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1); // 1
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

/**
 * @param $url
 * @param array $urlParams
 * @param array $ignoreParams
 * @return string
 */
function makeUrl($url, $urlParams = [], $ignoreParams = []): string
{
    foreach ($ignoreParams as $key) {
        unset($urlParams[$key]);
    };
    if ( ! empty($urlParams)) {
        $url .= "?" . http_build_query($urlParams);
    }
    return $url;
}
/**
 * @param $content
 * @return DOMXPath
 */
function dom($content): DOMXPath
{
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    return new DOMXpath($doc);
}
//function pretty_print($str)
//{
//    echo '<pre>';
//    print_r($str);
//    echo '</pre>';
//}
function getAllLinks(bool $needXmlQuery) // todo exadd + all
{
    $item_links = [];
    if($needXmlQuery) {
        // get xml query for all urls phones
        $xml = getAllPhones();
        foreach($xml->url as $item) {
            if($item->priority == 0.9) {
                $needed_link = (array)$item->loc[0];
                $item_links[] = getXmlLinks($needed_link[0]);
            }
        }
    } else {
        $links = WebPage::find()->where(['source' => 'gsmarena'])->asArray()->select('url')->all();
        foreach($links as $k => $item) {
            $item_links[] = $item['url'];
        }
    }
    return $item_links;
}
function isCodecExists($item, $name) // todo exadd +
{
    if(mb_strpos($item, $name) !== false) {
        return '+';
    } else {
        return '';
    }
}
function getAllPhones($list_all_phones = 'https://www.gsmarena.com/sitemap-phones.xml') // todo exadd +
{
    if(!$xml = simplexml_load_file($list_all_phones)) throw new RuntimeException('cant load file');
    return $xml;
}
function getXmlLinks($needed_link) /// todo exadd + all
{
    $path_hash = hash('sha256', $needed_link);
    $m = WebPage::find()->filterWhere(['path_hash' => $path_hash])->one();
    if ($m !== null) {
        return $m->url;
    }
    $m = new WebPage();
    $m->path_hash = $path_hash;
    $m->source = 'gsmarena';
    $m->url = $needed_link;
    $m->save(false);
    return $m->url;
}
function needLinkQuery() // todo exadd +
{
    $path = dirname(__FILE__) .'/../tag/cron_time.txt';
    $period = 60; // 86400
    $cron_time = filemtime($path);
    if (time() - $cron_time >= $period) {
        file_put_contents($path,"");
        $needXmlQuery = true;
    } else {
        $needXmlQuery = false;
    }
    return $needXmlQuery;
}

Render::pretty_print($result_summary_info);
?>

<?php //$this->beginBlock('popup-js') ?>
<!--$(document).ready(function() {-->
<!--    setInterval('cron()', 10000);-->
<!---->
<!--});-->
<!---->
<!---->
<!--function cron()-->
<!--{-->
<!--alert('hello');-->
<!--}-->
<?php //$this->endBlock(); ?>
<?php //$this->registerJs($this->blocks['popup-js'] )?>
