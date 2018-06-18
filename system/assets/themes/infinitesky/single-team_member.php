<?php
remove_all_actions('genesis_entry_header');
remove_action('genesis_entry_footer','genesis_post_meta');
add_action('wp_enqueue_scripts', 'msdlab_add_team_single_styles');
remove_all_actions('genesis_after_endwhile');


function msdlab_add_team_single_styles() {
    if(!is_admin()){
        wp_enqueue_style('msd-team-style',get_stylesheet_directory_uri().'/lib/css/team-single.css');
    }
}

add_action('genesis_before_content_sidebar_wrap','msdlab_add_single_team_member_header', 1);
function msdlab_add_single_team_member_header(){
    print '<div class="container team_member-header">
<div class="row"><div class="col-md-4 col-sm-6 col-xs-12">';
    do_action('msdlab_single_team_member_top_left');
    print '</div>
<div class="col-md-4 col-sm-6 col-xs-12">';
    do_action('msdlab_single_team_member_top_center');
    print '</div>
<div class="col-md-4 col-sm-6 col-xs-12">';
    do_action('msdlab_single_team_member_top_right');
    print '</div>
</div>
</div>';
}
add_action('msdlab_single_team_member_top_left','msd_add_team_headshot', 5);
function msd_add_team_headshot(){
    global $post;
    //setup thumbnail image args to be used with genesis_get_image();
    $size = 'headshot'; // Change this to whatever add_image_size you want
    $default_attr = array(
            'class' => "alignnone attachment-$size $size",
            'alt'   => $post->post_title,
            'title' => $post->post_title,
    );

    // This is the most important part!  Checks to see if the post has a Post Thumbnail assigned to it. You can delete the if conditional if you want and assume that there will always be a thumbnail
    if ( has_post_thumbnail() ) {
        printf( '%s', genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
    }
}
add_action('msdlab_title_area','msdlab_add_team_member_title');
function msdlab_add_team_member_title(){
    global $post,$contact_info;
    ?>
    <?php $contact_info->the_field('_team_title'); ?>
    <?php if($contact_info->get_the_value() != ''){ ?>
        <h4 class="team-title"><?php print $contact_info->get_the_value(); ?></h4>
    <?php } 
}

add_action('msdlab_single_team_member_top_left','msd_team_contact_info',12);
function msd_team_contact_info(){
    global $post,$contact_info;
    ?>
    <ul class="team-contact">
        <?php $contact_info->the_field('_team_twitter'); ?>
        <?php if($contact_info->get_the_value() != ''){ ?>
            <li class="twitter"><a href="<?php print $contact_info->get_the_value(); ?>"><i class="fa fa-twitter"></i></a></li>
        <?php } ?>
        <?php $contact_info->the_field('_team_linked_in'); ?>
        <?php if($contact_info->get_the_value() != ''){ ?>
            <li class="linkedin"><a href="<?php print $contact_info->get_the_value(); ?>"><i class="fa fa-linkedin"></i></a></li>
        <?php } ?>
        <?php $contact_info->the_field('_team_email'); ?>
        <?php if($contact_info->get_the_value() != ''){ ?>
            <li class="email"><a href="mailto:<?php print antispambot($contact_info->get_the_value()); ?>"><i class="fa fa-envelope"></i></a></li>
        <?php } ?>
        <?php $contact_info->the_field('_team_phone'); ?>
        <?php if($contact_info->get_the_value() != ''){ ?>
            <li class="phone"><a href="tel:<?php print $contact_info->the_value(); ?>"><?php print msd_str_fmt($contact_info->get_the_value(),'phone'); ?></a></li>
        <?php } ?>
        <?php $contact_info->the_field('_team_mobile'); ?>
        <?php if($contact_info->get_the_value() != ''){ ?>
            <li class="mobile"><?php print msd_str_fmt($contact_info->get_the_value(),'phone'); ?></li>
        <?php } ?>
    </ul>
    <?php
}



add_action('msdlab_single_team_member_top_center','msd_team_insights', 20);
function msd_team_insights(){
    global $post,$contact_info,$teamblogs;
    $titlearray = explode(" ",$post->post_title);
    $firstname = $titlearray[0];
    $firstname = (substr($firstname, -1) == 's')?$firstname."'":$firstname."'s";
    if($contact_info->get_the_value('_team_user_id')!=0){
        
    print '<section class="widget widget-insights">';
    print '<h3 class="insights-header" id="insights">'.$firstname.' Insights</h3>';
        //TODO: Add get blog posts by subject tag of user
        $teamblogs = get_post_items_for_team_member($post->ID);
        //merge blogs http://wordpress.org/support/topic/multiple-queries-compiling-into-one-loop
        $args = array(
            'author' => $contact_info->get_the_value('_team_user_id'),
            'posts_per_page' => '4',
            'post_status' => 'publish',
            'post_type' => 'post'
        );
        if(count($teamblogs)>0){
            $args['suppress_filters'] = FALSE;
            //add_filter('posts_where','msdlab_modify_posts_where');
        } 
        $blogs = get_posts($args);
        //ts_data($args);
        //ts_data($blogs);
        if(count($teamblogs)>0){
            //remove_filter('posts_where','msdlab_modify_posts_where');
        }
        //possibly use posts_where filter http://codex.wordpress.org/Plugin_API/Filter_Reference#Advanced_WordPress_Filters
        if($blogs){
            print '<div class="insights-blogs insights-section">';
            $i = 0;
            foreach($blogs AS $blog){
                team_display_blog($blog,$i);
                $i++;
            }
            print '</div>';
            print '<div class="button-wrapper clearfix">
<a class="button alignright" href="'.get_author_posts_url($contact_info->get_the_value('_team_user_id')).'" target="_self">More Posts ></a>
</div>';
        }
    print '</section>';
    }
}

function team_display_blog($blog,$count = 0){
    $thumbnail = get_the_post_thumbnail($blog->ID,'thumbnail',array('class' => 'aligncenter'));
    print '<article>
        <a href="'.get_permalink($blog->ID).'">'.$blog->post_title.'</a>
    </article>';
}
add_action('msdlab_single_team_member_top_right','msd_team_news', 30);
function msd_team_news(){
    global $post;
    $news = new MSDNewsCPT;
    $newses = $news->get_news_items_for_team_member($post->ID);
    $i = 1;
    if(count($newses)>0){
        print '<section class="widget widget-news">
<h3 class="insights-header">In the News</h3>';
            print '<div class="insights-news insights-section">';
        foreach($newses AS $press){
            $url = get_post_meta($press->ID,'_news_newsurl',1);
            $link = strlen($url)>4?msdlab_http_sanity_check($url):get_permalink($press->ID);
            $thumbnail = get_the_post_thumbnail($press->ID,'tiny-post-thumb',array('class' => 'alignleft'));
            
            print '<article>
                <a href="'.$link.'">'.$press->post_title.'</a>
                </article>';
        }
        print '</div>';
        print '<div class="button-wrapper clearfix">
<a class="button alignright" href="/about/press/" target="_self">Read More ></a>
</div>';
        print '</section>';
    }
}
function get_post_items_for_team_member($team_id){
    $args = array( 
        'post_type' => 'post', 
        'numberposts' => -1,
        'meta_query' => array(
           array(
               'key' => '_msdlab_team_members',
               'value' => '"'.$team_id.'"',
               'compare' => 'LIKE',
           )
       )
    );
    return(get_posts($args));
}

//add_action('genesis_after_entry','msd_team_videos',40);
function msd_team_videos(){
    global $post;
    $video = new MSDVideoCPT;
    $videos = $video->get_video_items_for_team_member($post->ID);
    if(count($videos)>0){
        print'<section class="widget-videos insights-section">
            <div class="insights-videos insights-section row">';
        foreach($videos AS $vid){
            print '<article class="col-xs-12 col-sm-6">';
            print wp_oembed_get($vid->youtube_url);
            print '
<h4 class="video-title">'.$vid->post_title.'</h4>
';
            print '</article>';           
        }
        print '</div>';
        print '</section>';
    }
}


genesis();