<?php foreach($array as $value): ?>
    <?php if(!is_null($value)): ?>
        <td class="text-break"><?=$this->e($value)?></td>
    <?php endif ?>
<?php endforeach ?>