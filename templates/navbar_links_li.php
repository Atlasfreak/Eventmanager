<?php
    // $nav_items Array von 2 Dimensionalen Arrays der Form ["Link Bezeichnung", "Link Adresse"]
?>

<?php foreach($nav_items as $nav_item):?>
    <li class="nav-item">
        <a href="<?=$this->e($nav_item[1])?>" class="nav-link"><?=$this->e($nav_item[0])?></a>
    </li>
<?php endforeach?>