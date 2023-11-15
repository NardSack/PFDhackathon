import pyttsx3
import speech_recognition as sr
#import os
#from dotenv import load_dotenv
#load_dotenv()
#OPENAI_KEY = os.getenv('sk-...1tlU')
import openai

openai.api_key ="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"; #insert your key here
print("hi")
# for ai to speak function
def SpeakText(command):
    engine = pyttsx3.init()
    engine.say(command)
    engine.runAndWait()
    
r = sr.Recognizer()
# converting recorded audio to text function
def record_text():
    
    while(True):
        try:
            with sr.Microphone() as source2:
                
                r.adjust_for_ambient_noise(source2, duration=0.2)  
                print("I'm listening")
                audio2 = r.listen(source2)
                
                MyText = r.recognize_google(audio2)
                return MyText
        
        except sr.RequestError as e:
            print("Could not request results; {0}".format(e))
            
        except sr.UnknownValueError:
            print("Unknown error occurred")
            
# send to chatbot function
def send_to_chatGPT(messages, model="gpt-3.5-turbo"):
    
    response = openai.ChatCompletion.create(
        model=model,
        messages=messages,
        max_tokens = 100,
        n=1,
        stop=None,
        temperature=0.5,
    )
    
    message = response.choices[0].message.content
    messages.append(response.choices[0].message)
    
    if message == "goodbye":
        exit()
    return message

# run the code here
messages = [{"role":"user","content":"Pretend you are an OCBC staff."}]

print("hi")
while(True):
    text = record_text()
    messages.append({"role":"user","content":text})
    response = send_to_chatGPT(messages)
    print(response)
    SpeakText(response)