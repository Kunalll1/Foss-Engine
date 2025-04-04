## Description

WordPress Content Generator is a powerful tool that automates your content creation process by leveraging OpenAI's API. This plugin allows you to upload a CSV file containing topics, generate content for each topic using OpenAI, edit the generated content, and publish it as WordPress posts or pages.

### Features

* **OpenAI Integration**: Connect to OpenAI's API to generate high-quality content
* **CSV Import**: Easily upload a CSV file with your desired topics
* **Batch Processing**: Generate content for multiple topics in one go
* **Content Editing**: Edit and refine AI-generated content before publishing
* **Flexible Publishing**: Publish content as either posts or pages
* **Content Regeneration**: Regenerate content that doesn't meet your standards
* **Progress Tracking**: Keep track of which topics have been processed and published

### How It Works

1. Enter your OpenAI API key in the plugin settings
2. Upload a CSV file containing your topics (You can see the sample-csv.csv file how to make one)
3. Generate content for each topic using OpenAI
4. Review and edit the generated content
5. Publish the content as a post or page, or regenerate if needed

### Use Cases

* Content marketers who need to produce blog posts at scale
* SEO professionals who want to create content for multiple keywords
* Publishers who need to quickly generate drafts for their writers
* Small business owners who want to maintain an active blog but lack time to write

## Installation

1. Upload the `wp-content-generator` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Content Generator > Settings to enter your OpenAI API key
4. Visit Content Generator > Topics to start generating content

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

### 1.0.0
* Initial release

## Upgrade Notice

### 1.0.0
Initial release


# WordPress Content Generator

**Contributors:** kunal kumar  
**Donate link:** https://kunalkr.in/  
**Tags:** content generation, openai, artificial intelligence, content writing, csv upload  
**Requires at least:** 5.0  
**Tested up to:** 6.7  
**Stable tag:** 1.0.0  
**Requires PHP:** 7.2  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

A WordPress plugin that generates content using OpenAI based on topics from a CSV file, with editing and publishing capabilities.