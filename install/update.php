<?php

require "../baseInfo.php";
$connection = new mysqli('localhost',$dbUserName,$dbPassword,$dbName);

$arrays = [
    "DROP TABLE `refered_users`;",
    "DROP TABLE `server_accounts`;",
    "ALTER TABLE `server_config` DROP `cookie`;",
    "CREATE TABLE `discounts` (
        `id` int(255) NOT NULL AUTO_INCREMENT,
        `hash_id` varchar(100) NOT NULL,
        `type` varchar(10) NOT NULL,
        `amount` int(255) NOT NULL,
        `expire_date` int(255) NOT NULL,
        `expire_count` int(255) NOT NULL,
        `used_by` text DEFAULT NULL,
        PRIMARY KEY (`id`)
        );",
    "CREATE TABLE `admins` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `username` varchar(200) NOT NULL,
	  `password` varchar(200) NOT NULL,
	  `backupchannel` varchar(200) CHARACTER SET utf8 NOT NULL,
	  `lang` varchar(10) CHARACTER SET utf8 NOT NULL,
      PRIMARY KEY (`id`)
    );",
    "INSERT INTO `admins` (`id`, `username`, `password`, `backupchannel`, `lang`) VALUES
    (1, 'admin', 'admin', '-1002545458541', 'en');",
    "CREATE TABLE `servers` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `ip` varchar(200) NOT NULL,
      `port` int(10) NOT NULL,
      `username` varchar(200) NOT NULL,
      `password` varchar(200) NOT NULL,
      `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
      `panel` varchar(100) NOT NULL,
      `status` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    );",
    "CREATE TABLE `increase_day` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `volume` float NOT NULL,
        `price` int(11) NOT NULL,
        PRIMARY KEY (`id`)
        );",
    "CREATE TABLE `increase_order` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `userid` varchar(30) NOT NULL,
        `server_id` int(11) NOT NULL,
        `inbound_id` int(11) NOT NULL,
        `remark` varchar(100) NOT NULL,
        `amount` int(11) NOT NULL,
        `date` varchar(30) NOT NULL,
        PRIMARY KEY (`id`)
        );",
    "CREATE TABLE `increase_plan` (
        `id` int(255) NOT NULL AUTO_INCREMENT,
        `volume` float NOT NULL,
        `price` int(255) NOT NULL,
        PRIMARY KEY (`id`)
        );",
    "ALTER TABLE `orders_list` ADD `token` varchar(100) NOT NULL AFTER `userid`, ADD `rahgozar` int(10) NOT NULL AFTER `notif`;",
    "ALTER TABLE `server_config` ADD `reality` VARCHAR(10) NOT NULL DEFAULT 'false' AFTER `port_type`;",
    "ALTER TABLE `server_info` ADD `state` int(255) NOT NULL DEFAULT 1 AFTER `active`;",
    "ALTER TABLE `server_plans` ADD `rahgozar` int(10) DEFAULT 0 AFTER `date`;",
    "DROP TABLE `users_wallet`;",
    "ALTER TABLE `users` ADD `phone` varchar(15)  CHARACTER SET utf8 COLLATE utf8_general_ci AFTER `date`, ADD `refered_by` bigint(10) AFTER `phone`, ADD `step` varchar(1000)  CHARACTER SET utf8 COLLATE utf8_general_ci AFTER `refered_by`, ADD `freetrial` varchar(10)  CHARACTER SET utf8 COLLATE utf8_general_ci AFTER `step`, ADD `isAdmin` tinyint(1)  NOT NULL DEFAULT 0 AFTER `freetrial`, ADD `first_start` varchar(10) AFTER `isAdmin`;",
    "ALTER TABLE `needed_sofwares` CHANGE `title` `title` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;",
    "ALTER TABLE `server_plans` ADD `dest` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL AFTER `rahgozar`, ADD `serverNames` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL AFTER `dest`;",
    "ALTER TABLE `pays` CHANGE `payid` `payid` VARCHAR(500) NULL DEFAULT NULL;",
    "ALTER TABLE `setting` CHANGE `type` `type` VARCHAR(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL;",
    "ALTER TABLE `server_plans` ADD `spiderX` VARCHAR(500) NULL AFTER `serverNames`;",
    "ALTER TABLE `server_plans` ADD `flow` VARCHAR(50) NOT NULL DEFAULT 'None' AFTER `spiderX`;",
    "ALTER TABLE `admins` ADD `lang` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'en' AFTER `backupchannel`;"
    ];


function updateBot(){
    global $arrays, $connection, $walletwizwiz, $nowPaymentKey, $zarinpalId;
    
    foreach($arrays as $query){
        try{
            $connection->query($query);
        }catch (exception $error){
            
        }
    }
    
    $usersInfo = json_decode(file_get_contents("../userInfo.json"),true);
    foreach($usersInfo as $user => $value){
        $query = "UPDATE `users` SET `step` = '" . $value['step'] . "'";
        if(isset($value['first_start'])) $query .= ", `first_start` = '" . $value['first_start'] . "'";
        if(isset($value['isAdmin'])) $query .= ", `isAdmin` = '" . $value['isAdmin'] . "'";
        if(isset($value['freetrial'])) $query .= ", `freetrial` = '" . $value['freetrial'] . "'";
        $query .= " WHERE `userid` = " . $user;
        $connection->query($query);
    }
    
    $newData = file_get_contents("../settings/botstate.json");
    $checkExist = $connection->query("SELECT * FROM `setting` WHERE `type` = 'BOT_STATES'");
    if(mysqli_num_rows($checkExist) == 0) $connection->query("INSERT INTO `setting` (`type`, `value`) VALUES ('BOT_STATES', '$newData')");


    $paymentKeys = array();
    $paymentKeys['bankAccount'] = $walletwizwiz;
    $paymentKeys['holderName'] = "";
    $paymentKeys['nowpayment'] = $nowPaymentKey;
    $paymentKeys['zarinpal'] = $zarinpalId;
    $paymentKeys['nextpay'] = "";
    
    $paymentKeys = json_encode($paymentKeys);
    $checkExist = $connection->query("SELECT * FROM `setting` WHERE `type` = 'PAYMENT_KEYS'");
    if(mysqli_num_rows($checkExist) == 0) $connection->query("INSERT INTO `setting` (`type`, `value`) VALUES ('PAYMENT_KEYS', '$paymentKeys')");    
}
?>
