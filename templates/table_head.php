<thead>
    <tr>
        <?php
        // ueberschriften ist ein Array aus Strings oder Arrays wobei ein Array die Form ["Überschrift", "HTML Klasse(n)"] hat.
        ?>
        <?php foreach($ueberschriften as $element): ?>
            <th scope='col'
                <?php if(is_array($element)):?>
                    class="<?=$this->e($element[1])?>"
                <?php endif?>
                >
                    <?php if(is_array($element)):?>
                        <?=$this->e($element[0])?>
                    <?php else: ?>
                        <?=$this->e($element)?>
                    <?php endif ?>
            </th>
        <?php endforeach ?>
    </tr>
</thead>