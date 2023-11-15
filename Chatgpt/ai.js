//install npm thn npm install openai and readline
// Import the necessary libraries
// const { createInterface } = require('readline');
// const openai = require('openai');
import { createInterface } from "../node_modules/readline/readline.js";
// import OpenAI from "openai";

// Set up the OpenAI API client
const openaiApiKey = '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';
const openaiApiEndpoint = 'https://api.openai.com/v1/chat/completions';
const openaiApiModel = 'gpt-3.5-turbo';
openai.apiKey = openaiApiKey;
openai.apiEndpoint = openaiApiEndpoint;

// Create a readline interface for user input
const rl = createInterface({
  input: process.stdin,
  output: process.stdout
});

// Create a function to handle user input
async function handleInput(input) {
  // Send the user input to the GPT-3 API for processing
  const response = await openai.completions.create({
    engine: openaiApiModel,
    prompt: input,
    maxTokens: 1024,
    n: 1,
    stop: '\n',
    temperature: 0.5,
  });

  // Log the response from the GPT-3 API
  console.log(response.choices[0].text.trim());
}

// Prompt the user for input
rl.question('Enter your message: ', async (input) => {
  // Call the handleInput function with the user input
  await handleInput(input);

  // Close the readline interface
  rl.close();
});
