<?php
/**
 * Determine if the div.site-inner should be wrapped
 */
function msdlab_maybe_wrap_inner(){
    global $do_wrap;

    $layout = genesis_site_layout();
    $template = get_page_template();
    switch($layout){
        case 'content-sidebar':
        case 'sidebar-content':
        case 'content-sidebar-sidebar':
        case 'sidebar-sidebar-content':
        case 'sidebar-content-sidebar':
            $do_wrap['site-inner'] = true;
            break;
        case 'full-width-content':
            $do_wrap['site-inner'] = false;
            break;
    }
}
/**
 * Customize search form input
 */
function msdlab_search_text($text) {
    $text = "Search";
    return $text;
}

/**
 * Customize search button text
 */
function msdlab_search_button($text) {
    $text = "&#xF002;";
    return $text;
}

/**
 * Create a sliding search form
 */
function msdlab_sliding_search_form($form){
    $pattern = '/<\/form>/i';
    $replacement= '</form><input type="button" class="search-slide-switch" value="'.msdlab_search_button('').'" />';
    return preg_replace($pattern,$replacement,$form);
}



/*** HEADER ***/


/*** NAV ***/
function msdlab_do_nav() {

    //* Do nothing if menu not supported
    if ( ! genesis_nav_menu_supported( 'primary' ) )
        return;

    $class = 'menu genesis-nav-menu menu-primary';
    if ( genesis_superfish_enabled() ) {
        $class .= ' js-superfish';
    }

    genesis_nav_menu( array(
        'theme_location' => 'primary',
        'menu_class'     => $class,
    ) );

}

/*** SIDEBARS ***/

/**
 * Customize Breadcrumb output
 */
function msdlab_breadcrumb_args($args) {
    $args['home'] = '';
    $args['labels']['prefix'] = ''; //marks the spot
    $args['sep'] = ' / ';
    return $args;
}

function msdlab_post_info_filter($post_info) {
    $post_info = '<span class="post-author">Posted by [post_author]</span>
    <span class="post-date">[post_date]</span>';
    return $post_info;
}

/**
 * Move titles
 */

function msdlab_maybe_move_title(){
    global $post,$subtitle_support;
    $template_file = get_post_meta($post->ID,'_wp_page_template',TRUE);
    switch($template_file){
        case 'default':
        default:
            remove_all_actions('genesis_archive_title_descriptions');
            add_action('msdlab_title_area','msdlab_do_chapter_title');
            add_action('msdlab_title_area','msdlab_do_post_title');
            add_action('genesis_after_header','msdlab_do_title_area');
        if(!is_single() && !is_cpt('post')) {
                remove_action('genesis_entry_header', 'genesis_do_post_title'); //move the title out of the content area
                remove_action('genesis_entry_header', array(&$subtitle_support, 'msdlab_do_post_subtitle')); //move the title out of the content area
                add_action('msdlab_title_area',array(&$subtitle_support,'msdlab_do_post_subtitle'));
            }
        break;

    }
}

function msdlab_do_chapter_title(){
    if(is_front_page()){
    } elseif(is_page()){
        global $post;
        print '<h2 class="chapter-title">';
        print get_section_title();
        print '</h2>';
    } elseif(is_home() || is_single()) {
        $blog_home = get_post(get_option( 'page_for_posts' ));
        $parent = get_post(get_topmost_parent($blog_home->ID));
        $title = apply_filters( 'genesis_post_title_text', $parent->post_title );//* Wrap in H1 on singular pages
        print '<h2 class="chapter-title">';
        print $title;
        print '</h2>';
    }
}

function msdlab_do_post_title(){
    if(is_home() || (is_single() && is_cpt('post'))) {
        $blog_home = get_post(get_option( 'page_for_posts' ));
        $title = apply_filters( 'genesis_post_title_text', $blog_home->post_title );//* Wrap in H1 on singular pages
        if ( 0 === mb_strlen( $title ) ) {
            return;
        }

        // Link it, if necessary.
        if ( ! is_singular() && apply_filters( 'genesis_link_post_title', true ) ) {
            $title = genesis_markup( array(
                'open'    => '<a %s>',
                'close'   => '</a>',
                'content' => $title,
                'context' => 'entry-title-link',
                'echo'    => false,
            ) );
        }

        // Wrap in H1 on singular pages.
        $wrap = is_singular() ? 'h1' : 'h2';

        // Also, if HTML5 with semantic headings, wrap in H1.
        $wrap = genesis_html5() && genesis_get_seo_option( 'semantic_headings' ) ? 'h1' : $wrap;

        /**
         * Entry title wrapping element.
         *
         * The wrapping element for the entry title.
         *
         * @since 2.2.3
         *
         * @param string $wrap The wrapping element (h1, h2, p, etc.).
         */
        $wrap = apply_filters( 'genesis_entry_title_wrap', $wrap );

        // Build the output.
        $output = genesis_markup( array(
            'open'    => "<{$wrap} %s>",
            'close'   => "</{$wrap}>",
            'content' => $title,
            'context' => 'entry-title',
            'params'  => array(
                'wrap'  => $wrap,
            ),
            'echo'    => false,
        ) );

        echo apply_filters( 'genesis_post_title_output', $output, $wrap, $title ) . "\n";
    } else {
        genesis_do_post_title();
    }
}

