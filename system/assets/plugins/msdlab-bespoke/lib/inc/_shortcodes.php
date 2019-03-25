<?php
if(!class_exists('MSDLab_Bespoke_Shortcodes')){
    class MSDLab_Bespoke_Shortcodes{
        var $size;

        function __construct()
        {
            add_shortcode('latest',array(&$this,'msdlab_latest_shortcode_handler'));
            add_shortcode('blog',array(&$this,'msdlab_blog_shortcode_handler'));
            add_shortcode('rollbox_set',array(&$this,'rollbox_set_shortcode_handler'));
            add_shortcode('rollbox',array(&$this,'rollbox_shortcode_handler'));
            add_shortcode('jazz-jobboard',array(&$this,'jazz_jobboard_shortcode_handler'));
            add_action('admin_head', array(&$this,'codex_custom_help_tab'));

            add_image_size('tatamithumb',400,200);
        }


        function codex_custom_help_tab() {
            global $current_screen;
            if($current_screen->base != 'post')
                return;

            // Setup help tab args.
            $args = array(
                'id'      => 'msdlab_shortcodes', //unique id for the tab
                'title'   => 'MSDLab Shortcodes', //unique visible title for the tab
                'content' => '<h3>[latest]</h3>
                          <p>Displays the latest content from the Posts archive.</p>
                          <p>Attributes (Defaults)
                          <dl>
                          <dt>post_type (post)</dt>
                          <dd>(str) selects post type to display</dd>
                          <dt>posts_per_page (1)</dt>
                          <dd>(int) determines number of posts selected</dd>
                            </dl>
                          </p>
                          <h3>[rollbox_set][/rollbox_set]</h3>
                          <p>Creates a set of rollboxes</p>
                          <p>Attributes (Defaults)
                          <dl>
                          <dt>size (3)</dt>
                          <dd>(int) determines the number of columns for the set</dd>
                            </dl>
                          </p>
                          <h3>[rollbox][/rollbox]</h3>
                          <p>Adds rollboxes. Must be nested in [rollbox_set]</p>
                          <p>Attributes (Defaults)
                          <dl>
                          <dt>url (false)</dt>
                          <dd>(str) the url to link the rollbox to</dd>
                          <dt>icon (false)</dt>
                          <dd>(str) the icon name to add</dd>
                          <dt>swap_white (false)</dt>
                          <dd>(bool) wether to swap the icon to the white version on rollover</dd>
                            </dl>
                          </p>
                          <p><b>Subshortcodes</b>
                          <dl>
                          <dt>[on][/on]</dt>
                          <dd>wraps content to be shown on the "over" state on non-mobile devices</dd>
                            </dl>
                            </p>
                          
                          ',  //actual help text
            );

            // Add the help tab.
            $current_screen->add_help_tab( $args );
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
                printf( '<a href="%s" title="%s" class="latest_image_wrapper aligncenter">%s</a>', get_permalink(), the_title_attribute('echo=0'), genesis_get_image(array('size' => 'tatamithumb')) );
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


        function msdlab_blog_shortcode_handler($atts){
            $args = (shortcode_atts( array(
                'post_type' => 'post',
                'posts_per_page' => '1',
            ), $atts ));
            global $post;
            $my_query = new WP_Query($args);
            ob_start();
            if( $my_query->have_posts() ) :
                print '<div class="blog-sc-grid">';
            remove_action('genesis_entry_content', 'genesis_do_post_content');
            remove_action('genesis_entry_content', 'genesis_do_post_image',8);
            remove_action( 'genesis_entry_header', 'msdlab_do_post_subtitle', 13);
            remove_action( 'genesis_entry_header', 'genesis_do_post_title');
            add_action( 'genesis_entry_header', array(&$this,'msdlab_shortcode_grid_header'),1);
            add_action( 'genesis_entry_footer', array(&$this,'msdlab_shortcode_grid_footer'),100);
            add_action( 'genesis_entry_header', 'genesis_do_post_title');
            add_filter( 'post_class', array(&$this,'msdlab_shortcode_grid_post_classes') );
            add_filter( 'genesis_get_image_default_args', array(&$this,'msdlab_shortcode_grid_image' ));

                while ( $my_query->have_posts() ) : $my_query->the_post();
                do_action( 'genesis_before_entry' );
                genesis_markup( array(
                    'open'    => '<article %s>',
                    'context' => 'entry',
                ) );
                do_action( 'genesis_entry_header' );
                do_action( 'genesis_before_entry_content' );
                printf( '<div %s>', genesis_attr( 'entry-content' ) );
                do_action( 'genesis_entry_content' );
                echo '</div>';
                do_action( 'genesis_after_entry_content' );
                do_action( 'genesis_entry_footer' );
                genesis_markup( array(
                    'close'   => '</article>',
                    'context' => 'entry',
                ) );
                do_action( 'genesis_after_entry' );
            endwhile;
            print '</div>';

                add_action('genesis_entry_content', 'genesis_do_post_content');
                add_action('genesis_entry_content', 'genesis_do_post_image',8);
                add_action( 'genesis_entry_header', 'msdlab_do_post_subtitle', 13);
                add_action( 'genesis_entry_header', 'genesis_do_post_title');
                remove_action( 'genesis_entry_header', array(&$this,'msdlab_shortcode_grid_header'),1);
                remove_action( 'genesis_entry_footer', array(&$this,'msdlab_shortcode_grid_footer'),100);
                remove_action( 'genesis_entry_header', 'genesis_do_post_title');
                remove_filter( 'post_class', array(&$this,'msdlab_shortcode_grid_post_classes') );
                remove_filter( 'genesis_get_image_default_args', array(&$this,'msdlab_shortcode_grid_image' ));
            endif;
            $ret = ob_get_contents();
            wp_reset_postdata();
            ob_end_clean();

            return $ret;
        }

        function msdlab_shortcode_grid_header(){
            global $post;
            print '<div class="entry-wrap">';
            printf( '<a href="%s" title="%s" class="grid_image_wrapper">%s</a>', get_permalink(), the_title_attribute('echo=0'), genesis_get_image() );
        }

        function msdlab_shortcode_grid_footer(){
            print '<hr class="clear" /></div>';
        }

        function msdlab_shortcode_grid_content(){
            global $post;
            the_excerpt();
            printf( '<a href="%s" title="%s" class="readmore-button alignright">%s</a>', get_permalink(), the_title_attribute('echo=0'), 'Read more >' );

        }

        function msdlab_shortcode_grid_post_classes( $classes ) {
                remove_action( 'genesis_entry_footer', 'genesis_post_meta');
                    $classes[] = 'genesis-teaser';
                    $classes[] = 'col-sm-4';
                    $classes[] = 'col-xs-12';
            return $classes;
        }

        function msdlab_shortcode_grid_image( $defaults ) {
            $defaults['size'] = 'child_thumbnail';
            return $defaults;
        }

        function rollbox_set_shortcode_handler($atts,$content){
            extract(shortcode_atts( array(
                'size' => 3,
            ), $atts ));
            $this->size = $size;
            return '<div class="rollbox-set cols-'.$size.' row">'.do_shortcode($content).'</div>';
        }

        function rollbox_shortcode_handler($atts,$content){
            extract(shortcode_atts( array(
                'url' => false,
                'icon' => false,
                'swap_white' => false,
            ), $atts ));
            switch($this->size){
                case 6:
                    $class = 'rollbox col-md-2 col-xs-12';
                    break;
                case 5:
                    $class = 'rollbox col-md-2 col-xs-12';
                    break;
                case 4:
                    $class = 'rollbox col-md-3 col-sm-6 col-xs-12';
                    break;
                case 3:
                default:
                    $class = 'rollbox col-md-4 col-sm-6 col-xs-12';
                    break;
            }
            if($swap_white){
                $iconon = $icon . '-w';
            } else {
                $iconon = $icon;
            }
            $content = preg_replace('/\[on\]/','<div class="rollbox-on"><div class="rollbox-wrapper-on"><img src="'.get_stylesheet_directory_uri().'/lib/images/icons/'.$iconon.'.svg" class="icon" alt="'.$icon.' icon" />',$content);
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

        function jazz_jobboard_shortcode_handler($atts){
            extract(shortcode_atts( array(
                'src' => '//app.jazz.co/widgets/basic/create/Infinitive',
            ), $atts ));
            return '<script type="text/javascript" src="'.$src.'" charset="utf-8"></script>';
        }
    }

}

new MSDLab_Bespoke_Shortcodes();