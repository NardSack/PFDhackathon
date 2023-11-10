import pyttsx3
import speech_recognition as sr

#import os
#from dotenv import load_dotenv
#load_dotenv()
#OPENAI_KEY = os.getenv('sk-...1tlU')

import openai
# openai.api_key = "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" #Jing kai key
openai.api_key="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" #raynard key

def SpeakText(command):
    engine = pyttsx3.init()
    engine.say(command)
    engine.runAndWait()
    
r = sr.Recognizer()

def record_text():
    
    while(1):
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

messages = [{"role":"user","content":"Please act like Jarvis from Iron man."}]
while(1):
    text = record_text()
    messages.append({"role":"user","content":text})
    response = send_to_chatGPT(messages)
    SpeakText(response)
    
    print(response)
    