<?php
include('../config.php');

// Initialize sales variable
$sales = null;

// ================== CREATE ==================
if (isset($_POST['add_sales'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $total = floatval($_POST['total']);

    if (empty($name) || $price <= 0 || $quantity <= 0 || $total <= 0) {
        header('Location: index.php?error=invalid_input');
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO `sales` (`name`, `price`, `quantity`, `total`) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sddi", $name, $price, $quantity, $total);
        if ($stmt->execute()) {
            header('Location: index.php?success=sales_added');
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
    $stmt = $conn->prepare("SELECT * FROM `sales` WHERE `name` = ?");
    if ($stmt) {
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $sales = $row;
        }
        $stmt->close();
    }
}

// ================== UPDATE ==================
if (isset($_POST['update_sales'])) {
    if (isset($_GET['name']) && !empty($_GET['name'])) {
        $original_name = $_GET['name'];

        $name = trim($_POST['name']);
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);
        $total = floatval($_POST['total']);

        if (empty($name) || $price <= 0 || $quantity <= 0 || $total <= 0) {
            header("Location: index.php?error=invalid_input");
            exit();
        }

        $stmt = $conn->prepare("UPDATE `sales` SET `name` = ?, `price` = ?, `quantity` = ?, `total` = ? WHERE `name` = ?");
        if ($stmt) {
            $stmt->bind_param("sddis", $name, $price, $quantity, $total, $original_name);
            if ($stmt->execute()) {
                header('Location: index.php?success=sales_updated');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['salesItems'])) {
        foreach ($input['salesItems'] as $item) {
            $name = mysqli_real_escape_string($conn, $item['name']);
            $price = floatval($item['price']);
            $qty = intval($item['quantity']);
            $total = floatval($item['total']);

            $query = "INSERT INTO sales (name, price, quantity, total) VALUES ('$name', $price, $qty, $total)";
            mysqli_query($conn, $query);
        }
        echo "Inserted successfully!";
        exit;
    }
}

// ================== DELETE ==================
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $name = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM `sales` WHERE `name` = ?");
    if ($stmt) {
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            header('Location: index.php?success=sales_deleted');
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

<!-- Inline Update Form (if sales selected) -->
<?php if ($sales): ?>
    <form action="crud.php?name=<?php echo urlencode($sales['name']); ?>" method="post" class="container mt-4">
        <h4>Update Sales</h4>
        <div class="form-group mb-2">
            <label for="name">Item Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($sales['name']); ?>" required>
        </div>
        <div class="form-group mb-2">
            <label for="price">Price</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($sales['price']); ?>" required>
        </div>
        <div class="form-group mb-2">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" value="<?php echo htmlspecialchars($sales['quantity']); ?>" required>
        </div>
        <div class="form-group mb-2">
            <label for="total">Total</label>
            <input type="number" id="total" name="total" class="form-control" step="0.01" readonly value="<?php echo htmlspecialchars($sales['total']); ?>">
        </div>
        <button type="submit" class="btn btn-success" name="update_sales">Update</button>
    </form>
<?php elseif (isset($_GET['name'])): ?>
    <p class="text-danger text-center mt-3">Sales item not found.</p>
<?php endif; ?>

<!-- Auto calculate total -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const priceInput = document.getElementById('price');
        const quantityInput = document.getElementById('quantity');
        const totalInput = document.getElementById('total');

        function updateTotal() {
            const price = parseFloat(priceInput.value);
            const quantity = parseFloat(quantityInput.value);

            if (!isNaN(price) && !isNaN(quantity)) {
                totalInput.value = (price * quantity).toFixed(2);
            } else {
                totalInput.value = '';
            }
        }

        priceInput.addEventListener('input', updateTotal);
        quantityInput.addEventListener('input', updateTotal);

    });

</script>