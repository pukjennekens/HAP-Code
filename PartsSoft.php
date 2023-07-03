<?php
    require __DIR__ . '/vendor/autoload.php';

    use GuzzleHttp\Client;
    use Phpfastcache\Helper\Psr16Adapter;

    class PartsSoft
    {
        /**
         * @var string $email Email for PartsSoft
         */
        private static $email;

        /**
         * @var string $password Password for PartsSoft
         */
        private static $password;

        /**
         * @var string TOKEN_KEY Key for the token in the cache
         */
        const TOKEN_KEY = 'partssoft_token';

        /**
         * @var string CACHE_DRIVER Driver for the cache
         */
        const CACHE_DRIVER = 'Files';

        /**
         * @var \Phpfastcache\Helper\Psr16Adapter $cache Cache
         */
        private static $cache;

        /**
         * Initialize the class
         * @return void
         */
        public static function init()
        {
            self::$cache = new Psr16Adapter( self::CACHE_DRIVER );
        }

        /**
         * Set the username and password for PartsSoft
         * @param string $email Email for PartsSoft
         * @param string $password Password for PartsSoft
         * @return void
         */
        public static function set_credentials( $email, $password )
        {
            self::$email    = $email;
            self::$password = $password;
        }

        /**
         * Get the client
         * @return \GuzzleHttp\Client
         */
        public static function get_client()
        {
            $token = self::get_token();

            $client = new Client([
                'base_uri' => 'https://con.parts-soft.net/api/',
                'headers' => [
                    'accept' => 'application/json',
                    'token'  => $token,
                ],
            ]);

            return $client;
        }

        /**
         * Get the token
         * @return string
         */
        public static function get_token()
        {
            $token = self::$cache->get( self::TOKEN_KEY );

            if ( ! $token )
                $token = self::get_new_token();

            return $token;
        }

        /**
         * Format the response
         * @param \Psr\Http\Message\ResponseInterface $response
         * @return array
         */
        public static function format_response( $response )
        {
            $body = $response->getBody();
            $body = json_decode( $body, true );

            return $body;
        }

        /**
         * Get a new token
         * @return string
         */
        private static function get_new_token()
        {
            $client = new Client([
                'base_uri' => 'https://con.parts-soft.net/api/',
                'headers' => [
                    'accept'       => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $response = $client->request( 'POST', 'login', array(
                'form_params' => array(
                    'email'    => self::$email,
                    'password' => self::$password,
                ),
            ) );

            $response = self::format_response( $response );
            
            if ( isset( $response['authToken'] ) )
                self::$cache->set( self::TOKEN_KEY, $response['authToken'], 3600 );

            return $response['authToken'];
        }

        /**
         * ========================
         * START ORDERS API
         * ========================
         */

        /**
         * Get the order list
         * @param int $relation_id Relation ID
         * @param string $start_date Start date, all orders after this date. Date is in ISO 8601 format or DATE_ATOM
         * @param string $end_date End date, all orders before this date. Date is in ISO 8601 format or DATE_ATOM
         * @return array Response
         */
        public static function get_order_list( $relation_id = 0, $start_date = '', $end_date = '' )
        {
            $client   = self::get_client();
            $response = $client->request( 'GET', 'order-list', array(
                'params' => array(
                    'relationId' => $relation_id,
                    'dateStart'  => $start_date,
                    'dateEnd'    => $end_date,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * Place an order in PartsSoft
         * @param string $products JSON array of products as a string. Example: [{"erpCode":"R18508655","quantity":2,"netPrice":"25.54","grossPrice":"25.54"}]
         * @param int $relation_id Relation ID
         * @param string $order_note Order note
         * @param string $invoice_address Invoice address
         * @param string|boolean $shipping_address Shipping address
         * @param string $zip_code Zip code
         * @param int $phone Phone number
         * @param string $currency Currency in ISO 4217 format. Example: EUR, docs: https://en.wikipedia.org/wiki/ISO_4217
         * @return array Response
         */
        public static function place_order( $products, $relation_id, $order_note = '', $invoice_address, $shipping_address = false, $zip_code = '', $phone = 0, $currency = 'EUR' )
        {
            $client   = self::get_client();
            $response = $client->request( 'POST', 'order', array(
                'form_params' => array(
                    'products'        => $products,
                    'relationId'      => $relation_id,
                    'note'            => $order_note,
                    'invoiceAddress'  => $invoice_address,
                    'shippingAddress' => $shipping_address,
                    'zipCode'         => $zip_code,
                    'phone'           => $phone,
                    'currency'        => $currency,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END ORDERS API
         * ========================
         */

        /**
         * ========================
         * START STOCK API
         * ========================
         */

         /**
          * Get the stock list for a product or range of products
          * @param string $data JSON array of products as a string. Example: {"products":[{"erpCode":"R18508655"}],"relationId":"10000","currencyCode":"EUR"}
          * @return array Response
          */
        public static function get_stock_list( $data )
        {
            $client   = self::get_client();
            $response = $client->request( 'POST', 'stock', array(
                'form_params' => array(
                    'data' => $data,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END STOCK API
         * ========================
         */

        /**
         * ========================
         * START PRICE API
         * ========================
         */

        /**
         * Get the price list for a product or range of products including the stock per location and availability
         * @param string $data JSON array of products as a string. Example: {"products":[{"erpCode":"R18508655"}],"relationId":"10000","currencyCode":"EUR"}
         * @return array Response
         */
        public static function get_stock_price( $data )
        {
            $client   = self::get_client();
            $response = $client->request( 'POST', 'stock-price', array(
                'form_params' => array(
                    'data' => $data,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * Get the price list for a product or range of products without the stock
         * @param string $data JSON array of products as a string. Example: {"products":[{"erpCode":"R18508655"}],"relationId":"10000","currencyCode":"EUR"}
         * @return array Response
         */
        public static function get_price( $data )
        {
            $client   = self::get_client();
            $response = $client->request( 'POST', 'price', array(
                'form_params' => array(
                    'data' => $data,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END PRICE API
         * ========================
         */

        /**
         * ========================
         * START CUSTOMER API
         * ========================
         */

        /**
         * Create a relation
         * @param string $name Name of the relation
         * @param string $first_name First name of the relation
         * @param string $email Email of the relation
         * @param string $zip_code Zip code of the relation
         * @param string $city City of the relation
         * @param string $country Country code in ISO 3166-1 alpha-2 format. Example: NL, docs: https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
         * @param string $language Language code in ISO 639-1 format. Example: nl, docs: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
         * @param int $phone Phone number of the relation
         * @return array Response
         */
        public static function create_relation( $name, $first_name, $email, $zip_code, $city, $country, $language, $phone = 0 )
        {
            $client   = self::get_client();
            $response = $client->request( 'POST', 'create-relation', array(
                'form_params' => array(
                    'name'      => $name,
                    'firstName' => $first_name,
                    'email'     => $email,
                    'zipCode'   => $zip_code,
                    'city'      => $city,
                    'country'   => $country,
                    'language'  => $language,
                    'phone'     => $phone,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * Get the customer list
         * @return array Response
         */
        public static function get_customer_list()
        {
            $client   = self::get_client();
            $response = $client->request( 'GET', 'customer-list' );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END CUSTOMER API
         * ========================
         */

        /**
         * ========================
         * START DOCUMENT API
         * ========================
         */

        /**
         * Get the document list
         * @param int $page Page number
         * @param int $size Page size
         * @param int $relation_id Relation ID
         * @param string $start_date Start date, all documents after this date. Date is in ISO 8601 format or DATE_ATOM
         * @param string $end_date End date, all documents before this date. Date is in ISO 8601 format or DATE_ATOM
         * @param string $document_number Document number, example: 2023030103Bla0000020
         * @return array Response
         */
        public static function get_document_list( $page = 1, $size = 10, $relation_id = 0, $start_date = '', $end_date = '', $document_number = '' )
        {
            $client   = self::get_client();
            $response = $client->request( 'GET', 'document-list', array(
                'form_params' => array(
                    'page'           => $page,
                    'size'           => $size,
                    'relationId'     => $relation_id,
                    'startDate'      => $start_date,
                    'endDate'        => $end_date,
                    'documentNumber' => $document_number,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END DOCUMENT API
         * ========================
         */
        
        /**
         * ========================
         * START PRODUCT API
         * ========================
         */

        /**
         * Get the product list
         * @param int $page Page number
         * @param int $size Page size
         * @param string $language Language code in ISO 639-1 format. Example: nl, docs: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
         * @return array Response
         */
        public static function get_product_list( $page = 1, $size = 10, $language = 'nl' )
        {
            $client   = self::get_client();
            $response = $client->request( 'GET', 'product-list', array(
                'form_params' => array(
                    'page'     => $page,
                    'size'     => $size,
                    'language' => $language,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * Get the detailed information of a product or a range of products
         * @param string $erp_codes Commaseparated list of ERP codes. Example: R12700,R12702
         * @return array Response
         */
        public static function get_product_info( $erp_codes )
        {
            $client   = self::get_client();
            $response = $client->request( 'GET', 'product-detail', array(
                'form_params' => array(
                    'erpCodes' => $erp_codes,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * Search for products in a catalog
         * @param string $catalog Catalog name, example: aldoc
         * @param string $products JSON array of products as a string. Example: [{"reference":"GDB1330","brand":14}]
         * @return array Response
         */
        public static function search_products( $catalog, $products )
        {
            $client   = self::get_client();
            $response = $client->request( 'POST', 'product-search', array(
                'form_params' => array(
                    'catalog'  => $catalog,
                    'products' => $products,
                ),
            ) );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END PRODUCT API
         * ========================
         */
    }