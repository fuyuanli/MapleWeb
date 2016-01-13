<?php
function connectdb(){
    //PDO 連線設定
    $PdoOptions = array(
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    );
 
    //資料庫連線
    try { 
	global $pdo;
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=twms'
                   , 'root', 'root',  $PdoOptions);
        $pdo ->exec('SET CHARACTER SET utf8');
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage());
    }
}

function recaptcha_vertify($response){

    $url = 'https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s';
    $url = sprintf($url, "6LcSSRUTAAAAAPahlNtP5LelkJqrqwFg9j-4_hf3", $response, $_SERVER['REMOTE_ADDR']); //自行替換 serectkey
    $status = file_get_contents($url);
    $r = json_decode($status);
    return (isset($r->success) && $r->success) ? true : false;
}

function recaptcha_display(){
	
	return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<div class="g-recaptcha" data-sitekey="6LcSSRUTAAAAAEaeXUdyWN9BJPp2N_n9HM7Rxno4"></div>'; //自行替換 sitekey

}

?>