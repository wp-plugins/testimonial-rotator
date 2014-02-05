<?php
/*
Plugin Name: Testimonial Rotator
Plugin URI: http://halgatewood.com/testimonial-rotator
Description: A handy plugin for WordPress developers to add testimonials to their site. Enough functionality to be helpful and also stays out of your way.
Author: Hal Gatewood
Author URI: http://www.halgatewood.com
Text Domain: testimonial_rotator
Domain Path: /languages
Version: 1.4
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/


define( 'TESTIMONIAL_ROTATOR_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


/* SETUP */
add_action( 'plugins_loaded', 'testimonial_rotator_setup' );
function testimonial_rotator_setup() 
{
	add_action( 'init', 'testimonial_rotator_init' );
	
	add_action( 'add_meta_boxes', 'testimonial_rotator_create_metaboxes' );
	add_action( 'save_post', 'testimonial_rotator_save_testimonial_meta', 1, 2 );
	add_action( 'save_post', 'testimonial_rotator_save_rotator_meta', 1, 2 );
	
	add_filter( 'manage_edit-testimonial_columns', 'testimonial_rotator_columns' );
	add_action( 'manage_testimonial_posts_custom_column', 'testimonial_rotator_add_columns' );
	add_filter( 'manage_edit-testimonial_sortable_columns', 'testimonial_rotator_column_sort' );
	
	add_filter( 'manage_edit-testimonial_rotator_columns', 'testimonial_rotator_rotator_columns' );
	add_action( 'manage_testimonial_rotator_posts_custom_column', 'testimonial_rotator_rotator_add_columns' );
	
	add_action( 'admin_head', 'testimonial_rotator_cpt_icon' );
	add_action( 'admin_head', 'testimonial_rotator_admin_menu_highlight' );
	
	add_action( 'admin_menu', 'register_testimonial_rotator_submenu_page' );

	add_action( 'widgets_init', create_function('', 'return register_widget("TestimonialRotatorWidget");') );
	
	add_action( 'wp_enqueue_scripts', 'testimonial_rotator_enqueue_scripts' );
}

function testimonial_rotator_enqueue_scripts()
{
	wp_enqueue_script( 'cycletwo', plugins_url('/js/jquery.cycletwo.js', __FILE__), array('jquery') );
	wp_enqueue_script( 'cycletwo-addons', plugins_url('/js/jquery.cycletwo.addons.js', __FILE__), array('jquery', 'cycletwo') );
	wp_enqueue_style( 'testimonial-rotator-style', plugins_url('/testimonial-rotator-style.css', __FILE__) ); 
}


