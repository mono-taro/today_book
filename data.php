<?php
require_once "Feed.php" ;
require_once "connect.php";

today_book();
soon_book();




function today_book(){
    $dbh = connectDB();

    $sql='TRUNCATE today_book';   
    $stmt=$dbh->prepare($sql);    //準備する命令    
    $stmt->execute();        //クエリの実行
    $stmt=null;                   //データーベースから切断  

    $feed = new Feed ;
    // フィードのURL
    $url = "https://www.hanmoto.com/ci/bd/search/sdate/today/edate/today/hdt/%E6%96%B0%E3%81%97%E3%81%84%E6%9C%AC/order/desc/vw/rss20" ;
    $a=1;

    // RSSの場合
    $rss = $feed->loadRss( $url ) ;


    foreach( $rss->item as $item )
    {
        //PHPでRSSやAtomのフィードを取得する方法    https://syncer.jp/php-how-to-get-feed   を参考にさせていただきました
        // 各エントリーの処理
        $link = $item->link ;	// リンク
        
        $url = $link;
        $keys = parse_url($url); //パース処理
        $path = explode("/", $keys['path']); //分割処理
        $last = end($path); //最後の要素を取得
                
        //PHP:超マニアック ISBN10とISBN13の変換 その２    https://mayer.jp.net/?tag=php-isbn10-isbn13-%E5%A4%89%E6%8F%9B  　を参考にさせていただきました

        $isbn13=$last;//ハイフンなし
    
        //そのまま配列に入れる。978を除いた3〜11番目が必要
        $isbn13_array=str_split($isbn13);
        
        //チェックデジット計算
        $checkdigit_10=(10*$isbn13_array[3]+9*$isbn13_array[4]+8*$isbn13_array[5]+7*$isbn13_array[6]+6*$isbn13_array[7]+5*$isbn13_array[8]+4*$isbn13_array[9]+3*$isbn13_array[10]+2*$isbn13_array[11]) % 11;
        $checkdigit_10=11-$checkdigit_10;
        
        if($checkdigit_10==10){
        $checkdigit_10='X';
        }elseif($checkdigit_10==11){
        $checkdigit_10='0';
        }
        
        $isbn10_result=$isbn13_array[3].$isbn13_array[4].$isbn13_array[5].$isbn13_array[6].$isbn13_array[7].$isbn13_array[8].$isbn13_array[9].$isbn13_array[10].$isbn13_array[11].$checkdigit_10;
    
        
        $amazon_url='https://www.amazon.co.jp/dp/'.$isbn10_result ;                 //アマゾンへのリンク

        $isbn12 = substr($isbn13, 0, -1);            //hontoアクセス用に13桁のISBNの末尾を削る
        $honto_url='http://honto.jp/redirect.html?bookno='.$isbn12;

        $OpenBDurl='https://api.openbd.jp/v1/get?isbn='.$isbn10_result;                 //openBDアクセス用URL

        //openBDからデータ取得
        $json = file_get_contents($OpenBDurl);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $arr = json_decode($json,true);

        if ($arr == NULL) {
            return;
        }else{  
            $book_content=null;
            $C_code=null;
            $Subject_word=null;

            $book_isbn = @$arr[0]["onix"]["RecordReference"];                                                                         //ISBN
            $book_title = @$arr[0]["onix"]["DescriptiveDetail"]["TitleDetail"]["TitleElement"]["TitleText"]["content"];               //タイトル            
            $book_subtitle = @$arr[0]["onix"]["DescriptiveDetail"]["TitleDetail"]["TitleElement"]["Subtitle"]["content"];             //サブタイトル
            $book_contributor = @$arr[0]["onix"]["DescriptiveDetail"]["Contributor"][0]["PersonName"]["content"];                     //著者

            if(!empty(@$arr[0]["onix"]["CollateralDetail"]["TextContent"])){                                        
                for($i=4;$i>=0;$i--){                                                                                                     //本の内容
                    if(!empty(@$arr[0]["onix"]["CollateralDetail"]["TextContent"][$i]["Text"])){   
                        $book_content=@$arr[0]["onix"]["CollateralDetail"]["TextContent"][$i-1]["Text"].'<br><br>';                           
                        $book_content = $book_content.@$arr[0]["onix"]["CollateralDetail"]["TextContent"][$i]["Text"].'<br>';
                        break;
                    }
                }
            }

 

            $book_imprint = @$arr[0]["onix"]["PublishingDetail"]["Imprint"]["ImprintName"];                                           //発行元出版社
            $book_publisher = @$arr[0]["onix"]["PublishingDetail"]["Publisher"]["PublisherName"];                                     //販売元出版社
            $book_picture = @$arr[0]["onix"]["CollateralDetail"]["SupportingResource"][0]["ResourceVersion"][0]["ResourceLink"];      //書影 
            $book_price = @$arr[0]["onix"]["ProductSupply"]["SupplyDetail"]["Price"][0]["PriceAmount"];                               //価格
            $book_date = @$arr[0]["onix"]["PublishingDetail"]["PublishingDate"][0]["Date"];                                           //出版日
        
            $audience_type=@$arr[0]["onix"]["DescriptiveDetail"]["Audience"][0]["AudienceCodeType"];                                       //読者対象
            $audience_value=@$arr[0]["onix"]["DescriptiveDetail"]["Audience"][0]["AudienceCodeValue"];                                 //成人指定

            if(!empty(@$arr[0]["onix"]["DescriptiveDetail"]["Subject"])){                                                             
                for($i=2;$i>=0;$i--){
                    if(@$arr[0]["onix"]["DescriptiveDetail"]["Subject"][$i]["SubjectSchemeIdentifier"]==78){                          //C-code
                        $C_code = @$arr[0]["onix"]["DescriptiveDetail"]["Subject"][$i]["SubjectCode"];
                    }
                    if(@$arr[0]["onix"]["DescriptiveDetail"]["Subject"][$i]["SubjectSchemeIdentifier"]==20){                          //キーワード
                        $Subject_word = @$arr[0]["onix"]["DescriptiveDetail"]["Subject"][$i]["SubjectHeadingText"];
                    }
                }
            }
            if($audience_type=""){  //指定なしの場合便宜的に99とする
                $audience_type=99;  
            }
            if($audience_value=""){
                $audience_value=99;
            }

            if(empty($C_code)){
                $C_code="____";
            }

            if(empty($book_picture)){
                $book_picture="no_image.png";
            }

            if($book_title==null){
                echo "title_error";
                continue;
            }
            
            //取得データ確認用
            echo"===================================================\n";
            echo $book_title."\n";
            //echo $book_subtitle."3\n"
            //echo $book_isbn."\n";
            //echo $isbn10_result."\n";
            echo $book_content."\n";
            //print_r($arr[0]["onix"]["CollateralDetail"]["TextContent"]);
            //echo $book_date."\n";
            //echo "-----\n";
            //echo $C_code."\n";
            //echo $Subject_word."\n";
            //echo $audience."\n";
            //echo $audience_type."\n";
            //echo $book_contributor."\n";
            //echo $book_imprint."\n";
            //echo $book_publisher."\n";
            //echo $book_picture."\n";
            //echo $book_price."\n";
            //echo $book_date."\n";
            //echo $a."\n";       //取得件数確認用
            echo"===================================================\n";
            $a=$a+1;




            try{
                $book_sql='INSERT IGNORE INTO book(isbn,title,subtitle,content,contributor,imprint,publisher,picture,price,date,audience_type,audience_code,c_code,subject_text,isbn_10,amazon_url,honto_url,add_data) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';   //SQL命令文 入れたいデータは「？」
                $today_book_sql='INSERT IGNORE INTO today_book(isbn,title,subtitle,content,contributor,imprint,publisher,picture,price,date,audience_type,audience_code,c_code,subject_text,isbn_10,amazon_url,honto_url,add_data) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';   //SQL命令文 入れたいデータは「？」
                $book_stmt=$dbh->prepare($book_sql);    //準備する命令
                $today_book_stmt=$dbh->prepare($today_book_sql);    //準備する命令
                $data[]=$book_isbn;          
                $data[]=$book_title;
                $data[]=$book_subtitle;
                $data[]=$book_content;
                $data[]=$book_contributor;
                $data[]=$book_imprint;
                $data[]=$book_publisher;
                $data[]=$book_picture;
                $data[]=$book_price;
                $data[]=$book_date;
                $data[]=$audience_type;
                $data[]=$audience_value;
                $data[]=$C_code;
                $data[]=$Subject_word;
                $data[]=$isbn10_result;
                $data[]=$amazon_url;
                $data[]=$honto_url;
                $data[]=date('Y-m-d');
                
                $book_stmt->execute($data);        //クエリの実行
                $today_book_stmt->execute($data);
                
                $book_stmt=null;                   //データーベースから切断  
                $today_book_stmt=null; 
                $data=[];  
                
                
                                            
                
        
            }catch(Exception $e){
                echo"error";
            }

            

        }
                
        //}
    }
    $url=null;
}

