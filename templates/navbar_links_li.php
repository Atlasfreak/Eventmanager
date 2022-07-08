<?php
    // $nav_items Array von 2 Dimensionalen Arrays der Form ["Link Bezeichnung", "Link Adresse"]
?>

<?php foreach($nav_items as $nav_item):?>
    <li class="nav-item">
        <a href="<?=$this->e($nav_item[1])?>" class="nav-link">
            <?php if(isset($nav_item[2])): ?>
                <i class="bi bi-<?=$this->e($nav_item[2])?>"></i>
            <?php endif ?>
            <?=$this->e($nav_item[0])?>
        </a>
    </li>
<?php endforeach?>