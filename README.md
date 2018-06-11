<h1 align="center">aent-console</h1>
<p align="center">A utility PHP package to create Aents using the Symfony console.</p>
<p align="center">
    <a href="https://travis-ci.org/theaentmachine/aent-console">
        <img src="https://travis-ci.org/theaentmachine/aent-console.svg?branch=master" alt="Travis CI">
    </a>
    <a href="https://scrutinizer-ci.com/g/theaentmachine/aent-console/?branch=master">
        <img src="https://scrutinizer-ci.com/g/theaentmachine/aent-console/badges/quality-score.png?b=master" alt="Scrutinizer">
    </a>
    <a href="https://codecov.io/gh/theaentmachine/aent-console/branch/master">
        <img src="https://codecov.io/gh/theaentmachine/aent-console/branch/master/graph/badge.svg" alt="Codecov">
    </a>
</p>

---

## Why do I need this?

This package contains a set of classes that extend Symfony console classes to help you get started building an Aent.

Docker Aents must contain a "aent" program that accepts in argument events triggered by other aents.

```
$ aent event-name payload
```

When writing a command line application in PHP, it is fairly common to use [Symfony console](https://symfony.com/doc/current/components/console.html).

However, Aents have some peculiarities that do not fit with the Symfony console:

- If an event is not found, the aent must not return an error code. In Symfony console, if the first argument
  is not a known command, an error is raised
- Aents are configured using the PHEROMONE_LOG_LEVEL environment variable. Symfony console log level is configured
  using the "-vvv" option.

## Usage

A typical Aent will look like this:

```php
#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use TheAentMachine\AentApplication;
use MyAent\AddEventCommand;
use MyAent\DeleteDockerServiceEventCommand;
use MyAent\NewDockerServiceInfoEventCommand;
use MyAent\RemoveEventCommand;

// Notice how the application is a "AentApplication" and not a classical Symfony Console "Application"
$application = new AentApplication();

// Each event is a Symfony command
$application->add(new AddEventCommand());
$application->add(new RemoveEventCommand());
$application->add(new NewDockerServiceInfoEventCommand());
$application->add(new DeleteDockerServiceEventCommand());

$application->run();
```

Each command you write should extend "\TheAentMachine\EventCommand".

```php
class MyCommand extends EventCommand
{
    protected function getEventName(): string
    {
        return 'my-event-name';
    }

    protected function executeEvent(?string $payload): void
    {
        // Do some stuff with this event
    }
}
```

If your payload is a JSON message, you can even extend the "\TheAentMachine\JsonEventCommand".

```php
class MyCommand extends JsonEventCommand
{
    protected function getEventName(): string
    {
        return 'my-event-name';
    }

    protected function executeJsonEvent(array $payload): void
    {
        // Do some stuff with this event
    }
}
```
