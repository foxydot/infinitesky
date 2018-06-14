<?php
/**
 * @package MSD Publication CPT
 * @version 0.1
 */

class MSDCaseStudyCPT {

	/**
    * PHP 4 Compatible Constructor
    */
    public function MSDCaseStudyCPT(){$this->__construct();}

    /**
     * PHP 5 Constructor
     */
    function __construct(){
        //Actions
        add_action( 'init', array(&$this,'register_taxonomies') );
        add_action( 'init', array(&$this,'register_cpt_casestudy') );
        
        //Filters
        add_filter( 'genesis_attr_casestudy', array(&$this,'custom_add_casestudy_attr') );
        
        //Shortcodes
        add_shortcode( 'case-studies', array(&$this,'list_case_studies') );
        add_shortcode('casestudies',  array(&$this,'msdlab_casestudies_special_loop_shortcode_handler'));
    }
	
    public function register_taxonomies() {
    
        $solution_labels = array( 
            'name' => _x( 'Solution areas', 'case-study' ),
            'singular_name' => _x( 'Solution area', 'case-study' ),
            'search_items' => _x( 'Search Solution areas', 'case-study' ),
            'popular_items' => _x( 'Popular Solution areas', 'case-study' ),
            'all_items' => _x( 'All Solution areas', 'case-study' ),
            'parent_item' => _x( 'Parent Solution area', 'case-study' ),
            'parent_item_colon' => _x( 'Parent Solution area:', 'case-study' ),
            'edit_item' => _x( 'Edit Solution area', 'case-study' ),
            'update_item' => _x( 'Update Solution area', 'case-study' ),
            'add_new_item' => _x( 'Add New Solution area', 'case-study' ),
            'new_item_name' => _x( 'New Solution area Name', 'case-study' ),
            'separate_items_with_commas' => _x( 'Separate Solution areas with commas', 'case-study' ),
            'add_or_remove_items' => _x( 'Add or remove Solution areas', 'case-study' ),
            'choose_from_most_used' => _x( 'Choose from the most used Solution areas', 'case-study' ),
            'menu_name' => _x( 'Solution areas', 'case-study' ),
        );
    
        $solution_args = array( 
            'labels' => $solution_labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true,
    
            'rewrite' => array('slug'=>'case-study/by-solution','with_front'=>true),
            'query_var' => true
        );
    
        register_taxonomy( 'msd_practice-area', array('msd_casestudy'), $solution_args );
        
        $industry_labels = array( 
            'name' => _x( 'Industries', 'case-study' ),
            'singular_name' => _x( 'Industry', 'case-study' ),
            'search_items' => _x( 'Search Industries', 'case-study' ),
            'popular_items' => _x( 'Popular Industries', 'case-study' ),
            'all_items' => _x( 'All Industries', 'case-study' ),
            'parent_item' => _x( 'Parent Industry', 'case-study' ),
            'parent_item_colon' => _x( 'Parent Industry:', 'case-study' ),
            'edit_item' => _x( 'Edit Industry', 'case-study' ),
            'update_item' => _x( 'Update Industry', 'case-study' ),
            'add_new_item' => _x( 'Add New Industry', 'case-study' ),
            'new_item_name' => _x( 'New Industry Name', 'case-study' ),
            'separate_items_with_commas' => _x( 'Separate Industries with commas', 'case-study' ),
            'add_or_remove_items' => _x( 'Add or remove Industries', 'case-study' ),
            'choose_from_most_used' => _x( 'Choose from the most used Industries', 'case-study' ),
            'menu_name' => _x( 'Industries', 'case-study' ),
        );
    
        $industry_args = array( 
            'labels' => $industry_labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true,
    
            'rewrite' => array('slug'=>'case-study/by-industry','with_front'=>true),
            'query_var' => true
        );
    
        register_taxonomy( 'msd_industry', array('msd_casestudy'), $industry_args );
        
        
        $function_labels = array( 
            'name' => _x( 'Functions', 'case-study' ),
            'singular_name' => _x( 'Function', 'case-study' ),
            'search_items' => _x( 'Search Functions', 'case-study' ),
            'popular_items' => _x( 'Popular Functions', 'case-study' ),
            'all_items' => _x( 'All Functions', 'case-study' ),
            'parent_item' => _x( 'Parent Function', 'case-study' ),
            'parent_item_colon' => _x( 'Parent Function:', 'case-study' ),
            'edit_item' => _x( 'Edit Function', 'case-study' ),
            'update_item' => _x( 'Update Function', 'case-study' ),
            'add_new_item' => _x( 'Add New Function', 'case-study' ),
            'new_item_name' => _x( 'New Function Name', 'case-study' ),
            'separate_items_with_commas' => _x( 'Separate Functions with commas', 'case-study' ),
            'add_or_remove_items' => _x( 'Add or remove Functions', 'case-study' ),
            'choose_from_most_used' => _x( 'Choose from the most used Functions', 'case-study' ),
            'menu_name' => _x( 'Functions', 'case-study' ),
        );
    
        $function_args = array( 
            'labels' => $function_labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true,
    
            'rewrite' => array('slug'=>'case-study/by-function','with_front'=>true),
            'query_var' => true
        );
    
        register_taxonomy( 'msd_function', array('msd_casestudy'), $function_args );
        flush_rewrite_rules();
    }
    