/* CREATES THE CUSTOM POST TYPE */
function testimonial_rotator_init() 
{
	// LOAD TEXT DOMAIN
	load_plugin_textdomain( 'testimonial_rotator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// REGISTER SHORTCODE
	add_shortcode( 'testimonial_rotator', 'testimonial_rotator_shortcode' );
	
	// POST THUMBNAILS (pippin)
	if(!current_theme_supports('post-thumbnails')) { add_theme_support('post-thumbnails'); }

	// TESTIMONIAL CUSTOM POST TYPE
  	$labels = array(
				    'name' 					=> __('Testimonials', 'testimonial_rotator'),
				    'singular_name' 		=> __('Testimonial', 'testimonial_rotator'),
				    'add_new' 				=> __('Add New', 'testimonial_rotator'),
				    'add_new_item' 			=> __('Add New Testimonial', 'testimonial_rotator'),
				    'edit_item' 			=> __('Edit Testimonial', 'testimonial_rotator'),
				    'new_item' 				=> __('New Testimonial', 'testimonial_rotator'),
				    'all_items' 			=> __('All Testimonials', 'testimonial_rotator'),
				    'view_item' 			=> __('View Testimonial', 'testimonial_rotator'),
				    'search_items' 			=> __('Search Testimonials', 'testimonial_rotator'),
				    'not_found' 			=>  __('No testimonials found', 'testimonial_rotator'),
				    'not_found_in_trash' 	=> __('No testimonials found in Trash', 'testimonial_rotator'), 
				    'parent_item_colon' 	=> '',
				    'menu_name'				=> __('Testimonials', 'testimonial_rotator')
  					);
	$args = array(
					'labels' 				=> $labels,
					'public' 				=> true,
					'publicly_queryable' 	=> true,
					'show_ui' 				=> true, 
					'show_in_menu' 			=> true, 
					'query_var' 			=> true,
					'rewrite' 				=> array( "slug" => apply_filters( "testimonial_rotator_testimonial_slug"  , "testimonials") ),
					'capability_type' 		=> 'post',
					'has_archive' 			=> true,
					'hierarchical' 			=> false,
					'menu_position' 		=> 26.6,
					'exclude_from_search' 	=> true,
					'supports' 				=> apply_filters( "testimonial_rotator_testimonial_supports", array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ) )
					);
					
	register_post_type( 'testimonial', $args );

	// TESTIMONIAL ROTATOR CUSTOM POST TYPE
  	$labels = array(
				    'name' 					=> __('Testimonial Rotators', 'testimonial_rotator'),
				    'singular_name' 		=> __('Rotator', 'testimonial_rotator'),
				    'add_new' 				=> __('Add New', 'testimonial_rotator'),
				    'add_new_item' 			=> __('Add New Rotator', 'testimonial_rotator'),
				    'edit_item' 			=> __('Edit Rotator', 'testimonial_rotator'),
				    'new_item' 				=> __('New Rotator', 'testimonial_rotator'),
				    'all_items' 			=> __('All Rotators', 'testimonial_rotator'),
				    'view_item' 			=> __('View Rotator', 'testimonial_rotator'),
				    'search_items' 			=> __('Search Rotators', 'testimonial_rotator'),
				    'not_found' 			=>  __('No rotators found', 'testimonial_rotator'),
				    'not_found_in_trash' 	=> __('No rotators found in Trash', 'testimonial_rotator'), 
				    'parent_item_colon' 	=> '',
				    'menu_name'				=> __('Rotators', 'testimonial_rotator')
  					);
						
	$args = array(
					'labels' 				=> $labels,
					'public' 				=> false,
					'publicly_queryable' 	=> false,
					'show_ui' 				=> true, 
					'show_in_menu' 			=> false, 
					'query_var' 			=> true,
					'rewrite' 				=> array( 'with_front' => false ),
					'capability_type' 		=> 'post',
					'has_archive' 			=> false,
					'hierarchical' 			=> false,
					'menu_position' 		=> 26.7,
					'exclude_from_search' 	=> true,
					'supports' 				=> apply_filters( "testimonial_rotator_supports", array( 'title' ) )
					);
					
	register_post_type( 'testimonial_rotator', $args );
}


/* ADDS META BOXES TO THE ADD NEW TESTIMONIAL PAGE */
function testimonial_rotator_create_metaboxes() 
{
	// TESTIMONIAL OPTIONS
	add_meta_box( 'testimonial_rotator_metabox_select', __('Testimonial Options', 'testimonial_rotator'), 'testimonial_rotator_metabox_select', 'testimonial', 'normal', 'default' );
	
	// TESTIMONIAL IMAGE
	add_meta_box( 'postimagediv', __('Testimonial Image', 'testimonial_rotator'), 'post_thumbnail_meta_box', 'testimonial', 'normal', 'default');
	
	// TESTIMONIAL ORDER
	add_meta_box( 'pageparentdiv', __('Testimonial Order', 'testimonial_rotator'), 'page_attributes_meta_box', 'testimonial', 'normal', 'default');
	
	// IF ON EDIT SHOW THE SHORTCODE
	if(isset($_GET['action']) AND $_GET['action'] == "edit")
	{
		add_meta_box( 'testimonial_rotator_shortcode_metabox', __('Rotator Shortcode', 'testimonial_rotator'), 'testimonial_rotator_shortcode_metabox', 'testimonial_rotator', 'normal', 'default' );
	}
	
	// ROTATOR OPTIONS
	add_meta_box( 'testimonial_rotator_metabox_effects', __('Rotator Options', 'testimonial_rotator'), 'testimonial_rotator_metabox_effects', 'testimonial_rotator', 'normal', 'default' );
	
}


