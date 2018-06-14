<?php
function msdlab_custom_case_study_header(){
    print '<div class="section-header"><h3>Case Studies</h3></div>';
    global $wp_filter;
    //ts_var( $wp_filter['genesis_entry_content'] );
}
add_action('genesis_before_loop','msdlab_custom_case_study_header');

add_filter('body_class','practice_area_body_class');
function practice_area_body_class($classes) {
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'msd_practice-area' );
    $classes[] = $terms[0]->slug;
    return $classes;
}
add_filter('genesis_post_title_text','msdlab_case_study_title');
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

//add_action('genesis_entry_footer', 'msdlab_npp_navigation_links' );

remove_action('genesis_entry_header','genesis_post_info',12);
remove_action('genesis_before_entry','msd_post_image');//add the image above the entry
remove_action('genesis_entry_content','genesis_do_post_content');
add_action('genesis_entry_content',array('MSDCaseStudyCPT','msdlab_do_casestudy_excerpt')); //not 100% sure this is right
remove_action('genesis_entry_footer','genesis_post_meta');

add_action('genesis_entry_content','msd_post_image',5);
genesis();