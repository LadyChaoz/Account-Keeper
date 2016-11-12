<?php
// Anne Warden

$home = getenv("HOME");
$pwkeep = "$home/.pwkeep";
$permissions = 0777; //CHANGE TO 0333

//Create the pwkeep library system
if(!file_exists("$pwkeep"))
   mkdir("$pwkeep", $permissions);
$dhandle = opendir("$pwkeep");

// Create a password if non-existant 
if(!file_exists("$pwkeep/.password.txt"))
{
   // Create password on first run
   $fhandle = fopen("$pwkeep/.password.txt", "w");

   // Prompt user
   printf("Please make a password: ");
   $input = get_input();
   
   // Write to file
   fwrite($fhandle, "$input");
   fclose($fhandle);
}

// catch instruction errors
if($argc < 2) $argv[1] = "-README";
else if($argc < 3)
{
   echo "php $argv[0] $argv[1] requires more data.\n";
   echo "Here is the expected syntax:\n";
   dispREADME();
   echo "What do you wish to do? (-q to quit) ";
   $argv[2] = get_input();
   if ($argv[2] == "-q")
   {
      closedir($dhandle);
      exit;
   }
}

// parse instructions
switch ($argv[1]){
   case "-ls": // display a list URL OR ALL
      if(!check_pass($pwkeep))
         break;
      if($argv[2] == "all")
      {
         echo "url|userid|password|comment|\n";
	 while(false !== ($entry = readdir($dhandle))){
	    if($entry != ".password.txt")
	       disp_entries($pwkeep, trim($entry, "."));
	 }
      }
      else
      {
         echo "url|userid|password|comment|\n";
	 disp_entries($pwkeep, $argv[2]);
      }
      break;
   case "-a": // add an account based on url
      // Open file and move pointer to end
      $fhandle = fopen("$pwkeep/.$argv[2]", "c");
      fseek($fhandle, 0, SEEK_END);

      // Prompt user for input
      echo "What is the userid? ";
      $userid = get_info();
      echo "What is the password? ";
      $pass = get_info();
      echo "Please include a comment: ";
      $comment = get_info;

      // Write to file
      fwrite($fhandle, "$userid|$pass|$comment|\n");
      
      //Close File
      fclose($fhandle);
      break;
   case "-mv": // replace an account based on url
      if(!check_pass($pwkeep))
         break;
      if(!file_exists("$pwkeep/.$argv[2]"))
         break;
      
      // Prompt user for target
      echo "What entry would you like to replace? ";
      $input = get_input();
      if ($input == NULL)
      {
         echo "Don't waste my time. Next time, please enter a value. Later!\n";
         break;
      }
      $cursor = 0;

      // Open file
      $fhandle = fopen("$pwkeep/.$argv[2]", "r+");
      $proceed = false;
      while($proceed == false && !feof($fhandle))
      {
         // Find next entry
         $cursor = return_first($pwkeep, $argv[2], $input, $cursor);
         // Check with user
         if(check_with_user($pwkeep, $argv[2], $cursor))
            $proceed = true;
	 fseek($fhandle, $cursor, SEEK_SET);
      }
      
      // Close file
      fclose($fhandle);
      break;
   case "-rm": // delete an account based on url
      if(!check_pass($pwkeep))
         break;
      echo "For $argv[1] you can delete accounts based on url\n";
      // Open file
      $fhandle = fopen("$pwkeep/.$argv[2]", "r+");

      // Close file
      fclose($fhandle);
      break;
   case "-s": // search entries for string and display
      if(!check_pass($pwkeep))
         break;
      echo "url|userid|password|comment|\n";

      while(false !== ($entry = readdir($dhandle))){
         $entry = trim($entry, ".");
	 if($entry != "password.txt")
	 {
	    $fhandle = fopen("$pwkeep/.$entry", "r");
      	    while(!feof($fhandle)){
               $line = trim(fgets($fhandle));
	       if (strpos($line, $argv[2]) || strpos($entry, $argv[2]))
	          echo "$entry|$line\n";
            }
	    fclose($fhandle);
      	 }
      }
      break;
   case "-i": // import data
      // Check to see that the source file exists and open
      if(!file_exists("$argv[2]"))
      {
         echo "Cannot import data from file. File does not exist\n";
	 break;
      }
      $fhandle = fopen("$argv[2]", "r");
      
      // Import data
      $header = explode(" ", trim(fgets($fhandle)));
      if($header[0] != "PWTEXT")
      {
         echo "File does not have the proper format!\n";
	 echo "See the README for instructions on how to format files.\n";
         break;
      }
      // Import account information
      for($i=0; $i<$header[1]; $i++){
         fgets($fhandle);// blank line
      	 fgets($fhandle, 5);//url
      	 $url=trim(fgets($fhandle));
      	 //Create file
	 $fin = fopen("$pwkeep/.$url", "a");
	 for($j=0; $j<3; $j++)
	 {
	    //Write to file
      	    $info=explode("=", trim(fgets($fhandle)));
	    fwrite($fin, "$info[1]|");
	 }
	 fwrite($fin, "\n");
	 fclose($fin);
      }
      
      // Close out files
      fclose($fhandle);
      break;
   case "-e": // export data
      if(!check_pass($pwkeep))
         break;
      // Check if file exists, prompt for overwrite
      if(file_exists($argv[2]))
      {
         echo "File already exists. Overwrite? (-y for yes) ";
	 $input = get_info();
	 if ($input != "-y")
	    break;
      }
      // Open the target file
      $fhandle = fopen("$argv[2]", "w");
      fwrite($fhandle, "PWTEXT #\n");

      // Export Data
      $count = 0;
      while (false !== ($entry = readdir($dhandle))){
         $entry = trim($entry, ".");
	 if($entry != "password.txt")
	 {
	    $fin = fopen("$pwkeep/.$entry", "r");

	    while(!feof($fin)){
	       $line = fgets($fin);
	       if(!feof($fin))
	       {
	          $part = explode("|", $line);
	          fwrite($fhandle, "\nurl=$entry");
		  fwrite($fhandle, "\nuserid=$part[0]");
		  fwrite($fhandle, "\npasswd=$part[1]");
		  fwrite($fhandle, "\ncomment=$part[2]\n");
		  $count = $count + 1;
	       }
	    }

	    fclose($fin);
	 }
      }

      // Close out files
      rewind($fhandle);
      fwrite($fhandle, "PWTEXT $count");
      fclose($fhandle);
      break;
   default:
      echo "php $argv[0] requires a command.\n";
      echo "Here is a list of commands:\n";
      dispREADME();
}

