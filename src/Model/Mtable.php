<?php

namespace App\Model;


use App\Lib\Config;
use App\Lib\Database;

/**
 * Class Mtable
 * mysql table
 * @package App\Model
 */
class Mtable
{
    /* @var Database */
    static private $db;
    static $tableName = null;

    protected $attributes;

    protected $old_attributes = null;

    static public $timestamp;


    static $primaryKey = 'id';


    /**
     * @return Database
     */
    public static function getDb()
    {
        if (!self::$db instanceof Database) {
            self::$db = new Database(Config::load('mysql'));
            self::$db->connect();
        }
        return self::$db;
    }

    public static function getTableName()
    {
        if (static::$tableName === null) {
            throw new \Exception('must set tableName');
        }
        return static::$tableName;
    }


    /**
     * @param array $conditions
     * @param bool $lock
     * @return Mtable|null
     * @throws \Exception
     */
    public static function findOne($conditions = [], $lock = false)
    {
        static::getDb()->db_apt->init('');
        $queryBuilder = static::getDb();
        foreach ($conditions as $key => $value) {
            $queryBuilder->where([$key, '=', $value]);
        }
        $queryBuilder->from(self::getTableName());
        if ($lock) {
            $queryBuilder->lock();
        }
        $data = $queryBuilder->one();
//        Log::write("findOne:".$queryBuilder->getSql()." data:".json_encode($data));
        if(empty($data)){
            return null;
        }
        return static::createModel($data);
    }


    protected static function createModel($row)
    {
        $model = new static();
        $model->old_attributes = $row;
        $model->load($row);
        return $model;
    }


    public function load($data)
    {
        foreach ($data as $key => $item) {
            $this->$key = $item;
        }
    }

    public function insert()
    {
        $ret = static::getDb()->insert($this->attributes, self::getTableName());
        if ($ret) {
            $this->attributes[self::$primaryKey] = static::getDb()->lastInsertId();
            $this->old_attributes = $this->attributes;
        }
        return $ret;
    }

    public function update()
    {
        $primaryKey = static::$primaryKey;

        $changedAttributes = [];

        foreach ($this->attributes as $name => $value) {
            if (isset($this->old_attributes[$name]) && $this->old_attributes[$name] !== $value) {
                $changedAttributes[$name] = $this->attributes[$name];
            }
        }

        if (empty($changedAttributes)) {
            return false;
        }

        $ret = static::getDb()->update($this->old_attributes[$primaryKey], $changedAttributes, self::getTableName());

        if ($ret) {
            foreach ($changedAttributes as $name => $value) {
                $this->old_attributes[$name] = $value;
            }
        }

        return $ret;
    }

    public function getIsNewRecord()
    {
        return $this->old_attributes === null;
    }

    public function save()
    {
        if ($this->getIsNewRecord()) {
            return $this->insert();
        } else {
            return $this->update() !== false;
        }
    }


    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return $this->attributes[$name];
    }


    public function attributes(){
       return $this->attributes;
    }



}