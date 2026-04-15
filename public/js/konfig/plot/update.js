// $(document).on("click", ".btnEdit", function () {
//     let id = $(this).data("id");
//     let periode = $(this).data("periode");
//     let hadiah = $(this).data("hadiah");

//     $("#edit_id").val(id);
//     $("#edit_periode_id").val(periode);
//     $("#edit_hadiah_id").val(hadiah);

//     $("#modalEditPlot").modal("show");
// });

$(document).on("click", ".btnEdit", function () {
    let id = $(this).data("id");
    let periode = $(this).data("periode");
    let hadiah = $(this).data("hadiah");

    // set value ke input
    $("#edit_id").val(id);
    $("#edit_periode_id").val(periode);
    $("#edit_hadiah_id").val(hadiah);

    // atur action URL form
    $("#formEditPlot").attr("action", "/plot/" + id + "/update");

    $("#modalEditPlot").modal("show");
});

// $("#formEditPlot").on("submit", function (e) {
//     e.preventDefault();

//     let id = $("#edit_id").val();

//     $.ajax({
//         url: "/plot/" + id,
//         type: "PUT",
//         data: {
//             periode_survey_id: $("#edit_periode_id").val(),
//             hadiah_id: $("#edit_hadiah_id").val(),
//             _token: $('meta[name="csrf-token"]').attr("content"),
//         },
//         success: function (res) {
//             if (res.status) {
//                 $("#modalEditPlot").modal("hide");
//                 location.reload();
//             }
//         },
//     });
// });

// $(document).on("click", ".btnEdit", function () {
//     let id = $(this).data("id");

//     $.ajax({
//         url: "/plot/" + id + "/edit",
//         type: "GET",
//         success: function (res) {
//             let data = res.data;

//             $("#edit_id").val(data.id);
//             $("#edit_periode_id").val(data.periode_survey_id);
//             $("#edit_hadiah_id").val(data.hadiah_id);
//             $("#edit_outlet_id").val(data.master_outlet_survey_id);

//             $("#edit_provinsi").val(data.provinsi?.id);
//             $("#edit_kabupaten").val(data.kabupaten?.id);
//             $("#edit_area").val(data.area?.id);

//             $("#modalEditArea").modal("show");
//         },
//     });
// });

// $(".btnEdit").on("click", function () {
//     let id = $(this).data("id");

//     $.ajax({
//         url: `/plot/${id}/edit`,
//         method: "GET",
//         success: function (res) {
//             // Jika controller kamu return langsung object, pakai res
//             // Jika pakai { status:true, data: {} } maka pakai res.data

//             let data = res.data ?? res;

//             $("#edit_id").val(data.id);
//             $("#edit_nama_area").val(data.nama_area);
//             $("#edit_outlet_id").val(data.outlet_id);

//             $("#modalEditArea").modal("show");
//         },
//         error: function (xhr) {
//             console.error(xhr.responseText);
//         },
//     });
// });
