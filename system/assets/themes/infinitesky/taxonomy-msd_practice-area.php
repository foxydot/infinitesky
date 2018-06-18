<?php
add_action('wp_enqueue_scripts', 'msdlab_add_cs_styles');
add_action('msdlab_title_area','msdlab_custom_case_study_header');

function msdlab_add_cs_styles() {
    if(!is_admin()){
        wp_enqueue_style('msd-cs-style',get_stylesheet_directory_uri().'/lib/css/case-studies.css');
    }
}
function msdlab_custom_case_study_header(){
    $qo = get_queried_object();
    print '<h2 class="chapter-title">Thought Leadership</h2>
<h1 class="entry-title">Case Studies</h1>
<h2 class="entry-subtitle">'.$qo->name.'</h2>
';
    global $wp_filter;
    //ts_var( $wp_filter['genesis_entry_content'] );
}
add_action('msdlab_title_area','msdlab_custom_case_study_header');

add_filter('body_class','practice_area_body_class');
function practice_area_body_class($classes) {
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'msd_practice-area' );
    $classes[] = $terms[0]->slug;
    return $classes;
}
//add_filter('genesis_post_title_text','msdlab_case_study_title');
function msdlab_case_study_title($content){
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'msd_practice-area' );
    $content = '<span class="icon-'.$terms[0]->slug.'"><i></i></span> '.$content;
    return $content;
}

function msdlab_npp_navigation_links() {
    previous_post_link('<div class="prev-link page-nav"><i class="fa fa-arrow-left"></i> %link</div>', 'Previous', true, '', 'msd_practice-area'); 
    next_post_link('<div class="next-link page-nav">%link <i class="fa fa-arrow-right"></i></div>', 'Next', true, '', 'msd_practice-area');
}

remove_action('genesis_entry_header','genesis_post_info',12);
remove_action('genesis_before_entry','msd_post_image');//add the image above the entry
remove_action('genesis_entry_content','genesis_do_post_content');
add_action('genesis_entry_content',array('MSDCaseStudyCPT','msdlab_do_casestudy_excerpt')); //not 100% sure this is right
remove_action('genesis_entry_footer','genesis_post_meta');

add_action('genesis_entry_content','msd_post_image',5);
genesis();