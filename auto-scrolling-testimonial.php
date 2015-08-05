<?php
/*
Plugin Name: Auto Scrolling Testimonials
Plugin URI: http://opensourcetechnologies.com
Description: Auto Scrolling Testimonials plugin allows you to show testimonials with auto scrolling effect in the side bar by using widget. There is also a shortcode functionaility which helps to show testimonials records in pages. To show testimonials on pages  you can use the shortcode [astlist]. This shortcode also accepts the different arguments. For example [ostlist nop="5" of testimonials" order="rand"].
Version: 1.1
Author: Opensourcetechnologies
Author URI: http://opensourcetechnologies.com
*/

add_action( 'wp_enqueue_scripts', 'register_plugin_styles'  );

class ast_testimonials extends WP_Widget
{

	//-----------------------------------------------
	function __construct() {
			parent::__construct(
				// Base ID of your widget
				'ast_testimonial_widget',
				// Widget name will appear in UI
				__('Auto Scrolling Testimonials', 'ast_testimonial_widget_domain'),
				// Widget description
				array( 'description' => __( 'Auto Scrolling Testimonials', 'ast_testimonial_widget_domain' ), )
			);
		
	}
 
  //-----------------------------------------------
  function form($instance){
    $instance		= wp_parse_args( (array) $instance, array( 'num_posts' => '' ) );
    $title			= $instance['title'];
    $num_posts		= $instance['num_posts'];
    $order			= $instance['order'];
    $count_char		= $instance['count_char'];
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: 
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</label>
	</p>

	<p><label for="<?php echo $this->get_field_id('num_posts'); ?>">Number of Testimonials to show: 
	<input class="widefat" id="<?php echo $this->get_field_id('num_posts'); ?>" name="<?php echo $this->get_field_name('num_posts'); ?>" type="text" value="<?php if($num_posts==''){echo'5' ;} else{echo esc_attr($num_posts);} ?>" />
	</label></p>

	<p><label for="<?php echo $this->get_field_id('order'); ?>">Order: 
	<select class="widefat" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
	<option value="ASC" <?php echo ($order=='ASC')?'Selected':''; ?>>ASC by Post Date</option>
	<option value="DESC" <?php echo ($order=='DESC')?'Selected':''; ?>>DESC by Post Date</option>
	<option value="rand" <?php echo ($order=='rand')?'Selected':''; ?>>Random</option>
	</select>
	</label></p>

	<p><label for="<?php echo $this->get_field_id('count_char'); ?>">No of Words Count: 
	<input class="widefat" id="<?php echo $this->get_field_id('count_char'); ?>" name="<?php echo $this->get_field_name('count_char'); ?>" type="text" value="<?php if($count_char==''){echo'150' ;} else{echo esc_attr($count_char); }?>" />
	</label></p>


	</label></p>
<?php
  }
 

  //------------------------------------------------------------
  function update($new_instance, $old_instance){
    $instance					= $old_instance;
     $instance['title']			= strip_tags( $new_instance['title'] );
     $instance['num_posts']		= $new_instance['num_posts'];
     $instance['order']			= $new_instance['order'];
     $instance['count_char']	= $new_instance['count_char'];
     return $instance;
  }
 
 //-----------------ast_shortDescription-----------------------------

 function ast_shortDescription($fullDescription,$initialCount = 30) {

  $fullDescription = trim(strip_tags($fullDescription));
   if ($fullDescription) {

	   if (strlen($fullDescription) > $initialCount) {
			$shortDescription = substr($fullDescription,0,$initialCount)."...";
	   }else{
		return $fullDescription;
	   }
  }

   return $shortDescription;

 }
 
 //--------------------Show Widget on front end----------------------------------------
  function widget($args, $instance){
	$title = apply_filters('widget_title', $instance['title'] );
	extract($args, EXTR_SKIP);
	
	$num_posts		= empty($instance['num_posts']) ? ' ' :  $instance['num_posts'];
	$order			= empty($instance['order']) ? 'DESC' :  $instance['order'];
	$count_char		= empty($instance['count_char']) ? ' ' :  $instance['count_char'];

	
    /* Preparing arguments for query post */
    if($order=='rand')
    {
		$args = array(
			'post_type' => 'ast_testimonial',
			'orderby'     => $order,
			'showposts' => $num_posts,
			'post_status' => 'publish',
			);
	}
	else
	{
		$args = array(
			'post_type' => 'ast_testimonial',
			'order'     => $order,
			'showposts' => $num_posts,
			'post_status' => 'publish',
			);
	}
	$query = new WP_Query( $args );

	/*echo '<pre>';
	print_r($query);
	echo '</pre>';*/
	/* The loop to show widget  */ 
	?>
	<style type="text/css">
		#news-container{ height:<?php echo $height.'px;'?> !important; }
	</style>
	<script type="text/javascript">
	<!--
		jQuery(function() {
		jQuery(".newsticker-jcarousellite").jCarouselLite({ vertical: true, hoverPause:true, visible: 3, auto:500, speed:1000});
	});
	//-->
	</script>

	<?php
	
	$result = '<div id="newsticker-demo">    
		<div class="title">'.$title.'</div>
		<div class="newsticker-jcarousellite">
				<ul>';
	while ($query->have_posts() ) : $query->the_post(); 
		
		$content = apply_filters('the_content', get_the_content());


		$result .= '<li>';
		//$result .= '<div class="thumbnail"><img src="http://shailimittal.com/clients/unique/images/letmeart/429photo_55.jpg" width="80"></div>';
		$result .= '<div class="info">';

		$tauthor = get_post_meta(get_the_ID(),'Author',true);

		if(count($tauthor)>0) $result .= '<span class="author">'.$tauthor.'</span>';

		$result .= $this->ast_shortDescription(strip_tags($content),$count_char).'</div>';
		
		$result .= '<br/><a class="ostmore" href="'.get_permalink().'">Read more</a>';

		$websiteLink = get_post_meta(get_the_ID(),'Website',true);

		if(!empty($websiteLink))$result .= '<br/><a class="ostmore" href="http://'.$websiteLink.'">View Website</a>';

		$result .= '<div class="clear"></div></li>';

		//
       
	endwhile;
	$result .='</ul>
			</div>
		</div>';

	wp_reset_query(); 
	echo $before_widget;
	echo $result;
	echo $after_widget;
	
  }
  //-----------------------------------------------------------

}

