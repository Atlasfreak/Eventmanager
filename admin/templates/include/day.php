<div class="row gx-2">
    <input type="hidden" name="<?=$this->e($extra_field ?? null)?>" value="true">
    <input type="hidden" name="day_id" value="<?=$this->e($id)?>">
    <label for="day_date_<?=$this->e($id)?>" class="col-auto col-form-label">Datum</label>
    <div class="col-auto">
        <input type="date" class="form-control" id="day_date_<?=$this->e($id)?>" name="day_date_<?=$this->e($id)?>"
            value="<?=$this->e($date ?? null)?>">
        <div class="invalid-feedback"></div>
    </div>
</div>