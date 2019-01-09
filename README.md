# sdLanguageShopSessionSyncShopware

## About sdLanguageShopSessionSyncShopware

This shopware plugin synchronizes the session during a language change.

The default behaviour in Shopware is that on a language change a new session is started
- the user (if logged in) will be logged out
- the cart will be emptied

The plugin will take care that the user
- stays logged in
- all products will stay in the shopping cart


## Running Tests

### phpunit - functional

    Not working at the moment because phpunit is functional testing and there is no running shopware installation.

    $ vendor/bin/phpunit
    
### phpunit - unit

    $ vendor/bin/phpunit -c phpunit_unit.xml.dist
    
### phpspec

    $ vendor/bin/phpspec-standalone.php7.2.phar

## License

Please see [License File](LICENSE) for more information.
