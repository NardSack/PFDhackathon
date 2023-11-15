const url = "https://api.openai.com/v1/chat/completions";
let ans = []
let qn = []
//post request to chatgpt
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
            ans.push(data.choices[0].message.content)
            console.log(ans)
            textdisplay()
        })
    })
}
function textdisplay(){
    for (i = 0; i < ans&&qn; ++i) {
        document.getElementById("responses").innerHTML = ans[i]
        document.getElementById("qn").innerHTML = qn[i]
    }
    // document.getElementById("responses").innerHTML = ans[count]
    // document.getElementById("qn").innerHTML = qn[count]
    // if ( anscount&qncount <= count){
    // count +=1
}

document.getElementById("submit").addEventListener('click', function()
{
    var questions = document.getElementById("Question").value
    qn.push(questions)
    callChatGPT(questions)
})  