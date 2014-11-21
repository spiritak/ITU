<?php
abstract class State {
	const active = 0;
	const inactive = 1;
	const waiting = 2;
	const rejected = 3;
	const expired = 4;

}

abstract class EmailType {
	const pop3 = 0;
	const imap = 1;
	const pop3imap = 2;
	const deactivated = 3;
}

$EmailTypeDict = array (
	0 => 'POP3',
	1 => 'IMAP',
	2 => 'POP3 & IMAP',
	3 => 'DEACTIVATED',
);

function getDefaultDomain($id)
{
	require('db.php');
	$qz = "SELECT name, tld, state FROM DOMAIN WHERE client='".$id."' LIMIT 1" ;
	$result = mysqli_query($conn,$qz);

	if ($result->num_rows != 0){
		$row = $result->fetch_assoc();
		return $row; 
	}

	return null;
}

function getWebHosting($id)
{
	require('db.php');
	$qz = "SELECT * FROM WEBHOSTING WHERE client='".$id."' LIMIT 1" ;
	$result = mysqli_query($conn,$qz);

	if ($result->num_rows != 0){
		$row = $result->fetch_assoc();
		return $row['id']; 
	}

	return null;
}

function getEmailAdress($id)
{
	if($id == null)
		return null;

	require('db.php');
	$qz = "SELECT alias, type, used, size FROM MAILBOX WHERE webhosting='".$id."' " ;
	$result = mysqli_query($conn,$qz);

	if ($result->num_rows != 0){
		return resultToArray($result); 
	}

	return array();
}

function addEmailAdress($id, $alias, $password, $type)
{
	if($id == null)
		return false;

	require('db.php');
	$qz = "INSERT INTO MAILBOX (alias, type, password, webhosting) VALUES ('".$alias."', '".$type."', '".$password."', '".$id."')";
	return $conn->query($qz);
}

function updateEmailAdress($id, $old_alias, $alias, $password, $type)
{
	if($id == null)
		return false;

	require('db.php');
	$qz = "UPDATE MAILBOX SET alias = '".$alias."', password = '".$password."', type = '".$type."' WHERE webhosting='".$id."' AND alias='".$old_alias."' ";
	return $conn->query($qz);

}

function deleteEmailAdress($id, $alias)
{
	if($id == null)
		return false;

	require('db.php');
	$qz = "DELETE FROM MAILBOX WHERE webhosting='".$id."' AND alias='".$alias."' ";
	return $conn->query($qz);
}


function resultToArray($result)
{
	$rows = array();
	while($row = $result->fetch_assoc()) {
		$rows[] = $row;
	}
	return $rows;
}
?>