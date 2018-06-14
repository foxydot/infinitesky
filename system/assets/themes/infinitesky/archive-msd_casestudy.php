<?php
remove_all_actions('genesis_loop');
add_action('genesis_loop',array('MSDCaseStudyCPT','msdlab_casestudies_special_loop'));
genesis();