# HerikaTwitchIRC - Herika Twitch Chat IRC Client
HerikaTwitchIRC is a Twitch IRC client that allows you to integrate Twitch chat directly into Herika's Skyrim experience. With SkyChatIRC, Herika can chat with viewers, receive notifications, and interact with the Twitch community while playing Skyrim.


## Installation
To set up HerikaTwitchIRC, follow these steps:

* Copy connect.php, index.ts, tsconfig.json and package.json into saig-gwserver folder
* Get a Twitch bot(Writing into chat)
  * Register a bot to generate messages on certain events (streamelements/nightbot/..)
  * Apply the message template (commented in index.ts) to your bot messages
* Start IRC Client(Reading from chat)
  * run "npm i" to install dependencies once
  * run "npm start" to start the bot

## FAQs
* I already have a Twitch bot, and it's not streamelements: Update the bot name in index.ts
* I already have a Twitch bot, and it's using different messages: Adjust regular expressions in index.ts (or copy index.ts to ChatGPT and provide your custom messages)