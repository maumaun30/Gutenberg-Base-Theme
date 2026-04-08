Gutenberg Tailwind Starter Theme

A lightweight WordPress theme using:

- Classic PHP templates (index.php, header.php, footer.php)
- Custom Gutenberg blocks (React)
- Tailwind CSS v3
- Auto block generator (npm run create-block)

---

FEATURES

- Simple classic theme (no block theme complexity)
- Auto-register Gutenberg blocks
- One-command block generator
- Tailwind styling system
- Dynamic block build + watch

---

INSTALLATION

1. Install dependencies:
   npm install

2. Build assets:
   npm run build

3. Activate theme in WordPress

---

CREATING A BLOCK

npm run create-block hero

This will:
- Create block folder
- Generate:
  - block.json
  - edit.js
  - index.js
  - render.php
  - style.css
- Auto build the block

---

BUILD COMMANDS

Build everything:
npm run build

Dev (watch mode):
npm run dev

---

PROJECT STRUCTURE

assets/
  css/
  js/
    blocks/
      hero/
      cta/

scripts/
  create-block.js
  build-blocks.js
  dev-blocks.js

---

BLOCK REGISTRATION

Blocks are auto-registered via:
register_block_type($block_path);

No manual registration needed.

---

STYLING

Main styles:
assets/css/main.css

Compiled output:
assets/css/main.min.css

---

NOTES

- Blocks are dynamic (rendered via PHP)
- Uses Tailwind utility classes + custom design system
- No need to edit functions.php when adding blocks

---

REQUIREMENTS

- Node.js 18+
- WordPress 6.6+
- PHP 8.1+

---

TIPS

- Use kebab-case for block names:
  hero-banner
  feature-grid
  pricing-table

- Always run:
  npm run dev
  while developing

---

LICENSE

GPL-2.0-or-later
