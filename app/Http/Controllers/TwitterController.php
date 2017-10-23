<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Twitter\TwitterStorage;
use App\Utility\Connection;

class TwitterController extends Controller
{
    /**
     * showing statistical information on stored tweets
     * @return view returning a blade view
     */
    public function stats()
    {
        $html = false;
        if(isset($_GET['html']) && $_GET['html'] == 'true')
        {
            $html = true;
        }

        $twitter = new TwitterStorage;
        $stats = $twitter->pullStats($html);

        return view('stats')->with([
            'html' => $html,
            'stats' => $stats
        ]);
    }

    /**
     * ajax call for search
     * @param  Request $request form data
     * @return json             json
     */
    public function search()
    {
        $twitter = new TwitterStorage;
        $results = $twitter->searchAndStore($_POST['search']);
        $response = [];
        $tweets = [];

        if($results !== false)
        {
            $response['errors'] = false;
            foreach($results as $tweet)
            {
                $tweets[] = $tweet->text;
            }
            $response['tweets'] = $tweets;
        }
        else
        {
            $response['errors'] = true;
        }

        echo json_encode($response);
    }
}
