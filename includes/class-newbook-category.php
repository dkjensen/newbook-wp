<?php

class NewBook_Category extends NewBook_REST_API {
    
    protected function categoryList( $category_id = '', $type_id = '' ) {
        $this->setRequest( array(
            'request_action'    => 'accommodation_category_list', 
            'category_id'       => $category_id,
            'type_id'           => $type_id,
        ) );
    }
}