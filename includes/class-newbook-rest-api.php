<?php

if( ! defined( 'ABSPATH' ) ) exit;

class NewBook_REST_API {

    private $endpoint = '';

    private $api_key  = '';

    private $request  = [];

    private $response = [];

    public function __construct( $api_key = '' ) {
        $settings = get_option( 'newbook_settings' );

        $this->setEndpoint( $settings['endpoint'] );
    }

    public function setEndpoint( $endpoint ) {
        $endpoint = strtolower( $endpoint );

        switch( $endpoint ) {
            case 'au' :
                $this->endpoint = 'https://syncau.newbook.cloud/rest';
                break;
            case 'ap' :
                $this->endpoint = 'https://syncap.newbook.cloud/rest';
                break;
            case 'eu' :
                $this->endpoint = 'https://synceu.newbook.cloud/rest';
                break;
            case 'us' :
                $this->endpoint = 'https://syncus.newbook.cloud/rest';
                break;
            default :
                $this->endpoint = 'https://syncdev.newbook.cloud/rest';
        }
    }

    public function authenticate( $username = '', $password = '' ) {
        $this->response = wp_remote_get( add_query_arg( array( 'request_action' => 'api_keys' ), esc_url( $this->endpoint ) ), apply_filters( 'newbook_rest_api_request_args', array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.1',
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $username . ':' . $password )
            ),
            'blocking'    => true,
        ) ) );
    }

    public function send() {
        $this->response = wp_remote_get( add_query_arg( $this->request, esc_url( $this->endpoint ) ), apply_filters( 'newbook_rest_api_request_args', array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.1',
            'headers'     => array(),
            'blocking'    => true,
        ) ) );
    }

    public function getResponse() {
        return wp_remote_retrieve_body( $this->response );
    }

    public function getResponseCode() {
        return wp_remote_retrieve_response_code( $this->response );
    }

}