<?=$this->layout("admin::layout", ["title" => "Admin-".$title, "no_logout" => isset($no_logout) ? $no_logout:false])?>

<div id="messages">
    <?php if(isset($messages) and $messages): ?>
        <?php foreach($messages as $message): ?>
            <?=$this->insert("main::alert", ["type" => $message["type"], "msg" => $message["msg"]])?>
        <?php endforeach ?>
    <?php endif ?>
</div>

<h1 class="pt-3"><?=$this->e($title)?></h1>
<?=$this->section("description")?>
<hr>

<?=$this->section("content")?>