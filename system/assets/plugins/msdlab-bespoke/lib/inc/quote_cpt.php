<?php
if (!class_exists('MSDQuoteCPT')) {
    class MSDQuoteCPT {
        //Properties
        var $cpt = 'quote';
        //Methods
        /**
         * PHP 4 Compatible Constructor
         */
        public function MSDQuoteCPT(){$this->__construct();}

        /**
         * PHP 5 Constructor
         */
        function __construct(){
            global $current_screen;
            //Actions
            add_action( 'init', array(&$this,'register_taxonomies') );
            add_action( 'init', array(&$this,'register_cpt') );
            add_action( 'init', array(&$this,'register_metaboxes') );
            add_action('admin_print_scripts', array(&$this,'add_admin_scripts') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            add_action('admin_footer',array(&$this,'info_footer_hook') );
            // important: note the priority of 99, the js needs to be placed after tinymce loads
            add_action('admin_print_footer_scripts',array(&$this,'print_footer_scripts'),99);

            //Filters
            add_filter( 'enter_title_here', array(&$this,'change_default_title') );

            //Shortcodes
            add_shortcode('quote', array(&$this,'quote_shortcode_handler'));

            //add cols to manage panel
            add_filter( 'manage_edit-'.$this->cpt.'_columns', array(&$this,'my_edit_columns' ));
            add_action( 'manage_'.$this->cpt.'_posts_custom_column', array(&$this,'my_manage_columns'), 10, 2 );
        }


        function register_taxonomies(){

            $labels = array(
                'name' => _x( 'Quote categories', 'quote-category' ),
                'singular_name' => _x( 'Quote category', 'quote-category' ),
                'search_items' => _x( 'Search quote categories', 'quote-category' ),
                'popular_items' => _x( 'Popular quote categories', 'quote-category' ),
                'all_items' => _x( 'All quote categories', 'quote-category' ),
                'parent_item' => _x( 'Parent quote category', 'quote-category' ),
                'parent_item_colon' => _x( 'Parent quote category:', 'quote-category' ),
                'edit_item' => _x( 'Edit quote category', 'quote-category' ),
                'update_item' => _x( 'Update quote category', 'quote-category' ),
                'add_new_item' => _x( 'Add new quote category', 'quote-category' ),
                'new_item_name' => _x( 'New quote category name', 'quote-category' ),
                'separate_items_with_commas' => _x( 'Separate quote categories with commas', 'quote-category' ),
                'add_or_remove_items' => _x( 'Add or remove quote categories', 'quote-category' ),
                'choose_from_most_used' => _x( 'Choose from the most used quote categories', 'quote-category' ),
                'menu_name' => _x( 'Quote categories', 'quote-category' ),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true, //we want a "category" style taxonomy, but may have to restrict selection via a dropdown or something.

                'rewrite' => array('slug'=>'quote-category','with_front'=>false),
                'query_var' => true
            );

            register_taxonomy( 'quote_category', array($this->cpt), $args );
        }

        function register_cpt() {

            $labels = array(
                'name' => _x( 'Quote', 'quote' ),
                'singular_name' => _x( 'Quote', 'quote' ),
                'add_new' => _x( 'Add New', 'quote' ),
                'add_new_item' => _x( 'Add New Quote', 'quote' ),
                'edit_item' => _x( 'Edit Quote', 'quote' ),
                'new_item' => _x( 'New Quote', 'quote' ),
                'view_item' => _x( 'View Quote', 'quote' ),
                'search_items' => _x( 'Search Quote', 'quote' ),
                'not_found' => _x( 'No quote found', 'quote' ),
                'not_found_in_trash' => _x( 'No quote found in Trash', 'quote' ),
                'parent_item_colon' => _x( 'Parent Quote:', 'quote' ),
                'menu_name' => _x( 'Quote', 'quote' ),
            );

            $args = array(
                'labels' => $labels,
                'hierarchical' => false,
                'description' => 'Quote',
                'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'genesis-cpt-archives-settings' ),
                'taxonomies' => array( 'quote_category'),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 20,

                'show_in_nav_menus' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => true,
                'has_archive' => true,
                'query_var' => true,
                'can_export' => true,
                'rewrite' => array('slug'=>'about/press','with_front'=>false),
                'capability_type' => 'post',
                'menu_icon' => 'dashicons-format-quote',
            );

            register_post_type( $this->cpt, $args );
        }


        function register_metaboxes(){
            global $quote_info;
            $quote_info = new WPAlchemy_MetaBox(array
            (
                'id' => '_quote_information',
                'title' => 'Quote Info',
                'types' => array($this->cpt),
                'context' => 'normal',
                'priority' => 'high',
                'template' => plugin_dir_path(dirname(__FILE__)).'/template/metabox-quote.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_quote_' // defaults to NULL
            ));
        }


        function add_admin_scripts() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
            }
        }

        function add_admin_styles() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'/css/meta.css');
            }
        }

        function print_footer_scripts()
        {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                print '<script type="text/javascript">/* <![CDATA[ */
					jQuery(function($)
					{
						var i=1;
						$(\'.customEditor textarea\').each(function(e)
						{
							var id = $(this).attr(\'id\');
			 
							if (!id)
							{
								id = \'customEditor-\' + i++;
								$(this).attr(\'id\',id);
							}
			 
							tinyMCE.execCommand(\'mceAddControl\', false, id);
			 
						});
					});
				/* ]]> */</script>';
            }
        }

        function info_footer_hook()
        {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                ?><script type="text/javascript">
                    jQuery('#postdivrich').before(jQuery('#_quote_info_metabox'));
                </script><?php
            }
        }


        function my_edit_columns( $columns ) {

            $columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __( 'Title' ),
                $this->cpt.'_category' => __( 'Categories' ),
                $this->cpt.'_tag' => __( 'Tags' ),
                'author' => __( 'Author' ),
                'date' => __( 'Date' )
            );

            return $columns;
        }

        function my_manage_columns( $column, $post_id ) {
            global $post;

            switch( $column ) {
                /* If displaying the 'logo' column. */
                case $this->cpt.'_category' :
                    $taxonomy = $column;
                    if ( $taxonomy ) {
                        $taxonomy_object = get_taxonomy( $taxonomy );
                        $terms = get_the_terms( $post->ID, $taxonomy );
                        if ( is_array( $terms ) ) {
                            $out = array();
                            foreach ( $terms as $t ) {
                                $posts_in_term_qv = array();
                                if ( 'post' != $post->post_type ) {
                                    $posts_in_term_qv['post_type'] = $post->post_type;
                                }
                                if ( $taxonomy_object->query_var ) {
                                    $posts_in_term_qv[ $taxonomy_object->query_var ] = $t->slug;
                                } else {
                                    $posts_in_term_qv['taxonomy'] = $taxonomy;
                                    $posts_in_term_qv['term'] = $t->slug;
                                }

                                $label = esc_html( sanitize_term_field( 'name', $t->name, $t->term_id, $taxonomy, 'display' ) );
                                $out[] = $this->get_edit_link( $posts_in_term_qv, $label );
                            }
                            /* translators: used between list items, there is a space after the comma */
                            echo join( __( ', ' ), $out );
                        } else {
                            echo '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">' . $taxonomy_object->labels->no_terms . '</span>';
                        }
                    }
                    break;
                default :
                    break;
            }
        }

        function change_default_title( $title ){
            global $current_screen;
            if  ( $current_screen->post_type == $this->cpt ) {
                return __('Quote Hint','quote');
            } else {
                return $title;
            }
        }

        function quote_shortcode_handler($atts, $content){
            extract(shortcode_atts( array(
                'title' => 'Quotes',
                'count' => 5,
                $this->cpt.'_category' => false,
            ), $atts ));
            $args = array(
                'post_type' => 'quote',
                'showposts' => $count,

            );
            if(${$this->cpt.'_category'}) {
                $class = $this->cpt.'_category'.${$this->cpt.'_category'};
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $this->cpt.'_category',
                        'field'    => 'slug',
                        'terms'    => ${$this->cpt.'_category'},
                    ),
                );
            } else {
                $class = $this->cpt.'_all';
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'quote_category',
                        'field'    => 'slug',
                        'terms'    => $allowed_terms,
                    ),
                );
            }

            $recents = new WP_Query($args);
            if($recents->have_posts()) {
                global $post;
                $ret[] = '<section class="widget quote-widget clearfix '.$class.'">
<h3 class="widgettitle widget-title">' . $title . ' </h3>
<div class="wrap">
<dl class="quote-widget-list">';
//start loop
                ob_start();
                while($recents->have_posts()) {
                    $recents->the_post();
                    print '<li><div class="quote-content">'.$post->get_the_content().'</div><div class="quote-attribution"></div></li>';
                } //end loop
                $ret[] = ob_get_contents();
                ob_end_clean();
                $ret[] = '</dl></div></section>';
            } //end loop check

            wp_reset_postdata();

            return implode("\n",$ret);
        }

        function special_loop(){
            global $post;
            if ( have_posts() ) :
                do_action( 'genesis_before_while' );
                print '<ul class="publication-list quote-display">';
                while ( have_posts() ) : the_post();
                    $url = get_post_meta($post->ID,'_quote_quoteurl',1);
                    $excerpt = $post->post_excerpt?$post->post_excerpt:msd_trim_headline($post->post_content);
                    $link = strlen($url)>4?msdlab_http_sanity_check($url):get_permalink($post->ID);
                    $background = msdlab_get_thumbnail_url($post->ID,'medium');
                    print '
    <li>
        <div class="col-sm-8">
            <div class="quote-info">
            <h3><a href="'.$link.'">'.$post->post_title.' ></a></h3>
                <div>
                    '.date('F j, Y',strtotime($post->post_date)).'
                    <div class="excerpt">'.$excerpt.'</div>
                    '.do_shortcode('[button url="'.$link.'"]Read More[/button]').'
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="quote-logo">
                <a href="'.$link.'" style="background-image:url('.$background.')">&nbsp;
                </a>
            </div>
        </div>
    </li>';
                endwhile;
                print '</ul>';
                do_action( 'genesis_after_endwhile' );
            endif;
        }


        function hide_single_quote(){
            if(!is_single())
                return;
            if(get_query_var('post_type') == $this->cpt){
                global $wp_query;
                wp_redirect(get_post_meta($wp_query->post->ID,'_quote_quoteurl',true));
                return;
            } else {
                return;
            }
        }
    } //End Class
} //End if class exists statement