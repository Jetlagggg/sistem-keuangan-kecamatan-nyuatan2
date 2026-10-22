---
name: Buku Kas Kecamatan Nyuatan
description: Sistem keuangan kecamatan yang bekerja seperti buku kas: angka di kolom, saldo berjalan, total dapat direkonsiliasi.
colors:
  paper: "#f2f1ea"
  sheet: "#fbfaf4"
  sheet-deep: "#f5f4ec"
  ink: "#1b211c"
  ink-soft: "#3d443e"
  muted: "#63695f"
  rule: "#c6cbc0"
  rule-blue: "#a9bccd"
  red: "#a33b2e"
  red-wash: "#f6ece8"
  green: "#2f5b45"
  focus: "#2f5d7c"
  input: "#fffdf7"
  black: "#000000"
  white: "#ffffff"
  red-deep: "#872f24"
  placeholder: "#6b7168"
  print-ink: "#111111"
  print-rule: "#333333"
  print-wash: "#f0f0ef"
  print-total: "#f7f7f6"
  print-foot: "#eeeeee"
  night-paper: "#141713"
  night-sheet: "#1c201c"
  night-sheet-deep: "#232823"
  night-ink: "#ede9dd"
  night-ink-soft: "#c9c5b9"
  night-muted: "#9b9c90"
  night-rule: "#3a403a"
  night-rule-blue: "#526d7e"
  night-red: "#d9776a"
  night-red-wash: "#33221e"
  night-green: "#86c7a4"
  night-focus: "#8fc2e2"
  night-input: "#242924"
  night-input-focus: "#2a302a"
  night-placeholder: "#8f9186"
  night-ink-hover: "#fffdf5"
  night-red-deep: "#e08a7e"
  night-on-state: "#141713"
  night-login-rule: "#2b322d"
typography:
  display:
    fontFamily: "Public Sans, Segoe UI, sans-serif"
    fontSize: "clamp(24px, 2.4vw, 31px)"
    fontWeight: 700
    lineHeight: 1.1
    letterSpacing: "-0.02em"
  title:
    fontFamily: "Public Sans, Segoe UI, sans-serif"
    fontSize: "20px"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "-0.01em"
  body:
    fontFamily: "Public Sans, Segoe UI, sans-serif"
    fontSize: "14px"
    fontWeight: 400
    lineHeight: 1.55
  summary:
    fontFamily: "Roboto Mono, Courier New, monospace"
    fontSize: "clamp(19px, 2vw, 27px)"
    fontWeight: 700
    lineHeight: 1.15
    letterSpacing: "-0.03em"
    fontFeature: "tabular-nums"
  label:
    fontFamily: "Roboto Mono, Courier New, monospace"
    fontSize: "9.5px"
    fontWeight: 500
    lineHeight: 1.4
    letterSpacing: "0.1em"
  print:
    fontFamily: "DejaVu Sans, sans-serif"
    fontSize: "10.5px"
    fontWeight: 400
    lineHeight: 1.45
  print-mono:
    fontFamily: "DejaVu Sans Mono, monospace"
    fontSize: "9.5px"
    fontWeight: 400
    lineHeight: 1.45
rounded:
  none: "0"
  sm: "2px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "22px"
  2xl: "30px"
components:
  button-primary:
    backgroundColor: "{colors.ink}"
    textColor: "{colors.sheet}"
    rounded: "{rounded.sm}"
    padding: "7px 12px"
  button-primary-hover:
    backgroundColor: "#000000"
    textColor: "{colors.sheet}"
    rounded: "{rounded.sm}"
    padding: "7px 12px"
  button-outline:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    padding: "7px 12px"
  field:
    backgroundColor: "{colors.input}"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    padding: "8px 10px"
  sheet:
    backgroundColor: "{colors.sheet}"
    textColor: "{colors.ink}"
    rounded: "{rounded.none}"
    padding: "16px"
  summary-value:
    textColor: "{colors.ink}"
    typography: "{typography.summary}"
  ledger-header:
    backgroundColor: "{colors.sheet}"
    textColor: "{colors.muted}"
    typography: "{typography.label}"
  tag:
    backgroundColor: "transparent"
    textColor: "{colors.ink-soft}"
    rounded: "{rounded.none}"
    padding: "2px 6px"
---

# Design System: Buku Kas Kecamatan Nyuatan

## Overview

**Creative North Star: "Buku Kas Kecamatan"**

The interface is a working ledger, not a dashboard of cards. Every rupiah sits in a column, every running balance stays visible, and every total can be reconciled by reading the sheet. Paper and sheet tones replace the usual app chrome; rules do the work that borders and shadows do elsewhere.

