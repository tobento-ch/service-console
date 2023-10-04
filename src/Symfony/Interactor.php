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

namespace Tobento\Service\Console\Symfony;

use Tobento\Service\Console\InteractorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Stringable;

/**
 * Interactor
 */
class Interactor implements InteractorInterface
{
    /**
     * @var SymfonyStyle
     */
    protected SymfonyStyle $style;
    
    /**
     * Create a new Interactor.
     *
     * @param Command $command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param null|SymfonyStyle $style
     */
    public function __construct(
        protected Command $command,
        protected InputInterface $input,
        protected OutputInterface $output,
        null|SymfonyStyle $style = null,
    ) {
        $this->style = $style ?: new SymfonyStyle($input, $output);
    }
    
    /**
     * Returns the argument value by name.
     *
     * @param string $name
     * @return mixed
     */
    public function argument(string $name): mixed
    {
        return $this->input->getArgument($name);
    }
    
    /**
     * Returns all the arguments values indexed by its name.
     *
     * @return array
     */
    public function arguments(): array
    {
        return $this->input->getArguments();
    }
    
    /**
     * Returns the option value by name.
     *
     * @param string $name
     * @return mixed
     */
    public function option(string $name): mixed
    {
        return $this->input->getOption($name);
    }
    
    /**
     * Returns all the option values indexed by its name.
     *
     * @return array
     */
    public function options(): array
    {
        return $this->input->getOptions();
    }

    /**
     * Returns true if matches the specified verbosity level, otherwise false.
     *
     * @param string $level
     * @return bool
     */
    public function isVerbose(string $level): bool
    {
        return match ($level) {
            'quiet' => $this->output->isQuiet(),
            'normal' => ! $this->output->isQuiet() && ! $this->output->isVerbose(),
            'v' => $this->output->isVerbose(),
            'vv' => $this->output->isVeryVerbose(),
            'vvv' => $this->output->isDebug(),
            default => false,
        };
    }
    
    /**
     * Write a message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function write(string|Stringable $message, mixed ...$options): static
    {
        $opt = match (true) {
            in_array('v', $options) => OutputInterface::VERBOSITY_VERBOSE,
            in_array('vv', $options) => OutputInterface::VERBOSITY_VERY_VERBOSE,
            in_array('vvv', $options) => OutputInterface::VERBOSITY_DEBUG,
            default => 0,
        };
        
        $this->output->write((string)$message, options: $opt);
        
        // newline
        //if ()
        
        return $this;
    }
    
    /**
     * Write a blank new line.
     *
     * @param int $num The number of new lines.
     * @return static $this
     */
    public function newLine(int $num = 1): static
    {
        $this->style->newLine($num);
        return $this;
    }
    
    /**
     * Write an info message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function info(string|Stringable $message, mixed ...$options): static
    {
        $this->style->info((string)$message);
        return $this;
    }
    
    /**
     * Write an comment message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function comment(string|Stringable $message, mixed ...$options): static
    {
        $this->style->comment((string)$message);
        return $this;
    }
    
    /**
     * Write a warning message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function warning(string|Stringable $message, mixed ...$options): static
    {
        $this->style->warning((string)$message);
        return $this;
    }
    
    /**
     * Write an error message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function error(string|Stringable $message, mixed ...$options): static
    {
        $this->style->error((string)$message);
        return $this;
    }
    
    /**
     * Write a success message.
     *
     * @param string|Stringable $message
     * @param mixed ...$options
     * @return static $this
     */
    public function success(string|Stringable $message, mixed ...$options): static
    {
        $this->style->success((string)$message);
        return $this;
    }
    
    /**
     * Writing a table.
     *
     * @param array $headers
     * @param array $rows
     * @return void
     */    
    public function table(array $headers, array $rows): void
    {
        $this->style->table($headers, $rows);
    }
    
    /**
     * Asking a question returning its value.
     *
     * @param string $question
     * @param mixed $default
     * @param mixed ...$options
     * @return mixed
     */
    public function ask(string $question, mixed $default = null, mixed ...$options): mixed
    {
        $question = new Question($question, is_scalar($default) ? $default : null);
        $question->setValidator($options['validator'] ?? null);
        $question->setMaxAttempts($options['attempts'] ?? null);
        
        return $this->style->askQuestion($question);
    }
    
    /**
     * Asking a secret returning its value.
     *
     * @param string $question
     * @param mixed ...$options
     * @return mixed
     */
    public function secret(string $question, mixed ...$options): mixed
    {
        $question = new Question($question);
        $question->setHidden(true);
        $question->setValidator($options['validator'] ?? null);
        $question->setMaxAttempts($options['attempts'] ?? null);
        
        return $this->style->askQuestion($question);
    }
    
    /**
     * Asking a confirm question returning its value.
     *
     * @param string $question
     * @param bool $default
     * @param mixed ...$options
     * @return mixed
     */
    public function confirm(string $question, bool $default = true, mixed ...$options): mixed
    {
        return $this->style->confirm($question, $default);
    }
    
    /**
     * Asking a choice question returning its value.
     *
     * @param string $question
     * @param array $choices
     * @param mixed $default
     * @param mixed ...$options
     * @return mixed
     */
    public function choice(string $question, array $choices, mixed $default = null, mixed ...$options): mixed
    {
        if (null !== $default) {
            $values = array_flip($choices);
            $default = $values[$default] ?? $default;
        }
        
        $question = new ChoiceQuestion($question, $choices, $default);
        $question->setMultiselect($options['multiselect'] ?? false);
        
        return $this->style->askQuestion($question);
    }
    
    /**
     * Starts a progress.
     *
     * @param int $max
     * @return void
     */
    public function progressStart(int $max = 0): void
    {
        $this->style->progressStart($max);
    }

    /**
     * Advances the progress started X steps.
     *
     * @param int $step
     * @return void
     */
    public function progressAdvance(int $step = 1): void
    {
        $this->style->progressAdvance($step);
    }

    /**
     * Finishes the progress started.
     *
     * @return void
     */
    public function progressFinish(): void
    {
        $this->style->progressFinish();
    }
    
    /**
     * Returns the symfony input.
     *
     * @return InputInterface
     */
    public function input(): InputInterface
    {
        return $this->input;
    }
    
    /**
     * Returns the symfony output.
     *
     * @return OutputInterface
     */
    public function output(): OutputInterface
    {
        return $this->output;
    }
}