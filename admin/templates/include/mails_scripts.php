<script src="js/select2.min.js"></script>
<script>
    $("#mail_adresses").select2({
        "theme": "bootstrap4",
        "width": $(this).data("width") ? $(this).data("width") : $(this).hasClass("w-100") ? "100%" : "style",
        "closeOnSelect": false,
        "language": "de",
    });

    let mail_editors = [[create_editor("email_editor"), "email_editor"]];
    $("mail_form").submit(function(e) {
        mail_editors.forEach(element => {submit_editor(element[0], element[1], e)});
    });
    $("#auto_mail").change(function(e) {
        if (this.checked) {
            $("#email_msg").hide();
        } else {
            $("#email_msg").show();
        }
    });
</script>