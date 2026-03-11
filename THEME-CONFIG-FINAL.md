# The Beyond Borders Report - WordPress Theme Configuration
**Premium Editorial Theme for Global Business Intelligence**

---

## Theme Overview

**Theme Name:** The Beyond Borders Report Theme  
**Version:** 1.0  
**Design Philosophy:** Editorial-first, Sophisticated, Timeless  
**Target:** Premium business publication, multi-author platform  
**Inspirations:** Financial Times, Sotheby's International Realty, The Economist, Monocle Magazine

---

## Color Palette - Editorial Excellence

### Primary Brand Colors

```css
:root {
  /* Deep Navy - Primary Brand */
  --navy-deep: #0A1628;           /* Hero backgrounds, footer, dark sections */
  --navy-primary: #003366;        /* Navigation, primary buttons, headers */
  --navy-medium: #00274D;         /* Alternating sections, overlays */
  --navy-light: #1A4D5C;          /* Hover states, accents */
  
  /* Charcoal - Editorial Text */
  --charcoal-dark: #1A1A2E;       /* Main headings on light backgrounds */
  --charcoal-medium: #16213E;     /* Section backgrounds */
  --charcoal-light: #2C3E50;      /* Subtle backgrounds */
  
  /* Gold - Premium Accents */
  --gold-primary: #C9A961;        /* Main gold - headings, icons, links */
  --gold-champagne: #D4AF37;      /* Bright gold - CTAs, highlights */
  --gold-dark: #B8941F;           /* Borders, dividers, lines */
  --gold-muted: #B8A17A;          /* Subtle accents */
  
  /* Neutrals - Foundation */
  --white: #FFFFFF;               /* Pure white backgrounds */
  --off-white: #F9F9F9;           /* Alternate backgrounds */
  --cream: #FAF8F5;               /* Warm section backgrounds */
  --light-gray: #E8E8E8;          /* Borders, separators */
  --medium-gray: #6B7280;         /* Secondary text, metadata */
  --dark-gray: #374151;           /* Body text on light */
  
  /* Text Colors */
  --text-primary: #0A0A0A;        /* Main body text - near black */
  --text-secondary: #374151;      /* Secondary content */
  --text-tertiary: #6B7280;       /* Metadata, captions */
  --text-light: #8A9AA8;          /* Light text on dark */
  --text-on-dark: #FFFFFF;        /* Text on navy/dark backgrounds */
  
  /* Accent Colors (Minimal Use) */
  --blue-link: #0066CC;           /* Hyperlinks */
  --red-editorial: #C73E1D;       /* Important highlights, labels */
  --teal-accent: #5CE1E6;         /* Modern touch (use sparingly) */
}
```

### Color Usage Guidelines

#### Navy Palette
```css
/* Deep Navy (#0A1628) - Maximum Impact */
- Full-width hero sections
- Footer backgrounds
- Dark mode sections
- Dramatic overlays
- Premium feature backgrounds

/* Primary Navy (#003366) - Brand Identity */
- Main navigation bar
- Primary buttons and CTAs
- Section dividers and borders
- Header backgrounds
- Category tags background

/* Medium Navy (#00274D) - Versatility */
- Alternating section backgrounds
- Card backgrounds (dark mode)
- Sidebar backgrounds
- Modal overlays

/* Light Navy (#1A4D5C) - Subtle Accents */
- Hover states on buttons
- Active navigation states
- Subtle overlays
- Background tints
```

#### Gold Palette
```css
/* Primary Gold (#C9A961) - Editorial Excellence */
- Main headings and display type
- Author names and bylines
- Category labels
- Icon accents
- Premium badges
- Pull quote accents

/* Champagne Gold (#D4AF37) - High Contrast */
- Primary CTAs
- Important highlights
- Featured article markers
- Newsletter buttons
- Social share icons
- Number badges

/* Dark Gold (#B8941F) - Refined Details */
- Thin decorative lines
- Section dividers (2px lines)
- Border accents
- Hover states on gold elements
- Subtle separators

/* Muted Gold (#B8A17A) - Understated */
- Secondary icons
- Disabled states
- Background tints
- Subtle accents
```

#### Neutral Palette
```css
/* Cream (#FAF8F5) - Warmth */
- Alternating section backgrounds
- Article content backgrounds
- Form backgrounds
- Card backgrounds (light mode)

/* Light Gray (#E8E8E8) - Structure */
- Borders and dividers
- Input field borders
- Card borders
- Subtle separators

/* Medium Gray (#6B7280) - Supporting Text */
- Article metadata (date, read time)
- Category descriptions
- Secondary navigation
- Captions and credits

/* Dark Gray (#374151) - Body Text */
- Main article body content
- List items
- Form labels
- Supporting copy
```

