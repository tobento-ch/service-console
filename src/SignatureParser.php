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

namespace Tobento\Service\Console;

use Tobento\Service\Console\Input\Argument;
use Tobento\Service\Console\Input\Option;
use InvalidArgumentException;

/**
 * SignatureParser
 */
class SignatureParser
{
    /**
     * Extract the name and description of the command from the signature.
     *
     * @param  string $signature
     * @return array
     * @throws InvalidArgumentException
     */
    public static function nameAndDesc(string $signature): array
    {
        $segments = explode('{', $signature);
        $names = explode('|', trim($segments[0]));
        $name = trim($names[0]);
        
        if (empty($name)) {
            throw new InvalidArgumentException('Unable to determine command name from signature.');
        }

        return [$name, trim($names[1] ?? '')];
    }
    
    /**
     * Extract the parameters from the signature.
     *
     * @param  string $signature
     * @return ParametersInterface
     */    
    public static function parameters(string $signature): ParametersInterface
    {
        if (preg_match_all('/\{\s*(.*?)\s*\}/', $signature, $matches) && count($matches[1])) {
            return static::createParameters($matches[1]);
        }
        
        return new Parameters();
    }
    
    /**
     * Create parameters from tokens.
     *
     * @param array $tokens
     * @return ParametersInterface
     */
    private static function createParameters(array $tokens): ParametersInterface
    {
        $parameters = new Parameters();

        foreach ($tokens as $token) {
            if (preg_match('/-{2,}(.*)/', $token, $matches)) {
                $parameters->add(static::createOption($matches[1]));
            } else {
                $parameters->add(static::createArgument($token));
            }
        }

        return $parameters;
    }
    
    /**
     * Create argument from token.
     *
     * @param string $token
     * @return Argument
     */
    private static function createArgument(string $token): Argument
    {
        [$token, $description] = static::extractTokenAndDescription($token);

        return match (true) {
            // name[]?
            str_ends_with($token, '[]?') => new Argument(
                name: rtrim($token, '[]?'),
                description: $description,
                optional: true,
                variadic: true,
            ),
            // name[]
            str_ends_with($token, '[]') => new Argument(
                name: rtrim($token, '[]'),
                description: $description,
                optional: false,
                variadic: true,
            ),
            // name?
            str_ends_with($token, '?') => new Argument(
                name: rtrim($token, '?'),
                description: $description,
                optional: true,
                variadic: false,
            ),
            // name=[foo,bar]
            (bool)preg_match('/(.+)\=\[(.+)\]/', $token, $matches) => new Argument(
                name: $matches[1],
                description: $description,
                value: preg_split('/,\s?/', $matches[2]),
                optional: true,
                variadic: true,
            ),
            // name=foo or name=
            (bool)preg_match('/(.+)\=(.+)?/', $token, $matches) => new Argument(
                name: $matches[1],
                description: $description,
                value: $matches[2] ?? null,
                optional: true,
                variadic: false,
            ),
            // name
            default => new Argument(
                name: $token,
                description: $description,
                optional: false,
                variadic: false,
            ),
        };
    }
    
    /**
     * Create option from token.
     *
     * @param string $token
     * @return Option
     */
    private static function createOption(string $token): Option
    {
        [$token, $description] = static::extractTokenAndDescription($token);

        $matches = \preg_split('/\s*\|\s*/', $token, 2);
        $shortName = null;

        if (isset($matches[1])) {
            [$shortName, $token] = $matches;
            $shortName = ltrim($shortName, '--');
        }
        
        $token = ltrim($token, '--');

        return match (true) {
            // name=[]
            str_ends_with($token, '=[]') => new Option(
                name: rtrim($token, '=[]'),
                shortName: $shortName,
                description: $description,
                variadic: true,
            ),            
            // name[]
            str_ends_with($token, '[]') => new Option(
                name: rtrim($token, '[]'),
                shortName: $shortName,
                description: $description,
                variadic: true,
            ),
            // name=
            str_ends_with($token, '=') => new Option(
                name: rtrim($token, '='),
                shortName: $shortName,
                description: $description,
                variadic: false,
            ),        
            // name=[foo,bar]
            (bool)preg_match('/(.+)\=\[(.+)\]/', $token, $matches) => new Option(
                name: $matches[1],
                shortName: $shortName,
                description: $description,
                value: preg_split('/,\s?/', $matches[2]),
                variadic: true,
            ),
            // name=foo
            (bool)preg_match('/(.+)\=(.+)/', $token, $matches) => new Option(
                name: $matches[1],
                shortName: $shortName,
                description: $description,
                value: $matches[2],
                variadic: false,
            ),
            // name
            default => new Option(
                name: $token,
                shortName: $shortName,
                description: $description,
                variadic: null,
            ),
        };
    }
    
    /**
     * Parse the token into its token and description segments.
     *
     * @param string $token
     * @return array
     */
    private static function extractTokenAndDescription(string $token): array
    {
        $parts = preg_split('/\s+:\s+/', trim($token), 2);

        return count($parts) === 2 ? $parts : [$token, ''];
    }
}