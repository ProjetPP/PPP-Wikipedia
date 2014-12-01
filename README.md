# PPP Wikipedia

[![Build Status](https://scrutinizer-ci.com/g/ProjetPP/PPP-Wikipedia/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ProjetPP/PPP-Wikipedia/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/ProjetPP/PPP-Wikipedia/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ProjetPP/PPP-Wikipedia/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ProjetPP/PPP-Wikipedia/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ProjetPP/PPP-Wikipedia/?branch=master)


PPP-Wikipedia is a PPP module that use [Wikipedia](http://wikipedia.org) content.

It currently only answers to the queries under the format `(*, identity, ?)`.

## Installation

1 - Clone the repository:

    git clone https://github.com/ProjetPP/PPP-Wikipedia.git

2 - Install dependencies with composer:

    curl -sS https://getcomposer.org/installer | php
    php composer.phar install

3 - Make www/index.php executable by a web server, and put an URL to this
  web server in the configuration of your PPP core (and make sure the latter
  can access it)
