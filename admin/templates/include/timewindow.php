<div class="row">
    <input type="hidden" name="timewindow_id" value="<?=$this->e($id)?>">
    <input type="hidden" name="<?=$this->e($extra_field ?? null)?>" value="true">
    <label for="timewindow_from_<?=$this->e($id)?>_<?=$this->e($day_id)?>" class="col-xl-auto col-form-label pe-xl-0">
        Von
    </label>
    <div class="col-xl-3 w-xl-20">
        <input type="time" class="form-control" id="timewindow_from_<?=$this->e($id)?>_<?=$this->e($day_id)?>"
            name="timewindow_from_<?=$this->e($id)?>" value="<?=$this->e($from ?? null)?>">
        <div class="invalid-feedback"></div>
    </div>
    <label for="timewindow_until_<?=$this->e($id)?>_<?=$this->e($day_id)?>" class="col-xl-auto col-form-label px-xl-0">
        Bis (optional)
    </label>
    <div class="col-xl-3 w-xl-20">
        <input type="time" class="form-control" id="timewindow_until_<?=$this->e($id)?>_<?=$this->e($day_id)?>"
            name="timewindow_until_<?=$this->e($id)?>" value="<?=$this->e($until ?? null)?>">
        <div class="invalid-feedback"></div>
    </div>
    <label for="timewindow_max_participants_<?=$this->e($id)?>_<?=$this->e($day_id)?>" class="col-xl-auto col-form-label px-xl-0">
        Max. Teilnehmer
    </label>
    <div class="col-xl-2">
        <input type="number" name="timewindow_max_participants_<?=$this->e($id)?>"
            id="timewindow_max_participants_<?=$this->e($id)?>_<?=$this->e($day_id)?>" class="form-control" min="1"
            value="<?=$this->e($max_participants ?? null)?>">
        <div class="invalid-feedback"></div>
    </div>
</div>