function msdlab_do_title_area(){
    global $post;
    $postid = is_admin()?$_GET['post']:$post->ID;
    print '<div id="page-title-area" class="page-title-area">';
    print '<div class="texturize">';
    print '<div class="gradient">';
    print '<div class="container">';
    do_action('msdlab_title_area');
    print '</div>';
    print '</div>';
    print '</div>';
    print '</div>';
}

/**
 * Manipulate the featured image
 */
function msd_post_image() {
    global $post;
    //setup thumbnail image args to be used with genesis_get_image();
    $size = 'child_full'; // Change this to whatever add_image_size you want
    $default_attr = array(
        'class' => "attachment-$size $size alignright",
        'alt'   => $post->post_title,
        'title' => $post->post_title,
    );

    // This is the most important part!  Checks to see if the post has a Post Thumbnail assigned to it. You can delete the if conditional if you want and assume that there will always be a thumbnail
    if ( has_post_thumbnail() && is_page() ) {
        msdlab_page_banner();
    } elseif ( has_post_thumbnail() && is_cpt('post') && is_single()){
        print genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) );
    }

}

function msdlab_page_banner(){
    if(is_front_page())
        return;
    global $post;
    $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'page_banner' );
    $background = $featured_image[0];
    $ret = '<div class="banner clearfix" style="background-image:url('.$background.')"></div>';
    print $ret;
}

function msdlab_get_thumbnail_url($post_id = null, $size = 'post-thumbnail'){
    global $post;
    if(!$post_id)
        $post_id = $post->ID;
    $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $size );
    $url = $featured_image[0];
    return $url;
}

/**
 * Previous next links
 */
function msdlab_read_more_link() {
    return '<a class="more-link nobr" href="' . get_permalink() . '">&hellip;read more</a>';
}

function msdlab_older_link_text($content) {
    $olderlink = 'Older Posts &raquo;';
    return $olderlink;
}

function msdlab_newer_link_text($content) {
    $newerlink = '&laquo; Newer Posts';
    return $newerlink;
}

/**
 * Display links to previous and next post, from a single post.
 *
 * @since 1.5.1
 *
 * @return null Return early if not a post.
 */
function msdlab_prev_next_post_nav() {
    if ( ! is_singular() || is_page() )
        return;

    $in_same_term = false;
    $excluded_terms = false;
    $previous_post_link = get_previous_post_link('&laquo; %link', '%title', $in_same_term, $excluded_terms, 'category');
    $next_post_link = get_next_post_link('%link &raquo;', '%title', $in_same_term, $excluded_terms, 'category');
    if(is_cpt('project')){
        $taxonomy = 'project_type';
        $prev_post = get_adjacent_post( $in_same_term, $excluded_terms, true, $taxonomy );
        $next_post = get_adjacent_post( $in_same_term, $excluded_terms, false, $taxonomy );
        $size = 'nav-post-thumb';
        $previous_post_link = $prev_post?'<a href="'.get_post_permalink($prev_post->ID).'" style="background-image:url('.msdlab_get_thumbnail_url($prev_post->ID, $size).'")><span class="nav-title"><i class="fa fa-angle-double-left"></i> '.$prev_post->post_title.'</span></a>':'<div href="'.get_post_permalink($post->ID).'" style="opacity: 0.5;background-image:url('.msdlab_get_thumbnail_url($post->ID, $size).'")><span class="nav-title">You are at the beginning of the portfolio.</span></div>';
        $next_post_link = $next_post?'<a href="'.get_post_permalink($next_post->ID).'" style="background-image:url('.msdlab_get_thumbnail_url($next_post->ID, $size).'")><span class="nav-title">'.$next_post->post_title.' <i class="fa fa-angle-double-right"></i></span></a>':'<div href="'.get_post_permalink($post->ID).'" style="opacity: 0.5;background-image:url('.msdlab_get_thumbnail_url($post->ID, $size).'")><span class="nav-title">You are at the end of the portfolio.</span></div>';

    }

    genesis_markup( array(
        'html5'   => '<div %s>',
        'xhtml'   => '<div class="navigation">',
        'context' => 'adjacent-entry-pagination',
    ) );



    echo '<div class="pagination-previous pull-left col-xs-6">';
    echo $previous_post_link;
    echo '</div>';

    echo '<div class="pagination-next pull-right col-xs-6">';
    echo $next_post_link;
    echo '</div>';

    echo '</div>';

}

