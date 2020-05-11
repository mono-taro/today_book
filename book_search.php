<!DOCTYPE html>
<html lang="jp">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-166046229-3"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-166046229-3');
    </script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>



    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.ja.min.js"></script>


    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">



    <title>本を探す</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">本を探す</a>
  <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="ナビゲーションの切替">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="today_book.php">本日の本</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="soon_book.php">明日の本</a>
      </li>
    </ul>
  </div>
</nav>

    <br/><br/>
    <div class="col-13 col-md-13 col-lg-7">
    <h1 class="col-xs-6 col-xs-offset-3">検索フォーム</h1><p>(4月30日以降の本のみ)</p>
    <div class="col-xs-6 col-xs-offset-3 well">
        <!--検索フォーム-->
        <form id="searchform" method="post">
        <div class="form-group">
            <label for="InputTitle">書名</label>
            <input name="title" class="form-control" id="InputTitle" placeholder="選択なし">
        </div>

        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio1_and" name="radio01" class="custom-control-input" value="AND" checked>
            <label class="custom-control-label" for="radio1_and">AND</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio1_or" name="radio01" class="custom-control-input" value="OR">
            <label class="custom-control-label" for="radio1_or">OR</label>
        </div>

        <div class="form-group">
            <label for="InputTitle">内容</label>
            <input name="content" class="form-control" id="InputContent" placeholder="選択なし">
        </div>

        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio2_and" name="radio02" class="custom-control-input" value="AND" checked>
            <label class="custom-control-label" for="radio2_and">AND</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio2_or" name="radio02" class="custom-control-input" value="OR">
            <label class="custom-control-label" for="radio2_or">OR</label>
        </div>
        <br/>
        <br/>


        <div class="form-group">
            <label for="InputContributor">著者名</label>
            <input name="contributor" class="form-control" id="InputContributor" placeholder="選択なし">
        </div>

        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio3_and" name="radio03" class="custom-control-input" value="AND" checked>
            <label class="custom-control-label" for="radio3_and">AND</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio3_or" name="radio03" class="custom-control-input" value="OR">
            <label class="custom-control-label" for="radio3_or">OR</label>
        </div>

        <br/>
        <br/>
        <div class="form-group">
            <label for="InputImprint">発行元出版社</label>
            <input name="imprint" class="form-control" id="InputImprint" placeholder="選択なし">
        </div>

        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio4_and" name="radio04" class="custom-control-input" value="AND" checked>
            <label class="custom-control-label" for="radio4_and">AND</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio4_or" name="radio04" class="custom-control-input" value="OR">
            <label class="custom-control-label" for="radio4_or">OR</label>
        </div>

        <br/>
        <br/>
        <div class="form-group">
            <label for="InputImprint">出版日</label>
            <!--カレンダー-->
            <div class="col-5 col-md-4 col-lg-2">
                <input class="datepicker form-control" name="date" id="date" laceholder="選択なし">
                    <script type="text/javascript">
                        $('.datepicker').datepicker({
                            format : 'yyyy/mm/dd'
                        });
                    </script>
            </div>
        </div>

        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio5_and" name="radio05" class="custom-control-input" value="AND" checked>
            <label class="custom-control-label" for="radio5_and">AND</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="radio5_or" name="radio05" class="custom-control-input" value="OR">
            <label class="custom-control-label" for="radio5_or">OR</label>
        </div>        


        <br/>
        <br/>
        <div class="form-group">
            <!--Cコード-->
            <label for="InputCcode">ジャンル</label>
            <br/>
            <label for="InputC_code01">　対象</label>
            <select name="Ccode01" class="form-control" id="InputCcode01">
                <option value="_" id ="0001">選択しない</option>
                <option value="0">一般</option>
                <option value="1">教養</option>
                <option value="2">実用</option>
                <option value="3">専門</option>
                <option value="4">検定教科書等</option>
                <option value="5">婦人</option>
                <option value="6">学参Ⅰ(小中)</option>
                <option value="7">学参Ⅱ(高校)</option>
                <option value="8">児童</option>
                <option value="9">雑誌扱い</option>
            </select>
            
            <label for="InputC_code02">　形態</label>
            <select name="Ccode02" class="form-control" id="InputCcode02">
                <option value="_">選択しない</option>
                <option value="0">単行本</option>
                <option value="1">文庫</option>
                <option value="2">新書</option>
                <option value="3">全集・双書</option>
                <option value="4">ムック・その他</option>
                <option value="5">事・事典</option>
                <option value="6">図鑑</option>
                <option value="7">絵本</option>
                <option value="8">磁気媒体等</option>
                <option value="9">コミック</option>
            </select>
            
            <label for="InputC_code03">　内容</label>
            <select name="Ccode03" class="form-control" id="InputCcode03">
                <option value="__">選択しない</option>
                <option value="0_">(総記)</option>
                <option value="00">総記</option>
                <option value="01">百科事典</option>
                <option value="02">年鑑・雑誌</option>
                <option value="04">情報科学</option>
                <option value="1_">(哲学・宗教・心理学)</option>
                <option value="10">哲学</option>
                <option value="11">心理(学)</option>
                <option value="12">倫理(学)</option>
                <option value="14">宗教</option>
                <option value="15">仏教</option>
                <option value="16">キリスト教</option>
                <option value="2_">(歴史・地理)</option>
                <option value="20">歴史総記</option>
                <option value="21">日本歴史</option>
                <option value="22">外国歴史</option>
                <option value="23">伝記</option>
                <option value="25">地理</option>
                <option value="26">旅行</option>
                <option value="3_">(社会科学)</option>
                <option value="30">社会科学総記</option>
                <option value="31">政治-含む国防軍事</option>
                <option value="32">法律</option>
                <option value="33">経済・財政・統計</option>
                <option value="34">経営</option>
                <option value="36">社会</option>
                <option value="37">教育</option>
                <option value="39">民族・風習</option>
                <option value="4_">(自然科学)</option>
                <option value="40">自然科学総記</option>
                <option value="41">数学</option>
                <option value="42">物理学</option>
                <option value="43">化学</option>
                <option value="44">天文・地理</option>
                <option value="45">生物学</option>
                <option value="47">医学・歯学・薬学</option>
                <option value="5_">(工学工業)</option>
                <option value="50">工学・工学総記</option>
                <option value="51">土木</option>
                <option value="52">建築</option>
                <option value="53">機械</option>
                <option value="54">電気</option>
                <option value="55">電子通信</option>
                <option value="56">海事</option>
                <option value="57">採掘・冶金</option>
                <option value="58">その他の工業</option>
                <option value="6_">(産業)</option>
                <option value="60">産業総記</option>
                <option value="61">農林業</option>
                <option value="62">水産業</option>
                <option value="63">商業</option>
                <option value="65">交通・通信</option>
                <option value="7_">(芸術・生活)</option>
                <option value="70">芸術総記</option>
                <option value="71">絵画・彫刻</option>
                <option value="72">写真・工芸</option>
                <option value="73">音楽・舞踊</option>
                <option value="74">演劇・映画</option>
                <option value="75">体育・スポーツ</option>
                <option value="76">諸芸・娯楽</option>
                <option value="77">家事</option>
                <option value="78">日記・手帳</option>
                <option value="79">コミックス・劇画</option>
                <option value="8_">語学</option>
                <option value="80">語学総記</option>
                <option value="81">日本語</option>
                <option value="82">英米語</option>
                <option value="84">ドイツ語</option>
                <option value="85">フランス語</option>
                <option value="87">各国語</option>
                <option value="9_">(文学)</option>
                <option value="90">文学総記</option>
                <option value="91">日本文学総記</option>
                <option value="92">日本文学詩歌</option>
                <option value="93">日本文学、小説・物語</option>
                <option value="95">日本文学、評論、随筆、その他</option>
                <option value="97">外国文学小説</option>
                <option value="98">外国文学、その他</option>
            </select>
        </div>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="check" name="check" value="true">
            <label class="custom-control-label" for="check">未分類含める</label>
        </div><br/>
        <button type="submit" class="btn btn-dark"id="search">検索</button>      
        </form>
    </div>


    <script>


        
        $(function() {
            //検索ボタンがクリックされたら処理
            $('#search').click(function() {                
                //HTMLからデータ受取
                var data = {title : $('#InputTitle').val(),
                            contributor : $('#InputContributor').val(),
                            imprint : $('#InputImprint').val(),
                            content : $('#InputContent').val(),
                            date :  $('#date').val(),
                            radio1 :  $('input[name=radio01]:checked').val(),
                            radio2 :  $('input[name=radio02]:checked').val(),
                            radio3 :  $('input[name=radio03]:checked').val(),
                            radio4 :  $('input[name=radio04]:checked').val(),
                            radio5 :  $('input[name=radio05]:checked').val(),
                            C_code01 : $('#InputCcode01').val(),
                            C_code02 : $('#InputCcode02').val(),
                            C_code03 : $('#InputCcode03').val(),
                            check :  $('input[name=check]:checked').val()
                            };
                            console.log(data);
                //ajax処理          
                $.ajax({
                        type: "GET",
                        url: "search.php",
                        
                        data: data,
                        async: true,
                        //処理が成功したら
                        success : function(data) {
                            //該当箇所にデータを表示
                            $('#res').html(data);
                        },
                        //エラーが出た場合
                        error : function() {
                            alert('通信エラー');
                        }
                });
                return false;
                });
            });



    </script>

    <!--ajaxの処理結果表示-->
    <div id="res"></div>
</div>
</body>
</html>