<?php

namespace Test;

use App\Model\Mtable;

trait DatabaseTransactions
{
    /**
     * Handle database transactions on the specified connections.
     *
     * @return void
     */
    public function beginDatabaseTransaction()
    {
        $database = Mtable::getDb();
        $database->start();
        //测试里面的事务失效
        $database->tran_lock = true;
    }


    public function rollBackDatabaseTransaction(){
        $database = Mtable::getDb();
        $database->tran_lock = false;
        $database->rollback();
    }

}
