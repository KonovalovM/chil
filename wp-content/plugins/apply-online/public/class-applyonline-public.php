<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wpreloaded.com/farhan-noor
 * @since      1.0.0
 *
 * @package    Applyonline
 * @subpackage Applyonline/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Applyonline
 * @subpackage Applyonline/public
 * @author     Farhan Noor <profiles.wordpress.org/farhannoor>
 */
class Applyonline_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

                new SinglePostTemplate($plugin_name, $version); //Passing 2 parameters to the child
                new Applyonline_Shortcodes();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Applyonline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Applyonline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

                wp_enqueue_style( $this->plugin_name.'-BS', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
                wp_enqueue_style('aol-jquery-ui-css', plugin_dir_url(__FILE__).'css/jquery-ui.min.css');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/applyonline-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Applyonline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Applyonline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/applyonline-public.js', array( 'jquery','jquery-ui-datepicker' ), $this->version, false );
                //wp_enqueue_script( 'aol-charcounter', plugin_dir_url( __FILE__ ) . 'js/cct_embed.min.js', array(), $this->version, false );
                $aol_js_vars = array(
                        'ajaxurl' => admin_url ( 'admin-ajax.php' ),
                        'date_format'   => get_option('aol_date_format', 'dd-mm-yy')
                        );
                wp_localize_script (
                    $this->plugin_name,
                    'aol_public', 
                    apply_filters('aol_js_vars', $aol_js_vars)
                );
	}
        
        public function check_ad_closing_status($query){
            $types = get_aol_ad_types();
            if(!is_admin() and isset($query->query['post_type']) and in_array($query->query['post_type'], $types)){
                global $wpdb;
                $closed = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_aol_ad_closing_date' AND meta_value != '' AND (meta_value BETWEEN 0 AND UNIX_TIMESTAMP()) AND post_id NOT IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_aol_ad_close_type' AND meta_value != 'ad')");
                $query->set('post__not_in', $closed);
            }
        }
}

class SinglePostTemplate{
        var $plugin_name;
        var $version;
        public function __construct($plugin_name = null, $version = null) {
            $this->plugin_name = $version;
            $this->version = $plugin_name;
            add_filter('body_class', array($this, 'aol_body_classes'));
            add_filter( 'the_content', array($this, 'aol_the_content') );
        }
    
        function aol_body_classes($classes){
            $classes[] = $this->plugin_name;
            $classes[] = $this->plugin_name.'-'.$this->version;
            return $classes;
        }
        public function aol_ad_is_checked($i){
            if($i==0) $checked="checked";
            else $checked = NULL;
            return $checked;
        }

        public function application_form_fields($post_id = 0){
            //Get current post object on SINGLE Template file(e.g. single.php,  aol_ad-single.php).
            if(empty($post_id)){
                global $post;
                $post_id = $post->ID;
            }
            
            $field_types = array('text'=> esc_html__('Text','ApplyOnline'), 'checkbox'=>esc_html__('Check Box','ApplyOnline'), 'dropdown'=>esc_html__('Drop Down','ApplyOnline'), 'radio'=> esc_html__('Radio','ApplyOnline'), 'file'=> esc_html__('File','ApplyOnline'), 'separator' => esc_html__('Seprator','ApplyOnline'));
            
            $raw_fields = get_aol_ad_post_meta($post_id);
            $fields = array();
            $i=0;
            foreach($raw_fields as $key => $val){
                    $fields[$i] = $val; //
                    $fields[$i]['key'] = substr($key, 9); //Add key as array element for easy access.
                    if(isset($fields[$i]['options'])) $fields[$i]['options'] = array_combine(explode(',', $fields[$i]['options']), explode(',', $fields[$i]['options'])); //Add options as an array, Setting arrays keys & values alike.
                    if(!isset($fields[$i]['required'])) $fields[$i]['required'] = 1; //Fix for older versions (prior to 1.6.1 when REQUIRED field was not available)
                    if(isset($fields[$i]['type']) AND $fields[$i]['type']=='seprator') $fields[$i]['type'] = 'separator'; //Fixed bug before 1.9.6, spell mistake in the key.
                    $i++;
            } 
            return $fields;
            //Debuggin
        }        

