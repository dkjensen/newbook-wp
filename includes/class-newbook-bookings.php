<?php

class NewBook_Bookings extends NewBook_REST_API {
    
    protected function availabilityPricing( $period_from = '', $period_to = '', $adults = 1, $children = 0, $infants = 0, $animals = 0 ) {
        $this->setRequest( array(
            'request_action'    => 'bookings_availability_pricing', 
            'period_from'       => $this->getDate( $period_from ),
            'period_to'         => $this->getDate( $period_to ),
            'adults'            => (int) $adults,
            'children'          => (int) $children,
            'infants'           => (int) $infants,
            'animals'           => (int) $animals,
            'daily_mode'        => 'true',
        ) );
    }
}