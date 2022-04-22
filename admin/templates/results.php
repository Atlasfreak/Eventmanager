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
    <div class="ms-auto">
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
                                            <div class="d-grid gap-1">
                                                <button
                                                class="btn btn-danger btn-sm text-nowrap delete"
                                                data-id="<?=$this->e($row_participant["id"])?>"
                                                data-replacement="<?=$this->e($row_participant["vorname"])." ".$this->e($row_participant["nachname"])?>">
                                                    <i class="bi bi-trash-fill"></i> Löschen
                                                </button>
                                                <a
                                                class="btn btn-primary btn-sm text-nowrap"
                                                href="event_details.php?event_id=<?=$this->e($_GET["event"])?>&email=<?=$this->e($row_participant["id"])?>"
                                                role="button">
                                                    <i class="bi bi-envelope-fill"></i> E-Mail
                                                </a>
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

<div class="form-check" id="checkbox_template" hidden>
    <input type="checkbox" class="form-check-input"">
    <label class="form-label form-check-label" for="">Nicht belegte Zeifenster ausblenden</label>
</div>

<select id="select_template" class="form-select" hidden="hidden">
    <option value="" selected="selected">Alle Stationen</option>
</select>

<?=$this->insert("admin::delete_modal", [
    "title" => "{name} löschen?",
    "content" => "Den Teilnehmer, \"{name}\", wirklich endgültig löschen?"
    ])
?>

<?=$this->push("scripts")?>
    <script src="../js/datatables.min.js?bs=5"></script>
    <script src="../js/printThis.js"></script>
    <script src="js/delete_modal.js"></script>
    <script>
        let stations =
            <?php if(!is_null($stations) and $stations > 0): ?>
                <?=$this->e($stations)?>
            <?php else: ?>
                null
            <?php endif ?>;
        let data_tables = {};
        let data_tables_enabled = false;
        let export_options = {
            "exportOptions": {
                "columns": ":not(:last-child)",
            }
        };

        function create_data_tables() {
            data_tables = $("table").DataTable({
                "dom":
                    "<'row d-print-none'<'col-sm-12 col-md-2'B>\
                    <'#checkbox.col-sm-12 col-md-4 mb-2 mb-md-0 align-self-center'>\
                    <'#select.col-sm-12 col-md-2 mb-2 mb-md-0'>\
                    <'col-sm-12 col-md-4'f>>" +
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
                    "targets": -1,
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
                    init_controls();
                    delete_init("name", "delete_participant.php", "participant_id");
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

        function init_control(day_id, type, modify_func, event_handler, id = type) {
            let selector = `#day_${day_id}_wrapper > .row #${id}`;
            let control_id = `${id}_timewindows_${day_id}`;

            $(selector).html("");
            $(`#${id}_template`).clone().removeAttr("hidden").appendTo($(selector));

            modify_func(selector, control_id);
            $(selector).find(type).attr("id", control_id);

            $(selector).find(type).change({"day_id": day_id}, event_handler);
        }

        function init_checkbox(day_id) {
            let event_handler = (event) => {
                if (event.target.checked) {
                    data_tables.table(`#day_${event.data.day_id}`).column(0).search("^(?!\s*$).+", true, false).draw(true);
                } else {
                    data_tables.table(`#day_${event.data.day_id}`).column(0).search("").draw(true);
                }
            }
            let modify_func = (selector, id) => {
                $(selector).find("input").prop("checked", false);
                $(selector).find("label").attr("for", id);
            }
            init_control(day_id, "input", modify_func, event_handler, "checkbox");
        }

        function init_select(day_id) {
            let event_handler = (event) => {
                data_tables.table(`#day_${day_id}`).column(7).search(event.target.value).draw();
            }
            let modify_func = (selector, id) => {
                for(let i = 1; i <= stations; i++) {
                    $(selector).find("select").append(new Option(i, i));
                }
            }
            init_control(day_id, "select", modify_func, event_handler);
        }

        function init_controls() {
            $(".table-container").each(function() {
                let day_id = $(this).data("day");
                init_checkbox(day_id);
                if (stations !== null && stations > 0) {
                    init_select(day_id);
                }
            });
        }

        $(function() {
            create_data_tables();
        });
    </script>
<?=$this->end()?>