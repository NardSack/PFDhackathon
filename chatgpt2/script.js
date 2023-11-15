import { config } from "dotenv"
config()

import { Configuration, OpenAIAPI } from "openai"

console.log("API Key:", process.env.API_KEY);

const openai = new OpenAIAPI(new Configuration({
    apiKey: process.env.API_KEY
}))

openai.createChatCompletion({
    model:"gpt-3.5-turbo",
    message:[{role:"user", content: "hello chatgpt"}]
})
.then(res => {
    console.log(res)
})