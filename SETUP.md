# 🚀 Guia de Instalação e Configuração

## DevMenthors - Cronograma de Aulas
**Versão**: 1.0  
**Data**: Abril de 2026  

---

## 📋 Pré-Requisitos

Antes de começar, verifique se você tem instalado:

```bash
# Verificar PHP
php --version  # Deve ser 8.1+

# Verificar Composer
composer --version

# Verificar Node.js
node --version  # Deve ser 16+
npm --version

# Verificar Git
git --version
```

---

## ✅ Passo 1: Preparar o Ambiente

### Windows (PowerShell)

```powershell
# Navegue até a pasta do projeto
cd "c:\Users\Mateus Amaro\Documents\DevMenthors\Cronograma de Aulas"

# Verifique se está no diretório correto
Get-Location
```

### macOS / Linux

```bash
cd ~/DevMenthors/Cronograma\ de\ Aulas
pwd
```

---

## ✅ Passo 2: Instalar Dependências PHP

```bash
# Instale as dependências via Composer
composer install

# Isto levará alguns minutos...
```

**Se tiver erro**: Execute `composer update` ou `composer install --no-scripts`

---

## ✅ Passo 3: Configurar Variáveis de Ambiente

```bash
# Copie o arquivo de exemplo
cp .env.example .env

# Gere a chave de aplicação
php artisan key:generate

# Verifique se a APP_KEY foi gerada em .env
cat .env | grep APP_KEY
```

---

## ✅ Passo 4: Instalar Dependências Frontend

```bash
# Instale as dependências Node.js
npm install

# Isto pode levar alguns minutos...
```

---

## ✅ Passo 5: Configurar Banco de Dados

### 5.1 Criar arquivo SQLite

```bash
# Crie o arquivo do banco de dados
touch database/database.sqlite

# Verifique se foi criado
ls -la database/database.sqlite
```

### 5.2 Executar Migrations (criar tabelas)

```bash
# Execute as migrações
php artisan migrate

# Você verá uma lista de migrações sendo executadas:
# Migrating: 2026_04_04_100000_create_people_table
# Migrated: 2026_04_04_100000_create_people_table (xxx ms)
# ... e assim por diante
```

### 5.3 Seed com Dados Iniciais

```bash
# Popule o banco de dados com dados iniciais
php artisan db:seed

# Você verá:
# Seeding database...
# Database seeded successfully.
```

---

## ✅ Passo 6: Build dos Assets Frontend

### Opção A: Build para Produção

```bash
npm run build

# Isto criará os arquivos otimizados em public/build/
```

### Opção B: Modo Desenvolvimento (com hot reload)

```bash
# Terminal 1: Inicie o dev server do Vite
npm run dev

# Deixe rodando (ele observará mudanças em CSS/JS)
# Pressione Ctrl+C para parar quando necessário
```

---

## ✅ Passo 7: Inicie o Servidor da Aplicação

**Terminal 2** (se estiver usando npm run dev em outro terminal):

```bash
php artisan serve

# Você verá:
# INFO  Server running on [http://127.0.0.1:8000]
# 
# Pressione Ctrl+C para parar o servidor
```

---

## ✅ Passo 8: Acesse a Aplicação

Abra seu navegador e visite:

```
http://localhost:8000
```

Você deve ver:
- ✅ Header com logo "DevMenthors"
- ✅ Sidebar com lista de 20 pessoas (13 professores + 7 mentorados)
- ✅ Grid 4×4 com as 4 turmas e 4 sábados
- ✅ Abas de "Cronograma" e "Suplentes"
- ✅ Botões "Gerar Rodízio" e "Limpar"

---

## 🧪 Testando as Funcionalidades

### Teste 1: Adicionar uma Nova Pessoa

1. Clique em **"Adicionar"** na sidebar
2. Digite um nome (ex: "João Silva")
3. Selecione "Professor"
4. Clique em "Confirmar"
5. ✅ Você deve ver um toast verde dizendo "Pessoa adicionada com sucesso!"

### Teste 2: Gerar Rodízio Automático

1. Clique em **"Gerar Rodízio"** no header
2. Confirme a ação
3. ✅ O sistema deve preencher o grid com nomes respeitando as regras
4. ✅ Vira um toast "Rodízio gerado com sucesso!"

### Teste 3: Drag & Drop Manual

