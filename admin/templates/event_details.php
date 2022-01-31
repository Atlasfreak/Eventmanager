<?=$this->layout("admin::layout", ["title" => "Test Veranstaltung"])?>

<?=$this->start("styles")?>
    <?=$this->insert("admin_inc::event_form_css")?>
<?=$this->end()?>

<h1 class="mb-4">Test Veranstaltung Übersicht</h1>
<div class="row">
    <div class="col-md-3">
        <div class="nav flex-column nav-pills" id="id" role="tablist" aria-orientation="vertical">
            <a class="nav-link active" id="edit-tab" data-toggle="pill" href="#edit" role="tab" aria-controls="edit" aria-selected="true">Veranstaltung bearbeiten</a>
            <a class="nav-link" id="e-mails-tab" data-toggle="pill" href="#e-mails" role="tab" aria-controls="e-mails" aria-selected="false">E-Mails</a>
        </div>
        <div class="nav flex-column nav-pills">
            <a class="nav-link" href="results.php?event=<?=$this->e($id)?>">Teilnehmerübersicht</a>
        </div>
    </div>
    <div class="col-md-9">
        <div class="tab-content" id="tabContent">
            <div class="tab-pane fade show active" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                <form id="edit_event_form" action="edit.php" method="post">
                    <?=$this->insert("admin_inc::event_form", [
                        "title_err" => $title_err ?? false,
                        "description_err" => $description_err ?? false,
                        "email_template_err" => $email_template_err ?? false,
                        "reg_date_err" => $reg_date_err ?? false,
                        "event_date_err" => $event_date_err ?? false,
                        "event_reg_date_err" => $event_reg_date_err ?? false,
                        "title_value" => $title_value,
                        "description" => $description,
                        "email_template" => $email_template,
                        "reg_startdate_val" => $reg_startdate_val,
                        "reg_enddate_val" => $reg_enddate_val,
                        ])?>
                    <button type="submit" class="btn btn-success"><i class="bi bi-pencil-square"></i> Veranstaltung bearbeiten</button>
                </form>
            </div>
            <div class="tab-pane fade" id="e-mails" role="tabpanel" aria-labelledby="e-mails-tab">E-Mails</div>
        </div>
    </div>
</div>

<?=$this->start("scripts")?>
    <?=$this->insert("admin_inc::event_form_scripts")?>
<?=$this->end()?>