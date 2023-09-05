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
use Tobento\Service\Console\ConsoleInterface;
use Tobento\Service\Console\CommandInterface;
use Tobento\Service\Console\Symfony\Console;
use Tobento\Service\Console\Symfony\Interactor;
use Tobento\Service\Container\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mockery;

class TestSymfonyCommand
{
    protected ConsoleInterface $console;
    
    protected array $collectedOutput = [];
    
    protected array $collectedTables = [];
    
    protected array $expectedQuestions = [];
    
    protected array $collectedQuestions = [];
    
    /**
     * Create a new TestSymfonyCommand.
     *
     * @param TestCommand $testCommand
     * @param null|Console $console
     */
    public function __construct(
        protected TestCommand $testCommand,
        null|Console $console,
    ) {
        if (is_null($console)) {
            $console = new Console(
                name: 'app',
                container: new Container(),
            );
        }
        
        $this->console = $console;
    }

    /**
     * Execute the command.
     *
     * @return void
     */    
    public function execute(): void
    {
        $command = $this->console->createCommand($this->testCommand->command());

        $this->console->addCommand($command);
        
        $input = array_merge(['command' => $command->getName()], $this->testCommand->input());
        
        $arrInput = new ArrayInput($input);
        
        $command = $this->console->app()->get($command->getName())->getCommand();
        
        $command->interactorFactory(function($command, $input, $output) use ($arrInput) {
            return $this->createInteractorMock($command, $arrInput, $output);
        });
        
        $this->expectedQuestions = $this->testCommand->expectedQuestions();
        
        $this->exitCode = $this->console->app()->run($arrInput, null);
        
        $this->verifyExpectations();
    }
    
    /**
     * Determine if expectations are fulfilled.
     *
     * @return void
     */
    protected function verifyExpectations(): void
    {
        if (count($this->expectedQuestions)) {
            $question = $this->expectedQuestions[array_key_first($this->expectedQuestions)];
            TestCase::fail('Question "'.$question[0].'" was not asked.');
        }

        foreach($this->testCommand->expectedOutput() as $i => $output) {
            if (
                !array_key_exists($i, $this->collectedOutput)
                || (array_key_exists($i, $this->collectedOutput) && $output !== $this->collectedOutput[$i])
            ) {
                TestCase::fail('Output "'.$output.'" was not printed.');
            }
        }
        
        foreach($this->testCommand->unexpectedOutput() as $i => $output) {
            if (
                array_key_exists($i, $this->collectedOutput)
                && $output === $this->collectedOutput[$i]
            ) {
                TestCase::fail('Output "'.$output.'" was printed.');
            }
        }
        
        foreach($this->testCommand->expectedOutputContains() as $i => $output) {
            if (
                array_key_exists($i, $this->collectedOutput)
                && !str_contains($this->collectedOutput[$i], $output)
            ) {
                TestCase::fail('Output does not contain "'.$output.'".');
            }
        }
        
        foreach($this->testCommand->unexpectedOutputContains() as $i => $output) {
            if (
                array_key_exists($i, $this->collectedOutput)
                && str_contains($this->collectedOutput[$i], $output)
            ) {
                TestCase::fail('Output contains "'.$output.'".');
            }
        }

        foreach($this->testCommand->expectedTables() as $i => $table) {
            if (
                !array_key_exists($i, $this->collectedTables)
                || (
                    array_key_exists($i, $this->collectedTables)
                    && !$this->tableMatches($table, $this->collectedTables[$i])
                )
            ) {
                TestCase::fail('Table "'.($i+1).'" not printed or matching.');
            }
        }
        
        if (
            !is_null($this->testCommand->expectedExitCode())
            && $this->exitCode !== $this->testCommand->expectedExitCode()
        ) {
            TestCase::fail(sprintf(
                'Received exit code [%s] but expected [%s].',
                $this->testCommand->expectedExitCode(),
                $this->exitCode
            ));
        }
        
        TestCase::assertTrue(true);
    }

