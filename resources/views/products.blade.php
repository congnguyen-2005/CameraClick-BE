<!DOCTYPE html>
<html>
<head>
    <title>Danh sách sản phẩm</title>
</head>
<body>

<h2>Dữ liệu JSON trả về từ API</h2>

<pre id="json"></pre>

<script>
    fetch("http://127.0.0.1:8000/api/products")
        .then(res => res.json())
        .then(data => {
            document.getElementById('json').innerText = JSON.stringify(data, null, 4);
        })
        .catch(err => console.error(err));
</script>

</body>
</html>
