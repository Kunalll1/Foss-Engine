**Contributors:** designomate
**Contributore URL:** https://www.designomate.com
**Website:** https://fossengine.com/  
**Tags:** content generation, artificial intelligence, content writing, SEO, alt text, meta title, meta description
**Requires at least:** 5.0  
**Tested up to:** 6.8  
**Stable tag:** 1.0.2  
**Requires PHP:** 7.2  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that generates content using OpenAI and DeepSeek based on topics from a CSV file. Users must provide their own API keys for content generation, giving them full control over usage and associated costs.

## Description

Foss Engine is a powerful WordPress plugin that automates your content creation process by leveraging OpenAI and DeepSeek's API. This plugin allows you to upload a CSV file containing topics, generate content for each topic using OpenAI, edit the generated content, and publish it as WordPress posts or pages. Users must provide their own API keys for content generation, giving them full control over usage and associated costs.

### Features

- **OpenAI and DeepSeek API Integration**: Connect to OpenAI or DeepSeek API to generate high-quality content
- **CSV Import**: Easily upload a CSV file with your desired topics
- **Batch Processing**: Generate content for multiple topics in one go
- **Content Editing**: Edit and refine AI-generated content before publishing
- **Flexible Publishing**: Publish content as either posts or pages
- **Content Regeneration**: Regenerate content that doesn't meet your standards
- **Progress Tracking**: Keep track of which topics have been processed and published

### How It Works

**Step 1: Add Your API Key**

- Before generating content, you must enter your API key.
- Please sign and login to AI model(OpenAI or DeepSeek) and generate the api key. How to generate api key on [OpenAI]: https://www.youtube.com/watch?v=OB99E7Y1cMA&pp=ygUeaG93IHRvIGdlbmVyYXRlIG9wZW5haSBhcGkga2V5 or [DeepSeek]: https://www.youtube.com/watch?v=c_mmydOAKl0&pp=ygUeaG93IHRvIGNyZWF0ZSBkZWVwc2VlayBhcGkga2V50gcJCX4JAYcqIYzv?
- Go to FossEngine → Settings in your WordPress admin menu.
- Under the API Configuration section:
- Choose your preferred AI provider: OpenAI or DeepSeek.
- Paste your API Key into the corresponding field.
- Enter the custom prompt for generating content. AI model will follow the prompt instructions to generate the content.
- Click Save Settings.

❗ Make sure your AI Model has enough funds for generating multiple pieces of contents.

❗ Please go through OpenAI's [Privacy Policy]: https://openai.com/policies/row-privacy-policy/ and [Terms of use]: https://openai.com/policies/terms-of-use/ , and DeepSeek's [Privacy Policy]: https://cdn.deepseek.com/policies/en-US/deepseek-privacy-policy.html and [Terms of Use]: https://cdn.deepseek.com/policies/en-US/deepseek-terms-of-use.html

**Step 2: Prepare Your CSV File**

- Create a CSV file with the list of topics you want content for.
- CSV Guidelines:
- Each topic should be on a separate line.
- Use only one column.
- No header row is required.

Example:

- How to grow indoor plants
- Benefits of solar energy for homes
- Best Shopify apps for conversions

**Step 3: Upload Topics CSV**

- Go to FossEngine → Topics.
- Click Upload CSV and select your file.
- The plugin will display a list of imported topics.
- Review the topics to ensure everything looks correct.

**Step 4: Generate Content**

- After uploading, select one or more topics from the list.
- Click Generate Content.
- Plugin will upload data of your CSV file to AI model of your choice (OpenAI or DeepSeek) to generate the content for each topic

**Step 5: Review and Edit Content**

- Once content is generated:
- Click Edit next to a topic.
- Make any changes using the built-in editor (includes formatting, links, etc.).
- You can Regenerate the content if you’re not satisfied.

**Step 6: Publish Your Posts**

- After editing:
- Click Publish to post it directly to your site.
- Choose whether to publish it as a Post or a Page.

**Additional Features**

- Regenerate content for a topic with a single click.
- Bulk actions to generate, edit, or publish multiple topics at once.
- Track status of each topic (Generated/Pending).
- Date of adding the topics

### Use Cases

- Content marketers who need to produce blog posts at scale
- SEO professionals who want to create content for multiple keywords
- Publishers who need to quickly generate drafts for their writers
- Small business owners who want to maintain an active blog but lack time to write

