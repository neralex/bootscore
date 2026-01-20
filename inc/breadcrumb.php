<?php

/**
 * Breadcrumb
 *
 * @package Bootscore
 * @version 6.3.1
 */


// Exit if accessed directly
defined('ABSPATH') || exit;


/**
 * Breadcrumb
 */
if (!function_exists('the_breadcrumb')) :
  function the_breadcrumb() {

    if (is_home()) {
      return;
    }

    echo '<nav aria-label="breadcrumb" class="' . esc_attr(apply_filters('bootscore/class/breadcrumb/nav', 'overflow-x-auto text-nowrap mb-4 mt-2 py-2 px-3 bg-body-tertiary rounded')) . '">' . PHP_EOL;
    echo '<ol class="breadcrumb ' . esc_attr(apply_filters('bootscore/class/breadcrumb/ol', 'flex-nowrap mb-0')) . '">' . PHP_EOL;

    // Home link
    echo '<li class="breadcrumb-item"><a aria-label="' . esc_attr__('Home', 'bootscore') . '" class="' . esc_attr(apply_filters('bootscore/class/breadcrumb/item/link', '')) . '" href="' . esc_url(home_url()) . '">' . wp_kses_post(apply_filters('bootscore/icon/home', '<i class="fa-solid fa-house" aria-hidden="true"></i>')) . '<span class="visually-hidden">' . esc_html__('Home', 'bootscore') . '</span></a></li>' . PHP_EOL;

    // Category archive
    if (is_category()) {
      $current_cat_id = get_queried_object_id();
      if ($current_cat_id) {
        $ancestors = array_reverse(get_ancestors($current_cat_id, 'category'));
        foreach ($ancestors as $ancestor_id) {
          $ancestor = get_category($ancestor_id);
          if ($ancestor && !is_wp_error($ancestor)) {
            echo '<li class="breadcrumb-item"><a class="' . esc_attr(apply_filters('bootscore/class/breadcrumb/item/link', '')) . '" href="' . esc_url(get_term_link($ancestor)) . '">' . esc_html($ancestor->name) . '</a></li>' . PHP_EOL;
          }
        }
        // current category as text only
        echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(single_cat_title('', false)) . '</li>' . PHP_EOL;
      }
    }
    
    // Single post
    elseif (is_single()) {
      $cat_ids = wp_get_post_categories(get_the_ID());
      foreach ($cat_ids as $cat_id) {
        $cat = get_category($cat_id);
        if ($cat && !is_wp_error($cat)) {
          echo '<li class="breadcrumb-item"><a class="' . esc_attr(apply_filters('bootscore/class/breadcrumb/item/link', '')) . '" href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a></li>' . PHP_EOL;
        }
      }
      echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>' . PHP_EOL;
    }
    
    // Pages, handle parent pages and current page
    elseif ( is_page() ) {
      if ( $parent_id = wp_get_post_parent_id( get_the_ID() ) ) {
        $parents = [];
        while ( $parent_id ) {
          $page = get_post($parent_id);
          $parents[] = '<li class="breadcrumb-item"><a class="' . esc_attr(apply_filters('bootscore/class/breadcrumb/item/link', '')) . '" href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
          $parent_id = wp_get_post_parent_id($page->ID);
        }
        $parents = array_reverse($parents); // Reverse the array to display from top-level to current page
        echo implode( '', $parents ); // Output parents without separator
      }

      // Display the current page title (active breadcrumb item)
      echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
    }

    echo '</ol>' . PHP_EOL;
    echo '</nav>' . PHP_EOL;
  }

  add_filter('breadcrumbs', 'breadcrumbs');
endif;
