# Console Service

Command Line Interface using [Symfony Console](https://github.com/symfony/console) as default implementation.

## Table of Contents

- [Getting started](#getting-started)
    - [Requirements](#requirements)
    - [Highlights](#highlights)
- [Documentation](#documentation)
    - [Console](#console)
        - [Create Console](#create-console)
        - [Add Command](#add-command)
        - [Run Console](#run-console)
        - [Execute Command](#execute-command)
    - [Creating Commands](#creating-commands)
        - [Command](#command)
            - [Arguments and Options](#arguments-and-options)
        - [Abstract Command](#abstract-command)
            - [Using Signature](#using-signature)
    - [Interactor](#interactor)
        - [Retrieving Argument and Option Values](#retrieving-argument-and-option-values)
        - [Writing Output](#writing-output)
        - [Asking Questions](#asking-questions)
        - [Progress Bar](#progress-bar)
        - [Verbosity Levels](#verbosity-levels)
    - [Locking](#locking)
    - [Signals](#signals)
    - [Events](#events)
    - [Testing](#testing)
    - [Symfony](#symfony)
        - [Symfony Console](#symfony-console)
        - [Symfony Custom Interactor](#symfony-custom-interactor)
- [Credits](#credits)
___

# Getting started

Add the latest version of the console service project running this command.

```
composer require tobento/service-console
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

# Documentation

## Console

### Create Console

```php
use Tobento\Service\Console\Symfony;
use Tobento\Service\Console\ConsoleInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

$console = new Symfony\Console(
    name: 'app',
    container: $container, // ContainerInterface
    
    // you may define a event dispatcher:
    eventDispatcher: $eventDispatcher, // EventDispatcherInterface
);

var_dump($console instanceof ConsoleInterface);
// bool(true)
```

### Add Command

After [Creating Commands](#creating-commands) you can add them in the console:

```php
$console->addCommand(SampleCommand::class);

// or
$console->addCommand(new SampleCommand());
```

### Run Console

```php
$console->run();
```

### Execute Command

You may want to execute a command instead of running the console.

```php
use Tobento\Service\Console\ExecutedInterface;

$executed = $console->execute(
    command: SampleCommand::class,
    
    // passing arguments and options:
    input: [
        // passing arguments:
        'username' => 'Tom',

        // with array value:
        'username' => ['Tom', 'Tim'],

        // passing options:
        '--some-option' => 'value',

        // with array value:
        '--some-option' => ['value'],
    ]
);

var_dump($executed instanceof ExecutedInterface);
// bool(true)

$command = $executed->command(); // string
$code = $executed->code(); // int
$output = $executed->output(); // string
```

**Example with command class**

```php
use Tobento\Service\Console\Command;
use Tobento\Service\Console\InteractorInterface;

$command = (new Command(name: 'name'))
    ->handle(function(InteractorInterface $io): int {
        // do sth:
        return 0;
    });
    
$console->execute(command: $command);
```

**Example with command name**

```php
$console->addCommand(SampleCommand::class);

$console->execute(command: 'sample');
```

## Creating Commands

### Command

You may use the ```Command::class``` to create simple commands.

```php
use Tobento\Service\Console\Command;
use Tobento\Service\Console\CommandInterface;
use Tobento\Service\Console\InteractorInterface;

$command = (new Command(name: 'mail:send'))
    // you may set a description:
    ->description('Send an email to a user(s)')
    
    // you may set a usage text:
    ->usage('Send emails ...')
    
    // you may add an argument(s):
    ->argument(
        name: 'user',
        description: 'The Id(s) of the user',
        variadic: true,
    )
    
    // you may add an option(s):
    ->option(
        name: 'queue',
        description: 'Whether the email should be queued',
    )
    
    // handle the command:
    ->handle(function(InteractorInterface $io, MailerInterface $mailer): int {
        // retrieve input arguments and options:
        $userIds = $io->argument('user');
        $queue = $io->option('queue');
        
        // send emails using the mailer...
        
        // you may write some output:
        $io->write(sprintf(
            'email(s) send to user ids %s queued [%s]',
            implode(',', $userIds),
            $queue ? 'true' : 'false',
        ));

        return Command::SUCCESS;
        // return Command::FAILURE;
        // return Command::INVALID;
    });
    
var_dump($command instanceof CommandInterface);
// bool(true)
```

Check out the [Interactor](#interactor) section to learn more about it.

#### Arguments and Options

**Arguments in detail**

```php
use Tobento\Service\Console\Command;

$command = (new Command(name: 'sample'))
    ->argument(
        // The name of the argument:
        name: 'name',

        // you may define a description:
        description: 'Some description',

        // you may define a default value(s) (null default):
        value: ['foo', 'bar'], // mixed

        // set if the argument is optional (false default):
        optional: true,

        // if true expecting multiple values (false default):
        variadic: true,

        // not supported yet!
        suggestedValues: null,
    );
```

**Options in detail**

```php
use Tobento\Service\Console\Command;

$command = (new Command(name: 'sample'))
    ->option(
        // The name of the option:
        name: 'name',

        // you may define a description:
        description: 'Some description',

        // you may define a default value(s) (null default):
        value: ['foo', 'bar'], // mixed
        
        // variadic:
        variadic: null, // (default)
        // acts as boolean value, if exists true, otherwise false.
        
        variadic: false,
        // optional value (e.g. --name or --name=foo) if not specified default value is used.
        
        variadic: true,
        // is variadic expecting multiple values (e.g. --name=foo --name=bar).
        // if not specified default values are used.
     
        // not supported yet!
        suggestedValues: null,
    );
```

### Abstract Command

Simply extend from the ```AbstractCommand::class``` to create more complex commands.

```php
use Tobento\Service\Console\AbstractCommand;
use Tobento\Service\Console\InteractorInterface;

class SendEmails extends AbstractCommand
{
    /**
     * The command name.
     */
    public const NAME = 'email:send';
    
    /**
     * The command description.
     */
    public const DESC = 'Send an email to a user(s)';
    
    /**
     * The command usage text.
     */
    public const USAGE = 'Send emails ...';
    
    /**
     * Create a new instance.
     */
    public function __construct()
    {
        // you may add an argument(s):
        $this->argument(
            name: 'user',
            description: 'The Id(s) of the user',
            variadic: true,
        );
        
        // you may add an option(s):
        $this->option(
            name: 'queue',
            description: 'Whether the email should be queued',
        );
    }
    
    /**
     * Handle the command.
     *
     * @param InteractorInterface $io
     * @return int The exit status code: 
     *     0 SUCCESS
     *     1 FAILURE If some error happened during the execution
     *     2 INVALID To indicate incorrect command usage e.g. invalid options
     */
    public function handle(InteractorInterface $io, MailerInterface $mailer): int
    {
        // retrieve input arguments and options:
        $userIds = $io->argument('user');
        $queue = $io->option('queue');
        
        // send emails using the mailer...
        
        // you may write some output:
        $io->write(sprintf(
            'email(s) send to user ids %s queued [%s]',
            implode(',', $userIds),
            $queue ? 'true' : 'false',
        ));
        
        return 0;
        // or use the available constants:
        // return static::SUCCESS;
        // return static::FAILURE;
        // return static::INVALID;
    }
}
```

Check out [Arguments and Options](#arguments-and-options) for more detail.

#### Using Signature

You may use the ```SIGNATURE``` as an alternative way to define the name, description, arguments and options of your command.

```php
use Tobento\Service\Console\AbstractCommand;
use Tobento\Service\Console\InteractorInterface;

class SendEmails extends AbstractCommand
{
    /**
     * The signature of the console command.
     */
    public const SIGNATURE = '
        mail:send | Send an email to a user(s)
        {user : The Id(s) of the user}
        {--queue : Whether the email should be queued}
    ';
    
    /**
     * Handle the command.
     *
     * @param InteractorInterface $io
     * @return int The exit status code: 
     *     0 SUCCESS
     *     1 FAILURE If some error happened during the execution
     *     2 INVALID To indicate incorrect command usage e.g. invalid options
     */
    public function handle(InteractorInterface $io): int
    {
        return 0;
    }
}
```

**Arguments**

* ```{name}``` required, expecting single value
* ```{name?}``` optional, expecting single value
* ```{name[]}``` required and variadic, expecting multiple values
* ```{name[]?}``` optional and variadic, expecting multiple values
* ```{name=}``` optional with ```null``` as default value
* ```{name=foo}``` optional with ```foo``` as default value
* ```{name=[foo,bar]}``` optional and variadic with ```foo``` and ```bar``` as default values

**Options**
    
* ```{--name}``` acts as boolean value, if exists ```true```, otherwise ```false```
* ```{--n|name}``` with ```n``` as short name
* ```{--name=}``` with ```null``` as default value
* ```{--name=foo}``` with ```foo``` as default value
* ```{--name[]}``` variadic, expecting multiple values
* ```{--name=[foo,bar]}``` variadic with ```foo``` and ```bar``` as default values

Options are optional in general!

## Interactor

The interactor let you interact with the input and output from the console while handling your command:

```php
use Tobento\Service\Console\Command;
use Tobento\Service\Console\InteractorInterface;

$command = (new Command(name: 'mail:send'))
    // handle the command:
    ->handle(function(InteractorInterface $io): int {
        // ...
        // use the interactor $io to interact
        // ...
    });
```

### Retrieving Argument and Option Values

Retrieve input argument(s) and option(s) if specified. See [Arguments and Options](#arguments-and-options).

```php
// Argument(s):
$value = $io->argument(name: 'name');

// all values indexed by the argument name:
$values = $io->arguments();

// Option(s):
$value = $io->option(name: 'name');

// all values indexed by the option name:
$values = $io->options();
```

**Argument details**

```php
// Argument with variadic: false
$value = $io->argument(name: 'name');
// NULL, if the argument was not passed when running the command
// Not NULL, if the argument was passed when running the command

// Argument with variadic: true
$value = $io->argument(name: 'name');
// Array empty if the argument was not passed when running the command
```

**Option details**

```php
// Option with variadic: null
$value = $io->option(name: 'name');
// bool(false), if the option was not passed when running the command
// bool(true), if the option was passed when running the command

// Option with variadic: false
$value = $io->option(name: 'name');
// NULL, if the option was not passed when running the command
// Not NULL, if the option was passed when running the command

// Option with variadic: true
$value = $io->option(name: 'name');
// Array, empty if the option was not passed when running the command
```

### Writing Output

```php
$io->write('Some text');

$io->write('Some Text', 'newline');
$io->write('Some Text', newline: 1);

// Write a single blank line:
$io->newLine();

// Write three blank lines:
$io->newLine(num: 3);

// Write specific messages:
$io->info('An info message');
$io->comment('A comment message');
$io->warning('A warning message');
$io->error('An error message');
$io->success('A success message');
```

**Writing formatted output**

You may write formatted output by the following way:

```php
$io->write('<comment>Some text</comment>');

$io->write('<fg=red>Some text</>');
$io->write('<bg=red>Some text</>');
$io->write('<fg=white;bg=red>Some text</>');
```

**Writing tables**

```php
$io->table(
    headers: ['Name', 'Email'],
    rows: [
        ['Tom', 'tom@example.com'],
    ],
);
```

### Asking Questions

```php
$name = $io->ask('What is your name?');

// With validator:
$name = $io->ask('What is your name?', validator: function(string $answer): void {
    if ($answer !== 'something') {
        throw new \Exception('Your answer is incorrect');
    }
});

// With max attempts:
$name = $io->ask('What is your name?', attempts: 2);
```

**Secret question**

You may ask a secret question:

```php
$password = $io->secret('What is the password?');

// With validator:
$name = $io->secret('What is your name?', validator: function(string $answer): void {
    if ($answer !== 'something') {
        throw new \Exception('Your answer is incorrect');
    }
});

// With max attempts:
$name = $io->secret('What is your name?', attempts: 2);
```

**Confirmation question**

You may ask for confirmation:

```php
if ($io->confirm('Do you wish to continue?', default: true)) {
    // ...
}
```

**Choice question**

You may ask a choice question:

```php
$color = $io->choice(
    question: 'What color do you wish to use?',
    choices: ['red', 'blue'],
    default: 'red',
    multiselect: true,
);
```

### Progress Bar

```php
$io->progressStart(max: 5);

foreach (range(0, 5) as $number) {
    sleep(1);
    $io->progressAdvance(step: 1);
}

$io->progressFinish();
```

### Verbosity Levels

* ```quiet``` No message output
* ```normal``` Normal output
* ```v``` Low verbosity
* ```vv``` Medium verbosity
* ```vvv``` High verbosity

```php
if ($io->isVerbose('vv')) {
    $io->write('Some Text');
}

// or:
$io->write('Some Text', 'v');
$io->write('Some Text', 'vv');
$io->write('Some Text', 'vvv');
```

## Locking

Not supported yet.

## Signals

Not supported yet.

## Events

You may listen to the following events if you have [defined a event listener in the console](#create-console):

| Event | Description |
| --- | --- |
| ```Tobento\Service\Console\Event\CommandStarting::class``` | The Event is fired ```before``` executing the console command. |
| ```Tobento\Service\Console\Event\CommandFinished::class``` | The Event is fired ```after``` executing the console command. |

## Testing

You may test commands using the ```TestCommand::class```.

You may check out the ```Tobento\Service\Console\Test\TestCommandTest::class``` for examples.

```php
use PHPUnit\Framework\TestCase;
use Tobento\Service\Console\Test\TestCommand;

class SampleCommandTest extends TestCase
{
    public function testCommand()
    {
        (new TestCommand(command: SampleCommand::class))
            // output expectations:
            ->expectsOutput('lorem')
            ->doesntExpectOutput('ipsum')
            ->expectsOutputToContain('lorem')
            ->doesntExpectOutputToContain('ipsum')
            ->expectsTable(
                headers: ['Name', 'Email'],
                rows: [
                    ['Tim', 'tom@example.com'],
                ],
            )
            
            // questions expectations:
            ->expectsQuestion('What is your name?', answer: 'Tom')
            ->expectsQuestion('What colors do you wish to use?', answer: ['red', 'yellow'])
            ->expectsQuestion('Do you wish to continue?', answer: true)
            
            // exit code expectation:
            ->expectsExitCode(0)
            
            // execute test:
            ->execute();
    }
}
```

**Passing input arguments and options**

```php
use PHPUnit\Framework\TestCase;
use Tobento\Service\Console\Test\TestCommand;
use Tobento\Service\Console\CommandInterface;

class SampleCommandTest extends TestCase
{
    public function testCommand()
    {
        (new TestCommand(
            command: SampleCommand::class, // string|CommandInterface
            input: [
                // passing arguments:
                'username' => 'Tom',
                
                // with array value:
                'username' => ['Tom', 'Tim'],

                // passing options:
                '--some-option' => 'value',
                
                // with array value:
                '--some-option' => ['value'],
                
                // pass null for options with variadic: null
                '--some-option' => null,
                
            ],
        ))
        // set expectations:
        ->expectsOutput('lorem')
        ->expectsExitCode(0)

        // execute test:
        ->execute();
    }
}
```

**Passing Container Or Console**

```php
use PHPUnit\Framework\TestCase;
use Tobento\Service\Console\Test\TestCommand;
use Tobento\Service\Console\ConsoleInterface;
use Psr\Container\ContainerInterface;

class SampleCommandTest extends TestCase
{
    public function testCommand()
    {
        (new TestCommand(command: SampleCommand::class))
            ->expectsExitCode(0)
            
            // if no dependencies
            ->execute() // null
            
            // passing the console to test on:
            ->execute($console) // ConsoleInterface
            
            // or just passing the container
            // (recommended way as console independent):
            ->execute($container); // ContainerInterface
    }
}
```

**With input**

You may use the ```withInput``` method returning a new ```TestCommand::class``` instance:

```php
use PHPUnit\Framework\TestCase;
use Tobento\Service\Console\Test\TestCommand;

class SampleCommandTest extends TestCase
{
    private function command(): TestCommand
    {
        return new TestCommand(command: SampleCommand::class);
    }
    
    public function testCommand()
    {
        $this->command()
            ->withInput([
                'username' => 'Tom',            
            ])
            ->expectsExitCode(0)
            ->execute();
    }
}
```

## Symfony

### Symfony Console

```php
use Tobento\Service\Console\Symfony;
use Tobento\Service\Console\ConsoleInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

$console = new Symfony\Console(
    name: 'app',
    container: $container, // ContainerInterface
    
    // you may define a event dispatcher:
    eventDispatcher: $eventDispatcher, // EventDispatcherInterface
);

var_dump($console instanceof ConsoleInterface);
// bool(true)
```

### Symfony Custom Interactor

You may create a custom interactor using the ```interactorFactory``` parameter:

```php
use Tobento\Service\Console\Symfony;
use Tobento\Service\Console\ConsoleInterface;
use Tobento\Service\Console\InteractorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Psr\Container\ContainerInterface;

$interactorFactory = function(
    Command $command,
    InputInterface $input,
    OutputInterface $output
): InteractorInterface {
    return new Symfony\Interactor(
        command: $command,
        input: $input,
        output: $output,
        style: null, // null|SymfonyStyle
    );
    
    // or create another Interactor fitting your needs
};

$console = new Symfony\Console(
    name: 'app',
    container: $container, // ContainerInterface
    interactorFactory: $interactorFactory,
);

var_dump($console instanceof ConsoleInterface);
// bool(true)
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)
- [Symfony Console](https://github.com/symfony/console)