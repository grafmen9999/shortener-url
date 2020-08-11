function getShortCode(url, data, method) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url,
            method,
            data,
            processData: false,
            contentType: false,
            success: function(data) {
                resolve(data);
            },
            error: function(error) {
                reject(error);
            }
        });
    });
}

function removeAllChildren(element) {
    $.each($(element).children(), function(index, value) {
        $(value).remove();
    });
}

$(document).ready(function() {
    $("#form button").click(function(event) {
        event.preventDefault();

        const form = $("#form");
        const responseDiv = $("#response");

        let data = new FormData();
        $.each($(form).find("input"), function(index, value) {
            let name = $(value).attr("name");
            let val = $(value).val();
            data.append(name, val);
        });

        const url = $(form).attr("ajax-action");
        const method = $(form).attr("ajax-method");

        getShortCode(url, data, method)
            .then(data => {
                removeAllChildren(responseDiv);
                $(responseDiv).append(
                    `<span class="text-success">You're code: <a target="_blank" href="/url/${data.short_code}">/url/${data.short_code}</a></span>`
                );
            })
            .catch(({ responseJSON }) => {
                console.log(responseJSON);
                removeAllChildren(responseDiv);
                $(responseDiv).append(
                    `<span class="text-danger">${responseJSON.message}</span>`
                );
            });
    });
});
