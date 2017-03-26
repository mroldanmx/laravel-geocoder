# php-skeleton-library
A PHP skeleton project with some goodies.

### Usage

1. Clone it:

```
$ git clone git@github.com:ratacibernetica/php-skeleton-project.git my-awesome-project
```

2. Install dependencies (require-dev):

```
$ composer install
```

3. Make it yours, remove the remote:

```
$ git remote remove 
```

### Testing

The composer.json comes with [PHPUnit]() and the task runner [Robo](https://github.com/consolidation/Robo).

To execute the task runner defined in the `RoboFile`:
```
$ composer test
```

The robo plugin executes the tests **every time a change is made in src or tests folder**. You can tweak this to your preference in the RoboFile.
 
