<?php

namespace App\Twitter;

use App\Utility\Connection;

class TwitterStorage {

    /**
     * twitterAPI class
     * @var class
     */
    private $twitterAPI;

    /**
     * database connection class
     * @var class
     */
    private $conn;

    /**
     * words to exclude from storage
     * @var array
     */
    private $exclude = [
        'if',
        'the',
        'a',
        'i',
        'rt',
        'and',
        'to',
        'at',
        'in',
        'you',
        'with',
        'of'
    ];

    /**
     * patterns to remove from words
     * @var array
     */
    private $replace = [
        "\n",
        '"',
        ',',
        ' ',
        '“',
        '.',
        '...',
        '?',
        '!',
        '…',
    ];

    /**
     * setup some variables
     */
    public function __construct()
    {
        $this->twitterAPI = new TwitterAPI;
        $this->conn = new Connection;
    }

    /**
     * search tweets and store the results
     * @param  string $query search parameter
     * @return object        search result statuses
     */
    public function searchAndStore($query)
    {
        $result = $this->twitterAPI->search($query);

        if(!isset($result->errors))
        {
            foreach($result->statuses as $status)
            {
                if(!$this->tweetExists($status->id))
                {
                    $this->conn->query('insert into tweets (tweet_id, tweet, status) values (:tweet_id, :tweet, :status)', [
                        ':tweet_id' => $status->id,
                        ':tweet' => $status->text,
                        ':status' => 0
                    ]);
                }
            }

            return $result->statuses;
        }
        else
        {
            return false;
        }
    }

    /**
     * pull statistics from stored tweets
     * TODO use combination of status column and words db table
     * to "cache" the word count each time stats are pulled
     * did not have enough time to implement this feature
     * @return array an array of hashtags and popular words
     */
    public function pullStats($html = false)
    {
        $result = $this->conn->query('select * from tweets');

        $popular = [];
        $hashtags = [];
        foreach($result as $row)
        {
            $words = explode(' ', $row['tweet']);

            foreach($words as $word)
            {
                $word = str_replace($this->replace, '', $word);
                $word = strtolower($word);

                if($word != '' && !in_array($word, $this->exclude) && strlen($word) > 1)
                {
                    if($word[0] == '#')
                    {
                        if(!isset($hashtags[$word]))
                        {
                            $hashtags[$word] = 1;
                        }
                        else
                        {
                            $hashtags[$word]++;
                        }
                    }
                    else if($word[0] != '@')
                    {
                        if(strpos($word, 'http') === false)
                        {
                            if(!isset($popular[$word]))
                            {
                                $popular[$word] = 1;
                            }
                            else
                            {
                                $popular[$word]++;
                            }
                        }
                    }
                }
            }
        }

        arsort($popular);
        arsort($hashtags);

        if(!$html)
        {
            return [
                'popular' => json_encode($popular),
                'hashtags' => json_encode($hashtags)
            ];
        }
        else
        {
            $popularTable = '<table class="table table-striped"><thead><tr><th>Word</th><th>Count</th></tr></thead></tbody>';
            foreach($popular as $key => $value)
            {
                $popularTable .= '<tr><td>'.$key.'</td><td>'.$value.'</td>';
            }
            $popularTable .= '</tbody></table>';

            $hashtagTable = '<table class="table table-striped"><thead><tr><th>Word</th><th>Count</th></tr></thead></tbody>';
            foreach($hashtags as $key => $value)
            {
                $hashtagTable .= '<tr><td>'.$key.'</td><td>'.$value.'</td>';
            }
            $hashtagTable .= '</tbody></table>';

            return [
                'popular' => $popularTable,
                'hashtags' => $hashtagTable
            ];
        }
    }

    /**
     * check if tweet is already stored in db
     * @param  string  $id id of the tweet
     * @return boolean    whether or not the tweet was stored
     */
    private function tweetExists($id)
    {
        $result = $this->conn->query('select id from tweets where tweet_id = :tweet_id limit 1', [
            ':tweet_id' => $id
        ]);

        $row = $result->fetchAll();

        if(count($row) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
