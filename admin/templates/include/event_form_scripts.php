<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="../js/flatpickr.min.js"></script>
<script src="../js/flatpickr.de.min.js"></script>
<script src="js/editors.js"></script>
<script src="js/quill_tooltips.js"></script>
<script>
    $(function(){
        flatpickr("#datetime-div", {
            enableTime: true,
            altInput: true,
            time_24hr: true,
            wrap: true,
            locale: "de",
            <?php if (!empty($minDate)): ?>
                minDate: "today",
            <?php endif ?>
            dateFormat: "Y-m-dTH:i",
            altFormat: "D j. F Y H:i",
        });
        let editors = [[create_editor("description_editor"), "description_editor"], [create_editor("email_template_editor"), "email_template_editor"]];
        showTooltips();
        $("#event_form").submit(function (e) {
            editors.forEach(element => {submit_editor(element[0], element[1], e)});
        });
    });
</script>
