<?php namespace spec\DataMapper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use DataMapper\DataMapper;
use DataMapper\Connection;
use DataMapper\Query;

class DataMapperSpec extends ObjectBehavior
{
    function let(Connection $connection)
    {
        $this->beAnInstanceOf('spec\DataMapper\DataMapperMock');
        $this->beConstructedWith($connection);
    }

    function it_adds_where_clause()
    {
        $this->where('col1', '=', 'val1')
            ->where('col2', '<', 'val2')
            ->shouldHaveType('DataMapper\DataMapper');

        $this->sql->where->shouldReturn(
            [
                ' WHERE col1 = :col1',
                ' AND WHERE col2 < :col2'
            ]
        );
        $this->params->shouldReturn(
            [
                'col1' => 'val1',
                'col2' => 'val2'
            ]
        );
    }

    function it_adds_and_where_clause()
    {
        $this->where('col1', '=', 'val1')
            ->andWhere('col2', '<', 'val2')
            ->shouldHaveType('DataMapper\DataMapper');

        $this->sql->where->shouldReturn(
            [
                ' WHERE col1 = :col1',
                ' AND WHERE col2 < :col2'
            ]
        );
        $this->params->shouldReturn(
            [
                'col1' => 'val1',
                'col2' => 'val2'
            ]
        );
    }

    function it_adds_or_where_clause()
    {
        $this->where('col1', '=', 'val1')
            ->orWhere('col2', '>', 'val2')
            ->shouldHaveType('DataMapper\DataMapper');

        $this->sql->where->shouldReturn(
            [
                ' WHERE col1 = :col1',
                ' OR WHERE col2 > :col2'
            ]
        );

        $this->params->shouldReturn(
            [
                'col1' => 'val1',
                'col2' => 'val2'
            ]
        );
    }

    function it_adds_order_by_clause()
    {
        $this->orderBy('col1', 'desc')
            ->orderBy('col2', 'asc')
            ->shouldHaveType('DataMapper\DataMapper');

        $this->sql->order->shouldReturn([' col1 DESC', ' col2 ASC']);
    }

    function it_adds_limit()
    {
        $this->limit(10);
        $this->sql->limit->shouldReturn(' LIMIT 10');

        $this->limit(10, 20);
        $this->sql->limit->shouldReturn(' LIMIT 10 OFFSET 19');
    }

    function it_adds_join()
    {
        $this->join('table2', ['table2.col1', '=', 'table.col1']);
        $this->sql->join->shouldReturn(
            [' JOIN table2 ON table2.col1 = table.col1']
        );
    }

    function it_performs_query($connection)
    {
        $connection->result(
            new Query(
                'SELECT * FROM table JOIN table2 ON table2.col1 = table.col1 WHERE col1 = :col1 OR WHERE col2 > :col2',
                ['col1' => 'val1', 'col2' => 'val2']
            )
        )->willReturn(
            ['col1'=>'val1', 'col2'=>'val2']
        );

        $this->join('table2', ['table2.col1', '=', 'table.col1'])
            ->where('col1', '=', 'val1')
            ->orWhere('col2', '>', 'val2')
            ->run()
            ->shouldReturn(['col1'=>'val1', 'col2'=>'val2']);
    }
}

class DataMapperMock extends DataMapper
{
    protected $table = 'table';
}
