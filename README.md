# Prjektmunka 2
## GPS Art

---

### Use-Casek

A felhasználó regisztrációt követően bejelentkezik. Rajzol egy törött vonalból álló rajzot, majd elmenti azt. Ezt követően a felhasználó ha rendelkezésre áll az API akkor a fitnesz applikációból letölti az adott útvonalat, ha az API használata nem lehetséges akkor a felhasználó feltölti a GPX fájlokat és a program rangsort állít fel az alapján, hogy melyik GPX fájl által leírt útvonal hasonlít legjobban a rajzolt ábrára.

A felhasználó belépést követően rajzol egy törött vonalból álló rajzot, majd a mentés gombra kattintva elmenti. Ezután a program megjelenít egy Győr térképet kiemelve azokat a részeket, amik legjobban hasonlítanak a rajzolt ábrára.

---

### Funcionális követelmények


-	Biztonsági rések ne legyenek
-	Csak az a felhasználó tudjon bejelentkezni, aki már regisztrált és a megfelelő adatokat adja meg
-	A rajzolófelületen csak egyenes vonalakból álló poligon alakzatot rajzolhasson a felhasználó
-	Felhasználó tudjon feltölteni .gpx kiterjesztésű fájlokat
-	A program legyen képes a felhasználó által készített rajz és az adott gpx fájl közötti hasonlóságot számszerűen megadni
-	A hasonlóságok alapján állítson fel rangsort a feltöltött gpx fájlokból
-	A program a rajz alapján legyen képes győri útvonalat javasolni

---

### Nem funkcionális követelmények

-	Legyen könnyen átlátható
-	A kód legyen verzió kezelve
-	Legnépszerűbb alkalmazásokhoz való csatlakoztatás lehetőségeinek feltárása, és amennyiben lehetőség van rá, egyikhez való csatlakoztatás
-	API-n keresztül lekérni a felhasználó adott futását
Amennyiben API nem áll rendelkezésre:
-	GPX fájl tartalmazzon egy útvonalat, mely összehasonlítható a rajzolt ábrával 
-	A felhasználó csak GPX fájlokat töltsön fel

---

*Banai Alex, Egyed Vivien, Horváth Gábor, Tóth Sándor Balázs* 