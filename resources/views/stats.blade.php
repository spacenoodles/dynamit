<!DOCTYPE html>
<html>
    <head>
        <title>Dynamit Code Exercise</title>

        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Stats</h1>
                    @if($html)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Popular Words</div>
                                    <div class="panel-body">
                                        {!! $stats['popular'] !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Popular hashtags</div>
                                    <div class="panel-body">
                                        {!! $stats['hashtags'] !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                    <div class="panel panel-default">
                        <div class="panel-heading">Stored statistics json</div>
                        <div class="panel-body">
                            <pre>{{$stats['popular']}}</pre>
                            <pre>{{$stats['hashtags']}}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </body>
</html>
