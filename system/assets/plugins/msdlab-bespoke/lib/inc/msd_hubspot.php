<?php

   
/*  Copyright 2013 

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class MSDHubSpot{
    private $the_path;
    private $the_url;
    public $icon_size;
    
    function __construct(){
        $this->the_path = plugin_dir_path(__FILE__);
        $this->the_url = plugin_dir_url(__FILE__);
        /*
         * Pull in some stuff from other files
         */
       // $this->requireDir($this->the_path . '/lib/inc');
        
        add_action('wp_footer',array(&$this,'add_footer_code'));
        add_shortcode('cta',array(&$this,'cta_codes'));
    }

function add_footer_code(){
    $hubspot_code = '
    <!-- Start of Async HubSpot Analytics Code -->
    <script type="text/javascript">
        (function(d,s,i,r) {
            if (d.getElementById(i)){return;}
            var n=d.createElement(s),e=d.getElementsByTagName(s)[0];
            n.id=i;n.src=\'//js.hubspot.com/analytics/\'+(Math.ceil(new Date()/r)*r)+\'/186670.js\';
            e.parentNode.insertBefore(n, e);
        })(document,"script","hs-analytics",300000);
    </script>
<!-- End of Async HubSpot Analytics Code -->
';
    if(!is_admin()){
        if(!current_user_can('edit_published_posts')){
            print $hubspot_code;
        } else {
            print '
            <!-- You are logged in as an author or above. HubSpot is not tracking. //-->
            ';
        }
    }
}

function cta_codes($atts,$content){
    ts_data($content);
    return $content;
}

function requireDir($dir){
    $dh = @opendir($dir);

    if (!$dh) {
        throw new Exception("Cannot open directory $dir");
    } else {
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..') {
                $requiredFile = $dir . DIRECTORY_SEPARATOR . $file;
                if ('.php' === substr($file, strlen($file) - 4)) {
                    require_once $requiredFile;
                } elseif (is_dir($requiredFile)) {
                    requireDir($requiredFile);
                }
            }
        }
    closedir($dh);
    }
    unset($dh, $dir, $file, $requiredFile);
}
    //end of class
}
$msd_hubspot = new MSDHubSpot();
