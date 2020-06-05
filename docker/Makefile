start:
	docker-compose up -d --build

down:
	docker-compose down

autoinstall: start
	docker-compose -f docker-compose.yml run --rm wp-auto-config

clean: down
	@echo "💥 Removing related folders/files..."
	@rm -rf  mysql wordpress

reset: clean

