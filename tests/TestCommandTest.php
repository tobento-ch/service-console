<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Console\Test;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use Tobento\Service\Console\Command;
use Tobento\Service\Console\InteractorInterface;
use Tobento\Service\Console\Symfony;
use Tobento\Service\Console\Test\Mock;
use Tobento\Service\Container\Container;

class TestCommandTest extends TestCase
{
    public function testExpectsExitCodeMethod()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsExitCode(0);
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsExitCodeMethodFailsIfNotSameCode()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Received exit code [1] but expected [0].');
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsExitCode(1);
        $test->execute();
    }
    
    public function testExpectsOutputMethod()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsOutput('lorem');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsOutputMethodFailsIfNotSameOutput()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Output "lorem ipsum" was not printed.');
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsOutput('lorem ipsum');
        $test->execute();
    }
    
    public function testExpectsOutputMethodMultiple()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem');
                $io->write('ipsum');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsOutput('lorem');
        $test->expectsOutput('ipsum');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsOutputMethodMultipleFailsIfOneIsNotSame()
    {
        $this->expectException(AssertionFailedError::class);
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem');
                $io->write('ipsum');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsOutput('lorem');
        $test->expectsOutput('ips');
        $test->execute();
    }    
    
    public function testExpectsOutputMethodMultipleMixed()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem');
                $io->info('info');
                $io->comment('comment');
                $io->warning('warning');
                $io->error('error');
                $io->success('success');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsOutput('lorem');
        $test->expectsOutput('info');
        $test->expectsOutput('comment');
        $test->expectsOutput('warning');
        $test->expectsOutput('error');
        $test->expectsOutput('success');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testDoesntExpectOutputMethod()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->doesntExpectOutput('foo');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testDoesntExpectOutputMethodFailsIfSame()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Output "lorem" was printed.');
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->doesntExpectOutput('lorem');
        $test->execute();
    }
    
    public function testExpectsOutputToContainMethod()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem ipsum dolor');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsOutputToContain('ipsum');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsOutputToContainMethodFailsIfNotContaining()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Output does not contain "foo".');
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem ipsum dolor');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsOutputToContain('foo');
        $test->execute();
    }
    
    public function testDoesntExpectOutputToContainMethod()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem ipsum dolor');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->doesntExpectOutputToContain('foo');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testDoesntExpectOutputToContainMethodFailsIfContaining()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Output contains "ipsum".');
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->write('lorem ipsum dolor');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->doesntExpectOutputToContain('ipsum');
        $test->execute();
    }
    
    public function testExpectsTableMethod()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->table(
                    headers: ['Name', 'Email'],
                    rows: [
                        ['Tom', 'tom@example.com'],
                    ],
                );
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsTable(
            headers: ['Name', 'Email'],
            rows: [
                ['Tom', 'tom@example.com'],
            ],
        );
        $test->execute();
        
        $this->assertTrue(true);
    }

    public function testExpectsTableMethodFailsIfMissing()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Table "1" not printed or matching.');
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsTable(
            headers: ['Name', 'Email'],
            rows: [
                ['Tim', 'tom@example.com'],
            ],
        );
        $test->execute();
    }
    
    public function testExpectsTableMethodFailsIfNotSame()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Table "1" not printed or matching.');
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $io->table(
                    headers: ['Name', 'Email'],
                    rows: [
                        ['Tom', 'tom@example.com'],
                    ],
                );
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsTable(
            headers: ['Name', 'Email'],
            rows: [
                ['Tim', 'tom@example.com'],
            ],
        );
        $test->execute();
    }
    
    public function testExpectsQuestionMethod()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $name = $io->ask('What is your name?');
                $io->write($name);
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('What is your name?', 'Tom');
        $test->expectsOutput('Tom');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsQuestionMethodFailsIfNotAsked()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Question "What is your...?" was not asked.');
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('What is your...?', 'Tom');
        $test->execute();
    }
    
    public function testExpectsQuestionMethodMulitple()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $firstname = $io->ask('What is your firstname?');
                $lastname = $io->ask('What is your lastname?');
                $io->write($firstname);
                $io->write($lastname);
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('What is your firstname?', 'Tom');
        $test->expectsQuestion('What is your lastname?', 'Taylor');
        $test->expectsOutput('Tom');
        $test->expectsOutput('Taylor');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsQuestionMethodMulitpleFailsIfOneNotAsked()
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Question "What is your name?" was not asked.');
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $firstname = $io->ask('What is your firstname?');
                $lastname = $io->ask('What is your lastname?');
                $io->write($firstname);
                $io->write($lastname);
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('What is your firstname?', 'Tom');
        $test->expectsQuestion('What is your name?', 'Taylor');
        $test->execute();
    }    
    
    public function testWithQuestionButWithoutExpectingQuestion()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $name = $io->ask('What is your name?');
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsQuestionMethodUsingSecret()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $password = $io->secret('What is the password?');
                $io->write($password);
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('What is the password?', '*****');
        $test->expectsOutput('*****');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsQuestionMethodUsingConfirm()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                if ($io->confirm('Do you wish to continue?')) {
                    $io->write('continued');
                }
                return 1;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('Do you wish to continue?', true);
        $test->expectsOutput('continued');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsQuestionMethodUsingConfirmNegative()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                if (! $io->confirm('Do you wish to continue?')) {
                    $io->write('not continued');
                }
                return 1;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('Do you wish to continue?', false);
        $test->expectsOutput('not continued');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsQuestionMethodUsingConfirmFailsIfNotAsked()
    {
        $this->expectException(AssertionFailedError::class);
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('Do you wish to continue?', true);
        $test->execute();
    }
    
    public function testExpectsQuestionMethodUsingChoice()
    {
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $colors = $io->choice(
                    question: 'What color do you wish to use?',
                    choices: ['red', 'blue', 'yellow'],
                    default: 'red',
                    multiselect: true,
                );
                $io->write(implode(',', $colors));
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('What color do you wish to use?', ['red', 'yellow']);
        $test->expectsOutput('red,yellow');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testExpectsQuestionMethodUsingChoiceFailsIfNotSame()
    {
        $this->expectException(AssertionFailedError::class);
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io): int {
                $colors = $io->choice(
                    question: 'What color do you wish to use?',
                    choices: ['red', 'blue', 'yellow'],
                    default: 'red',
                    multiselect: true,
                );
                $io->write(implode(',', $colors));
                return 0;
            });
        
        $test = new TestCommand(command: $command);
        $test->expectsQuestion('What color do you wish to use??', ['red', 'green']);
        $test->execute();
    }
    
    public function testWithArgument()
    {
        $command = (new Command(name: 'name'))
            ->argument(
                name: 'user',
                description: 'The Id(s) of the user',
                variadic: true,
            )
            ->handle(function(InteractorInterface $io): int {
                $io->write('users: '.implode(',', $io->argument('user')));
                return 0;
            });
        
        $test = new TestCommand(command: $command, input: [
            'user' => [2, 3],
        ]);
        $test->expectsOutput('users: 2,3');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testWithOption()
    {
        $command = (new Command(name: 'name'))
            ->option(
                name: 'foo',
                description: 'Foo desc',
            )
            ->handle(function(InteractorInterface $io): int {
                $io->write($io->option('foo'));
                return 0;
            });
        
        $test = new TestCommand(command: $command, input: [
            '--foo' => 'value',
        ]);
        $test->expectsOutput('value');
        $test->execute();
        
        $this->assertTrue(true);
    }
    
    public function testWithContainer()
    {
        $container = new Container();
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io, Mock\Foo $foo): int {
                $io->write($foo->name());
                return 0;
            });
        
        (new TestCommand(command: $command))
            ->expectsOutput('foo')
            ->expectsExitCode(0)
            ->execute($container);
        
        $this->assertTrue(true);
    }
    
    public function testWithConsole()
    {
        $console = new Symfony\Console(
            name: 'app',
            container: new Container(),
        );
        
        $command = (new Command(name: 'name'))
            ->handle(function(InteractorInterface $io, Mock\Foo $foo): int {
                $io->write($foo->name());
                return 0;
            });
        
        (new TestCommand(command: $command))
            ->expectsOutput('foo')
            ->expectsExitCode(0)
            ->execute($console);
        
        $this->assertTrue(true);
    }
}