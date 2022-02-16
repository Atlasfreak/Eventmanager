<script src="js/select2.min.js"></script>
<script>
    $.fn.select2.amd.require([
        "select2/utils",
        "select2/dropdown",
        "select2/dropdown/attachBody"
    ], function (Utils, Dropdown, AttachBody) {
        function SelectAll() { }

        SelectAll.prototype.render = function (decorated) {
            let $rendered = decorated.call(this);
            let self = $("#email_addresses");

            let $selectAll = $(
                `
                <div class="border-bottom p-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="selectAll">
                        <label class="custom-control-label" for="selectAll">Alle auswählen</label>
                    </div>
                </div>`
            );

            $rendered.find(".select2-dropdown").prepend($selectAll);

            $selectAll.find("input").change(function (e) {
                if (this.checked) {
                    let values = [];
                    self.find(":not(:selected)").each(function () {
                        values.push($(this).val());
                    });

                    self.val(values).trigger("change");
                } else {
                    self.val(null).trigger("change");
                }
                self.select2("close")
            });

            return $rendered;
        };
        $("#email_addresses").select2({
            "theme": "bootstrap4",
            "width": $(this).data("width") ? $(this).data("width") : $(this).hasClass("w-100") ? "100%" : "style",
            "closeOnSelect": false,
            "language": "de",
            "dropdownAdapter": Utils.Decorate(
                Utils.Decorate(
                    Dropdown,
                    AttachBody
                ),
                SelectAll,
            ),
        });
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