# Testing

Run all implemented checks from the repository root:

```bash
bash tools/test-all.sh
```

The suite performs PHP syntax checks, exercises the content installer against
WordPress-compatible stubs, confirms repeat activation does not duplicate pages,
renders and inspects the checker markup, verifies required approved copy, and
checks production PHP/CSS/JavaScript for obvious external API/CDN dependencies.

Phase 2 adds mixed legitimate/scam fixtures, rule-library validation, risk
thresholds, overlap and repetition protection, URL/domain classification,
security-warning false positives, XSS-oriented output checks, rate limiting,
schema assertions, nonce rejection, and request-size rejection.

Phase 3 tests Low, Caution, High, and Very High result contracts; contextual and
universal safety actions; suspicious-domain reasons; probabilistic wording;
accessible relationships, announcements, progress, and alerts; no-JavaScript and
malformed-response handling; DOM-safe rendering; phone/mobile breakpoints; print
rules; and the empty hidden advertising integration target.

Phase 4 validates the exact page inventory and type counts, hierarchical path
uniqueness, internal-link resolution, related-key integrity, title/description
uniqueness, minimum article depth, heading hierarchy, required fictional-example
and safety sections, pairwise content similarity, empty ad targets, robots and
sitemap integration, permitted schema types, visible breadcrumbs, accurate
privacy claims, hub completeness, and absence of rating/review claims.

After deployment to a WordPress staging site, manually verify:

- Plugin and theme activation with debug logging enabled
- Homepage at phone, tablet, and desktop widths
- Keyboard navigation, visible focus, skip link, mobile menu, and Escape behavior
- Checker character counter, loading/error states, maximum length, and results
- REST endpoint behavior for low, some, high, and very-high risk examples
- Home/checker page creation and repeat activation
- Permalinks, page title, description, and Open Graph tags
- Theme behavior when the plugin is inactive
