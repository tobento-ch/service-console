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
use Tobento\Service\Console\Parameters;
use Tobento\Service\Console\ParametersInterface;
use Tobento\Service\Console\ParameterInterface;
use Tobento\Service\Console\Input;

class ParametersTest extends TestCase
{
    public function testCreateParameters()
    {
        $parameters = new Parameters(
            new Input\Option(name: 'option'),
        );
        
        $this->assertInstanceof(ParametersInterface::class, $parameters);
    }
    
    public function testAddMethod()
    {
        $foo = new Input\Option(name: 'foo');
        $bar = new Input\Option(name: 'bar');
        
        $parameters = (new Parameters())->add($foo)->add($bar);
        
        $this->assertTrue($foo === ($parameters->all()[0] ?? null));
        $this->assertTrue($bar === ($parameters->all()[1] ?? null));
        $this->assertSame(2, count($parameters->all()));
    }
    
    public function testFilterMethod()
    {
        $parameters = new Parameters(
            new Input\Argument(name: 'foo'),
            new Input\Option(name: 'bar'),
        );
        
        $parametersNew = $parameters->filter(
            fn(ParameterInterface $p): bool => $p instanceof Input\Argument
        );
        
        $this->assertFalse($parameters === $parametersNew);
        $this->assertSame(1, count($parametersNew->all()));
        $this->assertSame(2, count($parameters->all()));
    }
    
    public function testGetMethod()
    {
        $foo = new Input\Option(name: 'foo');
        
        $parameters = new Parameters($foo);
        
        $this->assertTrue($foo === $parameters->get(Input\Option::class));
        $this->assertNull($parameters->get(Input\Argument::class));
    }
    
    public function testNameMethod()
    {
        $parameters = new Parameters(
            new Input\Argument(name: 'foo'),
            new Input\Option(name: 'bar'),
        );
        
        $parametersNew = $parameters->name(Input\Option::class);
        
        $this->assertFalse($parameters === $parametersNew);
        $this->assertSame(1, count($parametersNew->all()));
        $this->assertSame(2, count($parameters->all()));
    }
    
    public function testFirstMethod()
    {
        $parameters = new Parameters(
            new Input\Option(name: 'foo'),
            new Input\Option(name: 'bar'),
        );
        
        $this->assertInstanceof(ParameterInterface::class, $parameters->first());
        
        $parameters = new Parameters();
        
        $this->assertSame(null, $parameters->first());
    }    
    
    public function testAllMethod()
    {
        $parameters = new Parameters();
        
        $this->assertSame(0, count($parameters->all()));
        
        $parameters = new Parameters(
            new Input\Option(name: 'foo'),
        );
        
        $this->assertSame(1, count($parameters->all()));
    }
    
    public function testIteration()
    {
        $parameters = new Parameters(
            new Input\Option(name: 'foo'),
            new Input\Option(name: 'bar'),
        );
        
        foreach($parameters as $parameter) {
            $this->assertInstanceof(ParameterInterface::class, $parameter);
        }
    }
}