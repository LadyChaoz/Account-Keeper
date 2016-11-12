# File: Makefile
# Anne Warden

begin:
	php pwkeep.php -i testfile.txt

export:
	php pwkeep.php -e outfile.txt

clean:
	rm -f *~ .pwkeep \#*# outfile.txt

renew:
	rm $(HOME)/.pwkeep/.*

show:
	ls -a $(HOME)/.pwkeep

submit.tar: README pwkeep.php
	tar -cvf aw308p4.tar README pwkeep.php
