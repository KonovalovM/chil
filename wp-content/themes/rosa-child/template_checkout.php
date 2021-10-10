<?php
/**
* Template Name: Checkout
*/
?>

<?php
  function getRestaurantsData() {

    $restaurants = array();
    $restaurants_posts = new WP_Query(
    array(
      'post_status' => 'publish',
      "post_per_page" => -1,
      'post_type' => 'restaurant'
      )
    );

    global $post;
    if ( $restaurants_posts->have_posts() ) {
      while( $restaurants_posts->have_posts()) {
        $restaurants_posts->the_post();

        $olo_id = get_field('olo_id');
        $image = get_field('image');
        $restaurant = array(
          'olo_id' => $olo_id,
          'image' => $image['url']
        );

        array_push($restaurants, $restaurant);
      }
    }
    wp_reset_query();
    return $restaurants;
  }

  function getAssetManifest() {
      global $chilantroCheckoutManifest;
      if(!$chilantroCheckoutManifest) {
          if(!file_exists(dirname(__FILE__).'/checkout/dist/manifest.json')) {
              $chilantroCheckoutManifest = [];
              return $chilantroCheckoutManifest;
          }
          $manifest = file_get_contents(dirname(__FILE__).'/checkout/dist/manifest.json');
          if(!$manifest) {
              $chilantroCheckoutManifest = [];
              return $chilantroCheckoutManifest;
          }
          $chilantroCheckoutManifest = json_decode($manifest, true);
      }
      return $chilantroCheckoutManifest;
  }

  function getAssetversion($file) {
      $manifest = getAssetManifest();
      $found_version = null;
      foreach($manifest as $name => $filedata) {
          if(isset($filedata['file']) and isset($filedata['hash']) and $filedata['file'] === $file) {
              $found_version = $filedata['hash'];
          }
      }
      return $found_version;
  }

  wp_register_script('spreedly', 'https://core.spreedly.com/iframe/iframe-v1.min.js', null, null, true );
  wp_enqueue_script('spreedly');

  wp_register_script('mapbox-gl', 'https://cdnjs.cloudflare.com/ajax/libs/mapbox-gl/1.4.0/mapbox-gl.js', null, null, true );
  wp_enqueue_script('mapbox-gl');

  wp_enqueue_script('chilantro_checkout_script', get_theme_file_uri('checkout/dist/app.bundle.js'), array('spreedly', 'mapbox-gl'), getAssetVersion('app.bundle.js'), true);
  wp_enqueue_style('chilantro_checkout_style', get_theme_file_uri('checkout/dist/style.css'), array('wpgrade-main-style'), getAssetVersion('style.css'));
?>

<?php get_header();?>
      <div id="content"></div>
    <!--</div>-->
    <!-- .page -->

  <!-- Element used to avoid script error -->
  <div class="btn--top" style="display:none;"></div>

  <script type="application/json" id="restaurants-image-json">
      {
        "restaurants": <?php echo json_encode(getRestaurantsData()); ?>
      }
  </script>

<?php get_footer();?>
