<?php


// データベースに接続
function connectDB() {
    $dsn = 'mysql:dbname=book_DB;host=localhost;charset=utf8'; //データベース名とホスト名
    $user='root';   //データベースのユーザー名
    $password='1984477';    //データベースのパスワード
    try {
        $dbh = new PDO($dsn, $user,$password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $dbh;

    } catch (PDOException $e) {         //  データベースに接続出来なかった場合の処理
        print'エラー';      //  エラーメッセージを表示して終了
        exit();
    }
}

?>