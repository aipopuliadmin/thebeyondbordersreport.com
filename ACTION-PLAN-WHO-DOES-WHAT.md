# SEO/AEO/GEO Action Plan: Who Does What
**Fast-Track Implementation Guide**

**Date:** January 23, 2026  
**Goal:** Get site optimized for SEO/AEO/GEO in 2-4 weeks

---

## 📋 Quick Overview

| Task Category | Your Responsibility | My Responsibility (Technical) |
|--------------|---------------------|-------------------------------|
| **Content Strategy** | ✅ 80% | 20% |
| **Technical Setup** | 20% | ✅ 80% |
| **Schema Implementation** | 10% | ✅ 90% |
| **Rank Math Config** | ✅ 60% | 40% |
| **Ongoing Content** | ✅ 100% | 0% |

---

## 🟢 PART 1: YOUR ACTION ITEMS (Start Today!)

### Week 1: Content Preparation & Research

#### ✅ Day 1-2: Content Audit & Planning

**What You Do:**

1. **Identify Top 10 Posts** (30 mins)
   - [ ] Log into WordPress Admin
   - [ ] Go to: Posts → All Posts
   - [ ] Sort by: Views (or check Google Analytics)
   - [ ] List your top 10 performing posts
   - [ ] Save list in a Google Doc/spreadsheet

2. **Pick 3 Posts for Quick Wins** (15 mins)
   - [ ] Choose 3 posts from your top 10
   - [ ] Criteria: Already getting some traffic, easy to add FAQs
   - [ ] These will be your test cases

3. **Research Questions for Each Post** (2 hours)
   - [ ] For each of 3 posts, use these FREE tools:
     - Go to Google, search your topic
     - Scroll to "People Also Ask" section
     - Write down 5-8 questions people ask
   - [ ] Alternative: Type topic into ChatGPT, ask "What are the top 10 questions people ask about [topic]?"
   - [ ] Create a document with 5-8 Q&As per post

**Example Format:**
```
POST 1: "Understanding Global Trade"

Questions from People Also Ask:
1. What is global trade?
2. How does global trade affect economy?
3. What are the benefits of global trade?
4. Who benefits most from global trade?
5. What are examples of global trade?

My Answers (write 100-150 words each):
1. Global trade is...
2. Global trade affects the economy by...
etc.
```

---

#### ✅ Day 3-4: Create FAQ Content

**What You Do:**

1. **Write FAQ Answers** (4 hours total)
   - [ ] For each question, write 100-200 word answers
   - [ ] Use simple language (8th grade reading level)
   - [ ] Include 1-2 statistics if possible
   - [ ] Cite sources (Google, ChatGPT, industry reports)

2. **Find Statistics** (2 hours)
   - [ ] Google: "[your topic] statistics 2025"
   - [ ] Use: Statista, Pew Research, government data
   - [ ] Need 3-5 statistics per post minimum
   - [ ] Write down: Statistic + Source + Link

**Example Statistics Format:**
```
Statistics for "Global Trade" Post:

1. "Global trade reached $32 trillion in 2024"
   Source: World Trade Organization 2024 Report
   Link: https://wto.org/statistics

2. "Cross-border e-commerce grew 27% year-over-year"
   Source: McKinsey Global Commerce Report
   Link: https://mckinsey.com/reports
```

---

#### ✅ Day 5-6: Rewrite Headings as Questions

**What You Do:**

1. **Audit Current Headings** (1 hour per post)
   - [ ] Open each of your 3 posts in WordPress editor
   - [ ] List all current H2 headings
   - [ ] Rewrite each as a question

**Before vs After Examples:**

```
BEFORE: "Understanding Market Dynamics"
AFTER: "What Are Market Dynamics and How Do They Work?"

BEFORE: "Benefits of International Trade"
AFTER: "What Are the Top 5 Benefits of International Trade?"

BEFORE: "Trade Agreements Overview"
AFTER: "How Do Trade Agreements Affect Global Commerce?"
```

2. **Add Quick Answers** (2 hours per post)
   - [ ] After each new question heading, add this format:

```html
<p><strong>Quick Answer:</strong> [Write 40-60 word concise answer here that directly answers the question above.]</p>

<p>[Continue with your detailed explanation...]</p>
```

