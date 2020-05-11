<?php

require_once "/usr/local/connect.php";


    $dbh = connectDB();
    $where=null;



    //受け取ったキーワードの全角スペースを半角スペースに変換する
    $title = str_replace("　", " ", $_GET['title']);
    //キーワードを空白で分割する
    $title_array = explode(" ",$title);
    $title_array = array_filter($title_array);

    //入力された検索条件からSQl文を生成 
    if (!empty($_GET['title'])){
        $where .="(";


        for($i = 0; $i <count($title_array);$i++){  //単語が一つ以上ある場合
            $where .= 'title LIKE "%'.$title_array[$i].'%" OR  subtitle LIKE "%'.$title_array[$i].'%"';

            if ($i <count($title_array) -1){    //単語が2つ以上あった場合"OR"を挟む
                $where .= " OR ";
            }
        }
        $where .=")";
    }


    if (!empty($_GET['content'])){
        if(!empty($where)){
            @$where .=" ".$_GET['radio1']." ";
        }
        $where .= "(";
        $content = str_replace("　", " ", $_GET['content']);
        
        $content_array = explode(" ",$content);

        for($i = 0; $i <count($content_array);$i++){
            $where .= 'content LIKE "%'.$content_array[$i].'%"';

            if ($i <count($content_array) -1){
                $where .= " OR ";
            }
        }
        $where .=")";
    }



    if (!empty($_GET['contributor'])){
        if(!empty($where)){
            @$where .=" ".$_GET['radio2']." ";
        }
        $where .= "(";
        $contributor = str_replace("　", " ", $_GET['contributor']);
      
        $contributor_array = explode(" ",$contributor);

        for($i = 0; $i <count($contributor_array);$i++){
            $where .= 'contributor LIKE "%'.$contributor_array[$i].'%"';

            if ($i <count($contributor_array) -1){
                $where .= " OR ";
            }
        }
        $where .=")";
    }

    if (!empty($_GET['imprint'])){
        if(!empty($where)){
            @$where .= " ".$_GET['radio3']." ";
        }
        $where.="(";
        $imprint = str_replace("　", " ", $_GET['imprint']);


        $imprint_array = explode(" ",$imprint);

        for($i = 0; $i <count($imprint_array);$i++){
            $where .= 'imprint LIKE "%'.$imprint_array[$i].'%"';

            if ($i <count($imprint_array) -1){
                $where .= " OR ";
                
            }
        }
        $where.=")";
    }


    if (!empty($_GET['date'])){
        if(!empty($where)){
            @$where .=" ".$_GET['radio4']." ";
        }
        $where .= "(";
        $date = str_replace('/', '', $_GET['date']);            //年月日を区切るスラッシュを取り除く

        $date_array = explode(" ",$date);

        for($i = 0; $i <count($date_array);$i++){
            $where .= 'date = '.$date_array[$i].'';

            if ($i <count($date_array) -1){
                $where .= " OR ";
            }
        }
        $where .=")";
    }



    $code=null;
    if(!empty($where)){
        
        @$where .= " ".$_GET['radio5']." "; 
    }


    

    $code = $code."c_code LIKE ";
    @$C_code =$_GET['C_code01'].$_GET['C_code02'].$_GET['C_code03'];  
    
   
    @$code=$code."\"".$C_code."\"";

    if($_GET['check']=="true"){
        $code.=" OR c_code = \"____\"";
    }

    $where .= $code;
    
    if(empty($where)){
        echo'<p class="alert alert-danger">検索対象はNULLでした。</p>';
        exit();
    }
    
    try{
        $sql = 'SELECT * FROM book WHERE '.$where.' AND NOT (audience_type=22 AND (audience_code=01 OR audience_code=02  OR audience_code=03) )';       //成人指定本は除外
        echo $sql; //sql文確認用
        $stmt = $dbh->prepare($sql);
        
        
        $stmt->execute(); 
                                       
        $dbh=null; 
    
        $i=1;
        $count=$stmt->rowCount();
        if($count==0){                                          //検索結果に何もない
            echo'<p class="alert alert-danger">検索対象は見つかりませんでした。</p>';
            
        }else if($count>=200){                                  //検索結果が200件以上
            echo'<p class="alert alert-success">'.$count.'件見つかりました。<br/>200件のみ表示します。</p>';
        }else{
            echo'<p class="alert alert-success">'.$count.'件見つかりました。</p>';
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
        
            if($i<=200){                                        //検索結果200件まで表示
                if(!empty($result_imprint)){
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
                echo '
                    <ul class="list-group">
                    <li class="list-group-item">
                    <div class="mx-auto">
                        <div class="media">
                            <div class="media-left">
                                <div class="d-none d-lg-block">
                                <img class="mr-3 img-fluid" src="'.$result_picture.'" alt='.$result_title.' style="width:180px" >
                                </div>
                                <div class="d-lg-none">
                                    <img class="mr-3 img-fluid" src="'.$result_picture.'" alt='.$result_title.' style="width:100px" >
                                </div>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">'.$result_title.'</h4>
                                <h5>'.$result_title.'</h5><p>'.$result_contributor."  ISBN:". $result_isbn.'</p><br/>
                                <p>'.$result_content.'</p><br/>
                                <p>'.  $result_imprint. $result_publisher.$result_price.$result_data.'</p>
                                <a class="btn btn-primary" href= "'. $result_amazon_url.'" role="button" target="_blank">Amazon</a>
                                <a class="btn btn-primary" href= "'. $result_honto_url.'" role="button" target="_blank">honto</a>
                                <br/><br/>
                            </div>
                        </div>
                    <br/>
                    </div>
                    </li>
                    </ul>
                    ';
            }else{
                break;
            }
                        
        $i++;            
        }    
    }catch(Exception $e){
        echo "<script>alert(不正な入力値です。);</script>";
    }

    
    


?>
