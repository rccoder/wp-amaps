<?php
/*
Plugin Name: wp_amaps_plugin
Plugin URI: https://github.com/rccoder/wp-amaps-plugin
Description: 基于高德地图的WordPress插件，利用短代码可以在任何你想插入地图的地方插入有标记的地图。支持自定义尺寸大小,自定义其他描述
Version: 1.0
Author: rccoder
Author URI: http://www.rccoder.net
Copyright 2015  rccoder  (email : rccoder.net@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(!function_exists('amaps_init')) {
    add_action('init', 'wp_amaps_init');
}

function wp_amaps_init() {
    add_shortcode('amaps', 'wp_amaps_content_init');
}

function wp_amaps_content_init($atts, $content = null) {
    extract(shortcode_atts(array(
        'id' => '',
        'w'=>'',
        'h'=>'',
        'lon'=>'',
        'lat'=>'',
        'address'=>'',
        'info' => '',
        ) ,$atts));

    $id = $id ? $id : 'wp_amaps';
    $w = $w ? $w : '100%';
    $h = $h ? $h : '300px';
    $lon = $lon ? $lon : false;
    $lat = $lat ? $lat : false;
    $address = $address ? $address : false;
    $info = $info ? $info : false;

    if (($lon && $lat) || $address) {
        $output = '<div class="wp_amaps" style="weight: '.$w.'; height: '.$h.'"><div id=">'.$id.'"><div id="container"></div><div><div>';
        $output .= '
            <link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
            <script src="http://webapi.amap.com/maps?v=1.3&key=8ef1ddfea330aa3124a11f4aeaac187e"></script>
            <script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
            <style type="text/css">
                .amap-icon img {
                    margin: 0 !important;
                }
            </style>
            <script>
                var map = new AMap.Map("container", {
                    resizeEnable: true,
                    center: [';
        if($lon && $lat) {
            $output .= $lon.','.$lat;
            if (! $address) {
                $tep = file_get_contents("http://restapi.amap.com/v3/geocode/regeo?output=json&location=".$lon.",".$lat."&key=71587fdfc4998f07b4ddd25846f193b7&radius=1000");
                $tep = json_decode($tep);
                $address = $tep->regeocode->formatted_address;
            }
        } else if ($address){
            $tep = file_get_contents("http://restapi.amap.com/v3/geocode/geo?address=".$address."&output=json&key=71587fdfc4998f07b4ddd25846f193b7");
            $tep = json_decode($tep);
            $tep = $tep->geocodes[0]->location;
            $output .= $tep;
        }
        $output .= '],
                    zoom: 13
                });
                var marker = new AMap.Marker({
                    position: map.getCenter()
                });
                marker.setMap(map);
                marker.setTitle("';
        $output .= $address;
        $output .= '");
                marker.setLabel({
                    offset: new AMap.Pixel(20, 20),
                    content: "';
        if ($info) {
            $output .= $info;
        } else {
            $output .= $address;
        }
        $output .= '"
                });
            </script>
        ';
        return $output;
    }
}
?>