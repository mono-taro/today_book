<?php
    require_once "/usr/local/connect.php";
    $dbh = connectDB();
?>

<!DOCTYPE html>
<html lang="jp">
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-166046229-2"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-166046229-2');
  </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <title>明日の本</title>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>



<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">明日の本</a>
  <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="ナビゲーションの切替">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="today_book.php">本日の本</a>
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
    $sql='SELECT * FROM soon_book WHERE 1 AND NOT (audience_type=22 AND (audience_code=01 OR audience_code=02  OR audience_code=03) )';            //すべてのスタッフの名前データ要求
    $stmt = $dbh->prepare($sql);
    $stmt->execute();                                     

    $dbh=null; 

    $count=$stmt->rowCount();
    if($count==0){
        echo'<p class="alert alert-danger">明日出版予定の本はありません。</p>';
        
    }else{
      echo'<p class="alert alert-success">明日出版予定の本は'.$count.'件あります。</p>';
    }

    while(true){                              
        $rec=$stmt->fetch(PDO::FETCH_ASSOC);                //stmtから1レコード取り出す
        if($rec==false){                                    //取り出せるデータなくなったらbreakする
        break;
        }

        $result_imprint=htmlspecialchars($rec['imprint'],ENT_QUOTES,'UTF-8');
        $result_publisher=htmlspecialchars($rec['publisher'],ENT_QUOTES,'UTF-8');
        $result_price=htmlspecialchars($rec['price'],ENT_QUOTES,'UTF-8');
        $result_data=htmlspecialchars($rec['data'],ENT_QUOTES,'UTF-8');
        $result_picture=htmlspecialchars($rec['picture'],ENT_QUOTES,'UTF-8');
        $result_title=htmlspecialchars($rec['title'],ENT_QUOTES,'UTF-8');
        $result_contributor=htmlspecialchars($rec['contributor'],ENT_QUOTES,'UTF-8');
        $result_isbn=htmlspecialchars($rec['isbn'],ENT_QUOTES,'UTF-8');
        $result_content=htmlspecialchars($rec['content'],ENT_QUOTES,'UTF-8');
        $result_amazon_url=htmlspecialchars($rec['amazon_url'],ENT_QUOTES,'UTF-8');
        $result_honto_url=htmlspecialchars($rec['honto_url'],ENT_QUOTES,'UTF-8');

        if(!empty($$result_imprint)){
            $result_imprint="発行元出版社:".$result_imprint;
        }
        if(!empty($result_publisher)){
            $result_publisher=" / 販売元出版社:". $result_publisher;
        }
        if(!empty($result_price)){
            $result_price=" / 価格:".$result_price."円";
        }
        if(!empty($result_date)){
            $result_data=date('Y年m月d日',strtotime($result_data));
            $result_data = " / 出版日:".$result_data;
        }
        
        ?>
        




        <ul class="list-group">
        <li class="list-group-item">
        <div class="mx-auto">
          <div class="media">
            <div class="media-left">
              <div class="d-none d-lg-block">
                <img class="mr-3" src=<?php echo $result_picture;?> alt=<?php echo $result_title;?> style="width:200px" >
              </div>
              <div class="d-lg-none">
                <img class="mr-3" src=<?php echo $result_picture;?> alt=<?php echo $result_title;?> style="width:100px" >
              </div>
            </div>
            <div class="media-body">
              <h4 class="media-heading">
              <h5><?php echo $result_title; ?></h5><p><?php echo $result_contributor."  ISBN:".$result_isbn;?></p><br/>
              <p><?php echo $result_content;?></p><br/>
              <p><?php echo $result_imprint. $result_publisher.$result_price.$result_data;?></p>
              <a class="btn btn-primary" href= "<?php echo $result_amazon_url;?>" role="button" target="_blank">Amazon</a>
              <a class="btn btn-primary" href= "<?php echo $result_honto_url;?>" role="button" target="_blank">honto</a>
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