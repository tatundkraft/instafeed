# Instafeed

Do you want to display your instagram feed on your website while enjoying the performance of locally hosted images? 

Instafeed grabs the last 20 posts of your instagram feed, downloads and stores the images locally and serves an updated json.
It also includes an access token generator.

## Getting Started

1. Clone this repository.
2. Pastes the contents of `config.sample.php` to `config.php` and update it with your credentials. You'll need an access token for this. If you don't have one, see [Generate Access Token](#generate-access-token).
3. Serve it on  your local machine by pasting `php -S localhost:8000` in your terminal.
4. Access `localhost:8000` for the json or `localhost:8000/example.html` for an example.

### Prerequisites

 * php installed
 * Write access in the root folder of this script, because folders and files are written for caching.
 * An instagram account


### Generate Access Token

1. Register at https://instagram.com/developer
2. Click `Manage Clients` and follow their instructions to register a new client
3. Click `Manage` on your newly created client to get the `Client ID` and `Client Secret` at the top of the page. Basic permissions are enough for this.
4.  **EITHER**
    * Use an online service to generate your access token. **OR**
    * Use the following instructions to use the included access token generator.
5. Go the the settings of your client again, where you found the secret and id.
6. Click `Security` and paste `http://localhost:8000/generate_access_token.php` or your config's `REDIRECT_URI` into the `Valid redirect URIs` field. 
7. Start you local server with `php -S localhost:8000` and browse to `http://localhost:8000/generate_access_token.php` and your newly generated access token should be printed on the screen.
8. Copy and paste the generated access code into the corresponding field in your `config.php`.

### What's next?

The included example.html is only a very rough sketch how to use this tool, although it shows how you can use this tool. You now have all images locally cached and a json with updated urls to your cached files. Go create your own amazing websites with these or just store your instagram images.

### Notes

* This tool only caches the last 20 posts on your instagram account. Older images are also deleted from the cache.
* So far, only image-posts and carousel-image-posts are processed. Videos are not supported.

## Deployment

To deploy this service, you'll need to change some variables. Most, if not all changes can (and should) be done in the `config.php`.


## Authors

Joshua Stübner - [joshuastuebner.com](https://joshuastuebner.com)

See also the list of [contributors](https://github.com/tatundkraft/instafeed/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

Thank you to [InstaFeed.js](http://instafeedjs.com/) for inspiration.
