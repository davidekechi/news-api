.PHONY: qc phpstan php-cs-fixer fix

# Run static analysis and coding standards checks (without fixing)
stan: 
	vendor/bin/phpstan analyse -c phpstan.neon.dist --memory-limit=512M


# Auto-fix coding standards issues
fix:
	vendor/bin/php-cs-fixer fix
