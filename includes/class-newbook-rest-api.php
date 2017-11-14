<?php

if( ! defined( 'ABSPATH' ) ) exit;

class NewBook_REST_API {

    private $endpoint = '';

    private $api_key  = '';

    private $request  = [];

    private $response = [];

    private $auth = '';

    private $date_format = 'Y-m-d';

    public function __construct() {
        $this->setEndpoint( newbook_get_endpoint() );
        $this->setUserAuth( newbook_get_auth() );
        $this->setKey( newbook_get_api_key() );
        $this->setDateFormat( newbook_get_date_format() );
    }

    protected function setEndpoint( $endpoint ) {
        $endpoint = strtoupper( $endpoint );

        switch( $endpoint ) {
            case 'AU' :
                $this->endpoint = 'https://syncau.newbook.cloud/rest';
                break;
            case 'AP' :
                $this->endpoint = 'https://syncap.newbook.cloud/rest';
                break;
            case 'EU' :
                $this->endpoint = 'https://synceu.newbook.cloud/rest';
                break;
            case 'US' :
                $this->endpoint = 'https://syncus.newbook.cloud/rest';
                break;
            default :
                $this->endpoint = 'https://syncdev.newbook.cloud/rest';
        }
    }

    protected function setUserAuth( $auth = '' ) {
        $this->auth = $auth;
    }

    protected function getUserAuth() {
        return $this->auth;
    }

    protected function setKey( $key = '' ) {
        $this->api_key = $key;
    }

    protected function getKey() {
        return $this->api_key;
    }

    protected function setDateFormat( $format = '' ) {
        $this->date_format = $format;
    }

    protected function getDateFormat() {
        return $this->date_format;
    }

    protected function authenticate( $username = '', $password = '' ) {
        $this->response = wp_remote_post( esc_url( $this->endpoint ), apply_filters( 'newbook_rest_api_request_args', array(
            'timeout'     => 15,
            'redirection' => 5,
            'httpversion' => '1.0',
            'headers'     => array(
                'Authorization' => 'Basic ' . $this->getUserAuth()
            ),
            'body'        => json_encode( array( 'request_action' => 'api_keys' ) ),
            'blocking'    => true,
        ) ) );
    }

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

    protected function send() {
        $this->response = wp_remote_post( esc_url( $this->endpoint ), apply_filters( 'newbook_rest_api_request_args', array(
            'timeout'     => 15,
            'redirection' => 5,
            'httpversion' => '1.0',
            'headers'     => array(
                'Authorization' => 'Basic ' . $this->getUserAuth()
            ),
            'body'        => json_encode( array_merge( $this->request, array( 'api_key' => $this->api_key ) ) ),
            'blocking'    => true,
        ), $this->getRequest() ) );
    }

    protected function getDate( $date ) {
        $datetime = DateTime::createFromFormat( $this->getDateFormat(), $date );

        if( ! $datetime ) {
            return date( 'Y-m-d', strtotime( $date ) );
        }

        return $datetime->format( 'Y-m-d' );
    }

    protected function getResponse() {
        return json_decode( wp_remote_retrieve_body( $this->response ), true );
    }

    protected function getResponseRaw() {
        return wp_remote_retrieve_body( $this->response );
    }

    protected function getResponseCode() {
        return wp_remote_retrieve_response_code( $this->response );
    }

    protected function getMessage() {
        $response = $this->getResponse();
        
        return $response['message'];
    }

    protected function setRequest( $request ) {
        $this->request = array_filter( (array) $request );
    }

    protected function getRequest() {
        return json_encode( array_merge( $this->request, array( 'api_key' => $this->api_key ) ) );
    }

    protected function getData() {
        $response = $this->getResponse();

        return $response['data'];
    }

    /**
     * Did the request succeed
     *
     * @return boolean
     */
    protected function isSuccess() {
        $response = $this->getResponse();

        if( $response['success'] === 'true' || $response['success'] === true ) {
            return true;
        }

        return false;
    }

    /**
     * Chain methods that do not start with get or is
     *
     * @param  string $method
     * @param  int    $args
     * @return mixed
     */
    public function __call( $method, $args ) {
        if( substr( $method, 0, 3 ) !== 'get' && substr( $method, 0, 2 ) !== 'is' ) {
            call_user_func_array( [ $this, $method ], $args );
            return $this;
        }

        return call_user_func_array( [ $this, $method ], $args );
    }

}