## Dynamit Code Exercise

This exercise was completed using the Laravel framework. I kept in mind that the code written should be mine and not Laravel's, outside of the router.

All the of the database migrations are located [here](https://github.com/spacenoodles/dynamit/tree/master/database/migrations). I was unsure if this was the best approach for documenting the database structure, but seeing as the migrations are already built into Laravel, it made sense to me.

The Twitter application keys can be placed in the [Twitter config file](https://github.com/spacenoodles/dynamit/blob/master/config/twitter.php).

There are a few "core" files that run the bulk of the application. You can find them below:

* [Connection](https://github.com/spacenoodles/dynamit/blob/master/app/Utility/Connection.php)
* [TwitterAPI](https://github.com/spacenoodles/dynamit/blob/master/app/Twitter/TwitterAPI.php)
* [TwitterStorage](https://github.com/spacenoodles/dynamit/blob/master/app/Twitter/TwitterStorage.php)
* [TwitterController](https://github.com/spacenoodles/dynamit/blob/master/app/Http/Controllers/TwitterController.php)

For the statistics page you can return html instead of json, simply by passing a key/value pair as a get parameter in the url like such:

`/stats?html=true`

I would have loved to build in some caching features into this application, but simply did not have the time to. You can see some notes and database schema setups that were made for that eventuality.
