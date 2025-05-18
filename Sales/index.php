<?php include('../config.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <style>
        @media print {
            body {
                width: 48mm;
                font-family: monospace;
                font-size: 10px;
                margin: 0;
                padding: 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                text-align: left;
                padding: 2px 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="m-3 no-print">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Sales</button>
    </div>

    <div class="container mt-2">
        <h2 class="text-center">Sales Page</h2>
        <table class="table table-hover table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM `sales`";
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo "<tr><td colspan='5'>Query failed: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . number_format($row['price'], 2) . "</td>
                            <td>" . htmlspecialchars($row['quantity']) . "</td>
                            <td>" . number_format($row['total'], 2) . "</td>
                            <td>
                                <a href='crud.php?name=" . urlencode($row['name']) . "' class='btn btn-success'>Update</a>
                                <a href='crud.php?delete=" . urlencode($row['name']) . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-info"><?= htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['update_msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['update_msg']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['delete_msg'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['delete_msg']); ?></div>
        <?php endif; ?>
    </div>

    <form id="salesForm" method="post">
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Sales</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="clearModal()" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group mb-2">
                                    <label for="name">Item Name</label>
                                    <input type="text" id="name" class="form-control" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="price">Price</label>
                                    <input type="number" id="price" class="form-control" step="0.01" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" id="quantity" class="form-control" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="total">Total</label>
                                    <input type="number" id="total" class="form-control" step="0.01" readonly>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" onclick="clearInputs()">Clear</button>
                                    <button type="button" class="btn btn-info" onclick="addItem()">Add</button>
                                </div>
                            </div>

                            <div class="col-md-7">
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-primary" onclick="printSalesTable()">Print Sales</button>
                                </div>
                                <div id="salesTableContainer">
                                    <table class="table table-bordered table-striped" id="tempSalesTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Item</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="submitSales()">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="clearModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const name = document.getElementById('name');
        const price = document.getElementById('price');
        const quantity = document.getElementById('quantity');
        const total = document.getElementById('total');
        const tableBody = document.querySelector('#tempSalesTable tbody');
        let salesItems = [];

        function updateTotal() {
            const p = parseFloat(price.value);
            const q = parseFloat(quantity.value);
            total.value = (!isNaN(p) && !isNaN(q)) ? (p * q).toFixed(2) : '';
        }

        price.addEventListener('input', updateTotal);
        quantity.addEventListener('input', updateTotal);

        function addItem() {
            if (!name.value || !price.value || !quantity.value || !total.value) {
                alert("Please fill out all fields.");
                return;
            }

            const item = {
                name: name.value.trim(),
                price: parseFloat(price.value),
                quantity: parseInt(quantity.value),
                total: parseFloat(total.value)
            };

            salesItems.push(item);
            renderTable();
            clearInputs();
        }

        function renderTable() {
            tableBody.innerHTML = '';
            salesItems.forEach(item => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.price.toFixed(2)}</td>
                        <td>${item.quantity}</td>
                        <td>${item.total.toFixed(2)}</td>
                    </tr>`;
            });
        }

        function clearInputs() {
            name.value = '';
            price.value = '';
            quantity.value = '';
            total.value = '';
        }

        function clearModal() {
            clearInputs();
            salesItems = [];
            renderTable();
        }

        function submitSales() {
            if (salesItems.length === 0) {
                alert("Add at least one item before saving.");
                return;
            }

            fetch('crud.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ salesItems })
            })
            .then(response => response.text())
            .then(data => {
                alert("Sales saved successfully!");
                clearModal();
                location.reload();
            })
            .catch(err => {
                console.error(err);
                alert("Error saving sales.");
            });
        }

    

    </script>
</body>
</html>