        public function application_form($post_id = 0){
            
            if(empty($post_id) AND !is_singular()){ 
                return '<p id="aol_form_status alert alert-danger">'.__('Form ID is missing', 'ApplyOnline').'</p>';
            }
            
            global $post;
            $post_id = empty($post_id) ? $post->ID : (int)$post_id;
            $date = get_post_meta($post_id, '_aol_ad_closing_date', TRUE);
            
            $fields = apply_filters('aol_form_fields', $this->application_form_fields($post_id), $post_id);

            //If Form has no fields.
            if( empty($fields) ) return NULL;
            
            ob_start();

            echo '<h3 class="aol-heading">'._x('Apply Online', 'public', 'ApplyOnline').'</h3>';
            //If closing date has passed away.
            if( !empty($date) AND $date < time() )
                return '<span class="alert alert-warning">'. get_option_fixed('aol_application_close_message', __('We are no longer accepting applications for this ad.', 'ApplyOnline')).'</span>';
            ?>
            <form class="aol_app_form aol_app_form_<?php echo $post_id; ?>" name="aol_app_form" id="aol_app_form" enctype="multipart/form-data"  data-toggle="validator" action="#aol_app_form">
                <?php
                    do_action('aol_before_form_fields', $post_id);
                    echo aol_form_generator($fields, 0, '_aol_app_', $post_id);
                    do_action('aol_after_form_fields', $post_id);
                    $aol_form_button = apply_filters('aol_form_button', array('id' => 'aol_app_submit_button', 'value' => __('Submit', 'ApplyOnline'), 'class' => 'btn btn-primary btn-submit button submit fusion-button button-large aol-form-button '. get_option('aol_submit_button_classes')));
                    $attributes = NULL;
                    foreach($aol_form_button as $key => $val){

                        //Sanitized attributes
                        $attributes .= esc_attr($key).'="'.esc_attr($val).'" ';
                    }
                    ?>
                <p><small><i><?php echo get_option('aol_required_fields_notice', 'Fields with (*) are compulsory.'); ?></i></small></p>
                <?php  do_action('aol_before_submit_button', $post_id); ?> 
                <input type="hidden" name="ad_id" value="<?php echo $post_id; ?>" >
                <input type="hidden" name="action" value="aol_app_form" >
                <input type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce( 'the_best_aol_ad_security_nonce' ) ?>" >
                <input type="submit" <?php echo $attributes; ?> >
                <?php do_action('aol_after_submit_button', $post_id); ?>
            </form>
            <progress value="0" max="100" style="width: 100%"></progress>
            <p id="aol_form_status"></p>
        <?php
            return apply_filters('aol_form', ob_get_clean(), $fields, $post_id);
        }

        public function ad_features($post_id = 0, $output = 'table') {
            //Get current post object on SINGLE Template file.
            global $post;
            if(empty($post_id)) $post_id = $post->ID;
            
            $fields = get_aol_ad_features($post_id);

            $metas = NULL;
            if( !empty($fields) ):
                
                switch ($output):
                    case 'heading':
                        $start_wrapper = '<div class="aol_ad_features">';
                        $close_wrapper = '</div>';
                        $row_start = '<h4>';
                        $separator = ':</h4><span>';
                        $row_close = '</span>';
                        break;
                    
                    case 'list':
                        $start_wrapper = '<ul class="aol_ad_features">';
                        $close_wrapper = '</ul>';
                        $row_start = '<li><b>';
                        $separator = ':</b> ';
                        $row_close = '</li>';
                        break;
                    
                    default:
                        $start_wrapper = '<table class="aol_ad_features">';
                        $close_wrapper = '</table>';
                        $row_start = '<tr><td>';
                        $separator = '</td><td>';
                        $row_close = '</td></tr>';
                endswitch;
                $metas = $start_wrapper;
                foreach($fields as $key => $val):
                        if(!is_array($val)) 
                            $val = array('label' => str_replace('_', ' ',substr($key, 13)), 'value' => $val);
                            
                        $metas.= $row_start.$val['label'].$separator.$val['value'].$row_close;
                endforeach;
                $metas.= $close_wrapper;
            endif;
          return $metas;
        }
        
