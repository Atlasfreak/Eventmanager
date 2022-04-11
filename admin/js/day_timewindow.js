function display_error(response, event, form) {
    if (response.status == 400) {
        event.stopPropagation();
        let errors = response.responseJSON["errors"];
        Object.entries(errors).forEach(([input_name, error_msg]) => {
            let input = $(form).find("input").filter(function () {
                let regex = new RegExp(`${input_name}_\\w*|^${input_name}$`);
                return this.name.match(regex);
            });
            input.next("div.invalid-feedback").html(error_msg);
            input.get(0).setCustomValidity(error_msg);
            $(form).change(function () {
                $(form).removeClass("was-validated");
                input.get(0).setCustomValidity("");
            });
        });
        $(form).addClass("was-validated");
    }
}

$("#days_timewindows").on("submit", ".add", function (event) {
    event.preventDefault();
    let data = $(this).serialize();
    let empty_id = `#empty_${$(this).data("empty")}`;
    let target_id = `#${$(this).data("target")}`;
    let form = this;
    let request = $.post(form.action, data);
    request.done(function (data) {
        let new_form_parent = $(form).data("parent") !== undefined ? $(form).data("parent") : "";
        let empty_form = $(`${empty_id} > *`).clone().appendTo(target_id);
        let target_html = empty_form.html();
        empty_form.find(`${new_form_parent} input`).each(function () {
            const regex = /^([a-zA-Z0-9]+_\w+)_(?:[a-zA-Z0-9]|{\w*})*$|^([a-zA-Z0-9]+_\w+)$/;
            let match = regex.exec(this.name);
            let replacement;
            let needle;
            if (match !== null) {
                if (match[1] !== undefined) {
                    needle = `{${match[1]}}`;
                    replacement = $(form).find("input").filter(function () {
                        return this.name.match(new RegExp(`${match[1]}_\\w`));
                    }).val();
                } else if (match[2] !== undefined) {
                    needle = `{${match[2]}}`;
                    replacement = data[match[2]];
                }
                target_html = target_html.replace(new RegExp(needle, "g"), replacement);
            }
        });
        empty_form.html(target_html);
    });
    request.fail(function (response) {
        display_error(response, event, form);
    });
});

$("#days").on("submit", ".delete", function (event) {
    event.preventDefault();
    let data = $(this).serialize();
    $.post(this.action, data);
    let parent_id = $(this).data("parent");
    $(this).parentsUntil(`#${parent_id}`).remove();
});

let timeout = null;

$("#days").on("input", ".update", function (event) {
    clearTimeout(timeout);
    let form = this;
    timeout = setTimeout(function () {
        event.preventDefault();
        let data = $(form).serialize();
        $.post(form.action, data).fail(function (response) {
            display_error(response, event, form);
        });
    }, 650);
});