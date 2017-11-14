<?php

if( ! defined( 'ABSPATH' ) ) exit;

global $newbook_ap;
?>

<div class="newbook-booking-availability">

    <?php if( ! empty( $newbook_ap ) ) : ?>

        <?php
            foreach( $newbook_ap as $category ) :
        
                include NEWBOOK_DIR_PATH . 'templates/newbook-content-category.php';

            endforeach;
        ?>
    
    <?php else : ?>

    <p class="newbook-no-results"><?php _e( 'No results found, please try a different search query.', 'newbook' ); ?></p>

    <?php endif; ?>

</div>