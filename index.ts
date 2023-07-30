import { ChatUserstate, Client } from 'tmi.js';
import axios from 'axios';
import FormData from 'form-data';
import fs from 'fs';

// Configuration: Set Twitch channel name and bot name
const twitchConfig = {
    channels: ['ratherrude'],
};
const botName = "streamelements"

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
    console.log(message);

    if (message.match(/(!|@)herika/i) ||
        (userstate.username && ['botrix', 'Botrix', 'nightbot', 'Nightbot', 'streamlabs', 'Streamlabs', 'moobot', 'Moobot', 'streamelements', 'StreamElements', 'wizebot', 'Wizebot'].includes(userstate.username))
    ) {
        const form = new FormData();
        form.append('message', `${message.replace(/(!|@)herika/i, '')}`);
        form.append('user', `${userstate.username}`);
        form.append('platform', 'Twitch');
        console.log(`Posting: ${userstate.username}`, `${message.replace(/(!|@)herika/i, '')}`);

        axios.post('http://localhost:8080/saig-gwserver/externalMessage.php', form, {
            headers: form.getHeaders()
        })
            .then((response) => {
                console.log('HTTP request successful');
                //console.log('Response:', response.data);
            })
            .catch((error) => {
                console.error('Error making HTTP request:', error);
            });
    }
//}

});

// Connect to Twitch IRC
client.connect().catch(console.error);

