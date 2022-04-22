<?=$this->layout("main::layout", ["path" => "../", "title" => $title])?>

<?=$this->start("header")?>
    <nav class="navbar navbar-expand-sm shadow-sm navbar-light" style="background-color: #dbdbdb;">
        <div class="container-xl">
            <a href="<?=$this->e(ANMELDUNG_URL)?>/admin" class="navbar-brand">
                <?=$this->e($title)?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto">
                    <?=$this->section("navbar_items")?>
                    <?php if(!(isset($no_logout)) || !($no_logout)):?>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">Ausloggen <i class="bi bi-box-arrow-right"></i></a>
                        </li>
                    <?php endif?>
                </ul>
            </div>
        </div>
    </nav>
<?=$this->end()?>

<?=$this->start("container_classes")?>

<?=$this->end()?>

<?=$this->section("content")?>

<?=$this->start("footer_right")?>
<a href="https://github.com/Atlasfreak/Eventmanager" class="text-reset">Hilfe</a>
<?=$this->end()?>
