<?php
    require __DIR__ . '/vendor/autoload.php';

    use GuzzleHttp\Client;

    class Aldoc {
        /**
         * Get the client
         * @return \GuzzleHttp\Client
         */
        public static function get_client()
        {
            $client = new Client( array(
                'base_uri' => 'https://hap-psws.aldoc.eu/api/',
                'timeout'  => 2.0,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ) );

            return $client;
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
         * ========================
         * START APPLICATION LIST API
         * ========================
         */

        /**
         * Gets the list of vehicles where an article fits onto
         * @param string $reference (required) The reference of the article where the vehicle list is retrieved for
         * @param int $supcode (required) The Aldoc supplier code
         * @param int $pagestart (optional) The start record to return. 1 Is the first
         * @param int $pagesize (optional) The maximum number of records to return. Default is 50
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_application_list( $reference, $supcode, $pagestart = 1, $pagesize = 50, $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'ApplicationList' . $reference . '/' . $supcode, [
                'query' => [
                    'pagestart' => $pagestart,
                    'pagesize'  => $pagesize,
                    'language'  => $language,
                    'message'   => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END APPLICATION LIST API
         * ========================
         */

        /**
         * ========================
         * START ARTICLES API
         * ========================
         */

        /**
         * Gets the parts for the given search parameter. This service supports paging
         * @param string $search (required) The search term to search for
         * @param int $pagestart (optional) The start record to return. 1 Is the first
         * @param int $pagesize (optional) The maximum number of records to return. Default is 50
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_articles( $search, $pagestart = 1, $pagesize = 50, $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'Articles/' . $search, [
                'query' => [
                    'pagestart' => $pagestart,
                    'pagesize'  => $pagesize,
                    'language'  => $language,
                    'message'   => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END ARTICLES API
         * ========================
         */

        /**
         * ========================
         * START COMMERCIAL EXTENSIONS API
         * ========================
         */

        /**
         * Gets commercial descriptions for parts
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_commercial_extensions( $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'meta/CommercialExtensions', [
                'query' => [
                    'language' => $language,
                    'message'  => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END COMMERCIAL EXTENSIONS API
         * ========================
         */

        /**
         * ========================
         * START INFO API
         * ========================
         */

        /**
         * Gets standard information about the use of these services
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_info( $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'Info', [
                'query' => [
                    'message' => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END INFO API
         * ========================
         */

        /**
         * ========================
         * START MENU API
         * ========================
         */

        /**
         * Gets menu headings in a specific language. Also a message can be passed
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_menu( $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'Menu', [
                'query' => [
                    'language' => $language,
                    'message'  => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END MENU API
         * ========================
         */

        /**
         * ========================
         * START MENU PARTS API
         * ========================
         */

        /**
         * Gets the menuparts for one menuitem or the entire menu in a given language. A message can be given. When the menuitem is 0, the entire menu is listed. If typecode > 0 only the items that have parts for that typecode will be shown
         * @param int $menucode (required) The code of the menuitem. The first item is number 1. 0 Means all menuitems -> the entire menu
         * @param int $typecode (optional) > 0 only the items that have parts for that typecode will be shown
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         */
        public static function get_menu_parts( $menucode, $typecode = 0, $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'Menuparts/' . $menucode, [
                'query' => [
                    'typecode' => $typecode,
                    'language' => $language,
                    'message'  => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END MENU PARTS API
         * ========================
         */

        /**
         * ========================
         * START PARTNAME EXTENSIONS API
         * ========================
         */

        /**
         * Gets additional information of the part description if available
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_partname_extensions( $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'meta/PartnameExtensions', [
                'query' => [
                    'language' => $language,
                    'message'  => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END PARTNAME EXTENSIONS API
         * ========================
         */

        /**
         * ========================
         * START PARTS API
         * ========================
         */

        /**
         * Gets the parts for one menuitem and one typecode and optionally a partcode in a given language. A message can be given
         * @param int $menucode (required) The menucode for which the parts will be shown
         * @param int $typecode (required) The Aldoc car type
         * @param int $partcode (optional) The Aldoc part code
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_parts( $menucode, $typecode, $partcode = 0, $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'Parts/' . $menucode . '/' . $typecode, [
                'query' => [
                    'partcode' => $partcode,
                    'language' => $language,
                    'message'  => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END PARTS API
         * ========================
         */

        /**
         * ========================
         * START POSITIONS API
         * ========================
         */

        /**
         * Gets position information for parts where needed
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_positions( $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'meta/Positions', [
                'query' => [
                    'language' => $language,
                    'message'  => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END POSITIONS API
         * ========================
         */

        /**
         * ========================
         * START STATUSES API
         * ========================
         */

        /**
         * Gets information (for internal use mostly) about availability from the supplier
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_statuses( $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'meta/Statuses', [
                'query' => [
                    'language' => $language,
                    'message'  => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END STATUSES API
         * ========================
         */

        /**
         * ========================
         * START SUB PARTS API
         * ========================
         */

        /**
         * Get the articles that are a part of a parent article. The parent is added as first element in the result set
         * @param string $parentRef (required) Reference of the parent article
         * @param int $supcode (required) Supplier code of the parent article
         * @param int $partcode (required) Part code of the parent article
         * @param int $typecode (optional) Aldoc type code
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_sub_parts( $parentRef, $supcode, $partcode, $typecode = 0, $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'Subparts/' . $parentRef . '/' . $supcode . '/' . $partcode, [
                'query' => [
                    'typecode' => $typecode,
                    'language' => $language,
                    'message'  => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END SUB PARTS API
         * ========================
         */

        /**
         * ========================
         * START SUPPLIERS API
         * ========================
         */

        /**
         * Gets supplier IDs and names
         * @param string $language (optional) 2 Digit language code, e.g. nl
         * @param string $message (optional) This message will be in the returned result set
         * @return array Response
         */
        public static function get_suppliers( $language = 'nl', $message = '' )
        {
            $client = self::get_client();

            $response = $client->request( 'GET', 'meta/Suppliers', [
                'query' => [
                    'language' => $language,
                    'message'  => $message,
                ],
            ] );

            return self::format_response( $response );
        }

        /**
         * ========================
         * END SUPPLIERS API
         * ========================
         */
    }