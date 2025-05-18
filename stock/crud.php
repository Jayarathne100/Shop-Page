<?php
include('../config.php');

// Initialize stock variable
$stock = null;

// ================== CREATE ==================
if (isset($_POST['add_stock'])) {
    $name = trim($_POST['name']);
    $unitprice = floatval($_POST['unitprice']);
    $quantity = intval($_POST['quantity']);
    $sellprice = floatval($_POST['sellprice']);
    $total = floatval($_POST['total']);

    if (empty($name) || $unitprice <= 0 || $quantity <= 0 || $sellprice <= 0 || $total <= 0) {
        header('Location: index.php?error=invalid_input');
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO `stock` (`name`, `unitprice`, `quantity`, `sellprice`, `total`) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sdddi", $name, $unitprice, $quantity, $sellprice, $total);
        if ($stmt->execute()) {
            header('Location: index.php?success=stock_added');
        } else {
            header('Location: index.php?error=add_failed');
        }
        $stmt->close();
    } else {
        header('Location: index.php?error=stmt_prepare_failed');
    }
    exit();
}

// ================== READ (for update form) ==================
if (isset($_GET['name']) && !empty($_GET['name'])) {
    $name = $_GET['name'];
    $stmt = $conn->prepare("SELECT * FROM `stock` WHERE `name` = ?");
    if ($stmt) {
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stock = $row;
        }
        $stmt->close();
    }
}

// ================== UPDATE ==================
if (isset($_POST['update_stock'])) {
    if (isset($_GET['name']) && !empty($_GET['name'])) {
        $original_name = $_GET['name'];

        $name = trim($_POST['name']);
        $unitprice = floatval($_POST['unitprice']);
        $quantity = intval($_POST['quantity']);
        $sellprice = floatval($_POST['sellprice']);
        $total = floatval($_POST['total']);

        if (empty($name) || $unitprice <= 0 || $quantity <= 0 || $sellprice <= 0 || $total <= 0) {
            header("Location: index.php?error=invalid_input");
            exit();
        }

        $stmt = $conn->prepare("UPDATE `stock` SET `name` = ?, `unitprice` = ?, `quantity` = ?, `sellprice` = ?, `total` = ? WHERE `name` = ?");
        if ($stmt) {
            $stmt->bind_param("sdddis", $name, $unitprice, $quantity, $sellprice, $total, $original_name);
            if ($stmt->execute()) {
                header('Location: index.php?');
            } else {
                header('Location: index.php?error=update_failed');
            }
            $stmt->close();
        } else {
            header('Location: index.php?error=stmt_prepare_failed');
        }
    } else {
        header('Location: index.php?error=missing_original_name');
    }
    exit();
}

// ================== DELETE ==================
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $name = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM `stock` WHERE `name` = ?");
    if ($stmt) {
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            header('Location: index.php?success=product_deleted');
        } else {
            header('Location: index.php?error=delete_failed');
        }
        $stmt->close();
    } else {
        header('Location: index.php?error=stmt_prepare_failed');
    }
    exit();
}

$conn->close();
?>

<!-- Inline Update Form (if stock selected) -->
<?php if ($stock): ?>
    <form action="crud.php?name=<?php echo urlencode($stock['name']); ?>" method="post" class="container mt-4">
        <h4>Update Stock</h4>
        <div class="form-group mb-2">
            <label for="name">Item Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($stock['name']); ?>" required>
        </div>
        <div class="form-group mb-2">
            <label for="unitprice">Unit Price</label>
            <input type="number" id="unitprice" name="unitprice" class="form-control" step="0.01" value="<?php echo htmlspecialchars($stock['unitprice']); ?>" required>
        </div>
        <div class="form-group mb-2">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" value="<?php echo htmlspecialchars($stock['quantity']); ?>" required>
        </div>
        <div class="form-group mb-2">
            <label for="sellprice">Selling Price</label>
            <input type="number" id="sellprice" name="sellprice" class="form-control" step="0.01" value="<?php echo htmlspecialchars($stock['sellprice']); ?>" required>
        </div>
        <div class="form-group mb-2">
            <label for="total">Total</label>
            <input type="number" id="total" name="total" class="form-control" step="0.01" readonly value="<?php echo htmlspecialchars($stock['total']); ?>">
        </div>
        <button type="submit" class="btn btn-success" name="update_stock">Update</button>
    </form>
<?php elseif (isset($_GET['name'])): ?>
    <p class="text-danger text-center mt-3">Stock item not found.</p>
<?php endif; ?>

<!-- Auto calculate total -->
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
