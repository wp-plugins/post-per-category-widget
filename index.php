<?php
/*
Plugin Name: Post Per Category
Plugin URI: https://wordpress.org/plugins/post-per-category-widget/
Description: Display Post Per Category with thumbnail Widget.
Version: 1.1
Author: Muhammad Yasir Khan
License:--GNU License
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  

See the GNU General Public License for more details:
http://www.gnu.org/licenses/gpl.txt

*/

    add_action('wp_head', 'my_pds');
                 function my_pds(){
                 wp_register_style('pd_css', plugins_url('style.css',__FILE__ ));
                 wp_enqueue_style('pd_css');
                 }
  // use widgets_init action hook to execute custom function
add_action( 'widgets_init', 'latest_post_widgets' );
//register our widget
function latest_post_widgets() {
register_widget( 'latestpost_widget' );
}
//boj_widget_my_info class
class latestpost_widget extends WP_Widget {
//process the new widget
function latestpost_widget() {
$widget_ops = array(
'classname' =>'latestpost_widget_class',
'description' => 'Display a latest posts from each category.');
$this-> WP_Widget( 'latestpost_widget', 'Latest Post from Each Category Widget',$widget_ops );
}
//build the widget settings form
function form($instance) {
$instance = wp_parse_args($instance);
$title = $instance['title'];
$cat = $instance['cat'];
$thumb = $instance['thumb'];
?>
<p>Title: <input class="widefat" name="<?php echo $this-> get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?> " /> </p>
<p>Hide Category by ID:<input class="widefat" name="<?php echo $this-> get_field_name('cat'); ?>" type="text" value="<?php echo esc_attr( $cat ); ?>" /> </p>
<p>Enter post thumbnail widthxheight:<input class="widefat" name="<?php echo $this-> get_field_name('thumb'); ?>" type="text" value="<?php echo esc_attr( $thumb ); ?>" /> </p>

<?php
}
//save the widget settings
function update($new_instance, $old_instance) {
$instance = $old_instance;
$instance['title'] = strip_tags($new_instance['title'] );
$instance['cat'] = strip_tags($new_instance['cat']);
$instance['thumb'] = strip_tags($new_instance['thumb']);
return $instance;
}
//display the widget
function widget($args,$instance) {
extract($args, EXTR_SKIP);

$title=$instance['title'];
$cat = $instance['cat'];
$thumb = $instance['thumb'];
 $args=array('orderby'=>'name','type'=>'post','exclude'=>$cat,'taxonomy'=> 'category');
                       $category=get_categories($args);
//print_r($category);
                       foreach($category as $categories){
                         $cid=$categories->term_id;
						 $cat_link = get_category_link( $cid );
                       $out_cat='<a href='.esc_url( $cat_link ).'>'.$categories->name."</a><br>";
					   

                           //$out_posts.='<li><a href="'.get_category_link($categories->cat_ID).'">'.$categories->name.'</a></li>';
                            $out_posts1.=$categories->description;

                            query_posts("cat=$cid&posts_per_page=1");
                             if ( have_posts() ) : while (have_posts() ) : the_post();

                            $p_title=get_the_title();
                            $p_link=get_permalink();
							$t=explode('x',$thumb);
                            $thumbs= wp_get_attachment_image(get_post_thumbnail_id($post->ID), array($t[0],$t[1]),true);
	                    $out_posts.=<<<HTML
                            <li>
                            <h5>{$out_cat}</h5>
                            <a href="{$p_link}" title="$p_title"><div style="">{$thumbs}</div>{$p_title}</a>
                            </li>
HTML;

                            endwhile;
                            endif;
                           // Reset Query
                            wp_reset_query();
                           }

echo $before_widget;
$title = apply_filters('widget_title', $instance['title'] );
$cat = empty( $instance['cat'] ) ? ' &nbsp;' : $instance['cat'];
if ( !empty( $title ) ) { echo $before_title . $title . $after_title;};

echo '<div class="pd"><ul>'.$out_posts.'</div></ul>';
echo $after_widget;
}
}

?>