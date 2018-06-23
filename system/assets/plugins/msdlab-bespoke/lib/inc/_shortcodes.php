<?php
if(!class_exists('MSDLab_Bespoke_Shortcodes')){
    class MSDLab_Bespoke_Shortcodes{
        var $size;

        function __construct()
        {
            add_shortcode('latest',array(&$this,'msdlab_latest_shortcode_handler'));
            add_shortcode('rollbox_set',array(&$this,'rollbox_set_shortcode_handler'));
            add_shortcode('rollbox',array(&$this,'rollbox_shortcode_handler'));
        }

        function msdlab_latest_shortcode_handler($atts){
            $args = (shortcode_atts( array(
                'post_type' => 'post',
                'posts_per_page' => '1',
            ), $atts ));
            global $post;
            $my_query = new WP_Query($args);
            ob_start();
            while ( $my_query->have_posts() ) : $my_query->the_post();
                print '<article>';
                //printf( '<a href="%s" title="%s" class="latest_image_wrapper alignleft">%s</a>', get_permalink(), the_title_attribute('echo=0'), genesis_get_image(array('size' => 'thumbnail')) );
                print '<div>';
                printf( '<a href="%s" title="%s" class="latest-title"><h3>%s</h3></a>', get_permalink(), the_title_attribute('echo=0'), get_the_title() );
                print msdlab_get_excerpt(get_the_ID());
                print '</div>';
                printf( '<a href="%s" title="%s" class="btn latest-title">Read</a>', get_permalink(), the_title_attribute('echo=0') );
                print '</article>';
            endwhile;
            $ret = ob_get_contents();
            ob_end_clean();
            return $ret;
        }

        function rollbox_set_shortcode_handler($atts,$content){
            extract(shortcode_atts( array(
                'size' => 3,
            ), $atts ));
            $this->size = $size;
            return '<div class="rollbox-set cols-'.$size.'">'.do_shortcode($content).'</div>';
        }

        function rollbox_shortcode_handler($atts,$content){
            extract(shortcode_atts( array(
                'url' => false,
                'icon' => false,
            ), $atts ));
            switch($this->size){
                case 4:
                    $class = 'rollbox col-md-3 col-sm-6 col-xs-12';
                    break;
                case 3:
                default:
                    $class = 'rollbox col-md-4 col-sm-6 col-xs-12';
                    break;
            }
            $content = preg_replace('/\[on\]/','<div class="rollbox-on"><div class="rollbox-wrapper-on"><img src="'.get_stylesheet_directory_uri().'/lib/images/icons/'.$icon.'.svg" class="icon" alt="'.$icon.' icon" />',$content);
            $content = preg_replace('/\[\/on\]/','</div></div>',$content);
            if($icon){
                $content = '<img src="'.get_stylesheet_directory_uri().'/lib/images/icons/'.$icon.'.svg" class="icon" alt="'.$icon.' icon" />'.$content;
            }
            if($url){
                $content = '<a href="'.$url.'">'.$content.'</a>';
            }
            $content = '<div class="'.$class.'"><div class="rollbox-wrapper">'.$content.'</div></div>';
            return $content;
        }
    }
}

new MSDLab_Bespoke_Shortcodes();