<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="bg" lang="bg">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Title</title>
</head>
<body>
<?php
include 'layout/header.php';
include 'layout/menu.php'; 
include 'layout/contents/' . $contentTypeToFile[$requestData['content']];
if (!isset($requestData['user'])) {
	include 'layout/fastloginform.php';
}
include 'layout/footer.php';
?>
</body>
</html>