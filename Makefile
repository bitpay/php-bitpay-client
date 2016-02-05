setup:
	./composer.phar install
	npm install

test:
	php ./bin/phpunit -c build/

phantomjs:
	phantomjs --webdriver=8643 --ignore-ssl-errors=yes &
