@echo off
cls

echo *********************************************
echo *** DELETE ALL FILES IN THE _docs FOLDER ****
echo *********************************************
pause

phpdoc -d ./ -f ../../PEAR/PEAR.php -i _svn*,class.*.php -t _docs -dn verysimple -dc Maui -ti "VerySimple API Documentation"

echo ******************
echo *** FINISHED! ****
echo ******************
pause