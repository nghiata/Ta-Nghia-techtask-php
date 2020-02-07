# Ta-Nghia-techtask-php
 
### Configure

```composer install```

### Guide

Request to ```/lunch/[date]``` for recipe on date.

date format: ```Y-m-d```

**Example:** ```/lunch/2019-03-25```

### Unit Test

```php bin/phpunit tests/LunchControllerTest.php```

##### Note

Receive recipe for the current date if you just request to ```/lunch``` without date