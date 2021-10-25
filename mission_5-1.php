<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
        //DBの設定・接続
        $dsn = 'データベース名';
        $user = 'ユーザー名';
        $d_password = 'パスワード';
        $pdo = new PDO($dsn, $user, $d_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = "CREATE TABLE IF NOT EXISTS tb1"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date TEXT,"
        . "password TEXT"
        .");";
        $stmt = $pdo->query($sql);
        
        //新規投稿機能
        if(!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["password"])&&empty($_POST["flag"]))
        {
            //DBに新規登録
            $sql = $pdo -> prepare("INSERT INTO tb1 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':password', $password, PDO::PARAM_STR);
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $password = $_POST["password"];
            $date = date("Y年m月d日 H時i分s秒");
            $sql -> execute();
        }
        
        //投稿削除機能
        if(!empty($_POST["delete"]))
        {
            $delete = $_POST["delete"];
            $password=$_POST["password"];
        
            //DBに登録されているパスワードを取得
            $sql = 'SELECT password FROM tb1 WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
            $stmt->execute();
            $db_password = $stmt->fetch(PDO::FETCH_COLUMN);
            
            //パスワードが一致していればDBから投稿を削除
            if($db_password==$password)
            {
                $sql = 'delete from tb1 where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
                $stmt->execute();
                echo "<[".$delete."]の投稿を削除しました。>";
            }
            //パスワードが違うときの処理
            elseif($db_password!=$password)
            {
                echo "<パスワードが違います。>";
            }
        }
        
        //投稿編集機能
        if(!empty($_POST["edit"])||!empty($_POST["flag"]))
        {
            //編集機能1周目
            if(!empty($_POST["edit"]))
            {
                $edit = $_POST["edit"];
                $password=$_POST["password"];
                    
                //DBに登録されているパスワードを取得
                $sql = 'SELECT password FROM tb1 WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
                $stmt->execute();
                $db_password = $stmt->fetch(PDO::FETCH_COLUMN);
                    
                //パスワードが一致していれば編集機能を起動
                if($db_password==$password)
                {
                    $sql = 'SELECT name,comment FROM tb1 WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
                    $stmt->execute();
                    $results = $stmt->fetchAll();
                    
                    foreach ($results as $data)
                    {
                        //ここで入力欄に表示する名前とコメントを代入
                        $edit_name=$data['name'];
                        $edit_comment=$data['comment'];
                    }
                    
                    echo "<[".$edit."]の投稿を編集します。>";
                }
                //パスワードが違うときの処理
                elseif($db_password!=$password)
                {
                    echo "<パスワードが違います。>";
                }
            }
            //編集機能2周目
            elseif(!empty($_POST["flag"]))
            {
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $password = $_POST["password"];
                $date = date("Y年m月d日 H時i分s秒");
                $edit = $_POST["flag"];
                
                //投稿を編集
                $sql = 'UPDATE tb1 SET name=:name,comment=:comment,password=:password WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
                $stmt->execute();
                
                echo "<[".$edit."]の投稿を編集しました。>";
            }
        }
    ?>   
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php if(!empty($edit_name)){echo $edit_name ;} ?>">
        <input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($edit_comment)){echo $edit_comment ;} ?>">
        <input type="hidden" name="flag" placeholder="flag" value="<?php if(!empty($edit_name)&&!empty($edit_comment)){echo $edit;} ?>">
        <input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="submit">
    </form>
    <form action="" method="post">
        <input type="text" name="delete" placeholder="削除番号">
        <input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="submit" value="削除"><br>
    </form>
    <form action="" method="post">
        <input type="text" name="edit" placeholder="編集番号">
        <input type="text" name="password" placeholder="パスワード">
        <input type="submit" name="submit" value="編集">
    </form>
    
    <?php
        $sql = 'SELECT * FROM tb1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row)
        {
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].'<br>';
            echo $row['name'].'<br>';
            echo $row['comment'].'<br>';
            echo $row['date'].'<br>';
            echo "<hr>";
        }
    ?>
</body>
</html>