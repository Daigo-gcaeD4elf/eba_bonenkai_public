<?php
require_once('DbPass.php');

class Db
{
    protected $dbh;

    /**
     *  DBへ接続
     */
    public function __construct()
    {
        try {
            $this->dbh = new PDO('mysql:host=' . HOST. ';dbname='.DBNAME, DBUSER, DBPASS);
            $this->dbh->exec('set names utf8');
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e){

            echo 'Db.php connect() 「 DB接続失敗(´;ω;｀) 」<br>';
            return false;
        }
    }
}
