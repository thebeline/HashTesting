<?PHP
 // open dictionary handle
 $dict = crack_opendict("/usr/local/share/cracklib/pw_dict") or die('Cannot create dictionary handle');   

 // assume this is the password supplied by the user
 $pwd = 'prker9a^h2';   

 // check password
 crack_check($dict, $pwd);
 echo crack_getlastmessage();   

 // close dictionary handle
 crack_closedict($dict);

?>