---

## Typography System - Editorial Standards

### Font Stack

```css
/* Serif - Headlines & Editorial */
--font-headline: 'Playfair Display', 'Georgia', serif;
--font-subhead: 'Lora', 'Georgia', 'Times New Roman', serif;
--font-body: 'Lora', 'Georgia', 'Times New Roman', serif;

/* Sans-Serif - UI & Navigation */
--font-ui: 'Inter', 'Helvetica Neue', -apple-system, sans-serif;
--font-label: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;

/* Monospace - Code & Data */
--font-mono: 'IBM Plex Mono', 'Courier New', monospace;
```

### Font Weights

```css
--weight-light: 300;
--weight-regular: 400;
--weight-medium: 500;
--weight-semibold: 600;
--weight-bold: 700;
```

### Typography Scale

```css
/* Display & Headlines */
--text-display: 4rem;          /* 64px - Homepage hero */
--text-hero: 3.25rem;          /* 52px - Article hero */
--text-headline-xl: 2.625rem;  /* 42px - Section headings */
--text-headline-lg: 2.25rem;   /* 36px - Large article titles */
--text-headline-md: 1.875rem;  /* 30px - Card headlines */
--text-headline-sm: 1.5rem;    /* 24px - Small headlines */

/* Body & Content */
--text-intro: 1.25rem;         /* 20px - Article deck/intro */
--text-body-lg: 1.125rem;      /* 18px - Article body */
--text-body: 1rem;             /* 16px - Standard body */
--text-small: 0.875rem;        /* 14px - Metadata, captions */
--text-xs: 0.8125rem;          /* 13px - Labels, tags */
--text-tiny: 0.75rem;          /* 12px - Fine print */

/* Line Heights */
--leading-tight: 1.2;          /* Headlines */
--leading-snug: 1.3;           /* Subheadings */
--leading-normal: 1.5;         /* UI text */
--leading-relaxed: 1.7;        /* Article body */
--leading-loose: 1.8;          /* Wide reading */

/* Letter Spacing */
--tracking-tight: -0.02em;     /* Large headlines */
--tracking-normal: 0;          /* Body text */
--tracking-wide: 0.025em;      /* Small text */
--tracking-wider: 0.05em;      /* Labels */
--tracking-widest: 0.1em;      /* Uppercase labels */
```

### Typography Classes

```css
/* Display Typography */
.text-display {
  font-family: var(--font-headline);
  font-size: var(--text-display);
  font-weight: var(--weight-semibold);
  line-height: var(--leading-tight);
  letter-spacing: var(--tracking-tight);
  color: var(--navy-primary);
}

.text-hero {
  font-family: var(--font-headline);
  font-size: var(--text-hero);
  font-weight: var(--weight-semibold);
  line-height: var(--leading-tight);
  letter-spacing: var(--tracking-tight);
  color: var(--white);
}

/* Editorial Headlines */
.headline-section {
  font-family: var(--font-headline);
  font-size: var(--text-headline-xl);
  font-weight: var(--weight-semibold);
  line-height: var(--leading-tight);
  color: var(--navy-primary);
  margin-bottom: 1rem;
}

.headline-article {
  font-family: var(--font-headline);
  font-size: var(--text-headline-lg);
  font-weight: var(--weight-semibold);
  line-height: var(--leading-snug);
  color: var(--navy-primary);
  margin-bottom: 0.75rem;
}

.headline-card {
  font-family: var(--font-headline);
  font-size: var(--text-headline-md);
  font-weight: var(--weight-semibold);
  line-height: var(--leading-snug);
  color: var(--navy-primary);
}

/* Body Typography */
.text-deck {
  font-family: var(--font-body);
  font-size: var(--text-intro);
  font-weight: var(--weight-regular);
  line-height: var(--leading-relaxed);
  color: var(--medium-gray);
}

.text-article {
  font-family: var(--font-body);
  font-size: var(--text-body-lg);
  font-weight: var(--weight-regular);
  line-height: var(--leading-loose);
  color: var(--text-primary);
}

.text-excerpt {
  font-family: var(--font-body);
  font-size: var(--text-body);
  font-weight: var(--weight-regular);
  line-height: var(--leading-relaxed);
  color: var(--text-secondary);
}

/* UI Typography */
.text-label {
  font-family: var(--font-ui);
  font-size: var(--text-tiny);
  font-weight: var(--weight-semibold);
  line-height: var(--leading-normal);
  letter-spacing: var(--tracking-widest);
  text-transform: uppercase;
  color: var(--gold-dark);
}

.text-meta {
  font-family: var(--font-ui);
  font-size: var(--text-small);
  font-weight: var(--weight-regular);
  line-height: var(--leading-normal);
  color: var(--medium-gray);
}

/* Category Tags */
.category-tag {
  font-family: var(--font-ui);
  font-size: var(--text-tiny);
  font-weight: var(--weight-semibold);
  letter-spacing: var(--tracking-wider);
  text-transform: uppercase;
  color: var(--gold-dark);
}
```

