# MX_MegaMenu
The module provides useful topmenu to be able to create complex menu structure in Magento 2.

## Compatibility
Magento 2.X (tested in magento 2.2.11)

## Installation ##

### Composer ###

1. Make sure that you have added [Github's OAuth token](https://getcomposer.org/doc/articles/troubleshooting.md#api-rate-limit-and-oauth-tokens) to your composer configuration.


2. Add the Git repository to composer as the source of this package
```
composer config repositories.mx-megamenu vcs git@github.com:inviqa/mx-megamenu.git
```

3. Run the command below from your project root directory.
```
composer require "inviqa/mx-megamenu"
```

## Usage
Enable the menu in the admin:
MegaMenu > Settings > Enable Mega Menu

The default topmenu will be automatically removed when the module is enabled:
To move the megamenu in your layout structure the reference name is *mx.megamenu.topnav*

