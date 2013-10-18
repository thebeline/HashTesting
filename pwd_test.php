<html>
<head>
	<title>securepwd class test</title>
</head>

<body>

securepwd class test<br>
<br>

<form method=post action=pwd_test.php>
	User: <input type=text name=uid value="<? echo $GLOBALS["uid"]; ?>" size=30><br>
	Password: <input type=password name=pwd value="<? echo $GLOBALS["pwd"]; ?>" size=30><br>
	<input type=submit value="Check this password">
</form>

<?

	if ($GLOBALS["pwd"] != "")
	{
		include "securepwd.inc.php";

		$securepwd = new securepwd (true, true, true, true, $GLOBALS["uid"], 5, "passwords.txt");

		if ($securepwd->check ($GLOBALS["pwd"]))
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