---

## Layout System

### Container Widths

```css
--container-narrow: 680px;     /* Article content - optimal reading */
--container-reading: 720px;    /* Reading content max width */
--container-md: 960px;         /* Medium content */
--container-lg: 1200px;        /* Standard sections */
--container-xl: 1400px;        /* Wide layouts */
--container-full: 100%;        /* Full width */
```

### Spacing Scale

```css
--space-0: 0;
--space-1: 0.25rem;      /* 4px */
--space-2: 0.5rem;       /* 8px */
--space-3: 0.75rem;      /* 12px */
--space-4: 1rem;         /* 16px */
--space-5: 1.25rem;      /* 20px */
--space-6: 1.5rem;       /* 24px */
--space-8: 2rem;         /* 32px */
--space-10: 2.5rem;      /* 40px */
--space-12: 3rem;        /* 48px */
--space-16: 4rem;        /* 64px */
--space-20: 5rem;        /* 80px */
--space-24: 6rem;        /* 96px */
```

### Grid System

```css
.grid-2 { 
  display: grid; 
  grid-template-columns: repeat(2, 1fr); 
  gap: var(--space-10); 
}

.grid-3 { 
  display: grid; 
  grid-template-columns: repeat(3, 1fr); 
  gap: var(--space-8); 
}

.grid-4 { 
  display: grid; 
  grid-template-columns: repeat(4, 1fr); 
  gap: var(--space-8); 
}

.grid-article-sidebar {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: var(--space-16);
}

.grid-split {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-16);
  align-items: center;
}
```

---

## Component Library

### 1. Top Bar

```css
.top-bar {
  background: var(--charcoal-dark);
  color: var(--white);
  padding: 0.5rem 0;
  font-family: var(--font-ui);
  font-size: var(--text-tiny);
  border-bottom: 1px solid var(--gold-dark);
}

.top-bar-container {
  max-width: var(--container-xl);
  margin: 0 auto;
  padding: 0 var(--space-10);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.top-bar-link {
  color: var(--gold-primary);
  text-decoration: none;
  margin-left: var(--space-5);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  font-weight: var(--weight-medium);
  transition: color 0.3s ease;
}

.top-bar-link:hover {
  color: var(--gold-champagne);
}
```

### 2. Header & Logo

```css
.site-header {
  background: var(--white);
  border-bottom: 1px solid var(--light-gray);
  position: sticky;
  top: 0;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.header-container {
  max-width: var(--container-xl);
  margin: 0 auto;
  padding: var(--space-8) var(--space-10);
  text-align: center;
}

.site-logo {
  font-family: var(--font-headline);
  font-size: 2.625rem;
  font-weight: var(--weight-semibold);
  color: var(--navy-primary);
  letter-spacing: -0.02em;
  line-height: 1.2;
  text-decoration: none;
  display: inline-block;
}

.site-tagline {
  font-family: var(--font-ui);
  font-size: var(--text-xs);
  color: var(--text-tertiary);
  text-transform: uppercase;
  letter-spacing: var(--tracking-widest);
  margin-top: var(--space-2);
  font-weight: var(--weight-regular);
}
```

### 3. Main Navigation

```css
.main-nav {
  background: var(--navy-primary);
  border-top: 2px solid var(--gold-dark);
}

.nav-container {
  max-width: var(--container-xl);
  margin: 0 auto;
  padding: 0 var(--space-10);
  display: flex;
  justify-content: center;
  gap: 0;
}

.nav-link {
  color: var(--white);
  text-decoration: none;
  padding: 1.125rem 1.75rem;
  font-family: var(--font-ui);
  font-size: var(--text-small);
  font-weight: var(--weight-medium);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  transition: all 0.3s ease;
  position: relative;
  border-right: 1px solid rgba(255,255,255,0.1);
}

.nav-link:first-child {
  border-left: 1px solid rgba(255,255,255,0.1);
}

.nav-link:hover,
.nav-link.active {
  background: var(--navy-light);
  color: var(--gold-champagne);
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 0;
  background: var(--gold-champagne);
  transition: height 0.3s ease;
}

.nav-link:hover::after,
.nav-link.active::after {
  height: 3px;
}
```

