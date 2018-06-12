<?php
if (!class_exists('MSDLab_Video_Background_Support')) {
    class MSDLab_Video_Background_Support
    {
        //Properties
        private $options;

        //Methods
        function __construct($options)
        {
            global $current_screen;
            //"Constants" setup
            //Actions
            add_action('genesis_before_header', array(&$this, 'do_video_background'),10);

            //Filters
        }

        function do_video_background(){
            if(is_admin()){return;}
            if(wp_is_mobile()){return;}
            if(!is_front_page() && get_section() != 'solutions'){return;}
            $videosrc = get_stylesheet_directory_uri().'/lib/images/bluest.mp4';
            print '<!-- The video -->
<video autoplay muted loop id="bkgVideo">
  <source src="'.$videosrc.'" type="video/mp4">
</video>
<style>
#bkgVideo {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    min-height: 120%;
    min-width: 120%;
    z-index: -1000;
}

</style>';
        }
    }
}