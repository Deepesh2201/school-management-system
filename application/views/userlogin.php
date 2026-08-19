<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a" />
    <title>Login : <?php echo $name; ?></title>
    <link href="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/<?php $this->setting_model->getAdminsmalllogo(); ?>" rel="shortcut icon" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand: #4f46e5;
            --brand-dark: #4338ca;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --danger: #dc2626;
            --success: #059669;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                linear-gradient(135deg, rgba(15, 23, 42, .78), rgba(30, 27, 75, .85)),
                url('<?php echo base_url(); ?>uploads/school_content/login_image/<?php echo $school['user_login_page_background']; ?>') no-repeat center center / cover;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 30px 60px -15px rgba(15, 23, 42, .45), 0 0 0 1px rgba(255, 255, 255, .08);
            padding: 44px 40px 38px;
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 28px;
        }

        .brand-logo {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            object-fit: contain;
            padding: 6px;
            background: #f8fafc;
            border: 1px solid var(--line);
            margin-bottom: 16px;
        }

        .brand h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--ink);
            letter-spacing: -.02em;
            text-align: center;
            line-height: 1.3;
        }

        .brand p {
            margin-top: 6px;
            font-size: 13.5px;
            color: var(--muted);
            text-align: center;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 12px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--brand);
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 999px;
        }

        .alert {
            font-size: 13.5px;
            padding: 11px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
            line-height: 1.45;
        }

        .alert-danger { background: #fef2f2; color: var(--danger); border: 1px solid #fecaca; }
        .alert-success { background: #ecfdf5; color: var(--success); border: 1px solid #a7f3d0; }

        .field {
            margin-bottom: 18px;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 7px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            height: 48px;
            padding: 0 46px 0 44px;
            font-size: 14.5px;
            font-family: inherit;
            color: var(--ink);
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 12px;
            transition: border-color .2s, box-shadow .2s, background .2s;
            outline: none;
        }

        .input-wrap input:focus {
            background: #fff;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .14);
        }

        .input-wrap input::placeholder { color: #94a3b8; }

        .input-wrap.toggle .eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-wrap.toggle .eye:hover { color: var(--ink); }

        .field-error {
            display: block;
            margin-top: 6px;
            font-size: 12.5px;
            color: var(--danger);
        }

        .captcha-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .captcha-row img, .captcha-row .img { border-radius: 8px; border: 1px solid var(--line); }

        .captcha-refresh {
            background: none;
            border: 0;
            color: var(--brand);
            font-size: 14px;
            cursor: pointer;
            padding: 4px;
            display: inline-flex;
        }

        .btn-signin {
            width: 100%;
            height: 50px;
            margin-top: 6px;
            border: 0;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            letter-spacing: .01em;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            box-shadow: 0 10px 20px -8px rgba(79, 70, 229, .6);
            cursor: pointer;
            transition: transform .15s, box-shadow .15s, opacity .15s;
        }

        .btn-signin:hover { transform: translateY(-1px); box-shadow: 0 14px 24px -8px rgba(79, 70, 229, .7); }
        .btn-signin:active { transform: translateY(0); opacity: .92; }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 22px;
            font-size: 13.5px;
        }

        .form-footer a {
            color: var(--brand);
            font-weight: 600;
            text-decoration: none;
        }

        .form-footer a:hover { text-decoration: underline; }

        .form-footer .muted { color: var(--muted); font-weight: 400; }

        .card-footer {
            margin-top: 26px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
            text-align: center;
            font-size: 12.5px;
            color: var(--muted);
        }

        .card-footer strong { color: var(--ink); font-weight: 600; }

        @media (max-width: 480px) {
            body { padding: 16px; }
            .login-card { padding: 34px 24px 30px; border-radius: 16px; }
            .brand h1 { font-size: 19px; }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <img class="brand-logo" src="<?php echo base_url(); ?>uploads/school_content/admin_logo/<?php echo $this->setting_model->getAdminlogo(); ?>" alt="<?php echo htmlspecialchars($school['name'], ENT_QUOTES); ?>" onerror="this.style.display='none'">
            <h1><?php echo $this->lang->line('user_login'); ?></h1>
            <p><?php echo $school['name']; ?></p>
            <span class="role-badge">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <?php echo $this->lang->line('student_parent_login'); ?>
            </span>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('message')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('message'); ?></div>
        <?php endif; ?>

        <form action="<?php echo site_url('site/userlogin') ?>" method="post">
            <?php echo $this->customlib->getCSRF(); ?>

            <div class="field">
                <label class="field-label" for="email"><?php echo $this->lang->line('username'); ?></label>
                <div class="input-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <input type="text" name="username" id="email" placeholder="<?php echo $this->lang->line('username'); ?>" value="<?php echo set_value("username"); ?>" autocomplete="username">
                </div>
                <span class="field-error"><?php echo form_error('username'); ?></span>
            </div>

            <div class="field">
                <label class="field-label" for="password"><?php echo $this->lang->line('password'); ?></label>
                <div class="input-wrap toggle">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" name="password" id="password" placeholder="<?php echo $this->lang->line('password'); ?>" value="<?php echo set_value("password"); ?>" autocomplete="current-password">
                    <button type="button" class="eye" id="toggle-password" aria-label="Toggle password visibility">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <span class="field-error"><?php echo form_error('password'); ?></span>
            </div>

            <?php if ($is_captcha): ?>
            <div class="field">
                <label class="field-label"><?php echo $this->lang->line('captcha'); ?></label>
                <div class="captcha-row">
                    <span id="captcha_image"><?php echo $captcha_image; ?></span>
                    <button type="button" class="captcha-refresh" onclick="refreshCaptcha()" title="<?php echo $this->lang->line('refresh_captcha'); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    </button>
                    <input type="text" name="captcha" id="captcha" placeholder="<?php echo $this->lang->line('captcha'); ?>" autocomplete="off" style="flex:1;height:46px;padding:0 12px;font-size:14px;font-family:inherit;color:#0f172a;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;outline:none;">
                </div>
                <span class="field-error"><?php echo form_error('captcha'); ?></span>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn-signin"><?php echo $this->lang->line('sign_in'); ?></button>

            <div class="form-footer">
                <span class="muted">Welcome back</span>
                <a href="<?php echo site_url('site/ufpassword') ?>"><?php echo $this->lang->line('forgot_password'); ?></a>
            </div>
        </form>

        <div class="card-footer">
            &copy; <?php echo date('Y'); ?> <strong><?php echo $school['name']; ?></strong>. All rights reserved.
        </div>
    </div>

    <script>
        var toggle = document.getElementById('toggle-password');
        if (toggle) {
            toggle.addEventListener('click', function () {
                var pw = document.getElementById('password');
                pw.type = (pw.type === 'password') ? 'text' : 'password';
            });
        }
    </script>
    <script type="text/javascript">
        function refreshCaptcha(){
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('site/refreshCaptcha'); ?>",
                data: {},
                success: function(captcha){
                    document.getElementById("captcha_image").innerHTML = captcha;
                }
            });
        }
    </script>
    <script>
        function copy(email, password) {
            document.getElementById("email").value = email;
            document.getElementById("password").value = password;
        }
    </script>
    <script src="<?php echo base_url(); ?>backend/usertemplate/assets/js/jquery-1.11.1.min.js"></script>
</body>
</html>
