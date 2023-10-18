<!DOCTYPE html>
<html>
<head>
    <title>PHP, SOLR, AJAX</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">PHP, SOLR, AJAX</h1>
    <form id="search-form" class="form">
        <div class="form-group mb-4">
            <label for="fraza" class="sr-only">Wprowadź frazę:</label>
            <input type="text" id="fraza" name="name" class="form-control" placeholder="Wprowadź nazwę">
        </div>
        <div class="form-group mb-4">
            <label for="kategoria" class="sr-only">Wybierz kategorię:</label>
            <select class="form-control" id="kategoria" name="cat">
                <option value="" selected>Wybierz kategorie</option>
                <option value="electronics">Elektronika</option>
                <option value="graphics card">Karty graficzne</option>
                <option value="electronics and computer1">Komputery</option>
                <option value="currency">Waluty</option>
                <option value="memory">Pamięć</option>
                <option value="music">Muzyka</option>
                <option value="book">Książki</option>
                <option value="software">Oprogramowanie</option>
                <option value="printer">Drukarki</option>
            </select>
        </div>
        <div class="form-group mb-4">
            <label for="min-cena">Cena minimalna: <span id="min-cena-value">0</span></label>
            <input type="range" id="min-cena" name="min-cena" class="form-control-range" min="0" max="1000" step="1" value="0">
        </div>
        <div class="form-group mb-4">
            <label for="max-cena">Cena maksymalna: <span id="max-cena-value">750</span></label>
            <input type="range" id="max-cena" name="max-cena" class="form-control-range" min="0" max="1000" step="1" value="750">
        </div>
        <div class="form-group form-check mb-4">
            <input type="checkbox" class="form-check-input" id="inStock" name="inStock" checked>
            <label class="form-check-label" for="inStock">Produkt dostępny</label>
        </div>
        <div class="d-flex">
            <div class="form-group mb-4 mr-4">
                <label for="sort" class="sr-only">Sortowanie:</label>
                <select class="form-control" id="sort" name="sort">
                    <option value="" selected>Wybierz sortowanie</option>
                    <option value="price|asc">Od ceny najniższej</option>
                    <option value="price|desc">Od ceny najwyższej</option>
                    <option value="name|desc">Alfabetycznie</option>
                </select>
            </div>
            <div class="form-group mb-4 mr-4">
                <label for="sort" class="sr-only">Ilość rekordów:</label>
                <select class="form-control" id="rows" name="rows">
                    <option value="10" selected>Ilość rekordów</option>
                    <option value="3">3</option>
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="1000">1000</option>
                </select>
            </div>
            <div><a id="ajaxToAddDoc" class="btn btn-primary mr-4">Dodaj dokument</a></div>
            <div><a id="ajaxToDelete" class="btn btn-danger">Usuń dokumenty</a></div>
        </div>
    </form>
</div>

<div class="container my-5">
    <div id="success-alert" class="alert alert-success" style="display: none;">
        Operacja zakończona sukcesem!
    </div>
    <h1 class="mb-4">Lista wyników wyszukiwania:</h1>
    <ul id="results" class="list-group">
    </ul>
</div>

<!-- Dodaj Bootstrap JS oraz jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {

        $("#ajaxToAddDoc").click(function() {
            sendAjaxToAddDoc();
        });

        $("#ajaxToDelete").click(function() {
            if (confirm('Chcesz usunąć wszystkie elementy?')) sendAjaxToDeleteAll();
        });

        var delayTimer;
        sendAjaxRequest();

        $("#min-cena, #max-cena, #fraza, #kategoria, #inStock, #sort, #rows").on("input", function() {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(sendAjaxRequest, 200);
        });

        function sendAjaxRequest() {
            var name = $("#fraza").val();
            var cat = $("#kategoria").val();
            var minPrice = $("#min-cena").val();
            var maxPrice = $("#max-cena").val();
            var inStock = $('#inStock').is(':checked') ? 1 : 0;
            var sort = $("#sort").val();
            var rows = $("#rows").val();

            $.ajax({
                type: "POST",
                url: "search_engine.php",
                data: {
                    name: name,
                    cat: cat,
                    minPrice: minPrice,
                    maxPrice: maxPrice,
                    inStock: inStock,
                    sort: sort,
                    rows: rows,
                },
                success: function(data) {
                    $("#results").html(data);
                }
            });
        }

        function sendAjaxToAddDoc() {
            $.get('add_doc.php', function (data) {
                $("#success-alert").fadeIn().delay(2000).fadeOut();
            }).done(function (data) {
                console.log(data);
                setTimeout(sendAjaxRequest, 1000);
            });
        }

        function sendAjaxToDeleteAll() {
            $.get('delete_docs.php', function (data) {
                console.log(data);
                $("#success-alert").fadeIn().delay(2000).fadeOut();
            }).done(function () {
                $("#results").empty();
            });
        }


    });
</script>
<script>
    function sendAjaxToDeleteById(id) {
        if (confirm('Chcesz usunąć ten element '+id+'?')) {
            $.ajax({
                type: "POST",
                url: "delete_doc_by_id.php",
                data: {
                    id: id
                },
                success: function(data) {
                    $("#"+id).remove();
                }
            });
        }
    }
</script>
<script>

    function updateName(id) {
        var titleDiv = $("#div-"+id);
        var titleContent = $("#name-"+id);
        titleDiv.click(function() {
            var currentTitle = titleContent.text();
            var titleInput = $("<input type='text' class='form-control d-inline w-75' id='input-name-"+id+"' value='" + currentTitle + "'>");
            titleContent.replaceWith(titleInput);
            titleInput.focus();
            titleInput.blur(function() {
                var name = titleInput.val();
                titleDiv.html('<span id="name-'+id+'">'+name+'</span>');
                titleInput.remove();
                sendAjaxToUpdateName(id, name)
            });
        });
    }
    function sendAjaxToUpdateName(id, name) {
        $.ajax({
            type: "POST",
            url: "update_name_by_id.php",
            data: {
                id: id,
                name: name
            },
            success: function(data) {
                console.log(data);
            }
        });
    }
</script>
<script>
    $(document).ready(function() {
        $("#min-cena").on("input", function() {
            $("#min-cena-value").text($(this).val());
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#max-cena").on("input", function() {
            $("#max-cena-value").text($(this).val());
        });
    });
</script>
</body>
</html>