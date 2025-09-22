function handleStatusChange(
    buttonSelector,
    routeTemplate,
    tableId = "#yajra_table",
    idAttribute
) {
    $(document).on("click", buttonSelector, function (event) {
        event.preventDefault();

        let id;

        id = $(this).attr(idAttribute);

        if (!id) {
            console.error("Could not find ID attribute for status change");
            return;
        }

        const url = routeTemplate.replace(":id", id);
        const $btn = $(this);

        $.ajax({
            url: url,
            type: "GET",
            beforeSend: function () {
                $btn.prop("disabled", true);
            },
            success: function (response) {
                if (response.status === true) {
                    $(".tostar_success").text(response.message).show();
                    $(tableId).DataTable().ajax.reload();

                    setTimeout(() => {
                        $(".tostar_success").hide();
                    }, 5000);
                } else {
                    $(".tostar_error").text(response.message).show();

                    setTimeout(() => {
                        $(".tostar_error").hide();
                    }, 5000);
                }
            },
            error: function (xhr) {
                $(".tostar_error")
                    .text(xhr.responseJSON?.message || "Unexpected error")
                    .show();
                setTimeout(() => {
                    $(".tostar_error").hide();
                }, 5000);
            },
            complete: function () {
                $btn.prop("disabled", false);
            },
        });
    });
}