### 4. Hero Section (Homepage)

```css
.hero {
  background: var(--navy-deep);
  color: var(--white);
  position: relative;
  overflow: hidden;
}

.hero-grid {
  max-width: var(--container-xl);
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 600px;
}

.hero-content {
  padding: var(--space-20) var(--space-16);
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.hero-category {
  font-family: var(--font-ui);
  font-size: var(--text-tiny);
  text-transform: uppercase;
  letter-spacing: var(--tracking-widest);
  color: var(--gold-champagne);
  font-weight: var(--weight-semibold);
  margin-bottom: var(--space-5);
}

.hero-title {
  font-family: var(--font-headline);
  font-size: var(--text-hero);
  line-height: var(--leading-tight);
  font-weight: var(--weight-semibold);
  margin-bottom: var(--space-5);
  color: var(--white);
  letter-spacing: var(--tracking-tight);
}

.hero-excerpt {
  font-size: var(--text-intro);
  line-height: var(--leading-relaxed);
  color: var(--medium-gray);
  margin-bottom: var(--space-8);
  font-family: var(--font-body);
}

.hero-meta {
  font-family: var(--font-ui);
  font-size: var(--text-xs);
  color: var(--medium-gray);
  display: flex;
  gap: var(--space-5);
  align-items: center;
  margin-bottom: var(--space-8);
}

.hero-author {
  color: var(--gold-primary);
  font-weight: var(--weight-medium);
}

.hero-image {
  position: relative;
  overflow: hidden;
}

.hero-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}
```

### 5. Buttons & CTAs

```css
/* Primary Button */
.btn-primary {
  display: inline-block;
  padding: var(--space-4) var(--space-8);
  background: var(--navy-primary);
  color: var(--white);
  text-decoration: none;
  font-family: var(--font-ui);
  font-size: var(--text-xs);
  font-weight: var(--weight-semibold);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  border: none;
  border-radius: 2px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: var(--navy-light);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3);
}

/* Secondary Button (Gold) */
.btn-secondary {
  display: inline-block;
  padding: var(--space-4) var(--space-8);
  background: var(--gold-champagne);
  color: var(--navy-deep);
  text-decoration: none;
  font-family: var(--font-ui);
  font-size: var(--text-xs);
  font-weight: var(--weight-semibold);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  border: none;
  border-radius: 2px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-secondary:hover {
  background: var(--gold-dark);
  color: var(--white);
}

/* Outlined Button */
.btn-outlined {
  display: inline-block;
  padding: var(--space-4) var(--space-8);
  background: transparent;
  color: var(--gold-champagne);
  text-decoration: none;
  font-family: var(--font-ui);
  font-size: var(--text-xs);
  font-weight: var(--weight-semibold);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  border: 2px solid var(--gold-champagne);
  border-radius: 2px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-outlined:hover {
  background: var(--gold-champagne);
  color: var(--navy-deep);
}

/* Text Link with Arrow */
.link-arrow {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  color: var(--gold-dark);
  text-decoration: none;
  font-family: var(--font-ui);
  font-size: var(--text-xs);
  font-weight: var(--weight-semibold);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  transition: gap 0.3s ease;
}

.link-arrow:hover {
  gap: var(--space-3);
  color: var(--gold-champagne);
}
```

### 6. Article Cards

```css
.article-card {
  background: var(--white);
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid var(--light-gray);
}

.article-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
}

.article-card-image {
  width: 100%;
  height: 260px;
  overflow: hidden;
  position: relative;
}

.article-card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.article-card:hover .article-card-image img {
  transform: scale(1.05);
}

.article-card-content {
  padding: var(--space-8);
}

.article-card-category {
  font-family: var(--font-ui);
  font-size: var(--text-tiny);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wider);
  color: var(--gold-dark);
  font-weight: var(--weight-semibold);
  margin-bottom: var(--space-3);
  display: inline-block;
}

.article-card-title {
  font-family: var(--font-headline);
  font-size: var(--text-headline-md);
  line-height: var(--leading-snug);
  font-weight: var(--weight-semibold);
  margin-bottom: var(--space-3);
}

.article-card-title a {
  color: var(--navy-primary);
  text-decoration: none;
  transition: color 0.3s ease;
}

.article-card-title a:hover {
  color: var(--gold-dark);
}

.article-card-excerpt {
  font-family: var(--font-body);
  font-size: var(--text-body);
  line-height: var(--leading-relaxed);
  color: var(--text-secondary);
  margin-bottom: var(--space-4);
}

.article-card-meta {
  font-family: var(--font-ui);
  font-size: var(--text-small);
  color: var(--text-tertiary);
  display: flex;
  gap: var(--space-3);
}

.article-card-author {
  color: var(--gold-dark);
  font-weight: var(--weight-medium);
}
```

