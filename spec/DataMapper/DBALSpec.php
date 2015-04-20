<?php namespace spec\DataMapper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use InvalidArgumentException;

class DBALSpec extends ObjectBehavior
{
    function it_creates_a_value_object_when_compatible_array_is_passed()
    {
        $this->beConstructedWith([]);
        $this->shouldHaveType('DataMapper\DBAL');
    }

    function it_will_throw_exception_if_array_is_ill_formed()
    {
        $this->beConstructedWith(
            ['dbname' => 'DBNAME']
        );
        $this->shouldThrow(
            new InvalidArgumentException('Missing parameter: user')
        );
    }

    function it_stores_values()
    {
        $this->beConstructedWith(
            [
                'dbname'   => 'DBNAME',
                'user'     => 'USER',
                'password' => 'PASSWORD',
                'host'     => 'HOST',
                'driver'   => 'DRIVER'
            ]
        );

        $this->dbname->shouldReturn('DBNAME');
        $this->user->shouldReturn('USER');
        $this->password->shouldReturn('PASSWORD');
        $this->host->shouldReturn('HOST');
        $this->driver->shouldReturn('DRIVER');
    }
}
