import RPi.GPIO as GPIO
import time
import db
import tempsensor
import sys
sys.path.append("/home/brandt/brewmaster/Adafruit_CharLCD")
from Adafruit_CharLCD import Adafruit_CharLCD

#constants
UPPER_TEMP_THRESHOLD = 0.25
LOWER_TEMP_THRESHOLD = 0
POWER_CONTROL_PIN = 18
POWER_STATE_ON = 1
POWER_STATE_OFF = 0
READING_DELAY = 15

#setup the GPIO pins,
GPIO.setmode(GPIO.BCM) # Use broadcom pin numbering
GPIO.setup(POWER_CONTROL_PIN, GPIO.OUT) #set our specified pin to output
GPIO.output(POWER_CONTROL_PIN,False) # power is off initially

#set our global var initial state
powerState = POWER_STATE_OFF

#initialize LCD
lcd = Adafruit_CharLCD()
lcd.begin(20,4)

def PowerOn():
	global powerState
	GPIO.output(POWER_CONTROL_PIN,True)
	powerState = POWER_STATE_ON
	db.WriteToActivityLog("Power on")
	
def PowerOff():
	global powerState
	GPIO.output(POWER_CONTROL_PIN,False)
	powerState = POWER_STATE_OFF
	db.WriteToActivityLog("Power off")

def WriteLCDLine1(message):
    lcd.setCursor(0,0)
    lcd.message(message)

def WriteLCDLine2(message):
    lcd.setCursor(0,1)
    lcd.message(message)

def WriteLCDLine3(message):
    lcd.setCursor(0,2)
    lcd.message(message)

def WriteLCDLine4(message):
    lcd.setCursor(0,3)
    lcd.message(message)

lcd.clear()

try:
	while(1):
		desiredTemp = db.ReadCurrentDesiredTemp()
		actualTemp = tempsensor.read_temp_farenheit()
		lcd.begin(20,4)
		lcd.clear()
		WriteLCDLine1("Desired: " + "{0:.1f}".format(desiredTemp) + chr(223) + "F")
		WriteLCDLine2("Actual:  " + "{0:.1f}".format(actualTemp) + chr(223) + "F")
		print("Temp: " + str(actualTemp))
		if ((actualTemp - desiredTemp) > UPPER_TEMP_THRESHOLD) and (powerState == POWER_STATE_OFF):
			PowerOn()
			#print("Powered on.")
		elif ((actualTemp - desiredTemp) < LOWER_TEMP_THRESHOLD) and (powerState == POWER_STATE_ON):
			PowerOff()
			#print("Powered off.")
		if powerState == POWER_STATE_ON:
			WriteLCDLine3("Power is ON")
		else:
			WriteLCDLine3("Power is OFF")
		db.WriteTempReadingToDB(actualTemp)
		time.sleep(READING_DELAY)
		
except KeyboardInterrupt:
	print("Ctrl C detected")
finally:
	GPIO.cleanup()