### 7. Section Layouts

```css
/* Section Base */
.section {
  padding: var(--space-20) var(--space-10);
}

.section-white {
  background: var(--white);
}

.section-cream {
  background: var(--cream);
}

.section-navy {
  background: var(--navy-medium);
  color: var(--white);
}

/* Section Header */
.section-header {
  text-align: center;
  margin-bottom: var(--space-16);
}

.section-divider {
  width: 60px;
  height: 2px;
  background: var(--gold-dark);
  margin: 0 auto var(--space-5);
}

.section-title {
  font-family: var(--font-headline);
  font-size: var(--text-headline-xl);
  font-weight: var(--weight-semibold);
  margin-bottom: var(--space-4);
  color: var(--navy-primary);
  letter-spacing: var(--tracking-tight);
}

.section-navy .section-title {
  color: var(--white);
}

.section-subtitle {
  font-family: var(--font-ui);
  font-size: var(--text-body);
  color: var(--text-tertiary);
  max-width: 700px;
  margin: 0 auto;
}

.section-navy .section-subtitle {
  color: var(--medium-gray);
}

/* Container */
.section-container {
  max-width: var(--container-xl);
  margin: 0 auto;
}
```

### 8. Stats Display

```css
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-16);
  text-align: center;
}

.stat-item {
  padding: var(--space-10) var(--space-5);
}

.stat-number {
  font-family: var(--font-headline);
  font-size: 3.5rem;
  font-weight: var(--weight-semibold);
  color: var(--white);
  line-height: 1;
  margin-bottom: var(--space-4);
}

.stat-label {
  font-family: var(--font-ui);
  font-size: var(--text-tiny);
  text-transform: uppercase;
  letter-spacing: var(--tracking-widest);
  color: var(--medium-gray);
  font-weight: var(--weight-medium);
}
```

### 9. Newsletter Section

```css
.newsletter {
  background: linear-gradient(135deg, var(--navy-deep) 0%, var(--navy-light) 100%);
  padding: var(--space-20) var(--space-10);
  text-align: center;
  color: var(--white);
}

.newsletter-title {
  font-family: var(--font-headline);
  font-size: var(--text-headline-lg);
  font-weight: var(--weight-semibold);
  margin-bottom: var(--space-4);
}

.newsletter-subtitle {
  font-size: var(--text-body);
  color: var(--medium-gray);
  margin-bottom: var(--space-10);
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.newsletter-form {
  max-width: 600px;
  margin: 0 auto;
  display: flex;
  gap: var(--space-4);
}

.newsletter-input {
  flex: 1;
  padding: var(--space-4) var(--space-6);
  font-size: var(--text-body);
  border: 2px solid rgba(255,255,255,0.2);
  background: rgba(255,255,255,0.05);
  color: var(--white);
  font-family: var(--font-ui);
  transition: all 0.3s ease;
}

.newsletter-input:focus {
  outline: none;
  border-color: var(--gold-champagne);
  background: rgba(255,255,255,0.1);
}

.newsletter-input::placeholder {
  color: var(--medium-gray);
}

.newsletter-button {
  padding: var(--space-4) var(--space-8);
  background: var(--gold-champagne);
  color: var(--navy-deep);
  border: none;
  font-family: var(--font-ui);
  font-size: var(--text-xs);
  font-weight: var(--weight-semibold);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  cursor: pointer;
  transition: all 0.3s ease;
}

.newsletter-button:hover {
  background: var(--gold-dark);
  color: var(--white);
}
```

### 10. Footer

