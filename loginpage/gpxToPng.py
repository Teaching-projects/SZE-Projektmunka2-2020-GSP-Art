import gpxpy
import gpxpy.gpx
import matplotlib.pyplot as plt
import mysql.connector
import cv2
import os
import sys


def get_coordinates(filename):
    # utvonal koordinatak mentese
    gpx_file = open(filename, 'r')
    gpx = gpxpy.parse(gpx_file)

    x_geo = []
    y_geo = []

    for track in gpx.tracks:
        for segment in track.segments:
            for point in segment.points:
                # print('Point at ({0},{1})'.format(point.latitude, point.longitude))
                x_geo.append(point.longitude)
                y_geo.append(point.latitude)
    return (x_geo, y_geo)


def save_route(filename, x, y):
    # utvonal plottolasa, png fileba mentese
    plt.figure(figsize=(5.2083333333, 5.2083333333))
    # plt.figure(figsize=(500*0.0104166667, 500*0.0104166667))
    plt.style.use("classic")
    plt.axis("equal")
    plt.axis("off")
    plt.plot(x, y, "b", linewidth=3)
    plt.savefig(filename)


def save_filled(filename, x, y):
    # alakzat plottolasa, png fileba mentese
    plt.figure(figsize=(5.2083333333, 5.2083333333))
    # plt.figure(figsize=(500*0.0104166667, 500*0.0104166667))
    plt.style.use("dark_background")
    plt.axis("equal")
    plt.axis("off")
    plt.plot(x, y, "w", linewidth=2)
    plt.fill(x, y, "w")
    plt.savefig(filename)


def mysqlUpload(filename, score, user_id):
    mydb = mysql.connector.connect(
        host="localhost",
        user="projekt",
        password="123Projekt123",
        database="projekt2"
    )

    cursor = mydb.cursor()

    sql = "INSERT INTO tmp_gpx (user_id, name, score) VALUES (%s,%s,%s)"
    val = (user_id, filename, score)

    cursor.execute(sql, val)

    mydb.commit()

    print(cursor.rowcount, "sor lett basztatva")

def calculateScore(filename1, filename2):
    img1 = cv2.imread(filename1, cv2.IMREAD_GRAYSCALE)
    img2 = cv2.imread(filename2, cv2.IMREAD_GRAYSCALE)

    raw = cv2.matchShapes(img1, img2, cv2.CONTOURS_MATCH_I2, 0)

    return 100 - (raw * 100)


def main1(username, user_id):
    # get gpx/tmp tree because all gpx file need to png
    for file in os.listdir("./gpx/tmp"):
        if username in file:
            filename = file[:-4]
            x, y = get_coordinates("gpx/tmp/" + filename + ".gpx")
            save_filled("images/filled/tmp/"+filename+".png", x, y)

            x, y = get_coordinates("gpx/tmp/" + filename + ".gpx")
            save_route("images/routes/tmp/" + filename + ".png", x, y)

    #filenameOfDrawing = "images/edit/tmp/" + os.listdir(f"./images/edit/tmp")[0]

    for file in os.listdir("./images/filled/tmp"):
        if username in file:
            score = calculateScore('images/edit/tmp/' + file, "images/filled/tmp/" + file)

            mysqlUpload(file[:-4], round(score,3), user_id)


if __name__ == "__main__":
    if len(sys.argv) == 3:
        main1(sys.argv[1], sys.argv[2])
