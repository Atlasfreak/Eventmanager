function delete_init(replacement_identifier, endpoint, parameter) {
    $("button.delete").click(function () {
        let id = $(this).data("id");
        let replacement = $(this).data("replacement");
        let modal_store = $("div#modalStore");
        let regex = new RegExp(`{${replacement_identifier}}`, "gm");

        modal_store.html($("#confirmDeleteTemplate").clone().attr("id", "confirmDelete"));
        let modal = $("#confirmDelete");
        let modal_btn_selector = "#confirmDelete .modal-footer > button";

        modal.html(modal.html().replace(regex, replacement));
        $(modal_btn_selector).click(function () {
            let data = {};
            data[parameter] = id;
            $.get(endpoint, data).done(function (data) {
                location.reload();
            });
        });
        modal = new bootstrap.Modal(document.getElementById("confirmDelete"));
        modal.toggle();
    });
}