add_action( 'widgets_init', create_function('', 'return register_widget("ast_testimonials");') );

//------------CREATES THE CUSTOM POST TYPE-----------------------------------------------

add_action( 'init', 'create_ast_testimonial' );
 
function create_ast_testimonial() {
    $args = array(
                  'description' => 'Testimonial Post Type',
                  'show_ui' => true,
                  'menu_position' => 4,
                  'exclude_from_search' => true,
                  'labels' => array(
                                    'name'=> 'AST Testimonial',
                                    'singular_name' => 'AST Testimonial',
                                    'add_new' => 'Add New',
                                    'add_new_item' => 'Add New',
                                    'edit' => 'Edit Testimonial',
                                    'edit_item' => 'Edit Testimonial',
                                    'new-item' => 'New Testimonial',
                                    'view' => 'View Testimonial',
                                    'view_item' => 'View Testimonial',
                                    'search_items' => 'Search Testimonial',
                                    'not_found' => 'No Testimonial Found',
                                    'not_found_in_trash' => 'No Testimonial Found in Trash',
                                    'parent' => 'Parent Testimonial'
                                   ),
                 'public' => true,
                 'capability_type' => 'post',
                 'hierarchical' => false,
                 'rewrite' => true,
                 'supports' => array('title', 'editor', 'thumbnail', 
                 'excerpt', 'comments')
                 );
                 
  register_post_type( 'ast_testimonial' , $args );
  
 
}

//-------------------MetaBox----------------------------------------
add_action( 'add_meta_boxes', 'ast_meta_type' );

function ast_meta_type(){  add_meta_box("astInformation", "astInformation", "astInformation", "ast_testimonial", "normal", "core");  }

/* HTML for "author" meta box */
function astInformation(){
  global $post;
  
  $custom	= get_post_custom($post->ID);
  $Author	= get_post_meta($post->ID,'Author',true);
  $Website	= get_post_meta($post->ID,'Website',true);
  /*if (isset($_POST['Author']) && $_POST['Website']) {
	 $Author	= $custom["Author"][0];
	 $Website	= $custom["Website"][0];
	 }*/
	 
	 
  ?>
   <div> <p><label></label>Name:&nbsp;</label>
  <input name="Author" value="<?php echo $Author; ?>" /></p></div>
  <div><p><label></label>Website:</label>
  <input name="Website" value="<?php echo $Website; ?>" /></p></div>
  <?php
}


/* Function for saving the "author" value */
add_action('save_post', 'ast_update_details');

function ast_update_details(){
  global $post; 
  
  if (isset($_POST['Author']) && !empty($_POST['Author'])){  update_post_meta($post->ID, "Author", $_POST["Author"]);  }
  if ( isset($_POST['Website']) && !empty($_POST['Website']) ){   update_post_meta($post->ID, "Website", $_POST["Website"]);  }
}
//----------------REGISTER SHORTCODE-------------------------------------------

add_shortcode( 'astlist', 'ast_testimonial_shortcode' );
  
function ast_testimonial_shortcode($atts)
{
	$atts['nop'];
    
	$num_posts		= $atts['nop'];
	$order			= $atts['order'];
    /* Preparing arguments for query post */
    if($order=='rand'){	
		$args = array('post_type' => 'ast_testimonial', 'orderby' => $order,'showposts' => $num_posts, 'post_status' => 'publish');
	}else{
		$args = array( 'post_type' => 'ast_testimonial', 'order' => $order, 'showposts' => $num_posts, 'post_status' => 'publish' );
	}
		$query = new WP_Query( $args );
		$result		 = '<div id="osttestimonials">';
		$result		.= '<div class="carousel-wrap">';
		$result		.= '<ul id="osttestimonial-list1" class="clearfix">';
		while ($query->have_posts() ) : $query->the_post(); 
			$result		.= '<li>';
			$result		.= '<div class="context"><strong>'.get_the_title().'</strong></div>';

			$result		.= '<div><blockquote class="ostquote1"><div class="context">'.get_the_excerpt().'</div></blockquote ></div>';

			$result		.= '<a href=" '. get_post_meta(get_the_ID(),'Website',true).'">'.get_post_meta(get_the_ID(),'Website',true).'</a>';
			$result		.= '<div class="ostcredits">'.get_post_meta(get_the_ID(),'Author',true).'</div>';
			$result		.= '</li>';
		endwhile;
		$result .= '</ul>';
	$result .='</div>';
	wp_reset_query(); 
	return $result;
}


/*------------------------- Adding stylesheet ------------------------ */
    function register_plugin_styles() 
    {
		wp_register_style( 'ast_Testimonials', plugins_url( 'auto-scrolling-testimonials/css/ast_style.css' ) );
        wp_enqueue_style( 'ast_Testimonials' );

		wp_enqueue_script('ast_Testimonials1',plugins_url('auto-scrolling-testimonials/js/jcarousellite_1.0.1c4.js'),'','',true);
        
    }
//---------------------------------------------------------------------

?>
