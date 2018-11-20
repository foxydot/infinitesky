<?php

add_filter('body_class','practice_area_body_class');
function practice_area_body_class($classes) {
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'msd_practice-area' );
    $classes[] = $terms[0]->slug;
    return $classes;
}

function msdlab_just_back_to_case_study_nav_link() {
    print('<a class="btn button all-case-studies" href="/thought-leadership/case-studies/">All Case Studies</a>');
}

function msdlab_cat_tag_display($content){
    global $post;
    $industries = wp_get_post_terms( $post->ID, 'msd_industry' );
    $solutions = wp_get_post_terms( $post->ID, 'msd_practice-area' );
    $ret[] = '<div class="sidecar">';
    $ret[] = '<div class="wrapper">';
    if(isset($industries[0])) {
        $ret[] = '<h6>Industry</h6>';
        $ret[] = '<a class="icon icon-' . $industries[0]->slug . '" href="'.get_term_link($industries[0]->slug,'msd_industry').'">' . $industries[0]->name . '</a>';
    }
    /*if(isset($solutions[0])) {
        $ret[] = '<h6>Solutions</h6>';
        $ret[] = '<a>' . $solutions[0]->name . '</a>';
    }*/
    $ret[] = '</div>';
    $ret[] = '</div>';
    $r = implode("\n",$ret);
    $c = explode("\n",$content);
    $str1 = $str2 = '';
    while(strlen($str1) < 900){
        $str1 .= array_shift($c) . "\n";
    }
    $str2 = implode("\n",$c);
    return $str1 . $r . $str2;
}

add_action('genesis_entry_footer', 'msdlab_just_back_to_case_study_nav_link' );

add_filter('the_content','msdlab_cat_tag_display');

remove_action('genesis_entry_header','genesis_do_post_title');
remove_action('genesis_entry_header','genesis_post_info',12);
remove_action('genesis_before_entry','msd_post_image');//add the image above the entry
remove_action('genesis_entry_footer','genesis_post_meta');
remove_action('genesis_after_endwhile','msdlab_prev_next_post_nav');
genesis();