/* MAIN TESTIMONIAL META BOX (SELECT BOX OF ROTATORS) */
function testimonial_rotator_metabox_select() 
{
	global $post;	
	$rotator_id	= get_post_meta( $post->ID, '_rotator_id', true ); 
	
	$rotators = get_posts( array( 'post_type' => 'testimonial_rotator', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
?>
	<?php if(!count($rotators)) { ?>
		<p style="color: red;">
			<b><?php _e('No Testimonial Rotators have been created.', 'testimonial_rotator'); ?></b><br />
			<?php _e("You need to publish this testimonial so you don't lose your work and then go create a Testimonial Rotator. You can then edit this testimonial and select the rotator here.", "testimonial_rotator"); ?>
		</p>
	<?php } else { ?>
		<p>
		<?php _e('Attach to Rotator: ', 'testimonial_rotator'); ?> 
		<select name="rotator_id">
			<?php foreach($rotators as $rotator) { ?>
			<option value="<?php echo $rotator->ID ?>" <?php if($rotator->ID == $rotator_id) echo " SELECTED"; ?>><?php echo $rotator->post_title ?></option>
			<?php } ?>
		</select>
	</p>
	<?php } ?>
<?php }


/* SHORTCODE DISPLAY HELPER */
function testimonial_rotator_shortcode_metabox()
{
	global $post;
	
	echo '
		<b>' . __('Base Rotator', 'testimonial_rotator') . '</b><br />
		[testimonial_rotator id="' . $post->ID . '"]<br /><br />
		
		<b>' . __('List All Testimonials', 'testimonial_rotator') . '</b><br />
		[testimonial_rotator id="' . $post->ID . '" format="list"]<br /><br />
		
		<b>' . __('Limit Results to 10', 'testimonial_rotator') . '</b><br />
		[testimonial_rotator id="' . $post->ID . '" format="list" limit="10"]<br /><br />
			
		<b>' . __('Hide Titles', 'testimonial_rotator') . '</b><br />
		[testimonial_rotator id="' . $post->ID . '" hide_title="1"]<br /><br />
		
		<b>' . __('Randomize Testimonials', 'testimonial_rotator') . '</b><br />
		[testimonial_rotator id="' . $post->ID . '" shuffle="1"]<br /><br />	
	';
	
	echo '<span class="description">' . __('Put one of the above codes wherever you want the testimonials to appear', 'testimonial_rotator') . '</span>';	
}


/* TESTIMONIAL ROTATOR EFFECTS AND TIMING */
function testimonial_rotator_metabox_effects()
{
	global $post;
	$timeout		= (int) get_post_meta( $post->ID, '_timeout', true );
	$fx				= get_post_meta( $post->ID, '_fx', true );
	
	$available_effects = array('fade', 'scrollHorz', 'scrollVert',  'none');
	
	if(!$timeout) { $timeout = 5; }
	?>
	
	<p>
		<?php _e('Seconds per Testimonial:', 'testimonial_rotator'); ?>
		<input type="text" style="width: 45px; text-align: center;" name="timeout" value="<?php echo esc_attr( $timeout ); ?>" maxlength="2" /></p>
	</p>
	<p>
		<?php _e('Transition Effect:', 'testimonial_rotator'); ?>
		<select name="fx">
			<?php foreach($available_effects as $available_effect) { ?>
			<option value="<?php echo $available_effect ?>" <?php if($available_effect == $fx) echo " SELECTED"; ?>><?php echo $available_effect ?></option>
			<?php } ?>
		</select>
	</p>	
	<?php	
}

/* ROTATOR SUBMENU PAGE */
function register_testimonial_rotator_submenu_page()
{
	add_submenu_page( 'edit.php?post_type=testimonial', __('Rotators', 'testimonial_rotator'), __('Rotators', 'testimonial_rotator'), 'manage_options', 'edit.php?post_type=testimonial_rotator' ); 
	add_submenu_page( 'edit.php?post_type=testimonial', __('Add New', 'testimonial_rotator'), __('Add New', 'testimonial_rotator'), 'manage_options', 'post-new.php?post_type=testimonial_rotator' ); 
}


/* SAVE TESTIMONIAL META DATA */
function testimonial_rotator_save_testimonial_meta( $post_id, $post ) 
{
	global $post;  
	if( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == "testimonial" )  
    {  
		// SAVE
		if ( isset( $_POST['rotator_id'] ) ) { update_post_meta( $post_id, '_rotator_id', strip_tags( $_POST['rotator_id'] ) ); }
	}
}


/* SAVE TESTIMONIAL ROTATOR META DATA */
function testimonial_rotator_save_rotator_meta( $post_id, $post ) 
{
	global $post;  
	if( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == "testimonial_rotator" )  
    {  
		// SAVE
		if ( isset( $_POST['fx'] ) ) { update_post_meta( $post_id, '_fx', strip_tags( $_POST['fx'] ) ); }
		if ( isset( $_POST['timeout'] ) ) { update_post_meta( $post_id, '_timeout', strip_tags( $_POST['timeout'] ) ); }
	}
}


/* ADMIN COLUMNS IN LIST VIEW */
function testimonial_rotator_columns( $columns ) 
{
	$columns = array(
		'cb'       => '<input type="checkbox" />',
		'image'    => __('Image', 'testimonial_rotator'),
		'title'    => __('Title', 'testimonial_rotator'),
		'ID'       => __('Rotator', 'testimonial_rotator'),
		'order'    => __('Order', 'testimonial_rotator'),
		'date'     => __('Date', 'testimonial_rotator')
	);

	return $columns;
}
function testimonial_rotator_column_sort($columns)
{
	$columns = array(
		'title'    => __('Title', 'testimonial_rotator'),
		'ID'       => __('Rotator', 'testimonial_rotator'),
		'date'     => __('Date', 'testimonial_rotator')
	);

	return $columns;
}
function testimonial_rotator_add_columns( $column ) 
{
	global $post;
	$edit_link = get_edit_post_link( $post->ID );

	if ( $column == 'ID' ) 		{ echo get_the_title( get_post_meta( $post->ID, "_rotator_id", true ) ); }
	if ( $column == 'image' ) 	echo '<a href="' . $edit_link . '" title="' . $post->post_title . '">' . get_the_post_thumbnail( $post->ID, array( 50, 50 ), array( 'title' => trim( strip_tags(  $post->post_title ) ) ) ) . '</a>';
	if ( $column == 'order' ) 	echo '<a href="' . $edit_link . '">' . $post->menu_order . '</a>';
}

function testimonial_rotator_rotator_columns( $columns ) 
{
	$columns = array(
		'cb'       		=> '<input type="checkbox" />',
		'title'    		=> __('Title', 'testimonial_rotator'),
		'slug'    		=> __('Slug', 'testimonial_rotator'),
		'shortcode'		=> __('Shortcodes', 'testimonial_rotator')
	);

	return $columns;
}

function testimonial_rotator_rotator_add_columns( $column ) 
{
	global $post;
	if ( $column == 'shortcode' )  	
	{ 	
		echo '
			<b>' . __('Base Rotator') . '</b><br />
			[testimonial_rotator id="' . $post->ID . '"]<br /><br />
			
			<b>' . __('List All Testimonials') . '</b><br />
			[testimonial_rotator id="' . $post->ID . '" format="list"]<br /><br />
			
			<b>' . __('Limit Results to 10') . '</b><br />
			[testimonial_rotator id="' . $post->ID . '" format="list" limit="10"]<br /><br />
				
			<b>' . __('Hide Titles') . '</b><br />
			[testimonial_rotator id="' . $post->ID . '" hide_title="1"]<br /><br />
			
			<b>' . __('Randomize Testimonials') . '</b><br />
			[testimonial_rotator id="' . $post->ID . '" shuffle="1"]<br /><br />	
		'; }
	if ( $column == 'slug' ) { echo $post->post_name; }
}


function testimonial_rotator_admin_menu_highlight() 
{
	global $menu, $submenu, $parent_file, $submenu_file, $self, $post_type, $taxonomy;
	
	$to_highlight_types = array( 'testimonial_rotator' );
	if ( isset( $post_type ) ) 
	{
		if ( in_array( $post_type, $to_highlight_types ) ) 
		{
			$submenu_file = 'edit.php?post_type=' . $post_type;
			$parent_file  = 'edit.php?post_type=testimonial_rotator';
		}
	}
}



/* SHORTCODE */
function testimonial_rotator_shortcode($atts)
{
	$rotator = testimonial_rotator($atts);
	return $rotator;
}



/* MEAT & POTATOES OF THE ROTATOR */
function testimonial_rotator($atts)
{
	$id 				= isset($atts['id']) ? $atts['id'] : false;
	$show_title 		= isset($atts['hide_title']) ? false : true;
	$format				= isset($atts['format']) ? $atts['format'] : "rotator";
	$post_count			= isset($atts['limit']) ? (int) $atts['limit'] : -1;
	$shuffle			= (isset($atts['shuffle']) AND $atts['shuffle'] == 1) ? 1 : 0;
	

	
	// SETUP TESTIMONIAL ARGS
	$testimonials_args = array();
	$testimonials_args['post_type'] 			= "testimonial";
	$testimonials_args['order'] 				= "ASC";
	$testimonials_args['orderby'] 				= ($shuffle) ? "rand" : "menu_order";
	$testimonials_args['posts_per_page'] 		= $post_count;

	
	// SHOW ALL TESTIMONIALS
	if($id)
	{
		if(!is_numeric($id))
		{
			$rotator = get_page_by_path( $id, null, 'testimonial_rotator' );
			if($rotator)
			{
				$id = $rotator->ID;
				$testimonials_args['meta_key'] 			= '_rotator_id';
				$testimonials_args['meta_value'] 		= $id;
			}
		}
		else
		{
			$testimonials_args['meta_key'] 				= '_rotator_id';
			$testimonials_args['meta_value'] 			= $id;
		}
	}

	
	$timeout		= (int) get_post_meta( $id, '_timeout', true );
	$timeout 		= round($timeout * 1000);
	$fx				= get_post_meta( $id, '_fx', true );
	

	$testimonials = new WP_Query( apply_filters( 'testimonial_rotator_display_args', $testimonials_args ) );

	$rtn = "";

	if ( $testimonials->have_posts() )
	{
		$addClass = ($format == "rotator") ? "cycletwo-slideshow"  : "";
	
		$rtn .= "<div class=\"testimonial_rotator_wrap\">\n";
		$rtn .= "	<div id=\"testimonial_rotator_{$id}\" class=\"testimonial_rotator $addClass\" data-cycletwo-fx=\"{$fx}\" data-cycletwo-auto-height=\"" . apply_filters( 'testimonial_rotator_calc', 'calc' ) . "\" data-cycletwo-slides=\"> div.slide\" data-cycletwo-timeout=\"{$timeout}\" data-cycletwo-speed=\"" . apply_filters( 'testimonial_rotator_speed', 750 ) . "\" data-cycletwo-pause-on-hover=\"#testimonial_rotator_{$id}\">\n";
		
		do_action( 'testimonial_rotator_slides_before' );
		
		while ( $testimonials->have_posts() )
		{
			$testimonials->the_post();
				
			$slide = "<div class=\"slide cf-tr\">\n";
			
			if($show_title) $slide .= "	<h2>" . get_the_title() . "</h2>\n";
			
			if ( has_post_thumbnail() )
			{ 
				$slide .= "	<div class=\"img\">" . get_the_post_thumbnail( get_the_ID(), 'thumbnail') . "</div>\n"; 
			}
			$slide .= "	<div class=\"text\">" . do_shortcode(nl2br(get_the_content(' '))) . "</div>\n";
			$slide .= "</div>\n";

			$rtn .= apply_filters( 'testimonial_rotator_slide', $slide );
		}
		
		do_action( 'testimonial_rotator_after' );
		
		$rtn .= "</div>\n</div>\n\n";
	}
	
	wp_reset_postdata();
	wp_reset_query();
	return $rtn;
}


/* WIDGET */
class TestimonialRotatorWidget extends WP_Widget
{
	function TestimonialRotatorWidget()
	{
		$widget_ops = array('classname' => 'TestimonialRotatorWidget', 'description' => __('Displays rotating testimonials', 'testimonial_rotator') );
		$this->WP_Widget('TestimonialRotatorWidget', __('Testimonials Rotator', 'testimonial_rotator'), $widget_ops);
	}
 
	function form($instance)
	{
		$rotators = get_posts( array( 'post_type' => 'testimonial_rotator', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
		
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		
		$title 			= isset($instance['title']) ? $instance['title'] : "";
		$rotator_id 	= isset($instance['rotator_id']) ? $instance['rotator_id'] : 0;
		$format			= isset($instance['format']) ? $instance['format'] : "rotator";
		$shuffle		= (isset($instance['shuffle']) AND $instance['shuffle'] == 1) ? 1 : 0;
		$limit 			= (int) isset($instance['limit']) ? $instance['limit'] : 5;
		$show_size 		= (isset($instance['show_size']) AND $instance['show_size'] == "full") ? "full" : "excerpt";
		
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'testimonial_rotator'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
	
		<p>
		<label for="<?php echo $this->get_field_id('rotator_id'); ?>"><?php _e('Rotator', 'testimonial_rotator'); ?>:
		<select name="<?php echo $this->get_field_name('rotator_id'); ?>" class="widefat" id="<?php echo $this->get_field_id('rotator_id'); ?>">
			<option value=""><?php _e('All Rotators', 'testimonial_rotator'); ?></option>
			<?php foreach($rotators as $rotator) { ?>
			<option value="<?php echo $rotator->ID ?>" <?php if($rotator->ID == $rotator_id) echo " SELECTED"; ?>><?php echo $rotator->post_title ?></option>
			<?php } ?>
		</select>
		</label>
		</p>
		
		<p><label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit:', 'testimonial_rotator'); ?> <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr($limit); ?>" /></label></p>
	
		
		<p>
			<label for="<?php echo $this->get_field_id('format'); ?>"><?php _e('Display:', 'testimonial_rotator'); ?></label> &nbsp;
			<input id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>" value="rotator" type="radio"<?php if($format != "list") echo " checked='checked'"; ?>> <?php _e('Rotator', 'testimonial_rotator'); ?> &nbsp;
			<input id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>" value="list" type="radio"<?php if($format == "list") echo " checked='checked'"; ?>> <?php _e('List', 'testimonial_rotator'); ?>
		</p>		
		
		<p>
			<label for="<?php echo $this->get_field_id('show_size'); ?>"><?php _e('Show as:', 'testimonial_rotator'); ?></label> &nbsp;
			<input id="<?php echo $this->get_field_id('show_size'); ?>" name="<?php echo $this->get_field_name('show_size'); ?>" value="full" type="radio"<?php if($show_size == "full") echo " checked='checked'"; ?>> <?php _e('Full', 'testimonial_rotator'); ?>&nbsp;
			<input id="<?php echo $this->get_field_id('show_size'); ?>" name="<?php echo $this->get_field_name('show_size'); ?>" value="excerpt" type="radio"<?php if($show_size == "excerpt") echo " checked='checked'"; ?>> <?php _e('Excerpt', 'testimonial_rotator'); ?>
		</p>
		
		<p>
			<input id="<?php echo $this->get_field_id('shuffle'); ?>" name="<?php echo $this->get_field_name('shuffle'); ?>" value="1" type="checkbox"<?php if($shuffle) echo " checked='checked'"; ?>>&nbsp;
			<label for="<?php echo $this->get_field_id('shuffle'); ?>"><?php _e('Randomize Testimonials', 'testimonial_rotator'); ?></label>
		</p>
		
	<?php
	}
 
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] 			= $new_instance['title'];
		$instance['rotator_id'] 	= $new_instance['rotator_id'];
		$instance['format'] 		= $new_instance['format'];
		$instance['shuffle'] 		= isset($new_instance['shuffle']) ? 1 : 0;
		$instance['show_size'] 		= $new_instance['show_size'];
		$instance['limit'] 			= $new_instance['limit'];
		return $instance;
	}
 
	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);
		
		echo $before_widget;
		
		$title 			= empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$rotator_id 	= (int) isset($instance['rotator_id']) ? $instance['rotator_id'] : false ;
		$shuffle		= (isset($instance['shuffle']) AND $instance['shuffle'] == 1) ? 1 : 0;
		$show_size 		= (isset($instance['show_size']) AND $instance['show_size'] == "full") ? "full" : "excerpt";
		$limit 			= (int) isset($instance['limit']) ? $instance['limit'] : 5;
		$format			= isset($instance['format']) ? $instance['format'] : "rotator";
		
		if (!empty($title)) { echo $before_title . $title . $after_title; }

		$testimonials_args = array( 'post_type' => 'testimonial', 'orderby' => 'menu_order', 'posts_per_page' => $limit );

		$fx = 'fade';
		$timeout = 4000;
		
		if($rotator_id)
		{
			$testimonials_args['meta_key'] 		= '_rotator_id';
			$testimonials_args['meta_value'] 	= $rotator_id;
			
			$rotator = get_page_by_path( $rotator_id, null, 'testimonial_rotator' );
			
			$timeout		= (int) get_post_meta( $rotator_id, '_timeout', true );
			$timeout 		= round($timeout * 1000);
			$fx				= get_post_meta( $rotator_id, '_fx', true );
		}	
		
		if($shuffle)
		{
			$testimonials_args['orderby'] = 'rand';
		}	
		
		$testimonials = new WP_Query( apply_filters( 'testimonial_rotator_widget_testimonial_args', $testimonials_args )  );
		
		$rtn = "";
		
		if ( $testimonials->have_posts() )
		{
			$addClass = ($format == "rotator") ? "cycletwo-slideshow"  : "";
		
			$rtn .= "<div class=\"testimonial_rotator_widget_wrap\">\n";
			$rtn .= "	<div id=\"testimonial_rotator_widget_{$id}\" class=\"testimonial_rotator_widget {$addClass}\" data-cycletwo-fx=\"" . apply_filters( 'testimonial_rotator_widget_fx', $fx ) . "\" data-cycletwo-auto-height=\"" . apply_filters( 'testimonial_rotator_widget_calc', 'calc' ) . "\" data-cycletwo-slides=\"> div.slide\" data-cycletwo-timeout=\"" . apply_filters( 'testimonial_rotator_widget_timeout', $timeout ) . "\" data-cycletwo-speed=\"" . apply_filters( 'testimonial_rotator_widget_speed', 750 ) . "\" data-cycletwo-pause-on-hover=\"#testimonial_rotator_widget_{$id}\">\n";
			
			$template = "slide-widget";
			
			while ( $testimonials->have_posts() )
			{
				$testimonials->the_post();
					
				$slide = "<div class=\"slide\">\n";		
				$slide .= "	<blockquote class=\"testimonial_rotator_widget_blockquote\">\n";
				
				if($show_size == "full")
				{
					$slide .= do_shortcode(nl2br(get_the_content(' ')));
				}
				else
				{
					$slide .= get_the_excerpt();
				}
				
				$cite_title = get_the_title();
				if($cite_title) $slide .= "	<cite>- {$cite_title}</cite>";
				
				$slide .= "	</blockquote>\n";
				$slide .= "</div>\n";
				
				$rtn .= apply_filters( 'testimonial_rotator_widget_slide', $slide );
			} 
			
			$rtn .= "</div>\n</div>\n\n";
		}
		
		wp_reset_postdata();
		wp_reset_query();
		
		echo $rtn;
		echo $after_widget;
	}
}



/* ADMIN ICON */
function testimonial_rotator_cpt_icon() 
{
	global $wp_version;
	
	if($wp_version >= 3.8)
	{
		echo '
			<style> 
				#adminmenu #menu-posts-testimonial div.wp-menu-image:before { content: "\f205"; }
			</style>
		';	
	}
	else
	{
?>
	<style type="text/css" media="screen">
		#menu-posts-testimonial .wp-menu-image { background: url(<?php echo TESTIMONIAL_ROTATOR_URI . '/thumb-up.png'; ?>) no-repeat 6px -17px !important; }
		#menu-posts-testimonial:hover .wp-menu-image, #menu-posts-testimonial.wp-has-current-submenu .wp-menu-image { background-position: 6px 7px!important; }	
	</style>
<?php 
	}
		
}
?>