<?php

add_filter('body_class','practice_area_body_class');
function practice_area_body_class($classes) {
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'msd_practice-area' );
    $classes[] = $terms[0]->slug;
    return $classes;
}

function msdlab_npp_navigation_links() {
    previous_post_link('<div class="prev-link page-nav"><i class="fa fa-arrow-left"></i> %link</div>', 'Previous', true, '', 'msd_practice-area'); 
    next_post_link('<div class="next-link page-nav">%link <i class="fa fa-arrow-right"></i></div>', 'Next', true, '', 'msd_practice-area');
}

function msdlab_just_back_to_case_study_nav_link() {
    print('<div class="prev-link page-nav"><a href="/client-experience/all-case-studies/"<i class="fa fa-arrow-left"></i> All Case Studies</a></div>'); 
}

//add_action('genesis_entry_footer', 'msdlab_npp_navigation_links' );
add_action('genesis_entry_footer', 'msdlab_just_back_to_case_study_nav_link' );


remove_action('genesis_entry_header','genesis_do_post_title');
remove_action('genesis_entry_header','genesis_post_info',12);
remove_action('genesis_before_entry','msd_post_image');//add the image above the entry
remove_action('genesis_entry_footer','genesis_post_meta');
genesis();