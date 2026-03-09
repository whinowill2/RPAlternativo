# RP Alternativo - Repositório da Revista Laboratório do Curso de Relações Públicas da UFMA

Este é um projeto open-source para criação de uma plataforma de revistas digitais iterativa, permitindo a leitura online, possuindo um sistema robusto de gerenciamento e exibição de edições.

# Sobre a RP Alternativo

A Revista RP Alternativo é uma revista laboratório produzida pelos discentes do curso de Relações Públicas, da Universidade Federal do Maranhão (UFMA), há mais de trinta anos. Ela surgiu em 1993 no formato de boletim informativo, na disciplina de “Redação em Relações Públicas II”. A revista tem como objetivo abordar os principais assuntos da área de Relações Públicas e da Comunicação.

O site foi desenvolvido com um design simples e funcional, pensado para proporcionar uma melhor navegação e experiência para o usuário. Levando em consideração que a maior parte dos acessos será por meio de smartphones e tablets, a plataforma foi construída utilizando a estratégia de desenvolvimento mobile first, garantindo uma navegação mais fluida nesses dispositivos. Além disso, o site conta com recursos de acessibilidade em Libras, ampliando o acesso ao conteúdo e promovendo maior inclusão.

# Por que Open Source?

Decidimos abrir o código-fonte deste projeto com o intuito de democratizar o acesso a plataformas de conteúdos acadêmicos e de preservação de acervos digitais. Nosso principal objetivo é garantir que outros estudantes,especialmente os dos cursos de Comunicação, e qualquer pessoa envolvida na produção editorial possam ter seus próprios repositórios profissionais de maneira simplificada, sem altas barreiras tecnológicas.

Ao compartilhar essa estrutura tecnológica de base testada e robusta, queremos fomentar a memória universitária e facilitar a disseminação ágil de revistas laboratório, publicações e projetos inovadores para toda a sociedade!

## Estrutura do Projeto

O repositório é dividido em duas partes principais:

- **`frontend/`**: Aplicação construída em React com Vite, contendo a interface do usuário focada em performance, PWA integrado, com menus reativos e carregamento otimizado.
- **`backend/`**: Implementação em PHP nativo que provê os endpoints se conectando a um banco de dados MySQL para listar edições, registrar visualizações e recuperar as configurações visuais e dados dinâmicos da plataforma.

## Como Clonar e Executar

Esta aplicação web é compatível e pode ser executada perfeitamente no **Linux**, **Windows** ou **macOS**. Para iniciar, é importante se certificar de que você tenha em seu ambiente:
- Controle de versão **Git**.
- **Node.js** (versão 18 ou superior).
- Um **Servidor Web** rodando **PHP** integrado a um banco de dados **MySQL** local (XAMPP, WAMP, MAMP, ou Docker/LAMP).

### 1. Clonar o Repositório

Abra o seu sistema de terminais (Bash no Linux/macOS ou Prompt de Comando/PowerShell no Windows) e utilize o Git para baixar a pasta do projeto:

```bash
git clone https://github.com/SEU_USUARIO/nome-do-repositorio.git
cd nome-do-repositorio
```

### 2. Backend (PHP & MySQL)
1. Certifique-se de possuir um servidor web (Apache/Nginx) rodando PHP e conectividade MySQL.
2. Adicione as tabelas referente ao sistema para leitura das edições. Crie as tabelas baseadas no retorno de JSON esperado pelas APIs.
3. Modifique as credenciais no arquivo `backend/endpoints/conexao.php` para acessar sua base de dados local:
   ```php
   $hostname = 'localhost';
   $dbname   = 'seu_banco';
   $username = 'root';
   $password = '';
   ```

### 2. Frontend (React)
1. Acesse o diretório do frontend:
   ```bash
   cd frontend
   ```
2. Instale as dependências:
   ```bash
   npm install
   ```
3. Crie um arquivo `.env` em `frontend/` usando como base o `.env.example` contendo a sua API_KEY de proteção e a URL de acesso ao seu backend local:
   ```env
   VITE_API_URL=http://localhost/seu-projeto-backend/endpoints
   VITE_API_KEY=sua_chave_de_seguranca_aqui
   ```
4. Execute o servidor em modo desenvolvedor:
   ```bash
   npm run dev
   ```

## Funcionalidades Principais
- **Layout Responsivo**: Otimizado para desktop e mobile de forma independente e com design moderno.
- **Integração de Mídia**: Link rápido para Flipbooks externos, URLs de PDF e conexões diretas via Playlists baseadas na edição.
- **Progressive Web App**: O app possui suporte completo a PWA para instalação no celular.

## Pensado e executado por:
Giovana Garcia, Kerienn Teles, Maria Victória Sousa, Samantha Santos e Will Ribeiro

