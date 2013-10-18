<?

	/*
	securepwd v1.0.2b
	Class for checking the integrity of a given password.
	It basis its checks in a dictionary, or in a given word (normally a user name)
	By Llorenç Herrera [lha@hexoplastia.com]
	*/

	class securepwd
	{
		var $check_wordbased					= true;
		var $check_dictionary					= true;
		var $check_lessthan						= true;
		var $check_onlynumbers					= true;
		
		var $lessthan							= 5;
		
		var $dictionary_file;
		
		var $oliver_chardistance				= 2;
		var $oliver_percentage					= 50;
		
		var $baseword;
		
		var $result;
		
		var $error;
		
		
		// English descriptions for the errors
		var $errors = array
					  (
					  	"Based on the username",
						"",
						"Very similar words",
						"Both words are phonetically identical",
						"Both words are phonetically identical if spoked in english",
						"Password based on a dictionary word",
						"Not enough characters",
						"Only contains numbers"
					  );
		
		/*
		// Spanish descriptions for the errors
		var $errors = array
					  (
					  	"Basado en otra palabra",
						"",
						"Palabras muy parecidas",
						"Las dos palabras son fonéticamente iguales",
						"Las dos palabras son fonéticamente iguales en inglés",
						"Basado en una palabra de diccionario",
						"Pocos caracteres",
						"Sólo contiene números"
					  );
		*/
		function securepwd ($check_wordbased, $check_dictionary, $check_lessthan, $check_onlynumbers, $baseword, $lessthan, $dictionary_file)
		{
			$this->check_wordbased			= $check_wordbased;
			$this->check_dictionary			= $check_dictionary;
			$this->check_lessthan			= $check_lessthan;
			$this->check_onlynumbers		= $check_onlynumbers;
			$this->baseword					= $baseword;
			$this->lessthan					= $lessthan;
			$this->dictionary_file			= $dictionary_file;
		}

		function check ($password)
		{
			$password = strtolower ($password);
			
			$ok = true;
			
			// Base word based checks
			if ($this->check_wordbased)
			{
				if ($this->baseword == "")
				{
					echo "Base word not defined.";
					$ok = false;
				}
				
				// If baseword is contained in password
				if (stristr ($password, $this->baseword))
				{
					$this->result[] = $this->baseword." contained in ".$password;
					$this->error[] = 0;
					$ok = false;
				}else
					$this->result[] = $this->baseword." is not contained in ".$password;
					
				// If similarity between baseword and password is less than the specified percentage, based on Oliver algorythm
				$chardistance = similar_text ($password, $this->baseword, &$percentage);
				if ($percentage >= $this->oliver_percentage || (strlen ($password)-$chardistance) < $this->oliver_chardistance)
				{
					$this->error[] = 2;
					$ok = false;
				}
				$this->result[] = "Oliver distance between ".$this->baseword." and ".$password." is ".$chardistance." (difference of ".(strlen ($password)-$chardistance).")";
				$this->result[] = "Oliver equality percentage between ".$this->baseword." and ".$password." is ".$percentage."%";
					
				// If baseword and password hace the same soundex
				if (soundex ($password) == soundex ($this->baseword))
				{
					$this->result[] = $this->baseword." sounds like ".$password." based on Soundex";
					$this->error[] = 3;
					$ok = false;
				}else
					$this->result[] = $this->baseword." not sounds like ".$password." based on Soundex";
					
				// If baseword and password hace the same metaphone
				if (metaphone ($password) == metaphone ($this->baseword))
				{
					$this->result[] = $this->baseword." sounds like ".$password." based on Metaphone";
					$this->error[] = 4;
					$ok = false;
				}else
					$this->result[] = $this->baseword." not sounds like ".$password." based on Metaphone";
			}
			
			// Dictionary based checks
			$dictbased = false;
			if ($this->check_dictionary)
			{
				$fp = fopen ($this->dictionary_file, "r");
				if (!$fp || !is_file ($this->dictionary_file))
				{
					echo "Error opening dictionary file.";
					$ok = false;
				}
				while ($line = fgets($fp, 1024))
				{
					if ($password == trim ($line))
					{
						$dictbased = true;
						$this->error[] = 5;
						$ok = false;
						break;
					}
				}
				fclose ($fp);
				if ($dictbased)
					$this->result[] = $password." is based on a dictionary word.";
				else
					$this->result[] = $password." is not based on a dictionary word.";
			}
			
			// Lessthan string size check
			if (strlen ($password) < $this->lessthan)
			{
				$this->result[] = $password." has less than ".$this->lessthan." characters";
				$this->error[] = 6;
				$ok = false;
			}else
				$this->result[] = $password." has more than ".$this->lessthan." characters";
				
			// Check for only numbers
			
			if ($this->check_onlynumbers)
			{
				$check = false;
				for ($i=0; $i<strlen ($password); $i++)
					if (!is_numeric (substr ($password, $i, 1)))
						$check = true;
	
				if (!$check)
				{
					$this->result[] = $password." has only numbers";
					$this->error[] = 7;
					$ok = false;
				}else
					$this->result[] = $password." has not only numbers";
			}
				
			return $ok;
		}
		
		function geterror ()
		{
			while (list (, $error) = each ($this->error))
				$retr .= "<li> ".$this->errors[$error]."<br>";
			return $retr;
		}
		
		function getreport ()
		{
			while (list (, $report) = each ($this->result))
				$retr .= "<li> $report<br>";
			return $retr;
		}
		
	}	

?>