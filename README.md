<br/>
<p align="center">

  <h3 align="center">smoapp_bot - Telegram bot</h3>

  <p align="center">
    An Awesome ReadME To Jumpstart Your Own Telegram Bot!
    <br/>
    <br/>
    <a href="https://github.com/TGRTON/smoapp_bot"><strong>Explore the docs ยป</strong></a>
    <br/>
    <br/>
    <a href="https://github.com/TGRTON/smoapp_bot">View Demo</a>
    .
    <a href="https://github.com/TGRTON/smoapp_bot/issues">Report Bug</a>
    .
    <a href="https://github.com/TGRTON/smoapp_bot/issues">Request Feature</a>
  </p>
</p>

![Downloads](https://img.shields.io/github/downloads/TGRTON/smoapp_bot/total) ![Contributors](https://img.shields.io/github/contributors/TGRTON/smoapp_bot?color=dark-green) ![Issues](https://img.shields.io/github/issues/TGRTON/smoapp_bot) ![License](https://img.shields.io/github/license/TGRTON/smoapp_bot) 

## Table Of Contents

* [About the Project](#about-the-project)
* [Built With](#built-with)
* [Getting Started](#getting-started)
  * [Prerequisites](#prerequisites)
  * [Installation](#installation)
* [Usage](#usage)
* [Roadmap](#roadmap)
* [Contributing](#contributing)
* [License](#license)
* [Authors](#authors)
* [Acknowledgements](#acknowledgements)

## About The Project

This project contains everything you need to launch your own Telegram bot quickly.

This bot is designed for communication with interested audience, allows visitors to quickly get social media promotion services also has referral program.

## Built With

This project is developed in procedural PHP version 7+ without the use of libraries, which allows you to run it on any hosting that supports PHP and MySQL. This approach also makes it easy and accessible to edit the bot's code. 

## Getting Started

This is an example of how you may setting up your project locally.
To get a local copy up and running follow these simple steps described below.

### Prerequisites

This project requires any hosting that supports PHP 7 and MySQL. 

### Installation

Main bot executable script: `tgbot.php`

1) Fill in the user data in the `data.ini.php` file, namely:

############################<br/>
$admin = 00000; //   ChatID of a manager/owner<br/>
$refpercent = 15; // Referral percent<br/>
$user_id = 0000; // User ID at smoservice.media<br/>
$api_key = 'XXX'; // API Key of smoservice.media<br/>
$roskassa_publickey = 'XXXX'; // Tegro Money Public Key<br/>
$roskassa_secretkey = 'XXXX'; // Tegro Money Secret Key<br/>
define('TOKEN', 'XXXXX'); // Add the Bot API Token<br/>
###########################<br/><br/>

2) Set the postback URL in the Tegro Money account: [https://yourdomain/BotFolder/postback.php](https://yourdomain/BotFolder/postback.php)

3) Fill in the MySQL database data in the `global.php` file

4) Import MYSQL database structure from `database.sql` file

5) Run in a browser the URL: [https://yourdomain/BotFolder/_smoservice_services.php](https://yourdomain/BotFolder/_smoservice_services.php) - the script will grab all actual services from smoservice.media and store them in the DB.

6) Install the webhook at https://api.telegram.org/ for the `tgbot.php` script:
[https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php](https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php)

7) Place the script [https://yourdomain/BotFolder/tonratechecker.php](https://yourdomain/BotFolder/delayed_posts.php) on cron with execution once a hour. It will send reminder to subscribers once a week.


## Usage

Find the bot in the Telegram environment by its username: `@YourBot` and start it with the command `/start`

## Roadmap

See the [open issues](https://github.com/TGRTON/smoapp_bot/issues) for a list of proposed features (and known issues).

## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.
* If you have suggestions for adding or removing projects, feel free to [open an issue](https://github.com/TGRTON/smoapp_bot/issues/new) to discuss it, or directly create a pull request after you edit the *README.md* file with necessary changes.
* Please make sure you check your spelling and grammar.
* Create individual PR for each suggestion.
* Please also read through the [Code Of Conduct](https://github.com/TGRTON/smoapp_bot/blob/main/CODE_OF_CONDUCT.md) before posting your first idea as well.

### Creating A Pull Request

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

https://github.com/TGRTON/smoapp_bot/blob/main/LICENSE

## Authors

* **Lana Cool** - *Developer* - [Lana Cool](https://github.com/lana4cool/) - *Telegram bots on PHP*

## Acknowledgements

* [Lana](https://github.com/lana4cool/)
