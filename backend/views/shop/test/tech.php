<?php
ini_set('max_execution_time', 700);
ini_set('memory_limit', '256M');

use backend\views\shop\test\Sheet;

$code = Sheet::rf('@backend/views/shop/test/s.csv', ['indexFrom'=>'code']); // todo uncomment

// get value currency in usd
/*
$date = date("d/m/Y");
$content = simplexml_load_file("https://www.cbr.ru/scripts/XML_daily.asp?date_req=".$date);
echo $usd_val = $content->xpath('Valute[@ID="R01235"]')[0]->Value;
echo '<br>';
echo $turkish_val = $content->xpath('Valute[@ID="R01700J"]')[0]->Value;
echo '<hr><br>';
echo $turkish_val / $usd_val;
*/

// price * $

//////////////////// smartprix info mobile ////////
//$main_url = 'https://www.smartprix.com';
//$i = 1;
//$url = $main_url . "/mobiles/?page=$i";
//
//$html = getDataFromApi($url[0]);
//$htmlDom = dom($html);
//
//echo '<pre>';
//print_r($result_data);
//echo '</pre>';
////////////////////  smartprix info mobile info ///////


/////////////////////// $result_data['...'] info /////

// get list with all phones
 $list_all_phones = 'https://www.gsmarena.com/sitemap-phones.xml';
if(!$xml = simplexml_load_file($list_all_phones)) throw new RuntimeException('cant load file');
$item_links = [];
foreach($xml->url as $item) {
    if($item->priority == 0.9) $item_links[] = (array)$item->loc;
}

// uncomment
$item_links = array_slice($item_links, 8300, 10);

/*
 $item_links = [
  0 => [
      0 => 'https://www.gsmarena.com/haier_i8-9055.php'
  ]
];
*/

// result data info arr
$result_summary_info = [];

