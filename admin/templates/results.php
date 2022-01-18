<?=$this->layout("admin::layout", ["title" => "Auswertung"])?>

<?=$this->push("styles")?>
    <link rel="stylesheet" href="../css/datatables.min.css">
    <style>
        @media print {
            .table td  {
                background-color: transparent !important;
                width: 10%;
            }

            .sorting::before, .sorting::after {
                display: none !important;
            }
        }
    </style>
<?=$this->end()?>

<?=$this->push("navbar_items")?>
    <?=$this->insert("main::navbar_links_li", ["nav_items" => array(
        ["Zurück zum Admin-Panel", "../admin"]
    )])?>
    <li class="nav-item">
        <a href="" class="nav-link"><i class="bi bi-arrow-repeat"></i> Seite neu laden</a>
    </li>
<?=$this->end()?>

<div class="d-print-none bg-light border my-3 d-flex sticky-top shadow" style="top: 56px;">
    <div class="ml-auto">
        <button type="button" class="btn btn-light d-print-none rounded-0" onClick="prints()">
            <i class="bi bi-printer-fill"></i> Drucken
        </button>
        <button class="btn btn-light d-print-none rounded-0" onClick="toggle_data_tables()">
            <i class="bi bi-funnel-fill"></i> Sortieren umschalten
        </button>
    </div>
</div>

<?php foreach($data_days as $day_key => $row_day): ?>
    <div class="mb-4 printThis">
        <h1 class="text-center"><?=$this->e(strftime("%A %d.%m.%Y", strtotime($row_day["tagDatum"])))?></h1>
        <p>
            <b>Anmeldungen: <?=$this->e($results[$row_day["tagID"]])?></b>
        </p>

        <div class="table-container" data-day="<?=$this->e($day_key)?>">
            <table
            id="day_<?=$this->e($day_key)?>"
            class="table table-striped table-hover"
            <?php if(array_key_last($data_days)!==$day_key):?>
                style="page-break-after: always;"
            <?php endif ?>>
                <?=$this->insert("main::table_head", ["ueberschriften" => $titles])?>
                <tbody>
                    <?php foreach($data_timewindows[$row_day["tagID"]] as $row_timewindow):?>
                        <?php foreach($data_participants[$row_timewindow["zeitfensterID"]] as $row_participant): ?>
                            <tr>
                                <?php if($row_participant): ?>
                                    <?=$this->insert("main::table_array_td", ["array" => array(
                                        $row_participant["nachname"],
                                        $row_participant["vorname"],
                                        $row_participant["strasse"],
                                        $row_participant["ort"],
                                        $row_participant["email"],
                                        $row_participant["telefon"],
                                        timewindow_string($row_timewindow["von"], $row_timewindow["bis"]),
                                        $row_participant["anmeldestation"],
                                        )])
                                    ?>
                                    <td class="d-print-none">
                                        <?php if($row_participant["id"]): ?>
                                            <div class="btn-group btn-group-sm">
                                                <button
                                                class='btn btn-warning btn-sm text-right d-print-none'
                                                name='bearbeiten'
                                                value='<?=$this->e($row_participant["id"])?>'>
                                                    Bearbeiten
                                                </button>
                                                <button
                                                class="btn btn-danger btn-sm delete"
                                                data-id="<?=$this->e($row_participant["id"])?>"
                                                data-replacement="<?=$this->e($row_participant["vorname"])." ".$this->e($row_participant["nachname"])?>"
                                                data-toggle="modal"
                                                data-target="#confirmDelete">
                                                    <i class="bi bi-trash-fill"></i> Löschen
                                                </button>
                                                <button
                                                class='btn btn-primary btn-sm text-right d-print-none'
                                                name='sendnewemail'
                                                value='<?=$this->e($row_participant["id"])?>'>
                                                    E-Mail senden
                                                </button>
                                            </div>
                                        <?php endif ?>
                                    </td>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach ?>

<div class="custom-control custom-checkbox" id="checkbox_template" hidden>
    <input type="checkbox" class="custom-control-input" id="">
    <label class="custom-control-label" for="">Nicht belegte Zeifenster ausblenden</label>
</div>

<?=$this->insert("admin::delete_modal", [
    "title" => "{name} löschen?",
    "content" => "Den Teilnehmer, \"{name}\", wirklich endgültig löschen?"
    ])
?>

<?=$this->push("scripts")?>
    <script src="../js/datatables.min.js"></script>
    <script src="../js/printThis.js"></script>
    <script src="js/delete_modal.js"></script>
    <script>
        let data_tables = {};
        let data_tables_enabled = false;
        let export_options = {
            "exportOptions": {
                "columns": [0, 1, 2, 3, 4, 5, 6],
            }
        };
        function create_data_tables() {
            data_tables = $("table").DataTable({
                "dom":
                    "<'row d-print-none'<'col-sm-12 col-md-2'B><'#checkbox.col-sm-12 col-md-4 align-self-center'><'col-sm-12 col-md-6'f>>" +
                    "tr" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "paging": false,
                "info": false,
                "order": [[0, "desc"]],
                "responsive": true,
                "processing": true,
                "columnDefs": [{
                    "orderable": false,
                    "searchable": false,
                    "targets": 7,
                }],
                "buttons": [
                    $.extend( true, {}, export_options, {
                        "extend": "csv"
                    }),
                    $.extend( true, {}, export_options, {
                        "extend": "excel"
                    }),
                ],
                "language": {
                    "url": "js/i18n/dataTables.german.json"
                },
                "initComplete": function(settings, json) {
                    init_checkboxes();
                },
            });
        }
        function destroy_data_tables() {
            if(Object.keys(data_tables).length !== 0) {
                data_tables.destroy();
                data_tables = {}
            }
        }
        function toggle_data_tables() {
            if(Object.keys(data_tables).length === 0) {
                create_data_tables();
            } else {
                destroy_data_tables();
            }
        }
        async function prints() {
            $(".printThis").printThis({
                "importStyle": true,
                "base": "<?=$_SERVER['REQUEST_URI']?>",
            });
        }
        function init_checkboxes() {
            $(".table-container").each(function() {
                let day_id = $(this).data("day");
                let checkbox_selector = `#day_${day_id}_wrapper > .row > #checkbox`;
                let checkbox_id = `filter_timewindows_${day_id}`;

                $(checkbox_selector).html("");
                $("#checkbox_template").clone().removeAttr("hidden").appendTo($(checkbox_selector));

                $(checkbox_selector).find("input").prop("checked", false).attr("id", checkbox_id);
                $(checkbox_selector).find("label").attr("for", checkbox_id);

                $(checkbox_selector).find("input").change(function() {
                    if (this.checked) {
                        data_tables.table(`#day_${day_id}`).column(0).search("^(?!\s*$).+", true, false).draw(true);
                    } else {
                        data_tables.table(`#day_${day_id}`).column(0).search("").draw(true);
                    }
                });
            });
        }
        $(function() {
            create_data_tables();
            delete_init("name", "delete_participant.php", "participant_id")
        });
    </script>
<?=$this->end()?>