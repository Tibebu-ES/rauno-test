<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CSV Read DataTable test!</title>
    <meta name="description" content="CSV Read DataTable test!">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <!-- fa -->
    <script src="https://kit.fontawesome.com/4fd853b10c.js" crossorigin="anonymous"></script>

<!-- Pixel Code for https://startuplaunchtool.com/ -->
<script async src="https://startuplaunchtool.com/pixel/3wrbzxi1xmheyfq3w6dwgr7lthurgjg3"></script>
<!-- END Pixel Code -->

</head>

<body>

    <div class="container">
        <div class="card">
            <h5 class="card-header bg-warning">CSV Read DataTable Test</h5>
            <div class="card-body">
                <table id="csv_table" class="table " data-toggle="table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col" style="text-align: right; ">Value</th>
                            <th scope="col" style="text-align: left; ">Unit</th>
                            <th scope="col">Last Seen</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rows) : ?>
                            <?php foreach ($rows as $row) : ?>

                                <tr <?php echo "id='" . $row['id'] . "'" ?>>
                                    <td><span <?php echo "class='text-" . $row['color'] . "'"; ?>><i <?php echo "class='fas " . $row['icon'] . "'"; ?>></i></span>
                                        <span id="cell-name"> <?php echo $row['name']; ?> </span>
                                    </td>
                                    <th style="text-align: right; "><?php echo $row['value']; ?></th>
                                    <td style="text-align: left; "> <span id="cell-unit"><?php echo $row['unit']; ?> </span></td>
                                    <td><?php echo $row['date_update']; ?></td>
                                    <td>
                                        <a <?php echo "data-id='" . $row['id'] . "' " . "data-name='" . $row['name'] . "' " . "data-unit='" . $row['unit'] . "' " ?> class="btn edit-data">
                                            <span class="text-primary"><i class="fas fa-pencil-alt"></i></span> </a>
                                        <a <?php echo "data-id='" . $row['id'] . "' " ?> class="btn delete-data">
                                            <span class="text-danger"><i class="fas fa-trash"></i></span> </a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php endif; ?>



                    </tbody>
                </table>
            </div>
        </div>


    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.11.3/datatables.min.css" />
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.11.3/datatables.min.js"></script>
    <!-- bootbox  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>



    <script>
        $(document).ready(function() {

            $(".btn.edit-data").click(function(data) {
                //get row id, name, and unit
                var id = $(this).attr("data-id");
                var name = $(this).attr("data-name");
                var unit = $(this).attr("data-unit");

                var dialog = bootbox.dialog({
                    title: 'Edit row',
                    message: '<form>' +
                        '<div class="mb-3">' +
                        '<label for="rowName" class="form-label">Name</label>' +
                        '<input class="form-control" id="rowName" value="' + name + '">' +

                        '</div>' +
                        '<div class="mb-3">' +
                        '<label for="rowUnit" class="form-label">Unit</label>' +
                        '<input class="form-control" id="rowUnit" value="' + unit + '">' +
                        '</div>' +
                        '</form>',
                    size: 'large',
                    buttons: {
                        save: {
                            label: "Save",
                            className: 'btn-primary',
                            callback: function() {
                                $.ajax({
                                        method: "POST",
                                        url: "<?php echo base_url() . "/RaunoTest/update" ?>",
                                        data: {
                                            id: id,
                                            name: $("#rowName").val(),
                                            unit: $("#rowUnit").val()
                                        }
                                    })
                                    .done(function(res) {
                                        var data = JSON.parse(res);
                                        console.log("Data Saved: " + data.message);
                                        //update the row view
                                        $("#" + id + " " + "#cell-name").text(data.row.name);
                                        $("#" + id + " " + "#cell-unit").text(data.row.unit);
                                        //update editor
                                        $("#" + id + " " + ".btn.edit-data").attr('data-name', data.row.name);
                                        $("#" + id + " " + ".btn.edit-data").attr('data-unit', data.row.unit);

                                    }).fail(function(e) {
                                        alert("error" + e);
                                    });

                            }
                        },
                        cancel: {
                            label: "cancel",
                            className: 'btn-secondary',
                            callback: function() {
                                console.log('Custom cancel clicked');
                            }
                        }

                    }
                });

            });

            $(".btn.delete-data").click(function(data) {
                //get row id
                var id = $(this).attr("data-id");
                var confirm = bootbox.confirm({
                    title: 'Delete row?',
                    message: '<p> Are you sure, you want to delete the row?</p>',
                    buttons: {
                        confirm: {
                            label: "confirm",
                            className: 'btn-danger',
                        },
                        cancel: {
                            label: "cancel",
                            className: 'btn-secondary',
                        }

                    },
                    callback: function(result) {
                        if (result) {
                            //ajax call -controller to delete row 
                            $.ajax({
                                    method: "DELETE",
                                    url: "<?php echo base_url() . "/RaunoTest/delete/" ?>" + id,

                                })
                                .done(function(res) {
                                    var data = JSON.parse(res);
                                    if (data.error == false) {
                                        console.log("Data deleted: " + data.message);
                                        //delete the row view
                                        $("#" + id).hide();
                                    } else {
                                        console.log(data.message);
                                    }


                                }).fail(function(e) {
                                    alert("error" + JSON.stringify(e));
                                });
                        }
                    }
                });
            });
            $('#csv_table').DataTable();
        });
    </script>
</body>

</html>
