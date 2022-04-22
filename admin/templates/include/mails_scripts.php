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
                `<div class="border-bottom p-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                        <label class="form-check-label" for="selectAll">Alle auswählen</label>
                    </div>
                </div>`
            );

            $rendered.find(".select2-dropdown").prepend($selectAll);

            let checkbox = $selectAll.find("input");

            checkbox.change(function () {
                if (this.checked) {
                    let values = [];
                    self.find("option").each(function () {
                        values.push($(this).val());
                    });

                    self.val(values).trigger("change");
                } else {
                    self.val(null).trigger("change");
                }
                self.select2("close");
            });

            self.change(function () {
                let values_length = $(this).val().length;
                if (values_length === 0) {
                    checkbox.prop("checked", false).prop("indeterminate", false);
                } else if (values_length === $(this).find("option").length) {
                    checkbox.prop("checked", true).prop("indeterminate", false);
                } else if (values_length >= 0) {
                    checkbox.prop("checked", false).prop("indeterminate", true);
                }
            });

            return $rendered;
        };
        $("#email_addresses").select2({
            "theme": "bootstrap-5",
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