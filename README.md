# Account-Keeper
A PHP program for storing passwords.  
Programmer: Anne Warden

Welcome to pwkeep!

pwkeep is a password keeper program. Passwords are stored in a hidden database under your home directory. This database will not give read access to any user and will be created if it does not already exists. Upon running the program for the first time, a user will be prompted to input a password, which will then be used to make subsequent accesses to the application.

pwkeep is called from the command line using expressions. They are listed below: 
* -a URL ..... *Adds an entry for a specific url*
* -e FILE ..... *Exports data to a file*
* -i FILE ..... *Imports data from a file*
* -ls all ..... *Displays a list of accounts*
* -ls URL ..... *Displays a list of accounts for a specific url*
* -mv URL ..... *Replaces an entry for a specific url*
* -rm URL ..... *Deletes an entry for a specific url*
* -s STRING ..... *Displays all entries with a specific string*

If an expression is not given, the program assumes a value of '-README' which displays the list of options above. 

EXPRESSIONS
-----------
The funciton for adding entries (-a) requires the properly formatted url to be included on the command line and will prompt the user to obtain account information. All entries will be added at the end of the file, regardless of duplicates or blank fields. 

The function for exporting all entries (-e) requires an output file name. The file will be exported using the format at the end of this file. If the file already exists it will prompt the user to overwrite the file. Any response other than '-y' will not overwrite the file.

The function for importing new entries (-i) requires a properly formatted input file. The appropriate format is listed at the end of this file. After receiving this file, the program stores user information under the URL within the system by appending the data to the end of the appropriate file. 

The function for displaying a list of accounts stored in the system (-ls) requires one of two options. The user may run the function for one URL or for all URL's. This list will be displayed in the terminal with the following format:
~~~~  
url|userid|password|Comment|
~~~~  
If the user chooses to display account information for all URL's in the system, the list will be separated by URL, where the URL's are in alphabetical order.

The function for replacing an entry under a specific URL (-mv) requires a properly formatted URL in the command line. The user will be prompted for a search string. The program will then list the first entry for replacement and prompt the user for correctness. If the user answers '-y' the entry will be replaced with user input, otherwise the program will continue searching until the next match or the end of file. This capability is currently non-functional.

The function for deleting an entry under a specific URL (-rm) requires a properly formatted URL in the command line. The user will be prompted for a search string. The program will then list the first entry for deletion and prompt the user for correctness. If the user answers '-y' the entry will be deleted, otherwise the program will continue searching until the next match or the end of file. This capability is currently non-functional.

The function for displaying a list of any accounts with a matching string (-s) will search the entire directory to find instances of the string in any entry. It will then display the list for the user.

FILE FORMAT
-----------
Files should be formatted as below, where # is the number of entries and * is a string. For the sake of consistency, URL's should not include a server name of 'www.' 
~~~~
PWTEXT #  
  
url=*.*  
userid=*  
passwd=*  
comment=*  
  
...  
~~~~
