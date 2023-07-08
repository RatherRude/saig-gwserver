import { ChatUserstate, Client } from 'tmi.js';
import axios from 'axios';
import fs from 'fs';

// Twitch configuration
const twitchConfig = {
    channels: ['ratherrude'],
};

// Initialize the IRC client
const client = new Client({
    connection: {
        secure: true,
        reconnect: true,
    },
    channels: twitchConfig.channels,
});

// Event listener for incoming messages
client.on('message', (channel: string, userstate: ChatUserstate, message: string) => {
    // Check if the message meets your criteria
    if (message.startsWith('!herika ')) {
        const requestBody = {
            queue: 'Simchat',
            prompt: '(Chat+as+Herika)',
            preprompt: message.replace('!herika ', '')
        };

        axios.post('http://localhost:8080/saig-gwserver/', requestBody)
            .then((response) => {
                console.log('HTTP request successful');
                console.log('Response:', response.data);
            })
            .catch((error) => {
                console.error('Error making HTTP request:', error);
            });

        /*
        const content = `${userstate.username}: ${message.replace('!herika ', '')}\n`;
        fs.appendFile('twitch.txt', content, (err) => {
            if (err) {
                console.error('Error saving message:', err);
            } else {
                console.log('Message saved:', content);
            }
        });
         */
    }
});

// Connect to Twitch IRC
client.connect().catch(console.error);