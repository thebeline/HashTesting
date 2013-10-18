<?php
function securePass($lengthMin = 10, $lengthMax = NULL)
{   
    
    $underscores = 2; // Maximum number of underscores allowed in password
    
    if ($lengthMax = NULL || $lengthMax <= $lengthMin)
    	$length = $lengthMin;
    else
    	$length = mt_rand($lengthMin, $lengthMax);
   
    $p ="";
    for ($i=0;$i<$length;$i++)
    {   
        $c = mt_rand(1,7);
        switch ($c)
        {
            case ($c<=2):
                $p .= mt_rand(0,9);			// Add a number
            break;
            case ($c<=4):
                $p .= chr(mt_rand(65,90));	// Add an uppercase letter
            break;
            case ($c<=6):
                $p .= chr(mt_rand(97,122));	// Add a lowercase letter
            break;
            case 7:
                 $len = strlen($p);
                if ($underscores>0&&$len>0&&$len<($length-1)&&$p[$len-1]!="_")
                {
                    $p .= "_";
                    $underscores--;   
                }
                else
                {
                    $i--;
                    continue;
                }
            break;       
        }
    }
    return $p;
}
?>
<?php
    function secureHash($text=NULL, $salt=NULL, $mode='whirlpool', $test=FALSE){
	
	if ($salt === NULL)		// This generates a salt hash.
            $salt = uniqid(mt_rand(), true);
        
	if ($test !== TRUE)
	    $saltHash = hash($mode, $salt);
	
	if ($text === NULL) {	// For creating random hashes.
	    
	    $outHash = hash($mode, uniqid(mt_rand(), true));
	
	} else {				// For secure string hashes and checks.
	    
	    $saltStart = strlen($text);
	    $textHash = hash($mode, $text);
	    
	    if ($test === TRUE)
		list($saltHash, $testHash) = str_split($salt, strlen($textHash));
	    
	    if($saltStart > 0 && $saltStart < strlen($saltHash)) {
		
		$textHashStart = substr($textHash,0,$saltStart);
		$textHashEnd = substr($textHash,$saltStart,strlen($saltHash));
		$outHash = hash($mode, $textHashEnd.$saltHash.$textHashStart);
		
	    } elseif($saltStart > (strlen($saltHash)-1)) {
		
		$outHash = hash($mode, $textHash.$saltHash);
	    
	    } else {
		
		$outHash = hash($mode, $saltHash.$textHash);
	    
	    }
	    
	    if ($test === TRUE && $saltHash.$outHash === $salt)
		return TRUE;
	    elseif ($test === TRUE)
		return FALSE;
	
	}
	
        return $saltHash.$outHash;
    
    }
?>
<?php
    function testSecureHash($text, $hash, $mode='whirlpool'){
        return secureHash($text, $hash, $mode, TRUE);
    }
?>
<?php
    
    if(count($_POST) > 0) {
	set_time_limit(1200);
	
	$i = 0;
	
	$l = (int) ($_POST['pass_length'] ?: 4);
	
	$password = $_POST['pass'] ?: securePass($l);
	
	$l = (int) strlen($password);
	
	//$c = ini_get('max_execution_time') * 15000;
	
	$c = ini_get('max_execution_time') * 7300;
	
	$c = 1000; // Don't hose my server...  :-D
	
	echo "<h2>Real Password:</h2>\n<p>$password</p>";
	
	$testStart = microtime(TRUE);
	
	$hash = secureHash($password);
	
	echo "<h2>Secure Hash:</h2>\n<p>$hash</p>";
	
	echo "<h2>Testing Passwords:</h2>\n<ul>\n";
	
	for ($i=0; $i < $c; $i++) {
		$test = ($_POST['test_rand']) ? securePass($l) : $password;
		echo "<li>$test</li>\n";
		if (testSecureHash($test, $hash)) {
		    echo "<li><b>Success!</b></li>";
		    break;
		}
	}
	
	echo "</ul>\n";
	
	echo "<h3>PHEW!! $i tries took ".round((microtime(TRUE) - $testStart) / 60, 2)." minutes!</h3>";
    }
?>

<html>
    <form method="post">
	Password (empty to generate):<br />
	    <input name="pass" type="text" /><br />
	Password Length (empty defaults to 4):<br />
	    <input name="pass_length" type="text" /><br />
	Test Rand (bruteforce): <input name="test_rand" type="checkbox" value"1"><br />
	<input type="submit" value="process" />
	
    </form>
</html>