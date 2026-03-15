let table;

function get_fakultas(dt){
    var val = $(dt).val();

    $.ajax({
        url: BASE_URL + 'kader/get-fakultas',
        type: 'get',
        data: { id: val },
        success: function(res){
            $('#fakultas1').html(res);
            $('#prodi1').html('<option value="">-- Pilih Program Studi --</option>');
        }
    });
}

function get_prodi(dt){
    var val = $(dt).val();

    $.ajax({
        url: BASE_URL + 'kader/get-prodi',
        type: 'get',
        data: { fakultas: val },
        success: function(res){
            $('#prodi1').html(res);
        }
    });
}

$(() => {
    $('#fakultas1').select2({
        width: '100%',
    });

    $('#prodi1').select2({
        width: '100%',
    });

    $('#kampus1').select2({
        width: '100%',
    });


    $('#fakultasFilter').select2({
        width: '100%',
    });


    $('#prodiFilter').select2({
        width: '100%',
    });


    $('#kampusFilter').select2({
        width: '100%',
    });


    $('#perkaderan').select2({
        width: '100%',
    });

    $('#perkaderanKhusus').select2({
        width: '100%',
    });

    $('#table-data').on('click', '.btn-delete', function () {
        let data = table.row($(this).closest('tr')).data();

        let { id, nama_lengkap } = data;

        Swal.fire({
            title: 'Anda yakin?',
            html: `Anda akan menghapus kader "<b>${nama_lengkap}</b>"!`,
            footer: 'Data yang sudah dihapus tidak bisa dikembalikan kembali!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(BASE_URL + 'kader/delete', {
                    id,
                    _method: 'DELETE'
                }).done((res) => {
                    showSuccessToastr('sukses', 'Pengguna berhasil dihapus');
                    table.ajax.reload();
                }).fail((res) => {
                    let { status, responseJSON } = res;
                    showErrorToastr('oops', responseJSON.message);
                })
            }
        })
    })

    $('#table-data').on('click', '.btn-update', function () {
        var tr = $(this).closest('tr');
        var data = table.row(tr).data();
        var id_ubah = data.id;

        load_edit_form(id_ubah);
        $('#id_kategori').val(data.id_kategori).trigger('change');
    })

    function load_edit_form(id_ubah) {

        var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: BASE_URL + 'kader/show_edit_form',
            type: 'get',
            data: {
                id_ubah: id_ubah,
            },
            success: function(response) {
                window.location.href = BASE_URL + 'kader/show_edit_form?id_ubah=' + id_ubah;

            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    $('#form-kader').on('submit', function (e) {
        e.preventDefault();

        var data = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,

            beforeSend: function () {
                clearErrorMessage();
                $('#modal-kader').find('.modal-dialog').LoadingOverlay('show');
            },

            success: function (res) {
                $('#modal-kader').find('.modal-dialog').LoadingOverlay('hide', true);

                if (res.status === true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.msg ?? 'Data berhasil disimpan',
                        confirmButtonText: 'OK'
                    }).then(function () {
                        window.history.back();
                    });
                }
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.msg ?? 'Terjadi kesalahan',
                    });
                }
            },

            error: function (xhr) {
                $('#modal-kader').find('.modal-dialog').LoadingOverlay('hide', true);

                if (xhr.status == 422) {
                    generateErrorMessage(xhr.responseJSON);
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON?.msg ?? 'Server error'
                });
            }
        });
    });

    $(document).ready(function() {
        $(document).on('click', '.repeater-add-btn-pendidikan', function() {
            var $clone = $('.repeater-pendidikan').first().clone();
            $clone.find('input').val('');
            $clone.find('input, label');
            $clone.find('.repeater-remove-btn-pendidikan').css('margin-top', '47%').removeClass('d-none');
            $clone.find('.repeater-add-btn-pendidikan').remove();
            $('.container-pendidikan').append($clone);
        });

        $(document).on('click', '.repeater-remove-btn-pendidikan', function() {
            $(this).closest('.repeater-pendidikan').remove();
        });

        $(document).on('click', '.repeater-add-btn-terakhir', function() {
            var $clone = $('.repeater-terakhir').first().clone();
            $clone.find('input').val('');
            $clone.find('input, label');
            $clone.find('.repeater-remove-btn-terakhir').css('margin-top', '47%').removeClass('d-none');
            $clone.find('.repeater-add-btn-terakhir').remove();
            $('.container-terakhir').append($clone);
        });

        $(document).on('click', '.repeater-remove-btn-terakhir', function() {
            $(this).closest('.repeater-terakhir').remove();
        });

        $(document).on('click', '.repeater-add-btn-posisi', function() {
            var $clone = $('.repeater-posisi').first().clone();
            $clone.find('input').val('');
            $clone.find('input, label');
            $clone.find('.repeater-remove-btn-posisi').css('margin-top', '37%').removeClass('d-none');
            $clone.find('.repeater-add-btn-posisi').remove();
            $('.container-posisi').append($clone);
        });

        $(document).on('click', '.repeater-remove-btn-posisi', function() {
            $(this).closest('.repeater-posisi').remove();
        });

        $(document).on('click', '.repeater-add-btn-lainnya', function() {
            var $clone = $('.repeater-lainnya').first().clone();
            $clone.find('input').val('');
            $clone.find('input, label');
            $clone.find('.repeater-remove-btn-lainnya').css('margin-top', '16%').removeClass('d-none');
            $clone.find('.repeater-add-btn-lainnya').remove();
            $('.container-lainnya').append($clone);
        });

        $(document).on('click', '.repeater-remove-btn-lainnya', function() {
            $(this).closest('.repeater-lainnya').remove();
        });

        $(document).on('click', '.repeater-add-btn-perkaderan', function() {
            var $clone = $('.repeater-perkaderan').first().clone();
            $clone.find('input').val('');
            $clone.find('input, label');
            $clone.find('.repeater-remove-btn-perkaderan').css('margin-top', '38%').removeClass('d-none');
            $clone.find('.repeater-add-btn-perkaderan').remove();
            $('.container-perkaderan').append($clone);
        });

        $(document).on('click', '.repeater-remove-btn-perkaderan', function() {
            $(this).closest('.repeater-perkaderan').remove();
        });

        $(document).on('click', '.repeater-add-btn-pimpinan', function() {
            var $clone = $('.repeater-pimpinan').first().clone();
            $clone.find('input').val('');
            $clone.find('input, label');
            $clone.find('.repeater-remove-btn-pimpinan').css('margin-top', '38%').removeClass('d-none');
            $clone.find('.repeater-add-btn-pimpinan').remove();
            $('.container-pimpinan').append($clone);
        });

        $(document).on('click', '.repeater-remove-btn-pimpinan', function() {
            $(this).closest('.repeater-pimpinan').remove();
        });
    });

    $('.btn-tambah').on('click', function () {

        $('#form-kader')[0].reset();
        clearErrorMessage();
        $('#modal-kader').modal('show');
    });

    table = $('#table-data').DataTable({
        language: dtLang,
            serverSide: false,
            processing: true,
        ajax: {
            url: BASE_URL + 'kader/data',
            type: 'get',
            dataType: 'json',
            data: function(d){
                d.fakultas = $('#fakultasFilter').val();
                d.prodi = $('#prodiFilter').val();
                d.kampus = $('#kampusFilter').val();
                d.perkaderan = $('#perkaderan').val();
                d.perkaderanKhusus = $('#perkaderanKhusus').val();
            }
        },
        order: [[0, 'asc']],
        columnDefs: [{
            targets: [0, 6],
            orderable: false,
            searchable: false,
            className: 'text-center align-top'
        }, {
            targets: [1, 2, 3, 4, 5],
            className: 'text-left align-top'
        }, {
            targets: [7],
            visible: false,
        }],
        columns: [{
            data: 'DT_RowIndex'
        }, {
            data: null,
            render: function(data, type, row) {
                return "Nama Lengkap : " + data.nama_lengkap + "<br>" + "Tanggal Lahir : " + data.tanggal_lahir + "<br>" + "Tempat Lahir : " + data.tempat_lahir+ "<br>" + "No.Hp : " + data.no_hp+ "<br>" + "Email : " + data.email;
            }
        }, {
            data: null,
            render: function(data, type, row) {
                return "Pimpinan Cabang : " + (data.pc ?? '-') + "<br>" + "Pimpinan Komisariat : " + (data.ref_komisariat?.[0]?.komisariat ?? '-') + "<br>" + "Universitas : " + (data.ref_universitas?.[0]?.kampus ?? '-') + "<br>" + "Fakultas : " + (data.ref_fakultas?.[0]?.fakultas ?? '-') + "<br>" + "Program Studi : " + (data.ref_prodi?.[0]?.prodi ?? '-');
            }
        },{
            data: null,
            render: function(data, type, row) {
                var perkaderanList = data.ref_perkaderan.map(function(item) {
                    return "&#8226; " + item.kegiatan_perkaderan + ' (' + item.tahun_perkaderan + ')';
                }).join('<br>');
                return perkaderanList;
            }
        }, {
            data: null,
            render: function(data, type, row) {
                var pimpinanList = data.ref_pimpinan.map(function(item) {
                    return "&#8226; " + item.kegiatan_pimpinan + ' (' + item.tahun_pimpinan + ')';
                }).join('<br>');
                return pimpinanList;
            }
        }, {
            data: 'foto',
            render: function(data, type, row) {
                if (data) {
                    return '<img src="storage/' + data + '" alt="Image" width="100" height="100">';
                } else {
                    return 'No Image';
                }
            }
        },  {
            data: 'id',
            render: (data, type, row) => {
                const button_edit = $('<button>', {
                    class: 'btn btn-primary btn-update',
                    html: '<i class="bx bx-pencil"></i>',
                    'data-id': data,
                    title: 'Update Data',
                    'data-placement': 'top',
                    'data-toggle': 'tooltip'
                });

                const button_delete = $('<button>', {
                    type: 'button',
                    class: 'btn btn-danger btn-delete',
                    html: '<i class="bx bx-trash"></i>',
                    'data-id': data,
                    title: 'Delete Data',
                    'data-placement': 'top',
                    'data-toggle': 'tooltip'
                });

                return $('<div>', {
                    class: 'btn-group',
                    html: () => {
                        let arr = [];

                        if (permissions.update) {
                            arr.push(button_edit)
                        }
                        if (permissions.delete) arr.push(button_delete)

                        return arr;
                    }
                }).prop('outerHTML');
            }
        }, {
            data: 'created_at'
        }]
    })


    //filter

    $('.btn-filter').on('click', function(e){
        e.preventDefault();
        table.ajax.url(BASE_URL + 'kader/filter').load();
    });

    $('.btn-reset').on('click', function () {
        $('#fakultasFilter').val('').trigger('change');
        $('#prodiFilter').val('').trigger('change');
        $('#kampusFilter').val('').trigger('change');
        $('#perkaderan').val('').trigger('change');
        $('#perkaderanKhusus').val('').trigger('change');
        table.ajax.url(BASE_URL + 'kader/data').load();
    });
})
