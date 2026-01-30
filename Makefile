.PHONY: build dev pull up install

build: up
	ddev exec npm run build
dev: build
	ddev exec npm run serve
install: up build
	ddev exec php craft setup/app-id \
		$(filter-out $@,$(MAKECMDGOALS))
	ddev exec php craft setup/security-key \
		$(filter-out $@,$(MAKECMDGOALS))
	ddev exec php craft install \
		$(filter-out $@,$(MAKECMDGOALS))
	ddev exec php craft plugin/install ckeditor
	ddev exec php craft plugin/install image-resizer
	ddev exec php craft plugin/install knock-knock
	ddev exec php craft plugin/install translate
	ddev exec php craft plugin/install craft-siteswitcher
	ddev exec php craft plugin/install craft-seomatic
	ddev exec php craft plugin/install vite
up:
	if [ ! "$$(ddev describe | grep OK)" ]; then \
        ddev auth ssh; \
        ddev start; \
    fi
%:
	@:
# ref: https://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line
