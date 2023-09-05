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

namespace Tobento\Service\Console\Test\Mock;

use Tobento\Service\Console\AbstractCommand;
use Tobento\Service\Console\InteractorInterface;

class CommandSignature extends AbstractCommand
{
    /**
     * The command usage text.
     */
    public const USAGE = 'usage';
    
    /**
     * The signature of the console command.
     */
    public const SIGNATURE = '
        command:signature | desc
        {arg : desc}
        {--opt= : desc}
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
        $io->write(sprintf(
            'arg:%s opt:%s',
            $io->argument('arg'),
            $io->option('opt'),
        ));
        
        return 0;
    }
}