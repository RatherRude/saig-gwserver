# HerikaStreamingIntegration - Herika Twitch/Kick Chat Client
HerikaStreamingIntegration allows you to integrate Twitch chat directly into Herika's Skyrim experience. With HerikaStreamingIntegration, Herika can chat with viewers, receive notifications, and interact with the Twitch community while playing Skyrim.

## About
This bundle contains:
* `externalMessage.php`: PHP file to interface Herika saig-gwserver (works as entry point for Twitch IRC Client and Botrix Crawler)
* `index.ts, tsconfig.json, package.json`: For Twitch. Node Server that fetches and cleans all messages directed to Herika from a Twitch channel's chat, aswell as all of the bot's messages triggered twitch streaming events, via Twitch API and sends them to the PHP file
* `botrixCrawler.js`: For Kick. Javascript snippet that works as a webcrawler since Kick has no API to fetch messages from. Listens in a popped out chatroom and sends user' messages, aswell as all bot messages triggered by kick streaming events, and sends them to the PHP file

## Twitch
### Install for Twitch
* Register a bot to generate messages on certain events (nightbot/streamelements/..)
* Use markup from `externalMessage.php` for message generation
* Copy `externalMessage.php` into saig-server folder
* Set your Twitch channel's name in index.ts
* If you use a different chat bot than nightbot, streamlabs, moobot, streamelements or wizebot: add your bot's name in `index.ts` to the array of allowed bots
* run "npm i" to install dependencies once
### Execution for Twitch
* run "npm start" to start listening on messages

## Kick
### Connect to Kick
* Register a bot to generate messages on certain events (nightbot/streamelements/..)
* Use markup from `externalMessage.php` for message generation
* Copy `externalMessage.php` into saig-server folder
* If you use a different chat bot than botrix: change the bot's name in `botrixCrawler.js`
* Install a CORS plugin, that you should limit to "Kick.com" (The javascript snippet is run in a browser that has the chat opened, and it needs CORS because it send requests to a different URL - from kick.com to the local Herika Server; If you need further help check the next section about CORS Plugin to get an example configuration)
### Execution for Kick
* Run the snippet: Open your chatroom in a new browser window (e.g. https://kick.com/ratherrude/chatroom), open the Dev Tools by hitting F12 and paste the contents of `botrixCrawler.js` in there to start listening on messages

//	Setting up a CORS Plugin:
//	Here is an example of a CORS Plugin's configuration:
//		- Install Firefox Add-On: Allow CORS: Access-Control-Allow-Origin
//		- Navigate to options
//		- 6. Whitelisted domains from - Allow CORS -> https://kick.com
//	You don't need this specific CORS Plugin, just make sure to set it up right as CORS is an important and valuable security feature that you don't want disabled by default(!).