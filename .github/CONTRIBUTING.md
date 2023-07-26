# Contribution Guidelines
We welcome you to report [issues](/../../issues) or submit [pull requests](/../../pulls).  While the below guidelines are necessary to get code merged, you can
submit pull requests that do not adhere to them and we will try to take care of them in our spare time.  We are a smallish group of developers,
though, so if you can make sure the build is passing 100%, that would be very useful.

We recommend including details of your particular usecase(s) with any issues or pull requests.  We love to hear how our libraries are being used
and we can get things merged in quicker when we understand its expected usage.

## Pull Requests
Code changes should be sent through [GitHub Pull Requests](/../../pulls).  Before submitting the pull request, make sure that phpunit reports success:

### PHPUnit
While the build does not strictly enforce 100% [PHPUnit](http://www.phpunit.de) code coverage, it will not allow coverage to drop below its current percentage.

```sh
./vendor/bin/phpunit --coverage-html coverage
```

### PHP CodeSniffer
The build will also not allow any errors for the [coding standard](https://www.php-fig.org/psr/psr-12/)

```sh
./vendor/bin/phpcs
