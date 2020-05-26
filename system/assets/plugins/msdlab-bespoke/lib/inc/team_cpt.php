<?php
if (!class_exists('MSDTeamCPT')) {
    class MSDTeamCPT {
        //Properties
        var $cpt = 'team_member';

        var $location = array(
            'washington-d-c' => 'Washington, D.C.',
            'chicago'        => 'Chicago',
            'new-york'       => 'New York',
            'denver'         => 'Denver',
            'philadelphia'   => 'Philadelphia',
            'boston'         => 'Boston',
            'richmond-wv'    => 'Richmond, VA',
        );

        var $has_shortcode = false;
        //Methods
        /**
         * PHP 4 Compatible Constructor
         */
        public function MSDTeamCPT(){$this->__construct();}

        /**
         * PHP 5 Constructor
         */
        function __construct(){
            global $current_screen;
            //"Constants" setup
            $this->plugin_url = plugin_dir_url('msd-custom-cpt/msd-custom-cpt.php');
            $this->plugin_path = plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php');
            //Actions
            add_action( 'init', array(&$this,'register_taxonomies') );
            add_action( 'init', array(&$this,'register_cpt') );
            add_action( 'init', array(&$this,'register_metaboxes') );
            //add_action('admin_head', array(&$this,'plugin_header'));
            add_action('admin_print_scripts', array(&$this,'add_admin_scripts') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            add_action('admin_footer',array(&$this,'info_footer_hook') );
            // important: note the priority of 99, the js needs to be placed after tinymce loads
            add_action('admin_print_footer_scripts',array(&$this,'print_footer_scripts'),99);
            //add_action('template_redirect', array(&$this,'my_theme_redirect'));
            //add_action('admin_head', array(&$this,'codex_custom_help_tab'));
            add_action('wp_footer',array(&$this,'modal_goodness'));


            //add cols to manage panel
            add_filter( 'manage_edit-'.$this->cpt.'_columns', array(&$this,'my_edit_columns' ));
            add_action( 'manage_'.$this->cpt.'_posts_custom_column', array(&$this,'my_manage_columns'), 10, 2 );


            //Filters
            //add_filter( 'pre_get_posts', array(&$this,'custom_query') );
            add_filter( 'enter_title_here', array(&$this,'change_default_title') );
            add_filter( 'genesis_attr_team_member', array(&$this,'custom_add_team_member_attr') );

            //Shortcodes
            add_shortcode('teammembers', array(&$this,'msdlab_team_member_special_loop_shortcode_handler'));
            add_shortcode('team-members', array(&$this,'msdlab_team_member_special_loop_shortcode_handler'));
            add_shortcode('team',array(&$this,'msdlab_team_member_special_loop_shortcode_handler'));

            add_image_size('team-headshot',360,360, array('center','top'));

        }


        function register_taxonomies(){

            $labels = array(
                'name' => _x( 'Team categories', 'team-category' ),
                'singular_name' => _x( 'Team category', 'team-category' ),
                'search_items' => _x( 'Search team categories', 'team-category' ),
                'popular_items' => _x( 'Popular team categories', 'team-category' ),
                'all_items' => _x( 'All team categories', 'team-category' ),
                'parent_item' => _x( 'Parent team category', 'team-category' ),
                'parent_item_colon' => _x( 'Parent team category:', 'team-category' ),
                'edit_item' => _x( 'Edit team category', 'team-category' ),
                'update_item' => _x( 'Update team category', 'team-category' ),
                'add_new_item' => _x( 'Add new team category', 'team-category' ),
                'new_item_name' => _x( 'New team category name', 'team-category' ),
                'separate_items_with_commas' => _x( 'Separate team categories with commas', 'team-category' ),
                'add_or_remove_items' => _x( 'Add or remove team categories', 'team-category' ),
                'choose_from_most_used' => _x( 'Choose from the most used team categories', 'team-category' ),
                'menu_name' => _x( 'Team categories', 'team-category' ),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true, //we want a "category" style taxonomy, but may have to restrict selection via a dropdown or something.

                'rewrite' => array('slug'=>'team-category','with_front'=>false),
                'query_var' => true
            );

            register_taxonomy( 'team_category', array($this->cpt), $args );
        }

        function register_cpt() {

            $labels = array(
                'name' => _x( 'Team Members', 'team' ),
                'singular_name' => _x( 'Team Member', 'team' ),
                'add_new' => _x( 'Add New', 'team' ),
                'add_new_item' => _x( 'Add New Team Member', 'team' ),
                'edit_item' => _x( 'Edit Team Member', 'team' ),
                'new_item' => _x( 'New Team Member', 'team' ),
                'view_item' => _x( 'View Team Member', 'team' ),
                'search_items' => _x( 'Search Team Members', 'team' ),
                'not_found' => _x( 'No team members found', 'team' ),
                'not_found_in_trash' => _x( 'No team members found in Trash', 'team' ),
                'parent_item_colon' => _x( 'Parent Team:', 'team' ),
                'menu_name' => _x( 'Team Members', 'team' ),
            );

            $args = array(
                'labels' => $labels,
                'hierarchical' => false,
                'description' => 'Team',
                'supports' => array( 'title', 'editor', 'thumbnail', 'genesis-cpt-archives-settings', 'excerpt' ),
                'taxonomies' => array( 'team_category' ),
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
                'rewrite' => array('slug'=>'team','with_front'=>false),
                'capability_type' => 'post',
                'menu_icon' => 'dashicons-groups',
            );

            register_post_type( $this->cpt, $args );
        }


        function register_metaboxes()
        {
            global $contact_info;
            $contact_info = new WPAlchemy_MetaBox(array
            (
                'id' => '_contact_info',
                'title' => 'Contact Info',
                'types' => array('location', 'team_member'), // added only for pages and to custom post type "events"
                'context' => 'normal', // same as above, defaults to "normal"
                'priority' => 'high', // same as above, defaults to "high"
                'template' => plugin_dir_path(dirname(__FILE__)).'/template/contact-info.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_team_member_' // defaults to NULL
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
                    jQuery('#postdivrich').before(jQuery('#_contact_information_metabox'));
                    jQuery('#_contact_information_metabox').before(jQuery('#_team_information_metabox'));
                </script><?php
            }
        }


        function my_theme_redirect() {
            global $wp;

            //A Specific Custom Post Type
            if ($wp->query_vars["post_type"] == $this->cpt) {
                if(is_single()){
                    $templatefilename = 'single-'.$this->cpt.'.php';
                    if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                        $return_template = STYLESHEETPATH . '/' . $templatefilename;
                    } else {
                        $return_template = plugin_dir_path(dirname(__FILE__)). 'template/' . $templatefilename;
                    }
                    do_theme_redirect($return_template);

                    //A Custom Taxonomy Page
                } elseif ($wp->query_vars["taxonomy"] == 'team_category') {
                    $templatefilename = 'taxonomy-team_category.php';
                    if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                        $return_template = STYLESHEETPATH . '/' . $templatefilename;
                    } else {
                        $return_template = plugin_dir_path(dirname(__FILE__)) . 'template/' . $templatefilename;
                    }
                    do_theme_redirect($return_template);
                }
            }
        }

        function codex_custom_help_tab() {
            global $current_screen;
            if($current_screen->post_type != $this->cpt)
                return;

            // Setup help tab args.
            $args = array(
                'id'      => 'title', //unique id for the tab
                'title'   => 'Title', //unique visible title for the tab
                'content' => '<h3>The Event Title</h3>
                          <p>The title of the event.</p>
                          <h3>The Permalink</h3>
                          <p>The permalink is created by the title, but it doesn\'t change automatically if you change the title. To change the permalink when editing an event, click the [Edit] button next to the permalink. 
                          Remove the text that becomes editable and click [OK]. The permalink will repopulate with the new Location and date!</p>
                          ',  //actual help text
            );

            // Add the help tab.
            $current_screen->add_help_tab( $args );

            // Setup help tab args.
            $args = array(
                'id'      => 'event_info', //unique id for the tab
                'title'   => 'Event Info', //unique visible title for the tab
                'content' => '<h3>Event URL</h3>
                          <p>The link to the page describing the event</p>
                          <h3>The Event Date</h3>
                          <p>The Event Date is the date of the event. This value is restrained to dates (chooseable via a datepicker module). This value is also used to sort events for the calendars, upcoming events, etc.</p>
                          <p>For single day events, set start and end date to the same date.',  //actual help text
            );

            // Add the help tab.
            $current_screen->add_help_tab( $args );

        }


        function custom_query( $query ) {
            if(!is_admin()){
                if(is_page()){
                    return $query;
                }
                if($query->is_main_query()) {
                    $post_types = $query->get('post_type');             // Get the currnet post types in the query

                    if(!is_array($post_types) && !empty($post_types))   // Check that the current posts types are stored as an array
                        $post_types = explode(',', $post_types);

                    if(empty($post_types))
                        $post_types = array('post'); // If there are no post types defined, be sure to include posts so that they are not ignored

                    if ($query->is_search) {
                        $searchterm = $query->query_vars['s'];
                        // we have to remove the "s" parameter from the query, because it will prevent the posts from being found
                        $query->query_vars['s'] = "";

                        if ($searchterm != "") {
                            $query->set('meta_value', $searchterm);
                            $query->set('meta_compare', 'LIKE');
                        };
                        $post_types[] = $this->cpt;                         // Add your custom post type

                    } elseif ($query->is_archive) {
                        $post_types[] = $this->cpt;                         // Add your custom post type
                    }

                    $post_types = array_map('trim', $post_types);       // Trim every element, just in case
                    $post_types = array_filter($post_types);            // Remove any empty elements, just in case

                    $query->set('post_type', $post_types);              // Add the updated list of post types to your query
                }
            }
        }

        function change_default_title( $title ){
            global $current_screen;
            if  ( $current_screen->post_type == $this->cpt ) {
                return __('Team Member Name','team');
            } else {
                return $title;
            }
        }

        function cpt_display(){
            global $post;
            if(is_cpt($this->cpt)) {
                if (is_single()){
                    //display content here
                } else {
                    //display for aggregate here
                }
            }
        }

        function get_all_team_members(){
            $args = array(
                'posts_per_page'   => -1,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => $this->cpt,
            );
            $posts = get_posts($args);
            $i = 0;
            foreach($posts AS $post){
                $posts[$i]->lastname = get_post_meta($post->ID,'_team_alpha',TRUE);
                $i++;
            }
            usort($posts,array(&$this,'sort_by_lastname'));
            return $posts;
        }

        function msdlab_team_member_special_loop_shortcode_handler($atts){
            $this->has_shortcode = true;
            $args = shortcode_atts( array(
                    'cat' => false,
            ), $atts );
            remove_filter('the_content','wpautop',12);
            return $this->msdlab_team_member_special($args);
        }
        function msdlab_team_member_special($args){
            global $post,$contact_info;
            $cat = false;
            $origpost = $post;
            if(isset($args['cat'])){
                $cat = $args['cat'];
                unset($args['cat']);
                $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'team_category',
                            'field' => 'slug',
                            'terms' => $cat,
                        ),
                );
            }
            $defaults = array(
                'posts_per_page' => -1,
                'post_type' => 'team_member',
                'orderby' => 'meta_value',
                'meta_key' => '_team_member__team_last_name',
                'order' => 'ASC'
            );
            $args = array_merge($defaults,$args);
            //set up result array
            $results = array();
            $results = get_posts($args);
            //format result
            if($results) {
                $i = 0;
                if($cat){
                    $tax = get_term_by('slug',$cat,'team_category');
                    $title = sprintf('<h3 class="team-category-banner">%s</h3>',$tax->name);
                }
                foreach ($results AS $result) {
                    $popinfo = array();
                    $post = $result;
                    $i++;
                    $titlearray = explode(" ", $post->post_title);
                    $firstname = $titlearray[0];
                    $firstname = (substr($firstname, -1) == 's') ? $firstname . "'" : $firstname . "'s";
                    $contact_info->the_meta($result->ID);
                    //make the content for the popup
                    $popinfo[] = '<div id="'.$post->post_name.'" class="team-modal-content">';
                        $popinfo[] = '<table class="table"><tr class="row">';
                            $popinfo[] = '<td class="picntitle equalize col-xs-4">
'.get_the_post_thumbnail($result->ID, 'team-headshot', array('itemprop' => 'image')).'
<div class="titlebox">
<h3 class="entry-title" itemprop="name">' . $post->post_title . '</h3>
<h4 class="team-title" itemprop="jobTitle">' . $contact_info->get_the_value('_team_title') . '</h4></div>
</td>';
                            $popinfo[] = '<td class="bio equalize col-xs-8">'.$post->post_content.'</td>';
                            $popinfo[] = '</tr><tr class="row">';
                            $popinfo[] = '<td class="location col-xs-4">';
                            if ($contact_info->get_the_value('_team_location')) {
                        $popinfo[] = '
                      <i class="fa fa-map-marker"></i> ' . $this->location[$contact_info->get_the_value('_team_location')] . ' Area
                   ';
                    }
                            $popinfo[] = '</td>';
                            $popinfo[] = '<td class="social col-xs-8"><ul>';
                    if ($contact_info->get_the_value('_team_linked_in')) {
                        $popinfo[] = '<li class="email"><a href="mailto:' . $contact_info->get_the_value('_team_email') . '">
                      <i class="fa fa-envelope"><span class="screen-reader-text">' . $contact_info->get_the_value('_team_email') . '</span></i>
                    </a></li>';
                    }
                                if ($contact_info->get_the_value('_team_linked_in')) {
                                    $popinfo[] = '<li class="linkedin"><a href="' . $contact_info->get_the_value('_team_linked_in') . '" target="_linkedin">
                      <i class="fa fa-linkedin"><span class="screen-reader-text">LinkedIn</span></i>
                    </a></li>';
                                }
                    if ($contact_info->get_the_value('_team_phone')) {
                        $popinfo[] = '<li class="phone"><a href="tel:' . $contact_info->get_the_value('_team_phone') . '" target="_phone">
                                  <i class="fa fa-phone"><span class="screen-reader-text">'. $contact_info->get_the_value('_team_phone') .'</span></i>
                                </a></li>';
                    }
                            $popinfo[] = '</ul></td>';
                        $popinfo[] = '</tr></table>';
                    $popinfo[] = '</div>';
                    $ret[] = implode("\n",$popinfo);
                    //make the display square
                    $ret[] = genesis_markup(array(
                        'html5' => '<article %s>',
                        'xhtml' => '<div class="team_member type-team_member status-publish has-post-thumbnail entry">',
                        'context' => 'team_member',
                        'echo' => false,
                    ));
                    $ret[] = genesis_markup(array(
                        'html5' => '<div class="wrap">',
                        'xhtml' => '<div class="wrap">',
                        'echo' => false,
                    ));

                    $ret[] = genesis_markup(array(
                        'html5' => '<main>',
                        'xhtml' => '<div class="main">',
                        'echo' => false,
                    ));
                    $ret[] = genesis_markup(array(
                        'html5' => '<content>',
                        'xhtml' => '<div class="content">',
                        'echo' => false,
                    ));
                    $ret[] = get_the_post_thumbnail($result->ID, 'team-headshot', array('itemprop' => 'image'));
                    $ret[] = '<div class="title-wrapper equalize"><h3 class="entry-title" itemprop="name">' . $post->post_title . '</h3>
                            <h4 class="team-title" itemprop="jobTitle">' . $contact_info->get_the_value('_team_title') . '</h4></div>';
                    $ret[] = '<a href="#'.$post->post_name.'" class="cover-link"><span class="screen-reader-text">more</span></a>';
                    $ret[] = genesis_markup(array(
                        'html5' => '</content>',
                        'xhtml' => '</div>',
                        'echo' => false,
                    ));
                    $ret[] = genesis_markup(array(
                        'html5' => '</main>',
                        'xhtml' => '</div>',
                        'echo' => false,
                    ));
                    $ret[] = genesis_markup(array(
                        'html5' => '</div>',
                        'xhtml' => '</div>',
                        'echo' => false,
                    ));
                    $ret[] = genesis_markup(array(
                        'html5' => '</article>',
                        'xhtml' => '</div>',
                        'context' => 'team_member',
                        'echo' => false,
                    ));
                }
            }
            //return
            $post = $origpost;
            return $title.'<grid class="team">'.implode("\n",$ret).'</grid>';
        }

        function sort_by_lastname( $a, $b ) {
            return $a->lastname == $b->lastname ? 0 : ( $a->lastname < $b->lastname ) ? -1 : 1;
        }

        function print_shortcode_handler(){
            print $this->shortcode_handler(array());
        }

        /**
         * Callback for dynamic Genesis 'genesis_attr_$context' filter.
         *
         * Add custom attributes for the custom filter.
         *
         * @param array $attributes The element attributes
         * @return array $attributes The element attributes
         */
        function custom_add_team_member_attr( $attributes ){
            $attributes['class'] .= ' equalize col-xs-12 col-sm-6 col-md-3';
            $attributes['itemtype']  = 'http://schema.org/Person';
            // return the attributes
            return $attributes;
        }


        function my_edit_columns( $columns ) {

            $mycolumns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __( 'Title' ),
                $this->cpt.'_category' => __( 'Category' ),
                'author' => __( 'Author' ),
                'date' => __( 'Date' )
            );
            $columns = array_merge($mycolumns,$columns);

            return $columns;
        }

        function my_manage_columns( $column, $post_id ) {
            global $post;

            switch( $column ) {
                /* If displaying the 'logo' column. */
                case $this->cpt.'_category' :
                case $this->cpt.'_tag' :
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

        function get_edit_link( $args, $label, $class = '' ) {
            $url = add_query_arg( $args, 'edit.php' );

            $class_html = '';
            if ( ! empty( $class ) ) {
                $class_html = sprintf(
                    ' class="%s"',
                    esc_attr( $class )
                );
            }

            return sprintf(
                '<a href="%s"%s>%s</a>',
                esc_url( $url ),
                $class_html,
                $label
            );
        }

        function modal_goodness(){
            if(!$this->has_shortcode){return;} //escape if the shortcode isn't on the page
            $modal = array();
            $jq = array();
            //add modal structure
            $modal[] =  '<div id="globalModal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-close" data-dismiss="modal" aria-label="Close"><i class="fa fa-close"><span class="screen-reader-text">Close</span></i></div> 
            <div class="modal-body"></div>
        </div>
    </div>
</div>';
            //add javascript
            $jq[] = '$(\'.team_member .cover-link\').click(function(e){
        e.preventDefault();
        var target = $(this).attr(\'href\');
        var bio = $(target).html();
        console.log(bio);
        $(\'#globalModal .modal-body\').html(bio);
        $(\'#globalModal\').modal(\'show\');
    });';
            //print them both out
            print implode("\n",$modal);
            print '<script type="text/javascript" id="modal-scripts">
  jQuery(document).ready(function($) {
      '.implode("\n",$jq).'
  });
</script>';
        }

    } //End Class
} //End if class exists statement