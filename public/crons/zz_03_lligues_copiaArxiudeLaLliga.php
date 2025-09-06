<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="refresh" content="300">
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
    <title>Cron 3</title>
</head>

<body style='background-color:#fff'>
    <div id="tab_modal_contenido_competicion"></div>
    <div id="leagueslist">aaa</div>
</body>

</html>
<script>
    function checkDate(fecapaDate) {
        const now = new Date();
        now.setHours(now.getHours() - 12);

        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hour = String(now.getHours()).padStart(2, '0');
        const minute = String(now.getMinutes()).padStart(2, '0');
        const second = String(now.getSeconds()).padStart(2, '0');

        let actualDate = `${day}-${month}-${year} ${hour}:${minute}:${second}`;


        fD = fecapaDate.split(" ");
        fDD = fD[0].split("-");

        fecapaDate = fDD[2] + "-" + fDD[1] + "-" + fDD[0] + " " + fD[1];
        date1 = new Date(fecapaDate).getTime();


        fD = actualDate.split(" ");
        fDD = fD[0].split("-");

        aff = fDD[2] + "-" + fDD[1] + "-" + fDD[0] + " " + fD[1];
        date2 = new Date(aff).getTime();


        if (date1 > date2) {
            return true;
        } else {
            return false;
        }
    }

    function parseLeague(idLeague) {
        var ruta_files = 'https://www.server2.sidgad.es/';
        var idc = idLeague;
        var cliente = "fecapa";
        var site_lang = "ca";
        var tipo_stats = "";
        var file = "fecapa_cal_idc_" + idc + "_1.php";
        document.getElementById("leagueslist").innerHTML += "<br />" + file;
        var path = ruta_files + cliente + '/' + file;
        $('#tab_modal_contenido_competicion').load(path, {
                idc: idc,
                tipo_stats: tipo_stats,
                site_lang: site_lang
            },
            function(response, status, xhr) {
                if (status == 'success') {
                    const div = document.querySelectorAll('div');

                    let fecapaDate = div[div.length - 3].textContent.replace("Report Created on ", "").replace("GMT", "");

                    if (checkDate(fecapaDate)) {
                        $.ajax({
                            type: "POST",
                            url: "https://clubolesapati.cat/crons/receiveData.php",
                            crossDomain: true,
                            beforeSend: function(xhr) {
                                xhr.withCredentials = true;
                            },
                            data: {
                                "type": "league",
                                "idc": idc,
                                "html": response
                            }

                        });
                    }
                }
            }
        );
    }


    var leagues = [];
    <?php
    use cnx\c;
    $result = $mysqli->query("select idLeague from leagues where idSeason=37  order by lastupdated asc, idLeague desc limit 0,50");
    while ($row = mysqli_fetch_array($result)) {
        echo "leagues.push(" . $row['idLeague'] . ");";
    }

    ?>
    leagues.forEach((league) => {
  
    })
    setTimeout(() => window.location.replace("04_lligues_parsejaICopiaPartits.php"), 1000);
  
</script>
