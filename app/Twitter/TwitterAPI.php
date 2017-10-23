<?php

namespace App\Twitter;

use App\Utility\Connection;

class TwitterAPI {

    /**
     * url for twitter app-only auth
     * @var string
     */
    private $authUrl = 'https://api.twitter.com/oauth2/token';

    /**
     * url for the twitter api
     * @var string
     */
    private $apiUrl = 'https://api.twitter.com/1.1';

    /**
     * twitter api key
     * @var string
     */
    private $apiKey = '';

    /**
     * twitter api secret
     * @var string
     */
    private $apiSecret = '';

    /**
     * twitter access token
     * @var string
     */
    private $accessToken = '';

    /**
     * database connection
     * @var resource
     */
    private $conn;

    /**
     * setup some application variables
     */
    public function __construct()
    {
        $this->apiKey = config('twitter.api_key');
        $this->apiSecret = config('twitter.api_secret');
        $this->conn = new Connection();

        if(!$this->isAuthenticated())
            $this->authenticate();
    }

    public function search($query, $options = [])
    {
        $endpoint = $this->apiUrl.'/search/tweets.json';

        $response = $this->request($endpoint, [
            'data' => [
                'q' => $query,
                'lang' => 'en'
            ],
            'headers' => [
                'Authorization: Bearer '.$this->accessToken
            ],
            'end' => true
        ]);

        return $response;
    }

    /**
     * check database for access token
     * @return boolean if the access token is present or not
     */
    public function isAuthenticated()
    {
        $result = $this->conn->query('select * from config where code = :code limit 1', [
            ':code' => 'twitter_access_token'
        ]);

        $row = $result->fetchAll();

        if(count($row) > 0)
        {
            $this->accessToken = $row[0]['value'];
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * authenticate the twitter application
     * @return [type] [description]
     */
    public function authenticate()
    {
        $bearerToken = urlencode($this->apiKey).':'.urlencode($this->apiSecret);
        $encodedBearerToken = base64_encode($bearerToken);

        $response = $this->request($this->authUrl, [
            'method' => 'post',
            'data' => [
                'grant_type' => 'client_credentials',
            ],
            'headers' => [
                'Authorization: Basic '.$encodedBearerToken,
                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
            ]
        ]);

        if(!isset($response->errors))
        {
            $this->conn->query('delete from config where code = :code', [
                ':code' => 'twitter_access_token'
            ]);

            $this->conn->query('insert into config (code, value) VALUES (:code, :value)', [
                ':code' => 'twitter_access_token',
                ':value' => $response->access_token
            ]);

            $this->accessToken = $response->access_token;

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * sending a curl request
     * @param  string $url     url to send the request to
     * @param  array  $options options to be sent over
     * @return [type]          [description]
     */
    private function request($url, $options = [])
    {
        $defaults = [
            'method' => 'get',
            'data' => [],
            'headers' => [],
            'json' => true
        ];
        $options = array_merge($defaults, $options);
        $options['method'] = strtoupper($options['method']);

        $ch = curl_init();

        if($options['method'] == 'GET' && !empty($options['data']))
        {
            $url .= '?';

            foreach($options['data'] as $key => $value)
            {
                if($value != '')
                    $url .= $key.'='.$value.'&';
            }

            $url = rtrim($url, '&');
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if($options['method'] == 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, true);

            if(!empty($options['data']))
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options['data']));
        }

        if(!empty($options['headers']))
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);

        $response = curl_exec($ch);
        curl_close($ch);

        if($options['json'])
            return json_decode($response);
        else
            return $response;
    }

}
