<?php
remove_action('genesis_entry_header','genesis_do_post_title');
remove_action('genesis_entry_header','genesis_post_info',12);
remove_action('genesis_before_entry','msd_post_image');//add the image above the entry
remove_action('genesis_entry_footer','genesis_post_meta');
remove_action('genesis_after_endwhile','msdlab_prev_next_post_nav');
genesis();