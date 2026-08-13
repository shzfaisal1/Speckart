<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Speckarts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      background: #f8f9fa;
    }
    .login-container {
      display: flex;
      min-height: 100vh;
    }
    .login-form {
      max-width: 500px;
      margin: auto;
    }
    .login-image {
      background: linear-gradient(to right, #e3f2fd, #03a9f4);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }
    .login-image img {
      max-width: 80%;
    }
    .social-btns .btn {
      margin: 5px;
    }
  </style>
</head>
<body>
  <div class="container-fluid login-container">
    <div class="row flex-fill">
      <div class="col-md-6 d-flex align-items-center justify-content-center" style="background: linear-gradient(to right, #03a9f4, #e3f2fd);">
        <div class="login-form w-100">
          <h2 class="text-dark">Welcome!</h2>
          <h5 class="mb-4">Sign In to Continue</h5>
          <form method="POST" action="<?php echo e(route('login')); ?>">
              <?php if(Session::has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span><?php echo Session::get('success'); ?></span>
                    <span type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </span>
                </div>
            <?php endif; ?>
            
            <?php if(Session::has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span><?php echo Session::get('error'); ?></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus placeholder="Enter Email.">
              <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback" role="alert">
                    <?php echo e($message); ?>

                </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <label for="password" class="form-label">Password</label>
                <a href="#" class="text-decoration-none text-primary small">Forgot Password</a>
              </div>
              <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" required autocomplete="current-password" id="password" placeholder="Enter Password.">
              <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="invalid-feedback" role="alert">
                <?php echo e($message); ?>

            </span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe">
              <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
            <button type="submit" class="btn btn-dark w-100 mb-3">login</button>
            
          </form>
        </div>
      </div>
      <div class="col-md-6 login-image d-none d-md-flex">
        <img src="<?php echo e(asset('frontend/asset/img/logo/Specskart-logo.png')); ?>" alt="" style="max-width: 100%;display: block;margin: 0 auto; height: auto;">
      </div>
    </div>
  </div>

  <!-- Font Awesome for icons -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(".toggle-password").click(function() 
    {
        $(this).toggleClass("fa-eye fa-eye-slash");
        input = $(this).parent().find("input");
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        }else{
            input.attr("type", "password");
        }
    });
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Merge_speckart_09-07_26\resources\views/auth/login.blade.php ENDPATH**/ ?>