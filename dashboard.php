<?php
include 'module_booking/services/service_getAllRoomsFromDatabase.php';
?>

<html>
<head>
    <!-- stylesheet -->
    <link rel="stylesheet" href="stylesheet/style.css">

    <!-- externe library -->
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script> -->
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet"
          href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- help scripts -->
    <script type="text/javascript" src="utility/global_vars.js"></script>
    <script type="text/javascript" src="utility/help.js"></script>
    <script type="text/javascript" src="module_booking/utility/bookingModule_help.js"></script>
    <script type="text/javascript" src="module_booking/utility/sidenav_help.js"></script>

    <script type="text/javascript">

        var childCounter = 1;

        function childContent() {

            var strRoomDescription = <?php echo json_encode($strRoomDescription); ?>;

            return '<select class="selectRoom"> <option value="first">--select room--</option>'
                + strRoomDescription +
                '</select>\
                <br>\
        <input readonly="readonly" type="text" name="startdate" class="childOfDiv"\
    placeholder="Startdatum dd.mm.yyyy" onkeyup="checkData(this);">\
        <p id="error"></p>\
        <input readonly="readonly" type="text" name="enddate" class="childOfDiv"\
    placeholder="Enddatum dd.mm.yyyy" onkeyup="checkData(this);">\
        <p id="errorMsg"></p>\
        <input type="text" name="firstname" class="childOfDiv" onkeyup="checkData(this);"\
        placeholder="Vorname*"><br>\
        <input type="text" name="lastname" class="childOfDiv" onkeyup="checkData(this);"\
         placeholder="Lastname*"><br>'
        } //end of childContent

    </script>

    <!-- configuration script -->
    <script type="text/javascript"
            src="configuration/xmlHttpRequest_configurations.js"></script>

    <!-- scripts -->
    <script type="text/javascript"
            src="module_booking/services/service_tableGenerate.js"></script>
    <script type="text/javascript"
            src="module_booking/services/service_tableSelector.js"></script>
    <script type="text/javascript"
            src="module_booking/services/service_showDataFromDefinePeriod.js"></script>


    <script type="text/javascript">

        // set jquery-Datepicker
        $(function () {
            $(".datepicker").datepicker();
        });

    </script>
