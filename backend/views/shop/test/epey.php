<?php
ini_set('max_execution_time', 700);
ini_set('memory_limit', '256M');

use backend\views\shop\test\Sheet;

$code = Sheet::rf('@backend/views/shop/test/s.csv', ['indexFrom'=>'code']);
// get urls' for fitness band & smartwatches or phones
$category_wearable = 16;
// $category_phone = 1;
$result_links_wearable = getLinksFromEpey($category_wearable); // for smartwatches & fitness trackers
// $result_links_phones = getLinksFromEpey($category_phone); // for phones

$result_summary_info_turkish = [];

////////
//$result_links_wearable = [
//    0 => 'https://www.epey.com/akilli-saat/apple-watch-38-mm-paslanmaz-celik-kasa-ve-modern-tokali-kahverengi-kayis.html',
//    1 => 'https://www.epey.com/akilli-saat/garmin-forerunner-645.html',
//    2 => 'https://www.epey.com/akilli-saat/sony-smartband-talk.html',
//]; // todo delete
////////

foreach($result_links_wearable as $k => $url) { // $result_links_wearable or $result_links_phones
    $html_turkish = getDataFromApiWithCategory($url);
    $htmlDom2 = dom($html_turkish);

    /* common part */
    // get brand name
    $item_info = $htmlDom2->query('//div[@class="baslik"]/h1/a/text()')->item(0)->nodeValue ?? null;
    if($item_info) {
        $model_info = explode(' ', $item_info);
        $result_data_turkish['w81a9u0'] = $model_info[0]; // brand
        $model_name = substr($item_info, strlen($model_info[0]));
        $result_data_turkish['33fksng'] = getEngNameFromTurkish($model_name); // model
    }

    $price = $htmlDom2->query('//div[@class="fiyatlar"]/div[@class="fiyat fiyat-16"]/a/span[@class="urun_fiyat"]/text()')->item(0)->nodeValue ?? null; // todo exadd
    if($price) {
        $result_data_turkish['3n68sce'] = str_replace('TL','TRY', trim($price));
    } else {
        $result_data_turkish['3n68sce'] = 'price not found';
    }

    if(strpos($url, 'akilli-saat') === false) {
        /** get phones data */
        // get screen size
        $screen_size = $htmlDom2->query('//strong[@class="ozellik1 tip"]/following::span[@class="cell cs1"]/span/a/text()')->item(0)->nodeValue ?? null; // screen_size
        if($screen_size) {
            $result_data_turkish['1n820fz'] = explode(' ', $screen_size)[0];
        }

        // get display resolution
        $display_resolution = $htmlDom2->query('//strong[@class="ozellik3 tip"]/following::span[@class="cell cs1"]/span/a/text()')->item(0)->nodeValue ?? null;
        if($display_resolution) {
            $result_data_turkish['nggks18'] = explode('x', $display_resolution)[0]; // display width
            preg_match("/[0-9]+/", explode('x', $display_resolution)[1], $output_array);
            $result_data_turkish['j2p7bju'] = $output_array[0]; // display height
        }

        // get pixel intensity
        $pixel_intensity = ($htmlDom2->query('//strong[@class="ozellik2 tip"]/following::span[1]')->item(0)->nodeValue) ?? null; // pixel_intensity
        if($pixel_intensity) { $result_data_turkish['7x8x76o'] = str_replace(['PPI'],[''],trim($pixel_intensity)); }


        // get display technology
        $display_technology = ($htmlDom2->query('//strong[@class="ozellik4"]/following::span[1]')->item(0)->nodeValue) ?? null; // display_technology
        if($display_technology) { $result_data_turkish['xxyv5nx'] = trim($display_technology); }


        // get display features
        $display_info = $htmlDom2->query('//strong[@class="ozellik5"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_info) {
            $result_data_turkish['alrhep0'] = isValueExists($display_info, 'Multi Touch'); // multitouch
        }

        // get display touch
        $display_touch = $htmlDom2->query('//strong[@class="ozellik46 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_touch) {
            $result_data_turkish['yq2jcrla'] = str_replace(['Kapasitif Ekran'],['Capacitive Screen'],trim($display_touch));  // touch_type
        }

        // get display number of colors
        $display_colors = $htmlDom2->query('//strong[@class="ozellik45 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_colors) {
            $result_data_turkish['8vzzca7'] = str_replace('Milyon','M', $display_colors); // number of colors
        }

        // get display rate
        $display_rate = $htmlDom2->query('//strong[@class="ozellik886"]/following::span[1]')->item(0)->nodeValue ?? null; // body_rate
        if($display_rate) {
            $display_ratio = preg_match('/\d+/ui', $display_rate, $output_array);
            if(isset($output_array[0]) && (!empty($output_array[0]))) {
                $result_data_turkish['zq2ektp'] = trim($output_array[0]);
            }
        }

        // get battery capacity
        $battery_capacity = $htmlDom2->query('//strong[@class="ozellik7 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($battery_capacity) {
            $result_data_turkish['wbswcml'] = trim($battery_capacity);
        }

        // get speech time (3g)
        $speech_time = $htmlDom2->query('//strong[@class="ozellik85"]/following::span[1]')->item(0)->nodeValue ?? null;// speech_time
        if($speech_time) {
            preg_match('/\d+/ui', $speech_time, $output_array);
            if(isset($output_array[0]) && (!empty($output_array[0]))) {
                $result_data_turkish['zuqqmwi3'] = $output_array[0];
            }
        }

        // get internet usage(wi-fi)
        $internet_usage = $htmlDom2->query('//strong[@class="ozellik213"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($internet_usage) {
            preg_match('/\d+/ui', $internet_usage, $output_array);
            if(isset($output_array[0]) && (!empty($output_array[0]))) {
                $result_data_turkish['qwkph15b'] = $output_array[0];
            }
        }

        // get charging info
        $charging_info =  $htmlDom2->query('//strong[@class="ozellik81"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($charging_info) {
            $result_data_turkish['xc2onhy'] = isValueExists($charging_info, 'ablosuz'); // wireless charging
        }

        // get battery tech
        $battery_tech = $htmlDom2->query('//strong[@class="ozellik331"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_tech
        if($battery_tech) {
            $result_data_turkish['63r9r99'] = trim($battery_tech);
        }

        // get replacement battery
        $battery_replace = $htmlDom2->query('//strong[@class="ozellik102 tip"]/following::span[2]')->item(0)->nodeValue ?? null; // replacement_battery
        if($battery_replace) {
            $result_data_turkish['c220c9j'] = getAnswerTurkish($battery_replace);
        }

        // get battery fast charging
        $fast_charging = $htmlDom2->query('//strong[@class="ozellik880"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($fast_charging) {
            $result_data_turkish['27s8wl4'] = isValueExists($fast_charging, 'Hızlı'); // fast charging
        }

        // get battery time fast charging
        $time_fast_charging = $htmlDom2->query('//strong[@class="ozellik880"]/following::span[3]')->item(0)->nodeValue ?? null; // time fast charging
        if($time_fast_charging) {
            preg_match('/([0-9]+).+(%[0-9]+|[0-9]+\%)/', $time_fast_charging, $output_array);
            if($output_array) {
                $percent = str_replace('%50','50%',$output_array[2]);
                $result_data_turkish['le00i0c'] = $output_array[1] . ' min ' . $percent; // time fast charging
            }
        }

        // get camera resolution
        $camera_resol = $htmlDom2->query('//strong[@class="ozellik19 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // camera_resolution
        if($camera_resol) {
            preg_match('/\d+/ui', $camera_resol, $output_array);
            if(isset($output_array[0]) && !empty($output_array[0])) {
                $result_data_turkish['lggn0m2'] = trim($output_array[0]);
            }
        }

        // get camera features
        for ($i = 0; $i <= 15; $i++) {
            $cam_features = $htmlDom2->query('//strong[@class="ozellik69"]/following::span[@class="cell cs1"]/span/a/text()')->item($i)->nodeValue ?? null;;
            if($cam_features) {
                $result_data_turkish['lggn001'] = isValueExists($cam_features, 'Live Photos') ;
                $result_data_turkish['lggn002'] = isValueExists($cam_features, 'HDR') ;
                $result_data_turkish['gn4gn6xk'] = isValueExists($cam_features, 'Otomatik odaklama') ; // autofocus
                $result_data_turkish['lggn003'] = isValueExists($cam_features, 'Karma Kızılötesi (Hybrid IR) Filtresi'); // hybrid IR filter
                $result_data_turkish['lggn004'] = isValueExists($cam_features, 'Sesli komut'); // voice command for camera
                $result_data_turkish['c4awfagk'] = isValueExists($cam_features, 'Yüz Algılama'); // face id
                $result_data_turkish['lggn005'] = isValueExists($cam_features, 'Elle Odaklama'); // manual focus
                $result_data_turkish['lggn006'] = isValueExists($cam_features, 'Coğrafi konum etiketleme'); // geo tag
                $result_data_turkish['lggn007'] = isValueExists($cam_features, 'BSI'); // bsi
                $result_data_turkish['lggn008'] = isValueExists($cam_features, 'Depth of Field (DOF)'); // DOF
                $result_data_turkish['gn4gn6xk'] = isValueExists($cam_features, 'Otomatik Odaklama'); // autofocus
                $result_data_turkish['lggn009'] = isValueExists($cam_features, 'Safir Kristal Objektif Kapağı'); // crystal cap lens
                $result_data_turkish['lggn010'] = isValueExists($cam_features, 'Seri Çekim (Burst) Modu'); // burst mode
                $result_data_turkish['lggn011'] = isValueExists($cam_features, 'Zamanlayıcı'); // camera timer
            }
        }

        // get flash alarm
        $flash_alarm = $htmlDom2->query('//strong[@class="ozellik72"]/following::span[2]')->item(0)->nodeValue ?? null; // flash_alarm_1
        if($flash_alarm) { $result_data_turkish['jefetfa2'] = str_replace(['Tek Tonlu Flaş','Yok','Çift Tonlu','Halka'],
            ['Single-Tone Flash','No','Dual Tone','Ring'],trim($flash_alarm)); } // led flash

        // get aperture clear
        $aperture_clear = $htmlDom2->query('//strong[@class="ozellik73 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($aperture_clear) { $result_data_turkish['lggn0m3'] = $aperture_clear;} // aperture

        // get optical zoom
        $optical_zoom = ($htmlDom2->query('//strong[@class="ozellik107 tip"]/following::span[1]')->item(0)->nodeValue) ?? null;
        $result_data_turkish['lggn0m4'] = str_replace(' ', '', trim($optical_zoom));

        // get video recording resoluton 4k
        $video_rec_resol = $htmlDom2->query('//strong[@class="ozellik71 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // video_resolution
        if($video_rec_resol) { $result_data_turkish['t9q0h7hd'] = trim($video_rec_resol); }

        // get video fps value
        $fps_value = $htmlDom2->query('//strong[@class="ozellik70 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($fps_value) { $result_data_turkish['lggn0m5'] = trim($fps_value); }

        // get video recording features
        for($i = 0; $i <= 8; $i++) {
            $video_rec_features = $htmlDom2->query('//strong[@class="ozellik216"]/following::span[@class="cell cs1"]/span/a/text()')->item($i)->nodeValue ?? null;
            if($video_rec_resol) {
                $result_data_turkish['lggn012'] = isValueExists($video_rec_resol, 'OIS'); // optical image stabilizer
                $result_data_turkish['lggn013'] = isValueExists($video_rec_resol, 'Time-lapse Video Kayıt'); // time lapse video rec.
                $result_data_turkish['lggn014'] = isValueExists($video_rec_resol, 'Video Yakınlaştırma'); // video zoom
                $result_data_turkish['lggn015'] = isValueExists($video_rec_resol, 'Slow motion video'); // slow motion video
            }
        }

        // get video recording options
        for($i = 2; $i <= 9; $i++) {
            $video_rec_options = $htmlDom2->query('//strong[@class="ozellik793"]/following::span')->item(0)->nodeValue ?? null;
            if($video_rec_options) { $result_data_turkish['lggn016'] = trim($video_rec_options); }
        }

        // get second rear camera
        $second_rear_camera = ($htmlDom2->query('//strong[@class="ozellik876"]/following::span[1]')->item(0)->nodeValue) ?? null;
        if($second_rear_camera) {
            $result_data_turkish['lggn017'] = getAnswerTurkish($second_rear_camera);
        }

        // get second rear camera diaphragm
        $second_rear_camera_dia = ($htmlDom2->query('//strong[@class="ozellik878"]/following::span[1]')->item(0)->nodeValue) ?? null;
        if($second_rear_camera_dia) {
            $result_data_turkish['lggn018'] = trim($second_rear_camera_dia);
        }

        // get second rear camera features
        $second_rear_camera_ois = ($htmlDom2->query('//strong[@class="ozellik879"]/following::span[2]')->item(0)->nodeValue) ?? null;
        if($second_rear_camera_ois) {
            $result_data_turkish['lggn019'] = isValueExists($second_rear_camera_ois, 'OIS');
        }

        // get second rear camera zoom
        $second_rear_camera_zoom = ($htmlDom2->query('//strong[@class="ozellik879"]/following::span[3]')->item(0)->nodeValue) ?? null;
        if($second_rear_camera_zoom) {
            if(isValueExists($second_rear_camera_zoom, 'Optik Zoom') === '+') {
                preg_match('/\d+/ui', $second_rear_camera_zoom, $output_array);
                if(isset($output_array[0]) && (!empty($output_array[0]))) {
                    $result_data_turkish['lggn020'] = $output_array[0];
                }
            }
        }

        // get front camera resolution
        $cam_resolution = $htmlDom2->query('//strong[@class="ozellik18 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // front_camera_resolution
        if($cam_resolution) {
            preg_match('/\d+/ui', $cam_resolution, $output_array);
            if(isset($output_array[0]) && !empty($output_array[0])) {
                $result_data_turkish['wzu4yz'] = trim($output_array[0]);
            }
        }

        // get front camera video resolution
        $cam_video_res = $htmlDom2->query('//strong[@class="ozellik27 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // front_cam_video_resolution;
        if($cam_video_res) { $result_data_turkish['8041luk6'] = trim($cam_video_res); }


        // get front camera fps value
        $cam_fps_value = $htmlDom2->query('//strong[@class="ozellik32 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cam_fps_value) {$result_data_turkish['lggn0m6'] = trim($cam_fps_value); }

        // get front camera aperture
        $cam_aperture = $htmlDom2->query('//strong[@class="ozellik337 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cam_aperture) {$result_data_turkish['lggn0m7'] = trim($cam_aperture); }

        // get from camera capabilities
        for($i=2;$i<=10;$i++) {
            $cam_capabilities_1 = $htmlDom2->query('//strong[@class="ozellik31"]/following::span['.$i.']')->item(0)->nodeValue ?? null;
            if($cam_capabilities_1) { $result_data_turkish['lggn021'] = isValueExists($cam_capabilities_1, 'Animoji'); }
            if($cam_capabilities_1) { $result_data_turkish['lggn022'] = isValueExists($cam_capabilities_1, 'HDR'); }
            if($cam_capabilities_1) { $result_data_turkish['lggn023'] = isValueExists($cam_capabilities_1, 'Arka Arkaya Çekim Modu'); }
            if($cam_capabilities_1) { $result_data_turkish['lggn024'] = isValueExists($cam_capabilities_1, 'BSI'); }
            if($cam_capabilities_1) { $result_data_turkish['lggn025'] = isValueExists($cam_capabilities_1, 'Live Photos'); }
            if($cam_capabilities_1) { $result_data_turkish['lggn026'] = isValueExists($cam_capabilities_1, 'Portre Modu'); }
            if($cam_capabilities_1) { $result_data_turkish['lggn027'] = isValueExists($cam_capabilities_1, 'Pozlama Kontrolü'); }
            if($cam_capabilities_1) { $result_data_turkish['lggn028'] = isValueExists($cam_capabilities_1, 'Zamanlayıcı'); }
        }

        // get 2g frequencies
        $freg_2g = $htmlDom2->query('//strong[@class="ozellik41"]/following::span[1]')->item(0)->nodeValue ?? null;// network_2g_freq
        if($freg_2g) { $result_data_turkish['es77mka'] = trim($freg_2g); }

        // get 2g technology
        $technology_2g = $htmlDom2->query('//strong[@class="ozellik56 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // gsm
        if($technology_2g) {
            $result_data_turkish['o3kmrtz'] = isValueExists($technology_2g, 'EDGE');
            $result_data_turkish['6me3pwq'] = isValueExists($technology_2g, 'GSM');
            $result_data_turkish['de60w8u'] = isValueExists($technology_2g, 'GPRS');
        }

        // get 3g freq
        $freg_3g = $htmlDom2->query('//strong[@class="ozellik42"]/following::span[1]')->item(0)->nodeValue ?? null;// network_3g_freq;
        if($freg_3g) { $result_data_turkish['lfy3yhr'] = trim($freg_3g); }

        // get 3g download
        $speed_3g_download = $htmlDom2->query('//strong[@class="ozellik39"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($speed_3g_download) { $result_data_turkish['p4zld7l'] = trim($speed_3g_download); }

        // get 3g upload
        $upload_3g = $htmlDom2->query('//strong[@class="ozellik40"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($upload_3g) { $result_data_turkish['p4zld8l'] = trim($upload_3g); }

        // get 4g
        $four_g = $htmlDom2->query('//strong[@class="ozellik51 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // 4g
        if($four_g) { $result_data_turkish['w77yz4j'] = getAnswerTurkish($four_g); }

        // get 4g download
        $download_4g = $htmlDom2->query('//strong[@class="ozellik52"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($download_4g) { $result_data_turkish['p4zld9l'] = trim($download_4g); }

        // get 4g upload
        $upload_4g = $htmlDom2->query('//strong[@class="ozellik53"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($upload_4g) {$result_data_turkish['p4zld10l'] = trim($upload_4g); }

        // get 4g technology
        $lte_info = $htmlDom2->query('//strong[@class="ozellik55 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($lte_info) { $result_data_turkish['k6ddojx'] = isValueExists($lte_info, 'LTE'); } // LTE

        // get 4g features
        $network_4g_feat = $htmlDom2->query('//strong[@class="ozellik1055 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($network_4g_feat) {
            $result_data_turkish['p4zld10lw'] = isValueExists($network_4g_feat, 'VoLTE (Voice over LTE)'); // voLTE
        }

        // get support 4.5g
        $support_45g = $htmlDom2->query('//strong[@class="ozellik1737"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($support_45g) { $result_data_turkish['p4zld1l1'] = getAnswerTurkish(trim($support_45g)); }

        // get chipset
        $chipset = $htmlDom2->query('//strong[@class="ozellik15 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // chipset
        if($chipset) {$result_data_turkish['dkg7n4e'] = trim($chipset); }

        // get main CPU
        $main_cpu = $htmlDom2->query('//strong[@class="ozellik28 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // main_cpu
        if($main_cpu) { $result_data_turkish['y5xo6x4'] = trim($main_cpu); }

        // get cpu frequency
        $cpu_freq = $htmlDom2->query('//strong[@class="ozellik11 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_freq) { $result_data_turkish['y5xo6x6'] = trim($cpu_freq); }

        // get cpu core
        $cpu_core = $htmlDom2->query('//strong[@class="ozellik12 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_core) {$result_data_turkish['y5xo6x5'] = str_replace(['Çekirdek'],['core'], trim($cpu_core));}

        // get processor architecture
        $cpu_archit = $htmlDom2->query('//strong[@class="ozellik347"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_archit) { $result_data_turkish['y4xo6x6'] = trim($cpu_archit); }

        // get first auxiliary processor
        $first_aux_proc = $htmlDom2->query('//strong[@class="ozellik29"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($first_aux_proc) { $result_data_turkish['y5xo6x7'] = trim($first_aux_proc); }

        // get cpu production technology
        $cpu_prod_tech = $htmlDom2->query('//strong[@class="ozellik2033 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_prod_tech) { $result_data_turkish['y3xo6x6'] = trim($cpu_prod_tech); }

        // get gpu info
        $gpu_info = ($htmlDom2->query('//strong[@class="ozellik17 tip"]/following::span[1]')->item(0)->nodeValue) ?? null;
        if($gpu_info) { $result_data_turkish['4kzmswo'] = trim($gpu_info); }

        // get antutu score
        $antutu_score = $htmlDom2->query('//strong[@class="ozellik1672"]/following::span[1]')->item(0)->nodeValue ?? null; // antutu_score
        if($antutu_score) {
            $result_data_turkish['q85w6qmq'] = trim(explode(' ', $antutu_score)[0]);
        }

        // get memory RAM
        $memory_ram = $htmlDom2->query('//strong[@class="ozellik14 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($memory_ram) { $result_data_turkish['ej4wq1y'] = trim($memory_ram); }

        // get memory ram type
        $ram_type = $htmlDom2->query('//strong[@class="ozellik332"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($ram_type) {$result_data_turkish['z3xo6x6'] = trim($ram_type); }

        // get internal storage
        $internal_storage = $htmlDom2->query('//strong[@class="ozellik21 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($internal_storage) { $result_data_turkish['7aadfmc'] = trim($internal_storage); }

        // get memory card support
        $card_support = $htmlDom2->query('//strong[@class="ozellik1557 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // memory_card_support
        if($card_support) {$result_data_turkish['yz90cwl'] = getAnswerTurkish($card_support);}

        // get other memory options
        $memory_options = $htmlDom2->query('//strong[@class="ozellik105 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($memory_options) { $result_data_turkish['a3xo6x6'] = str_replace(['Depolama seçeneği var'],['storage options'],trim($memory_options));}

        // get length
        $length = $htmlDom2->query('//strong[@class="ozellik26 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // length
        if($length) {$result_data_turkish['qorav98'] = str_replace('mm', '', trim($length));}

        // get width
        $width = $htmlDom2->query('//strong[@class="ozellik8 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // width
        if($width) {$result_data_turkish['65ihv16'] = str_replace('mm', '', trim($width));}

        $thickness = $htmlDom2->query('//strong[@class="ozellik10 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // thick
        if($thickness) {$result_data_turkish['vbryix7'] = str_replace('mm', '', trim($thickness));}

        // get weight
        $weight = $htmlDom2->query('//strong[@class="ozellik9"]/following::span[1]')->item(0)->nodeValue ?? null;// weight
        if($weight) {$result_data_turkish['uanzwi8'] = str_ireplace('Gram', '',trim($weight));}

        // get color's
        $colors = $htmlDom2->query('//strong[@class="ozellik80 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // colors
        if($colors) {$result_data_turkish['ywkph10b'] = getEngNameFromTurkish(trim($colors));}

        // get cover materials
        $cover_materials = $htmlDom2->query('//strong[@class="ozellik1320"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cover_materials) { $result_data_turkish['3bjbzry'] = getEngNameMaterial(trim($cover_materials));}

        // get frame materials
        $frame_materials = $htmlDom2->query('//strong[@class="ozellik1321"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($frame_materials) {$result_data_turkish['3bjbzra'] = getEngNameMaterial(trim($frame_materials));}

        // get OS
        $platform_os = $htmlDom2->query('//strong[@class="ozellik24 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($platform_os) {
            $result_data_turkish['ui65qcn'] = trim($platform_os); // get os
            $result_data_turkish['0v8w2sz'] = getOS($platform_os, 'iOS'); // iOS
            $result_data_turkish['a5sj3l2'] = getOS($platform_os, 'indow'); // windows
            $result_data_turkish['vxq3g1f'] = getOS($platform_os, 'lackBerry'); // blackberry
            $result_data_turkish['llulwif'] = getOS($platform_os, 'ndroid'); // android
        }

        // get radio
        $radio_info = $htmlDom2->query('//strong[@class="ozellik76"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($radio_info) {$result_data_turkish['tix99ot'] = getAnswerTurkish($radio_info); } // radio

        // get speaker features
        $speaker_info = ($htmlDom2->query('//strong[@class="ozellik318"]/following::span[1]')->item(0)->nodeValue) ?? null;
        if($speaker_info) {
            $result_data_turkish['yq2jcrl2'] = isValueExists($speaker_info, 'Çift Hoparlör'); // dual speaker
        }

        // get audio out
        $audio_out = $htmlDom2->query('//strong[@class="ozellik324 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($audio_out) {$result_data_turkish['yh7xh3q'] = trim($audio_out);}

        // get wi-fi channels
        $wifi_channels = $htmlDom2->query('//strong[@class="ozellik36 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // wifi_channels
        if($wifi_channels) {$result_data_turkish['2pinrcv'] = trim($wifi_channels);}

        // get wi-fi features
        $wifi_features = $htmlDom2->query('//strong[@class="ozellik59"]/following::span')->item(0)->nodeValue ?? null; // todo add ???
        if($wifi_features) {
            $result_data_turkish['p4zld1l5'] = isValueExists($wifi_features, 'MIMO');
            $result_data_turkish['p4zld1l2'] = isValueExists($wifi_features, 'Dual-Band');
            $result_data_turkish['p4zld1l3'] = isValueExists($wifi_features, 'Hotspot');
        }

        // get nfc
        $nfc_info = $htmlDom2->query('//strong[@class="ozellik61 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // nfc
        if($nfc_info) {$result_data_turkish['9ee4viy'] = getAnswerTurkish($nfc_info);}

        // get bluetooth ver.
        $bluetooth_version = $htmlDom2->query('//strong[@class="ozellik48 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // bluetooth_version
        if($bluetooth_version) {$result_data_turkish['p4zld5l'] = trim($bluetooth_version);}

        // get infrared
        $is_infrared = $htmlDom2->query('//strong[@class="ozellik62 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($is_infrared) {$result_data_turkish['hwst1n7'] =  getAnswerTurkish($is_infrared); } // infrared

        // get navigation features
        $glonass = $htmlDom2->query('//strong[@class="ozellik79 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($glonass) {
            $result_data_turkish['39ji8mm'] = isValueExists($glonass, 'GLONASS'); // glonass
            $result_data_turkish['yfvshn2'] = isValueExists($glonass, 'GPS'); // gps
        }

        // get water resistance
        $water_resistance = $htmlDom2->query('//strong[@class="ozellik329 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // todo add + water resistance
        if($water_resistance) {$result_data_turkish['cxeplx1'] = trim($water_resistance);}

        // get video formats
        $video_formats = ($htmlDom2->query('//strong[@class="ozellik82 tip"]/following::span[1]')->item(0)->nodeValue) ?? null;
        $result_data_turkish['x055z520'] = isValueExists($video_formats, 'MP4');
        $result_data_turkish['8j6be1ko'] = isValueExists($video_formats, 'DivX');
        $result_data_turkish['xc4bb9kc'] = isValueExists($video_formats, 'XviD');
        $result_data_turkish['crrbpcar'] = isValueExists($video_formats, 'H.265');
        $result_data_turkish['xd942mit'] = isValueExists($video_formats, 'WMV');

        // get audio formats
        $audio_formats = ($htmlDom2->query('//strong[@class="ozellik83 tip"]/following::span[1]')->item(0)->nodeValue) ?? null;
        $result_data_turkish['f7lsmmw9'] = isValueExists($audio_formats, 'MP3');
        $result_data_turkish['am1zgml8'] = isValueExists($audio_formats, 'WAV');
        $result_data_turkish['t1inmosa'] = isValueExists($audio_formats, 'FLAC');
        $result_data_turkish['lnk8dr8h'] = isValueExists($audio_formats, 'eAAC');
        $result_data_turkish['7zq7neoh'] = isValueExists($audio_formats, 'WMA');
        $result_data_turkish['tfuq45ng'] = isValueExists($audio_formats, 'AAX');
        $result_data_turkish['f3n8nqp4'] = isValueExists($audio_formats, 'AIFF');

        // get water resistance level
        $water_resistance_level = ($htmlDom2->query('//strong[@class="ozellik114 tip"]/following::span[1]')->item(0)->nodeValue) ?? null; // water resistant standart
        if($water_resistance_level) {
            $result_data_turkish['cxeplx1'] = str_ireplace(['Sadece Sıçramalara Karşı','Yok'],['Only Against Jumps','-'],$water_resistance_level);
        }

        // get dust resistance
        $dust_resistance = $htmlDom2->query('//strong[@class="ozellik330 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($dust_resistance) {$result_data_turkish['cxeplx2'] = getAnswerTurkish(trim($dust_resistance));}

        // get resistance level
        $result_data_turkish['59e6c9r'] = ($htmlDom2->query('//strong[@class="ozellik113 tip"]/following::span[1]')->item(0)->nodeValue) ?? null; // resistance_level

        // get 3g video call
        $video_call_3g = $htmlDom2->query('//strong[@class="ozellik1708 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($video_call_3g) {$result_data_turkish['p4zld1lk'] = getAnswerTurkish(trim($video_call_3g));}

        // get video conversation
        $video_conversation = $htmlDom2->query('//strong[@class="ozellik2734 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($video_conversation) {$result_data_turkish['p4zld1lq'] = getAnswerTurkish(trim($video_conversation));}

        // get sensors
        $feat_sensors = $htmlDom2->query('//strong[@class="ozellik75 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($feat_sensors) {
            $result_data_turkish['h1ddzrt'] = isValueExists($feat_sensors, 'İvmeölçer'); // accelerometer
            $result_data_turkish['ywtcejg'] = isValueExists($feat_sensors, 'Jiroskop'); // gyroskop
            $result_data_turkish['x399jxz'] = isValueExists($feat_sensors, 'Barometre'); // barometer
            $result_data_turkish['x0xgsbn'] = isValueExists($feat_sensors, 'Pusula'); // compass
            $result_data_turkish['h88pkmdy'] = isValueExists($feat_sensors, 'Ortam Işığı Sensörü'); // light sensor
            $result_data_turkish['h88pkmd1'] = isValueExists($feat_sensors, 'Ortam Işığı Sensörü'); // proxymiti sensor
        }

        // get fingerprint
        $finger_print = $htmlDom2->query('//strong[@class="ozellik1511 tip"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($finger_print) {
            $result_data_turkish['rsub3l9c'] = getAnswerTurkish($finger_print); // fingerprint
        }

        // get notification light
        $light_indicator = $htmlDom2->query('//strong[@class="ozellik111 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // notify_led
        if($light_indicator) {$result_data_turkish['xdet6dq'] = getAnswerTurkish($light_indicator);}

        // get SAR value head
        $result_data_turkish['5cp2ol9j'] = ($htmlDom2->query('//strong[@class="ozellik92 tip"]/following::span[1]')->item(0)->nodeValue) ?? null;

        // get SAR value body
        $result_data_turkish['owpcmmmy'] = ($htmlDom2->query('//strong[@class="ozellik91 tip"]/following::span[1]')->item(0)->nodeValue) ?? null;

        // get services and apps
        $services_info = $htmlDom2->query('//strong[@class="ozellik217 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // services_apps
        if($services_info) {$result_data_turkish['c4awfagk'] = isValueExists($services_info, 'Face ID'); } // face id

        // get usb connection type
        $usb_info = $htmlDom2->query('//strong[@class="ozellik65 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // usb type
        if($usb_info) {$result_data_turkish['q8o92fk'] = trim($usb_info);}

        // get usb features
        $usb_features = $htmlDom2->query('//strong[@class="ozellik66"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($usb_features) {$result_data_turkish['usb_features'] = trim($usb_features);}

        // get sim info
        $sim_info = $htmlDom2->query('//strong[@class="ozellik44 tip"]/following::span[1]')->item(0)->nodeValue ?? null; // sim
        if($sim_info) {$result_data_turkish['mdmfh57'] = str_ireplace(['Mikro'],['Micro'],trim($sim_info));}

        // get announcement date
        $announcement_date = $htmlDom2->query('//strong[@class="ozellik599"]/following::span[1]')->item(0)->nodeValue ?? null; // announcement_date
        if($announcement_date) {$result_data_turkish['zgxvylx'] = trim($announcement_date); }

        // get release date
        $release_date_info = ($htmlDom2->query('//strong[@class="ozellik600"]/following::span[1]')->item(0)->nodeValue) ?? null; // release_date
        if($release_date_info) {$result_data_turkish['2lbcv9f'] = trim($release_date_info); }

        // ger user rating
        $user_rating = $htmlDom2->query('//span[@class="kpuan"]')->item(0)->nodeValue ?? null; // user ratings
        if($user_rating) { $result_data_turkish['bkaqn4m'] = str_replace(['kullanıcı'], ['people'], trim($user_rating));}

        // get type
        $result_data_turkish['drbmx1r'] = 1;

    } else {
        /** get smartwatch & fitness tracker data */
        // get display
        $display_exist = $htmlDom2->query('//strong[@class="ozellik1246"]/following::span[@class="cell cs1"]/span/a/text()')->item(0)->nodeValue ?? null; // screen_size
        if($display_exist) {
            $result_data_turkish['is_display'] = getAnswerTurkish($display_exist);
        }

        // get screen size
        $screen_size = $htmlDom2->query('//strong[@class="ozellik1117"]/following::span[@class="cell cs1"]/span/a/text()')->item(0)->nodeValue ?? null; // screen_size
        if($screen_size) {
            $result_data_turkish['1n820fz'] = explode(' ', $screen_size)[0];
        }

        // get display resolution
        $display_resolution = $htmlDom2->query('//strong[@class="ozellik1116"]/following::span[@class="cell cs1"]/span/text()')->item(0)->nodeValue ?? null;
        if($display_resolution) {
            $result_data_turkish['nggks18'] = trim(explode('x', $display_resolution)[0]); // display width
            preg_match("/[0-9]+/", explode('x', $display_resolution)[1], $output_array);
            $result_data_turkish['j2p7bju'] = trim($output_array[0]); // display height
        }

        // get display number of colors
        $display_colors = $htmlDom2->query('//strong[@class="ozellik1177"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_colors) {
            $result_data_turkish['8vzzca7'] = str_replace(['Renkli'], ['Color'], explode(' ', $display_colors)[0]); // number of colors
        }

        // get ppi
        $ppi = $htmlDom2->query('//strong[@class="ozellik1124"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($ppi) {
            $ppi = preg_match('/\d+/ui', $ppi, $output_array);
            if($output_array[0] && !empty($output_array[0])) {
                $result_data_turkish['7x8x76o'] = $output_array[0];
            }
        }

        // get display technology
        $display_technology = ($htmlDom2->query('//strong[@class="ozellik1125"]/following::span[1]')->item(0)->nodeValue) ?? null; // display_technology
        if($display_technology) { $result_data_turkish['xxyv5nx'] = getEngNameFromTurkish(trim($display_technology)); }

        // get display features(touch)
        $display_info = $htmlDom2->query('//strong[@class="ozellik1126"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($display_info) {
            $result_data_turkish['pcao0re'] = isValueExists($display_info, 'Dokunmatik'); // multitouch
        }

        // compatible brand
        $compatible_brand = $htmlDom2->query('//strong[@class="ozellik1266"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($compatible_brand) $result_data_turkish['ywkph11b'] = trim($compatible_brand);

        // compatible devices
        $compatible_devices = $htmlDom2->query('//strong[@class="ozellik1099"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($compatible_devices) $result_data_turkish['ywkph12b'] = trim($compatible_devices);

        // get battery capacity
        $battery_capacity = $htmlDom2->query('//strong[@class="ozellik1108"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($battery_capacity) $result_data_turkish['wbswcml'] = trim($battery_capacity);

        // get battery type
        $battery_type = $htmlDom2->query('//strong[@class="ozellik1109"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($battery_type) $result_data_turkish['63r9r99'] = getEngNameFromTurkish(trim($battery_type));

        // charging format
        $charg_format = $htmlDom2->query('//strong[@class="ozellik1161"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($charg_format) $result_data_turkish['charg_type'] = trim(str_replace(['Şarj İstasyonu'], ['Charging Station'], $charg_format));

        // get camera
        $camera = $htmlDom2->query('//strong[@class="ozellik1162"]/following::span[1]')->item(0)->nodeValue ?? null; // battery_capacity
        if($camera) $result_data_turkish['lggn0m2'] = getAnswerTurkish($camera);

        // get video
        $video = $htmlDom2->query('//strong[@class="ozellik1182"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($video) $result_data_turkish['t9q0h7hd'] = getAnswerTurkish($video);

        // get required apps adroid
        $required_apps = $htmlDom2->query('//strong[@class="ozellik1189"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($required_apps) $result_data_turkish['ywkph13b'] = trim(str_replace(['Gerekli Uygulama'], [''], $required_apps));

        // get required apps ios
        $required_apps_ios = $htmlDom2->query('//strong[@class="ozellik1230"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($required_apps_ios) $result_data_turkish['awkph14b'] = trim(str_replace(['Gerekli Uygulama'], [''], $required_apps_ios));

        // average battery time life
        $average_battery_life = $htmlDom2->query('//strong[@class="ozellik1158"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($average_battery_life) {
            preg_match('/\d+/ui', $average_battery_life, $output_array);
            if(isset($output_array[0]) &&  !empty($output_array[0])) {
                $result_data_turkish['ywkph14b'] = trim($output_array[0]);
            }
        }

        // low battery life
        $low_battery_life = $htmlDom2->query('//strong[@class="ozellik1159"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($low_battery_life) {
            preg_match('/\d+/ui', $low_battery_life, $output_array);
            if(isset($output_array[0]) && !empty($output_array[0])) {
                $result_data_turkish['ywkph15b'] = trim($output_array[0]);
            }
        }

        // charging time
        $charging_time_battery = $htmlDom2->query('//strong[@class="ozellik1160"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;

        if($charging_time_battery) {
            if(stripos($charging_time_battery, 'akika') !== false) {
                preg_match('/\d+/ui', $charging_time_battery, $out_arr);
                if(isset($out_arr[0]) && !empty($out_arr[0])) {
                    $result_data_turkish['le00i0c'] = number_format( trim($out_arr[0] / 60), 2 );
                }
            } else {
                preg_match('/\d+/ui', $charging_time_battery, $out_arr);
                if(isset($out_arr[0]) && !empty($out_arr[0])) {
                    $result_data_turkish['le00i0c'] = trim($out_arr[0]);
                }
            }
        } else {
            $result_data_turkish['le00i0c'] = null;
        }

        // length(height)
        $size = $htmlDom2->query('//strong[@class="ozellik1101"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($size) {$result_data_turkish['qorav98'] = str_replace('mm','',trim($size));}

        // width
        $width = $htmlDom2->query('//strong[@class="ozellik1102"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($width) { $result_data_turkish['65ihv16'] = trim(str_replace('mm','', $width)); }

        // length alternative
        $length_alternative = $htmlDom2->query('//strong[@class="ozellik1300"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($length_alternative) $result_data_turkish['qorav98'] = trim($length_alternative);

        // width alternative
        $width_alternative = $htmlDom2->query('//strong[@class="ozellik1301"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($width_alternative) $result_data_turkish['65ihv16'] = str_replace('mm','',trim($width_alternative));

        // thick alternative
        $thick_alternative = $htmlDom2->query('//strong[@class="ozellik1302"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($thick_alternative) $result_data_turkish['vbryix7'] = str_replace('mm','',trim($thick_alternative));

        // thickness
        $thickness = $htmlDom2->query('//strong[@class="ozellik1103"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($thickness) { $result_data_turkish['vbryix7'] = str_replace('mm','',trim($thickness)); }

        // weight
        $weight = $htmlDom2->query('//strong[@class="ozellik1104"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($weight) { $result_data_turkish['uanzwi8'] = trim(str_replace(['gr'],'', $weight)); }

        // body weight
        $body_weight = $htmlDom2->query('//strong[@class="ozellik1179"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($body_weight) { $result_data_turkish['ywkph16b'] = trim(str_replace(['gr'], '', $body_weight));}

        // cord weight
        $cord_weight = $htmlDom2->query('//strong[@class="ozellik1180"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($cord_weight) { $result_data_turkish['ywkph17b'] = trim(str_replace(['gr'], '', $cord_weight)); }

        // screen shaped
        $screen_shaped = $htmlDom2->query('//strong[@class="ozellik1120"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($screen_shaped) {
            $result_data_turkish['ywkph18b'] = trim(str_replace(['Dikdörtgen', 'Kare', 'Daire'],['Rectangle', 'Frame', 'Circle'], $screen_shaped));
        }

        // body color
        $body_colors = $htmlDom2->query('//strong[@class="ozellik1100"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($body_colors) {$result_data_turkish['ywkph19b'] = getEngNameFromTurkish(trim($body_colors));}

        // body material
        $body_material = $htmlDom2->query('//strong[@class="ozellik1113"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($body_material) {
            $body_material_info = trim(str_replace(['Fiber Takviyeli Polimer'],['Fiber Reinforced Polymer'], $body_material));
            $result_data_turkish['rt0qxrl'] = getEngNameFromTurkish($body_material_info);
        }

        // cord colors
        $cord_colors = $htmlDom2->query('//strong[@class="ozellik1123"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cord_colors) {$result_data_turkish['ywkph20b'] = getEngNameFromTurkish(trim($cord_colors));}

        // wifi exist
        $wifi_exist = $htmlDom2->query('//strong[@class="ozellik1129"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($wifi_exist) {$result_data_turkish['2pinrcs'] = getAnswerTurkish($wifi_exist);}

        // cord material
        $cord_material = $htmlDom2->query('//strong[@class="ozellik1121"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($cord_material) {$result_data_turkish['3bjbzrk'] = getEngNameFromTurkish(trim($cord_material));} // translate

        // operating system of device
        $operating_sys = $htmlDom2->query('//strong[@class="ozellik1143"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($operating_sys) {
            $result_data_turkish['ui65qcn'] = trim($operating_sys);
        }

        // operating system version
        $os_version = $htmlDom2->query('//strong[@class="ozellik1144"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($os_version) { $result_data_turkish['ui65qcp'] = trim($os_version); }

        // replace cord
        $replace_cord = $htmlDom2->query('//strong[@class="ozellik1122"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($replace_cord) {$result_data_turkish['cc1cqt0'] = getAnswerTurkish($replace_cord);}

        // vibration
        $vibration = $htmlDom2->query('//strong[@class="ozellik1146"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($vibration) {$result_data_turkish['u8sj5wc'] = getAnswerTurkish($vibration);}

        // microfone
        $microfone =  $htmlDom2->query('//strong[@class="ozellik1147"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($microfone) {$result_data_turkish['yq2jcrll'] = getAnswerTurkish($microfone);}

        // microphone features
        $microphone_features = $htmlDom2->query('//strong[@class="ozellik1186"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($microphone_features) { $result_data_turkish['yq2jcrlw'] = trim(str_replace('Gürültü önleyici ikinci mikrofon', 'Second microphone for noise-cancelling', $microphone_features));}

        // speaker
        $speaker = $htmlDom2->query('//strong[@class="ozellik1148"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($speaker) {$result_data_turkish['8l2ljo2'] = getAnswerTurkish($speaker);}

        // infrared
        $infrared = $htmlDom2->query('//strong[@class="ozellik1191"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($infrared) {$result_data_turkish['hwst1n7'] = getAnswerTurkish($infrared);}

        // speaker features
        $speaker_features = $htmlDom2->query('//strong[@class="ozellik1188"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($speaker_features) {$result_data_turkish['yq2jcrlw'] = trim(str_replace(['Tümleşik Ahize'], ['Integrated Handsfree'], $speaker_features));}

        // dust resistance
        $dust_resistance = $htmlDom2->query('//strong[@class="ozellik1150"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($dust_resistance) { $result_data_turkish['cxeplx2'] = getAnswerTurkish($dust_resistance);}

        // dust resistance properties
        $dust_resistance_prop = $htmlDom2->query('//strong[@class="ozellik1152"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($dust_resistance_prop) {$result_data_turkish['cxeplx3'] = trim($dust_resistance_prop);}

        // water resistance
        $water_resistance = $htmlDom2->query('//strong[@class="ozellik1151"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($water_resistance) { $result_data_turkish['cxeplx1'] = getAnswerTurkish($water_resistance);}

        // water resistance prop
        $water_resistance_prop = $htmlDom2->query('//strong[@class="ozellik1153"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($water_resistance_prop) {$result_data_turkish['cxeplx4'] = getEngNameFromTurkish(trim($water_resistance_prop));}

        // services and apps
        $services_apps = $htmlDom2->query('//strong[@class="ozellik1149"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($services_apps) {
            $result_data_turkish['iw93r5f8'] = isValueExists($services_apps, 'larmlar'); // alarm
            $result_data_turkish['19xfliw'] = isValueExists($services_apps, 'atırlatıcılar'); // reminder
            $result_data_turkish['yq2jcrlz'] = isValueExists($services_apps, 'ulaklık'); // headphone
            $result_data_turkish['2pinrcz'] = isValueExists($services_apps, 'hizesi'); // handset
            $result_data_turkish['rd3kh2w'] = isValueExists($services_apps, 'elefonumu Bul'); // find my phone
            $result_data_turkish['pjthco3'] = isValueExists($services_apps, 'yku Mönitör'); // sleep monitor
            $result_data_turkish['vubpb9d'] = isValueExists($services_apps, 'Aktivite Tanımlama');
            $result_data_turkish['58ue5nd'] = isValueExists($services_apps, 'Arayan İsmi Gösterimi');
            $result_data_turkish['h3dd5mz'] = isValueExists($services_apps, 'Ayın Evreleri');
            $result_data_turkish['19xfli1'] = isValueExists($services_apps, 'Ok Google');
            $result_data_turkish['jbsxi9o'] = isValueExists($services_apps, 'Uygulama Bildirimlerini Görüntüleme'); // apps notify
            $result_data_turkish['7ue2z84'] = isValueExists($services_apps, 'Yüzme'); // swim
            $result_data_turkish['zrxr18u1'] = isValueExists($services_apps, 'Atlayış'); // jump
            $result_data_turkish['xpff407'] = isValueExists($services_apps, 'Bisiklet'); // bicycle
            $result_data_turkish['2wb37pk'] = isValueExists($services_apps, 'Golf'); // golf
            $result_data_turkish['x0xgsbl'] = isValueExists($services_apps, 'GPS Saat Senkronizasyonu'); // gps synchro
            $result_data_turkish['zdsda7a'] = isValueExists($services_apps, 'Hava Durumu'); // display weather
            $result_data_turkish['bjlwf02'] = isValueExists($services_apps, 'Kalori Takibi'); // calories
            $result_data_turkish['581d8u2'] = isValueExists($services_apps, 'Kayak'); // calories
            $result_data_turkish['9haky35'] = isValueExists($services_apps, 'Koşu'); // run
            $result_data_turkish['gny9uz9'] = isValueExists($services_apps, 'Kronometre'); // stopwatch
            $result_data_turkish['zrxr18ut'] = isValueExists($services_apps, 'Kürek Çekme'); // rowing
            $result_data_turkish['f7lsmmw9'] = isValueExists($services_apps, 'Müzik Çalar'); // music player
            $result_data_turkish['myof5la'] = isValueExists($services_apps, 'Otomatik Uyku Algılama'); // auto sleep
            $result_data_turkish['2exqey7'] = isValueExists($services_apps, 'Saatimi/Bilekliğimi Bul'); // find my device
            $result_data_turkish['zrxr18u3'] = isValueExists($services_apps, 'Sanal Antreman Partneri'); // virt execis part
            $result_data_turkish['zrxr18u4'] = isValueExists($services_apps, 'Snowboarding'); // snowboard
            $result_data_turkish['h3dd5mg'] = isValueExists($services_apps, 'Snowboarding'); // calendar
            $result_data_turkish['sicux2c'] = isValueExists($services_apps, 'Snowboarding'); // climbing
            $result_data_turkish['19xfli2'] = isValueExists($services_apps, 'Dünya Saatleri');
            $result_data_turkish['rdxjplx'] = isValueExists($services_apps, 'Gelen Çağrı ve Bildirimleri'); // incoming call info
            $result_data_turkish['1rez7re'] = isValueExists($services_apps, 'Geri Sayın Sayacı'); // timer
            $result_data_turkish['2pinrc1'] = isValueExists($services_apps, 'Hands Free Görüşme'); // hands free
            $result_data_turkish['zdsda7a'] = isValueExists($services_apps, 'Hava Durumu');
            $result_data_turkish['19xfli3'] = isValueExists($services_apps, 'Idle Alert'); // idle alert
            $result_data_turkish['bjlwf02'] = isValueExists($services_apps, 'Kalori');
            $result_data_turkish['nh7sleo'] = isValueExists($services_apps, 'Kalp');
            $result_data_turkish['lggn0m2'] = isValueExists($services_apps, 'Kamera');
            $result_data_turkish['gny9uz9'] = isValueExists($services_apps, 'Kronometre');
            $result_data_turkish['x0xgsbz'] = isValueExists($services_apps, 'Navigasyon'); // navigator
            $result_data_turkish['19xfli4'] = isValueExists($services_apps, 'Passbook'); // passbook
            $result_data_turkish['c534jxf'] = isValueExists($services_apps, 'Ses ile komut verme');
            $result_data_turkish['2q53kqi'] = isValueExists($services_apps, 'SMS Görüntüleme ve Yanıtlama');
            $result_data_turkish['h3dd5mg'] = isValueExists($services_apps, 'Takvim');
            $result_data_turkish['bmpc3f3j'] = isValueExists($services_apps, 'Uygulama Yükleyebilme');
            $result_data_turkish['e61kgyj'] = isValueExists($services_apps, 'Uyku Mönitörü');

            if(isValueExists($services_apps, 'Medya Oynatıcı') == '+') {
                $result_data_turkish['qlsq9yh'] = '+';
                $result_data_turkish['a7j5c0b'] = '+';
                $result_data_turkish['ja7w3sb'] = '+';
            }
        }
        // pulsometer
        $pulsometer = $htmlDom2->query('//strong[@class="ozellik1139"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($pulsometer) {$result_data_turkish['nx6ywkn'] = getAnswerTurkish($pulsometer);}

        // ambient light sensor
        $ambient_light_sensor = $htmlDom2->query('//strong[@class="ozellik1140"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($ambient_light_sensor) {$result_data_turkish['h88pkmdy'] = getAnswerTurkish($ambient_light_sensor);}

        // compass
        $compass = $htmlDom2->query('//strong[@class="ozellik1142"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($compass) $result_data_turkish['x0xgsbn'] = getAnswerTurkish($compass);

        // barometer
        $barometer = $htmlDom2->query('//strong[@class="ozellik1175"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($barometer) $result_data_turkish['x399jxz'] = getAnswerTurkish($barometer);

        // uv sensor
        $uv_sensor = $htmlDom2->query('//strong[@class="ozellik1176"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($uv_sensor) $result_data_turkish['ywtcej1'] = getAnswerTurkish($uv_sensor);

        // thermometer
        $thermometer = $htmlDom2->query('//strong[@class="ozellik1190"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($thermometer) $result_data_turkish['k626aelh'] = getAnswerTurkish($thermometer);

        // proximity sensor
        $proxim_sensor = $htmlDom2->query('//strong[@class="ozellik1250"]/following::span[1]/span/text()')->item(0)->nodeValue ?? null;
        if($proxim_sensor) $result_data_turkish['h88pkmd1'] = getAnswerTurkish($proxim_sensor);

        // pedometer
        $pedometer = $htmlDom2->query('//strong[@class="ozellik1141"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($pedometer) {$result_data_turkish['guoawdo'] = getAnswerTurkish($pedometer);}

        // accelerometer
        $accelerometer = $htmlDom2->query('//strong[@class="ozellik1137"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($accelerometer) { $result_data_turkish['h1ddzrt'] = getAnswerTurkish($accelerometer); }

        // gyroscope
        $gyroscope = $htmlDom2->query('//strong[@class="ozellik1138"]/following::span[1]/span/a/text()')->item(0)->nodeValue ?? null;
        if($gyroscope) { $result_data_turkish['ywtcejg'] = getAnswerTurkish($gyroscope);}

        // compability os
        $compability_os = $htmlDom2->query('//strong[@class="ozellik1264"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($compability_os) { $result_data_turkish['ui65qc1'] = trim(str_replace(' ', '', $compability_os)); }

        // compability version
        $compability_ver = $htmlDom2->query('//strong[@class="ozellik1265"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($compability_ver) {$result_data_turkish['ui65qc2'] = trim($compability_ver);}

        // bluetooth
        $bluetooth = $htmlDom2->query('//strong[@class="ozellik1127"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($bluetooth) { $result_data_turkish['p4zld5l'] = getAnswerTurkish($bluetooth);}

        // bluetooth version
        $bluetooth_version = $htmlDom2->query('//strong[@class="ozellik1114"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($bluetooth_version) {$result_data_turkish['p4zld6l'] = trim($bluetooth_version); }

        // wifi
        $wifi = $htmlDom2->query('//strong[@class="ozellik1130"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($wifi) $result_data_turkish['2pinrcv'] = str_replace(['Yazılım güncellemesi gerektirebilir'],['Software update may require'], trim($wifi));

        // nfc
        $nfc = $htmlDom2->query('//strong[@class="ozellik1134"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($nfc) $result_data_turkish['9ee4viy'] = getAnswerTurkish($nfc);

        // usb
        $usb = $htmlDom2->query('//strong[@class="ozellik1131"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($usb) {$result_data_turkish['2q8o92fk'] = getAnswerTurkish($usb);}

        // usb type
        $usb_type = $htmlDom2->query('//strong[@class="ozellik1132"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($usb_type) { $result_data_turkish['p85t8s8z'] = isValueExists($usb_type, 'Micro-USB');} // micro-usb

        // release date
        $release_date = $htmlDom2->query('//strong[@class="ozellik1154"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($release_date) { $result_data_turkish['2lbcv9f'] = trim($release_date); }

        // series
        $series = $htmlDom2->query('//strong[@class="ozellik2137"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($series) { $result_data_turkish['34fksng'] = trim($series);}

        // chipset
        $chipset = $htmlDom2->query('//strong[@class="ozellik1136"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($chipset) {$result_data_turkish['dkg7n4e'] = trim($chipset);}

        // cpu info
        $cpu = $htmlDom2->query('//strong[@class="ozellik1110"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu) {$result_data_turkish['y5xo6x4'] = trim($cpu);}

        // cpu core
        $cpu_core = $htmlDom2->query('//strong[@class="ozellik1112"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($cpu_core) $result_data_turkish['y5xo6x5'] = trim($cpu_core);

        // ram size
        $ram_size = $htmlDom2->query('//strong[@class="ozellik1106"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($ram_size) $result_data_turkish['ej4wq1y'] = trim($ram_size);

        // internal storage
        $flash = $htmlDom2->query('//strong[@class="ozellik1105"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($flash) $result_data_turkish['7aadfmc'] = trim($flash);

        // GPS
        $gps = $htmlDom2->query('//strong[@class="ozellik1167"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($gps) $result_data_turkish['yfvshn2'] = getAnswerTurkish($gps);

        // sim support
        $sim = $htmlDom2->query('//strong[@class="ozellik1169"]/following::span[1]')->item(0)->nodeValue ?? null;
        if($sim) $result_data_turkish['yfvshn2'] = getAnswerTurkish($sim);

        // ger user rating
        $user_rating = $htmlDom2->query('//span[@class="kpuan"]')->item(0)->nodeValue ?? null; // user ratings
        if($user_rating) { $result_data_turkish['bkaqn4m'] = str_replace(['kullanıcı'], ['people'], trim($user_rating));}

        // get type
        $result_data_turkish['drbmx1r'] = 2;

    }
    $result_summary_info_turkish[$url] = $result_data_turkish;
}
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
            <?php foreach($result_summary_info_turkish as $k => $item) : // $result_summary_info ?>
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
pretty_print($result_data_turkish);

///// functions for epey //////
function getEngNameMaterial($data)
{
    return $result = str_replace(
        [
            'Cam','Yekpare','Polikarbonat','Alüminyum','Plastik','Metalik','Görünümlü','Deri','Kauçuk','Seramik','Paslanmaz','Çelik','Aluminum-Magnezyum Alaşımlı Metal',
            'Değiştirilebilir',' ya da ','PoliKarbonat','Suni','Silikon','Titanyum'
        ],
        [
            'Glass','Unibody','Polycarbonate','Aluminum','Plastic','Metallic','','Leather','Rubber','Ceramic','Stainless','Steel','Aluminum-Magnesium Alloy Metal',
            'Replaceable',' or ','Polycarbonate','Artificial','Silicone','Titanium'
        ],
        $data);
}
function getEngNameFromTurkish($str)
{
    return $result = str_replace(
        [
            ' ve ', 'Altın','Alüminyum','Kasa','Kum','Pembesi',' Spor ','Kordon','Uzay','Grisi','Gri','Siyah','Gümüş',' Saf ','Platin','Antrasit','Puslu',
            'Gece','Mavisi','Beyaz','Paslanmaz','Çelik','Milano','Mat','Roze','Taş','Klasik','Tokalı','Kayış','Akıllı','Bileklik','Bilezik','Baklalı',
            'Orta','Büyük','Soğuk','Beton','Naylon','Örme','İnci','Açık','Okyanus','Deri','Kraliyet','Tropik','Kırmızı','Turuncusu','Pembe',
            'Kahverengi','Ayar','Parlak','Mavi','Kutup','Seramik','Polikarbonat','Eloksal ','Termoplastik','Poliüretan','Titanyum','Hipoalerjenik',
            'Kauçuk','Silikon','Manyetik','Kilit','Klips','Örgü','Vulkanize','Tam','Daire','Çift','Katmanlı','Kavisli','Renkli','Değiştirilebilir Para pil','Adet',
            'Bordo','Lacivert','Mor','Fuşya','Turkuaz','Yeşil','Bakır','Krem',"mt'ye kadar su geçirmez", 'Yalnızca su sıçramalarına karşı',
            'Uçuk','Rengi','Kahve','Plastik','Değiştirilebilir Para Pil','Üretan',' ı ','Sarı','Krımızı','Vestel Akıllı Bileklik','Değişebilir Kapak','Fenerbahç','Yanık','Fırtına','Turuncu'
        ],
        [
            ' and ','Gold','Aluminum','Safe','Sand','Pink',' Sport ','cord','Space','Grey','Gray','Black ','Silver',' Pure ','Platinum','Anthracite','Misty',
            'Night','Blue','White','Stainless','Steel','Milan','Matte','Rose','Stone','Classic','Buckle','Slip','Smart','Wrist','Bracelet','Broad beans',
            'Middle','Large','Cold','Concrete','Nylon','Knitting','Pearl','Open','Ocean','Leather','Royal','Tropical','Red','Orange','Pink',
            'Brown','Setting','Bright','Blue','Pole','Ceramic','Polycarbonate','Anodizing ','Thermoplastic','Polyurethane','Titanium','Hypoallergenic',
            'Rubber','Silicone','Magnetic','Lock','Clipping','Mesh','Vulcanized','Full','','Double','Layer','Curved','Color','Replaceable battery','Piece',
            'Maroon','Blue','Purple','Fushya','Turquoise','Green','Copper','Cream','mt waterproof','Only against water splashes',
            'Herpes','Color','Coffee','Plastic','Replaceable Battery','Urethane',' ','Yellow','Red','Vestel Smart Wristband','Cover May Vary','Fenerbahce',
            '','Storm','Orange'
        ],
        $str);
}
function getLinksFromEpey($categoryId)
{
    $url = 'https://www.epey.com/kat/listele/';
    $result_links = [];
    for($i = 1; $i <= 45; $i++ ) { // page // todo uncomment '45'
        $html_turkish = getDataFromApiWithCategory($url, ['sayfa' => $i], $categoryId);
        $htmlDom2 = dom($html_turkish);

        for($k = 0; $k <= 60; $k++) { // item per page todo uncomment '60'
            $links = $htmlDom2->query('//div[@class="detay cell"]/a/@href')->item($k)->nodeValue ?? null;
            if($links) $result_links[] = $links;
        }
    }
    return $result_links;
}
function getAnswerTurkish($str)
{
    return  (strpos(trim($str), 'ar') !== false) ? '+' : '-';
}
/////////////////////
// helper functions

function getOS($haystack, $string)
{
    if(strpos($haystack, $string) !== false) {
        return '+';
    } else {
        return '-';
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
function getAnswer($str)
{
    return  (strpos(trim($str), 'es') != false) ? '+' : '-';
}
function getDataFromApiWithCategory($endpoint, $params = [], $category_id = false)
{
    $url = makeUrl($endpoint, $params);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if($category_id) {
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, "kategori_id=$category_id");
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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