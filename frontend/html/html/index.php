<!DOCTYPE html>


<!-- PHP Skript zum API-Aufruf -->
<!-- Block 1: Aufruf der API, wenn Längen- und Breitengrad angegeben wurden;
     Block 2: Aufruf, wenn nur der Städtename angegeben wurde;
     Block 3: Aufruf, wenn über die Buttons "Mögliche Städte" eine Stadt aufgerufen wurde;
     Block 4: Aufruf, wenn die Seite zum ersten mal geladen wird -->
<?php
    if(isset($_POST['lon']) && isset($_POST['lat'])){
        if($_POST['lon'] != null && $_POST['lat'] != null){
            $boolLatLon = true;
            $response1 = file_get_contents("http://10.50.15.51:7353/cityconvert/coord?lat=".$_POST['lat']."&lon=".$_POST['lon']);
            $response1 = json_decode($response1, true);
            $cityName = $response1["cityName"];
            $response2 = file_get_contents("http://10.50.15.51:7352/pol/actualPollutionCoord?lat=".$_POST['lat']."&lon=".$_POST['lon']);
            $response2 = json_decode($response2, true);
            $histResponse = file_get_contents("http://10.50.15.51:7352/pol/HistoryPollutionCoord?lat=".$_POST['lat']."&lon=".$_POST['lon']."&startDate=".(time()-2595600)."&endDate=".time());
            $histResponse = json_decode($histResponse, true);
        }else {
            if(isset($_POST['name'])){  
                $boolLatLon = false;
                $name = str_replace(" ", "%20", $_POST['name']);
                $cityLocation = file_get_contents("http://10.50.15.51:7353/cityconvert/city?cityName=".$name);
                $cityLocation = json_decode($cityLocation, true);
                $response2 = file_get_contents("http://10.50.15.51:7352/pol/actualPollutionIn?cityName=".$name);
                $response2 = json_decode($response2, true);
                $histResponse = file_get_contents("http://10.50.15.51:7352/pol/HistoryPollutionIn?cityName=".$name."&startDate=".(time()-2595600)."&endDate=".time());
                $histResponse = json_decode($histResponse, true);
            }
        }  
    }else if(isset($_POST['name'])){
        $boolLatLon = false;
        $name = str_replace(" ", "%20", $_POST['name']);
        $cityLocation = file_get_contents("http://10.50.15.51:7353/cityconvert/city?cityName=".$name);
        $cityLocation = json_decode($cityLocation, true);
        $response2 = file_get_contents("http://10.50.15.51:7352/pol/actualPollutionIn?cityName=".$name);
        $response2 = json_decode($response2, true);
        $histResponse = file_get_contents("http://10.50.15.51:7352/pol/HistoryPollutionIn?cityName=".$name."&startDate=".(time()-2595600)."&endDate=".time());
        $histResponse = json_decode($histResponse, true);
    }else{
        $boolLatLon = false;
        $cityLocation = file_get_contents("http://10.50.15.51:7353/cityconvert/city?cityName=Frankfurt%20am%20Main");
        $cityLocation = json_decode($cityLocation, true);
        $response2 = file_get_contents("http://10.50.15.51:7352/pol/actualPollutionIn?cityName=Frankfurt");
        $response2 = json_decode($response2, true);
        $_POST["name"] = "Frankfurt";
        $histResponse = file_get_contents("http://10.50.15.51:7352/pol/HistoryPollutionIn?cityName=Frankfur&startDate=".(time()-2595600)."&endDate=".time());
        $histResponse = json_decode($histResponse, true);
    }
?>

<!-- JavaScript zum Darstellen des Graphen "Historische Übersicht: Luftverschmutzung"-->
<!-- Der PHP Teil lädt alle Parameter in einen JavaScript-String, welcher mithilfe eines Trennzeichen aufgetrennt wird
     Die While-Schleife kümmert sich um die Darstellung der Daten und "var options" legt die wichtigsten Titel des Graphen fest-->

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      var x = "<?php 
            foreach($histResponse as $stuff){
                echo $stuff['main']['aqi']."-";} ?>";
      x = x.split("-");
      data.addColumn('number', 'Day');
      data.addColumn('number', 'Luftqualitätsindex');

    

      var y = (x.length-1);
      var xz = -y;
      var z = -y;
      while(y>0){
        data.addRows([
            [(xz-z),  parseInt(x[y])]
        ]);
        z++;
        y=y-1;
      }

      var options = {
        chart: {
          title: 'Air Quality Index (AQI)',
          subtitle: 'Last days at the current time'
        },
        axes: {
          x: {
            0: {side: 'top'}
          }
        }
      };

      var chart = new google.charts.Line(document.getElementById('topChart'));

      chart.draw(data, google.charts.Line.convertOptions(options));
    }
