<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/26
 * Time: 9:41
 */

namespace Test;


use App\Model\Mtable;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase{
    use TestCaseTrait { setUp as setUpTC; }

    private $conn;
    static private  $pdo;

    public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = Mtable::getDb()->_db;
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, ':memory:');
        }
        return $this->conn;
    }

    public function getDataSet()
    {
        return new YamlDataSet(TEST_PATH."_data".DS.'player_task.yml');
    }


    protected function setUp()
    {
        $this->setUpTC();
        $this->setUpTrait();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->tearDownTrait();
    }

    protected function setUpTrait(){
        if(method_exists($this,'beginDatabaseTransaction')){
            $this->beginDatabaseTransaction();
        }
    }

    private function tearDownTrait(){
        if(method_exists($this,'rollBackDatabaseTransaction')){
            $this->rollBackDatabaseTransaction();
        }
    }
}