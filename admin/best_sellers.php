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
                    <h3 class="card-title">Best Seller Items</h3>
                  </div>
                  <?php
                    $currentDate = date("Y-m-d");
                    $stmt = $pdo->prepare("SELECT * FROM sale_order_detail GROUP BY product_id HAVING SUM(quantity) > 5");
                    $stmt->execute([':from_date'=>$fromDate, ':to_date'=>$toDate]);
                    $result = $stmt->fetchAll();
                  ?>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <table id="d-table" class="table table-bordered">
                      <thead>
                        <tr>
                          <th style="width: 10px">#</th>
                          <th>Product</th></tr>
                      </thead>
                      <tbody>
                      <?php
                        if($result){
                          $i = 1;
                          foreach($result as $value){ ?>
                            <?php
                              $stmt = $pdo->prepare("SELECT * FROM products WHERE id=".$value['product_id']);
                              $stmt->execute();
                              $result = $stmt->fetchAll();
                            ?>
                            <tr>
                              <td><?php echo $i ?></td>
                              <td><?php echo escape($result[0]['name']) ?></td>
                            </tr>
                          <?php 
                          $i++;
                          }
                        }
                        ?>
                      </tbody>
                    </table><br>
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
    <script>
      $(document).ready(function () {
        $('#d-table').DataTable();
      });
    </script>