</script>
      
<!-- JavaScript zum Darstellen des Graphen "Historische Übersicht: Vergleich der Größen"-->
<!-- Der PHP Teil lädt alle Parameter in einen JavaScript-String, welcher mithilfe eines Trennzeichen aufgetrennt wird
     Die While-Schleife kümmert sich um die Darstellung der Daten und "var options" legt die wichtigsten Titel des Graphen fest-->

<script type="text/javascript">
      google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      var x = "<?php 
            foreach($histResponse as $stuff){
                echo $stuff['components']['pm2_5']."-";} ?>";
      x = x.split("-");
      var xs = "<?php 
            foreach($histResponse as $stuff){
                echo $stuff['components']['pm10']."-";} ?>";
      xs = xs.split("-");
      data.addColumn('number', 'Day');
      data.addColumn('number', 'Feinstaub 2.5 µm');
      data.addColumn('number', 'Feinstaub 10 µm');

    

      var y = (x.length-1);
      var xz = -y;
      var z = -y;
      while(y>0){
        data.addRows([
            [(xz-z),  parseInt(x[y]), parseInt(xs[y])]
        ]);
        z++;
        y=y-1;
      }


      var options = {
        chart: {
          title: 'Feinstaub Vergleich',
          subtitle: 'Last days at the current time'
        },
        axes: {
          x: {
            0: {side: 'top'}
          }
        }
      };

      var chart = new google.charts.Line(document.getElementById('bottomChart'));

      chart.draw(data, google.charts.Line.convertOptions(options));
    }
</script>

<!-- Dieses JavaScript-Skript kümmert sich um die korrekte Angabe des Standortes in der Google-Map -->

<script type="text/javascript">
    var daten ="<?php if($boolLatLon==true){
            echo $_POST['lat'].",".$_POST['lon'];
        }else {
            echo $cityLocation['coord']['lat'].",".$cityLocation['coord']['lon'];
        }?>";
    daten = daten.split(',');
</script>

<!-- Initialisierung der Google-Map -->

<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: parseFloat(daten[0]), lng: parseFloat(daten[1]) },
            zoom: 10,
        });
    }
</script>

<!-- Zugriff auf Google-Maps-Api via API-Key -->

<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA9KB5Yj_WtX_YfFZmChCd6emgxzb68s1s&callback=initMap">
</script>