closedir($dhandle);

?>
<?php

function check_pass($path)
{
   // Retrieve the password from the filesystem
   $fhandle = fopen("$path/.password.txt", "r");

   // Prompt for password
   $terminal = fopen("php://stdin", "r");
   echo "Please input the password: ";

   // Compare user input to existing password
   if(trim(fgets($terminal)) == fgets($fhandle))
      $bool = true;
   else
   { 
      $bool = false;
      echo "Passwords do not match!\n";
   }

   // Close and Return
   fclose($fhandle);
   fclose($terminal);
   return $bool;
}

function dispREADME()
{
   echo "\t-a URL  \tAdds an entry for a specific url\n";
   echo "\t-e FILE \tExports data to a file\n";
   echo "\t-i FILE \tImports data from a file\n";
   echo "\t-ls all \tDisplays a list of accounts\n";
   echo "\t-ls URL \tDisplays a list of accounts for a specific url\n";
   echo "\t-mv URL \tReplaces an entry for a specific url\n";
   echo "\t-rm URL \tDeletes an entry for a specific url\n";
   echo "\t-s STRING\tDisplays all entries with a specific string\n";
}

function disp_entries($location, $url)
{
   if(!file_exists("$location/.$url"))
      return;

   $fhandle=fopen("$location/.$url", "r");

   while(!feof($fhandle)){
      $line = fgets($fhandle);
      if(!feof($fhandle))
         echo "$url|$line";
   }

   fclose($fhandle); 
}

function get_input()
{
   $terminal = fopen("php://stdin", "r");
   $input = trim(fgets($terminal));
   fclose($terminal);
   return $input;
}

function return_first($location, $url, $target, $offset)
{
   $fhandle = fopen("$location/.$url", "r");
   $found = false;
   $pointer = ftell($fhandle);
   fseek($fhandle, $offset, SEEK_SET);
   while(!feof($fhandle) || !$found){
      $line = trim(fgets($fhandle));
      if (strpos($line, $target) || strpos($url, $target))
	 $found = true;
      else
         $pointer = ftell($fhandle);      
   }
   //echo "$line\n";
   fclose($fhandle);
   return $pointer;
}

function check_with_user($location, $url, $offset)
{
   // Open and move pointer
   $fhandle = fopen("$location/.$url", "r");
   $terminal = fopen("php://stdin", "r");
   fseek($fhandle, $offset, SEEK_SET);

   // Pull info and prompt user
   $line = trim(fgets($fhandle));
   echo "The entry you are trying to modify is: \n";
   echo "\t$url|$line\n";
   echo "Are you sure? (-y for yes) ";
   $input = trim(fgets($terminal));

   // Handle response
   if ($input == "-y")
      $yno = true;
   else
      $yno = false;

   //Close and Return
   fclose($fhandle);
   fclose($terminal);
   return $yno;
}

?>