<?php namespace DataMapper;

use InvalidArgumentException;

class DBAL
{
    public function __construct(Array $options)
    {
        $required = array(
            'dbname',
            'user',
            'password',
            'host',
            'driver'
        );

        $keys = array_keys($options);
        foreach ($required as $field) {
            if (!in_array($field, $keys)) {
                throw new InvalidArgumentException("Missing parameter: {$field}");
            }
            $this->$field = $options[$field];
        }
    }

    public function __get($property)
    {
        return $this->$property;
    }
}
