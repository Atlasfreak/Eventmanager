<?=$this->layout("main::layout", ["title" => $title, "path" => ""])?>
<h1>Aktuelle Veranstaltungen</h1>
<p>
    Zurzeit gibt es
    <b>
    <?php if($count_events === 0): ?>
        keine
    <?php elseif($count_events === 1): ?>
        eine
    <?php else: ?>
        <?=$this->e($count_events)?>
    <?php endif ?>
    </b>
    Veranstaltung<?php if($count_events > 1): ?>en<?php endif ?>.
</p>
<?php if($messages): ?>
    <?php foreach($messages as $message): ?>
        <?=$this->insert("main::alert", ["type" => $message["type"], "msg" => $message["msg"]])?>
    <?php endforeach ?>
<?php endif ?>
<table class="table">
    <?=$this->insert("main::table_head", ["ueberschriften" => ["Veranstaltung", "Beschreibung", "Anmeldeschluss", "Links"]])?>
    <tbody>
        <?php foreach($events as $event): ?>
            <tr>
                <td><?=$this->e($event["titel"])?></td>
                <td>
                    <a
                    class="text-toggle"
                    href="#description_<?=$this->e($event["id"])?>"
                    data-toggle="collapse"
                    aria-expanded="false"
                    aria-controls="description_<?=$this->e($event["id"])?>"
                    >
                        <span class="text-collapsed">ausklappen <i class="bi bi-chevron-down"></i></span>
                        <span class="text-expanded">einklappen <i class="bi bi-chevron-up"></i></span>
                    </a>
                    <div class="collapse" id="description_<?=$this->e($event["id"])?>">
                        <?=parse_delta($event["beschreibung"])?>
                    </div>
                </td>
                <td>
                    <?=$this->e(strftime("%A %d.%m.%Y %H:%M", strtotime($event["anmeldeende"])))?>
                </td>
                <td>
                    <a href="?event=<?=$this->e($event["id"])?>">Anmelden <i class="bi bi-arrow-right"></i></a>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>