```css
.site-footer {
  background: var(--charcoal-dark);
  color: var(--white);
  padding: var(--space-16) var(--space-10) var(--space-8);
}

.footer-content {
  max-width: var(--container-xl);
  margin: 0 auto;
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: var(--space-16);
  margin-bottom: var(--space-10);
}

.footer-about h3 {
  font-family: var(--font-headline);
  font-size: var(--text-headline-sm);
  margin-bottom: var(--space-4);
  color: var(--gold-primary);
}

.footer-about p {
  font-size: var(--text-small);
  line-height: var(--leading-relaxed);
  color: var(--medium-gray);
}

.footer-section h4 {
  font-family: var(--font-ui);
  font-size: var(--text-tiny);
  text-transform: uppercase;
  letter-spacing: var(--tracking-widest);
  margin-bottom: var(--space-5);
  color: var(--gold-champagne);
  font-weight: var(--weight-semibold);
}

.footer-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-section li {
  margin-bottom: var(--space-3);
}

.footer-section a {
  color: var(--medium-gray);
  text-decoration: none;
  font-size: var(--text-small);
  transition: color 0.3s ease;
}

.footer-section a:hover {
  color: var(--gold-champagne);
}

.footer-bottom {
  max-width: var(--container-xl);
  margin: 0 auto;
  padding-top: var(--space-8);
  border-top: 1px solid rgba(255,255,255,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-family: var(--font-ui);
  font-size: var(--text-tiny);
  color: var(--medium-gray);
}

.social-links {
  display: flex;
  gap: var(--space-5);
}

.social-links a {
  color: var(--medium-gray);
  text-decoration: none;
  transition: color 0.3s ease;
}

.social-links a:hover {
  color: var(--gold-champagne);
}
```

---

## Article Page Styles

### Article Header

```css
.article-header {
  max-width: var(--container-xl);
  margin: 0 auto;
  padding: var(--space-16) var(--space-10);
}

.article-hero-image {
  width: 100%;
  height: 500px;
  margin-bottom: var(--space-10);
  overflow: hidden;
}

.article-hero-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.article-category-tag {
  font-family: var(--font-ui);
  font-size: var(--text-tiny);
  text-transform: uppercase;
  letter-spacing: var(--tracking-widest);
  color: var(--gold-dark);
  font-weight: var(--weight-semibold);
  margin-bottom: var(--space-5);
  display: inline-block;
}

.article-title {
  font-family: var(--font-headline);
  font-size: var(--text-display);
  line-height: var(--leading-tight);
  font-weight: var(--weight-semibold);
  color: var(--navy-primary);
  margin-bottom: var(--space-5);
  letter-spacing: var(--tracking-tight);
  max-width: var(--container-reading);
}

.article-deck {
  font-family: var(--font-body);
  font-size: var(--text-intro);
  line-height: var(--leading-relaxed);
  color: var(--text-secondary);
  margin-bottom: var(--space-8);
  max-width: var(--container-reading);
}

.article-byline {
  display: flex;
  align-items: center;
  gap: var(--space-4);
  margin-bottom: var(--space-8);
  padding-bottom: var(--space-8);
  border-bottom: 1px solid var(--light-gray);
}

.author-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  overflow: hidden;
}

.author-info {
  flex: 1;
}

.author-name {
  font-family: var(--font-ui);
  font-size: var(--text-body);
  font-weight: var(--weight-semibold);
  color: var(--gold-dark);
  margin-bottom: 2px;
}

.article-meta {
  font-family: var(--font-ui);
  font-size: var(--text-small);
  color: var(--text-tertiary);
}
```

### Article Content

```css
.article-content {
  max-width: var(--container-reading);
  margin: 0 auto;
  padding: var(--space-10);
}

.article-content p {
  font-family: var(--font-body);
  font-size: var(--text-body-lg);
  line-height: var(--leading-loose);
  color: var(--text-primary);
  margin-bottom: var(--space-6);
}

.article-content h2 {
  font-family: var(--font-headline);
  font-size: var(--text-headline-md);
  font-weight: var(--weight-semibold);
  color: var(--navy-primary);
  margin-top: var(--space-12);
  margin-bottom: var(--space-5);
  line-height: var(--leading-snug);
}

.article-content h3 {
  font-family: var(--font-headline);
  font-size: var(--text-headline-sm);
  font-weight: var(--weight-semibold);
  color: var(--navy-primary);
  margin-top: var(--space-10);
  margin-bottom: var(--space-4);
  line-height: var(--leading-snug);
}

.article-content a {
  color: var(--blue-link);
  text-decoration: underline;
  transition: color 0.3s ease;
}

.article-content a:hover {
  color: var(--gold-dark);
}

/* Pull Quote */
.pullquote {
  font-family: var(--font-headline);
  font-size: var(--text-headline-sm);
  line-height: var(--leading-snug);
  color: var(--navy-primary);
  font-style: italic;
  margin: var(--space-12) 0;
  padding-left: var(--space-8);
  border-left: 3px solid var(--gold-champagne);
}

.pullquote-attribution {
  font-family: var(--font-ui);
  font-size: var(--text-small);
  color: var(--text-tertiary);
  font-style: normal;
  margin-top: var(--space-3);
}

/* Inline Image */
.article-image {
  margin: var(--space-12) 0;
}

.article-image img {
  width: 100%;
  height: auto;
}

.article-image-caption {
  font-family: var(--font-ui);
  font-size: var(--text-small);
  color: var(--text-tertiary);
  margin-top: var(--space-3);
  font-style: italic;
}
```