        function ad_type_fix($val, $key){
            return 'aol_'.$val;
        }

        public function aol_the_content($content){
            global $post;
            $types = get_option_fixed('aol_ad_types', array('ad' => array('singular'=> esc_html__('Ad','ApplyOnline'), 'plural' => esc_html__('Ads','ApplyOnline'))));
            $aol_types = array();
            foreach($types as $type => $val){
                $aol_types[] = 'aol_'.$type;
            }
            if(!is_singular($aol_types)) return $content;
            
            global $template; 
            $features = $this->ad_features($post->ID);
            $title_features = empty($features) ? NULL : '<h4 class="aol-heading-features">'. get_option( 'aol_features_title', 'Salient Features' ).'</h4>';
            $form = $this->application_form();
            
            $not_working = '<ul><li>'.esc_html__('If the application does not load after a few seconds, please try the following:', 'ApplyOnline').'</li>';
            $not_working .= '<li>'.esc_html__('Open the application in a new tab. ', 'ApplyOnline').'</li>';
            $not_working .= '<li>'.esc_html__('Try using a different browser. ', 'ApplyOnline').'</li>';
            $not_working .= '<li>'.esc_html__('Try switching to a more stable network connection. ', 'ApplyOnline').'</li>';
            $not_working .= '<li>'.esc_html__('Report this problem to the development team. ', 'ApplyOnline').'</li></ul>';

            //Show this content if you are viewing aol_ad post type using single.php (not with single-aol_type.php)
            $aol_content;
            $this_template = substr(wp_basename($template), 7, -4);
            if(in_array($this_template, $aol_types) OR has_shortcode( $content, 'aol_form' )):
                $aol_content = $content;
            else: 
                $aol_content = '<div class="aol-single aol-wrapper">'.$content.$title_features.$features.$form.'</div>';
                $aol_content = apply_filters( 'aol_content', $aol_content, $content, $features, $form );
            endif;
            return $aol_content;
        }
}

class Applyonline_Shortcodes{
    function __construct() {
        add_shortcode( 'aol', array($this, 'aol') ); //archive of ads.
        add_shortcode( 'aol_ads', array($this, 'aol') ); //deprecated since 1.1
        add_shortcode( 'aol_ad', array($this, 'aol_ad') ); //Single ad with form.
        add_shortcode( 'aol_form', array($this, 'aol_form') ); //Single ad form only.
        add_shortcode( 'aol_filters', array($this, 'aol_filters') ); //Single ad form only.
        add_shortcode( 'aol_features', array($this, 'aol_features') );
    }
    
