<!DOCTYPE html>
<html>
    <head>
        <title>Dynamit Code Exercise</title>

        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <style>
            .search-result-box { display: none; margin-top: 50px; }
        </style>
        <script>
            $(document).ready(function(){
                var searchBox = $('input[name="search"]');

                $('button').on('click', function(e){
                    e.preventDefault();
                    var search = searchBox.val();

                    if(search != '')
                    {
                        var searchResults = $('.search-results');
                        var searchParam = $('.search-param');

                        searchResults.html('');
                        searchParam.text('');

                        $.post('/search', {_token: '{{csrf_token()}}', search: search}, function(response){
                            if(response.errors)
                            {
                                alert('There was an error search for tweets.');
                            }
                            else
                            {
                                var append = '';
                                for(var x = 0; x < response.tweets.length; x++)
                                {
                                    append += '<li>'+response.tweets[x]+'</pli>';
                                }
                                searchParam.text(search);
                                searchResults.append(append);
                            }

                            showSearchResults();
                        }, 'json');
                    }
                });

                function showSearchResults(query)
                {
                    $('.search-result-box').show();
                }
            });
        </script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1>Search Tweets</h1>
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" class="form-control" name="search" placeholder="Query" />
                        </div>
                        <button type="submit" class="btn btn-default">Search</button>
                    </form>
                    <div class="search-result-box">
                        <div class="panel panel-default">
                            <div class="panel-heading">Search results for: <span class="search-param"></span></div>
                            <div class="panel-body">
                                <ul class="search-results">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h1>Statistics</h1>
                    <a href="/stats" class="btn btn-default">Get Stats</a>
                </div>
            </div>
        </div>
    </body>
</html>
