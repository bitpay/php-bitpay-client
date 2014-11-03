setup:
	composer.phar install
	npm install

test:
	php ./bin/phpunit -c build/

phantomjs:
	./node_modules/.bin/phantomjs --webdriver=4444 --ssl-protocol=TLSv1 --ignore-ssl-errors=yes &
