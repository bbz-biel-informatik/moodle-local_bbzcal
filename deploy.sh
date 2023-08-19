ssh -t root@moodle.bbz-biel.ch 'rm -rf /var/www/html/moodle/local/bbzcal'
ssh -t root@moodle.bbz-biel.ch 'mkdir /var/www/html/moodle/local/bbzcal'
scp -r * root@moodle.bbz-biel.ch:/var/www/html/moodle/local/bbzcal
