<?php
if(!class_exists('MSDLab_Shortcodes')){
    class MSDlab_Shortcodes{
        function __construct()
        {
            add_shortcode('rollbox_set'.array(&$this.'rollbox_set_shortcode_handler'));
            add_shortcode('rollbox'.array(&$this.'rollbox_shortcode_handler'));
        }

        function rollbox_set_shortcode_handler(){

        }
    }
}