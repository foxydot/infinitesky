<?php
if (!class_exists('MSDLab_Chapter_Title_Support')) {
    class MSDLab_Chapter_Title_Support {
        //Properties
        private $options;

        //Methods
        /**
         * PHP 4 Compatible Constructor
         */
        public function MSDLab_Chapter_Title_Support(){$this->__construct();}

        /**
         * PHP 5 Constructor
         */
        function __construct($options){
            //"Constants" setup
            //Actions
            add_action( 'init', array(&$this,'register_metaboxes') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            add_action('admin_footer',array(&$this,'footer_hook') );
            //Filters
        }

        function register_metaboxes(){
            global $chaptertitle_metabox;
            $chaptertitle_metabox = new WPAlchemy_MetaBox(array
            (
                'id' => '_chaptertitle',
                'title' => 'Chapter Title',
                'types' => array('page'),
                'context' => 'normal', // same as above, defaults to "normal"
                'priority' => 'high', // same as above, defaults to "high"
                'template' => plugin_dir_path(dirname(__FILE__)).'/template/metabox-chapter_title.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_msdlab_' // defaults to NULL
            ));
        }

        function add_admin_styles() {
            wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'css/meta.css');
        }

        function footer_hook()
        {
            ?><script type="text/javascript">
                jQuery('#titlediv').before(jQuery('#_chaptertitle_metabox'));
            </script><?php
        }

    } //End Class
} //End if class exists statement