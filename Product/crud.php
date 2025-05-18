<?php
include('../config.php');

// Initialize product variable
$product = null;

// ================== CREATE ==================
if (isset($_POST['add_products'])) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $sellprice = floatval($_POST['sellprice']);

    if (empty($name) || empty($category) || $sellprice <= 0) {
        header('Location: index.php?error=invalid_input');
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO `product` (`name`, `category`, `sellprice`) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssd", $name, $category, $sellprice);
        if ($stmt->execute()) {
            header('Location: index.php?success=product_added');
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
    $stmt = $conn->prepare("SELECT * FROM `product` WHERE `name` = ?");
    if ($stmt) {
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $product = $row;
        }
        $stmt->close();
    }
}

// ================== UPDATE ==================
if (isset($_POST['update_product'])) {
    if (isset($_GET['name']) && !empty($_GET['name'])) {
        $original_name = $_GET['name'];
        $name = trim($_POST['name']);
        $category = trim($_POST['category']);
        $sellprice = floatval($_POST['sellprice']);

        if (empty($name) || empty($category) || $sellprice <= 0) {
            header("Location: index.php?error=invalid_input");
            exit();
        }

        $stmt = $conn->prepare("UPDATE `product` SET `name` = ?, `category` = ?, `sellprice` = ? WHERE `name` = ?");
        if ($stmt) {
            $stmt->bind_param("ssds", $name, $category, $sellprice, $original_name);
            if ($stmt->execute()) {
                header('Location: index.php?success=product_updated');
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

if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $name = urldecode($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM `product` WHERE `name` = ?");
    if ($stmt) {
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Location: index.php?success=product_deleted');
            } else {
                header('Location: index.php?error=not_found');
            }
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


<?php if ($product): ?>
<form action="crud.php?name=<?php echo urlencode($product['name']); ?>" method="post">
    <div class="form-group mb-2">
        <label for="name">නම</label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
    </div>
    <div class="form-group mb-2">
        <label for="category">ප්‍රවර්ගය</label>
        <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($product['category']); ?>" required>
    </div>
    <div class="form-group mb-2">
        <label for="sellprice">විකුණුම් මිල</label>
        <input type="number" name="sellprice" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['sellprice']); ?>" required>
    </div>
    <button type="submit" class="btn btn-success" name="update_product">Update</button>
</form>
<?php elseif (isset($_GET['name'])): ?>
    <p>Product not found.</p>
<?php endif; ?>
