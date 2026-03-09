.PHONY: build dev pull up install

build: up
	ddev exec npm run build
dev: build
install: up build
	ddev composer install
	ddev exec php app/craft setup/app-id \
		$(filter-out $@,$(MAKECMDGOALS))
	ddev exec php app/craft setup/security-key \
		$(filter-out $@,$(MAKECMDGOALS))
	ddev exec php app/craft install \
		$(filter-out $@,$(MAKECMDGOALS))
	ddev exec php app/craft plugin/install ckeditor || true
	ddev exec php app/craft plugin/install image-resizer || true
	ddev exec php app/craft plugin/install knock-knock || true
	ddev exec php app/craft plugin/install site-switcher || true
	ddev exec php app/craft plugin/install vite || true
	ddev exec php app/craft plugin/install pragmatic-web-toolkit || true
up:
	if [ ! "$$(ddev describe | grep OK)" ]; then \
        ddev auth ssh; \
        ddev start; \
    fi
%:
	@:
# ref: https://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line
