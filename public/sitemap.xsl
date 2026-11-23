<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
      <head>
        <title>XML Sitemap — Empetix Solutions</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <style type="text/css">
          :root {
            --bg: #0B0C0E;
            --surface: #14161A;
            --surface-2: #1C1F26;
            --line: rgba(255, 255, 255, 0.08);
            --accent: #FF4D1C;
            --text: #F3F4F6;
            --text-muted: #9CA3AF;
          }
          body {
            margin: 0;
            padding: 40px 20px;
            background: var(--bg);
            color: var(--text);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
          }
          .container {
            max-width: 1100px;
            margin: 0 auto;
          }
          .header {
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--line);
          }
          .badge {
            display: inline-block;
            background: rgba(255, 77, 28, 0.15);
            color: var(--accent);
            border: 1px solid rgba(255, 77, 28, 0.3);
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
          }
          h1 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.02em;
          }
          p.desc {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
          }
          .stats {
            display: inline-flex;
            gap: 16px;
            margin-top: 14px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 8px 16px;
            font-size: 13px;
          }
          .stats strong {
            color: var(--accent);
          }
          table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            margin-top: 20px;
          }
          th {
            background: var(--surface-2);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            text-align: left;
            padding: 14px 18px;
            border-bottom: 1px solid var(--line);
          }
          td {
            padding: 13px 18px;
            border-bottom: 1px solid var(--line);
            font-size: 13.5px;
          }
          tr:last-child td {
            border-bottom: none;
          }
          tr:hover td {
            background: rgba(255, 255, 255, 0.02);
          }
          a {
            color: var(--accent);
            text-decoration: none;
            word-break: break-all;
          }
          a:hover {
            text-decoration: underline;
          }
          .priority-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(255, 77, 28, 0.12);
            color: var(--accent);
          }
          .time {
            color: var(--text-muted);
            font-size: 12px;
          }
        </style>
      </head>
      <body>
        <div class="container">
          <div class="header">
            <span class="badge">Google &amp; AI Discovery Protocol</span>
            <h1>XML Sitemap Index</h1>
            <p class="desc">Generated automatically by Empetix Solutions for search engines (Google, Bing) and AI crawlers (Perplexity, ChatGPT).</p>
            <div class="stats">
              <span>Total Indexed URLs: <strong><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></strong></span>
            </div>
          </div>

          <table>
            <thead>
              <tr>
                <th style="width: 55%;">URL Location</th>
                <th style="width: 15%;">Priority</th>
                <th style="width: 15%;">Change Frequency</th>
                <th style="width: 15%;">Last Modified</th>
              </tr>
            </thead>
            <tbody>
              <xsl:for-each select="sitemap:urlset/sitemap:url">
                <tr>
                  <td>
                    <a href="{sitemap:loc}" target="_blank">
                      <xsl:value-of select="sitemap:loc"/>
                    </a>
                  </td>
                  <td>
                    <span class="priority-pill"><xsl:value-of select="sitemap:priority"/></span>
                  </td>
                  <td>
                    <xsl:value-of select="sitemap:changefreq"/>
                  </td>
                  <td class="time">
                    <xsl:value-of select="sitemap:lastmod"/>
                  </td>
                </tr>
              </xsl:for-each>
            </tbody>
          </table>
        </div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