**Real Example:**
```
<h2>What Are Market Dynamics and How Do They Work?</h2>

<p><strong>Quick Answer:</strong> Market dynamics are the forces of supply and demand that influence price changes and market behavior. They work through the interaction of buyers and sellers, where increased demand raises prices while increased supply lowers them, creating constant market fluctuations.</p>

<p>Market dynamics form the foundation of economic theory and practice. These forces... [continue detailed content]</p>
```

---

#### ✅ Day 7: Configure Rank Math Settings

**What You Do:** (I'll guide you, but you click the buttons)

1. **Enable Schema Types** (10 mins)
   - [ ] Go to: WordPress Dashboard → Rank Math SEO
   - [ ] Click: General Settings → Schema
   - [ ] Check these boxes:
     - ✅ Article Schema
     - ✅ FAQ Schema
     - ✅ HowTo Schema
     - ✅ Person Schema
     - ✅ Organization Schema
   - [ ] Click: Save Changes

2. **Configure Site-Wide Defaults** (15 mins)
   - [ ] Go to: Rank Math → Titles & Meta
   - [ ] Configure:
     - Site name: "Beyond Borders Report"
     - Site description: [Your site tagline]
     - Default author: [Your name]
   - [ ] Save Changes

3. **Enable Breadcrumbs** (5 mins)
   - [ ] Go to: Rank Math → General Settings → Breadcrumbs
   - [ ] Toggle: Enable Breadcrumbs = ON
   - [ ] Save Changes
   - [ ] (I'll add code to display them later)

---

### Week 2: Content Enhancement

#### ✅ Task 1: Expand Article Length

**What You Do:** (4-6 hours per post)

1. **Target: 2,500+ Words per Post**
   - [ ] Current word count: Check in WordPress editor (bottom left)
   - [ ] Calculate how many words to add
   - [ ] Add sections like:
     - "Common Mistakes to Avoid"
     - "Expert Tips and Best Practices"
     - "Real-World Examples"
     - "Case Studies"
     - "Future Trends in [Topic]"

2. **Add Data Tables** (1 hour per post)
   - [ ] Create 1-2 comparison tables
   - [ ] Use WordPress table block OR paste HTML

**Example Table to Copy:**
```html
<table>
  <caption>Global Trade Growth 2024-2025</caption>
  <thead>
    <tr>
      <th>Region</th>
      <th>2024</th>
      <th>2025</th>
      <th>Growth %</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Asia-Pacific</td>
      <td>$12.5T</td>
      <td>$13.8T</td>
      <td>+10.4%</td>
    </tr>
    <tr>
      <td>Europe</td>
      <td>$8.2T</td>
      <td>$8.9T</td>
      <td>+8.5%</td>
    </tr>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="4">Source: World Trade Organization 2025</td>
    </tr>
  </tfoot>
</table>
```

---

#### ✅ Task 2: Add "Sources & Citations" Section

**What You Do:** (30 mins per post)

1. **Add Before Comments** 
   - [ ] Scroll to bottom of post (above comments)
   - [ ] Add new H2 heading: "Sources & Further Reading"
   - [ ] List all sources you used

**Template to Copy:**
```markdown
## Sources & Further Reading

This article includes data and insights from authoritative sources:

### Primary Sources
1. **World Trade Organization - 2025 Global Trade Report**  
   Published: January 2025 | [Link](https://wto.org)  
   Key Finding: Global trade reached $32 trillion

2. **McKinsey & Company - International Commerce Analysis**  
   Published: December 2024 | [Link](https://mckinsey.com)  
   Key Finding: E-commerce grew 27% YoY

### Additional Resources
- International Monetary Fund Trade Statistics
- United Nations Conference on Trade and Development Reports
- [Your industry sources]

---
*Last Updated: January 23, 2026*
```

---

#### ✅ Task 3: Upgrade Author Bios

**What You Do:** (1 hour total)

1. **Update Your WordPress Profile**
   - [ ] Go to: Users → Your Profile
   - [ ] Fill in:
     - Biographical Info: 200+ words
     - Job Title (if custom field exists)
     - Company/Organization
     - Social media links (Twitter, LinkedIn)

2. **Write Enhanced Bio** (use this template)

```
[Your Name] is [job title] at [Organization] with [X] years of experience in [field/industry]. 

[He/She] holds a [degree] from [University] and has [notable achievement]. [His/Her] expertise includes [topic 1], [topic 2], and [topic 3].

Previous work has been featured in [publications if any], and [he/she] regularly contributes insights on [topics] to [platforms].

Connect with [Name] on [LinkedIn/Twitter] for more insights on [your niche].
```

---

### Week 3-4: Optimization & Publishing

#### ✅ Task 1: Use Rank Math for Each Post

**What You Do:** (15 mins per post after editing)

1. **Set Focus Keywords**
   - [ ] Open post in WordPress editor
   - [ ] Find "Rank Math SEO" panel (right sidebar)
   - [ ] Add Focus Keyword: Your main topic (1 keyword)
   - [ ] Add Additional Keywords: 3-4 variations
   - [ ] Aim for: 70+ SEO Score (Rank Math shows this)

2. **Optimize Meta Description**
   - [ ] In Rank Math panel, find "Edit Snippet"
   - [ ] Write 150-160 character description
   - [ ] Include your focus keyword
   - [ ] Make it compelling (this shows in Google)

**Example:**
```
Focus Keyword: global trade impact
Additional Keywords: international trade effects, world commerce trends, trade economics

Meta Description: "Discover how global trade impacts economies in 2026. Learn about market dynamics, statistics, and expert insights on international commerce trends."
```

3. **Check Rank Math Recommendations**
   - [ ] Scroll through Rank Math's checklist
   - [ ] Fix issues marked in red/orange
   - [ ] Green = good!

---

#### ✅ Task 2: Content Publishing Schedule

**What You Do:** (Ongoing commitment)

**Minimum Publishing Frequency:**
- [ ] Week 1-2: Update 3 existing posts (as per above)
- [ ] Week 3-4: Publish 1-2 NEW posts (using new format)
- [ ] Month 2+: Publish 2 posts per week minimum

**Every New Post Must Have:**
- ✅ 2,000+ words
- ✅ Question-based H2 headings (5-8 questions)
- ✅ Quick answers (40-60 words each)
- ✅ 5-8 FAQ section at end
- ✅ 1-2 data tables
- ✅ 8-10 statistics with sources
- ✅ Sources section at bottom
- ✅ Rank Math SEO score 70+

---

#### ✅ Task 3: Monthly Content Updates

**What You Do:** (2 hours per month)

1. **Pick 3-5 Older Posts Each Month**
   - [ ] Update statistics (replace old data)
   - [ ] Add 2-3 new FAQs based on comments/questions
   - [ ] Update "Last Modified" date
   - [ ] Re-submit to Google Search Console

2. **Monitor Performance**
   - [ ] Check Google Search Console weekly
   - [ ] Look for: Featured snippets you won
   - [ ] Track: Which questions appear in "People Also Ask"
   - [ ] Note: Posts losing traffic (update those first)

---

## 🔵 PART 2: MY TECHNICAL IMPLEMENTATION

### What I Will Code/Implement for You

#### ✅ Week 1: Core Schema Setup

**I Will Create:**

1. **FAQ Schema Auto-Generator** (`/inc/class-faq-schema.php`)
   - Detects Rank Math FAQ blocks
   - Auto-generates FAQPage schema
   - Injects into `<head>` section

2. **HowTo Schema Handler** (`/inc/class-howto-schema.php`)
   - Detects step-by-step content
   - Generates HowTo structured data
   - Adds to posts automatically

3. **Enhanced Author Schema** (update `functions.php`)
   - Pulls from WordPress user meta
   - Adds Person schema with credentials
   - Includes social profile links

4. **Breadcrumbs Display** (update `header.php` or `single.php`)
   - Shows breadcrumb navigation
   - Includes BreadcrumbList schema
   - SEO-friendly markup

---

#### ✅ Week 2: Content Enhancements

**I Will Build:**

1. **Auto Table of Contents Generator** (`functions.php`)
   - Scans H2/H3 headings
   - Creates clickable TOC
   - Adds jump links
   - Option: Use Rank Math TOC block (simpler)

2. **Reading Time Calculator** (`functions.php`)
   - Calculates estimated read time
   - Displays: "X min read"
   - Adds to post meta

3. **Last Updated Display** (`template-parts/content-single.php`)
   - Shows "Published" and "Updated" dates
   - Includes proper schema markup
   - Helps with freshness signals

4. **Related Posts with Schema** (`template-parts/related-posts.php`)
   - Semantic related posts
   - ItemList schema markup
   - Better internal linking

---

#### ✅ Week 3: Advanced Features

**I Will Implement:**

1. **Statistics Shortcode** (`/inc/shortcodes/class-stat-box.php`)
   ```
   Usage: [stat number="$32T" label="Global Trade 2025" source="WTO"]
   ```
   - Creates styled stat boxes
   - Includes source attribution
   - Easy to add statistics

2. **Sources Section Template** (`template-parts/sources-section.php`)
   - Structured sources display
   - Auto-formats citations
   - Includes schema markup

3. **Author Bio Enhancement** (`template-parts/author-bio.php`)
   - Enhanced author box
   - Credentials display
   - Social links integration
   - Person schema included

4. **FAQ Block Styling** (`assets/css/faq-styles.css`)
   - Custom CSS for Rank Math FAQ blocks
   - Accordion-style (optional)
   - Mobile-optimized

---

#### ✅ Week 4: Performance & Monitoring

**I Will Set Up:**

1. **Schema Validation Function** (`functions.php`)
   - Admin notice if schema missing
   - Validates JSON-LD output
   - Helps catch errors

2. **Google Search Console Integration Helper**
   - Adds verification meta tag (if needed)
   - Sitemap notification
   - Instructions for submitting URLs

3. **Performance Optimizations**
   - Lazy loading images (if not already)
   - Defer non-critical CSS
   - Minify schema output
   - Preload critical resources

4. **Analytics Tracking Enhancements**
   - Track FAQ interactions (optional)
   - Monitor scroll depth
   - Track outbound citation clicks

---

## 📊 PROGRESS TRACKING CHECKLIST

### Week 1: Foundation ✅

**Your Tasks:**
- [ ] Identify top 10 posts
- [ ] Research 5-8 questions per post (3 posts)
- [ ] Write FAQ answers (15-24 Q&As total)
- [ ] Configure Rank Math schema settings
- [ ] Enable breadcrumbs in Rank Math

**My Tasks:**
- [ ] Create FAQ schema generator
- [ ] Set up HowTo schema handler
- [ ] Enhance author schema output
- [ ] Implement breadcrumbs display

---

### Week 2: Content Enhancement ✅

**Your Tasks:**
- [ ] Rewrite H2 headings as questions (3 posts)
- [ ] Add 40-60 word quick answers
- [ ] Expand posts to 2,500+ words
- [ ] Create 2-3 data tables per post
- [ ] Add sources section to each post

**My Tasks:**
- [ ] Build TOC generator
- [ ] Add reading time display
- [ ] Create last updated functionality
- [ ] Enhance related posts section

---

### Week 3: Advanced Features ✅

**Your Tasks:**
- [ ] Update author bio (200+ words)
- [ ] Add social profiles to WordPress
- [ ] Create 1 new post using full template
- [ ] Test all new features

**My Tasks:**
- [ ] Create statistics shortcode
- [ ] Build sources section template
- [ ] Enhance author bio display
- [ ] Style FAQ blocks

---

### Week 4: Launch & Monitor ✅

**Your Tasks:**
- [ ] Publish updated posts
- [ ] Submit to Google Search Console
- [ ] Test schema with Google Rich Results Test
- [ ] Share on social media
- [ ] Monitor analytics for 1 week

**My Tasks:**
- [ ] Add schema validation
- [ ] Set up GSC helper
- [ ] Performance optimizations
- [ ] Analytics enhancements

---

## 🎯 QUICK START: Do This TODAY

### In Next 2 Hours:

**You:**
1. [ ] Pick your top 3 posts (30 mins)
2. [ ] Google "[post topic] questions" for each (30 mins)
3. [ ] Write down 5 questions per post (30 mins)
4. [ ] Reply to me with: "Ready for technical implementation"

**Me (after you say ready):**
1. [ ] Create FAQ schema code
2. [ ] Set up HowTo schema
3. [ ] Enhance author schema
4. [ ] Give you instructions to add FAQs

### Tomorrow:

**You:**
1. [ ] Write FAQ answers (2-3 hours)
2. [ ] Add FAQs using Rank Math FAQ block
3. [ ] Test with Google Rich Results Test

**Me:**
1. [ ] Build TOC generator
2. [ ] Create statistics shortcode
3. [ ] Enhance templates

---

## 📝 CONTENT CREATION TEMPLATE

### Use This for Every New Post:

```markdown
# [Question-Based Title]?

**Quick Summary:** [150-200 word overview with 2-3 key statistics]

## Table of Contents
[Auto-generated by code I'll provide]

---

## What Is [Topic]? (The Quick Answer)
**Quick Answer (50 words):** [Concise definition]

[Detailed explanation - 300-400 words]

[Add statistic with source]

---

## Why Does [Topic] Matter in 2026?
**Quick Answer (50 words):** [Why it's important]

[Detailed explanation - 300-400 words]

[Add data table here]

---

## How to [Do Something] (Step-by-Step Guide)
**Quick Answer (50 words):** [Overview of process]

**Step 1:** [Title]
[Explanation]

**Step 2:** [Title]
[Explanation]

[Continue 5-7 steps]

---

## [Related Question Heading]?
**Quick Answer (50 words):** [Answer]

[Detailed content - 300-400 words]

---

## FAQ: Your Questions Answered

[Use Rank Math FAQ Block - Add 5-8 Q&As]

---

## Sources & Further Reading

### Primary Sources
1. [Source] - [Link]
2. [Source] - [Link]

### Industry Reports
- [Source]
- [Source]

---

**About the Author:** [Your enhanced bio]

**Last Updated:** [Date]
```

---

## 🚀 SUCCESS METRICS

### After 1 Month, You Should See:

**✅ Technical Checklist:**
- [ ] 100% of posts have FAQ schema
- [ ] All posts have Article schema
- [ ] Breadcrumbs on all pages
- [ ] Author schema on all posts
- [ ] 3+ posts with HowTo schema

**✅ Content Checklist:**
- [ ] 3+ updated posts (2,500+ words each)
- [ ] 1+ new post with full template
- [ ] 15+ total FAQs published
- [ ] 10+ statistics added across posts
- [ ] 3+ data tables created

**✅ SEO Results (30-60 days):**
- [ ] 1+ featured snippet captured
- [ ] 3+ appearances in "People Also Ask"
- [ ] Rank Math SEO score 70+ on all posts
- [ ] Google Search Console shows impressions increase

**✅ GEO Results (60-90 days):**
- [ ] Test: Search topic in ChatGPT, see if cited
- [ ] Test: Search in Perplexity, check mentions
- [ ] Monitor: Direct traffic increase (may be AI)

---

## ⚠️ COMMON MISTAKES TO AVOID

**DON'T:**
- ❌ Install multiple SEO plugins (keep only Rank Math)
- ❌ Keyword stuff (use naturally)
- ❌ Copy/paste AI content without editing
- ❌ Ignore Rank Math recommendations
- ❌ Forget to cite sources
- ❌ Write less than 2,000 words for pillar content
- ❌ Skip the FAQ section (crucial for AEO!)

**DO:**
- ✅ Write for humans first, SEO second
- ✅ Update old content regularly
- ✅ Use real statistics from real sources
- ✅ Test schema with Google tools
- ✅ Monitor Google Search Console weekly
- ✅ Be patient (results take 30-60 days)

---

## 📞 COORDINATION PROCESS

### How We'll Work Together:

**Phase 1: You Prepare Content (Week 1)**
1. You gather FAQs and questions
2. You write answers
3. You tell me: "Content ready for Post X, Y, Z"
4. I implement technical features

**Phase 2: I Build Features (Week 1-2)**
1. I create schema generators
2. I enhance templates
3. I tell you: "Features ready - here's how to use them"
4. You test and give feedback

**Phase 3: You Publish (Week 2-3)**
1. You add FAQs using Rank Math block
2. You publish updated posts
3. You test with Google Rich Results Test
4. I fix any technical issues

**Phase 4: Monitor & Optimize (Week 4+)**
1. You check analytics weekly
2. I adjust code based on results
3. You create more content
4. We iterate and improve

---

## 🎉 READY TO START?

### Your First Action (Do Now):

Reply to me with:

```
READY TO START

My top 3 posts for optimization:
1. [Post title or URL]
2. [Post title or URL]
3. [Post title or URL]

I have gathered [X] questions for each post.

Proceed with technical implementation: YES
```

Once you reply, I'll immediately start building:
1. FAQ schema generator
2. Enhanced author schema
3. Breadcrumbs implementation
4. All technical features

**Time to first results:** 7-14 days  
**Time to featured snippets:** 30-60 days  
**Time to AI citations:** 60-90 days

---

**Let's make Beyond Borders the most optimized site in your niche! 🚀**

**Questions?** Ask me anything about:
- Which posts to optimize first
- How to write better FAQs
- Where to find statistics
- How to use Rank Math features
- Anything technical

---

**Document:** Action Plan - Who Does What  
**Version:** 1.0  
**Date:** January 23, 2026  
**Status:** Ready to Execute
