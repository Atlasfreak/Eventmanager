<?=$this->layout("main::layout", ["path" => "../", "title" => $title])?>

<?=$this->start("header")?>
    <?=$this->insert("admin_inc::navbar", ["title" => $title, "no_logout" => $no_logout ?? null, "navbar_items" => $navbar_items ?? null])?>
<?=$this->end()?>

<?=$this->start("container_classes")?>

<?=$this->end()?>

<?=$this->section("content")?>

<?=$this->start("footer_right")?>
<a href="https://github.com/Atlasfreak/Eventmanager" class="text-reset">Hilfe</a>
<?=$this->end()?>
