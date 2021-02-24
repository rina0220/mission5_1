<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
    //データベース名＝tb221275db
    //ユーザー名＝tb-221275
    //パスワード＝MNXk7nesLB
    //$dsnの式の中にスペースをいれないこと！
    $dsn='データベース名';
    $user='ユーザー名';
    $password='パスワード';
    //◆PDOオブジェクトの生成（DB接続）
    $pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //mission4_2
    //テーブル名はmission5_1、そこに登録できる項目はid、name、comment
    //id＝自動で登録されているナンバリング
    //name＝名前を入れる。文字列、半角英数で32文字
    //comment＝コメントを入れる。文字列、長めの文章も入る。
    
    //◆CREATE TABLE＝テーブルを作成
    $sql="CREATE TABLE IF NOT EXISTS tbtest"
    ."("
    //AUTO_INCREMENT＝カラムに値が指定されなかった場合、MySQLが自動的に値を割り当てる
    //データ型は整数、値は一つずつ増加して連番になる
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name char(32),"
    ."comment TEXT,"
    ."pass TEXT,"
    ."date TEXT"
    .");";
    //PDO::queryはSQLを実行
    //実行後に実行結果の情報が格納されたPDOStatement（stmt）オブジェクトを返す
    //つまり、$stmt＝とすべき時は、実行後にSQLの実行結果に関する情報を得たい時
    //SQLを実行するだけであれば、$db->query($sql);のように描けばok
    //$stmtを使うのが伝統的なだけで別の名前でもいい
    //->（アロー演算子）は左辺から右辺を取り出す演算子
    //queryは指定したSQL文をデータベースに対して発行してくれる役割
    //→SQLをデータベースに届ける
    //◆↓この式は$pdoから$sql（mission5_1）を取り出し、データベースに届けるという意味
    $stmt=$pdo->query($sql);
    ?>
    <?php
    //新規or編集処理
    //もし名前とコメントとパスワードに入力があれば
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST['pass'])){
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $pass=$_POST["pass"];
        $date=date("Y/m/d H:i:s");
        //名前とコメントとパスワードの入力があり、かつ$hiddenに入力があれば
        if(!empty($_POST["hidden"])){
            $id=$_POST["hidden"];
            //編集処理
        	$sql = 'UPDATE tbtest SET name=:name,comment=:comment, pass=:pass, date=:date WHERE id=:id';
        	$stmt = $pdo->prepare($sql);
        	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
        	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        	$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
        	$stmt->execute();
            
        //名前とコメントの入力があり、かつ$hiddenが空欄（＝新規投稿）
        }elseif(empty($_POST["hidden"])){
            $sql=$pdo->prepare
            (("INSERT INTO tbtest (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)"));
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
        	$sql -> execute();
        }
    }
    //削除処理
    //もし削除番号とパスワードが入力されたら
    //もし投稿番号と削除番号、入力されたパスワードと$passが同じだったら
    if(!empty($_POST["sakuzyo"]) && !empty($_POST["sakuzyopass"])){
    	$id=$_POST["sakuzyo"];
    	$sakuzyopass=$_POST["sakuzyopass"];
    	//idとpassが一致した行を消す
        $sql = 'delete from tbtest where id=:id and pass=:sakuzyopass';
        $stmt = $pdo->prepare($sql);
    	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    	$stmt->bindParam(':sakuzyopass', $sakuzyopass, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    //フォームの内容が新規か修正かを判断する処理
    //もし編集番号とパスワードが入力されたら
    if(!empty($_POST["hensyuu"]) && !empty($_POST["hensyuupass"])){
        $hensyuu=$_POST["hensyuu"];
        $hensyuupass=$_POST["hensyuupass"];
        //tbtestに入力されたデータレコードを抽出し、表示する
        $sql='SELECT*FROM tbtest';
        //pdoから$sql（mission5_1）を取り出し、データベースに届ける
        $stmt=$pdo->query($sql);
        //fetch=1行ずつ取得
        //fetchAll=全データを配列に変換、全ての結果行を含む配列を返す=$resultsに代入
        $results=$stmt->fetchAll();
        foreach($results as $row){
            //もしidと$hensyuuが同じかつ、passとhensyuupassが同じであれば
            if($row['id']==$hensyuu && $row['pass']==$hensyuupass){
                $editnumber=$row['id'];
                $editname=$row['name'];
                $editcomment=$row['comment'];
            }
        }
    }
    ?>
    
    <form action="" method="post">
        <h4>新規投稿</h4>
        <input type="text" name="name" placeholder="名前" 
        value="<?php if(!empty($editname)){echo $editname;}?>">
        <input type="text" name="comment" placeholder="コメント" 
        value="<?php if(!empty($editcomment)){echo $editcomment;}?>">
        <input type="hidden" name="hidden" 
        value="<?php if(!empty($editnumber)){echo $editnumber;}?>">
        <br>
        <input type="text" name="pass" placeholder="パスワード">
        <input type="submit" name="submit1">
    </form>
    <form action="" method="post">
        <h4>削除処理</h4>
        <input type="text" name="sakuzyo" placeholder="削除対象番号">
        <input type="text" name="sakuzyopass" placeholder="パスワード">
        <input type="submit" name="submit">
    </form>
    <form action="" method="post">
        <h4>編集処理</h4>
        <input type="text" name="hensyuu" placeholder="編集対象番号">
        <input type="text" name="hensyuupass" placeholder="パスワード">
        <input type="submit" name="submit2">
    </form>
    <br>
    <hr>
    
    <?php
    //投稿表示処理
    $sql = 'SELECT * FROM tbtest';
	$stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
    	//$rowの中にはテーブルのカラム名が入る
    	echo $row['id'].',';
    	echo $row['name'].',';
    	echo $row['date'].'<br>';
    	echo $row['comment'];
    	echo "<br>";
	}
	echo "<hr>";
    ?>
</body>
</html>