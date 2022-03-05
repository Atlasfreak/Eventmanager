<?=$this->layout("main::layout_base", ["title" => $title, "path" => ""])?>

<div class="bg-white shadow p-3 my-auto mx-auto">
    <h2>Wirklich von "<?=$this->e($event)?>" abmelden?</h2>
    <hr>
    <div class="d-flex">
        <a class="btn btn-danger" role="button" href="?<?=$this->e($_SERVER["QUERY_STRING"])?>&confirm=true">
                <i class="bi bi-x-lg"></i> Endgültig abmelden
        </a>
        <a href="." class="btn btn-outline-secondary ml-auto" role="button">
            <i class="bi bi-arrow-left"></i> Zurück zur Übersicht
        </a>
    </div>
</div>