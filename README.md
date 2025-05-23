# Foss Engine

**Contributors:** kunal kumar
**Contributore URL:** https://kunalkr.in/
**Donate link:** https://fossengine.com/  
**Tags:** content generation, openai, artificial intelligence, content writing, csv upload  
**Requires at least:** 5.0  
**Tested up to:** 6.8  
**Stable tag:** 1.0.2  
**Requires PHP:** 7.2  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that generates content using OpenAI based on topics from a CSV file, with editing and publishing capabilities.

## Description

Foss Engine is a powerful tool that automates your content creation process by leveraging OpenAI's API. This plugin allows you to upload a CSV file containing topics, generate content for each topic using OpenAI, edit the generated content, and publish it as WordPress posts or pages.

### Features

- **OpenAI Integration**: Connect to OpenAI's API to generate high-quality content
- **CSV Import**: Easily upload a CSV file with your desired topics
- **Batch Processing**: Generate content for multiple topics in one go
- **Content Editing**: Edit and refine AI-generated content before publishing
- **Flexible Publishing**: Publish content as either posts or pages
- **Content Regeneration**: Regenerate content that doesn't meet your standards
- **Progress Tracking**: Keep track of which topics have been processed and published

### How It Works

1. Enter your OpenAI API key in the plugin settings
2. Upload a CSV file containing your topics (You can see the sample-csv.csv file how to make one)
3. Generate content for each topic using OpenAI
4. Review and edit the generated content
5. Publish the content as a post or page, or regenerate if needed

### Use Cases

- Content marketers who need to produce blog posts at scale
- SEO professionals who want to create content for multiple keywords
- Publishers who need to quickly generate drafts for their writers
- Small business owners who want to maintain an active blog but lack time to write

## Installation

1. Click the `CODE` button on the GitHub page and choose `Download ZIP` to download the plugin.
2. Upload the foss-engine.zip file via the `Plugins > Add New section` in your WordPress dashboard.
3. Activate the plugin from the Plugins menu in WordPress.
4. Go to Foss Engine > Settings and enter your OpenAI API key.
5. Navigate to Foss Engine > Topics to begin generating content.

## Frequently Asked Questions

### Do I need an OpenAI API key?

Yes, you'll need to sign up for an OpenAI API key at https://platform.openai.com/ to use this plugin.

### What format should my CSV file be in?

Your CSV file should have one topic per line. The first column will be used as the topic. You can optionally include a header row.

### Are there any limits to how many topics I can process?

The plugin itself doesn't impose any limits, but be aware that the OpenAI API has rate limits and usage costs associated with it.

### Can I customize the prompt used for content generation?

Yes, you can customize the prompt template in the plugin settings. Use [TOPIC] as a placeholder for the actual topic.

### Will the generated content be SEO friendly?

The plugin uses a well-crafted prompt that encourages SEO-friendly content, but you may want to review and edit the content to ensure it meets your specific SEO requirements.

### Is the plugin GDPR compliant?

The plugin does not collect any personal data from your visitors. However, you should review OpenAI's privacy policy to ensure compliance with GDPR.

### Can I regenerate content if I'm not satisfied with it?

Yes, you can regenerate content for any topic that has not been published yet.

## Changelog

### 1.0.2

- Updated version with various improvements
- Added DeepSeek integration for content generation

### 1.0.2

Initial release
