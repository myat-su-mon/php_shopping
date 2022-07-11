<?php
    session_start();
    require '../config/config.php';
    require '../config/common.php';

    if(empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
        header('Location: login.php');
    }

    if($_SESSION['role'] != 1){
        header('Location: login.php');
    }

    if($_POST){
        if (empty($_POST['name']) || empty($_POST['description']) || empty($_POST['category']) ||
            empty($_POST['quantity']) || empty($_POST['price']) || empty($_FILES['image'])) {
            if(empty($_POST['name'])){
                $nameError = "Product name is required";
            }
            if(empty($_POST['description'])){
                $descError = "Description is required";
            }
            if(empty($_POST['category'])){
                $catError = "Category name is required";
            }
            if(empty($_POST['quantity'])){
                $qtyError = "Quantity is required";
            } elseif((is_numeric($_POST['quantity'])) !=1 ) {
                $qtyError = "Quantity should be integer value";
            }
            if(empty($_POST['price'])){
                $priceError = "Price is required";
            } elseif( (is_numeric($_POST['quantity'])) !=1 ) {
                $priceError = "Price should be integer value";
            }
            if(empty($_FILES['image'])){
                $imageError = "Image is required";
            }
        } else { // validation success
            if ($_FILES['image']['name'] != null) {
                $file = 'images/'.($_FILES['image']['name']);
                $imageType = pathinfo($file, PATHINFO_EXTENSION);
    
                if ($imageType != 'jpg' && $imageType != 'jpeg' && $imageType != 'png') {
                    echo "<script>alert('Image should be jpg, jpeg, png');</script>";
                } else { // image validation success
                    $name = $_POST['name'];
                    $desc = $_POST['description'];
                    $category = $_POST['category'];
                    $quantity = $_POST['quantity'];
                    $price = $_POST['price'];
                    $image = $_FILES['image']['name'];
                    $id = $_POST['id'];
    
                    move_uploaded_file($_FILES['image']['tmp_name'], $file);

                    $stmt = $pdo->prepare("UPDATE products SET name=:name, description=:description, category_id=:category, quantity=:quantity, price=:price, image=:image WHERE id=:id");
                    $result = $stmt->execute(
                        array(':name' => $name, ':description' => $desc, ':category' => $category, ':quantity' => $quantity, ':price' => $price, ':image' => $image, ':id' => $id )
                    );
    
                    if ($result) {
                        echo "<script>alert('Product is updated'); window.location.href = 'index.php'</script>";
                    }
                }
            } else {   
                $name = $_POST['name'];            
                $desc = $_POST['description'];
                $category = $_POST['category'];
                $quantity = $_POST['quantity'];
                $price = $_POST['price'];
                $id = $_POST['id'];

                $stmt = $pdo->prepare("UPDATE products SET name=:name, description=:description, category_id=:category, quantity=:quantity, price=:price WHERE id=:id");
                $result = $stmt->execute(
                    array(':name' => $name, ':description' => $desc, ':category' => $category, ':quantity' => $quantity, ':price' => $price, ':id' => $id )
                );
    
                if ($result) {
                    echo "<script>alert('Product is updated'); window.location.href = 'index.php'</script>";
                }
            }
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=".$_GET['id']);
    $stmt->execute();
    $result = $stmt->fetchAll();
?>

<?php include('header.php') ?>
  <!-- Main content -->
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                        <input type="hidden" name="id" value="<?php echo $result[0]['id']; ?>">
                        <div class="form-group">
                            <label for="name">Name</label><p class="text-danger"><?php echo empty($nameError)? '': '*'.$nameError; ?></p>
                            <input class="form-control" type="text" name="name" value="<?php echo escape($result[0]['name']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label><p class="text-danger"><?php echo empty($descError)? '': '*'.$descError; ?></p>
                            <textarea class="form-control" name="description" cols="30" rows="5"><?php echo escape($result[0]['description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <?php
                                $catStmt = $pdo->prepare("SELECT * FROM categories");
                                $catStmt->execute();
                                $catResult = $catStmt->fetchAll();
                                ?>
                            <label for="category">Category</label><p class="text-danger"><?php echo empty($catError)? '': '*'.$catError; ?></p>
                            <select name="category" class="form-control">
                                <option value="">Select Category</option>
                                <?php foreach ($catResult as $value) {?>
                                    <?php if ($value['id'] == $result[0]['category_id']) : ?>
                                        <option value="<?php echo $value['id']; ?>" selected><?php echo $value['name']; ?></option>
                                    <?php else : ?>
                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                    <?php endif; ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label><p class="text-danger"><?php echo empty($qtyError)? '': '*'.$qtyError; ?></p>
                            <input class="form-control" type="number" name="quantity" value="<?php echo escape($result[0]['quantity']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label><p class="text-danger"><?php echo empty($priceError)? '': '*'.$priceError; ?></p>
                            <input class="form-control" type="number" name="price" value="<?php echo escape($result[0]['price']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label><p class="text-danger"><?php echo empty($imageError)? '': '*'.$imageError; ?></p>
                            <img src="images/<?php echo escape($result[0]['image']); ?>" width=150 height=150> <br>
                            <input type="file" name="image">
                        </div>
                        <div class="form-group">
                            <a href="index.php"  class="btn btn-default">Back</a>
                            <input type="submit" value="Submit" class="btn btn-success">
                        </div>
                    </form>
                    </div>
                </div>
              </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    <?php include('footer.html'); ?>