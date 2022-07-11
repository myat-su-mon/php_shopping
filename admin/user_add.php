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
    if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['address']) 
      || empty($_POST['password']) || strlen($_POST['password'])< 4 ){
      if(empty($_POST['name'])){
        $nameError = 'Name cannot be empty';
      }
      if(empty($_POST['email'])){
        $emailError = 'Email is required';
      }
      if(empty($_POST['phone'])){
        $phoneError = 'Phone is required';
      }
      if(empty($_POST['address'])){
        $addressError = 'Address is required';
      }
      if(empty($_POST['password'])){
        $passwordError = 'Password cannot be null';
      }
      if(strlen($_POST['password']) < 4){
        $passwordError = 'Password should be 4 characters at least';
      }
    }else {
      $name = $_POST['name'];
      $email = $_POST['email'];
      $phone = $_POST['phone'];
      $address = $_POST['address'];
      $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

      if(empty($_POST['role'])){
          $role = 0;
      }else {
          $role = 1;
      }
      
      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
      $stmt->bindValue(':email', $email);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
      if($user) {
          echo "<script>alert('User Email already exists');</script>";
      }else {
          $stmt = $pdo->prepare("INSERT INTO users(name, email, password, role, phone, address) VALUES (:name, :email, :password, :role, :phone, :address)");
          $result = $stmt->execute(
              array(':name' => $name, ':email' => $email, ':password' => $password, ':role' => $role, ':phone' => $phone, ':address' => $address)
          );
  
          if($result) {
              echo "<script>alert('New User Successfully Added');window.location.href='user_list.php';</script>";
          }
      }
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
                    <div class="card-body">
                    <form action="user_add.php" method="post">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                    <div class="form-group">
                        <label for="name">Name</label><p class="text-danger"><?php echo empty($nameError)? '': '*'.$nameError; ?></p>
                        <input class="form-control" type="text" name="name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label><p class="text-danger"><?php echo empty($emailError)? '': '*'.$emailError; ?></p>
                        <input class="form-control" type="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label><p class="text-danger"><?php echo empty($phoneError)? '': '*'.$phoneError; ?></p>
                        <input class="form-control" type="text" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label><p class="text-danger"><?php echo empty($addressError)? '': '*'.$addressError; ?></p>
                        <input class="form-control" type="text" name="address">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label><p class="text-danger"><?php echo empty($passwordError)? '': '*'.$passwordError; ?></p>
                        <input class="form-control" type="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="role">Admin</label>
                        <input class="ml-3" type="checkbox" name="role">
                    </div>
                    <div class="form-group">
                        <a href="user_list.php"  class="btn btn-default">Back</a>
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