1. Na grid, tente arrastar um nome da sidebar para uma célula
2. ✅ Deve aparecer a pessoa naquela turma/sábado
3. Se inválido (ex: mentorado em turma avançada), verá erro em vermelho

### Teste 4: Abrir Aba de Suplentes

1. Clique na aba **"Suplentes"**
2. Arraste nomes para as células
3. ✅ Mesma validação que o cronograma principal

### Teste 5: Remover uma Pessoa

1. Clique em **"Remover"** na sidebar
2. Selecione uma pessoa
3. Confirme a remoção
4. ✅ Pessoa deve desaparecer da lista

---

## 🛠️ Comandos Úteis

### Verificar Status do Banco de Dados

```bash
# Ver todas as migrações
php artisan migrate:status

# Reset do banco (CUIDADO: apaga tudo!)
php artisan migrate:reset

# Re-executar migrações e seed
php artisan migrate:refresh --seed
```

### Debugging

```bash
# Acessar o shell interativo
php artisan tinker

# Exemplos de comandos:
# Person::all()
# Person::where('type', 'professor')->get()
# Schedule::forMonth(4, 2026)->entries()->count()
# exit
```

### Build Assets

```bash
# Build production
npm run build

# Development com hot reload
npm run dev

# Preview dos assets
npm run preview
```

---

## 🐛 Troubleshooting

### Erro: "Call to undefined function ..."

```bash
# Limpe o cache do auto-loader
composer dump-autoload
```

### Erro: "No application key has been generated"

```bash
php artisan key:generate
```

### Erro: "SQLSTATE: unable to open database file"

```bash
# Verifique se o arquivo existe
ls -la database/database.sqlite

# Se não existir, crie-o:
touch database/database.sqlite

# Execute migrações novamente
php artisan migrate
```

### Erro: "Class not found"

```bash
# Limpe todos os caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Re-rode composer
composer dump-autoload
```

### O CSS/JS não está atualizando

```bash
# Parar o npm run dev (Ctrl+C)
# Limpar caches de build
rm -rf node_modules/.vite

# Reinstalar e rebuildar
npm install
npm run build

# Ou rode novamente em dev:
npm run dev
```

---

## 📁 Verificar Estrutura de Arquivos

Após completar a instalação, você deve ter:

```bash
# Verifique se estes arquivos existem:
ls -la app/Models/                  # Models (Person.php, etc)
ls -la app/Http/Controllers/        # Controllers
ls -la database/migrations/         # Migrações
ls -la database/seeders/            # Seeders
ls -la resources/views/schedule/    # Views
ls -la database/database.sqlite     # Banco de dados

# Verifique permissões corretas:
ls -la storage/                     # Deve ser escrevível
ls -la bootstrap/cache/             # Deve ser escrevível
```

---

## 🚀 Próximas Etapas

Após a instalação com sucesso:

1. **Explorar a Interface**: Teste todas as funcionalidades
2. **Adicionar Dados**: Adicione mais pessoas/turmas se necessário
3. **Gerar Cronogramas**: Use o botão "Gerar Rodízio"
4. **Customizar**: Modifique cores, textos, regras conforme necessário
5. **Backup**: Faça backup regulares do `database/database.sqlite`

---

## 📞 Dúvidas Comuns

**P: Como faço backup do cronograma?**  
R: O arquivo `database/database.sqlite` contém tudo. Faça cópia do arquivo.

**P: Posso usar PostgreSQL em vez de SQLite?**  
R: Sim! Altere `DB_CONNECTION` no `.env` para `pgsql` e configure as credenciais.

**P: Como faço deploy em produção?**  
R: Build com `npm run build`, copie para servidor, execute `php artisan migrate` lá.

**P: Como reseto o banco completamente?**  
R: Execute `php artisan migrate:refresh --seed`

**P: Onde ficam os logs erros?**  
R: Em `storage/logs/laravel.log`

---

## ✨ Instalação Concluída!

Se chegou aqui sem erros, congratulações! Seu sistema DevMenthors está pronto para uso.

**Endereço**: http://localhost:8000  
**Banco de Dados**: `database/database.sqlite`  
**Pessoas**: 13 profesores + 7 mentorados  
**Turmas**: 4 (2 avançadas + 2 iniciantes)

Bom cronograma! 🎉

---

**Última atualização**: Abril 4, 2026
