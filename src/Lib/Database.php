<?php

namespace App\Lib;

use App\Lib\Database\Mysqli;
use App\Lib\Database\PdoDB;

/**
 * Database类，处理数据库连接和基本的SQL组合
 * 提供4种接口，query  insert update delete
 * @method connect
 * @method close
 * @method quote $str
 * @method errno
 */
class Database
{
    public $debug = false;
    public $read_times = 0;
    public $write_times = 0;


    public $tran_lock = false;

    /**
     * @var IDatabase
     */
    public $_db = null;
    /**
     * @var \Swoole\SelectDB
     */
    public $db_apt = null;
    protected $lastSql = '';
//    const TYPE_MYSQL   = 1;
    const TYPE_MYSQLi = 'mysqli';
//    const TYPE_PDO     = 3;
    /**
     * 协程版本
     */
//    const TYPE_COMYSQL = 4;

    function __construct($db_config)
    {

        switch ($db_config['type']) {
            case self::TYPE_MYSQLi:
                $this->_db = new MySQLi($db_config);
                break;
            default:
                $dsn = "mysql" . ":host=" . $db_config['host'] . ";dbname=" . $db_config['database'];
                $this->_db = new PdoDB($dsn, $db_config['user'], $db_config['password'], array(\PDO::ATTR_PERSISTENT => true));
                break;
        }
        $this->db_apt = new SelectDB($this);
    }

    /**
     * 初始化参数
     */
    function __init()
    {
        $this->check_status();
        $this->db_apt->init();
        $this->read_times = 0;
        $this->write_times = 0;
    }

    /**
     * 检查连接状态，如果连接断开，则重新连接
     */
    function check_status()
    {
        if (!$this->_db->ping()) {
            $this->_db->close();
            $this->_db->connect();
        }
    }

    /**
     * 启动事务处理
     * @return bool
     */
    function start()
    {
        if ($this->tran_lock) {
            return false;
        }
        if ($this->query('set autocommit = 0') === false) {
            return false;
        }
        return $this->query('START TRANSACTION');
    }

    /**
     * 提交事务处理
     * @return bool
     */
    function commit()
    {
        if ($this->tran_lock) {
            return false;
        }
        if ($this->query('COMMIT') === false) {
            return false;
        }
        $this->query('set autocommit = 1');
        return true;
    }

    /**
     * 事务回滚
     * @return bool
     */
    function rollback()
    {
        if ($this->tran_lock) {
            return false;
        }
        if ($this->query('ROLLBACK') === false) {
            return false;
        }
        $this->query('set autocommit = 1');
        return true;
    }

    /**
     * 执行一条SQL语句
     * @param $sql
     * @return \Swoole\Database\MySQLiRecord
     */
    public function query($sql)
    {
        if ($this->debug) {
            echo "$sql<br />\n<hr />";
        }
        $this->read_times += 1;
        $this->lastSql = $sql;
        return $this->_db->query($sql);
    }

    /**
     * 插入$data数据库的表$table，$data必须是键值对应的，$key是数据库的字段，$value是对应的值
     * @param $data
     * @param $table
     * @return int
     */
    public function insert($data, $table)
    {
        $this->db_apt->init();
        $this->db_apt->from($table);
        $this->write_times += 1;
        return $this->db_apt->insert($data);
    }

    /**
     * 批量插入$data数据库的表$table，$data为数据记录列表
     * @param $fields array
     * @param $data array
     * @param $table string
     * @return int
     */
    public function insertBatch(array $fields, array $data, $table)
    {
        $this->db_apt->init();
        $this->db_apt->from($table);
        $this->write_times += 1;
        return $this->db_apt->insertBatch($fields, $data);
    }

    /**
     * 从$table删除一条$where为$id的记录
     * @param $id
     * @param $table
     * @param $where
     * @return \mysqli_result
     */
    public function delete($id, $table, $where = 'id')
    {
        if (func_num_args() < 2) {
            Error::info('SelectDB param error', 'Delete must have 2 paramers ($id,$table) !');
        }
        $this->db_apt->init();
        $this->db_apt->from($table);
        $this->write_times += 1;
        return $this->query("delete from $table where $where='$id'");
    }

    /**
     * 执行数据库更新操作，参数为主键ID，值$data，必须是键值对应的
     * @param int $id 主键ID
     * @param array $data 数据
     * @param string $table 表名
     * @param string $where 其他字段
     * @return int $n     SQL语句的返回值
     */
    public function update($id, $data, $table, $where = 'id')
    {
        if (func_num_args() < 3) {
            echo Error::info('SelectDB param error', 'Update must have 3 paramers ($id,$data,$table) !');
            return false;
        }
        $this->db_apt->init();
        $this->db_apt->from($table);
        $this->db_apt->where("$where='$id'");
        $this->write_times += 1;
        return $this->db_apt->update($data);
    }

    /**
     * 根据主键获取单条数据
     * @param $id
     * @param $table
     * @param $primary
     * @return array
     */
    public function get($id, $table, $primary = 'id')
    {
        $this->db_apt->init();
        $this->db_apt->from($table);
        $this->db_apt->where("$primary='$id'");
        return $this->db_apt->getone();
    }

    /**
     * 调用$driver的自带方法
     * @param $method
     * @param array $args
     * @return mixed
     */
    function __call($method, $args = array())
    {
        return $this->_db->{$method}(...$args);
    }

    /**
     * @return $this
     */
    function where($condition)
    {
        $this->db_apt->where(...$condition);
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    function from($table)
    {
        $this->db_apt->from($table);
        return $this;
    }

    /**
     * @param string $field
     * @return array
     */
    function one($field = '')
    {
        return $this->db_apt->getone($field);
    }

    function all()
    {
        return $this->db_apt->getall();
    }


    function getSql()
    {
        return $this->db_apt->getsql();
    }

    function lastInsertId()
    {
        return $this->_db->lastInsertId();
    }

    function lock()
    {
        return $this->db_apt->lock();
    }
}