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

  if(!empty($_POST['search'])){
    setcookie('search', $_POST['search'], time(), '/');
  }else {
    if(empty($_GET['pageno'])){
      unset($_COOKIE['search']);
      setcookie('search', null, -1, '/');
    }
  }
?>
<?php include('header.php') ?>
  <!-- Main content -->
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Product Listings</h3>
                  </div>
                  <?php
                    if(!empty($_GET['pageno'])){
                      $pageno = $_GET['pageno'];
                    }else {
                      $pageno = 1;
                    }

                    $numOfrecs = 5;
                    $offset = ($pageno -1) * $numOfrecs;

                    if(empty($_POST['search']) && empty($_COOKIE['search'])) {
                      $stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC");
                      $stmt->execute();
                      $rawResult = $stmt->fetchAll();
                      $total_pages = ceil(count($rawResult)/$numOfrecs);

                      $stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC LIMIT $offset, $numOfrecs");
                      $stmt->execute();
                      $result = $stmt->fetchAll();
                    }else {
                      $searchKey = $_POST['search'] ? $_POST['search'] : $_COOKIE['search'];
                      $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE '%$searchKey%' ORDER BY id DESC");
                      $stmt->execute();
                      $rawResult = $stmt->fetchAll();
                      $total_pages = ceil(count($rawResult)/$numOfrecs);
  
                      $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE '%$searchKey%' ORDER BY id DESC LIMIT $offset, $numOfrecs");
                      $stmt->execute();
                      $result = $stmt->fetchAll();
                    }
                  ?>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div>
                      <a href="product_add.php" type="button" class="btn btn-success">Create New Product</a>
                    </div>
                    <br>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th style="width: 10px">#</th>
                          <th>Name</th>
                          <th>Description</th>
                          <th>Category</th>
                          <th>In Stock</th>
                          <th>Price</th>
                          <th style="width: 40px">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if($result){
                          $i = 1;
                          foreach($result as $value){ ?>
                            <?php
                              $catStmt = $pdo->prepare("SELECT * FROM categories WHERE id=".$value['category_id']);
                              $catStmt->execute();
                              $catResult = $catStmt->fetchAll();
                            ?>
                            <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo escape($value['name']) ?></td>
                            <td><?php echo escape(substr($value['description'],0,30)) ?></td>
                            <td><?php echo escape($catResult[0]['id']) ?></td>
                            <td><?php echo escape($value['quantity']) ?></td>
                            <td><?php echo escape($value['price']) ?></td>
                            <td>
                              <div class="btn-group">
                                <div class="container">
                                  <a href="product_edit.php?id=<?php echo $value['id'] ?>" type="button" class="btn btn-warning"
                                  >Edit</a
                                >
                                </div>
                                <div class="container">
                                  <a href="product_delete.php?id=<?php echo $value['id'] ?>"
                                  onclick="return confirm('Are you sure to delete this post?');"
                                  type="button" class="btn btn-danger"
                                  >Delete</a
                                >
                                </div>
                              </div>
                            </td>
                          </tr>
                          <?php 
                          $i++;
                          }
                        }
                        ?>
                      </tbody>
                    </table><br>
                    <nav aria-label="Page navigation example" class="float-right">
                    <ul class="pagination">
                      <li class="page-item"><a class="page-link" href="?pageno=1">First</a></li>
                      <li class="page-item <?php if($pageno <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php echo $pageno <=1 ? '#' : '?pageno='.($pageno-1); ?>">Previous</a>
                      </li>
                      <li class="page-item disabled">
                        <a class="page-link" href="#"><?php echo $pageno; ?></a>
                      </li>
                      <li class="page-item <?php if($pageno >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php echo $pageno >= $total_pages ? '#' : '?pageno='.($pageno+1); ?>">Next</a>
                      </li>
                      <li class="page-item"><a class="page-link" href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
                    </ul>
                  </nav> 
                  </div>
                  <!-- /.card-body -->
                  
                </div>
              </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    <?php include('footer.html'); ?>