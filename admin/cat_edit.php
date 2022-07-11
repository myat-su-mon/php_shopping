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
    if(empty($_POST['name']) || empty($_POST['description'])){
        if(empty($_POST['name'])){
            $nameError = "Category name is required";
        }
        if(empty($_POST['description'])){
            $descError = "Description is required";
        }
    }else {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $id = $_POST['id'];

        $stmt = $pdo->prepare("UPDATE categories SET name=:name, description=:description WHERE id=:id");

        $result = $stmt->execute(
            array(':name' => $name, ':description' => $description, ':id' => $id)
        );

        if($result){
            echo "<script>alert('Category Updated');window.location.href='category.php';</script>";
        }
    }
  }

  $stmt = $pdo->prepare("SELECT * FROM categories WHERE id =" . $_GET['id']);
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
                    <form action="cat_edit.php" method="post">
                      <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                      <input type="hidden" name="id" value="<?php echo $result[0]['id']; ?>">
                    <div class="form-group">
                        <label for="name">Name</label><p class="text-danger"><?php echo empty($nameError)? '': '*'.$nameError; ?></p>
                        <input class="form-control" type="text" name="name" value="<?php echo escape($result[0]['name']);?>">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label><p class="text-danger"><?php echo empty($descError)? '': '*'.$descError; ?></p>
                        <textarea class="form-control" name="description" cols="30" rows="10"><?php echo escape($result[0]['description']);?></textarea>
                    </div>
                    <div class="form-group">
                        <a href="category.php"  class="btn btn-default">Back</a>
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