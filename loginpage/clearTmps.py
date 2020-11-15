import mysql.connector
import os
import sys


def clearEveryThing(username):
    paths = [
        "images/edit/tmp",
        "images/filled/tmp",
        "images/orig/tmp",
        "images/routes/tmp",
        "gpx/tmp"
    ]
    for path in paths:
        for file in os.listdir(path):
            if username in file:
                mydb = mysql.connector.connect(
                    host="localhost",
                    user="projekt",
                    password="123Projekt123",
                    database="projekt2"
                )

                cursor = mydb.cursor()

                sql = "DELETE FROM tmp_gpx WHERE name LIKE '%" + username + "%'"

                cursor.execute(sql)
                mydb.commit()
                os.unlink(path + "/" + file)


def moveNeededFiles(filename):
    paths = [
        "images/edit/tmp",
        "images/filled/tmp",
        "images/orig/tmp",
        "images/routes/tmp",
    ]
    for path in paths:
        os.rename(path + "/" + filename + ".png", path[:-3] + filename + ".png")

    os.rename("gpx/tmp/" + filename + ".gpx", "gpx/" + filename + ".gpx")


def main():
    filename = ""
    if (len(sys.argv) == 2):
        filename = sys.argv[1]

        username = filename.split('-')[0]

        moveNeededFiles(filename)

        clearEveryThing(username)
    elif (len(sys.argv) == 3):
        clearEveryThing(sys.argv[1])



if __name__ == "__main__":
    main()