        /**
         * Shortcode Generator
         * @param type $atts
         * @return type
         */
        function aol( $atts ) {
            $archive_wraper_classes = apply_filters('aol_archive_wrapper_classes', array('aol-ad-outer-wrapper'));
            $wraper_classes = apply_filters('aol_ad_wrapper_classes', array('aol-ad-inner-wrapper'));
            //$title_wrapper = apply_filters('aol_ad_title_wrapper', 'div');
            $title_classes = apply_filters('aol_ad_title_wrapper_classes', array('panel-heading'));
            $body_classes = apply_filters('aol_ad_body_wrapper_classes', array('panel-body'));
            $thumb_wrapper = apply_filters('aol_ad_thumb_wrapper', 'div');
            $thumb_classes= apply_filters('aol_ad_thumb_classes', array('img-thumbnail', 'pull-md-left', 'center-sm-block'));
            $footer_classes = apply_filters('aol_ad_footer_wrapper_classes', array('panel-footer'));
            
            $order = apply_filters('aol_grid_element_order', array('title', 'body_start', 'meta', 'thumbnail', 'excerpt', 'body_close', 'footer'));
            $a = shortcode_atts( array(
                'categories' => NULL, //depricated since 1.9
                'ads' => 'all',
                'excerpt' => 'yes',
                'display'    => 'full',
                'list-style' => 'ul',
                'count' => '-1',
                'filter' => 'yes',
                'type'  => 'ad'
            ), $atts );

            /*Start - Depricated since 1.9*/
            $term = null;
            if(isset($a['categories'])) {
                $_POST['aol_ad_category'] = explode(',',$atts['categories']);
                //$a['show_filter'] = 'no';
                /*
                $args['tax_query'] = array(
                        array('taxonomy' => 'aol_ad_category', 'terms'    => explode(',',$atts['categories']))
                    );
                 */
            }
            /*End - Depricated since 1.9*/

            $lstyle = ($a['list-style'] == 'ol') ? 'ol' : 'ul';
            $args=array(
                'posts_per_page'=> $a['count'],
                'post_type'     =>'aol_'.$a['type'],
            );
            if($a['ads'] != 'all'){
                $args['post__in'] = explode(',',$atts['ads']);
                $a['show_filter'] = 'no';
            }
            
            //Get list of taxanomies
            $taxes = get_object_taxonomies('aol_'.$a['type']);
            $args['tax_query'] = array();
            foreach($taxes as $tax){
                $tax = substr($tax, 7);
                if(isset($_REQUEST[$tax]) AND $_REQUEST[$tax] != NULL) {
                    $args['tax_query'][] = array('taxonomy' => "aol_ad_$tax", 'terms'    => array((int)$_REQUEST[$tax]));
                }
            }

            //query_posts( $args );
            //global $post; 
            $args = apply_filters('aol_pre_get_posts', $args);
            $posts = get_posts($args);

            add_filter( 'excerpt_more', array($this, 'aol_excerpt_more') );
            //$show_filter = get_option('aol_show_filter', 1);
            $filters = aol_ad_cpt_filters($a['type']);
            $filter_count = count($filters);
            ob_start();
            do_action('aol_before_shortcode', $a, $filters);
            if(!(empty($filters) OR $a['filter'] == 'no' )){
                    echo '<div class="well well-lg">';
                    echo '<form method="post" id="aol_'.$a['type'].'_form" action="#aol_'.$a['type'].'_form"><div class="row">';
                    $col_count = $filter_count < 4 ? 12/($filter_count+1) : 3;
                    foreach ($filters as $key => $filter){
                        $key = sanitize_key($key);
                        //$Fclass = ((isset($_REQUEST['filter']) AND $_REQUEST['filter']) == 'aol_ad_'. $key) ? 'selected' : NULL;
                        echo '<div class="col-md-'.$col_count.'"><select name="'.$key.'" class="aol-filter-select form-control"><option value="">'. sprintf(_x('%s - All', 'Filter Dropdown', 'ApplyOnline'), sanitize_text_field($filter['plural'])).'</option>';
                        $args = array(
                            'taxonomy' => 'aol_ad_'. $key,
                            'hide_empty' => true,
                        );
                        $terms = get_terms($args);
                        foreach ($terms as $term){
                            $selected = (isset($_REQUEST[$key]) AND $term->term_id == (int)$_REQUEST[$key]) ? 'selected="selected"': NULL;
                            echo '<option value="'.(int)$term->term_id.'" '.$selected.'>'.sanitize_text_field($term->name).'</option>';
                        }
                        echo '</select></div>';
                    }
                    echo '<div class="col-md-'.$col_count.'"><button class="fusion-button button btn btn-info btn-block aol-filter-button">'.__('Filter', 'ApplyOnline').'</button></div>';
                    echo '</div></form><div class="clearfix"></div></div>';
            }
            if(!empty($posts)):
                if($a['display'] == 'list') echo "<$lstyle>";
                do_action('aol_before_archive');
                echo '<div class="'.implode(' ', $archive_wraper_classes).'">';
                $post_count = 0;
                foreach($posts as $post): setup_postdata($post);
                    $wrapper_inner_classes = apply_filters('aol_ad_inner_wrapper_classes', array('panel', 'panel-default'), $post);
                    /* Getting Post Status*/
                    $timestamp = (int)get_post_meta($post->ID, '_aol_ad_closing_date', true);
                    $timestamp = apply_filters('aol_ad_closing_date', $timestamp, $post);
                    if( $timestamp == 0 OR $timestamp > time() ) $status = 'open';
                    else $status = 'closed';
                    /* END Getting Post Status*/
                    
                    $terms = get_terms(array('object_ids' => $post->ID, 'hide_empty' => TRUE, 'taxonomy' => aol_sanitize_taxonomies($filters)));
                    
                    if($a['display'] == 'list'): echo '<li>'. apply_filters('aol_shortcode_list', '<a href="'.get_the_permalink($post).'">'.$post->post_title.'</a>').'</li>';
                    else:
                        do_action('aol_before_ad', $post_count, $post->post_count);
                        echo '<div class="'.implode(' ', $wraper_classes).' aol_ad_'.$post->ID.'">';
                            echo '<div class="'.implode(' ', $wrapper_inner_classes).' '.$status.'">';
                            foreach($order as $index):
                                switch ($index):
                                    case 'title':
                                        $output = apply_filters('aol_shortcode_title', $post->post_title, $post, $title_classes);
                                        echo '<div class="'.implode(' ', $title_classes).'">'.$output.'</div>';
                                        break;

                                    case 'body_start' :
                                        echo '<div class="'.implode(' ', $body_classes).'">';
                                        do_action('aol_shortcode_before_body');
                                        break;

                                    case 'thumbnail' :
                                        if(has_post_thumbnail($post))  echo get_the_post_thumbnail($post->ID, apply_filters('aol_ad_thumbnail_size', 'thumbnail') , array('class' => implode(' ', $thumb_classes), 'title' => $post->post_title, 'alt' => $post->post_title));
                                        break;

                                    case 'meta' :
                                        echo apply_filters('aol_ad_meta', NULL, $post);
                                        break;

                                    case 'body_close':
                                        $body = array(
                                            'excerpt' => get_the_excerpt($post),
                                            'readmore' => sprintf(
                                                    '<a href="%s" ><button class="%s">%s</button></a>',
                                                    get_the_permalink($post),
                                                    'fusion-button button read-more btn btn-info',
                                                    __( 'Read More', 'ApplyOnline' ))
                                            );
                                        $body = apply_filters('aol_shortcode_body', $body, $post);
                                        do_action('aol_shortcode_before_body', $post);
                                        if($a['excerpt'] != 'no') echo '<p>'.$body['excerpt'].'</p>';
                                        echo '<div class="clearfix"></div>';
                                        echo apply_filters('aol_shortcode_button', $body['readmore']);
                                        do_action('aol_shortocde_after_body', $post);
                                        echo "</div>"; //Boody Wrapper
                                        break;

                                    case 'footer':
                                        if(empty($terms) or empty($filters)) break;
                                        echo '<div class="'.implode(' ', $footer_classes).'">';
                                        echo apply_filters('aol_shortcode_footer', $this->aol_filters_terms($terms, $post->ID), $post, $terms);
                                        echo "</div>";
                                    break;
                                endswitch;
                            endforeach;
                        echo "</div></div>"; //Closing inner & outer wrapers.
                        do_action('aol_after_ad', $post_count, $post->post_count);
                        if($a['display'] == 'list') echo "</$lstyle>";
                    endif; //End aol display check
                    $post_count++;
                endforeach; 
                echo "</div>"; //Outer Wrapper
                do_action('aol_after_archive', $post);
            else: _e('Sorry, we could not find what you were looking for.', 'ApplyOnline');
            endif;
            wp_reset_postdata();
            $html = apply_filters('aol_shortcode', ob_get_clean());
            return '<div class="aol-archive aol-wrapper">'.$html.'</div>';
        }   
        
        
        function aol_filters($atts){
            //@ad support for all ad types.
            //if(!is_singular('aol_ad')) return;
            
            $a = shortcode_atts( array(
                'style'   => 'csv',
                'ad'      => 0,
            ), $atts );
            if(empty($a['ad'])){
                global $post;
                $post_id = $post->ID;
            } else {
                    $post_id = (int)$a['ad'];
            }

            $filters = aol_ad_cpt_filters(get_post_type());
            $terms = get_terms(array('object_ids' => $post_id, 'hide_empty' => TRUE, 'taxonomy' => aol_sanitize_taxonomies($filters)));

            ob_start();
            echo '<div class="aol_meta">' ;
            echo $this->aol_filters_terms($terms, $post_id);
            echo '</div>';
            return ob_get_clean();
            
            ob_start();
            $terms = get_terms(array('object_ids' => $post_id, 'orderby' => 'term_group', 'hide_empty' => TRUE, 'taxonomy' => aol_sanitize_taxonomies($filters)));
            echo '<div class="aol_meta">' ;
                if( !(empty($terms) or empty($filters)) ):
                    $tax = NULL;
                    foreach ($terms as $term){
                            $title = NULL;
                            $separator = ', ';
                        if($tax != $term->taxonomy) {
                            $taxObj = get_taxonomy($term->taxonomy);
                            $pad = empty($tax) ? NULL : ' &nbsp;';
                            $title = $pad.'<h4 class="aol-ad-taxonomy">'.$taxObj->label.': </h4>';
                        }
                        echo $title.'<span>'.$term->name.$separator.'</span>'; 
                        $tax = $term->taxonomy;
                    } 
                endif;
            echo '</div>';
            return ob_get_clean();
        }