The system is formal, calm, and dense without feeling crowded. Public Sans carries the language of the office; Roboto Mono carries anything that must line up: money, dates, table labels, audit metadata, and the masthead's institutional line. Red is a stamp, not decoration: it appears when a budget is full, an action is destructive, or a page reports an error.

**Key Characteristics:**
- Flat paper sheets with hairline rules and double rules closing totals.
- Tabular numerals in a dedicated mono face; amounts align right in their columns.
- Square controls, 2px input and button corners, no card shadows.
- A red "stamp" vocabulary for computed danger states, not for emphasis.
- Institutional masthead with the Kubar emblem, fiscal year, and a quiet utility row.
- A deliberate night ledger ("Buku Kas Malam") with warm charcoal paper, cream ink, and the same stamp-red signals.
- Responsive by stacking the summary band and pos sheets, then letting wide ledger tables scroll.

## Colors

The palette is a ledger on a desk: warm paper, a lighter sheet, dark ink, blue-grey column rules, and a single red stamp. Color is restrained; the red and green are reserved for states that mean something.

### Primary
- **Tinta Buku** (#1b211c): body text, primary buttons, table rules that close a section, and the masthead's double rule. It is the default "ink" for action and structure.
- **Tinta Lembut** (#3d443e): secondary text inside sheets, table headers, and user metadata.

### Secondary
- **Stempel Merah** (#a33b2e): over-budget states, destructive actions, validation errors, and the short accent rule under page titles. Never use it as a general highlight.

### Tertiary
- **Tinta Hijau** (#2f5b45): success alerts, paid/available states, and the positive variant of tags and buttons.

### Neutral
- **Kertas Arsip** (#f2f1ea): the page ground behind every sheet.
- **Lembar Buku Kas** (#fbfaf4): the sheet surface for tables, forms, summaries, modals, and the masthead.
- **Lembar Tenggelam** (#f5f4ec): disabled fields and subtle tonal separation inside a sheet.
- **Garis Kolom** (#c6cbc0): the default hairline between rows, cells, and sections.
- **Garis Saldo** (#a9bccd): the blue-grey rule that separates the running-balance column and outlines the summary band.
- **Tinta Redup** (#63695f): labels, notes, empty states, and metadata that should recede.
- **Kertas Form** (#fffdf7): the warm white field surface that sits slightly above the sheet.
- **Fokus Biru** (#2f5d7c): visible keyboard focus ring around links, buttons, and fields.
- **Hitam / Putih** (#000000 / #ffffff): hover extension for the ink primary button and inverted text on solid stamps; not a second background system.
- **Merah Tua** (#872f24): the hover extension of the stamp-red danger button.
- **Placeholder** (#6b7168): placeholder text; it meets contrast without competing with entered values.
- **Tinta Cetak** (#111111) and **Garis Cetak** (#333333): the dompdf report's ink and rules, which use a print-safe typeface rather than the screen palette.
- **Kertas Cetak** (#f0f0ef), **Total Cetak** (#f7f7f6), **Kaki Cetak** (#eeeeee): the muted table header, summary, and footer grounds in the PDF report.

### Dark Theme — Buku Kas Malam

The theme switch changes the desk from daylight paper to a night ledger. It is not an inversion: the hierarchy stays the same, the sheet is still lighter than the desk, and red/green remain state signals. The toggle stores `light` or `dark` in `localStorage`; with no stored choice, the page follows the operating system preference.

- **Kertas Malam** (#141713): the night desk behind the sheets.
- **Lembar Malam** (#1c201c): the sheet surface; **Lembar Tenggelam Malam** (#232823) for disabled fields and tonal separation.
- **Tinta Malam** (#ede9dd): body text and primary button ground; **Tinta Lembut Malam** (#c9c5b9) for secondary text and headers.
- **Garis Malam** (#3a403a): hairlines; **Garis Saldo Malam** (#526d7e) for the running-balance rule.
- **Stempel Merah Malam** (#d9776a): the lighter red that stays legible on a dark sheet; **Cuci Merah Malam** (#33221e) is its alert ground. **Tinta Hijau Malam** (#86c7a4) carries success.
- **Fokus Biru Malam** (#8fc2e2): the visible keyboard focus ring.
- **Kertas Form Malam** (#242924): field ground; focus lifts to #2a302a. The placeholder is #8f9186.
- **Tinta Hover Malam** (#fffdf5): the cream hover extension of the inverted primary button.
- **Teks di Atas Stempel Malam** (#141713): dark text on the lighter red/green solid buttons.

**The Night Ledger Rule.** Dark mode is still a ledger: keep warm charcoal paper, cream ink, and the same red/green meanings. Do not use pure black, blue-black, neon accents, or a second visual language.

### Named Rules
**The One Red Rule.** Red is a signal, not a theme. It belongs to violations, destructive controls, error pages, and the page-title accent; if it appears as decoration, remove it.
**The Blue Column Rule.** The running balance is the only column allowed a tinted background and a blue-grey left rule. It is the reader's anchor, not another data column.

## Typography

**Display Font:** Public Sans (with Segoe UI, sans-serif)
**Body Font:** Public Sans (with Segoe UI, sans-serif)
**Label/Mono Font:** Roboto Mono (with Courier New, monospace)

**Character:** A neutral workhorse for prose and a strict tabular face for money. The pairing reads like a government ledger that was designed, not a spreadsheet that was styled.

**Print:** The PDF report is a separate, print-safe sheet. It uses DejaVu Sans for body text and DejaVu Sans Mono for figures because those faces are available to dompdf; it does not load the screen web fonts. Keep the print sheet's greys and rules self-contained.

### Hierarchy
- **Display** (700, clamp(24px, 2.4vw, 31px), 1.1, -0.02em): page titles such as "Monev Anggaran" and "Transaksi Keuangan".
- **Title** (700, 20px, 1.2, -0.01em): login title and compact section titles that need more weight than the mono label.
- **Body** (400, 14px, 1.55): prose, table cells, form values, and notes. Keep body copy near 62ch.
- **Summary** (700, clamp(19px, 2vw, 27px), 1.15, -0.03em, tabular): the three headline figures in the ledger summary band.
- **Label** (500, 9.5px, 1.4, 0.1em, uppercase, mono): table headers, field labels, nav items, audit metadata, and the brand subtitle.

### Named Rules
**The Column Rule.** Any number that a treasurer compares down a column uses Roboto Mono with tabular numerals and right alignment. Do not set amounts in the sans face.
**The Quiet Label Rule.** Labels are small, tracked, uppercase mono. They name the data; they never compete with the figure.

## Layout

The shell is a centered 1240px sheet with 32px side padding on desktop, 20px on tablet, and 20px on mobile. The masthead carries the institutional mark, fiscal year, navigation, and the primary "Input Dana (GU)" action; the content begins with a page head, a short red rule, and the working area.

The ledger summary is a three-column band on desktop, stacking to one column below 992px. Pos kegiatan uses a two-column slip grid that becomes one column below 992px. Filter bars use a 12-column grid: GU and keyword span 3, dates span 2, actions span 2; on tablet each field spans 6 and actions span 12; on mobile every field spans 12. Wide ledger tables keep a 760px minimum and scroll horizontally inside `.table-responsive` below 992px. The primary review width is 1366×768; the layout is also checked at 1440 and 390.

Spacing follows a tight ledger rhythm: 4px inside tags, 8px between controls, 12–16px inside sheets, 18–22px between sheets, 30px above the shell content, and 56px below it.

## Elevation & Depth

This is a flat system. Sheets are separated by tonal paper/sheet contrast and 1px rules, not by shadows. A sheet has a 1px rule border; its header closes with 1px ink and its footer closes with a 3px double ink rule. The only lifted surface is the input-dana modal.

### Shadow Vocabulary
- **Modal lift** (`box-shadow: 0 18px 50px rgba(27, 33, 28, .22)`): the single modal surface, used to separate a protected data-entry task from the ledger behind it.
- **Focus ring** (`box-shadow: 0 0 0 1px var(--ink)` on fields, `outline: 2px solid var(--focus)` elsewhere): state feedback, not decoration.

### Named Rules
**The Flat Sheet Rule.** Surfaces are flat at rest. If a sheet needs to feel separate, add a rule or a tonal step, not a shadow.
**The Double Rule Rule.** Reserve 3px double rules for the close of a ledger section: totals, sheet footers, modal headers, login/error sheet edges. Do not use them as ordinary borders.

## Shapes

The form language is rectilinear and official: 0 radius on sheets, tags, alerts, and stamps; 2px on buttons, inputs, selects, pagination, and modal corners. Borders are 1px hairline rules, with 2px only for the red accent under page titles and the stamp's double outline. The stamp is rotated -2.5deg; everything else stays on the grid.

The Kubar emblem is the one organic shape in the system. It sits at 44px in the masthead and login header, 36px on small screens, and is never redrawn, boxed, or replaced by a monogram.

## Components

### Buttons
- **Shape:** 2px radius; 1px border; no shadow; uppercase 11.5px/600 with 0.04em tracking.
- **Primary:** ink background, sheet text, 7px 12px padding. Hover/focus goes to black.
- **Outline / secondary:** transparent ground, rule border, ink text. Hover adds the deep-sheet tone and an ink border.
- **Danger:** solid stamp red with white text; outline danger uses red border/text and fills red on hover.
- **Small / icon:** `btn-sm` is 5px 9px at 10px; icon buttons are 32×32 with centered glyphs.
- **Disabled:** deep-sheet ground, rule border, muted text, no hover lift.

### Forms
- **Style:** 2px radius, 1px rule border, warm `#fffdf7` field ground, 13px Public Sans, 8px 10px padding, 38px minimum height.
- **Focus:** ink border plus a 1px ink ring; caret is stamp red.
- **Error:** red border and red-wash ground; focus ring turns red.
- **Labels:** mono 9.5px, 500, 0.1em tracking, uppercase, ink-soft.
- **Hints:** 10.5px muted text directly below the field.

### Navigation
- **Masthead:** sheet ground closed by a 3px double ink rule; brand block with the Kubar emblem, institution name, and mono subtitle.
- **Main nav:** mono 10.5px uppercase links in muted ink, 13px 14px padding, transparent 2px bottom border. Active links turn ink with a stamp-red bottom border.
- **Utility row:** fiscal year at the far right, then the primary Input Dana action, the theme toggle, user link with role, and a ghost logout icon.
- **Theme toggle:** a 32×32 ghost icon button with a sun/moon glyph, `aria-pressed`, and an action label; it sits in the utility row and is fixed at the login page's top-right.
- **Mobile:** below 992px the nav stacks into full-width rows with 1px rules; the toggler is a square 1px ink button. The fiscal year meta is hidden.

### Sheets & Tables
- **Sheet:** sheet ground, 1px rule border, 0 radius. Header 10px 16px with a 1px ink close; body 16px; footer 9px 16px with a 3px double ink top.
- **Ledger table:** 12.5px body, 9px 14px cells, 1px rule row separators. Header is mono 9.5px/500 uppercase muted with a 1px ink bottom.
- **Numbers:** mono, tabular, right-aligned, nowrap. The balance column gets the blue-tinted ground and a 1px blue-grey left rule.
- **Warnings:** a 45deg red hatch at 7.5% opacity plus red numerals; never rely on color alone.
- **Empty state:** centered mono 11px muted copy with generous 32px vertical padding.

### Tags & Stamps
- **Tag:** 1px rule border, 0 radius, mono 9px/500 uppercase, 2px 6px padding. Solid inverts to ink/sheet; red and green variants carry state.
- **Stamp:** 2px double red border, stamp-red mono 10px/700 with 0.18em tracking, uppercase, rotated -2.5deg. Use only for computed states such as "PAGU HABIS".

### Summary Band
- **Structure:** three equal cells on a sheet ground, 1px blue-grey outer border and 2px ink top rule.
- **Cell:** mono 9.5px label, large mono summary value, 11px muted note.
- **Negative state:** red hatch ground and red value/note. This is a computed state, not a manual style.

### Modal
- Sheet ground, 1px ink border, 2px radius, modal lift shadow.
- Header 13px 18px with a 3px double ink bottom; body 18px; footer 12px 18px with a 1px rule top.
- Close button is square; the dark theme swaps the Bootstrap glyph for an ink × so it stays visible. Backdrop is ink at 55%.

### Login & Error Sheets
- Login and error pages center a single sheet on the paper ground; login adds faint 30px horizontal rules and a fixed theme toggle at the top-right.
- Login/error sheets use 1px ink sides with 3px double ink top and bottom.
- Error code is mono 64px/700 with -0.05em tracking; the 500 code is stamp red. The rule between copy and action is a 3px double line.

## Do's and Don'ts

### Do:
- **Do** keep amounts in Roboto Mono, tabular, right-aligned, and inside a column with a visible header.
- **Do** use the Kubar emblem in the masthead and login at 44px (36px on small screens); keep the institution name and mono subtitle beside it.
- **Do** close totals and sheet footers with the 3px double ink rule; use 1px rules everywhere else.
- **Do** mark budget violations with the red hatch plus red numerals so the state survives grayscale.
- **Do** keep controls square or at 2px radius; use the ink primary button for the single most important action on the page.
- **Do** preserve the paper/sheet/ink palette and the stamp-red accent when adding a new screen.
- **Do** use the documented dark tokens when `data-theme="dark"` is active; keep the same sheet hierarchy and state meanings.

### Don't:
- **Don't** introduce rounded cards, soft shadows, gradients, or glass surfaces; the system is flat paper and rules.
- **Don't** use red for ordinary emphasis, hover states, or decoration; reserve it for computed danger and destructive actions.
- **Don't** set money in Public Sans or align amounts left; the column is the contract.
- **Don't** replace the Kubar emblem with a monogram, text mark, or generic government icon.
- **Don't** use kickers/eyebrows above headings, gradient text, or icon-plus-heading cards as the page structure.
- **Don't** add a second accent color or a second visual language. Dark mode is the documented "Buku Kas Malam" theme, not a license for pure black, blue-black, neon, or glass.
