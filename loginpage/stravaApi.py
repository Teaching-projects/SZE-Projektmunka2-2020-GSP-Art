import os
import shutil
import sys
import uuid

import gpxpy
import gpxpy.gpx
import mysql.connector
import polyline
import requests

import gpxToPng

DB = mysql.connector.connect(
    host="localhost",
    user="projekt",
    password="123Projekt123",
    database="projekt2"
)

ACCESS_TOKEN = ""
USER_ID = sys.argv[1]
USERNAME = ""

def get_all_activities():
    url = "https://www.strava.com/api/v3/athlete/activities"

    header = {
        "page" : "1",
        "per_page" : "10",
        "Authorization": "Bearer " + ACCESS_TOKEN
    }

    jsonfile = requests.get(url, headers=header).json()

    return jsonfile

def create_gpx(activities):
    # get point from activities
    for activity in activities:
        generatedName = USERNAME + "-" + str(uuid.uuid4())[:8] + str(uuid.uuid4())[:5]
        filename = "gpx/tmp/" + generatedName + ".gpx"

        points = polyline.decode(activity['map']['summary_polyline'])

        # init gpx file
        gpx = gpxpy.gpx.GPX()

        gpx_track = gpxpy.gpx.GPXTrack()
        gpx.tracks.append(gpx_track)

        gpx_segment = gpxpy.gpx.GPXTrackSegment()
        gpx_track.segments.append(gpx_segment)

        for point in points:
            gpx_segment.points.append(gpxpy.gpx.GPXTrackPoint(point[0], point[1]))

        file = open(filename, "w")
        file.write(gpx.to_xml())
        file.close()
        cursor = DB.cursor()
        sql = "INSERT INTO tmp_gpx (name, user_id) VALUES (%s,%s)"

        cursor.execute(sql, (generatedName, USER_ID))
        DB.commit()


def copy_needed_pngs():
    for asd in ["edit", "orig"]:
        filenameOrig = f"images/{asd}/tmp/" + [file for file in os.listdir(f"images/{asd}/tmp") if USERNAME in file][0]

        sql = "SELECT name FROM tmp_gpx WHERE user_id = " + USER_ID
        cursor = DB.cursor()

        cursor.execute(sql)

        res = cursor.fetchall()

        for i in res:
            filename = f"images/{asd}/tmp/" + i[0] + ".png"
            if filename != filenameOrig:
                shutil.copy(filenameOrig, filename)
        cursor.close()
        os.unlink(filenameOrig)


def get_refresh_token():
    CLIENT_ID = get_data_from_database("client_id", USER_ID)[0]
    CLIENT_SECRET = get_data_from_database("client_secret", USER_ID)[0]
    CODE = get_data_from_database("code", USER_ID)[0]
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
    update_database("refresh_token", REFRESH_TOKEN, USER_ID)
    ACCESS_TOKEN = jsonfile.get('access_token')
    update_database("access_token", ACCESS_TOKEN, USER_ID)
    update_database("code", "setted", USER_ID)



def get_new_access_token():
    CLIENT_ID = get_data_from_database("client_id", USER_ID)[0]
    CLIENT_SECRET = get_data_from_database("client_secret", USER_ID)[0]
    REFRESH_TOKEN = get_data_from_database("refresh_token", USER_ID)[0]
    POST_URL = "https://www.strava.com/api/v3/oauth/token"


    data = {
        "client_id": CLIENT_ID,
        "client_secret": CLIENT_SECRET,
        "grant_type": "refresh_token",
        "refresh_token": REFRESH_TOKEN
    }

    jsonfile = requests.post(POST_URL, data=data).json()

    ACCESS_TOKEN = jsonfile.get('access_token')
    update_database("access_token", ACCESS_TOKEN, USER_ID)

def update_database(key, value, userid):
    cursor = DB.cursor()
    sql = "UPDATE users SET " + key + " = \"" + value + "\" WHERE id = " + userid

    cursor.execute(sql)
    DB.commit()


def get_data_from_database(key, userid):
    cursor = DB.cursor()

    sql = "SELECT " + key + " FROM projekt2.users WHERE id = " + userid
    cursor.execute(sql)

    return cursor.fetchone()


def main():
    get_new_access_token()

    global USERNAME
    USERNAME = get_data_from_database('username', USER_ID)[0]
    global ACCESS_TOKEN
    ACCESS_TOKEN = get_data_from_database("access_token", USER_ID)[0]

    create_gpx(get_all_activities())
    copy_needed_pngs()

    gpxToPng.main1(USERNAME)


if __name__ == "__main__":
    main()