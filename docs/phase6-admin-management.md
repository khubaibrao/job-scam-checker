# Phase 6 WordPress admin and management

Phase 6 adds a native, lightweight **Job Scam Checker** section to WordPress
Admin. Every screen requires the `manage_options` capability. State-changing
rule and statistics operations also require a WordPress nonce. Admin CSS loads
only on this plugin's screens.

## Overview

Open **Job Scam Checker → Overview**. The page shows all-time check and risk
totals, the latest 14-day check total, warning rules, recruitment channels,
money-request answers, payment purposes, and system status. All values come
directly from the anonymous aggregate table. A zero or missing dataset produces
an explicit empty state; the interface never estimates sample data.

## Rules

Open **Job Scam Checker → Rules** to search by name, slug, or explanation and to
filter by category or enabled status. Each row supports edit, enable/disable and
duplicate. Custom rules may also be deleted after confirmation. Shipped default
rules have a durable protected marker and cannot be deleted; disable one instead.

Use **Add rule** to create a rule. Supply a unique stable slug, supported
detection type, detection configuration, category, scoring group, risk weight
from 0–100, priority, explanation, and recommendation. Phrase configuration may
be plain text or a JSON string list. Contextual rules require JSON containing at
least two phrase groups. Regular expressions are bounded and validated. URL
classification types use the existing internal link analyzer and need no
pattern. Arbitrary executable code is not supported.

Built-in categories cover payment, cryptocurrency, gift cards, fake checks,
equipment, task scams, impersonation, communication, pressure, credentials,
compensation, suspicious links, recruitment channels, hiring, identity, and job
types. Extra sanitized category identifiers can be added under Settings.

## Settings

Open **Job Scam Checker → Settings**:

- **Checker** controls checker availability, optional follow-up questions, and
  whether focus moves to a completed result.
- **Statistics** enables or disables existing anonymous daily counters.
- **Search and content** controls native content filtering and related reading.
- **Display** controls the public trend component; sample safeguards remain.
- **Rule categories** accepts optional comma-separated category identifiers.
- **Privacy and retention** states the collection boundary and limits retention
  to 30–730 days.

Statistics remain disabled by default. The system stores daily aggregate counts
for checks, risk levels, stable rule slugs, and optional allow-listed answers. It
does not store pasted messages, visitor identities, contact details, IP
addresses, full URLs, credentials, or per-visitor histories.

## Statistics

Open **Job Scam Checker → Statistics** for risk totals, warning signs, channels,
money requests, payment purposes, and the latest 30 dates with check data.

The red reset panel permanently deletes only anonymous aggregate rows. Reset
requires administrator capability, nonce verification, a required acknowledgement
checkbox, and browser confirmation. It never changes pages, posts, users, rules,
settings, or other content.

## Content

Open **Job Scam Checker → Content** for grouped links to installed scam types,
guides, checker pages, trust pages, and legal pages. Links open the normal
WordPress page editor; Phase 6 does not introduce a parallel editor.

## Database migration

Schema version `3.0.0` adds one `is_default` unsigned boolean column to the
existing `{prefix}_jsc_rules` table, defaulting to `0`. During upgrade, stable
slugs from the shipped library are marked `1`. No new table is created. Existing
`{prefix}_jsc_daily_stats` rows and the Phase 5 format are unchanged.
