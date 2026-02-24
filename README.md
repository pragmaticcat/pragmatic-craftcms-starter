## Sobre el projecte

A Craft CMS starter project using DDEV for local hosting and Vite for front-end bundling and HMR.

## Notable Features:

- [DDEV](https://ddev.readthedocs.io/) for local development
- [Vite 5.x](https://vitejs.dev/) for front-end bundling & HMR
- [Makefile](https://www.gnu.org/software/make/manual/make.html) for common CLI commands

## Local machine prerequisites:

1. [Docker](https://www.docker.com/)
2. [DDEV](https://ddev.readthedocs.io/), minimum version 1.19
3. Optional but recommended, [Composer](https://getcomposer.org/)

## Getting Started

### Option 1: With Composer (recommended)

If you have [Composer](https://getcomposer.org/) installed on your local machine,
you can use `create-project` to pull the latest tagged release.

Open terminal prompt, and run:

```shell
composer create-project pragmaticcat/pragmatic-craftcms-starter PATH --no-install
```

Make sure that `PATH` is a **new** or **existing and empty** folder.

### Option 2: With Git CLI

Alternatively you can clone the repo via the Git CLI:

```shell
git clone git@github.com:pragmaticcat/pragmatic-craftcms-starter.git PATH
```

Make sure that `PATH` is a **new** _or_ **existing and empty** folder.

Next, you'll want to discard the existing `/.git` directory. In the terminal, run:

```shell
cd PATH
rm -rf .git
```

Last, clean up and set some default files for use:

```shell
cp app/.env.example app/.env
cp app/composer.json.default app/composer.json
cp .gitignore.default .gitignore
rm CHANGELOG.md && rm LICENSE.md
```

### Option 3: Manual Download

Download a copy of the repo to your local machine and move to where you want to your project to run. Similar to above, you'll then want to clean up and set some default files for use. In the terminal, run:

```shell
cd PATH
cp app/.env.example app/.env
cp app/composer.json.default app/composer.json
cp .gitignore.default .gitignore
rm CHANGELOG.md && rm LICENSE.md
```

## Configuring DDEV

_Note: This section is optional. If you are simply test-driving this project, feel free to skip to the next section. ⚡_

To configure your project to operate on a domain other than `https://craftcms.ddev.site`, run:

```shell
ddev config
```

Follow the prompts.

- **Project name:** e.g. `mysite` would result in a project URL of `https://mysite.ddev.site` (make note of this for later in the installation process)
- **Docroot location:** defaults to `web`, keep as-is
- **Project Type:** defaults to `php`, keep as-is

## Installing Craft

To install a clean version of Craft, run:

```shell
make install
```

Follow the prompts.

This command will:

1. Start your DDEV project (with SSH auth)
2. Build all front-end assets
3. Install Composer dependencies
4. Generate `APP_ID` and save to your `.env` file
5. Generate `SECURITY_KEY` and save to your `.env` file
6. Install Craft for the first time, allowing you to set the admin's account credentials
7. Install all Craft plugins

Once the process is complete, type `ddev launch` to open the project in your default browser. 🚀

## Local development with Vite

To begin development with Vite's dev server & HMR, run:

```shell
make dev
```

This command will:

1. Start your DDEV project (with SSH auth)
2. Build all front-end assets

Open up a browser to your project domain to verify that Vite is connected. Begin crafting beautiful things. ❤️

## Makefile

A Makefile has been included to provide a unified CLI for common development commands.

- `make install` - Runs a complete one-time process to set the project up and install Craft.
- `make up` - Starts the DDEV project with SSH auth.
- `make dev` - Starts the DDEV project and builds all front-end assets.
- `make build` - Builds all front-end assets.

## Craft CMS Plugins

1. [CKEditor](https://plugins.craftcms.com/ckeditor)
1. [Knock Knock](https://plugins.craftcms.com/knock-knock)
1. [Pragmatic Web Toolkit](https://github.com/pragmaticcat/web-toolkit-craftcms-plugin)
1. [Vite](https://plugins.craftcms.com/vite)
1. [Image Resizer](https://plugins.craftcms.com/image-resizer)
1. [Site Switcher](https://plugins.craftcms.com/site-switcher)

## Javascript Libraries

1. [Bootstrap](https://getbootstrap.com/)
1. [jQuery](https://jquery.com/)
1. [AOS](https://michalsnik.github.io/aos/)
1. [GSAP](https://gsap.com/)
1. [Fancyapps UI](https://fancyapps.com/ui/)
1. [Lazysizes](https://afarkas.github.io/lazysizes/)

## Acknowledgements & Credits

Aside from the obvious gratitude owed to the entire team at [Pixel & Tonic](https://pixelandtonic.com/) for their tireless work on Craft, a special thanks goes out to Andrew Welch of [nystudio107](https://nystudio107.com/). Not only has he developed some of the most widely used plugins in the Craft ecosystem, he has dedicated countless time and energy to pushing all of us in the community to excel at everything we do. He has an uncanny ability to see through the fog of development war to know what's best - not just for us, but for our future selves, our clients, and the users of the sites we build. His contributions have made all of our sites perform better in SEO, run faster in the browser, and made our development workflow more streamlined and efficient. Hats off to you, sir.
