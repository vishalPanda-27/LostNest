<?php include("header.php"); ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Nest - Check Items</title>
    <link rel="stylesheet" href="style.css"> <!-- common CSS -->
</head>
<body>
<div class="form-container">
    <h2>Check Items</h2>
    <form id="checkItemForm" enctype="multipart/form-data">

        <div class="form-row">
            <input type="text" name="item_name" placeholder="Item Name" required>
            <input type="text" name="category" placeholder="Category" required>
        </div>

        <h3>Upload Photos</h3>
        <div class="photo-upload">
            <label class="photo-box">
                Add photo
                <input type="file" name="photos[]" accept="image/*" style="display:none">
            </label>
            <label class="photo-box">
                Add photo
                <input type="file" name="photos[]" accept="image/*" style="display:none">
            </label>
            <label class="photo-box">
                Add photo
                <input type="file" name="photos[]" accept="image/*" style="display:none">
            </label>
        </div>

        <button type="button" class="submit-btn" onclick="checkItem()">Check</button>
    </form>

    <div id="results" style="margin-top: 25px;"></div>
</div>
</body>

<script>
function checkItem() {
    let data = new FormData($("#checkItemForm")[0]);
    data.append("action", "checkItem");

    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: data,
        contentType: false,
        processData: false,
        success: function(response) {
            let obj = JSON.parse(response);
            if (obj.success) {
                let html = `<h3>Similar Products:</h3><div class="photo-upload">`;
                if (obj.items.length > 0) {
                    obj.items.forEach(item => {
                        html += `<div class="photo-box" style="padding:10px;">
                                    <a href="lost-item-details.php?id=${item.id}">
                                        <img src="uploads/${item.photo}" style="width:100%; height:150px; object-fit:cover; border-radius:6px;" />
                                    </a>
                                </div>`;
                    });
                } else {
                    html += `<p class="empty-note">No result found. This product hasn't been reported lost here.</p>`;
                }
                html += "</div>";
                $("#results").html(html);
            } else {
                $("#results").html(`<div class="alert alert-danger">${obj.message}</div>`);
            }
        }
    });
}
</script>
<?php include("footer.php"); ?>
