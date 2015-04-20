<?php namespace DataMapper;

use PDO;
use Devtools\Log;
use DataMapper\Query;

class Connection extends PDO
{
    public function __construct(DBAL $dbal, Log $log)
    {
        $this->log = $log;

        parent::__construct(
            self::dsn($dbal),
            $dbal->user,
            $dbal->password
        );
    }

    private function logOnError($response, Query $query)
    {
        if (!is_null($response[1])) {
            $this->log->write(
                "==========\n{$response}\n{$query->sql}\n{$query->params}=========="
            );
        }
    }

    public function result(Query $query, $reduce = false)
    {
        $stmt = $this->prepare($query->sql);
        $executionResult = !is_null($query->params)
            ? $stmt->execute($query->params)
            : $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->logOnError($this->errorInfo(), $query);
        $this->logOnError($stmt->errorInfo(), $query);
        $this->prepareResponseData($data, $executionResult);

        return $reduce
            ? self::reduceResult($data)
            : $data;
    }

    private function prepareResponseData(&$data, $executionResult)
    {
        if (empty($data)) {
            switch($this->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mssql':
            case 'dblib':
            case 'firebird':
                break;
            default:
                $isInsertStatement = $executionResult
                    && ($lastInsertId = $this->lastInsertId()) != 0;

                $data = $isInsertStatement
                    ?  array('insert_id' => $lastInsertId)
                    :  $executionResult;
                break;
            }
        }
    }

    private static function dsn(DBAL $dbal)
    {
        switch($dbal->driver) {
        case 'mysql':
            $template = '%s:host=%s;dbname=%s';
            break;
        case 'firebird':
            $template = '%s:dbname=%s:%s';
            break;
        case 'dblib':
            $template = '%s:host=%s;%s';
            break;
        }

        return sprintf(
            $template,
            $dbal->driver,
            $dbal->host,
            $dbal->dbname
        );
    }
}
