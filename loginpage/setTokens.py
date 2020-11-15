import mysql.connector
import requests
import sys

def get_refresh_token():
    CLIENT_ID = get_data_from_database("client_id", sys.argv[1])[0]
    CLIENT_SECRET = get_data_from_database("client_secret", sys.argv[1])[0]
    CODE = get_data_from_database("code", sys.argv[1])[0]
    POST_URL = "https://www.strava.com/api/v3/oauth/token"

    data = {
        "client_id" : CLIENT_ID,
        "client_secret": CLIENT_SECRET,
        "code" : CODE,
        "grant_type" : "authorization_code"
    }


    resp = requests.post(POST_URL, data=data)
    jsonfile = resp.json()

    REFRESH_TOKEN = jsonfile.get('refresh_token')
    update_database("refresh_token", REFRESH_TOKEN,sys.argv[1])
    ACCESS_TOKEN = jsonfile.get('access_token')
    update_database("access_token", ACCESS_TOKEN,sys.argv[1])
    update_database("code", "setted", sys.argv[1])



def get_new_access_token():
    CLIENT_ID = get_data_from_database("client_id", sys.argv[1])[0]
    CLIENT_SECRET = get_data_from_database("client_secret", sys.argv[1])[0]
    REFRESH_TOKEN = get_data_from_database("refresh_token", sys.argv[1])[0]
    POST_URL = "https://www.strava.com/api/v3/oauth/token"


    data = {
        "client_id": CLIENT_ID,
        "client_secret": CLIENT_SECRET,
        "grant_type": "refresh_token",
        "refresh_token": REFRESH_TOKEN
    }

    jsonfile = requests.post(POST_URL, data=data).json()

    ACCESS_TOKEN = jsonfile.get('access_token')
    update_database("access_token", ACCESS_TOKEN, sys.argv[1])

def update_database(key, value, userid):
    DB = mysql.connector.connect(
        host="localhost",
        user="projekt",
        password="123Projekt123",
        database="projekt2"
    )

    cursor = DB.cursor()
    sql = "UPDATE users SET " + key + " = \"" + value + "\" WHERE id = " + userid

    cursor.execute(sql)
    DB.commit()


def get_data_from_database(key, userid):
    DB = mysql.connector.connect(
        host="localhost",
        user="projekt",
        password="123Projekt123",
        database="projekt2"
    )
    cursor = DB.cursor()

    sql = "SELECT " + key + " FROM projekt2.users WHERE id = " + userid
    cursor.execute(sql)

    return cursor.fetchone()

def main():
    if get_data_from_database("code", sys.argv[1])[0] != "setted":
        get_refresh_token()
    else:
        get_new_access_token()

if __name__ == '__main__':
    main()