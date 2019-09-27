<html>
<head>
<meta charset="utf-8">
</head>
<body>

<?php

//DB接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//DB接続　終わり

//テーブル削除
//$sql = "DROP TABLE IF EXISTS mission5";
//テーブル削除終わり

//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS mission5"
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32),"
. "comment TEXT,"
. "date DATETIME,"
. "pass char(15)"
.");";
$stmt = $pdo->query($sql);
//テーブル作成　終わり

	
//変数の初期化
$henshu_No = "0";
$henshu_Name = "";
$henshu_Com = "";


//投稿
if ($_SERVER["REQUEST_METHOD"] === "POST") {//何か投稿されたら
if(empty($_POST["deleteNo"] )){//削除番号書き込みなし
	//何か記入されて送信ボタンが押されたら
	if(!empty($_POST["comment"])&&($_POST["pass1"])){
		if(empty($_POST["edit"])){
		$comment = $_POST["comment"];
		echo $comment . "を受け付けました" . "<br>";

		//変数名前
		$name = $_POST["name"];
		$pass = $_POST["pass1"];
		//テーブルデータ入力
		$sql = $pdo -> prepare("INSERT INTO mission5 (name, comment,date,pass) VALUES (:name, :comment, :date,:pass)");
		$sql -> bindParam(':name', $name, PDO::PARAM_STR);
		$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$date = date("Y/m/d H:i:s");
		$sql -> bindParam(':date',$date, PDO::PARAM_STR);
		$sql -> bindParam(':pass',$pass, PDO::PARAM_STR);
		$sql -> execute();
		//テーブルデータ入力　終わり


		//編集書き込み機能
		}elseif(!empty($_POST["edit"])){
			
			$edit = $_POST["edit"];
			//入力したデータ編集
			$id = $edit; //変更する投稿番号
			$ediname = $_POST["name"];
			$edicom = $_POST["comment"];
			$sql = 'update mission5 set name=:name,comment=:comment where id=:id';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name', $ediname, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $edicom, PDO::PARAM_STR);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			//入力したデータ編集　終わり
		}
	}elseif(!empty($_POST["henshu"])&&($_POST["pass2"])){//投稿機能　終わり。編集フォームに書き込まれたら
		$editpass = $_POST["pass2"];
		$henshu = $_POST["henshu"];

		$sql = 'SELECT * FROM mission5 WHERE id=:id';//データの呼び出し
		$stmt = $pdo->prepare($sql);
		$stmt -> bindParam(':id', $henshu, PDO::PARAM_STR);
		$stmt -> execute();

		$result = $stmt -> fetch(PDO::FETCH_ASSOC);//データの取得
		if($result['pass']==$editpass){
			$henshu_No = $result['id'];
			$henshu_Name = $result['name'];
			$henshu_Com = $result['comment'];
		}
	}//編集機能　終わり
//削除機能
}elseif(!empty($_POST["deleteNo"]&&($_POST["pass3"]))){
	$delete = $_POST["deleteNo"];//番号取得
	$delpass=$_POST["pass3"];

	$sql = 'SELECT * FROM mission5 WHERE id=:id';//データの呼び出し
	$stmt = $pdo->prepare($sql);
	$stmt -> bindParam(':id', $delete, PDO::PARAM_STR);
	$stmt -> execute();
	$result = $stmt -> fetch(PDO::FETCH_ASSOC);//データの取得
	if($result['pass']==$delpass){
		//入力したデータ削除
		$id = $delete;
		$sql = 'delete from mission5 where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		//入力したデータ削除　終わり
	echo $delete . "を削除しました。<br>";
	}
	}
//削除機能　終わり
}
?>
<form method="POST" action="">
<!--入力フォーム-->
<p>
	【　投稿フォーム　】<br>
	名前：<textarea name="name"><?php echo $henshu_Name;?></textarea><br>
	コメント：<textarea name="comment"><?php echo $henshu_Com;?></textarea><br>
	パスワード：<input type="password" name="pass1"><br>
	<input type= "hidden" name="edit" value="<?php echo $henshu_No;?>"><br>
	<input type="submit" value="送信"><br>
</p>
<!--削除フォーム-->
<p>
	【　削除フォーム　】<br>
	投稿番号：<textarea name="deleteNo"></textarea><br>
	パスワード：<input type="password" name="pass3"><br>
	<input type="submit" value="削除"><br>
</p>
<!--編集フォーム-->
<p>
	【　編集フォーム　】<br>
	編集対象番号：<textarea name="henshu"></textarea><br>
	パスワード：<input type="password" name="pass2"><br>
	<input type="submit" value="編集"><br>

</form>
<?php
		//データ表示
		$sql = 'SELECT * FROM mission5';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach ($results as $row){
			//$rowの中にはテーブルのカラム名が入る
			echo $row['id'].',';
			echo $row['name'].',';
			echo $row['comment'].',';
			echo $row['date'].'<br>';
			echo "<hr>";
		}
		//データ表示　終わり
?>
</body>
</html>
