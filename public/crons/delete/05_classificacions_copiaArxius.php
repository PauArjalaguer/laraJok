<?php
header("Access-Control-Allow-Origin: *");

?>
<html>

<head>
    <meta http-equiv="refresh" content="3000">
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
</head>

<body style='background-color:#fff'>
    <div id="tab_modal_contenido_competicion"></div>
</body>

</html>
<script>
    function checkDate(fecapaDate) {
        const now = new Date();
        now.setHours(now.getHours() - 21);

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


        console.log("-------------------------------------------");
        console.log(fecapaDate + " " + date1);
        console.log(actualDate + " " + date2);

        if (date1 > date2) {
            return true;
        } else {
            return false;
        }
    }

    function parseLeague(idLeague) {
        var ruta_files = 'http://www.server2.sidgad.es/';
        var idc = idLeague;
        var cliente = "fecapa";
        var site_lang = "ca";
        var tipo_stats = "";
        var file = "fecapa_clasif_idc_" + idLeague + "_1.php";
        console.log("--->" + idLeague);

        https: //www.server2.sidgad.es/fecapa/fecapa_cal_idc_3056_1.php

            //http://www.server2.sidgad.es/fecapa/fecapa_clasif_idc_2223_1.php
            var path = ruta_files + cliente + '/' + file;

        var path = 'https://www.server2.sidgad.es/fecapa/fecapa_clasif_idc_' + idLeague + '_1.php';
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
                                "type": "classification",
                                "idc": idc,
                                "html": response
                            }

                        });
                    }
                }
            }
        );
    }

    //

    var leagues = [];
    <?php
    include("cnx/c.php");
    $result = $mysqli->query("select idLeague from leagues where idSeason=37  order by lastupdated desc, idLeague desc ");
    while ($row = mysqli_fetch_array($result)) {
        echo "leagues.push(" . $row['idLeague'] . ");";
    }

    ?>
    leagues.forEach((league) => {
       // parseLeague(league);
    })
    setTimeout(() => window.location.replace("06_classificacions_parseja.php"), 1000);
</script>