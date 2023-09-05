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
use Tobento\Service\Console\SignatureParser;
use Tobento\Service\Console\Input;
use InvalidArgumentException;

class SignatureParserTest extends TestCase
{
    public function testNameAndDescMethod()
    {
        $this->assertSame(
            ['command', 'desc'],
            SignatureParser::nameAndDesc('command | desc')
        );
        
        $this->assertSame(
            ['command:name', 'desc'],
            SignatureParser::nameAndDesc('command:name | desc')
        );
        
        $this->assertSame(
            ['command:name', ''],
            SignatureParser::nameAndDesc('command:name')
        );
        
        $this->assertSame(
            ['command', ''],
            SignatureParser::nameAndDesc('command')
        );        
        
        $this->assertSame(
            ['command:name', 'desc'],
            SignatureParser::nameAndDesc('command:name | desc {argument} {--option}')
        );        
        
        $this->assertSame(
            ['command:name', ''],
            SignatureParser::nameAndDesc('command:name {argument} {--option}')
        );
        
        $this->assertSame(
            ['command:name', 'desc'],
            SignatureParser::nameAndDesc('command:name|desc{argument}{--option}')
        );
        
        $this->assertSame(
            ['command:name', 'desc'],
            SignatureParser::nameAndDesc('     command:name   |   desc      ')
        );
    }
    
    public function testNameAndDescMethodThrowsExceptionIfEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        
        SignatureParser::nameAndDesc('');
    }
    
    public function testParametersMethodEmpty()
    {
        $this->assertNull(SignatureParser::parameters('command')->first());
        $this->assertNull(SignatureParser::parameters('command | desc')->first());
        $this->assertNull(SignatureParser::parameters('  command | desc  ')->first());
    }
    
    public function testParametersMethodArgument()
    {
        $params = SignatureParser::parameters('command {argument}');
        $this->assertInstanceof(Input\Argument::class, $params->first());
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertFalse($params->first()->optional());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument?}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument[]}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertFalse($params->first()->optional());
        $this->assertTrue($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument[]?}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertTrue($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument=}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument=foo}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('', $params->first()->description());
        $this->assertSame('foo', $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument=[foo,bar]}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('', $params->first()->description());
        $this->assertSame(['foo', 'bar'], $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertTrue($params->first()->variadic());
    }
    
    public function testParametersMethodArgumentWithDesc()
    {
        $params = SignatureParser::parameters('command {argument : desc}');
        $this->assertInstanceof(Input\Argument::class, $params->first());
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertFalse($params->first()->optional());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument? : desc}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument[] : desc}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertFalse($params->first()->optional());
        $this->assertTrue($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument[]? : desc}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertTrue($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument= : desc}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument=foo : desc}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame('foo', $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {argument=[foo,bar] : desc}');
        $this->assertSame('argument', $params->first()->name());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame(['foo', 'bar'], $params->first()->value());
        $this->assertTrue($params->first()->optional());
        $this->assertTrue($params->first()->variadic());
    }
    
    public function testParametersMethodOption()
    {
        $params = SignatureParser::parameters('command {--option}');
        $this->assertInstanceof(Input\Option::class, $params->first());
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('', $params->first()->description());
        $this->assertNull($params->first()->value());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {--o|option}');
        $this->assertSame('option', $params->first()->name());
        $this->assertSame('o', $params->first()->shortName());
        
        $params = SignatureParser::parameters('command {--option=}');
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('', $params->first()->description());
        $this->assertNull($params->first()->value());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {--option=foo}');
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('', $params->first()->description());
        $this->assertSame('foo', $params->first()->value());
        $this->assertFalse($params->first()->variadic());

        $params = SignatureParser::parameters('command {--option=[]}');
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertTrue($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {--option=[foo,bar]}');
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('', $params->first()->description());
        $this->assertSame(['foo', 'bar'], $params->first()->value());
        $this->assertTrue($params->first()->variadic());
    }
    
    public function testParametersMethodOptionWithDesc()
    {
        $params = SignatureParser::parameters('command {--option : desc}');
        $this->assertInstanceof(Input\Option::class, $params->first());
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('desc', $params->first()->description());
        $this->assertNull($params->first()->value());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {--o|option : desc}');
        $this->assertSame('option', $params->first()->name());
        $this->assertSame('o', $params->first()->shortName());
        $this->assertSame('desc', $params->first()->description());
        
        $params = SignatureParser::parameters('command {--option= : desc}');
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('desc', $params->first()->description());
        $this->assertNull($params->first()->value());
        $this->assertFalse($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {--option=foo : desc}');
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame('foo', $params->first()->value());
        $this->assertFalse($params->first()->variadic());

        $params = SignatureParser::parameters('command {--option=[] : desc}');
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame(null, $params->first()->value());
        $this->assertTrue($params->first()->variadic());
        
        $params = SignatureParser::parameters('command {--option=[foo,bar] : desc}');
        $this->assertSame('option', $params->first()->name());
        $this->assertNull($params->first()->shortName());
        $this->assertSame('desc', $params->first()->description());
        $this->assertSame(['foo', 'bar'], $params->first()->value());
        $this->assertTrue($params->first()->variadic());
    }
    
    public function testParametersMethodMultiple()
    {
        $params = SignatureParser::parameters('command {arg1} {arg2} {--opt1} {--opt2} {--opt3}');
        $this->assertSame(2, count($params->name(Input\Argument::class)->all()));
        $this->assertSame(3, count($params->name(Input\Option::class)->all()));
    }
}