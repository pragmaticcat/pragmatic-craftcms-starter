.PHONY: build dev pull up install

build: up
	ddev exec npm run build
dev: build
install: up build
	ddev exec php app/craft setup/app-id \
		$(filter-out $@,$(MAKECMDGOALS))
	ddev exec php app/craft setup/security-key \
		$(filter-out $@,$(MAKECMDGOALS))
	ddev exec php app/craft install \
		$(filter-out $@,$(MAKECMDGOALS))
	ddev exec php app/craft plugin/install ckeditor
	ddev exec php app/craft plugin/install image-resizer
	ddev exec php app/craft plugin/install knock-knock
	ddev exec php app/craft plugin/install craft-siteswitcher
	ddev exec php app/craft plugin/install craft-vite
	ddev exec php app/craft plugin/install web-toolkit-craftcms-plugin
up:
	if [ ! "$$(ddev describe | grep OK)" ]; then \
        ddev auth ssh; \
        ddev start; \
    fi
%:
	@:
# ref: https://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line
