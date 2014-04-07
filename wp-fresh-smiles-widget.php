<?php

//WP Fresh Smiles Satisfaction Widget
add_action( 'widgets_init', 'wfs_smiles_widget' );
function wfs_smiles_widget() {
  register_widget( 'wfs_smiles_widget' );
}

class wfs_smiles_widget extends WP_Widget {
  function wfs_smiles_widget() {
    $widget_ops = array( 'classname' => 'wfs-smiles', 'description' => __('A widget that displays your Smiles.', 'wfs-smiles') );
    $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'wfs-smiles' );
    $this->WP_Widget( 'wfs-smiles', __('Fresh Smiles Widget', 'wfs-smiles'), $widget_ops, $control_ops );
  }

  function widget( $args, $instance ) {
    extract( $args );

    //Our variables from the widget settings.
    $title = apply_filters('widget_title', $instance['title'] );

    echo $before_widget;

    // Display the widget title
    if ( $title )
      echo $before_title . $title . $after_title;

    $wfs_content = get_transient( 'wfs-content' );
    if ( empty( $wfs_content ) ) {
      $ratings = $wpdb->get_results(
        "
        SELECT count(1) as count, survey_rating
        FROM (
          SELECT survey_rating
          FROM $wpdb->freshsmiles
          WHERE NOT survey_rating = 'NULL'
          ORDER BY survey_updated_at
          DESC LIMIT 100
        ) t GROUP BY survey_rating;
        "
      );

      foreach ( $ratings as $rating )
        if ( $rating->survey_rating == '1' ) {
          echo '<div class="overall">';
          echo '<i class="overall-score icon-smile happy"></i> <label class="label happy-score">' . $rating->count . '% said AWESOME!</label>';
          echo '</div>';
        }
        else if ( $rating->survey_rating == '2' ) {
          echo '<ul class="small-block-grid-2">';
          echo '<li class="rating-text"><i class="icon-2x icon-meh meh"></i> ' . $rating->count . ' said just OK</li>';
        }
        else if ( $rating->survey_rating == '3' ) {
          echo '<li class="rating-text"><i class="icon-2x icon-frown unhappy"></i> ' . $rating->count . ' said not good</li>';
          echo '</ul>';
        }
        else {
          //Do Nothing
        }
      }

      //set_transient( 'wfs-content', $wfs_content, HOUR_IN_SECONDS );
    }

    echo $wfs_content;
    echo $after_widget;
  }

  //Update the widget
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;

    //Strip tags from title and name to remove HTML
    $instance['title'] = strip_tags( $new_instance['title'] );
    return $instance;
  }

  function form( $instance ) {
    //Set up some default widget settings.
    $defaults = array( 'title' => __('Fresh Smiles', 'title') );
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'title'); ?></label>
      <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
    </p>
  <?php }
}
