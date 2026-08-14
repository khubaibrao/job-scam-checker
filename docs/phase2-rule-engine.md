# Phase 2 rule engine

The checker analyzes messages entirely inside WordPress. It does not call a
model, reputation API, URL expander, or remote service. It also does not insert,
update, log, or cache the submitted message.

## Rule storage

Activation or a schema-version upgrade creates `{prefix}_jsc_rules` through
WordPress `dbDelta`. Each row has a stable slug, name, match type, pattern,
category, scoring group, weight, explanation, recommendation, enabled flag, and
priority. Missing defaults are inserted without overwriting an existing rule.

The initial library contains phrase, regex, contextual-combination, shortened
URL, messaging URL, free-hosting domain, unusual TLD, punycode, and raw-IP URL
rules. Phase 6 will provide the administration interface for these rows.

## Scoring

Each rule can match only once per message. Only the highest-weight detection in
an overlapping `score_group` contributes. Category caps prevent many similar
phrases from dominating the score. The final score is clamped to 0–100:

- 0–24: Low Risk Indicators
- 25–49: Some Warning Signs
- 50–74: High Risk
- 75–100: Very High Risk

A low score never certifies legitimacy, and all responses include an independent
verification disclaimer.

## Endpoint safeguards

`POST /wp-json/job-scam-checker/v1/analyze` requires the page nonce in a custom
header. The controller checks request size and message length, requires useful
content, and allows ten checks per minute per salted one-way network identifier.
Only the transient counter is retained. Proxy headers are deliberately ignored.

The response contains structured detections and suspicious domain names. It does
not echo the pasted message or make URLs clickable. The browser constructs result
markup with DOM `textContent` rather than HTML strings.
