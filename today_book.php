<?php
    require_once "/usr/local/share/book/connect.php";
    $dbh = connectDB();
?>

<!DOCTYPE html>
<html lang="jp">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-166046229-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-166046229-1');
    </script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <title>本日の本</title>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>



<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">本日の本</a>
  <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="ナビゲーションの切替">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="soon_book.php">明日の本</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="book_search.php">本を探す</a>
      </li>
    </ul>
  </div>
</nav>

<br/><br/>





<div class="col-13 col-md-13 col-lg-7">
<?php
try{
    $sql='SELECT * FROM today_book WHERE 1 AND NOT (audience_type=22 AND (audience_code=01 OR audience_code=02  OR audience_code=03) )';            //すべてのスタッフの名前データ要求
    $stmt = $dbh->prepare($sql);
    $stmt->execute();                                     

    $dbh=null; 

    $count=$stmt->rowCount();
    if($count==0){
        echo'<p class="alert alert-danger">本日出版予定の本はありません。</p>';
        
    }else{
      echo'<p class="alert alert-success">本日出版予定の本は'.$count.'件あります。</p>';
    }

    while(true){                              
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);                //stmtから1レコード取り出す
        if($rec==false){                                    //取り出せるデータなくなったらbreakする
        break;
        }

        $imprint=$rec['imprint'];
        if(!empty($imprint)){
          $imprint="発行元出版社:".$imprint;
        }
        $publisher=$rec['publisher'];
        if(!empty($publisher)){
          $publisher=" / 販売元出版社:".$publisher;
        }
        $price=$rec['price'];
        if(!empty($price)){
          $price=" / 価格:".$price."円";
        }
        ?>
        




<ul class="list-group">
<li class="list-group-item">
<div class="mx-auto">
  <div class="media">
    <div class="media-left">
      <div class="d-none d-lg-block">
        <img class="mr-3" src=<?php echo $rec['picture'];?> alt=<?php echo $rec['title'];?> style="width:200px" >
      </div>
      <div class="d-lg-none">
        <img class="mr-3" src=<?php echo $rec['picture'];?> alt=<?php echo $rec['title'];?> style="width:100px" >
      </div>
    </div>
    <div class="media-body">
    <h4 class="media-heading">
      <h5><?php echo $rec['title']; ?></h5><p><?php echo $rec['contributor']."  ISBN:".$rec['isbn'];?></p><br/>
      <p><?php echo $rec['content'];?></p><br/>
      <p><?php echo $imprint.$publisher.$price;?></p>
      <a class="btn btn-primary" href= "<?php echo $rec['amazon_url'];?>" role="button" target="_blank">Amazon</a>
      <a class="btn btn-primary" href= "<?php echo $rec['honto_url'];?>" role="button" target="_blank">honto</a>
      <br/><br/>
    </div>
        
  </div>
    <br/>
</div>
</li>
</ul>


 <?php
    }    
}catch(Exception $e){

}

?>
</div>

</body>
</html>