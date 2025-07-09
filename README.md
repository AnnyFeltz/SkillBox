
# 🎨 SkillBox – Meu Projeto de TCC

O **SkillBox** é uma plataforma acessível e intuitiva, desenvolvida para **todos que desejam organizar suas habilidades, ferramentas e projetos de forma visual e moderna**. Com um editor estilo Canva, qualquer usuário pode:

- Criar projetos visuais em canvas com múltiplos elementos  
- Associar ferramentas e tarefas a cada projeto  
- Fazer upload e armazenamento de imagens PNG via ImgBB  
- Salvar o estado dos projetos localmente ou no banco de dados  
- Publicar seus projetos com visualização em imagem  
- Gerenciar perfis e dashboards personalizados  

---

## 🚀 Instruções de Uso

Depois de entrar no SkillBox, você vai encontrar:

- 🏠 **Dashboard:** sua central com seus projetos, ferramentas e estatísticas rápidas para te ajudar a se organizar.  
- 🎨 **Editor de Projetos:** crie e edite seus projetos visuais usando um editor estilo Canva, com formas, imagens e textos.  
- 🧰 **Ferramentas:** gerencie as ferramentas que você usa nos seus projetos e associe elas facilmente.  
- 📋 **Tarefas:** acompanhe o progresso das tarefas vinculadas a cada projeto.  
- 📂 **Projetos Publicados:** visualize e compartilhe seus trabalhos publicados com imagens de alta qualidade.  
- 👤 **Perfil:** gerencie suas informações pessoais e preferências.  

Explore cada área para tirar o máximo proveito do SkillBox e organizar suas habilidades de forma prática e criativa! ✨

---

## 🖼️ Descrição das Views Principais

| View                        | Descrição                                                                                      |
|-----------------------------|------------------------------------------------------------------------------------------------|
| `dashboard.index`           | Tela principal do usuário com visão geral dos seus projetos, ferramentas e notificações       |
| `canvas.index`              | Lista os projetos/canvases do usuário, permitindo criação, edição e exclusão                    |
| `canvas.edit`               | Editor visual do projeto, com ferramentas para desenhar, adicionar imagens e manipular objetos  |
| `tools.index`               | Lista as ferramentas cadastradas pelo usuário, com opções para criar, editar e associar         |
| `tasks.index`               | Tela para gerenciar tarefas vinculadas a projetos, podendo criar e acompanhar progresso         |
| `projects.publicados`       | Exibe os projetos publicados por todos os usuários, com opção de visualização e filtro          |
| `profile.show`              | Visualização do perfil do usuário                                                              |
| `profile.edit`              | Tela para editar informações do perfil                                                        |

---

## ⚙️ Instruções de Execução

📂 Entre na pasta do projeto:

```bash
cd SkillBox
```

📦 Instale as dependências do PHP:

```bash
composer install
```

📦 Instale as dependências do Node.js e compile os assets:

```bash
npm install
npm run dev
```

📝 Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

🔑 Gere a chave do Laravel:

```bash
php artisan key:generate
```

🛠️ Rode as migrações e seeders:

```bash
php artisan migrate --seed
```

🌐 Inicie o servidor local:

```bash
php artisan serve
```

---

## 🧪 Tecnologias Utilizadas

### Backend

- Laravel 10  
- MySQL (com replicação master-slave)  
- Autenticação Breeze  
- API ImgBB (para upload de imagens PNG)  

### Frontend

- Blade (Laravel)  
- Bootstrap 5  
- Konva.js (editor visual estilo Canva)  
- JavaScript (vanilla)  

---

## 🧩 Organização dos Casos de Uso e Modelos

### Casos de Uso

| Caso de Uso           | Descrição                                                         |
|-----------------------|-------------------------------------------------------------------|
| Criar Canvas          | Usuário cria um novo projeto visual, definindo tamanho e título  |
| Editar Canvas         | Elementos visuais podem ser manipulados com ferramentas (Konva.js)|
| Salvar Progresso      | Estado do canvas é salvo localmente ou no banco de dados          |
| Publicar Projeto      | Uma imagem PNG é gerada do canvas e enviada para o ImgBB         |
| Gerenciar Tarefas     | Tarefas associadas ao projeto podem ser criadas no editor ou no index |
| Associar Ferramentas  | Ferramentas previamente cadastradas são associadas aos projetos   |
| Visualizar Projetos   | Projetos publicados são exibidos com imagem e informações básicas |
| Perfil de Usuário     | Visualização e edição do perfil                                   |
| Dashboard             | Exibição dos projetos e ferramentas do usuário                   |

### Principais Modelos

| Modelo          | Atributos Principais                        |
|-----------------|--------------------------------------------|
| `User`          | `id`, `name`, `email`, `role_id`           |
| `CanvasProjeto` | `id`, `user_id`, `titulo`, `data_json`, `preview_url` |
| `CanvasImagem`  | `id`, `canvas_id`, `url`, `descricao`      |
| `Task`          | `id`, `canvas_id`, `titulo`, `descricao`, `status` |
| `Tool`          | `id`, `user_id`, `nome`, `descricao`, `icone` |

---

## 👥 Autoria

Projeto desenvolvido por **Ana Caroline Madera Feltz (Anny Feltz)** para o curso de Tecnologia em Análise e Desenvolvimento de Sistemas de 2024 (TADS24) — 2º ano — Trabalho Final. 💖  
Github: [@AnnyFeltz](https://github.com/AnnyFeltz)

---

data de entrega: 09/07/2025