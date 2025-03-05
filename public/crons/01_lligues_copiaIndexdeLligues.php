<?php header("Access-Control-Allow-Origin: *"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="refresh" content="300">
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
    <title>Cron 01</title>
</head>

<body style='background-color:#fff'>
    <div id="tab_modal_contenido_competicion"></div>
</body>

</html>
<script>
    function parseLeague(idLeague) {
        var ruta_files = 'http://www.server2.sidgad.es/';
        var idc = idLeague;
        var cliente = "fecapa";
        var site_lang = "ca";
        var tipo_stats = "";
        var file = "fecapa_ls_1.php";
        var path = "http://www.server2.sidgad.es/fecapa/fecapa_ls_1.php";
        $('#tab_modal_contenido_competicion').load(path, {
                idc: 1,
                tipo_stats: tipo_stats,
                site_lang: site_lang
            },
            function(response, status, xhr) {

                if (status == 'success') {
                    $.ajax({
                        type: "POST",
                        url: "http://clubolesapati.cat/crons/receiveData.php",
                        crossDomain: true,
                        beforeSend: function(xhr) {
                            xhr.withCredentials = true;
                        },
                        data: {
                            "type":"leagues",
                            "idc": idc,
                            "html": response
                        }

                    });
                }
            }
        );
    }

    //

    var leagues = [1];

    leagues.forEach((league) => {
        parseLeague(league);
    })
    setTimeout(()=> window.location.replace("02_lligues_insertaLLiguesaDDBB.php"),15000);
</script>
