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
?>
<?php include('header.php') ?>
  <!-- Main content -->
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Order Listings</h3>
                  </div>
                  <?php
                    if(!empty($_GET['pageno'])){
                      $pageno = $_GET['pageno'];
                    }else {
                      $pageno = 1;
                    }

                    $numOfrecs = 5;
                    $offset = ($pageno -1) * $numOfrecs;

                    $stmt = $pdo->prepare("SELECT * FROM sale_order_detail WHERE sale_order_id=".$_GET['id']);
                    $stmt->execute();
                    $rawResult = $stmt->fetchAll();
                    $total_pages = ceil(count($rawResult)/$numOfrecs);

                    $stmt = $pdo->prepare("SELECT * FROM sale_order_detail WHERE sale_order_id=".$_GET['id']." LIMIT $offset, $numOfrecs");
                    $stmt->execute();
                    $result = $stmt->fetchAll();
                  ?>
                  <!-- /.card-header -->
                  <div class="card-body">
                  <div>
                      <a href="order_list.php" type="button" class="btn btn-default">Back</a>
                    </div>
                    <br>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th style="width: 10px">#</th>
                          <th>Product</th>
                          <th>Quantity</th>
                          <th>Order Date</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        if($result){
                          $i = 1;
                          foreach($result as $value){ ?>
                            <?php
                              $productStmt = $pdo->prepare("SELECT * FROM products WHERE id=".$value['product_id']);
                              $productStmt->execute();
                              $productResult = $productStmt->fetchAll();
                            ?>
                          <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo escape($productResult[0]['name']); ?></td>
                          <td><?php echo escape($value['quantity']); ?></td>
                          <td><?php echo escape(date('Y-m-d', strtotime($value['order_date']))); ?></td>
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
                      <li class="page-item"><a class="page-link" href="?id=<?php echo $_GET['id']; ?>&pageno=1">First</a></li>
                      <li class="page-item <?php if($pageno <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php echo $pageno <=1 ? '#' : '?id=' . $_GET['id'] . '&pageno='.($pageno-1); ?>">Previous</a>
                      </li>
                      <li class="page-item disabled">
                        <a class="page-link" href="#"><?php echo $pageno; ?></a>
                      </li>
                      <li class="page-item <?php if($pageno >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php echo $pageno >= $total_pages ? '#' : '?id=' . $_GET['id'] . '&pageno='.($pageno+1); ?>">Next</a>
                      </li>
                      <li class="page-item"><a class="page-link" href="?id=<?php echo $_GET['id']; ?>&pageno=<?php echo $total_pages; ?>">Last</a></li>
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