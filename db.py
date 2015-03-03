import MySQLdb
import sys
import time

def ConnectToDB():
	conn = MySQLdb.connect('localhost', 'brewmaster', 'brewmasterpassword', 'brewmaster')
	return conn
	
def WriteTempReadingToDB(temperature):
	conn = ConnectToDB()
	currentTime = time.strftime('%Y-%m-%d %H:%M:%S')
	
	cur = conn.cursor()
	try:
		cur.execute("INSERT INTO TemperatureStatistics (ReadingTime, Temperature) VALUES(%s, %s)", (currentTime, temperature))
		conn.commit()
	except:
		conn.rollback()
		print "Unexpected error:", sys.exc_info()
	conn.close()
	
def ReadCurrentDesiredTemp():
    conn = ConnectToDB()
    cursor = conn.cursor()
    temp = None
    sql = "select * from TemperatureSchedule where KeyDate <= NOW() order by KeyDate desc limit 1"
    
    try:
        cursor.execute(sql)
        result = cursor.fetchone()
        temp = result[1]
        return temp
    except:
        return None
    return temp
    conn.close()
    
def WriteToActivityLog(message):
	conn = ConnectToDB()
	currentTime = time.strftime('%Y-%m-%d %H:%M:%S')
	
	cur = conn.cursor()
	try:
		cur.execute("INSERT INTO ActivityLog (Date, Action) VALUES(%s, %s)", (currentTime, message))
		conn.commit()
	except:
		conn.rollback()
		print "Unexpected error:", sys.exc_info()
	conn.close()
    