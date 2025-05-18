<?php include('../config.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <title>Stock Page</title>
</head>

<body>
    <div class="box1 m-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Stock</button>
    </div>

    <div>
        <h2 id="main_header" class="text-center">Stock Page</h2>
        <div class="container mt-2">
            <table class="table table-hover table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Selling Price</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM `stock`";
                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        die("Query failed: " . mysqli_error($conn));
                    } else {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['unitprice']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo $row['sellprice']; ?></td>
                                <td><?php echo $row['total']; ?></td>
                                <td>
                                    <a href="crud.php?name=<?php echo urlencode($row['name']); ?>" class="btn btn-success">Update</a>
                                    <a href="crud.php?delete=<?php echo urlencode($row['name']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>

            <!-- Feedback Messages -->
            <?php
            if (isset($_GET['message'])) {
                echo "<div class='alert alert-info'>" . htmlspecialchars($_GET['message']) . "</div>";
            }
            if (isset($_GET['update_msg'])) {
                echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['update_msg']) . "</div>";
            }
            if (isset($_GET['delete_msg'])) {
                echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['delete_msg']) . "</div>";
            }
            ?>
        </div>
    </div>

    <!-- Modal Form -->
    <form action="crud.php" method="post">
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="name"> Item Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="unitprice">Unit Price</label>
                            <input type="number" step="0.01" name="unitprice" id="unitprice" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="sellprice">Selling Price</label>
                            <input type="number" step="0.01" name="sellprice" id="sellprice" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="total">Total</label>
                            <input type="number" step="0.01" name="total" id="total" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning" name="add_stock" value="add">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- JavaScript for Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for Total Calculation -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const unitPriceInput = document.getElementById('unitprice');
            const quantityInput = document.getElementById('quantity');
            const totalInput = document.getElementById('total');

            function updateTotal() {
                const unitPrice = parseFloat(unitPriceInput.value);
                const quantity = parseFloat(quantityInput.value);

                if (!isNaN(unitPrice) && !isNaN(quantity)) {
                    totalInput.value = (unitPrice * quantity).toFixed(2);
                } else {
                    totalInput.value = '';
                }
            }

            unitPriceInput.addEventListener('input', updateTotal);
            quantityInput.addEventListener('input', updateTotal);
        });
    </script>
</body>

</html>
