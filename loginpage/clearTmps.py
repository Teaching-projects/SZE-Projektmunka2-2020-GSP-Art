#!/usr/local/lib python3
import mysql.connector
import os
import sys


def clearEveryThing():
    paths = [
        "images/edit/tmp",
        "images/filled/tmp",
        "images/orig/tmp",
        "images/routes/tmp",
        "gpx/tmp"
    ]
    for path in paths:
        for file in os.listdir(path):
            os.unlink(os.path.join(path, file))

    mydb = mysql.connector.connect(
        host="localhost",
        user="projekt",
        password="123Projekt123",
        database="projekt2"
    )

    cursor = mydb.cursor()

    sql = "TRUNCATE TABLE tmp_gpx"

    cursor.execute(sql)
    mydb.commit()


def moveNeededFiles(filename):
    paths = [
        "images/edit/tmp",
        "images/filled/tmp",
        "images/orig/tmp",
        "images/routes/tmp",
    ]
    for path in paths:
        os.rename(path + "/" + filename + ".png", path[:-3] + "/" + filename + ".png")

    os.rename("gpx/tmp/" + filename + ".gpx", "gpx/" + filename + ".gpx")


def main():
    filename = sys.argv[1]

    moveNeededFiles(filename)

    clearEveryThing()


if __name__ == "__main__":
    main()