function soon_book(){
    $dbh = connectDB();

    $sql='TRUNCATE soon_book';   //SQL命令文 入れたいデータは「？」
    $stmt=$dbh->prepare($sql);    //準備する命令    
    $stmt->execute();        //クエリの実行
    $stmt=null;                   //データーベースから切断  

    $feed = new Feed ;
    // フィードのURL
    $url = "https://www.hanmoto.com/ci/bd/search/sdate/day/edate/day/hdt/%E6%98%8E%E6%97%A5%E7%99%BA%E5%A3%B2%E3%81%AE%E6%9C%AC/order/desc/vw/rss20" ;
    $a=1;
    // RSSの場合
    $rss = $feed->loadRss( $url ) ;

    foreach( $rss->item as $item )
    {
        // 各エントリーの処理
        $link = $item->link ;	// リンク        
        $url = $link;
        $keys = parse_url($url); //パース処理
        $path = explode("/", $keys['path']); //分割処理
        $last = end($path); //最後の要素を取得

        $isbn13=$last;//ハイフンなし
    


        //そのまま配列に入れる。978を除いた3〜11番目が必要
        $isbn13_array=str_split($isbn13);  
        //チェックデジット計算
        $checkdigit_10=(10*$isbn13_array[3]+9*$isbn13_array[4]+8*$isbn13_array[5]+7*$isbn13_array[6]+6*$isbn13_array[7]+5*$isbn13_array[8]+4*$isbn13_array[9]+3*$isbn13_array[10]+2*$isbn13_array[11]) % 11;
        $checkdigit_10=11-$checkdigit_10;
        
        if($checkdigit_10==10){
        $checkdigit_10='X';
        }elseif($checkdigit_10==11){
        $checkdigit_10='0';
        }
        $isbn10_result=$isbn13_array[3].$isbn13_array[4].$isbn13_array[5].$isbn13_array[6].$isbn13_array[7].$isbn13_array[8].$isbn13_array[9].$isbn13_array[10].$isbn13_array[11].$checkdigit_10;
    
        $amazon_url='https://www.amazon.co.jp/dp/'.$isbn10_result ;                 //アマゾンへのリンク
        $isbn12 = substr($isbn13, 0, -1);            //hontoアクセス用に13桁のISBNの末尾を削る
        $honto_url='http://honto.jp/redirect.html?bookno='.$isbn12;
        $OpenBDurl='https://api.openbd.jp/v1/get?isbn='.$isbn10_result;                 //openBDアクセス用URL

        //openBDからデータ取得
        $json = file_get_contents($OpenBDurl);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $arr = json_decode($json,true);

        if ($arr == NULL) {
            return;
        }else{  
            $book_content=null;
            $C_code=null;
            $Subject_word=null;

            $book_isbn = @$arr[0]["onix"]["RecordReference"];                                                                         //ISBN
            $book_title = @$arr[0]["onix"]["DescriptiveDetail"]["TitleDetail"]["TitleElement"]["TitleText"]["content"];               //タイトル            
            $book_subtitle = @$arr[0]["onix"]["DescriptiveDetail"]["TitleDetail"]["TitleElement"]["Subtitle"]["content"];             //サブタイトル
            $book_contributor = @$arr[0]["onix"]["DescriptiveDetail"]["Contributor"][0]["PersonName"]["content"];                     //著者

            if(!empty(@$arr[0]["onix"]["CollateralDetail"]["TextContent"])){                                        
                for($i=4;$i>=0;$i--){                                                                                                     //本の内容
                    if(!empty(@$arr[0]["onix"]["CollateralDetail"]["TextContent"][$i]["Text"])){   
                        $book_content=@$arr[0]["onix"]["CollateralDetail"]["TextContent"][$i-1]["Text"].'<br><br>';                           
                        $book_content = $book_content.@$arr[0]["onix"]["CollateralDetail"]["TextContent"][$i]["Text"].'<br>';
                        break;
                    }
                }
            }


            $book_imprint = @$arr[0]["onix"]["PublishingDetail"]["Imprint"]["ImprintName"];                                           //発行元出版社
            $book_publisher = @$arr[0]["onix"]["PublishingDetail"]["Publisher"]["PublisherName"];                                     //販売元出版社
            $book_picture = @$arr[0]["onix"]["CollateralDetail"]["SupportingResource"][0]["ResourceVersion"][0]["ResourceLink"];      //書影 
            $book_price = @$arr[0]["onix"]["ProductSupply"]["SupplyDetail"]["Price"][0]["PriceAmount"];                               //価格
            $book_date = @$arr[0]["onix"]["PublishingDetail"]["PublishingDate"][0]["Date"];                                           //出版日
        
            $audience_type=@$arr[0]["onix"]["DescriptiveDetail"]["Audience"][0]["AudienceCodeType"];                                       //読者対象
            $audience_value=@$arr[0]["onix"]["DescriptiveDetail"]["Audience"][0]["AudienceCodeValue"];                                 //成人指定

            if(!empty(@$arr[0]["onix"]["DescriptiveDetail"]["Subject"])){                                                             
                for($i=2;$i>=0;$i--){
                    if(@$arr[0]["onix"]["DescriptiveDetail"]["Subject"][$i]["SubjectSchemeIdentifier"]==78){                          //C-code
                        $C_code = @$arr[0]["onix"]["DescriptiveDetail"]["Subject"][$i]["SubjectCode"];
                    }
                    if(@$arr[0]["onix"]["DescriptiveDetail"]["Subject"][$i]["SubjectSchemeIdentifier"]==20){                          //キーワード
                        $Subject_word = @$arr[0]["onix"]["DescriptiveDetail"]["Subject"][$i]["SubjectHeadingText"];
                    }
                }
            }

            if($audience_type=""){  //指定なしの場合便宜的に99とする
                $audience_type=99;  
            }
            if($audience_value=""){
                $audience_value=99;
            }

            if(empty($C_code)){
                $C_code="____";
            }

            if(empty($book_picture)){
                $book_picture="no_image.png";
            }

            if($book_title==null){
                echo "title_error";
                continue;
            }

            //取得データ確認用
            //echo"===================================================\n";
            //echo $book_title."\n";
            //echo $book_subtitle."3\n"
            //echo $book_isbn."\n";
            //echo $isbn10_result."\n";
            //echo $book_content."\n";
            //echo $book_date."\n";
            //echo "-----\n";
            //echo $C_code."\n";
            //echo $Subject_word."\n";
            //echo $audience."\n";
            //echo $audience_type."\n";
            //echo $book_contributor."\n";
            //echo $book_imprint."\n";
            //echo $book_publisher."\n";
            //echo $book_picture."\n";
            //echo $book_price."\n";
            //echo $book_date."\n";
            //echo $a."\n";       //取得件数確認用
            //echo"===================================================\n";
            $a=$a+1;


            try{
                $sql='INSERT IGNORE INTO soon_book(isbn,title,subtitle,content,contributor,imprint,publisher,picture,price,date,audience_type,audience_code,c_code,subject_text,isbn_10,amazon_url,honto_url,add_data) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';   //SQL命令文 入れたいデータは「？」
                $stmt=$dbh->prepare($sql);    //準備する命令
                $data[]=$book_isbn;          
                $data[]=$book_title;
                $data[]=$book_subtitle;
                $data[]=$book_content;
                $data[]=$book_contributor;
                $data[]=$book_imprint;
                $data[]=$book_publisher;
                $data[]=$book_picture;
                $data[]=$book_price;
                $data[]=$book_date;
                $data[]=$audience_type;
                $data[]=$audience_value;
                $data[]=$C_code;
                $data[]=$Subject_word;
                $data[]=$isbn10_result;
                $data[]=$amazon_url;
                $data[]=$honto_url;
                $data[]=date('Y-m-d');
                
                $stmt->execute($data);        //クエリの実行
                
                $stmt=null;                   //データーベースから切断  
                $data=[]; 
        
            }catch(Exception $e){
                echo"DBerror";
            }

            

        }
                
        //}
    }
    $url=null;

}

?>