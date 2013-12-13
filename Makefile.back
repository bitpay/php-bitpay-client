test:
	./vendor/bin/phpunit
sniff:
	./vendor/bin/phpcs -p --standard=phpcs.xml .

lint:
	./vendor/bin/parallel-lint --exclude vendor .

.PHONY: test sniff lint