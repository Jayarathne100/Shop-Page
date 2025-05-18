<?php include('../config.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <title>Product Page</title>
</head>

<body>
    <div class="box1 m-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Products</button>
    </div>

    <div>
        <h2 id="main_header" class="text-center">Product Page</h2>
        <div class="container mt-2">
            <table class="table table-hover table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Selling Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM `product`";
                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        die("query failed" . mysqli_error($conn));
                    } else {
                        while ($row = mysqli_fetch_assoc($result)) {

                    ?>
                            <tr>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['sellprice']; ?></td>
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
            <?php
            if (isset($_GET['message'])) {
                echo "<div class='alert alert-info'>" . htmlspecialchars($_GET['message']) . "</div>";
            }
            ?>
            <?php
            if (isset($_GET['message'])) {
                echo "<div class='alert alert-info'>" . htmlspecialchars($_GET['update_msg']) . "</div>";
            }
            if (isset($_GET['delete_msg'])) {
                echo "<div class='alert alert-info'>" . htmlspecialchars($_GET['delete_msg']) . "</div>";
            }
            ?>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>

    <!-- Modal Form -->
    <form action="crud.php" method="post">
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">නිෂ්පාදන විස්තර</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="name">Item Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="category">Category</label>
                            <input type="text" name="category" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="sellprice">Selling Price</label>
                            <input type="number" name="sellprice" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning" name="add_products" value="add">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</body>

</html>