</head>
<body>
<div id="main">
    <div id="sidenav" >
        <div id="mySidenav" class="sidenav">
            <!--- Beginn of sidenav-->

            <a class="closebtn" onclick="closeNav()">&times;</a>

            <form id="addPerson" name="addPerson" method="post" action="">

                <div id="personAttribute">

                    <!-- This content will be automatically set -->

                </div>
            </form>

            <button type="submit" onclick="request(readData);">Submit</button>
            <button id="myBtnWeiter" onclick="CreateDivInSidenav();" disabled>add More...</button>

        </div><!-- End of sidenav-->
    </div>

    <!--- click on this element to show sidenav -->
    <div title="click to add reservation" onclick="openNav();" id="overlay">
        <span>&lsaquo;</span>
    </div>

    <!-- beginn of Dashboard-->
    <div id="divKalendar">
        <div id="kalendarNavDiv">
            <button id="leftKalendar" onclick="getAPreviousPeriodeClick()">
                <img alt="left" src="images/navLink.png">
            </button>
            <button id="RightKalendar" onclick="getANextPeriodeClick()">
                <img alt="rigth" src="images/navRecht.png">
            </button>
            <p style="clear: both;"></p>
        </div>

        <div>
            <div class="floatTable" id="roomDiv">

                <!-- this is a empty table on the top of the Object-table in order to get a good layout -->
                <table>
                    <tr>
                        <td></td>
                    </tr>
                </table>

                <!-- this is the Table with all room Information -->
                <table id="roomtable">
                    <tr>
                        <td>Objekte</td>
                    </tr>
                    <?php echo $roomTable ?>
                </table>
                <!-- this helps to save a number of all rooms, this is used to generate the booking table -->
                <input type="hidden" class=" notVisible" id="roomRowNbr"
                       value="<?php echo $RoomNum_rows ?>">
            </div>

            <!-- this is the table with all booking infomation -->
            <div class="floatTable" id="tableDiv">

            </div>
            <p style="clear: both;"></p>
        </div>
    </div>
    <!-- End of Dashboard-->
    <!-- end of content-->

    <!-- *************************scripte************************************** -->
    <script type="text/javascript">

        // TODO: please set the rigth Date before going produtive
        // the EndDate needed to generate the booking Table
        var endDateFromFirstBookingTableGeneration = new Date();
        endDateFromFirstBookingTableGeneration = dateToString(endDateFromFirstBookingTableGeneration);

        var nbrOfRoomRows = parseInt(document.getElementById("roomRowNbr").value) + 1;
        document.getElementById("tableDiv").innerHTML = generateTable(nbrOfRoomRows, 28, endDateFromFirstBookingTableGeneration);

        // after create the Table all the data for the Period will be loaded.
        loadData();
        // set date Row onlyread
        onlyRead = $(".onlyRead");
        onlyRead.attr("disabled", "disabled");
        // The table Seletor to get the Possibility to make a select on the table
        tableSelector();

        // a click Funktion to get the last forteen (actually periode) days an  update the booking table
        function getAPreviousPeriodeClick() {
            var endDateFormat = stringToDate(endDateFromFirstBookingTableGeneration);

            endDateFormat.setDate(endDateFormat.getDate() - 14);
            endDateFormat = dateToString(endDateFormat);
            endDateFromFirstBookingTableGeneration = endDateFormat;
            document.getElementById("tableDiv").innerHTML = generateTable(nbrOfRoomRows, 28, endDateFromFirstBookingTableGeneration);
            // reload all Booking Information and tableSelector
            loadData();
            tableSelector();

        }
        // a click Funktion to get the next forteen (actually periode) days an  update the booking table
        function getANextPeriodeClick() {

            var endDateFormat = stringToDate(endDateFromFirstBookingTableGeneration);
            endDateFormat.setDate(endDateFormat.getDate() + 14);
            endDateFormat = dateToString(endDateFormat);
            endDateFromFirstBookingTableGeneration = endDateFormat;
            document.getElementById("tableDiv").innerHTML = generateTable(nbrOfRoomRows, 28, endDateFromFirstBookingTableGeneration);

            // reload all Booking Information and tableSelector
            loadData();
            tableSelector();
        }


        /***************BEGINN OF EVENT ON SIDENAV***********************
         *
         * Set datepicker on choosen element
         *
         ******************************************************************************/
//
//        window.addEventListener('mouseup', function (event) {
//
//
//            var status = false;
//            var box = document.getElementById('mySidenav');
//            var boxCalendar = document.getElementById('ui-datepicker-div');
//            var nodes = [];
//            var element = event.target;
//            nodes.push(element);
//
//            while (element.parentNode) {
//                nodes.unshift(element.parentNode);
//                element = element.parentNode;
//            }
//
//            for (var i = 0; i < nodes.length; i++) {
//                if (nodes[i] == box || nodes[i] == boxCalendar) {
//                    status = true;
//                }
//            }
//            if (status == false) {
//                closeNav();
//            }
//        });


        function request(callback) {

            var xhr = getXMLHttpRequest();

            xhr.onreadystatechange = function () {

                if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {

                    callback(xhr.responseText); //recuperation de donnees sous forme textuel


                }

            };

            var oStoredData = getJsonData();

            xhr.open("POST", "module_booking/services/service_boockingDataValidator.php", true);

            xhr.setRequestHeader("Content-Type", "application/json");

            xhr.send(oStoredData);

            document.getElementById("mySidenav").style.width = "0";
            document.getElementById("sidenav").style.width = "0";

            document.getElementById("main").style.marginLeft = "0";

            document.body.style.backgroundColor = "white";

        }

        function readData(sData) {

            if (sData) {

                document.getElementById("console").innerHTML = "...." + sData;

            } else {

                alert("Y'a eu un problème");

            }

        }
    </script>

</div>
<!-- end of main-->
</body>
</html>