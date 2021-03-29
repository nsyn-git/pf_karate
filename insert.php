<?php
require_once("util.php");
$gobackURL = "contact.html";

// 文字エンコードの検証
if (!cken($_POST)){
  header("Location:{$gobackURL}");
  exit();
}

// 簡単なエラー処理
$errors = [];
if (!isset($_POST["name"])||($_POST["name"]==="")){
  $errors[] = "名前を入力してください。";
}
if (!isset($_POST["kana_name"])||($_POST["kana_name"]==="")){
  $errors[] = "フリガナを入力してください。";
}
if (!isset($_POST["b_day"])||($_POST["b_day"])){
  $errors[] = "生年月日を入力してください。";
}
if (!isset($_POST["sex"])||!in_array($_POST["sex"], ["男","女","その他"])) {
  $errors[] = "性別を選択してください。";
}
if (!isset($_POST["mail"])||($_POST["mail"])){
  $errors[] = "メールアドレスを入力してください。";
}
if (!isset($_POST["q_category"])||!in_array($_POST["q_category"],["無料体験を希望","入会を希望","その他"])){
  $errors[] = "お問い合わせジャンルを選択してください。";
}

//エラーがあったとき
if (count($errors)>0){
  echo '<ol class="error">';
  foreach ($errors as $value) {
    echo "<li>", $value , "</li>";
  }
  echo "</ol>";
  echo "<hr>";
  echo "<a href=", $gobackURL, ">戻る</a>";
  exit();
}

// データベースユーザ
$user = 'bushin';
$password = 'bushinuser';
// 利用するデータベース
$dbName = 'bushin_db';
// MySQLサーバ
$host = 'localhost:3306';
// MySQLのDSN文字列
$dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>レコード追加</title>
<link href="../../../css/style.css" rel="stylesheet">
<!-- テーブル用のスタイルシート -->
<link href="../../css/tablestyle.css" rel="stylesheet">
</head>
<body>
<div>
  <?php
  $name = $_POST["name"];
  $kana_name = $_POST["kana_name"];
  $b_day = $_POST["b_day"];
  $sex = $_POST["sex"];
  $mail = $_POST["mail"];
  $q_category = $_POST["q_category"];
  $message = $_POST["message"];
  //MySQLデータベースに接続する
  try {
    $pdo = new PDO($dsn, $user, $password);
    // プリペアドステートメントのエミュレーションを無効にする
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // 例外がスローされる設定にする
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL文を作る
    $sql = "INSERT INTO contact (name, kana_name, b_day, sex, mail, q_category, message) VALUES (:name, :kana_name, :b_day, :sex, :mail, :q_category, :message)";
    // プリペアドステートメントを作る
    $stm = $pdo->prepare($sql);
    // プレースホルダに値をバインドする
    $stm->bindValue(':name', $name, PDO::PARAM_STR);
    $stm->bindValue(':kana_name', $kana_name, PDO::PARAM_INT);
    $stm->bindValue(':b_day', $b_day, PDO::PARAM_INT);
    $stm->bindValue(':sex', $sex, PDO::PARAM_STR);
    $stm->bindValue(':mail', $mail, PDO::PARAM_INT);
    $stm->bindValue(':q_category', $q_category, PDO::PARAM_INT);
    $stm->bindValue(':message', $message, PDO::PARAM_INT);
    // SQL文を実行する
    if ($stm->execute()){
      // レコード追加後のレコードリストを取得する
      $sql = "SELECT * FROM contact";
      // プリペアドステートメントを作る
      $stm = $pdo->prepare($sql);
      // SQL文を実行する
      $stm->execute();
      // 結果の取得（連想配列で受け取る）
      $result = $stm->fetchAll(PDO::FETCH_ASSOC);
      // テーブルのタイトル行
      echo "<table>";
      echo "<thead><tr>";
      echo "<th>", "ID", "</th>";
      echo "<th>", "名前", "</th>";
      echo "<th>", "名前(カナ)", "</th>";
      echo "<th>", "生年月日", "</th>";
      echo "<th>", "性別", "</th>";
      echo "<th>", "メールアドレス", "</th>";
      echo "<th>", "お問い合わせカテゴリー", "</th>";
      echo "<th>", "メッセージ内容", "</th>";
      echo "</tr></thead>";
      // 値を取り出して行に表示する
      echo "<tbody>";
      foreach ($result as $row) {
        // １行ずつテーブルに入れる
        echo "<tr>";
        echo "<td>", es($row['id']), "</td>";
        echo "<td>", es($row['name']), "</td>";
        echo "<td>", es($row['kana_name']), "</td>";
        echo "<td>", es($row['b_day']), "</td>";
        echo "<td>", es($row['sex']), "</td>";
        echo "<td>", es($row['mail']), "</td>";
        echo "<td>", es($row['q_category']), "</td>";
        echo "<td>", es($row['message']), "</td>";
        echo "</tr>";
      }
      echo "</tbody>";
      echo "</table>";
    } else {
      echo '<span class="error">追加エラーがありました。</span><br>';
    };
  } catch (Exception $e) {
    echo '<span class="error">エラーがありました。</span><br>';
    echo $e->getMessage();
  }
  ?>
  <hr>
  <p><a href="<?php echo $gobackURL ?>">戻る</a></p>
</div>
</body>
</html>
