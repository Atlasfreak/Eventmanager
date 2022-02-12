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

function create_editor(id) {
    return new Quill(`#${id}`, quill_settings);
}

function submit_editor(editor, editor_id, event) {
    let input = $(`#${editor_id.replace("_editor", "")}`);;
    if (isQuillEmpty(editor) && input.attr("required")) {
        element = $(`#${editor_id}`);
        if (isQuillEmpty(editor) && !(element.hasClass("is-invalid"))) {
            element.addClass("is-invalid");
            element.nextAll(".invalid-feedback").append(" darf nicht leer sein.");
        }
        event.preventDefault();
    }

    input.val(JSON.stringify(editor.getContents()));
}