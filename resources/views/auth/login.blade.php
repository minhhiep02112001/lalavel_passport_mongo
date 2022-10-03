<!DOCTYPE html>
<html class="no-js" lang="vi">

<head>
    <meta charset="utf-8">
    <title>Đăng nhập - IMAP ID</title>
    <meta name="author" content="TUTA">
    <meta name="description" content="IMAP ID">
    <meta name="keywords" content="IMAP ID">
    <meta name="viewport" content="width=device-width initial-scale=1">
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
    <link rel="stylesheet" href="{{asset('css/style.min.css')}}">
</head>

<body>
<div class="page">
    <div class="login">
        <div class="container">
            <div class="login-left">
                <img class="img-fluid login-slogan" src="/images/slogan.png" alt=""/>
                <hr class="login-divider">
                <img class="img-fluid login-brands" src="/images/brands.png" alt=""/>
            </div>
            <div class="login-form">
                <img class="img-fluid login-form-logo" src="/images/logo.svg" alt=""/>
                <div class="login-form-header">
                    <h2 class="modal-title mb-1">Đăng nhập</h2>
                    <p class="mb-4">Chưa có tài khoản? <a class="link-primary" href="/register">Tạo tài khoản mới</a>
                    </p>
                </div>
                <form class="login-form-groups" id="login" method="POST" action="{{route('login.check')}}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label visually-hidden" for="login-form-email">Email</label>
                        <input class="form-control is-invalid" id="login-form-email" name="email_phone" type="text"
                               placeholder="Email hoặc số điện thoại" value="{{old('email_phone')}}"/>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="login-form-btn-group">
                        <div class="form-group d-grid">
                            <button class="btn btn-primary fw-bold" type="submit">Tiếp theo</button>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 10px">
                        <div class="form-text text-end">
                            <a href="/forgot-password">Quên mật khẩu?</a>
                        </div>
                    </div>
                </form>
                <div class="login-form-btn-group">
                    <div class="form-group d-grid">
                        <a class="btn btn-login-light" style="background-color: #f4f4f4;
                border-color: #d3d3d3; box-shadow: none;
    color: #323232;" type="button" href="/login/google">
                            <img class="img-fluid btn-icon" src="/images/google.svg" alt=""/>Đăng nhập với Google
                        </a>
                    </div>
                    <div class="form-group d-grid">
                        <a class="btn btn-login-light" style="background-color: #3b5998;
                border-color: #3b5998;
                color: #fff;" type="button" href="/oauth/facebook">
                            <img class="img-fluid btn-icon" src="/images/facebook.svg" alt=""/>Đăng nhập với Facbook
                        </a>
                    </div>

                    <div class="form-group d-grid">
                        <a class="btn btn-login-light" style="background-color: #000;
                border-color: #000;
                color: #fff;" type="button" href="/oauth/apple">
                            <img class="img-fluid btn-icon" src="/images/apple.svg" alt=""/>Đăng nhập với Apple
                        </a>
                    </div>
                    <div class="form-group d-grid">
                        <a class="btn btn-login-light" style="background-color: #f4f4f4;
                        border-color: #d3d3d3; box-shadow: none;
            color: #323232;" type="button" href="/oauth/zalo">
                            <img width="24" height="24" class="img-fluid btn-icon" src="/images/zalo.svg" alt=""/>Đăng
                            nhập với Zalo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    {{--    window.loginUrl = {{route('post.login')}}--}}
        window.findAccountUrl = "{{route('search.account')}}"
    window.forgotPasswordUrl = "{{route('forgot-password')}}"
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="{{asset('js/customer.js')}}"></script>

</body>

</html>
