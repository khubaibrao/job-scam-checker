# Phase 4 content and SEO architecture

Phase 4 installs a deliberately limited 30-page public information architecture:
one homepage, five checker/tool pages, two hubs, ten scam-pattern articles, seven
verification guides, and five trust/legal pages. It does not generate location,
company, salary, keyword-variant, or programmatic doorway pages.

## Installation and editorial safety

The content release is versioned separately from the rule database. On update,
the installer creates missing pages in parent-before-child order and stores SEO,
content-type, and curated-related-page metadata. It does not overwrite a page
that an administrator already created or edited. The exact untouched Phase 1 Home
and Job Scam Checker bodies have explicit safe upgrade paths.

All Phase 4 pages are published so links resolve immediately on a fresh MVP
installation. A site administrator should review the legal pages and configure a
real contact channel appropriate to the site operator and jurisdiction before a
public launch.

## Information flow

The fallback primary menu connects Home, the checker, Job Scam Categories, Guides
and About. The homepage introduces the checker first and then three educational
paths. Both hubs visibly link every child article. Article bodies link contextually
to the checker and relevant guides, while curated related cards provide a small
set of next reads. Footer fallback navigation exposes About, Contact, Privacy,
Terms and Disclaimer.

## Technical SEO

- WordPress provides clean hierarchical permalinks and canonical links.
- Curated metadata fields provide unique document titles and descriptions.
- Open Graph title, description, type, site name and URL are printed when a known
  SEO plugin has not taken over.
- Scam articles and guides receive honest `Article` JSON-LD using the site name as
  an organization, without invented people or credentials.
- Singular pages receive `BreadcrumbList` JSON-LD matching visible breadcrumbs.
- WordPress core supplies the XML sitemap; virtual robots.txt advertises it.
- Search results and 404 responses receive `noindex`; public content remains
  indexable unless WordPress site visibility is disabled.
- No review, rating, testimonial, FAQ-rich-result, or fabricated authority schema
  is emitted.

## Advertising readiness

Long articles contain at most two inert shortcode targets, one mid-article and one
near the lower content. The renderer outputs an empty hidden `aside`. No blank
space, ad copy, script, tracker, button-like treatment, or fake inventory appears
until a future real integration is intentionally configured.