    /**
     * Create an interactor mock.
     *
     * @param Command $command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Mockery\MockInterface
     */
    protected function createInteractorMock(
        Command $command,
        InputInterface $input,
        OutputInterface $output,
    ) {
        $mock = Mockery::mock(Interactor::class, [$command, $input, $output])->makePartial();

        $mock->shouldReceive('write')
            ->andReturnUsing(function(string $message) use ($mock) {
                $this->collectedOutput[] = $message;
                return $mock;
            });

        $mock->shouldReceive('info')
            ->andReturnUsing(function(string $message) use ($mock) {
                $this->collectedOutput[] = $message;
                return $mock;
            });
        
        $mock->shouldReceive('comment')
            ->andReturnUsing(function(string $message) use ($mock) {
                $this->collectedOutput[] = $message;
                return $mock;
            });
        
        $mock->shouldReceive('warning')
            ->andReturnUsing(function(string $message) use ($mock) {
                $this->collectedOutput[] = $message;
                return $mock;
            });
        
        $mock->shouldReceive('error')
            ->andReturnUsing(function(string $message) use ($mock) {
                $this->collectedOutput[] = $message;
                return $mock;
            });
        
        $mock->shouldReceive('success')
            ->andReturnUsing(function(string $message) use ($mock) {
                $this->collectedOutput[] = $message;
                return $mock;
            });
        
        $mock->shouldReceive('table')
            ->andReturnUsing(function(array $headers, array $rows) use ($mock) {
                $this->collectedTables[] = [$headers, $rows];
                return $mock;
            });
                
        $mock->shouldReceive('ask')
            ->andReturnUsing(function($question) {
                $expectedQuestion = $this->getExpectedQuestion($question);
                
                if (is_null($expectedQuestion)) {
                    return '';
                }
                
                if ($question == $expectedQuestion[0]) {
                    unset($this->expectedQuestions[$expectedQuestion[2]]);
                }
                
                return $expectedQuestion[1];
            });
        
        $mock->shouldReceive('secret')
            ->andReturnUsing(function($question) {
                $expectedQuestion = $this->getExpectedQuestion($question);
                
                if (is_null($expectedQuestion)) {
                    return '';
                }
                
                if ($question == $expectedQuestion[0]) {
                    unset($this->expectedQuestions[$expectedQuestion[2]]);
                }
                
                return $expectedQuestion[1];
            });
        
        $mock->shouldReceive('confirm')
            ->andReturnUsing(function($question) {
                $expectedQuestion = $this->getExpectedQuestion($question);
                
                if (is_null($expectedQuestion)) {
                    return '';
                }
                
                if ($question == $expectedQuestion[0]) {
                    unset($this->expectedQuestions[$expectedQuestion[2]]);
                }
                
                return $expectedQuestion[1];
            });
        
        $mock->shouldReceive('choice')
            ->andReturnUsing(function($question, $choices) {
                $expectedQuestion = $this->getExpectedQuestion($question);
                
                if (is_null($expectedQuestion)) {
                    return [];
                }
                
                if ($question == $expectedQuestion[0]) {
                    unset($this->expectedQuestions[$expectedQuestion[2]]);
                }

                return $expectedQuestion[1];
            });
        
        return $mock;
    }
    
    /**
     * Returns the expected question or null if not exist.
     *
     * @param string $question
     * @return null|array
     */
    protected function getExpectedQuestion(string $question): null|array
    {
        foreach($this->expectedQuestions as $i => [$q, $answer]) {
            if ($q === $question) {
                return [$q, $answer, $i];
            }
        }
        
        return null;
    }
    
    /**
     * Returns true if table matches, otherwise false.
     *
     * @param array $expectedTable
     * @param array $collectedTable
     * @return bool
     */
    protected function tableMatches(array $expectedTable, array $collectedTable): bool
    {
        return $expectedTable === $collectedTable;
    }
}