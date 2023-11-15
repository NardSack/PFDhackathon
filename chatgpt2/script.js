import { config } from "dotenv"
config()
import OpenAI from 'openai'
import readline from 'readline'

console.log("API Key:", process.env.API_KEY);

const openai = new OpenAI({
    apiKey: process.env.API_KEY
})

const userinterface = readline.createInterface({
    input: process.stdin,
    output: process.stdout
})

userinterface.prompt()
userinterface.on("line", async input => {
    const chatCompletion = await openai.chat.completions.create({
        model: "gpt-3.5-turbo",
        messages: [{"role": "user", "content": input}],
      })
      console.log(chatCompletion.choices[0].message.content)
      userinterface.prompt()
})