<html lang="en">
<head>
    <title>3KoKb - Air Pollution Control</title>
    <link rel="icon" type="image/png" href="Icon/icon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/kokb.css" important>
    <meta name="description" content="This project is for the subject Web-Services and is a website for checking air pollution.">
    <meta name="keywords" content="Web-Service, Website, DHBW Mosbach, DHBW, Students, Project">
    <meta name="author" content="Maximilian Hausknecht, Maik Blümel, Justin Unger">
    <meta charset="UTF-8">
    <style>
        #map { 
            width: 100%;
            height: 17.25rem;
        }

        #topChart { 
            width: 90%;
            height: 26rem;
            margin-top: 1rem;
            margin-left: auto;
            margin-right: auto;
        }

        #bottomChart { 
            width: 90%;
            height: 26rem;
            margin-top: 1rem;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <header class="head"><img class="img" src="css/Pictures/Logo.png"></header>
    <div class="row">
        <div class="col col-lg-3 col-md-6 col-sm-12">
            <!-- aside ist der Container für "Suche nach Standort", "Mögliche Städte" und die Google Map -->
            <aside>
                <div class="container">
                    <!-- Formular für die Eingabe des Städtenamen, des Längen-, sowie des Breitengrades -->
                    <div class="heading text-white text-center h5">
                        <p class="font">Suche nach Standort</p>
                    </div>
                    <div class="data" id="posCity">
                        <form action="index.php" method="POST" accept-charset="UTF-8" target="_SELF" class="inputLogin" id="inputForm">
                            <input class="inputCity h5" id="city" type="text" name="name" placeholder="Städtename*" required autofocus>
                            <input class="inputCity h5" step="0.0001" id="lat" type="number" name="lat" placeholder="Breitengrad (empf.)"> 
                            <input class="inputCity h5" step="0.0001" id="lon" type="number" name="lon" placeholder="Längengrad (empf.)" > 
                            <button class="inputCity h4" id="submit" type="submit" name="submit">Bestätigen</button>
                        </form>
                    </div>
                </div>
                <div id="mid" class="container">
                    <!-- Formular für die Schnellauswahl der möglichen Städte -->
                    <div class="heading text-white text-center h5">
                        <p class="font">Mögliche Städte</p>
                    </div>
                    <div class="data" id="cityForm">
                        <form action="index.php" method="POST" accept-charset="UTF-8" target="_SELF" class="cityForm" id="inputForm">
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Bad Mergentheim">Bad Mergentheim</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Berlin">Berlin</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Braunschweig">Braunschweig</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Bremen">Bremen</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Dortmund">Dortmund</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Dresden">Dresden</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Essen">Essen</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Frankfurt%20Am%20Main">Frankfurt</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Hamburg">Hamburg</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Hannover">Hannover</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Kiel">Kiel</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Leipzig">Leipzig</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Magdeburg">Magdeburg</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Mannheim">Mannheim</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Mosbach">Mosbach</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Rostock">Rostock</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Stuttgart">Stuttgart</button>
                            <button class="inputCity h5" id="submit2" type="submit" name="name" value="Wolfsburg">Wolfsburg</button>
                        </form>
                    </div>
                </div>
                <div id="last" class="container">
                    <!-- Darstellung von Google Maps -->
                    <div class="heading text-white text-center h5">
                        <p class="font">Google Maps</p>
                    </div>
                    <div class="data" id="map">
                    </div>
                </div>
            </aside>
        </div>

        
        <div class="col col-lg-3 col-md-6 col-sm-12">
            <nav>
            <!-- Der nav-container navigiert durch die wichtigsten Parameter und stellt diese dar -->
                <div class="accordion">
                    <div class="heading text-white text-center h5">
                        <p class="font">Parameter</p>
                    </div>
                    <div class="box">
                        <div class="header h5">
                            Luftqualitätsindex (Air Quality Index - AQI)
                        </div>
                        <div class="content">
                            <p>
                                Der Luftqualitätsindex gibt Auskunft über die Schädlichkeit der aktuellen Luftverschmutzung.
                                <br><br>
                                <span class="span" style="color:red">Der AQI ist ein Stufenmodell mit folgenden Stufen: </span>   
                                <br>
                                <span class="span" style="color:green">1 : Gut</span>  
                                <br>
                                <span class="span" style="color:rgba(218, 214, 34, 0.925);">2 : Befriedigend</span>  
                                <br>
                                <span class="span" style="color:orange">3 : Mäßig</span>  
                                <br>
                                <span class="span" style="color:red">4 : Schlecht</span>  
                                <br>
                                <span class="span" style="color:purple">5 : Sehr schlecht</span>  
                                <br>
                            </p>
                            <!-- Ausgabe des aktuellen Städtenamens -->
                            <p class="border">
                                Der aktuelle Wert in <?php if($boolLatLon == false) {echo $_POST["name"];} else {echo $cityName;} ?> beträgt:
                                <?php echo $response2[0]["main"]["aqi"];?>
                            </p>
                            <p>
                                <a href="https://aqicn.org/scale/de/">Genauere Informationen finden Sie hier.</a>
                            </p>
                        </div>
                    </div>
                    <div class="box">     
                        <div class="header h5">
                            Amoniak
                        </div>
                        <div class="content">
                            <p>
                                Ammoniak ist ein Gas und reaktiv in Zussamenhang mit anderen Luftschadstoffen.
                                Hauptverantwortlich ist die Landwirtschaft.
                                <br><br>
                                Ammoniak kann Böden versauern und ist schädlich für Pflanzen.
                            </p>
                            <p class="border">
                            <!-- Ausgabe des aktuellen Städtenamens -->
                                Der aktuelle Wert in <?php if($boolLatLon == false) {echo $_POST["name"];} else {echo $cityName;} ?> beträgt:
                                <?php echo $response2[0]["components"]["nh3"];?> µg/m³
                            </p>
                            <p>
                                <a href="https://www.umweltbundesamt.de/themen/luft/luftschadstoffe-im-ueberblick/ammoniak#emittenten-quellen-fur-ammoniak-in-der-landwirtschaft">Genauere Informationen finden Sie hier.</a>
                            </p>
                        </div>
                    </div>
                    <div class="box">
                        <div class="header h5">
                            Kohlenstoffmonoxid
                        </div>
                        <div class="content">
                            <p>
                                Bei Kohlenstoffmonoxid handelt es sich um ein giftiges Gas, welches farb- und geruchslos ist.
                                Kohlenstoffmonoxid, bzw. Kohlenmonoxid entsteht durch die unzureichende Sauerstoffzufuhr bei Verbrennungen und ist dadurch
                                unter anderem im Straßenverkehr zu finden. Schuld daran ist unter anderem die unvollständige Verbrennung von Brenn- bzw. Teibstoffen.
                                <br><br>
                                <span style="color:red">Da Kohlenstoffmonoxid nicht ganz ungefährlich ist, darf der Grenzwert von 10 mg/m³ im 8-Stunden-Mittelwert nicht überschritten werden. </span>                      
                            </p>
                            <p class="border">
                            <!-- Ausgabe des aktuellen Städtenamens -->
                                Der aktuelle Wert in <?php if($boolLatLon == false) {echo $_POST["name"];} else {echo $cityName;} ?> beträgt:
                                <?php echo $response2[0]["components"]["co"];?> µg/m³
                            </p>
                            <p>
                                <a href="https://www.umweltbundesamt.de/themen/luft/luftschadstoffe-im-ueberblick/kohlenmonoxid">Genauere Informationen finden Sie hier.</a>
                            </p>
                        </div>
                    </div>
                    <div class="box">
                        <div class="header h5">
                            Ozon
                        </div>
                        <div class="content">
                            <p>
                                Ozon oder auch "aktivierter Sauerstoff" kommt natürlich in der Umwelt durch z.B. Blitzeinschläge oder an Wasserfällen vor. Auch für 
                                den bekannten "Regengeruch" ist Ozon verantwortlich.
                                <br>
                                Jedoch ist Ozon auch das zweitstärkste Desinfektionsmittel der Welt und ca. dreitausendmal stärker als Chlor im Töten von Bakterien.
                                <br><br>
                                In der Ozon-Schicht entsteht Ozon durch die ultraviolette Strahlung der Sonne und schützt uns auch vor dieser UV-Strahlung.
                                Am Boden ist Ozon giftig und kann in geringer Konzentration Kopf- und Halsschmerzen verursachen, sowie zu Entzündungen der Augen und Nase führen.
                                In größerer Konzentration werden vor allem die Atemwege und die Lunge angegriffen.
                                Aktuell besteht der Verdacht, dass Ozon krebseregend sei.
                                <br><br>
                                <span style="color:red">Die aktuellen Grenzwerte liegen bei:</span>
                                <br>
                                120 µg/m3 - 8-Stunden-Grenzwert       
                                <br>
                                180 µg/m3 - 1-Stunden-Informationsschwelle.
                                <br>
                                240 µg/m3 - 1-Stunden-Alarmschwelle.
                            </p>
                            <p class="border">
                            <!-- Ausgabe des aktuellen Städtenamens -->
                                Der aktuelle Wert in <?php if($boolLatLon == false) {echo $_POST["name"];} else {echo $cityName;} ?> beträgt:
                                <?php echo $response2[0]["components"]["o3"];?> µg/m³
                            </p>
                            <p>
                                <a href="https://www.umweltbundesamt.de/themen/luft/luftschadstoffe-im-ueberblick/ozon">Genauere Informationen finden Sie hier.</a>
                            </p>
                        </div>
                    </div>
                    <div class="box">
                        <div class="header h5">
                            Stickstoffmonoxid
                        </div>
                        <div class="content">
                            <p>
                                Stickstoffmonoxid entsteht bei dem Zuführen von Hitze und ist ein farb- und geruchsloses Gas. Es zerfällt meist bei langsamen Abkühlen in die Bestandteile Sauerstoff und Stickstoff.
                                Bei schnellem Abkühlen (wie beispielsweise in einem Motor) zerfällt nur ein kleiner Teil der Stickstoffmonoxid-Moleküle.
                                Stickoxide (wie Stickstoffmonoxid und Stickstoffdioxid) tragen in der Atmosphäre zur Bildung von Ozon bei und sind somit schädlich für das Klima.
                                Auch können diese Oxide den ph-Wert des Wassers verändern, da Stickoxide mit Wasser zu Säuren reagieren.
                                (Dies ist problematisch bei Flüssen und Seen)
                                <br><br>
                                Stickstoffmonoxid reagiert in der Regel sehr schnell zu Stickstoffdioxid, weshalb es hier keine Grenzwerte gibt.
                            </p>
                            <p class="border">
                            <!-- Ausgabe des aktuellen Städtenamens -->
                                Der aktuelle Wert in <?php if($boolLatLon == false) {echo $_POST["name"];} else {echo $cityName;} ?> beträgt:
                                <?php echo $response2[0]["components"]["no"]; ?> µg/m³
                            </p>
                        </div>
                    </div>
                    <div class="box">
                        <div class="header h5">
                            Stickstoffdioxid
                        </div>
                        <div class="content">
                            <p>
                                Stickstoffdioxid entsteht meist durch eine Reaktion von Stickstoffmonoxid.
                                Stickstoffdioxid ist pflanzenschädigend und mit für die Überdüngung und Versauerung von Böden verantwortlich.
                                Wie Stickstoffmonoxid, reagiert auch Stickstoffdioxid in Verbindung mit Wasser zu einer Säure und kann Seen und andere Gewässer übersäuern.
                                <br><br>
                                Eine zu hohe Belastung der Umwelt mit Stickstoffdioxid ist aufgrund der Bronchienvergenden Wirkung ein Problem für Asthmatiker.
                                <br><br>
                                <span style="color:red">Die aktuellen Grenzwerte liegen bei:</span>
                                <br>
                                200 µg/m3 - 1-Stunden-Grenzwert - Darf maximal 18x pro Kalenderjahr überschritten werden.
                                <br>
                                40 µg/m3 - Jahresgrenzwert
                                <br>
                                30 µg/m3 - Jahresmittelwert
                            </p>
                            <p class="border">
                            <!-- Ausgabe des aktuellen Städtenamens -->
                                Der aktuelle Wert in <?php if($boolLatLon == false) {echo $_POST["name"];} else {echo $cityName;} ?> beträgt:
                                <?php echo $response2[0]["components"]["no2"];?> µg/m³
                            </p>
                            <p>
                                <a href="https://www.umweltbundesamt.de/themen/luft/luftschadstoffe-im-ueberblick/stickstoffoxide">Genauere Informationen finden Sie hier.</a>
                            </p>
                        </div>
                    </div>
                    <div class="box">
                        <div class="header h5">
                            Schwefeldioxid
                        </div>
                        <div class="content">
                            <p>
                                Schwefeldioxid ist ein farbloses, stechend riechendes Gas.
                                Es entsteht hauptsächlich bei der Verbrennung von Kohle und Öl und ist pflanzenschädigend.
                                Wie Stickstoffoxide, kann Schwefeldioxid Böden und Gewässer versauern.
                                <br><br>
                                Gesundheitlich sorgt Schwefeldioxid für gereizte Augen und Atemwegsprobleme.
                                Aktuell werden in Deutschland die Grenzwerte weitestgehend eingehalten und gesundheitliche Probleme sind nicht zu befürchten.
                                <br><br>
                                <span style="color:red">Die aktuellen Grenzwerte liegen bei:</span>
                                <br>
                                350 µg/m3 - 1-Stunden-Grenzwert - darf maximal 24x im Jahr überschritten werden
                                <br>
                                125 µg/m3 - 1-Tages-Grenzwert - darf maximal dreimal im Jahr überschritten werden.
                                <br>
                                20 µg/m3 - Jahresmittelwert.
                            </p>
                            <p class="border">
                            <!-- Ausgabe des aktuellen Städtenamens -->
                                Der aktuelle Wert in <?php if($boolLatLon == false) {echo $_POST["name"];} else {echo $cityName;} ?> beträgt:
                                <?php echo $response2[0]["components"]["so2"];?> µg/m³
                            </p>
                            <p>
                                <a href="https://www.umweltbundesamt.de/themen/luft/luftschadstoffe-im-ueberblick/schwefeldioxid">Genauere Informationen finden Sie hier.</a>
                            </p>
                        </div>
                    </div>
                    <div class="box">
                        <div class="header h5">
                            Feinstaub 2.5 µm
                        </div>
                        <div class="content">
                            <p>
                                Feinstaub der Größe &#060 2.5 µm ist aufgrund der geringen Größe stark gesundheitsschädlich und dringt tief in die Atemwege ein.
                                Der Feinstaub wird hauptsächlich durch Haushalte und den Straßenverkehr produziert.
                                Feinstaub besteht unter anderem aus Schwefeldioxid-, Stickstoffdioxid- und Amonikapartikeln.
                                <br><br>
                                <span style="color:red">Die aktuellen Grenzwerte liegen bei:</span>
                                <br>
                                20 µg/m3 - Jahresmittelwert
                            </p>
                            <p class="border">
                            <!-- Ausgabe des aktuellen Städtenamens -->
                                Der aktuelle Wert in <?php if($boolLatLon == false) {echo $_POST["name"];} else {echo $cityName;} ?> beträgt:
                                <?php echo $response2[0]["components"]["pm2_5"];?> µg/m³
                            </p>
                            <p>
                                <a href="https://www.umweltbundesamt.de/daten/luft/luftschadstoff-emissionen-in-deutschland/emission-von-feinstaub-der-partikelgroesse-pm25">Genauere Informationen finden Sie hier.</a>
                            </p>
                        </div>
                    </div>
                    <div class="box">
                        <div class="header h5">
                            Feinstaub 10 µm
                        </div>
                        <div class="content">
                            <p>
                                Feinstaub der Größe &#060 10 µm entsteht hauptsächlich in der Produktion.
                                Als zweitgrößter Produzent von Feinstaub in dieser Größe ist der Straßenverkehr.
                                <br><br>
                                <span style="color:red">Die aktuellen Grenzwerte liegen bei:</span>
                                <br>
                                50 µg/m3 - Tagesmittelwert bei maximal 35 Überschreitungen im Jahr
                                <br>
                                40 µg/m3 - Jahresmittelwert
                            </p>
                            <p class="border">
                            <!-- Ausgabe des aktuellen Städtenamens -->
                                Der aktuelle Wert in <?php if($boolLatLon == false) {echo $_POST["name"];} else {echo $cityName;} ?> beträgt:
                                <?php echo $response2[0]["components"]["pm10"];?> µg/m³
                            </p>
                            <p>
                                <a href="https://www.umweltbundesamt.de/daten/luft/luftschadstoff-emissionen-in-deutschland/emission-von-feinstaub-der-partikelgroesse-pm25">Genauere Informationen finden Sie hier.</a>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Skript zum Aufklappen der Informationen -->
                <script>
                    const accordion = document.getElementsByClassName('box');
                    for(i=0; i<accordion.length; i++){
                        accordion[i].addEventListener('click', function(){
                            this.classList.toggle('active')
                        })
                    }
                </script>
            </nav>
        </div>

        <div class="col col-lg-6 col-md-12 col-sm-12">
            <section class="diagramm">
            <!-- Der Bereich "section" enthält die beiden Diagramme -->
                <div class="heading text-white text-center h5">
                    <p class="font">Historische Übersicht: Luftverschmutzung</p>
                </div>
                <div>
                    <div id="topChart"></div>
                </div>
            </section>
            <section class="diagramm" id="diagramm2">
                <div class="heading text-white text-center h5">
                    <p class="font">Historische Übersicht: Vergleich der Größen</p>
                </div>
                <div>
                    <div id="bottomChart"></div>
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col col-12">
            <footer class="footer">
            <!-- Im Footer sind die wichtigsten Daten (Kontaktdaten, Copyright und Impressum) angegeben -->
                <p class="footer2 text-center" style="color: white">Kontaktinformationen <br> max.hausknecht.19@lehre.mosbach.dhbw.de <br> mai.bluemel.19@lehre.mosbach.dhbw.de <br> jus.unger.19@lehre.mosbach.dhbw.de</p>
                <p class="footer2 text-center" style="color: white">3KoKb <br>Copyright © 2021<br>(Maximilian Hausknecht, Maik Blümel, Justin Unger) <br> Alle Rechte vorbehalten</p>
                <p class="footer2 text-center" style="color: white"><a href="html/Impressum.php" target="_blank">Impressum</p>
            </footer>
        </div>
    </div>
</body>
</html>