	function register_cpt_casestudy() {
	
	    $labels = array( 
	        'name' => _x( 'Case Studies', 'case-study' ),
	        'singular_name' => _x( 'Case Study', 'case-study' ),
	        'add_new' => _x( 'Add New', 'case-study' ),
	        'add_new_item' => _x( 'Add New Case Study', 'case-study' ),
	        'edit_item' => _x( 'Edit Case Study', 'case-study' ),
	        'new_item' => _x( 'New Case Study', 'case-study' ),
	        'view_item' => _x( 'View Case Study', 'case-study' ),
	        'search_items' => _x( 'Search Case Studies', 'case-study' ),
	        'not_found' => _x( 'No case studies found', 'case-study' ),
	        'not_found_in_trash' => _x( 'No case studies found in Trash', 'case-study' ),
	        'parent_item_colon' => _x( 'Parent Case Study:', 'case-study' ),
	        'menu_name' => _x( 'Case Studies', 'case-study' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Case Studies',
	        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail','genesis-cpt-archives-settings'),
	        'taxonomies' => array( 'genre' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'menu_position' => 20,
	        
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'has_archive' => true,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => array('slug'=>'thought-leadership/case-studies','with_front'=>false),
	        'capability_type' => 'post'
	    );
	
	    register_post_type( 'msd_casestudy', $args );
	    flush_rewrite_rules();
	}
		
	function list_case_studies( $atts ) {
		global $subtitle,$documents;
		extract( shortcode_atts( array(
		), $atts ) );
		
		$args = array( 'post_type' => 'msd_casestudy', 'numberposts' => 0, );

		$items = get_posts($args);
	    foreach($items AS $item){ 
	    	$subtitle->the_meta($item->ID);
            $documents->the_meta($item->ID);
	    	$excerpt = $item->post_excerpt?$item->post_excerpt:msd_trim_headline($item->post_content);
	     	$publication_list .= '
	     	<li>
				<strong><a href="'.get_permalink($item->ID).'">'.$item->post_title.'</a>:</strong> '.$subtitle->get_the_value('subtitle').'
				<div class="clear"></div>
			</li>';
	
	     }
		
		return '<ul class="publication-list case-studies">'.$publication_list.'</ul><div class="clear"></div>';
	}	
    
    function msdlab_casestudies_special_loop(){
        $args = array(
        );
        print self::msdlab_casestudies_special($args);
    }
    function msdlab_casestudies_special_loop_shortcode_handler($atts){
        $args = shortcode_atts( array(
        ), $atts );
        remove_filter('the_content','wpautop',12);
        return self::msdlab_casestudies_special($args);
    }
    
    function msdlab_casestudies_special($args){
        global $post,$case_study_key;
        $origpost = $post;
        $defaults = array(
            'posts_per_page' => 1,
            'post_type' => 'msd_casestudy',
        );
        $args = array_merge($defaults,$args);
        //set up result array
        $results = array();
        //get all practice areas
        $terms = get_terms('msd_practice-area');
        //do a query for each practice area?
        foreach($terms AS $term){
            $args['msd_practice-area'] = $term->slug;
            $this_result = get_posts($args);
            $results[$term->slug] = $this_result;
            $results[$term->slug]['term'] = $term;
        }
        //format result
        foreach($results AS $case_study_key => $result){
            $post = $result[0];
            $ret .= genesis_markup( array(
                    'html5'   => '<article %s>',
                    'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
                    'context' => 'casestudy',
                    'echo' => false,
                ) );
                $ret .= genesis_markup( array(
                        'html5' => '<header>',
                        'xhtml' => '<div class="header">',
                        'echo' => false,
                    ) ); 
                    $ret .= '<a href="'.get_term_link($result['term']).'">More '.$result['term']->name.' ></a>';
                $ret .= genesis_markup( array(
                        'html5' => '</header>',
                        'xhtml' => '</div>',
                        'echo' => false,
                    ) ); 
                $ret .= genesis_markup( array(
                        'html5' => '<content>',
                        'xhtml' => '<div class="content">',
                        'echo' => false,
                    ) ); 
                    $ret .= '<i class="icon-'.$case_study_key.'"></i>
                        <h3 class="entry-title">'.$post->post_title.'</h3>
                        <div class="entry-content">'.msdlab_excerpt($post->ID).'</div>
                        <a href="'.get_permalink($post->ID).'" class="readmore">Read More ></a>';
                $ret .= genesis_markup( array(
                        'html5' => '</content>',
                        'xhtml' => '</div>',
                        'echo' => false,
                    ) ); 
            $ret .= genesis_markup( array(
                    'html5' => '</article>',
                    'xhtml' => '</div>',
                    'context' => 'casestudy',
                    'echo' => false,
                ) );
        }
        //return
        $post = $origpost;
        return $ret;
    }
    /**
     * Callback for dynamic Genesis 'genesis_attr_$context' filter.
     * 
     * Add custom attributes for the custom filter.
     * 
     * @param array $attributes The element attributes
     * @return array $attributes The element attributes
     */
    function custom_add_casestudy_attr( $attributes ){
            global $case_study_key;
            $attributes['class']     = join( ' ', get_post_class(array($case_study_key,'icon-'.$case_study_key)) );
            $attributes['itemtype']  = 'http://schema.org/CreativeWork';
            $attributes['itemprop']  = 'caseStudy';
            // return the attributes
            return $attributes;      
    }
    function msdlab_do_casestudy_excerpt() {

        global $post;
    
        if ( is_singular() ) {
            the_content();
    
            if ( is_single() && 'open' === get_option( 'default_ping_status' ) && post_type_supports( $post->post_type, 'trackbacks' ) ) {
                echo '<!--';
                trackback_rdf();
                echo '-->' . "\n";
            }
    
            if ( is_page() && apply_filters( 'genesis_edit_post_link', true ) )
                edit_post_link( __( '(Edit)', 'genesis' ), '', '' );
        }
        else {
                    $ret .= '
                        <div>'.msdlab_excerpt($post->ID).'</div>
                        <a href="'.get_permalink($post->ID).'" class="readmore">Read More ></a>';
                print $ret;
        }
    
    }
}