== Installation ==

1. Click the `Download` button on the wordpress plugin directory page and choose `Download` to download the plugin.
2. Upload the foss-engine.zip file via the `Plugins > Add PLugin` in your WordPress dashboard or search Foss Engine plugins search section and install the plugin
3. Activate the plugin from the Plugins menu in WordPress.
4. Go to Foss Engine > Settings and enter your OpenAI or DeepSeek API key.
5. Navigate to Foss Engine > Topics to begin generating content.

== Frequently Asked Questions ==

= Do I need an OpenAI API key or DeepSeek API key? =

Yes, you'll need to sign up for an OpenAI API key at https://platform.openai.com/ or sign up for DeepSeek API: https://platform.deepseek.com/sign_in to use this plugin.

= What format should my CSV file be in? =

Your CSV file should have one topic per line. The first column will be used as the topic. You can optionally include a header row.

= Are there any limits to how many topics I can process? =

The plugin itself doesn't impose any limits, but be aware that the OpenAI API has rate limits and usage costs associated with it.

= Can I customize the prompt used for content generation? =

Yes, you can customize the prompt template in the plugin settings. Use [TOPIC] as a placeholder for the actual topic.

= Will the generated content be SEO friendly? =

The plugin uses a well-crafted prompt that encourages SEO-friendly content, but you may want to review and edit the content to ensure it meets your specific SEO requirements.

= Is the plugin GDPR compliant? =

The plugin does not collect any personal data from your visitors. However, you should review OpenAI's privacy policy to ensure compliance with GDPR.

= Can I regenerate content if I'm not satisfied with it? =

Yes, you can regenerate content for any topic that has not been published yet.

= AI Services and Data Usage =

Foss Engine utilizes the following AI services to generate content:

### OpenAI and DeepSeek

#### OpenAI

OpenAI is an advanced artificial intelligence research and deployment company. It offers state-of-the-art language models like GPT-4, which can generate human-like content, answer questions, and assist with a variety of natural language tasks. Foss Engine uses OpenAI’s API to generate high-quality content such as blog posts, SEO metadata, and image alt text based on provided topics.

Website: [https://www.openai.com](https://www.openai.com)

#### DeepSeek

DeepSeek is a cutting-edge AI platform offering multilingual and efficient large language models for content generation. It serves as an alternative to OpenAI for generating written content, allowing users to leverage different capabilities or pricing models. Foss Engine integrates DeepSeek’s API to give users a flexible and scalable content creation option.
Website: [https://deepseek.com](https://deepseek.com)

- **OpenAI** – [https://api.openai.com](https://api.openai.com)  
  Used to generate content from your provided prompts.  
  ➤ [OpenAI Terms of Use](https://openai.com/policies/terms-of-use)  
  ➤ [OpenAI Privacy Policy](https://openai.com/policies/privacy-policy)
- **DeepSeek** – [https://api.deepseek.com](https://api.deepseek.com)  
  Alternative model for content generation.  
  ➤ [DeepSeek Terms of Service](https://deepseek.com/terms)  
  ➤ [DeepSeek Privacy Policy](https://deepseek.com/privacy)

- **Service Purpose:** Used to generate content such as blog posts, meta titles, descriptions, and alt text by processing topic-based prompts.

- **Data Sent:** When content generation is triggered, Foss Engine sends your custom prompt and the topic (entered in your CSV file) to OpenAI's API for processing.

- **Data Received:** The plugin receives the generated text content in response.

- **When It's Sent:** Data is sent only when you actively request content generation for a topic. No background data sharing occurs.

- **OpenAI Terms:** https://openai.com/policies/terms-of-use

- **OpenAI Privacy Policy:** https://openai.com/policies/privacy-policy

- **DeepSeek Terms:** https://cdn.deepseek.com/policies/en-US/deepseek-terms-of-use.html

- **DeepSeek Privacy Policy:** https://cdn.deepseek.com/policies/en-US/deepseek-privacy-policy.html

Foss Engine does not collect, log, or transmit any personal data to third-party services.

The plugin processes content generation requests securely and only when initiated by the user.

API keys are stored locally on your WordPress site and not transmitted to any third party beyond OpenAI or DeepSeek, depending on your selection.

## Changelog

### 1.0.3

- Updated version with various improvements
- Added DeepSeek integration for content generation

### 1.0.2

Initial release
