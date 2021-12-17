function isQuillEmpty(quill) {
    if ((quill.getContents()["ops"] || []).length !== 1) { return false; }
    return quill.getText().trim().length === 0;
}
let quill_settings = {
    theme: "snow",
    placeholder: "...",
    formats: [
        "background",
        "color",
        "bold",
        "italic",
        "link",
        "size",
        "strike",
        "underline",
        "blockquote",
        "header",
        "list",
        "align"
    ],
    modules: {
        toolbar: {
            container: [
                [{ header: [1, 2, 3, false] }],
                ["bold", "italic", "underline", "strike"],
                ["link", "blockquote"],
                [{ "color": [] }, { "background": [] }],
                [{ "list": "ordered" }, { "list": "bullet" }],
                [{ "align": [] }],
                ["clean"],
                ["clear"],
            ],
            handlers: {
                "clear": function () {
                    this.quill.setText("", "user");
                }
            }
        }
    }
};
let description_editor = new Quill("#description_editor", quill_settings);
let email_template_editor = new Quill("#email_template_editor", quill_settings);
$("#create_event_form").submit(function (e) {
    if (isQuillEmpty(description_editor) || isQuillEmpty(email_template_editor)) {
        description_editor_el = $("#description_editor");
        if (isQuillEmpty(description_editor) && !(description_editor_el.hasClass("is-invalid"))) {
            description_editor_el.addClass("is-invalid");
            description_editor_el.nextAll(".invalid-feedback").append(" darf nicht leer sein.");
        }

        email_template_editor_el = $("#email_template_editor");
        if (isQuillEmpty(email_template_editor) && !(email_template_editor_el.hasClass("is-invalid"))) {
            email_template_editor_el.addClass("is-invalid");
            email_template_editor_el.nextAll(".invalid-feedback").append(" darf nicht leer sein.");
        }
        e.preventDefault();
    }

    let email_input = $("#email_template");
    email_input.val(JSON.stringify(email_template_editor.getContents()));
    let description_input = $("#description");
    description_input.val(JSON.stringify(description_editor.getContents()));
})