<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL);
require('function.php');
	
	if(isset($_POST["submit"])){
		$account = htmlspecialchars($_POST["account"]);
		$email = htmlspecialchars($_POST["email"]);
		$password = htmlspecialchars($_POST["password"]);
		$password_confirm = htmlspecialchars($_POST["password_confirm"]);
		
		// Google 機器人驗證
		$response = $_POST['g-recaptcha-response'];
		if (!recaptcha_vertify($response))
			$errRecaptcha = "<div class='alert alert-danger'>請驗證是否為機器人！</div>";
		
		//帳號純英數驗證
		$account_len=strlen($account);
		if(!preg_match("/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i",$account)||$account_len<5||$account_len>15){
			$errAccount = "<div class='alert alert-danger'>帳號必須為6~15字的英數組合！</div>";
		}
		//email 驗證
		if(!$_POST["email"] || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
			$errEmail = "<div class='alert alert-danger'>電子郵件格式有誤！請重試一次！</div>";
		
		//密碼驗證
		$pwd_len=strlen($password);
		if(!preg_match("/^(([a-z]+[0-9]+)|([0-9]+[a-z]+))[a-z0-9]*$/i",$password)||$pwd_len<5||$pwd_len>15)
			$errPawssword1 = "<div class='alert alert-danger'> 密碼必須為6~15字的英數組合！</div>";
		
		if ($password != $password_confirm)
			$errPawssword2 = "<div class='alert alert-danger'>請確認兩次輸入的密碼相同！</div>";
		
		
		if( !$errRecaptcha && !$errAccount && !$errEmail && !$errPawssword1 && !$errPawssword2){
			connectdb();
			
			$sql_account = "select name from accounts where name='$account';";
			$stmt = $pdo->prepare($sql_account);
			$stmt->bindValue(':sn', '1');
			$stmt->execute();
			while($row = $stmt->fetch()){
				$result_account = $row['name'];
			}
				
			if($result_account != ""){
				$errAccount = "<div class='alert alert-danger'>此帳號已註冊！請使用其他帳號！</div>";
				$db = null;
			}
			else{
				
				$sql_create = "INSERT INTO accounts (name, password, email) VALUES ('$account', SHA1('$password'),'$email');";
				$sth = $pdo->prepare($sql_create);
				try {	
					if ($sth->execute()) 
						$Message = "<div class='alert alert-success'>註冊成功！請登入遊戲！</div>";
					else 
						$Message = "<div class='alert alert-danger'>註冊失敗！請通知管理員！</div>";
				}
				catch (PDOException $e) {
					$Message = "<div class='alert alert-danger'>註冊失敗！請通知管理員！</div>";
				
				
				}
			
			}					
		}
	}
?>

<html lang="zh-tw">
<head>
	<title>楓之谷註冊</title> 
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<style>

	</style>
	
</head>
<body>
<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#MyNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href=".">楓之谷註冊</a>
		</div>
		
		<div class="collapse navbar-collapse" id="MyNavbar">
			<ul class="nav navbar-nav">
				<li><a href="#"></span>註冊帳號</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#"><span class="glyphicon glyphicon-info-sign"></span> 關於</a></li>
			</ul>
		</div>
			
	</div>
</nav>

<div class="container">
<form action="registr.php" method="POST">
	<fieldset>
		<div id="legend">
			<legend class=""><h2>帳號註冊</h2></legend>
		</div>
		<?php echo "$Message";?>
		<div class="form-group">
		<!-- 帳號 -->
			<label for="account"><h3>帳號</h3></label>
			<input type="text" id="account" name="account" placeholder="maplestory113" class="form-control input-lg">
			<p class="help-block">登入遊戲時使用的帳號，限用長度為 6~15 字的英數組合，EX: maplestory113。</p>
			<?php echo "$errAccount";?>
		</div>
 
		<div class="form-group">
			<!-- Email -->
			<label for="email"><h3>Email</h3></label>		
			<input type="email" id="email" name="email" placeholder="mail@gmail.com" class="form-control input-lg">
			<p class="help-block">請輸入你的信箱。</p>		
			<?php echo "$errEmail";?>
		</div>
 
		<div class="form-group">
			<!-- Password-->
			<label for="password"><h3>密碼</h3></label>
			<input type="password" id="password" name="password" placeholder="" class="form-control input-lg">
			<p class="help-block">密碼長度為 6~15 字的英數組合。</p>
			<?php echo "$errPawssword1";?>
		</div>
 
		<div class="form-group">
			<!-- Password -->
			<label for="password_confirm"><h3>再輸入一次密碼。</h3></label>	
			<input type="password" id="password_confirm" name="password_confirm" placeholder="" class="form-control input-lg">
			<p class="help-block">重複輸入密碼以確保密碼正確</p>
			<?php echo "$errPawssword2";?>
		</div>
 
		<div class="form-group">
		<!-- Button -->
			<center><?php echo recaptcha_display(); ?><br></center>
			<?php echo "$errRecaptcha";?>
			<input id="submit" name="submit" type="submit" class="btn btn-lg btn-block btn-success" value="註冊">
		</div>
	</fieldset>
</form>

</div>


</body>
</html>