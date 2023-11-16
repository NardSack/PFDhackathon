//speech recognition input
var text = []
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
let recognition = new SpeechRecognition()
recognition.lang = "en-US"

recognition.onstart = () => {
    console.log("starting listening, speak in microphone")
};

recognition.onspeechend = () => {
    console.log("stopped listening")
    recognition.stop()
}

recognition.onresult = (result) => {
    const vocalInput = result.results[0][0].transcript
    // document.getElementById("qn").innerHTML += vocalInput + "<br>" // i don know what this is for
    console.log(vocalInput)
    callChatGPT(vocalInput)
}
document.getElementById("audio").addEventListener('mousedown', function()
{
    recognition.start()
})  
document.getElementById("audio").addEventListener('mouseup', function()
{
    recognition.stop()
}) 
//post request to chatgpt
const url = "https://api.openai.com/v1/chat/completions"
function callChatGPT(input) {
    fetch(url, {
        "method": "POST",
        "headers": new Headers({
            "Content-Type": "application/json",
            "Authorization": "Bearer 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"
        }),
        body: JSON.stringify({
            model: "gpt-3.5-turbo",
            messages: [{"role": "user", "content": input}],
            temperature: 0.7,
        })
    }).then((res) => {
        res.json().then((data) => {
            console.log(data.choices[0].message.content)
            document.getElementById("textchatarea").innerHTML += "<div class='qn'>AI:<br>"+data.choices[0].message.content + "<br></div>"
            let text = new SpeechSynthesisUtterance(data.choices[0].message.content);
            speechSynthesis.speak(text);
        })
    })
}

document.getElementById("submit").addEventListener('click', function()
{
    var questions = document.getElementById("Question").value
    var qn = "Pretend you are an OCBC AI Assitant." + questions
    document.getElementById("textchatarea").innerHTML += "<div class='responses'>User:<br>"+questions + "<br></div>"
    console.log(questions)
    callChatGPT(qn)
})