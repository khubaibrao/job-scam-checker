# Phase 3 results experience

The checker now turns the Phase 2 analysis response into an accessible risk
assessment. Results use a numeric score, explicit text label, summary sentence,
and warning count. Color supports the hierarchy but never carries meaning alone.

## Result sections

- Score and Low, Caution, High, or Very High Risk label
- Each detected warning sign with “Why it matters” and “What to do” details
- Suspicious domains displayed as non-clickable plain text with specific reasons
- Deduplicated recommended actions based on detected categories
- Universal reminders about money, passwords/codes, personal information,
  independent verification, and the official careers page
- A clear statement that automated analysis cannot prove legitimacy or fraud

The browser never injects response strings as HTML. It constructs elements and
sets text content. The full URL path and query are not returned for suspicious
links, reducing accidental exposure and preventing clickable scam destinations.

## Accessibility and resilience

The result region has an accessible name and receives focus after analysis. A
polite atomic status announces the score, risk level, and warning count. The score
also uses a semantic `progress` element. Failure output is a focusable alert with
a retry action. Every checker instance has unique form and result IDs.

JavaScript-disabled visitors receive a clear privacy-safe explanation and manual
verification advice. Network, invalid JSON, API, nonce, request-limit, and other
failures use the same visible alert state. Scrolling honors reduced-motion
preferences.

## Print and advertising

Print CSS removes the form, navigation, footer, buttons, errors, and ad area while
keeping the assessment, warnings, domains, actions, and disclaimer. Results are
never forced visible before an analysis exists.

An empty, hidden `data-jsc-ad-slot` appears after the result component as a future
AdSense integration target. It contains no ad, fake creative, placeholder claim,
tracking code, or reserved blank space until a real integration is configured.
