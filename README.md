# Guzzle Bundle Middleware Plugin

This plugin integrates a generic way to include all middleware.


## Requirements
 - PHP 7.0 or above
 - [Guzzle Bundle][1]

 
### Installation
Using [composer][2]:

##### command line
``` bash
$ composer require wizbit/guzzle-bundle-middleware-plugin
```

## Usage
### Enable bundle
``` php
# app/AppKernel.php

new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new Wizbit\Bundle\GuzzleBundleMiddlewarePlugin\GuzzleBundleMiddlewarePlugin(),
])
```

### Basic configuration
``` yaml
# app/config/config.yml

eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"

            # define headers, options

            # plugin settings
            plugin:
                middleware:
                    - '@my.middleware.service'
```

[1]: https://github.com/8p/EightPointsGuzzleBundle
[2]: https://getcomposer.org/