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
                <?php if(!empty($navbar_items)): ?>
                    <?php foreach($navbar_items as $navbar_item): ?>
                        <?=$navbar_item //WARNING this is not being escaped! Only trusted input!?>
                    <?php endforeach ?>
                <?php endif ?>
                <?php if(!(isset($no_logout)) || !($no_logout)):?>
                    <?=$this->fetch("main::navbar_links_li", ["nav_items" => [
                        ["Ausloggen", "logout.php", "box-arrow-right"]
                    ]])?>
                <?php endif?>
            </ul>
        </div>
    </div>
</nav>