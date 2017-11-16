<?php

if( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="newbook-category">
    <div class="category-images">
        <?php
            if( ! empty( $category['images'] ) ) {
                for( $i = 0; $i < sizeof( $category['images'] ); $i++ ) :
                ?>

                <input type="radio" name="nb-images[<?php print (int) $category['category_id']; ?>]" id="cat-image-<?php print (int) $category['category_id'] . '-' . $i; ?>" value="<?php print $i; ?>" style="display: none;" <?php checked( $i, 0 ); ?> />
                <div class="category-image" data-name="<?php print esc_attr( $category['images'][$i]['image_type_name'] ); ?>" data-type-id="<?php print esc_attr( $category['images'][$i]['image_type_id'] ); ?>">
                    <img src="<?php print esc_url( $category['images'][$i]['image_url'] ); ?>" alt="<?php print esc_attr( $category['images'][$i]['image_type_name'] ); ?>" />
                    <div class="images-nav">
                        <?php printf( '<label for="cat-image-%d-%d" class="nav-next"></label>', (int) $category['category_id'], ( $i === sizeof( $category['images'] ) - 1 ) ? 0 : $i + 1 ); ?>
                        <?php printf( '<label for="cat-image-%d-%d" class="nav-prev"></label>', (int) $category['category_id'], ( $i === 0 ) ? sizeof( $category['images'] ) - 1 : $i - 1 ); ?>
                    </div>
                </div>

                <?php
                endfor;
            }
        ?>
    </div>
    <div class="category-content">
        <div class="category-name"><h2><?php print newbook_get_category_field( 'category_name', $category ); ?></h2></div>
        <div class="category-av">
            <div class="category-max">
                <span class="dashicons dashicons-admin-users max-anchor" data-max="<?php print (int) newbook_get_category_field( 'category_max_combined', $category ); ?>"></span>
                <div class="category-max-overlay">
                    <?php printf( '<div class="max-l">%s: <span>%d</span></div>', __( 'Maximum Adults', 'newbook' ), (int) newbook_get_category_field( 'category_max_adults', $category ) ); ?>
                    <?php printf( '<div class="max-l">%s: <span>%d</span></div>', __( 'Maximum Children', 'newbook' ), (int) newbook_get_category_field( 'category_max_children', $category ) ); ?>
                    <?php printf( '<div class="max-l">%s: <span>%d</span></div>', __( 'Maximum Infants', 'newbook' ), (int) newbook_get_category_field( 'category_max_infants', $category ) ); ?>
                    <?php printf( '<div class="max-l">%s: <span>%d</span></div>', __( 'Maximum Animals', 'newbook' ), (int) newbook_get_category_field( 'category_max_animals', $category ) ); ?>
                    <?php printf( '<div class="max-l">%s: <span>%d</span></div>', __( 'Maximum Total Occupants', 'newbook' ), (int) newbook_get_category_field( 'category_max_combined', $category ) ); ?>
                </div>
            </div>
        </div>
        <div class="category-description">
            <?php newbook_get_category_description( $category ); ?>
        </div>
        <div class="category-pricing">
            <?php print newbook_get_category_price_html( $category ); ?>

            <div class="booking-button"><?php print newbook_get_category_booking_button( $category ); ?></div>
        </div>
    </div>
</div>