foreach($item_links as $k => $url ) {
    $result_data = [];
    $html = getDataFromApi($url[0]);
    $htmlDom = dom($html);
    $elem = $htmlDom->query('//h1[@class="specs-phone-name-title"]/text()');

    // get name of item
    $name = $elem->item(0)->nodeValue ?? null;
    if($name) {
        $full_name = explode(' ', $name);
        $result_data['w81a9u0'] = $full_name[0];
        $result_data['33fksng'] = $full_name[1] . ' ' . ($full_name[2] ?? '');
    }

    // get data released
    $data_release_start = $htmlDom->query('//span[@data-spec="released-hl"]/text()')->item(0)->nodeValue ?? null;
    if($data_release_start) {
        $data_release = preg_match_all('/[0-9]+\,\s+[\w]+/mu', $data_release_start, $output_array);
        if(strpos( $output_array[0][0] ?? $data_release_start, 'ot announced y') != false) {
            $result_data['2lbcv9f'] = '-';
        } else {
            $result_data['2lbcv9f'] = trim(str_replace('Released', '', $output_array[0][0] ?? $data_release_start ));
        }
    }

    // get weight & thin params
    $weight = $htmlDom->query('//span[@data-spec="body-hl"]/text()')->item(0)->nodeValue ?? null;

    // get version OS
    $version_os = $htmlDom->query('//span[@data-spec="os-hl"]/text()')->item(0)->nodeValue ?? null;

    // get storage info
    $storage = $htmlDom->query('//span[@data-spec="storage-hl"]/text()')->item(0)->nodeValue ?? null;
    if($storage) {
        $storage_info = explode(',', $storage)[0];
        preg_match("/No card/ui", $storage_info, $output_array);
        if(!empty($output_array[0])) {
            $result_data['7aadfmc'] = '-';
        } else {
            preg_match('/[\d]+\w+/ui', $storage_info, $output_array);
            if(!empty($output_array[0])) {
                $result_data['7aadfmc'] = $output_array[0];
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
    if($ram_size) {
        $result_data['ej4wq1y'] = $ram_size . 'GB';
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
    $result_data['63r9r99'] = $battery_type;

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
                    $result_data['es77mka'] = trim($technology_2g);
                }

                // - 3g bands td
                $bands_3g_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue
                    : null;
                if($bands_3g_td == '3G bands') {
                    // get 3g
                    $technology_3g = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)][3]/td')->item(1)->nodeValue;
                    $result_data['lfy3yhr'] = trim($technology_3g);
                }

                // - 4g bands td
                $bands_4g_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue
                    : null;
                if($bands_4g_td == '4G bands') {
                    // get 4g
                    $technology_4g = $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)][4]/td')->item(1)->nodeValue;
                    $four_g = trim(str_replace(['LTE', 'band'], '', trim($technology_4g)));
                    if(!empty($four_g)) $result_data['w77yz4j'] = $four_g;
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
                        if(!empty($speed_lte)) $result_data['p2tqrwex'] = $speed_lte;
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
                    if(!empty($output_array[0])) $result_data['zgxvylx'] = $output_array[0][0];
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
                            if(!empty($output_array[0])) $result_data['vbryix7'] = str_replace(' mm', '', $output_array[0]);
                        }
                        preg_match('/[\d]+[\.|\,]*[\d]+\sx\s[\d]+[\.|\,]*[\d]+\sx\s[\d]+[\.|\,]*[\d]+\s+mm/ui', $body_dimensions, $output_array);
                        if(isset($output_array[0]) && !empty($output_array[0])) {
                            $result_data['ly7hk3b'] = $output_array[0];
                        }
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
                    // get SIM
                    $sim = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(3)->nodeValue ?? ''; //
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
                        $result_data['cxeplx1'] = getWaterResistant($body_specs);
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
                            $display = str_replace(['capacitive', 'touchscreen', 'Capacitive'],'', explode(',',  $display_type)[0]);
                            if(!empty($display)) $result_data['xxyv5nx'] = $display;

                        }
                        $display_colors = explode(',', $display_type)[1] ?? '';
                        if($display_colors) {
                            preg_match('/[\d]+M/ui', $display_colors, $output_array);
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
                            $result_data['zq2ektp'] = $output_array[1];
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
                    $result_data['59e6c9r'] = $display_protection;
                }

                // - 'No name' features
                $display_features_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(5)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(5)->nodeValue
                    : null;
                if($display_features_td && $display_features_td != null) {
                    // get display specs
                    $display_specs = $htmlDom->query('//table['.$i.']/tr')->item(5)->nodeValue ?? null;
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

                    $result_data['ui65qcn'] = $platform_os; // get os
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
                if($cpu_td && $cpu_td == 'CPU') {
                    // get CPU
                    $cpu = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(2)->nodeValue;
                    $result_data['y5xo6x4'] = $cpu;
                }

                // GPU td
                $gpu_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue
                    : null;
                if($gpu_td && $gpu_td == 'GPU') {
                    $gpu = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(3)->nodeValue ?? null;
                    $result_data['4kzmswo'] = $gpu;
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
                    $result_data['yz90cwl'] = getAnswer($is_memory_card_slot);
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
                    // get camera primary
                    $cam_primary = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(0)->nodeValue;
                    $cam_primary_info = getPrimaryCamera($cam_primary);
                    $result_data['lggn0m2'] = explode(',', $cam_primary_info)[0];
                    $result_data['gn4gn6xk'] = isValueExists($cam_primary_info, 'utofocus');
                    $result_data['zrru3eek'] = isValueExists($cam_primary_info, 'flash');
                }

                // - Features td
                $features_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(1)->nodeValue
                    : null;
                if($features_td && $features_td == 'Features') {
                    // get camera features
                    $cam_features = $htmlDom->query('//table['.$i.']/tr')->item(1)->nodeValue;
                }

                // - Video td
                $video_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(2)->nodeValue
                    : null;
                if($video_td && $video_td == 'Video') {
                    // get camera video
                    $cam_video = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(2)->nodeValue;
                    if(isset($cam_video)) {
                        $result_data['t9q0h7hd'] = explode(',', $cam_video)[0]; // video 1
                        if(isset(explode(',', $cam_video)[1]))  $result_data['8041luk6'] = trim(explode(',', $cam_video)[1]); // video 2
                    }
                }

                // - Secondary td
                $secondary_td = (isset($htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue))
                    ? $htmlDom->query('//table['.$i.']/tr[not(@data-spec-optional)]/td[@class="ttl"]')->item(3)->nodeValue
                    : null;
                if($secondary_td && $secondary_td == 'Secondary') {
                    // get camera secondary
                    $cam_secondary =  $htmlDom->query('//table['.$i.']/tr/td[2]')->item(3)->nodeValue;
                    $cam_secondary_info = explode(',', $cam_secondary);
                    if(isset($cam_secondary_info[0])) {
                        $result_data['06wzu4yz'] = $cam_secondary_info[0];
                        if(isset($cam_secondary_info[1])) {
                            // get led flash
                            $result_data['jefetfa2'] = isValueExists($cam_secondary_info[1], 'LED');
                        }
                    } else {
                        $result_data['06wzu4yz'] = $cam_secondary;
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
                    $sound_specs = $htmlDom->query('//table['.$i.']/tr')->item(3)->nodeValue;
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
                            break;
                        case 'GPS':
                            // get comms gps & glonass
                            $comms_gps = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue;
                            $result_data['yfvshn2'] = getAnswer($comms_gps);
                            $result_data['39ji8mm'] = isValueExists($comms_gps, 'GLON');
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
                            $comms_radio = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue;
                            $result_data['tix99ot'] = getAnswer($comms_radio);
                            break;
                        case 'USB':
                            // get comms usb
                            $comms_usb = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue;
                            preg_match('/^No$/u', $comms_usb, $output_array);
                            if(!empty($output_array[0])) {
                                $result_data['2q8o92fk'] = '-';
                            }

                            $resuld_data['rmjj6m5t'] = isValueExists($comms_usb, 'ype-C');
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
                            break;
                        case ($features_value_td == 'Messaging'):
                            // get features messaging
                            $feat_messaging = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue ?? null;
                            break;
                        case ($features_value_td == 'Browser'):
                                // get browser
                                $feat_browser = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                                $result_data['fdfoul1'] = $feat_browser;
                            break;
                        case (strlen($features_value_td) == 2):
                            // get features audio & video formats
                            $feat_browser = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            $result_data['f7lsmmw9'] = isValueExists($feat_browser, 'MP3');
                            $result_data['x055z520'] = isValueExists($feat_browser, 'MP4');
                            $result_data['8j6be1ko'] = isValueExists($feat_browser, 'DviX');
                            $result_data['xc4bb9kc'] = isValueExists($feat_browser, 'XviD');
                            $result_data['crrbpcar'] = isValueExists($feat_browser, 'H.265');
                            $result_data['xd942mit'] = isValueExists($feat_browser, 'WMV');
                            $result_data['am1zgml8'] = isValueExists($feat_browser, 'WAV');
                            $result_data['t1inmosa'] = isValueExists($feat_browser, 'FLAC');
                            $result_data['lnk8dr8h'] = isValueExists($feat_browser, 'eAAC');
                            $result_data['7zq7neoh'] = isValueExists($feat_browser, 'WMA');
                            $result_data['tfuq45ng'] = isValueExists($feat_browser, 'AAX');
                            $result_data['f3n8nqp4'] = isValueExists($feat_browser, 'AIFF');

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
                            $result_data['zuqqmwi3'] = $battery_talk_time;
                            break;
                        case 'Music play':
                            // get battery music play time
                            $battery_music_time = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            $result_data['6ojsm29w'] = $battery_music_time;
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
                                $result_data['ywkph10b'] = $misc_colors;
                            }
                            break;
                        case 'Price':
                            // get misc price
                            $misc_price = $htmlDom->query('//table['.$i.']/tr')->item($k)->nodeValue ?? null;
                            $price = preg_match_all('/([0-9]+\s+[A-Z]+)/mu', $misc_price, $output_array);
                            $result_data['3n68sce'] = $output_array[0][0] ?? null;
                            break;
                        case 'SAR';
                            // get SAR value
                            $sar = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            if($sar) {
                                $sar_info = preg_split('/\s{2,}/ui', trim($sar));
                                $sar_info = array_diff($sar_info, array(''));
                                if(isset($sar_info[0]))  $result_data['5cp2ol9j'] = str_replace('(head)', '', $sar_info[0]);
                                if(isset($sar_info[1])) $result_data['owpcmmmy'] = str_replace('(body)', '', $sar_info[1]);
                            }
                        break;
                        case 'SAR EU';
                            // get SAR EU value
                            $sar_eu = $htmlDom->query('//table['.$i.']/tr/td[2]')->item($k)->nodeValue ?? null;
                            if($sar_eu) {
                                $sar_info = preg_split('/\s{2,}/ui', trim($sar_eu));
                                $sar_info = array_diff($sar_info, array(''));
                                if(isset($sar_info[0]))  $result_data['psbzu2e9'] = str_replace('(head)', '', $sar_info[0]);
                                if(isset($sar_info[1])) $result_data['uuapl9gw'] = str_replace('(body)', '', $sar_info[1]);
                            }
                    }
                }
                break;
            case 'Tests':
                // - Performance td
                $performance_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item(0)->nodeValue ?? null;
                if($performance_td && $performance_td == 'Performance') {
                    // get test info
                    $test_performance = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(0)->nodeValue ?? null;
                    $result_data['q85w6qmq'] = trim($test_performance);
                }

                // - Display td
                $display_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item(1)->nodeValue ?? null;
                if($display_td && $display_td == 'Display') {
                    // get test display
                    $test_display = $htmlDom->query('//table['.$i.']/tr')->item(1)->nodeValue ?? null;
                }

                // - Camera td
                $camera_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item(2)->nodeValue ?? null;
                if($camera_td && $camera_td == 'Camera') {
                    // get test camera
                    $test_camera = $htmlDom->query('//table['.$i.']/tr')->item(2)->nodeValue ?? null;
                }

                // - Loudspeaker td
                $loudspeaker_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item(3)->nodeValue ?? null;
                if($loudspeaker_td && $loudspeaker_td == 'Loudspeaker') {
                    // get test loudspeaker
                    $test_loudspeaker = $htmlDom->query('//table['.$i.']/tr')->item(3)->nodeValue ?? null;
                }

                // - Audio quality td
                $audio_quality_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item(4)->nodeValue ?? null;
                if($audio_quality_td && $audio_quality_td == 'Audio quality') {
                    // get test audio quality
                    $test_audio = $htmlDom->query('//table['.$i.']/tr/td[2]')->item(4)->nodeValue ?? null;
                    $audio_quality_info = explode('/', $test_audio);
                    $result_data['3t8uo9z6'] = (isset($audio_quality_info[0])) ? getNoiseCrosstalk($audio_quality_info[0]) : null;
                    $result_data['m93am75k'] = (isset($audio_quality_info[1])) ? getNoiseCrosstalk($audio_quality_info[1]) : null;
                }

                // - Battery life td
                $battery_td = $htmlDom->query('//table['.$i.']/tr/td[@class="ttl"]')->item(5)->nodeValue ?? null;
                if($battery_td && $battery_td == 'Battery life') {
                    // get test battery life
                    $test_battery = $htmlDom->query('//table['.$i.']/tr')->item(5)->nodeValue ?? null;
                }
                break;
        }

        $result_summary_info[$url[0]] = $result_data;
    }

    // get device type if don't have platform os
    if(!isset($result_summary_info[$url[0]]['drbmx1r'])) {
        $not_gsm_device = $htmlDom->query('//span[@id="non-gsm"]/p/text()')->item(0)->nodeValue ?? null;
        if(!empty($not_gsm_device)) {
            $result_summary_info[$url[0]]['drbmx1r'] = 2;
        } else {
            if(isset($result_summary_info[$url[0]]['qorav98'])) {
                if($result_summary_info[$url[0]]['qorav98'] > 60) $result_summary_info[$url[0]]['drbmx1r'] = 1;
            }
            if(isset($result_summary_info[$url[0]]['qorav98'])) {
                if($result_summary_info[$url[0]]['qorav98'] < 60) $result_summary_info[$url[0]]['drbmx1r'] = 2;
            }
        }
    }
}
/////////////////////// /. $result_data['...'] info /////
?>
    <!-- table view -->
    <div class="tableScroll" style="width:auto; overflow-x:scroll;">
        <table class="table table-responsive table-bordered">
            <thead>
            <tr>
                <?php foreach ($code as $k => $item) : ?>
                    <th scope="col"><?= $item['title_ru'] ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach ($code as $k => $item) : ?>
                    <th scope="col"><?= $k ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($result_summary_info as $k => $item) : ?>
                <tr>
                    <?php foreach ($code as $key => $value) : ?>
                        <td scope="col"><?= $item[$key] ?? '' ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<style>
    table td {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
    <!-- /. table view -->

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
function getWaterResistant($string)
{
     if(strpos($string, 'IP') != false){
         return '+';
     } else {
         return '-';
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
function getWatchOs($platform_os)
{
    $watch_os = ['wearable', 'Android OS compatible', 'Android Wear', 'watchOS', 'Proprietary OS', 'Wearable platform', 'Nucleus OS', 'LG Wearable', 'Tencent OS'];
    foreach($watch_os as $item) {
         if(strpos($platform_os, $item) !== false) {
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
function pretty_print($str)
{
    echo '<pre>';
    print_r($str);
    echo '</pre>';
}

pretty_print($result_summary_info);