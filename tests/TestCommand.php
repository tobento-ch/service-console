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
use Tobento\Service\Console\Symfony;
use Psr\Container\ContainerInterface;

final class TestCommand
{
    protected null|int $expectedExitCode = null;
    
    protected array $expectedOutput = [];
    
    protected array $unexpectedOutput = [];
    
    protected array $expectedOutputContains = [];
    
    protected array $unexpectedOutputContains = [];
    
    protected array $expectedTables = [];
    
    protected array $expectedQuestions = [];
    
    /**
     * Create a new TestCommand.
     *
     * @param string|CommandInterface $command
     * @param array $input
     */
    public function __construct(
        protected string|CommandInterface $command,
        protected array $input = [],
    ) {}
    
    /**
     * Returns the command.
     *
     * @return string|CommandInterface
     */
    public function command(): string|CommandInterface
    {
        return $this->command;
    }
    
    /**
     * Returns the input.
     *
     * @return array
     */
    public function input(): array
    {
        return $this->input;
    }
    
    /**
     * Returns a new instance with the specified input.
     *
     * @param array $input
     * @return static
     */
    public function withInput(array $input): static
    {
        return new static($this->command(), $input);
    }

    /**
     * Execute the command.
     *
     * @param null|ContainerInterface|ConsoleInterface $console
     * @return void
     */
    public function execute(null|ContainerInterface|ConsoleInterface $console = null): void
    {
        // In future, we might execute the command
        // for all console implementations to test on!
        
        if ($console instanceof ConsoleInterface && ! $console instanceof Symfony\Console) {
            throw new \InvalidArgumentException('Unsupported Console');
        }
        
        if ($console instanceof ContainerInterface) {
            $console = new Symfony\Console(name: 'app', container: $console);
        }
        
        (new TestSymfonyCommand(testCommand: $this, console: $console))->execute();
    }

    /**
     * Specify the exit code expected.
     *
     * @param int $code
     * @return static $this
     */
    public function expectsExitCode(int $code): static
    {
        $this->expectedExitCode = $code;
        return $this;
    }
    
    /**
     * Returns the expected exit code.
     *
     * @return null|int
     */
    public function expectedExitCode(): null|int
    {
        return $this->expectedExitCode;
    }
    
    /**
     * Specify the output expected.
     *
     * @param string $output
     * @return static $this
     */
    public function expectsOutput(string $output): static
    {
        $this->expectedOutput[] = $output;
        return $this;
    }
    
    /**
     * Returns the expected output.
     *
     * @return array<int, string>
     */
    public function expectedOutput(): array
    {
        return $this->expectedOutput;
    }
    
    /**
     * Specify the output not expected.
     *
     * @param string $output
     * @return static $this
     */
    public function doesntExpectOutput(string $output): static
    {
        $this->unexpectedOutput[] = $output;
        return $this;
    }
    
    /**
     * Returns the unexpected output.
     *
     * @return array<int, string>
     */
    public function unexpectedOutput(): array
    {
        return $this->unexpectedOutput;
    }
    
    /**
     * Specify the output expected to contain.
     *
     * @param string $output
     * @return static $this
     */
    public function expectsOutputToContain(string $output): static
    {
        $this->expectedOutputContains[] = $output;
        return $this;
    }
    
    /**
     * Returns the expected output contains.
     *
     * @return array<int, string>
     */
    public function expectedOutputContains(): array
    {
        return $this->expectedOutputContains;
    }
    
    /**
     * Specify the output unexpected to contain.
     *
     * @param string $output
     * @return static $this
     */
    public function doesntExpectOutputToContain(string $output): static
    {
        $this->unexpectedOutputContains[] = $output;
        return $this;
    }
    
    /**
     * Returns the unexpected output contains.
     *
     * @return array<int, string>
     */
    public function unexpectedOutputContains(): array
    {
        return $this->unexpectedOutputContains;
    }

    /**
     * Specify the table expected.
     *
     * @param array $headers
     * @param array $rows
     * @return static $this
     */
    public function expectsTable(array $headers, array $rows): static
    {
        $this->expectedTables[] = [$headers, $rows];
        return $this;
    }
    
    /**
     * Returns the expected tables.
     *
     * @return array
     */
    public function expectedTables(): array
    {
        return $this->expectedTables;
    }
    
    /**
     * Specify the question expected.
     *
     * @param string $question
     * @param mixed $answer
     * @return static $this
     */
    public function expectsQuestion(string $question, mixed $answer): static
    {
        $this->expectedQuestions[] = [$question, $answer];
        return $this;
    }
    
    /**
     * Returns the expected questions.
     *
     * @return array
     */
    public function expectedQuestions(): array
    {
        return $this->expectedQuestions;
    }
}