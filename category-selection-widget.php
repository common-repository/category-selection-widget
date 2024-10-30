<?php
/*
Plugin Name: Category Selection Widget
Plugin URI: 
Description: Create category widget can re-select Posts when click on check box
Version: 0.1
Author: Quang Pham
Author URI: 
License: GPL
*/

/**
 * Adds CSW_Widget widget.
 */
class CSW_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'csw_widget', // Base ID
      __( 'Category Selection Widget', 'text_domain' ), // Name
      array( 'description' => __( 'Create category widget can re-select Posts when click on check box', 'text_domain' ), ) // Args
    );
    wp_register_script("csw-select", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/js/selection.js"), array( 'jquery' ), NULL, true);
    wp_enqueue_script( "csw-select");

    $customs = array( 'home_url' => get_home_url() );
    wp_localize_script( 'csw-select', 'customs', $customs );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    echo $args['before_widget'];
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }

    if (! empty($instance['exclude'])) {
      $exclude = explode(',', $instance['exclude']);
    } else {
      $exclude = array();
    }

    global $wp;
    $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
    $pos = strpos($current_url, "category_name=");
    $categoryArr = array();
    if ($pos > 0) {
      $categoryStr = substr($current_url, $pos+strlen("category_name="), strlen($current_url)-1);
      $categoryArr = explode('%2B', $categoryStr);
    }
    
    $categories = get_categories(array('orderby' => 'id', 'exclude' => $exclude, 'parent' => 0));
    foreach ($categories as $key => $category) {
      $this->printCategory($category, $exclude, $categoryArr);
    }

    echo $args['after_widget'];
  }

  public function printCategory($category, $exclude, $categoryArr) {
    $checked = "";
    if (in_array($category->slug, $categoryArr)) {
      $checked = "checked";
    }

    echo '<input type="checkbox" name="csw_category[]" ' . $checked . ' value="'. $category->slug .'">'. $category->name .'<br>';
    $childs = get_categories(array('orderby' => 'name', 'exclude' => $exclude, 'parent' => $category->term_id));
    echo '<div style="margin-left:10px;">';
    foreach ($childs as $key => $child) {
      $this->printCategory($child, $exclude, $categoryArr);
    }
    echo "</div>";
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Category Selection Widget', 'text_domain' );
    $exclude = ! empty( $instance['exclude'] ) ? $instance['exclude'] : '';
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php _e( 'Exclude:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" type="text" value="<?php echo esc_attr( $exclude ); ?>">
    <div>Enter a comma seperated category ID.<br>
      Ex: 2,5 (This widget will display all of categories except these categories)
    </div>
    </p>
    <?php 
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['exclude'] = ( ! empty( $new_instance['exclude'] ) ) ? strip_tags( $new_instance['exclude'] ) : '';

    return $instance;
  }

}

// register CSW_Widget widget
function register_csw_widget() {
    register_widget( 'CSW_Widget' );
}
add_action( 'widgets_init', 'register_csw_widget' );







?>

