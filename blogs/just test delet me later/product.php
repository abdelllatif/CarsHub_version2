<?php 
require_once 'search.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta title="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Produits</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">Recherche Produits</h1>

        <div class="mb-4 flex gap-4">
        <div class="relative">
            <!-- Input recherche -->
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Rechercher un produit..." 
                class="w-full p-3 border border-gray-300 rounded-lg"
            />
            <!-- قائمة الاقتراحات -->
            <div id="suggestions" class="absolute bg-white border border-gray-300 w-full rounded-lg mt-1 hidden">
                <!-- اقتراحات البحث ستظهر هنا -->
            </div>
        </div>
            <!-- تصفية حسب الاسم -->
            <div>
                <label for="titleFilter" class="block text-sm font-medium text-gray-700">تصفية بالاسم:</label>
                <select id="titleFilter" class="w-full p-2 border border-gray-300 rounded-lg">
                    <option value="">جميع الأسماء</option>
                    <?php foreach ($titles as $title): ?>
                        <option value="<?= htmlspecialchars($title['title']) ?>">
                            <?= htmlspecialchars($title['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="priceFilter" class="block text-sm font-medium text-gray-700">تصفية بالسعر:</label>
                <select id="priceFilter" class="w-full p-2 border border-gray-300 rounded-lg">
                    <option value="">tags </option>
                    <?php foreach ($priceRanges as $range): ?>
                        <option value="<?= htmlspecialchars($range['price_range']) ?>">
                            <?= htmlspecialchars($range['price_range']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- نتائج المنتجات -->
        <div id="productCards" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6"></div>
    </div>

    <script>
        $(document).ready(function () {
            $(document).ready(function () {
            // عرض جميع البطاقات عند تحميل الصفحة
            $.ajax({
                url: 'search.php',
                method: 'POST',
                data: { query: '' },
                success: function (data) {
                    $('#productCards').html(data);
                }
            });

            // البحث مع الاقتراحات
            $('#searchInput').on('input', function () {
                const query = $(this).val();
                if (query.length > 0) {
                    $.ajax({
                        url: 'suggestions.php',
                        method: 'POST',
                        data: { query: query },
                        success: function (data) {
                            $('#suggestions').html(data).removeClass('hidden');
                        }
                    });
                } else {
                    $('#suggestions').addClass('hidden');
                }

                // تحديث بطاقات المنتجات بناءً على البحث
                $.ajax({
                    url: 'search.php',
                    method: 'POST',
                    data: { query: query },
                    success: function (data) {
                        $('#productCards').html(data);
                    }
                });
            });

            // اختيار اقتراح
            $(document).on('click', '.suggestion-item', function () {
                const selectedText = $(this).text();
                $('#searchInput').val(selectedText);
                $('#suggestions').addClass('hidden');

                // تحديث البطاقات
                $.ajax({
                    url: 'search.php',
                    method: 'POST',
                    data: { query: selectedText },
                    success: function (data) {
                        $('#productCards').html(data);
                    }
                });
            });
        });
            // تحديث المنتجات بناءً على التصفية
            function updateProducts() {
                const title = $('#titleFilter').val();
                const price = $('#priceFilter').val();

                $.ajax({
                    url: 'search.php',
                    method: 'POST',
                    data: { title: title, price: price },
                    success: function (data) {
                        $('#productCards').html(data);
                    }
                });
            }

            // الاستماع لتغييرات الفلاتر
            $('#titleFilter, #priceFilter').on('change', function () {
                updateProducts();
            });

            // عرض جميع المنتجات عند تحميل الصفحة
            updateProducts();
        });
    </script>
</body>
</html>
