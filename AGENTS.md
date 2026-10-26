# Craft CMS starter — working guide

## Environment and commands

Use DDEV for PHP, Composer, Craft and npm commands. The project uses Craft CMS 5,
PHP 8.3, Node 20 and Vite. Run `ddev start`, `ddev composer install`,
`ddev npm install`, `ddev npm run serve` or `ddev npm run build` as needed.
Use `ddev exec php craft` for Craft CLI commands in this checkout.
Run `ddev exec php craft up` after changing project config or migrations.

## Structure

- `templates/layouts/base.twig` is the site page shell.
- `templates/includes/` holds site-wide parts and reusable Twig macros.
- `templates/pages/` holds entry templates.
- `templates/slices/{handle}.twig` renders each Matrix slice. Matching component
  styles belong in `src/scss/components/` or a project's `src/scss/slices/`.
- `modules/sitemodule/` holds custom PHP behavior. Keep controllers small and
  put reusable business logic in services.
- `config/project/` is Craft project config. Commit its changes together with
  the templates or module code that use them.

## Slice headings

`templates/includes/macros/heading.twig` keeps one H1 on pages built from slices.
When a slice has a visible headline, add its handle to `headlineSliceHandles`
in the page template, pass `headingTag` to its include, and render the headline
with `<{{ headingTag }}>…</{{ headingTag }}>`. Keep handles without a visible
headline out of the list. If the page prints its own H1, pass `pageHasH1=true`.

## Media

Use `templates/includes/macros/media.twig` for Craft image or video assets.
`cover(asset, opts)` accepts `transform`, `sizes`, `loading`, `fetchpriority`,
`alt`, `poster`, `fallbackVideo` and `background`. The default image transforms
are `fit500`, `fit1000`, `fit1500` and `fit2000`. Supply a poster and fallback
video explicitly; do not assume project-specific asset fields.
`figure(asset, opts)` adds an optional caption and aspect ratio. Add new
transforms to both Craft project config and the macro's width map.

## SEO and missing URLs

`templates/includes/head.twig` uses the current entry or category for SEO tags.
Pages without an element get a title and `noindex`. Craft renders
`templates/404.twig` with HTTP 404 for unmatched URLs. Put migrations from old
URLs in `config/redirects.php`; do not add a catch-all route that returns 200.

## Before finishing

Check `git diff`, run a Vite build for front-end changes, and verify the affected
Craft templates through DDEV. Keep client-specific content and credentials out
of the starter.

## Blog / Actualitat

The Actualitat landing page is an entry in the `page` channel (slug `actualitat`)
containing one `sliceActualitat` Matrix block. The optional `sliceHomeActualitat`
block shows recent posts elsewhere. Article entries use the `pageArticle` entry
type, with featured image, categories, excerpt, `mostraExtracte`, the `slices`
field (using the `slicesArticle` Matrix configuration), and SEO. The article Matrix has five block types: text, quote, images, featured
image and video. `templates/includes/articlesActualitat.twig` supplies the
featured items, category filter, grid and pagination shared by the landing
page and category archives. Keep Bootstrap styling in `src/scss/components/`.
