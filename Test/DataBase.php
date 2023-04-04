<?php

use yasmf\DataSource;
class DataBase
{
    
    public static function getPDOTest() {
        try{
            $dataSource = new DataSource(
                $host = 'localhost',
                $port = '3306', # to change with the port your mySql server listen to
                $db = 'check_your_mood_test', # to change with your db name
                $user = 'root', # to change with your db user name
                $pass = 'root', # to change with your db password
                $charset = 'utf8mb4'
            );
            return $dataSource->getPDO();
        }catch(PDOException $e){
            $e->getMessage();
            return null;
        }
        

    }

    
}