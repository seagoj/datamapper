<?php namespace spec\DataMapper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use DataMapper\DBAL;
use DataMapper\Connection;
use Devtools\Log;
use DataMapper\Query;

class ConnectionSpec extends ObjectBehavior
{
    function let(DBALMock $dbal, Log $log)
    {
        $this->beAnInstanceOf('spec\DataMapper\ConnectionMock');
        $this->beConstructedWith($dbal, $log);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('DataMapper\Connection');
    }

    function it_performs_query()
    {
        $this->result(
            new Query(
                'SELECT * FROM table WHERE col1 = :col1',
                ['col1' => 'val1']
            )
        )->shouldReturn(
            [
                ['col1'=> 'val1', 'col2' => 'val2'],
                ['col1'=> 'val1', 'col2' => 'val2']
            ]
        );
    }
}

class ConnectionMock extends Connection
{
    public function __construct(DBAL $dbal)
    {
        $dbal;
    }

    public function prepare($sql, $params = null)
    {
        if ($sql === 'SELECT * FROM table WHERE col1 = :col1'
            && $params = ['col1' => 'val1']
        ) {
            return new PDOStatementMock();
        }
    }

    public function errorInfo()
    {}

    public function getAttribute($attribute)
    {}
}

class PDOStatementMock
{
    public function execute()
    {}

    public function fetchAll()
    {
        return [
            ['col1' => 'val1', 'col2' => 'val2'],
            ['col1' => 'val1', 'col2' => 'val2']
        ];
    }

    public function errorInfo()
    {}
}

class DBALMock extends DBAL
{
    public $dbname   = 'DBNAME';
    public $user     = 'USER';
    public $password = 'PASSWORD';
    public $host     = 'HOST';
    public $driver   = 'mysql';
}
