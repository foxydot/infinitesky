<?php
function msdlab_add_press_scripts() {
    if(!is_admin()){
        wp_enqueue_script('msd-press-jquery',get_stylesheet_directory_uri().'/lib/js/press-jquery.js',array('jquery','bootstrap-jquery'));
    }
}

remove_action('genesis_loop','genesis_do_loop');
add_action('genesis_loop',array('MSDNewsCPT','special_loop'));
//add_action('wp_enqueue_scripts', 'msdlab_add_press_scripts');

add_action('wp_enqueue_scripts', 'msdlab_add_press_styles');


function msdlab_add_press_styles() {
    if(!is_admin()){
        wp_enqueue_style('msd-press-style',get_stylesheet_directory_uri().'/lib/css/press.css');
    }
}
genesis();