<?php
remove_all_actions('genesis_loop');
add_action('genesis_loop',array('MSDCaseStudyCPT','msdlab_casestudies_special_loop'));
add_action('wp_enqueue_scripts', 'msdlab_add_cs_styles');
add_action('msdlab_title_area','msdlab_custom_case_study_header');

function msdlab_custom_case_study_header()
{
    $qo = get_queried_object();
    print '<h2 class="chapter-title">Thought Leadership</h2>
<h1 class="entry-title">Case Studies</h1>
';
}
function msdlab_add_cs_styles() {
    if(!is_admin()){
        wp_enqueue_style('msd-cs-style',get_stylesheet_directory_uri().'/lib/css/case-studies.css');
    }
}
genesis();