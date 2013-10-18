<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>securepwd class test</title>
</head>

<body>

securepwd class test<br>
<br>

<form method=post action=test.php>
	User: <input type=text name=uid value="<? echo $_POST["uid"]; ?>" size=30><br>
	Password: <input type=password name=pwd value="<? echo $_POST["pwd"]; ?>" size=30><br>
	<input type=submit value="Check this password">
</form>

<?

	if ($_POST["pwd"] != "")
	{
		include "securepwd.inc.php";

		$securepwd = new securepwd (true, true, true, true, $_POST["uid"], 5, "passwords.txt");

		if ($securepwd->check ($_POST["pwd"]))
		{
			echo "This password is secure.";
		}
		else
		{
			echo "This password is not secure:<br>";
			echo $securepwd->geterror ();
		}

		echo "<br>Report:<br>";

		echo $securepwd->getreport ();

	}

?>

</body>
</html>