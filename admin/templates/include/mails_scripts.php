<script src="js/select2.min.js"></script>
<script>
    $("#email_addresses").select2({
        "theme": "bootstrap4",
        "width": $(this).data("width") ? $(this).data("width") : $(this).hasClass("w-100") ? "100%" : "style",
        "closeOnSelect": false,
        "language": "de",
    });

    let mail_editors = [[create_editor("email_editor"), "email_editor"]];
    $("#mail_form").submit(function(e) {
        mail_editors.forEach(element => {submit_editor(element[0], element[1], e)});
    });
    $("#auto_email").change(function(e) {
        if (this.checked) {
            $("#email_msg").hide();
            $("#email_subject").attr("required", false);
            $("#email").attr("required", false);
        } else {
            $("#email_msg").show();
            $("#email_subject").attr("required", true);
            $("#email").attr("required", true);
        }
    });
</script>