### Author Bio Box

```css
.author-bio-box {
  max-width: var(--container-reading);
  margin: var(--space-16) auto;
  padding: var(--space-10);
  background: var(--cream);
  border-top: 3px solid var(--gold-dark);
}

.author-bio-header {
  display: flex;
  gap: var(--space-6);
  margin-bottom: var(--space-6);
}

.author-bio-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.author-bio-info h3 {
  font-family: var(--font-headline);
  font-size: var(--text-headline-sm);
  color: var(--navy-primary);
  margin-bottom: var(--space-2);
}

.author-bio-title {
  font-family: var(--font-ui);
  font-size: var(--text-small);
  color: var(--text-tertiary);
  margin-bottom: var(--space-4);
}

.author-bio-text {
  font-size: var(--text-body);
  line-height: var(--leading-relaxed);
  color: var(--text-secondary);
  margin-bottom: var(--space-5);
}

.author-bio-social {
  display: flex;
  gap: var(--space-4);
}

.author-bio-social a {
  color: var(--gold-dark);
  font-size: var(--text-small);
  text-decoration: none;
}
```

---

## Form Elements

```css
.form-group {
  margin-bottom: var(--space-6);
}

.form-label {
  display: block;
  font-family: var(--font-ui);
  font-size: var(--text-small);
  font-weight: var(--weight-medium);
  color: var(--text-secondary);
  margin-bottom: var(--space-2);
}

.form-input,
.form-textarea,
.form-select {
  width: 100%;
  padding: var(--space-4);
  font-family: var(--font-ui);
  font-size: var(--text-body);
  color: var(--text-primary);
  background: var(--white);
  border: 2px solid var(--light-gray);
  transition: all 0.3s ease;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: var(--navy-primary);
  box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
}

.form-textarea {
  min-height: 120px;
  resize: vertical;
}
```

---

## Responsive Breakpoints

```css
/* Mobile First Approach */

/* Small devices - 640px and up */
@media (min-width: 640px) {
  :root {
    --text-display: 3rem;
    --text-hero: 2.5rem;
  }
}

/* Medium devices - 768px and up */
@media (min-width: 768px) {
  :root {
    --text-display: 3.5rem;
    --text-hero: 3rem;
  }
  
  .hero-grid {
    grid-template-columns: 1fr;
  }
  
  .grid-3,
  .grid-4 {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Large devices - 1024px and up */
@media (min-width: 1024px) {
  :root {
    --text-display: 4rem;
    --text-hero: 3.25rem;
  }
  
  .hero-grid {
    grid-template-columns: 1fr 1fr;
  }
  
  .grid-3 {
    grid-template-columns: repeat(3, 1fr);
  }
  
  .grid-4 {
    grid-template-columns: repeat(4, 1fr);
  }
}

/* Extra large devices - 1280px and up */
@media (min-width: 1280px) {
  /* Full layout enabled */
}
```

---

## WordPress Integration

### Theme Setup (functions.php)

```php
<?php
function beyond_borders_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => 'Primary Navigation',
        'footer' => 'Footer Navigation',
    ));
    
    // Add editor styles
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
    
    // Add image sizes
    add_image_size('hero', 1400, 600, true);
    add_image_size('featured', 800, 600, true);
    add_image_size('card', 600, 400, true);
}
add_action('after_setup_theme', 'beyond_borders_theme_setup');

// Enqueue styles and scripts
function beyond_borders_scripts() {
    // Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;500;600&display=swap', array(), null);
    
    // Main stylesheet
    wp_enqueue_style('beyond-borders-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Main script
    wp_enqueue_script('beyond-borders-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'beyond_borders_scripts');

// Custom excerpt length
function beyond_borders_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'beyond_borders_excerpt_length');

// Custom read more
function beyond_borders_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'beyond_borders_excerpt_more');
?>
```

### Customizer Integration

