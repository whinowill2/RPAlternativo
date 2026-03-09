<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração</title>
    <meta property="og:image" content="/">
    <meta property="og:title" content="/">
    <meta property="og:description" content="/">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap');

        :root {
            --cor-fundo: #131314;
            --cor-card: #1f1f1f;
            --cor-texto: #e0e0e0;
            --cor-titulo: #ffffff;
            --cor-destaque: #80EF80;
            --cor-cinza: #333;
            --cor-vermelho: #ff5555;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--cor-fundo);
            margin: 0;
            padding: 20px;
            color: var(--cor-texto);
        }

        h1,
        h2,
        h3 {
            color: var(--cor-titulo);
            font-weight: 600;
        }

        .main-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .main-header h1 {
            color: var(--cor-destaque);
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(128, 239, 128, 0.2);
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
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 900px;
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

        .modal-content h2,
        .modal-content h3 {
            color: var(--cor-destaque);
            margin-top: 20px;
        }

        .modal-body {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }

        @media (min-width: 900px) {
            .modal-body {
                grid-template-columns: 1fr 1fr;
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="url"],
        input[type="email"],
        textarea,
        select {
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

        input[type="color"] {
            padding: 0;
            height: 48px;
            width: 50px;
            border-radius: 6px;
            border: none;
            background: none;
            cursor: pointer;
        }

        textarea {
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--cor-destaque);
            box-shadow: 0 0 0 3px rgba(128, 239, 128, 0.3);
        }

        .color-picker-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .color-picker-wrapper input[type="text"] {
            width: 150px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            font-family: 'Montserrat', sans-serif;
        }

        .btn-primary {
            background-color: var(--cor-destaque);
            color: #000;
        }

        .btn-primary:hover {
            filter: brightness(1.1);
        }

        .btn-danger {
            background-color: var(--cor-vermelho);
            color: #fff;
        }

        .btn-secondary {
            background-color: #444;
            color: #fff;
        }

        .item-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .item {
            background-color: var(--cor-card);
            border: 1px solid var(--cor-cinza);
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .item img {
            width: 50px;
            height: 70px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 4px;
        }

        .item .membro-foto {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            flex-shrink: 0;
        }

        .item-content {
            flex-grow: 1;
            margin-left: 15px;
        }

        .item-content p {
            margin: 0;
        }

        .item-actions {
            display: flex;
            gap: 10px;
        }

        fieldset {
            border: 1px solid var(--cor-cinza);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        legend {
            color: var(--cor-destaque);
            font-weight: bold;
            padding: 0 10px;
        }

        .feedback-message {
            display: none;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .feedback-message.success {
            background-color: #28a745;
            color: white;
        }

        .feedback-message.error {
            background-color: #dc3545;
            color: white;
        }

        .feedback-message.loading {
            background-color: #007bff;
            color: white;
        }

        .item-social-links {
            margin-top: 8px;
        }

        .item-social-links a {
            color: var(--cor-texto);
            text-decoration: none;
            font-size: 1.2em;
            margin-right: 15px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .item-social-links a:hover {
            opacity: 1;
            color: var(--cor-destaque);
        }

        .email-preview-wrapper {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Work Sans', sans-serif;
        }

        .email-preview {
            background-color: #ffffff;
            max-width: 600px;
            margin: auto;
            border-collapse: collapse;
            width: 100%;
            overflow: hidden;
        }

        .preview-header-logo {
            padding: 20px 0;
            background-color: #1f1f1f;
            text-align: center;
        }

        .preview-header-logo img {
            width: 100px;
        }

        .preview-banner img {
            display: block;
            width: 100%;
            max-width: 600px;
            height: auto;
        }

        .preview-title-wrapper {
            padding: 30px 20px 10px 20px;
            text-align: center;
        }

        .preview-title-wrapper h2 {
            font-family: 'Work Sans', sans-serif;
            font-size: 24px;
            color: #333333;
            margin: 0;
            font-weight: bold;
        }

        .preview-body-wrapper {
            padding: 10px 30px 20px 30px;
            font-family: 'Work Sans', sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #555555;
        }

        .preview-body-wrapper p {
            margin: 0;
        }

        .preview-button-wrapper {
            padding: 10px 30px 40px 30px;
            text-align: center;
        }

        .preview-button {
            font-size: 16px;
            font-family: 'Work Sans', sans-serif;
            color: #ffffff;
            text-decoration: none;
            display: inline-block;
            padding: 12px 50px;
            border-radius: 20px;
            font-weight: bold;
        }

        .preview-footer {
            padding: 20px 30px;
            background-color: #f4f4f4;
            font-family: 'Work Sans', sans-serif;
            font-size: 12px;
            color: #1f1f1f;
            line-height: 1.4;
            text-align: center;
        }

        .preview-footer img {
            max-width: 100%;
            margin-bottom: 10px;
        }

        .preview-footer p {
            margin: 0 0 10px 0;
            font-family: 'Work Sans', sans-serif;
        }

        .preview-footer a {
            color: #005A9C;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .modal-content {
                margin-top: 100px;
                margin-bottom: 100px;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="main-header">
        <img src="/" style="width:200px; margin-top: 50px;">
        <h1>Seu titulo</h1>
    </div>
    <div class="dashboard-grid">
        <div class="card" data-modal-target="#modal-config-gerais">
            <div class="card-header"><i class="fas fa-cog card-icon"></i>
                <div class="card-title">
                    <h2>Configurações Gerais</h2>
                </div>
            </div>
            <p>Gerenciar título, logos, favicon, links, cores e rodapé.</p>
        </div>
        <div class="card" data-modal-target="#modal-pagina-inicial">
            <div class="card-header"><i class="fas fa-home card-icon"></i>
                <div class="card-title">
                    <h2>Página Inicial</h2>
                </div>
            </div>
            <p>Alterar conteúdo da página principal: edição em destaque e seção "Sobre".</p>
        </div>
        <div class="card" data-modal-target="#modal-gerenciar-edicoes">
            <div class="card-header"><i class="fas fa-book-open card-icon"></i>
                <div class="card-title">
                    <h2>Gerenciar Edições</h2>
                </div>
            </div>
            <p>Adicionar, editar, visualizar e remover todas as edições da revista.</p>
        </div>
        <div class="card" data-modal-target="#modal-gerenciar-expediente">
            <div class="card-header"><i class="fas fa-users card-icon"></i>
                <div class="card-title">
                    <h2>Gerenciar Expediente</h2>
                </div>
            </div>
            <p>Adicionar, editar ou remover membros e professores da equipe.</p>
        </div>
        <div class="card" data-modal-target="#modal-gerenciar-galeria">
            <div class="card-header"><i class="fas fa-images card-icon"></i>
                <div class="card-title">
                    <h2>Gerenciar Galeria</h2>
                </div>
            </div>
            <p>Adicionar ou remover fotos da galeria e gerenciar o banner principal.</p>
        </div>
        <div class="card" data-modal-target="#modal-email-dispatcher">
            <div class="card-header"><i class="fas fa-paper-plane card-icon"></i>
                <div class="card-title">
                    <h2>Disparar Email</h2>
                </div>
            </div>
            <p>Enviar newsletter ou comunicados para os inscritos.</p>
        </div>
    </div>

    <div id="modal-config-gerais" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Configurações Gerais</h2>
            <form id="form-config-gerais" class="admin-form" data-action-prefix="config-gerais">
                <fieldset>
                    <legend>Identidade Visual e Cores</legend>
                    <div class="form-group"><label for="site_logo">Logo do Cabeçalho (Desktop)</label><input type="url"
                            id="site_logo" name="site_logo" placeholder="URL da imagem"></div>
                    <div class="form-group"><label for="site_logo_mobile">Logo Mobile</label><input type="url"
                            id="site_logo_mobile" name="site_logo_mobile" placeholder="URL da imagem para celular">
                    </div>
                    <div class="form-group"><label for="site_favicon">Favicon</label><input type="url" id="site_favicon"
                            name="site_favicon" placeholder="URL da imagem"></div>
                    <div class="form-group"><label for="cor_principal">Cor Principal (Destaques e links)</label>
                        <div class="color-picker-wrapper"><input type="color" id="cor_principal_picker"
                                value="#80EF80"><input type="text" id="cor_principal" name="cor_principal"
                                maxlength="7"></div>
                    </div>
                    <div class="form-group"><label for="cor_header">Cor do Fundo do Cabeçalho</label>
                        <div class="color-picker-wrapper"><input type="color" id="cor_header_picker"
                                value="#1f1f1f"><input type="text" id="cor_header" name="cor_header" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group"><label for="cor_texto_header">Cor do Texto do Cabeçalho</label>
                        <div class="color-picker-wrapper"><input type="color" id="cor_texto_header_picker"
                                value="#FFFFFF"><input type="text" id="cor_texto_header" name="cor_texto_header"
                                maxlength="7"></div>
                    </div>
                    <div class="form-group"><label for="cor_botoes">Cor do Fundo dos Botões</label>
                        <div class="color-picker-wrapper"><input type="color" id="cor_botoes_picker"
                                value="#80EF80"><input type="text" id="cor_botoes" name="cor_botoes" maxlength="7">
                        </div>
                    </div>
                    <div class="form-group"><label for="cor_texto_botoes">Cor do Texto dos Botões</label>
                        <div class="color-picker-wrapper"><input type="color" id="cor_texto_botoes_picker"
                                value="#000000"><input type="text" id="cor_texto_botoes" name="cor_texto_botoes"
                                maxlength="7"></div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Metadados (SEO)</legend>
                    <div class="form-group"><label for="site_title">Título do Site</label><input type="text"
                            id="site_title" name="site_title" value="Nome do Projeto"></div>
                    <div class="form-group"><label for="og_description">Descrição Curta</label><textarea
                            id="og_description" name="og_description" rows="3"></textarea></div>
                    <div class="form-group"><label for="og_image">Imagem de Compartilhamento</label><input type="url"
                            id="og_image" name="og_image" placeholder="URL da imagem"></div>
                </fieldset>
                <fieldset>
                    <legend>Rodapé e Contato</legend>
                    <div class="form-group"><label for="footer_instagram">Link do Instagram</label><input type="url"
                            id="footer_instagram" name="footer_instagram" placeholder="https://instagram.com/..."></div>
                    <div class="form-group"><label for="footer_email">Email de Contato</label><input type="email"
                            id="footer_email" name="footer_email" placeholder="contato@email.com"></div>
                </fieldset>
                <button type="submit" class="btn btn-primary">Salvar Configurações</button>
            </form>
            <div class="feedback-message"></div>
        </div>
    </div>

    <div id="modal-pagina-inicial" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Conteúdo da Página Inicial</h2>
            <form id="form-pagina-inicial" class="admin-form" data-action-prefix="pagina-inicial">

                <fieldset>
                    <legend>Edição em Destaque</legend>
                    <p style="font-size: 0.8em; color: #aaa; margin-top:-10px; margin-bottom:20px;">
                        Selecione qual edição cadastrada será o destaque da página inicial.
                    </p>
                    <div class="form-group">
                        <label for="destaque_edicao_id">Edição em Destaque</label>
                        <select id="destaque_edicao_id" name="destaque_edicao_id">
                            <option value="">Carregando...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hero_title">Título Principal do Hero</label>
                        <input type="text" id="hero_title" name="hero_title" placeholder="Ex: Edição 53">
                    </div>
                    <div class="form-group">
                        <label for="hero_image_url">URL da Imagem de Fundo do Hero</label>
                        <input type="url" id="hero_image_url" name="hero_image_url"
                            placeholder="Link para a imagem de fundo">
                    </div>
                    <div class="form-group">
                        <label for="hero_cover_url">URL da Imagem da Capa no Hero</label>
                        <input type="url" id="hero_cover_url" name="hero_cover_url"
                            placeholder="Link para a imagem da capa da revista">
                    </div>
                    <div class="form-group">
                        <label for="sobre_p1">Resumo da Edição em Destaque</label>
                        <textarea id="sobre_p1" name="sobre_p1" rows="5"></textarea>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Seção "Sobre a Revista"</legend>
                    <div class="form-group">
                        <label for="sobre_p2">Texto da Seção Sobre</label>
                        <textarea id="sobre_p2" name="sobre_p2" rows="5"></textarea>
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-primary">Salvar Conteúdo</button>
            </form>
            <div class="feedback-message"></div>
        </div>
    </div>

    <div id="modal-gerenciar-edicoes" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Gerenciar Edições</h2>
            <div class="modal-body">
                <div>
                    <h3>Edições Cadastradas</h3>
                    <ul id="lista-edicoes" class="item-list"></ul>
                </div>
                <div>
                    <h3>Adicionar / Editar Edição</h3>
                    <form id="form-edicao" class="admin-form" data-action-prefix="edicao">
                        <input type="hidden" name="id" id="edicao_id">
                        <fieldset>
                            <legend>Informações Principais</legend>
                            <div class="form-group"><label for="numero_edicao">Número da Edição</label><input
                                    type="text" id="numero_edicao" name="numero_edicao" placeholder="Ex: Edição #52">
                            </div>
                            <div class="form-group"><label for="data_lancamento">Data de lançamento</label><input
                                    type="text" id="data_lancamento" name="data_lancamento"
                                    placeholder="Ex: Julho de 2025"></div>
                            <div class="form-group"><label for="resumo">Resumo da Edição</label><textarea id="resumo"
                                    name="resumo" rows="4"></textarea></div>
                            <div class="form-group"><label for="url_capa">URL da Imagem da Capa</label><input type="url"
                                    id="url_capa" name="url_capa"></div>
                            <div class="form-group"><label for="url_flipbook">Link do Flipbook</label><input type="url"
                                    id="url_flipbook" name="url_flipbook"></div>
                            <div class="form-group"><label for="url_playlist">Link da Playlist</label><input type="url"
                                    id="url_playlist" name="url_playlist"></div>
                        </fieldset>
                        <fieldset>
                            <legend>Conteúdo das Editorias</legend>
                            <div class="form-group"><label for="editoria_entrevista">Entrevista</label><textarea
                                    id="editoria_entrevista" name="editoria_entrevista" rows="3"></textarea></div>
                            <div class="form-group"><label for="editoria_afinal">Afinal, o que é?</label><textarea
                                    id="editoria_afinal" name="editoria_afinal" rows="3"></textarea></div>
                            <div class="form-group"><label for="editoria_lookin">Look In</label><textarea
                                    id="editoria_lookin" name="editoria_lookin" rows="3"></textarea></div>
                            <div class="form-group"><label for="editoria_mestre">Conhecendo o Mestre</label><textarea
                                    id="editoria_mestre" name="editoria_mestre" rows="3"></textarea></div>
                            <div class="form-group"><label for="editoria_prata">Prata da Casa</label><textarea
                                    id="editoria_prata" name="editoria_prata" rows="3"></textarea></div>
                            <div class="form-group"><label for="resumos_tccs">Resumos de TCCs</label><textarea
                                    id="resumos_tccs" name="resumos_tccs" rows="5"></textarea></div>
                        </fieldset>
                        <fieldset>
                            <legend>Expediente da Edição</legend>
                            <div class="form-group"><label for="exp_professor">Professor(a)</label><textarea
                                    id="exp_professor" name="exp_professor" rows="1"></textarea></div>
                            <div class="form-group"><label for="exp_editor_chefe">Editor(a) chefe</label><textarea
                                    id="exp_editor_chefe" name="exp_editor_chefe" rows="1"></textarea></div>
                            <div class="form-group"><label for="exp_redacao">Redação</label><textarea id="exp_redacao"
                                    name="exp_redacao" rows="3"></textarea></div>
                            <div class="form-group"><label for="exp_diagramacao">Diagramação</label><textarea
                                    id="exp_diagramacao" name="exp_diagramacao" rows="3"></textarea></div>
                            <div class="form-group"><label for="exp_revisao">Revisão</label><textarea id="exp_revisao"
                                    name="exp_revisao" rows="3"></textarea></div>
                            <div class="form-group"><label for="exp_fotografia">Fotografia</label><textarea
                                    id="exp_fotografia" name="exp_fotografia" rows="3"></textarea></div>
                            <div class="form-group"><label for="exp_comissao_site">Comissão do Site</label><textarea
                                    id="exp_comissao_site" name="exp_comissao_site" rows="5"></textarea></div>
                            <div class="form-group"><label for="exp_colaboradores">Colaboradores</label><textarea
                                    id="exp_colaboradores" name="exp_colaboradores" rows="3"></textarea></div>
                        </fieldset>
                        <button type="submit" class="btn btn-primary">Salvar Edição</button><button type="button"
                            class="btn btn-secondary btn-clear-form">Limpar</button>
                    </form>
                    <div class="feedback-message"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-gerenciar-expediente" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Gerenciar Expediente</h2>
            <div class="modal-body">
                <div>
                    <h3>Membros Cadastrados</h3>
                    <ul id="lista-expediente" class="item-list"></ul>
                </div>
                <div>
                    <h3>Adicionar / Editar Membro</h3>
                    <form id="form-expediente" class="admin-form" data-action-prefix="expediente">
                        <input type="hidden" name="id" id="expediente_id">
                        <div class="form-group"><label for="exp_nome">Nome</label><input type="text" id="exp_nome"
                                name="nome" required></div>
                        <div class="form-group"><label for="exp_cargo">Cargo (Ex: Redatora, Editor-chefe)</label><input
                                type="text" id="exp_cargo" name="cargo" required></div>
                        <div class="form-group" style="display: none;"><label for="edicao_numero">Número da Edição (se
                                aplicável)</label><input type="text" id="edicao_numero" name="edicao_numero"
                                placeholder="Ex: 53"></div>
                        <div class="form-group"><label for="exp_url_foto">URL da Foto</label><input type="url"
                                id="exp_url_foto" name="url_foto" required></div>
                        <div class="form-group"><label for="exp_email">Email</label><input type="email" id="exp_email"
                                name="email" placeholder="email@dominio.com"></div>
                        <div class="form-group"><label for="exp_linkedin">LinkedIn (Opcional)</label><input type="url"
                                id="exp_linkedin" name="linkedin" placeholder="https://linkedin.com/in/..."></div>
                        <div class="form-group"><label for="exp_instagram">Instagram (Opcional)</label><input type="url"
                                id="exp_instagram" name="instagram" placeholder="https://instagram.com/..."></div>
                        <div class="form-group" id="exp_bio_group"><label for="exp_bio"
                                id="exp_bio_label">Mensagem</label><textarea id="exp_bio" name="bio" rows="4"
                                placeholder="Uma breve mensagem."></textarea></div>
                        <p style="font-size: 0.8em; color: #aaa; margin-top:-15px; margin-bottom:20px;">
                            <b>⚠️ Atenção:</b> Máximo de 500 caracteres.
                        </p>
                        <button type="submit" class="btn btn-primary">Salvar Membro</button><button type="button"
                            class="btn btn-secondary btn-clear-form">Limpar</button>
                    </form>
                    <div class="feedback-message"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-gerenciar-galeria" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Gerenciar Galeria de Fotos</h2>
            <div class="modal-body">
                <div>
                    <h3>Fotos na Galeria</h3>
                    <ul id="lista-galeria" class="item-list"></ul>
                </div>
                <div>
                    <h3>Adicionar / Editar Foto</h3>
                    <form id="form-galeria" class="admin-form" data-action-prefix="galeria">
                        <input type="hidden" name="id" id="galeria_id">
                        <div class="form-group"><label for="gal_url_imagem">URL da Imagem</label><input type="url"
                                id="gal_url_imagem" name="url_imagem" required></div>
                        <div class="form-group"><label for="gal_categoria">Categoria</label>
                            <select id="gal_categoria" name="categoria" required>
                                <option value="">Selecione uma categoria</option>
                                <option value="equipe">Equipe</option>
                                <option value="evento">Evento</option>
                                <option value="bastidores">Bastidores</option>
                                <option value="geral">Geral</option>
                            </select>
                        </div>
                        <div class="form-group-checkbox"
                            style="display: flex; align-items: center; margin-bottom: 20px;">
                            <input type="checkbox" id="gal_is_hero" name="is_hero" value="1"
                                style="width: auto; margin-right: 10px;">
                            <label for="gal_is_hero" style="margin-bottom: 0; font-weight: bold;">Definir como Banner
                                Principal?</label>
                        </div>
                        <p style="font-size: 0.8em; color: #aaa; margin-top:-15px; margin-bottom:20px;">
                            <b>⚠️ Atenção:</b> Marcar esta opção substituirá qualquer outro banner que esteja ativo. O
                            tamanho da foto para hero é 800x880px.
                        </p>
                        <button type="submit" class="btn btn-primary">Salvar Foto</button>
                        <button type="button" class="btn btn-secondary btn-clear-form">Limpar</button>
                    </form>
                    <div class="feedback-message"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-email-dispatcher" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Disparar Email</h2>
            <div class="modal-body">
                <div>
                    <h3>Configurar Email</h3>
                    <form id="form-email-dispatcher">
                        <fieldset>
                            <legend>Conteúdo</legend>
                            <div class="form-group">
                                <label for="email_header_url">URL da Imagem do Cabeçalho</label>
                                <input type="url" id="email_header_url" name="email_header_url"
                                    placeholder="https://...">
                            </div>

                            <div class="form-group">
                                <label for="email_header_color">Cor de Fundo do Cabeçalho</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" id="email_header_color_picker" value="#1f1f1f">
                                    <input type="text" id="email_header_color" name="email_header_color" maxlength="7"
                                        value="#1f1f1f">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email_subject">Título do Email</label>
                                <input type="text" id="email_subject" name="email_subject"
                                    placeholder="Assunto do email">
                            </div>
                            <div class="form-group"><label for="email_body">Corpo do Email</label><textarea
                                    id="email_body" name="email_body" rows="8"
                                    placeholder="Escreva o corpo do seu e-mail aqui... (use HTML básico se desejar)"></textarea>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Botão (CTA)</legend>
                            <div class="form-group"><label for="email_button_text">Texto do Botão</label><input
                                    type="text" id="email_button_text" name="email_button_text"
                                    placeholder="Ex: Leia Agora"></div>
                            <div class="form-group"><label for="email_button_link">Link do Botão</label><input
                                    type="url" id="email_button_link" name="email_button_link"
                                    placeholder="https://..."></div>
                            <div class="form-group"><label for="email_button_color">Cor do Fundo do Botão</label>
                                <div class="color-picker-wrapper"><input type="color" id="email_button_color_picker"
                                        value="#1f1f1f"><input type="text" id="email_button_color"
                                        name="email_button_color" maxlength="7" value="#1f1f1f"></div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Envio</legend>
                            <div class="form-group"><label for="email_test_address">Email de Teste</label><input
                                    type="email" id="email_test_address" name="email_test_address"
                                    placeholder="seu.email@dominio.com"></div>
                            <button type="button" id="btn-send-test-email" class="btn btn-secondary">Enviar
                                Teste</button>
                            <hr style="border-color: var(--cor-cinza); margin: 20px 0;">
                            <div class="form-group"><label for="email_custom_list">Lista de Emails
                                    (Opcional)</label><textarea id="email_custom_list" name="email_custom_list" rows="4"
                                    placeholder="Separe os emails por vírgula (,) ou quebra de linha (Enter)"></textarea>
                            </div>
                            <button type="button" id="btn-send-custom-email" class="btn btn-primary">Enviar para
                                Lista</button>
                            <hr style="border-color: var(--cor-cinza); margin: 20px 0;">
                            <!--<button type="button" id="btn-send-all-email" class="btn btn-danger">Enviar para TODOS</button> -->
                        </fieldset>
                    </form>
                    <div class="feedback-message"></div>
                </div>
                <div>
                    <h3>Preview do Email</h3>
                    <div class="email-preview-wrapper">
                        <table border="0" cellpadding="0" cellspacing="0" class="email-preview">
                            <tbody>
                                <tr>
                                    <td id="preview-header-cell" class="preview-header-logo">
                                        <img src="https://i.ibb.co/b5g0507n/RP-1-sem-fundo-branco.png"
                                            alt="Logo RP Alternativo" width="180">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="preview-banner">
                                        <img id="preview-img" src="https://placehold.co/600x200/png" alt="Banner"
                                            width="600" style="height: auto;">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="preview-title-wrapper">
                                        <h2 id="preview-title">Este é o Título Principal</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="preview-body-wrapper">
                                        <p id="preview-text">Corpo da mensagem do email aparece aqui...<br>Lorem ipsum
                                            dolor sit amet, consectetur adipiscing elit.</p>
                                    </td>
                                </tr>
                                <tr id="preview-button-row">
                                    <td class="preview-button-wrapper">
                                        <table border="0" cellspacing="0" cellpadding="0" style="margin: auto;">
                                            <tbody>
                                                <tr>
                                                    <td id="preview-button-cell" align="center"
                                                        style="border-radius: 20px; background-color: rgb(31, 31, 31);">
                                                        <a href="#" id="preview-button" target="_blank"
                                                            class="preview-button"
                                                            style="color: rgb(255, 255, 255); font-size: 16px; font-family: &quot;Work Sans&quot;, sans-serif; text-decoration: none; display: inline-block; padding: 12px 50px; border-radius: 20px; font-weight: bold;">
                                                            Clique Aqui
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="preview-footer">
                                        <img src="https://rpalternativo.ufma.br/wp-content/uploads/2025/07/cropped-android-chrome-192x192-1.png"
                                            alt="Logo" width="50"
                                            style="filter: invert(100%); margin-top: 15px; margin-bottom: 10px;">
                                        <p style="margin: 0px 0px 10px;">
                                            © 2025 RP Alternativo - UFMA. Todos os direitos reservados.<br>
                                            Av. dos Portugueses, 1966, Bacanga, São Luís, MA - CEP: 65080-805<br>
                                            <a
                                                href="mailto:contato@rpalternativo.ufma.br">contato@rpalternativo.ufma.br</a>
                                        </p>
                                        <img src="https://www.natalshowdepremios.com.br/uploads/imagens/powered_by_sosamy.png"
                                            alt="Logo samy" width="150" style="margin-top: 50px; margin-bottom: 10px;">
                                        <p style="font-size: 1em; color: rgb(153, 153, 153); margin: 10px;">
                                            Enviado com tecnologia <a href="https://www.instagram.com/whinowill"
                                                style="color: rgb(0, 90, 156);">SAMY</a>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const ENDPOINT_URL = 'https://www.exemplo.dev.br/endpoints/endpoint.php';
                        const EMAIL_API_ENDPOINT_URL = 'https://www.exemplo.dev.br/endpoints/email_api.php';
                        const API_SECRET_KEY = '4A5po8OlBHYvIHQDuB81Jmb465uvrlHhrPyRClY5JiysefYVeesrBRPfgwY584pyn3FqGxgqPBXQAwnzOMGVbnflro39SDPQT0qONjG0wbhebLM9cyP9KEcE0BHVGwHpJHcc9k4GVrqPPHKRf2hwdOuhbHINhaq1vxV1kmc2gfqduQs99eqD2cfetpGYc0tilKJvq48Ol5beyAYIZFxTfTYqFZSR2USPIKTPiO2UiqTS45URT7xXBPODRtGmil4n';

                        function syncColorInputs(pickerId, textId) {
                            const picker = document.getElementById(pickerId);
                            const text = document.getElementById(textId);
                            if (!picker || !text) return;
                            picker.addEventListener('input', () => { text.value = picker.value; });
                            text.addEventListener('input', () => { if (/^#[0-9A-F]{6}$/i.test(text.value)) { picker.value = text.value; } });
                        }

                        syncColorInputs('cor_principal_picker', 'cor_principal');
                        syncColorInputs('cor_header_picker', 'cor_header');
                        syncColorInputs('cor_botoes_picker', 'cor_botoes');
                        syncColorInputs('cor_texto_header_picker', 'cor_texto_header');
                        syncColorInputs('cor_texto_botoes_picker', 'cor_texto_botoes');
                        syncColorInputs('email_button_color_picker', 'email_button_color');
                        syncColorInputs('email_header_color_picker', 'email_header_color');

                        const cargoInputBio = document.getElementById('exp_cargo');
                        const bioLabel = document.getElementById('exp_bio_label');

                        function updateBioFieldLabel() {
                            if (!cargoInputBio || !bioLabel) return;
                            const cargoValue = cargoInputBio.value.trim().toLowerCase();
                            const isEditorChefe = cargoValue === 'editor-chefe' || cargoValue === 'editora-chefe';
                            bioLabel.textContent = isEditorChefe ? 'Mensagem do Editor (Opcional)' : 'Biografia (Opcional)';
                        }
                        if (cargoInputBio) { cargoInputBio.addEventListener('input', updateBioFieldLabel); }

                        function showFeedback(element, message, type) {
                            if (!element) return;
                            element.style.display = 'block';
                            element.className = `feedback-message ${type}`;
                            element.textContent = message;
                            setTimeout(() => { if (element) element.style.display = 'none'; }, 4000);
                        }

                        async function apiRequest(url, options = {}) {
                            try {
                                const response = await fetch(url, options);
                                if (!response.ok) throw new Error(`Erro de servidor: ${response.status}`);
                                return await response.json();
                            } catch (error) {
                                console.error('Erro de API:', error);
                                return { success: false, message: `Erro de comunicação: ${error.message}` };
                            }
                        }

                        function buildItemList(containerId, items, renderFunction) {
                            const container = document.getElementById(containerId);
                            if (!container) return;
                            container.innerHTML = '';
                            if (items && items.length > 0) {
                                items.forEach(item => {
                                    const li = document.createElement('li');
                                    li.className = 'item';
                                    li.dataset.id = item.id;
                                    li.innerHTML = renderFunction(item);
                                    container.appendChild(li);
                                });
                            } else {
                                container.innerHTML = '<p style="text-align:center; color: #aaa;">Nenhum item cadastrado.</p>';
                            }
                        }

                        function renderEdicaoItem(item) {
                            return `<img src="${item.url_capa || ''}" alt="Capa"><div class="item-content"><p>${item.numero_edicao}</p></div><div class="item-actions"><button class="btn btn-primary btn-edit" data-type="edicao" data-id="${item.id}"><i class="fas fa-pen"></i></button><button class="btn btn-danger btn-delete" data-action="delete_edicao" data-id="${item.id}"><i class="fas fa-trash"></i></button></div>`;
                        }

                        function renderExpedienteItem(item) {
                            let socialLinksHtml = '';
                            if (item.linkedin) { socialLinksHtml += `<a href="${item.linkedin}" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a>`; }
                            if (item.instagram) { socialLinksHtml += `<a href="${item.instagram}" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>`; }
                            if (item.email) { socialLinksHtml += `<a href="mailto:${item.email}" title="Email"><i class="fas fa-envelope"></i></a>`; }
                            const edicaoInfo = item.edicao_numero ? ` <span style="font-size:0.8em; opacity:0.7;">- Ed. #${item.edicao_numero}</span>` : '';
                            return `<div class="membro-foto" style="background-image: url('${item.url_foto || ''}');"></div><div class="item-content"><p><b>${item.nome}</b> (${item.cargo})${edicaoInfo}</p><div class="item-social-links">${socialLinksHtml}</div></div><div class="item-actions"><button class="btn btn-primary btn-edit" data-type="expediente" data-id="${item.id}"><i class="fas fa-pen"></i></button><button class="btn btn-danger btn-delete" data-action="delete_expediente" data-id="${item.id}"><i class="fas fa-trash"></i></button></div>`;
                        }

                        function renderGaleriaItem(item) {
                            const heroBadge = item.is_hero == 1 ? `<span class="hero-badge" title="Banner Principal" style="color: var(--cor-destaque); margin-left: 10px;">★ Hero</span>` : '';
                            return `<img src="${item.url_imagem || ''}" alt="Foto da Galeria"><div class="item-content"><p>Categoria: ${item.categoria}${heroBadge}</p></div><div class="item-actions"><button class="btn btn-primary btn-edit" data-type="galeria" data-id="${item.id}"><i class="fas fa-pen"></i></button><button class="btn btn-danger btn-delete" data-action="delete_galeria" data-id="${item.id}"><i class="fas fa-trash"></i></button></div>`;
                        }

                        async function carregarDadosIniciais() {
                            const result = await apiRequest(`${ENDPOINT_URL}?action=get_all_data&api_key=${API_SECRET_KEY}`, { method: 'GET' });
                            if (result.success && result.data) {
                                if (result.data.configuracoes) {
                                    const c = result.data.configuracoes;
                                    Object.keys(c).forEach(key => {
                                        const textInput = document.getElementById(key);
                                        if (textInput) {
                                            textInput.value = c[key] || '';
                                            const pickerInput = document.getElementById(`${key}_picker`);
                                            if (pickerInput) { pickerInput.value = c[key] || ''; }
                                        }
                                    });
                                }
                                if (result.data.pagina_inicial) {
                                    const p = result.data.pagina_inicial;
                                    Object.keys(p).forEach(key => { const el = document.getElementById(key); if (el) el.value = p[key] || ''; });
                                }
                                if (result.data.edicoes) {
                                    buildItemList('lista-edicoes', result.data.edicoes, renderEdicaoItem);
                                    const selectDestaque = document.getElementById('destaque_edicao_id');
                                    if (selectDestaque) {
                                        selectDestaque.innerHTML = '<option value="">Selecione uma edição</option>';
                                        result.data.edicoes.forEach(ed => {
                                            const option = document.createElement('option');
                                            option.value = ed.id;
                                            option.textContent = ed.numero_edicao;
                                            if (result.data.pagina_inicial && result.data.pagina_inicial.destaque_edicao_id == ed.id) { option.selected = true; }
                                            selectDestaque.appendChild(option);
                                        });
                                    }
                                }
                                if (result.data.expediente) { buildItemList('lista-expediente', result.data.expediente, renderExpedienteItem); }
                                if (result.data.galeria) { buildItemList('lista-galeria', result.data.galeria, renderGaleriaItem); }
                            } else {
                                console.error('Falha ao carregar dados iniciais:', result.message);
                                alert('Não foi possível carregar os dados do painel.');
                            }
                        }

                        document.querySelectorAll('.card[data-modal-target]').forEach(card => card.addEventListener('click', () => {
                            const modal = document.querySelector(card.dataset.modalTarget);
                            if (modal) modal.style.display = 'flex';
                        }));

                        document.querySelectorAll('.close-button').forEach(button => button.addEventListener('click', () => button.closest('.modal').style.display = 'none'));

                        window.addEventListener('click', (event) => {
                            if (event.target.classList.contains('modal')) event.target.style.display = 'none';
                        });

                        document.querySelectorAll('.admin-form').forEach(form => {
                            form.addEventListener('submit', async (e) => {
                                e.preventDefault();
                                const feedbackElement = form.closest('.modal-content').querySelector('.feedback-message');
                                showFeedback(feedbackElement, 'Salvando...', 'loading');
                                const formData = new FormData(form);
                                formData.append('api_key', API_SECRET_KEY);
                                const id = formData.get('id');
                                const actionPrefix = form.dataset.actionPrefix;
                                const action = actionPrefix.startsWith('config') || actionPrefix.startsWith('pagina') ? `update_${actionPrefix}` : (id ? `update_${actionPrefix}` : `add_${actionPrefix}`);
                                formData.append('action', action);
                                const result = await apiRequest(ENDPOINT_URL, { method: 'POST', body: formData });
                                if (result.success) {
                                    showFeedback(feedbackElement, result.message, 'success');
                                    carregarDadosIniciais();
                                    if (!id) form.reset();
                                } else {
                                    showFeedback(feedbackElement, result.message || 'Ocorreu um erro desconhecido.', 'error');
                                }
                            });
                        });

                        document.querySelectorAll('.btn-clear-form').forEach(button => {
                            button.addEventListener('click', () => {
                                const form = button.closest('form');
                                form.reset();
                                const hiddenId = form.querySelector('input[type="hidden"][name="id"]');
                                if (hiddenId) hiddenId.value = '';
                                if (form.id === 'form-expediente') updateBioFieldLabel();
                            });
                        });

                        document.body.addEventListener('click', async (e) => {
                            const button = e.target.closest('button');
                            if (!button) return;

                            if (button.classList.contains('btn-delete')) {
                                const id = button.dataset.id;
                                const action = button.dataset.action;
                                if (confirm(`Tem certeza que deseja excluir o item? Esta ação não pode ser desfeita.`)) {
                                    const formData = new FormData();
                                    formData.append('action', action);
                                    formData.append('id', id);
                                    formData.append('api_key', API_SECRET_KEY);
                                    const result = await apiRequest(ENDPOINT_URL, { method: 'POST', body: formData });
                                    if (result.success) {
                                        alert(result.message);
                                        carregarDadosIniciais();
                                    } else {
                                        alert(`Erro: ${result.message}`);
                                    }
                                }
                            }

                            if (button.classList.contains('btn-edit')) {
                                const id = button.dataset.id;
                                const type = button.dataset.type;
                                const form = document.getElementById(`form-${type}`);
                                if (!form) return;

                                const result = await apiRequest(`${ENDPOINT_URL}?action=get_${type}&api_key=${API_SECRET_KEY}&id=${id}`, { method: 'GET' });

                                if (result.success && result.data) {
                                    form.reset();
                                    const hiddenIdInput = form.querySelector('input[type="hidden"][name="id"]');
                                    if (hiddenIdInput) hiddenIdInput.value = result.data.id;

                                    for (const key in result.data) {
                                        const input = form.querySelector(`[name="${key}"]`);
                                        if (input) {
                                            if (input.type === 'checkbox') {
                                                input.checked = (result.data[key] == 1);
                                            } else {
                                                input.value = result.data[key];
                                            }
                                        }
                                    }
                                    if (type === 'expediente') { updateBioFieldLabel(); }
                                    alert(`Dados carregados no formulário para edição!`);
                                } else {
                                    alert(`Erro ao carregar dados para edição: ${result.message}`);
                                }
                            }
                        });

                        const emailForm = document.getElementById('form-email-dispatcher');
                        if (emailForm) {
                            const inputs = emailForm.querySelectorAll('input, textarea');
                            const previewImg = document.getElementById('preview-img');
                            const previewTitle = document.getElementById('preview-title');
                            const previewText = document.getElementById('preview-text');
                            const previewButton = document.getElementById('preview-button');

                            function updateEmailPreview() {
                                const headerUrl = document.getElementById('email_header_url').value;
                                const headerColor = document.getElementById('email_header_color').value;
                                const subject = document.getElementById('email_subject').value;
                                const body = document.getElementById('email_body').value;
                                const btnText = document.getElementById('email_button_text').value;
                                const btnLink = document.getElementById('email_button_link').value;
                                const btnColor = document.getElementById('email_button_color').value;
                                const previewImg = document.getElementById('preview-img');
                                const previewHeaderCell = document.getElementById('preview-header-cell');
                                const previewTitle = document.getElementById('preview-title');
                                const previewText = document.getElementById('preview-text');
                                const previewButton = document.getElementById('preview-button');
                                const previewButtonCell = document.getElementById('preview-button-cell');
                                const previewButtonRow = document.getElementById('preview-button-row');

                                if (previewImg) {
                                    previewImg.src = headerUrl || 'https://placehold.co/600x300/png?';
                                }
                                if (previewHeaderCell) {
                                    previewHeaderCell.style.backgroundColor = headerColor || '#1f1f1f';
                                }

                                if (previewTitle) {
                                    previewTitle.textContent = subject || 'Este é o Título Principal';
                                }
                                if (previewText) {
                                    previewText.innerHTML = body.replace(/\n/g, '<br>') || 'Corpo da mensagem do email aparece aqui...';
                                }

                                if (previewButtonCell) {
                                    previewButtonCell.style.backgroundColor = btnColor;

                                    const hex = btnColor.replace('#', '');
                                    const r = parseInt(hex.substring(0, 2), 16);
                                    const g = parseInt(hex.substring(2, 4), 16);
                                    const b = parseInt(hex.substring(4, 6), 16);
                                    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
                                    previewButton.style.color = luminance > 0.5 ? '#000000' : '#FFFFFF';
                                }

                                if (btnText && previewButtonRow) {
                                    previewButton.textContent = btnText;
                                    previewButton.href = btnLink;
                                    previewButtonRow.style.display = 'table-row';
                                } else if (previewButtonRow) {
                                    previewButtonRow.style.display = 'table-row';
                                }
                            }

                            inputs.forEach(input => input.addEventListener('input', updateEmailPreview));
                            updateEmailPreview();

                            const btnSendTest = document.getElementById('btn-send-test-email');
                            const btnSendCustom = document.getElementById('btn-send-custom-email');
                            const btnSendAll = document.getElementById('btn-send-all-email');
                            const feedbackEmail = emailForm.closest('.modal-content').querySelector('.feedback-message');

                            async function handleEmailSend(dispatchType, confirmationMessage) {
                                if (!confirm(confirmationMessage)) return;
                                showFeedback(feedbackEmail, 'Enviando...', 'loading');
                                const formData = new FormData(emailForm);
                                formData.append('api_key', API_SECRET_KEY);
                                formData.append('action', 'dispatch_email');
                                formData.append('dispatch_type', dispatchType);

                                const result = await apiRequest(EMAIL_API_ENDPOINT_URL, { method: 'POST', body: formData });
                                if (result.success) {
                                    showFeedback(feedbackEmail, `Email enviado para ${result.data.sent_count || '...'} destinatário(s)!`, 'success');
                                } else {
                                    showFeedback(feedbackEmail, `Erro: ${result.message}`, 'error');
                                }
                            }

                            btnSendTest.addEventListener('click', () => {
                                const testEmail = document.getElementById('email_test_address').value;
                                if (!testEmail) { alert('Por favor, insira um email de teste.'); return; }
                                handleEmailSend('test', `Enviar um email de teste para ${testEmail}?`);
                            });

                            btnSendCustom.addEventListener('click', () => {
                                const customList = document.getElementById('email_custom_list').value;
                                if (!customList.trim()) { alert('Por favor, insira pelo menos um email na lista.'); return; }
                                const emailCount = (customList.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi) || []).length;
                                handleEmailSend('custom', `Enviar este email para a lista de ${emailCount} destinatário(s)?`);
                            });

                            btnSendAll.addEventListener('click', () => {
                                handleEmailSend('all', `⚠️ ATENÇÃO! ⚠️\n\nVocê tem certeza que deseja enviar este email para TODOS os inscritos no banco de dados?\n\nEsta ação não pode ser desfeita.`);
                            });
                        }

                        carregarDadosIniciais();
                    });
                </script>
</body>

</html>