
(function attachEvents() {
    $(".opt").on("click", function () {
        let data = {answer : $(this).text()};
        let url = window.location.pathname;
        let modal = $('#myModal');
        modal.css("display", "block");
        $.ajax({
            method: "POST",
            url: url,
            cache: false,
            data: data,
            success:function (ans) {
                $("#answer").text(ans);
                $("#ok").css("display","block")
            }
        })
    });
    let modal = $('#myModal');
    let span = $("#ok");
    span.on("click", function() {
        modal.css("display", "none");
        $(".loader").css("display", "block");
    });
})();