        function aol_filters_terms($terms, $post_id){
            do_action('aol_shortcode_before_terms', $post_id);
            $tax = $output = NULL;
            $terms = $this->organize_post_terms($terms);
            foreach ($terms as $term){
                    $title = NULL;
                    $separator = ', ';
                if($tax != $term->taxonomy) {
                    $pad = empty($tax) ? NULL : ' &nbsp;';
                    $taxObj = get_taxonomy($term->taxonomy);
                    $span = is_null($tax) ? '<span class="aol-tax-wrapper">' : '</span><span class="aol-tax-wrapper">';
                    $title = $span.$pad.'<strong class="aol-ad-taxonomy">'.__($taxObj->label, 'ApplyOnline').': </strong>';
                }
                $output.= $title.$term->name.$separator;
                $tax = __($term->taxonomy, 'ApplyOnline');
            }
            $output.= '</span>';
            do_action('aol_shortcode_after_terms', $post_id);
            return $output;
        }
        
        function organize_post_terms($terms_obj){
            $terms = new stdClass();
            $sort = array();
            
            foreach($terms_obj as $key => $term){
                $sort[$key] = $term->taxonomy;
            }
            asort($sort);
            $keys = array_keys($sort);
            $new_obj = array();
            foreach($keys as $key){
                array_push($new_obj, $terms_obj[$key]);
            }
            
            return $new_obj;
        }

        function aol_excerpt_more( $more ) {
                return '....';
            }

         //@todo Form generated with this shortcode may not submit & generate error: "Your form could not submit, Please contact us"
        function aol_form( $atts ){
            global $post;
            $id = is_singular('aol_ad') ? $post->ID: NULL;
            $a = shortcode_atts( array(
                //Check if shortcode is called on the Ads Page, ID is not required in that case.
                //@todo extend post type to all ad types.
                'id'   => $id,
            ), $atts );
            
            if(isset($a['id']))    return aol_form($a['id']);
        }         

        /*
         * @todo: this function should print complete ad with application form.
         */
        function aol_ad( $atts ) {
            $a = shortcode_atts( array(
                'id'   => NULL,
            ), $atts );

            if(isset($a['id'])) {
                $id = $a['id'];
                $post = get_post($id);
                return $post->post_content. aol_form($a['id']);
            }
        }
        
        function aol_features($atts){
            $a = shortcode_atts( array(
                'style'   => 'table',
            ), $atts );
            
            return aol_features($a['style']);
        }        
}