/*** FOOTER ***/

/**
 * Menu area for footer menus
 */
register_nav_menus( array(
    'footer_menu' => 'Footer Menu'
) );
function msdlab_do_footer_menu(){
    if(has_nav_menu('footer_menu')){$footer_menu = wp_nav_menu( array( 'theme_location' => 'footer_menu','container_class' => 'ftr-menu ftr-links','echo' => FALSE) );}
    print '<div id="footer_menu" class="footer-menu"><div class="wrap">'.$footer_menu.'</div></div>';
}



/**
 * Footer replacement with MSDSocial support
 */
function msdlab_do_social_footer(){
    global $msd_social;

    if(has_nav_menu('footer_menu')){$footer_menu = wp_nav_menu( array( 'theme_location' => 'footer_menu','container_class' => 'menu genesis-nav-menu nav-footer','echo' => FALSE ) );}

    if($msd_social && get_option('msdsocial_street')!=''){
        $address = '<span itemprop="streetAddress">'.get_option('msdsocial_street').'</span>, <span itemprop="addressLocality">'.get_option('msdsocial_city').'</span>, <span itemprop="addressRegion">'.get_option('msdsocial_state').'</span> <span itemprop="postalCode">'.get_option('msdsocial_zip').'</span><br />'.$msd_social->get_digits(true,'').' &middot; <a href="mailto:'.antispambot(get_option('msdsocial_email')).'">'.antispambot(get_option('msdsocial_email')).'</a>';
        $copyright .= 'COPYRIGHT &copy;'.date('Y').' '.$msd_social->get_bizname().'';
    } else {
        $copyright .= '&copy;'.date('Y').' '.get_bloginfo('name').'. All Rights Reserved. ';
    }
    print '<div class="row">';
    //print '<div class="social">'.$msd_social->social_media().'</div>';
    //print '<div class="identity">'.$msd_social->get_bizname().', '.get_bloginfo('description').'</div>';
    print '<div class="legal">'.$copyright.'</div>';
    print '<nav class="footer-menu" itemtype="http://schema.org/SiteNavigationElement" itemscope="itemscope" role="navigation">'.$footer_menu.'</nav>';
    print '</div>';
    //print '<div class="backtotop"><a href="#pre-header"><i class="fa fa-angle-up"></i></a></div>';
}


/*** SITEMAP ***/
function msdlab_sitemap(){
    //get the menu
    ob_start();
    wp_nav_menu(array('menu'=>'primary-links','menu_class'=>'sitemap','container'=>''));
    $sitemap = ob_get_contents();
    ob_end_clean();
    print $sitemap;
}
add_shortcode('sitemap','msdlab_sitemap');

/**
 * Add new image sizes to the media panel
 */
if(!function_exists('msd_insert_custom_image_sizes')){
    function msd_insert_custom_image_sizes( $sizes ) {
        global $_wp_additional_image_sizes;
        if ( empty($_wp_additional_image_sizes) )
            return $sizes;

        foreach ( $_wp_additional_image_sizes as $id => $data ) {
            if ( !isset($sizes[$id]) )
                $sizes[$id] = ucfirst( str_replace( '-', ' ', $id ) );
        }

        return $sizes;
    }
}
add_filter( 'image_size_names_choose', 'msd_insert_custom_image_sizes' );

add_action( 'genesis_before_loop', 'sk_excerpts_search_page' );
function sk_excerpts_search_page() {
    if ( is_search() ) {
        add_filter( 'genesis_pre_get_option_content_archive', 'sk_show_excerpts' );
    }
}

function sk_show_excerpts() {
    return 'excerpts';
}

add_shortcode('icon','msdlab_icon_sc_callback');
function msdlab_icon_sc_callback($atts){
    extract(shortcode_atts( array(
        'img' => false,
    ), $atts ));
    if(!$img){return false;}
    $img_url = get_stylesheet_directory_uri().'/lib/images/icon_'.$img.'.svg';
    $ret = '<span class="icon-shortcode" style="background-image:url('.$img_url.')"><span class="screen-reader-text">'.$img.'</span></span>';
    return $ret;
}