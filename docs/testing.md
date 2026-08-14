# Testing

Run all Phase 1 checks from the repository root:

```bash
bash tools/test-phase1.sh
```

The suite performs PHP syntax checks, exercises the content installer against
WordPress-compatible stubs, confirms repeat activation does not duplicate pages,
renders and inspects the checker markup, verifies required approved copy, and
checks production PHP/CSS/JavaScript for obvious external API/CDN dependencies.

After deployment to a WordPress staging site, manually verify:

- Plugin and theme activation with debug logging enabled
- Homepage at phone, tablet, and desktop widths
- Keyboard navigation, visible focus, skip link, mobile menu, and Escape behavior
- Checker character counter, maximum length, and Phase 1 notice
- Home/checker page creation and repeat activation
- Permalinks, page title, description, and Open Graph tags
- Theme behavior when the plugin is inactive