```php
<?php
function beyond_borders_customize_register($wp_customize) {
    // Colors Section
    $wp_customize->add_section('beyond_borders_colors', array(
        'title' => 'Theme Colors',
        'priority' => 30,
    ));
    
    // Primary Navy
    $wp_customize->add_setting('navy_primary', array(
        'default' => '#003366',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'navy_primary', array(
        'label' => 'Primary Navy',
        'section' => 'beyond_borders_colors',
    )));
    
    // Gold Primary
    $wp_customize->add_setting('gold_primary', array(
        'default' => '#C9A961',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'gold_primary', array(
        'label' => 'Gold Accent',
        'section' => 'beyond_borders_colors',
    )));
}
add_action('customize_register', 'beyond_borders_customize_register');

// Output custom colors
function beyond_borders_custom_colors() {
    $navy_primary = get_theme_mod('navy_primary', '#003366');
    $gold_primary = get_theme_mod('gold_primary', '#C9A961');
    ?>
    <style type="text/css">
        :root {
            --navy-primary: <?php echo esc_attr($navy_primary); ?>;
            --gold-primary: <?php echo esc_attr($gold_primary); ?>;
        }
    </style>
    <?php
}
add_action('wp_head', 'beyond_borders_custom_colors');
?>
```

---

## Performance Optimization

### Critical CSS

Inline critical above-the-fold CSS in `<head>`:

```html
<style>
  /* Top bar, header, navigation - inline these */
  .top-bar { background: #1A1A2E; }
  .site-header { background: #FFFFFF; }
  .main-nav { background: #003366; }
  /* Hero section styles */
  .hero { background: #0A1628; }
</style>
```

### Lazy Loading

```html
<img src="placeholder.jpg" data-src="actual-image.jpg" class="lazyload" alt="Description">
```

### Font Loading

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" href="fonts/playfair-display.woff2" as="font" type="font/woff2" crossorigin>
```

---

## Accessibility

### Focus States

```css
a:focus,
button:focus,
input:focus,
select:focus,
textarea:focus {
  outline: 2px solid var(--gold-champagne);
  outline-offset: 2px;
}

*:focus:not(:focus-visible) {
  outline: none;
}

*:focus-visible {
  outline: 2px solid var(--gold-champagne);
  outline-offset: 2px;
}
```

### Skip to Content

```html
<a href="#main-content" class="skip-link">Skip to main content</a>
```

```css
.skip-link {
  position: absolute;
  top: -40px;
  left: 0;
  background: var(--navy-primary);
  color: var(--white);
  padding: 8px 16px;
  text-decoration: none;
  z-index: 100;
}

.skip-link:focus {
  top: 0;
}
```

---

## File Structure

```
wp-content/themes/beyond-borders/
├── style.css
├── functions.php
├── index.php
├── header.php
├── footer.php
├── single.php
├── page.php
├── archive.php
├── author.php
├── category.php
├── 404.php
├── search.php
├── searchform.php
├── comments.php
├── screenshot.png
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   ├── editor-style.css
│   │   └── admin.css
│   ├── js/
│   │   ├── main.js
│   │   └── admin.js
│   └── images/
│       └── placeholder.jpg
├── template-parts/
│   ├── content.php
│   ├── content-single.php
│   ├── content-page.php
│   ├── content-none.php
│   └── hero-section.php
├── inc/
│   ├── customizer.php
│   ├── template-functions.php
│   └── template-tags.php
└── languages/
    └── beyond-borders.pot
```

---

## Implementation Checklist

### Phase 1: Setup
- [ ] Install WordPress
- [ ] Create theme directory structure
- [ ] Set up style.css with theme header
- [ ] Create functions.php with basic setup
- [ ] Add screenshot.png
- [ ] Activate theme

### Phase 2: Core Templates
- [ ] Build header.php with logo and navigation
- [ ] Build footer.php with footer sections
- [ ] Create index.php (homepage template)
- [ ] Create single.php (article template)
- [ ] Create archive.php (category pages)
- [ ] Create author.php (author archives)

### Phase 3: Styling
- [ ] Implement CSS variables
- [ ] Style navigation
- [ ] Style hero section
- [ ] Style article cards
- [ ] Style article content
- [ ] Style footer

### Phase 4: Components
- [ ] Newsletter signup form
- [ ] Social sharing buttons
- [ ] Related articles
- [ ] Author bio box
- [ ] Comments section

### Phase 5: Optimization
- [ ] Add lazy loading
- [ ] Optimize images
- [ ] Minify CSS/JS
- [ ] Test performance
- [ ] Fix accessibility issues

### Phase 6: Testing
- [ ] Cross-browser testing
- [ ] Mobile responsive testing
- [ ] Content testing
- [ ] User acceptance testing

---

**Document Version:** 1.0  
**Last Updated:** January 12, 2026  
**Status:** Ready for Development  
**Next Step:** Begin Phase 1 Implementation
