<?php
session_start();

$usuarios_permitidos = [
    'admin' => 'admin',
];
$erro = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_enviado = $_POST['usuario'] ?? '';
    $senha_enviada = $_POST['senha'] ?? '';

    if (isset($usuarios_permitidos[$usuario_enviado]) && $usuarios_permitidos[$usuario_enviado] == $senha_enviada) {

        $_SESSION['loggedin'] = true;
        $_SESSION['usuario'] = $usuario_enviado;
        header("Location: admin.php");
        exit;

    } else {
        $erro = 'Usuário ou senha incorretos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Admin</title>
    <meta property="og:image" content="/">
    <meta property="og:title" content="/">
    <meta property="og:description" content="/">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        :root {
            --cor-fundo: #131314;
            --cor-card: rgba(31, 31, 31, 0.7);
            --cor-texto: #e0e0e0;
            --cor-titulo: #ffffff;
            --cor-destaque: #80EF80;
            --cor-cinza: #333;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--cor-fundo);
            margin: 0;
            padding: 20px;
            color: var(--cor-texto);
        }

        h1,
        h2 {
            color: var(--cor-titulo);
            font-weight: 600;
            text-align: center;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1400px;
            margin: auto;
        }

        .card {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid var(--cor-cinza);
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .card-icon {
            font-size: 1.5em;
            color: var(--cor-destaque);
        }

        .card-title h2 {
            margin: 0;
            font-size: 1.3em;
        }

        .card p {
            font-size: 0.9em;
            line-height: 1.6;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .modal-content {
            background-color: #1a1a1a;
            color: var(--cor-texto);
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .close-button {
            color: #aaa;
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-content h2 {
            color: var(--cor-destaque);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            margin-top: 8px;
        }

        input[type="text"],
        input[type="url"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid var(--cor-cinza);
            background-color: #0a0a1a;
            color: var(--cor-texto);
            font-size: 1em;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        textarea {
            resize: vertical;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--cor-destaque);
            box-shadow: 0 0 0 3px rgba(128, 239, 128, 0.3);
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            width: 100%;
            font-weight: bold;
            font-family: 'Montserrat', sans-serif;
            margin-top: 15px;
        }

        .btn-primary {
            background-color: var(--cor-destaque);
            color: #000;
        }

        .btn-primary:hover {
            filter: brightness(1.1);
        }

        .feedback-message {
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
            font-weight: bold;
        }

        .feedback-message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .feedback-message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .feedback-message.loading {
            background-color: #fff3cd;
            color: #856404;
        }

        .login-container {
            max-width: 400px;
            margin: auto;
        }

        .glow-button {
            display: block;
            height: 60px;
            max-width: 230px;
            position: relative;
            width: 100%;
            cursor: pointer;
        }

        .effect-layer {
            border-radius: 102px;
            contain: paint;
            height: 100%;
            position: absolute;
            width: 100%;
        }

        .gradient-layer {
            background: conic-gradient(transparent 10deg,
                    #00ff7b 40deg,
                    #34a853 65deg,
                    #9cff2e 90deg,
                    rgba(0, 255, 123, 0.5) 150deg,
                    transparent 200deg);
            filter: blur(8px);
            height: 200px;
            inset: 0;
            position: absolute;
            scale: 4 0.8;
            translate: 0 -70px;
            opacity: 0;
            transition: opacity 0.5s cubic-bezier(0.2, 0, 0, 1);
        }

        .mask-window {
            border-radius: 100px;
            inset: 2px;
            overflow: hidden;
            position: absolute;
        }

        .mask-window::before {
            background: #1f1f1f;
            border-radius: inherit;
            content: "";
            inset: 0;
            position: absolute;
        }

        .content-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            gap: 8px;
            position: relative;
            z-index: 1;
        }

        .developed-by {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: "Montserrat", sans-serif;
            margin-top: 30px;
        }

        .content-wrapper svg {
            width: 35px;
            height: 35px;
            fill: #80EF80;
        }

        .content-text {
            color: #fff;
            font-size: 14px;
            font-weight: 500;
        }

        .content-text-2 {
            color: #fff;
            font-size: 10px;
            font-weight: 500;
        }

        .glow-button.intro-active .gradient-layer {
            opacity: 1;
            animation:
                rotate-effect-intro 8s cubic-bezier(0.2, 0, 0, 1) forwards,
                fade-out-effect 1s cubic-bezier(0.4, 0, 0.2, 1) 4s forwards;
        }

        .glow-button.intro-active .mask-window::before {
            animation: change-bg-effect 1s cubic-bezier(0.4, 0, 0.2, 1) 4s forwards;
        }

        .glow-button:hover .gradient-layer {
            opacity: 1;
            animation: rotate-effect-loop 8s linear infinite;
        }

        .glow-button:hover .mask-window::before {
            animation: none;
            transition: background-color 0.5s ease;
        }

        @keyframes rotate-effect-intro {
            from {
                transform: rotate(135deg);
            }

            to {
                transform: rotate(565deg);
            }
        }

        @keyframes rotate-effect-loop {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes fade-out-effect {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        @keyframes change-bg-effect {
            from {
                background: #1f1f1f;
            }

            to {
                background: #1f1f1f;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Acesso ao Painel</h1>
        <div class="card">
            <form action="login.php" method="post">
                <label for="user">Usuário</label>
                <input type="text" id="usuario" name="usuario" required>
                <label for="senha">Senha de Acesso</label>
                <input type="password" id="senha" name="senha" required>
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
            <?php if (!empty($erro)): ?>
                <p class="feedback-message error" style="margin-top: 1rem;"><?php echo $erro; ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="developed-by">
        <div id="ai-button" class="glow-button intro-active">
            <div class="effect-layer">
                <div class="gradient-layer"></div>
            </div>
            <div class="effect-layer">
                <div class="mask-window"></div>
                <div class="content-wrapper"><span class="content-text-2">Developed by</span>
                    <svg viewBox="0 0 75 75" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M40.3721 74.8896C39.4223 74.9617 38.4624 75 37.4941 75C37.1941 75 36.895 74.9943 36.5967 74.9873C36.8952 74.9943 37.1949 74.999 37.4951 74.999C38.4631 74.999 39.4225 74.9617 40.3721 74.8896ZM63.1631 64.835C63.0718 64.9208 62.9779 65.004 62.8857 65.0889C62.9779 65.004 63.0698 64.9188 63.1611 64.833L63.1631 64.835ZM37.4951 0C58.2027 0.000228869 74.9893 16.7897 74.9893 37.5C74.9892 48.2783 70.4409 57.9927 63.1611 64.833C58.6764 60.8613 52.4291 57.517 45.127 55.4092C54.939 54.7471 62.5086 50.4537 63.666 44.0645C64.6538 38.6099 60.7301 32.946 53.9883 28.8779C54.3439 28.2453 54.5269 27.4978 54.4639 26.6914L53.7344 17.3701C53.4759 14.0708 49.4582 12.6045 47.1357 14.9619L40.5752 21.6211C40.2085 21.9934 39.9363 22.4138 39.7529 22.8564C39.0353 22.7619 38.2893 22.7119 37.5244 22.7119C36.2035 22.7119 34.9399 22.8599 33.7783 23.1309C33.2908 23.1139 32.8069 23.1055 32.3271 23.1055C32.2875 22.5759 32.1045 22.0446 31.7578 21.5674L24.2344 11.2109C22.7072 9.10884 19.4131 9.80017 18.8594 12.3389L16.1318 24.8467C15.9523 25.6703 16.1232 26.4599 16.5244 27.0928C13.5921 29.0088 11.6394 31.5442 11.0967 34.54C9.79549 41.7243 17.0163 49.2714 27.9727 53.0576C19.3583 53.1689 11.8008 55.3177 6.75293 58.9697C2.49838 52.8876 7.39276e-05 45.4861 0 37.5C0 16.7895 16.7874 0 37.4951 0ZM43.3057 27.5098C47.9167 25.9737 53.0677 28.9703 54.8105 34.2031C56.5531 39.4357 54.2278 44.9227 49.6172 46.459C45.3032 47.8961 40.5162 45.3654 38.4902 40.748C36.2729 45.5815 31.086 48.022 26.7139 46.2119C22.2234 44.3528 20.2931 38.7142 22.4023 33.6182C24.5117 28.5221 29.862 25.8977 34.3525 27.7568C36.2368 28.537 37.6706 29.9827 38.5439 31.7744C39.5215 29.7829 41.168 28.2219 43.3057 27.5098ZM72.3057 42.2852C72.3905 41.7635 72.037 41.4111 71.5156 41.4971L65.5039 42.4902C64.9822 42.5765 64.4894 43.0698 64.4043 43.5918C64.3195 44.1135 64.6738 44.467 65.1953 44.3809L71.207 43.3867C71.7287 43.3004 72.2205 42.8071 72.3057 42.2852ZM31.582 31.1924C29.0641 31.1925 27.0227 33.2338 27.0225 35.752C27.0225 38.2703 29.064 40.3123 31.582 40.3125C34.1002 40.3125 36.1416 38.2704 36.1416 35.752C36.1414 33.2337 34.1 31.1924 31.582 31.1924ZM46.7812 31.1924C44.2632 31.1924 42.2219 33.2337 42.2217 35.752C42.2217 38.2704 44.2631 40.3125 46.7812 40.3125C49.2992 40.3123 51.3408 38.2703 51.3408 35.752C51.3406 33.2338 49.2991 31.1926 46.7812 31.1924ZM73.1416 35.2197C73.1169 34.6916 72.6891 34.3963 72.1865 34.5605L66.0264 36.5742C65.5237 36.7385 65.1366 37.3008 65.1611 37.8291C65.186 38.3571 65.6128 38.6515 66.1152 38.4873L72.2754 36.4736C72.778 36.3093 73.1662 35.748 73.1416 35.2197ZM3.25098 35.1123C2.72939 35.0263 2.37583 35.3795 2.46094 35.9014C2.54607 36.4233 3.03784 36.9167 3.55957 37.0029L9.57129 37.9961C10.0929 38.0822 10.4464 37.729 10.3613 37.207C10.2761 36.6851 9.78437 36.1917 9.2627 36.1055L3.25098 35.1123ZM69.5449 30.4902C69.25 30.0514 68.6615 29.9438 68.2305 30.25L62.6035 34.248C62.1725 34.5544 62.0625 35.1587 62.3574 35.5977C62.6524 36.0365 63.2408 36.1442 63.6719 35.8379L69.2988 31.8398C69.7298 31.5335 69.8398 30.9292 69.5449 30.4902ZM2.58008 28.1768C2.07744 28.0125 1.64959 28.3076 1.625 28.8359C1.60052 29.3642 1.98863 29.9256 2.49121 30.0898L8.65137 32.1035C9.15385 32.2675 9.58089 31.9725 9.60547 31.4443C9.63002 30.9161 9.24273 30.3548 8.74023 30.1904L2.58008 28.1768ZM6.53613 23.8662C6.10504 23.5599 5.51661 23.6675 5.22168 24.1064C4.92678 24.5454 5.0367 25.1498 5.46777 25.4561L11.0947 29.4541C11.5257 29.7601 12.1142 29.6525 12.4092 29.2139C12.704 28.775 12.5939 28.1707 12.1631 27.8643L6.53613 23.8662ZM6.75098 58.9717L6.75293 58.9697C6.8019 59.0397 6.84901 59.111 6.89844 59.1807C6.84911 59.1112 6.79984 59.0415 6.75098 58.9717Z"
                            fill="80EF80"></path>
                    </svg><span class="content-text"><b>Nome da Equipe</b></span>
                </div>
            </div>
        </div>
    </div>
    <script>
        const aiButton = document.getElementById('ai-button');
        const gradientLayer = aiButton.querySelector('.gradient-layer');

        gradientLayer.addEventListener('animationend', (event) => {
            if (event.animationName === 'rotate-effect-intro' && aiButton.classList.contains('intro-active')) {
                aiButton.classList.remove('intro-active');
            }
        });
    </script>
</body>

</html>