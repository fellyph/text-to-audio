# PRD: Text to Audio - Plugin WordPress

## Introdução

O **Text to Audio** é um plugin WordPress que converte o conteúdo de posts e páginas em arquivos de áudio usando o Google Gemini TTS (Text-to-Speech). O objetivo é oferecer aos visitantes do site a opção de consumir o conteúdo em formato de podcast/áudio, além da leitura tradicional.

O plugin utiliza a biblioteca `wordpress/php-ai-client` para integração com a API do Gemini e oferece configurações flexíveis para personalização da voz, idioma, velocidade e tom do áudio gerado.

## Objetivos

- Permitir que administradores/editores convertam posts e páginas em áudio com um clique
- Oferecer configurações de personalização (voz, idioma, velocidade, tom)
- Armazenar os áudios gerados na Biblioteca de Mídia do WordPress
- Disponibilizar um bloco Gutenberg nativo para inserção do player de áudio no conteúdo
- Suportar todos os 24 idiomas disponíveis no Gemini TTS
- Manter interface simples e intuitiva para usuários não técnicos

## User Stories

### US-001: Configurar API Key do Gemini
**Descrição:** Como administrador, quero configurar minha API Key do Gemini para que o plugin possa se comunicar com o serviço de TTS.

**Critérios de Aceitação:**
- [ ] Campo de API Key na página de configurações (Settings > Text to Audio)
- [ ] Campo do tipo password para ocultar a chave
- [ ] Validação de que a chave não está vazia ao tentar gerar áudio
- [ ] Mensagem de erro clara se a chave não estiver configurada
- [ ] API Key armazenada de forma segura nas options do WordPress

---

### US-002: Selecionar voz para geração de áudio
**Descrição:** Como administrador, quero escolher qual voz será usada para gerar os áudios para que eu possa personalizar a experiência do usuário.

**Critérios de Aceitação:**
- [ ] Dropdown com vozes disponíveis: Aoede, Charon, Fenrir, Kore, Puck
- [ ] Valor padrão: Puck
- [ ] Configuração salva e aplicada em todas as conversões subsequentes
- [ ] Preview da voz selecionada (opcional, para versão futura)

---

### US-003: Selecionar idioma para geração de áudio
**Descrição:** Como administrador, quero escolher o idioma do áudio gerado para que corresponda ao idioma do meu conteúdo.

**Critérios de Aceitação:**
- [ ] Dropdown com todos os 24 idiomas suportados pelo Gemini TTS:
  - Árabe (Egito) - ar-EG
  - Inglês (EUA) - en-US
  - Inglês (Índia) - en-IN
  - Francês (França) - fr-FR
  - Alemão (Alemanha) - de-DE
  - Espanhol (EUA) - es-US
  - Hindi (Índia) - hi-IN
  - Indonésio (Indonésia) - id-ID
  - Italiano (Itália) - it-IT
  - Japonês (Japão) - ja-JP
  - Coreano (Coreia) - ko-KR
  - Português (Brasil) - pt-BR
  - Russo (Rússia) - ru-RU
  - Holandês (Países Baixos) - nl-NL
  - Polonês (Polônia) - pl-PL
  - Tailandês (Tailândia) - th-TH
  - Turco (Turquia) - tr-TR
  - Vietnamita (Vietnã) - vi-VN
  - Romeno (Romênia) - ro-RO
  - Ucraniano (Ucrânia) - uk-UA
  - Bengali (Bangladesh) - bn-BD
  - Marathi (Índia) - mr-IN
  - Tamil (Índia) - ta-IN
  - Telugu (Índia) - te-IN
- [ ] Valor padrão: Português (Brasil) - pt-BR
- [ ] Configuração salva nas options do WordPress
- [ ] Idioma aplicado corretamente na chamada à API do Gemini

---

### US-004: Configurar velocidade do áudio
**Descrição:** Como administrador, quero ajustar a velocidade de fala do áudio para adequar ao estilo do meu conteúdo.

**Critérios de Aceitação:**
- [ ] Campo numérico com valor entre 0.5 e 2.0
- [ ] Step de 0.1 para ajuste fino
- [ ] Valor padrão: 1.0 (velocidade normal)
- [ ] Validação de limites (mínimo 0.5, máximo 2.0)
- [ ] Descrição explicativa do campo

---

### US-005: Configurar tom (pitch) do áudio
**Descrição:** Como administrador, quero ajustar o tom da voz para personalizar como o áudio soa.

**Critérios de Aceitação:**
- [ ] Campo numérico com valor entre -20.0 e 20.0
- [ ] Step de 0.1 para ajuste fino
- [ ] Valor padrão: 0.0 (tom neutro)
- [ ] Validação de limites
- [ ] Descrição explicativa do campo

---

### US-006: Gerar áudio a partir de um post
**Descrição:** Como editor, quero converter o conteúdo de um post em áudio para disponibilizar aos visitantes.

**Critérios de Aceitação:**
- [ ] Meta box "Text to Audio" visível na sidebar do editor de posts
- [ ] Botão "Convert to Audio" claramente visível
- [ ] Indicador de loading durante o processamento
- [ ] Mensagem de sucesso após conversão concluída
- [ ] Mensagem de erro clara em caso de falha
- [ ] Player de áudio exibido no meta box após geração bem-sucedida
- [ ] Conteúdo do post limpo de shortcodes e HTML antes da conversão

---

### US-007: Gerar áudio a partir de uma página
**Descrição:** Como editor, quero converter o conteúdo de uma página em áudio para disponibilizar aos visitantes.

**Critérios de Aceitação:**
- [ ] Meta box "Text to Audio" visível na sidebar do editor de páginas
- [ ] Mesma funcionalidade disponível para posts (US-006)
- [ ] Registro do meta box para post type "page"

---

### US-008: Regenerar áudio existente
**Descrição:** Como editor, quero poder regenerar o áudio de um post/página para atualizar após mudanças no conteúdo.

**Critérios de Aceitação:**
- [ ] Botão muda para "Regenerate Audio" quando já existe áudio
- [ ] Ao regenerar, o áudio anterior é substituído
- [ ] O attachment antigo pode ser mantido ou removido (decisão de implementação)
- [ ] Confirmação visual de que a regeneração foi concluída
- [ ] Novo áudio vinculado ao post via meta `_fellyph_audio_id`

---

### US-009: Salvar áudio na Biblioteca de Mídia
**Descrição:** Como sistema, o áudio gerado deve ser salvo na Biblioteca de Mídia do WordPress para gerenciamento centralizado.

**Critérios de Aceitação:**
- [ ] Arquivo MP3 salvo no diretório de uploads do WordPress
- [ ] Nome do arquivo segue padrão: `post-audio-{post_id}-{timestamp}.mp3`
- [ ] Attachment criado e vinculado ao post pai
- [ ] Metadados do attachment gerados corretamente
- [ ] Áudio acessível via Biblioteca de Mídia

---

### US-010: Criar bloco Gutenberg para player de áudio
**Descrição:** Como editor, quero inserir um player de áudio no conteúdo usando o editor de blocos para ter controle sobre onde o player aparece.

**Critérios de Aceitação:**
- [ ] Bloco "Post Audio Player" disponível no inserter do Gutenberg
- [ ] Bloco exibe player HTML5 nativo de áudio
- [ ] Bloco detecta automaticamente o áudio vinculado ao post atual
- [ ] Mensagem informativa se não houver áudio gerado para o post
- [ ] Bloco renderiza corretamente no frontend
- [ ] Estilização básica responsiva para o player
- [ ] Bloco categorizado em "Media" ou categoria custom "Text to Audio"

---

### US-011: Exibir player de áudio no frontend
**Descrição:** Como visitante, quero poder ouvir o conteúdo do post em formato de áudio.

**Critérios de Aceitação:**
- [ ] Player HTML5 funcional com controles nativos (play, pause, volume, progress)
- [ ] Player responsivo (adapta à largura do container)
- [ ] Player só aparece se houver áudio vinculado ao post
- [ ] Carregamento otimizado (não bloqueia renderização da página)

---

### US-012: Remover funcionalidade de shortcode (deprecar)
**Descrição:** Como desenvolvedor, quero descontinuar o shortcode `[post_audio]` em favor do bloco Gutenberg.

**Critérios de Aceitação:**
- [ ] Shortcode continua funcionando para retrocompatibilidade
- [ ] Documentação atualizada recomendando uso do bloco
- [ ] Aviso de depreciação em versão futura (opcional)

---

### US-013: Processar posts longos com chunking
**Descrição:** Como editor, quero que posts longos sejam convertidos em áudio corretamente, mesmo que excedam o limite da API.

**Critérios de Aceitação:**
- [ ] Sistema detecta quando o texto excede 4.000 bytes
- [ ] Texto é dividido em chunks respeitando limites de parágrafos/sentenças
- [ ] Cada chunk é enviado separadamente à API do Gemini
- [ ] Áudios resultantes são concatenados em um único arquivo MP3
- [ ] Indicador de progresso mostra qual chunk está sendo processado
- [ ] Mensagem informativa se o post for muito longo (estimativa de tempo)
- [ ] Fallback gracioso se a concatenação falhar

---

### US-014: Exibir estimativa de tamanho do áudio
**Descrição:** Como editor, quero ver uma estimativa do tamanho/duração do áudio antes de gerar para saber se o post precisa de chunking.

**Critérios de Aceitação:**
- [ ] Meta box exibe contagem de caracteres/bytes do conteúdo
- [ ] Indicador visual se o post excede o limite de 4.000 bytes
- [ ] Estimativa aproximada de duração do áudio (baseada em ~150 palavras/minuto)
- [ ] Aviso se o áudio estimado exceder 10 minutos

---

### US-015: Sanitizar conteúdo antes da conversão
**Descrição:** Como sistema, o conteúdo do post deve ser limpo e sanitizado antes de ser enviado à API do Gemini para garantir que apenas texto puro seja convertido em áudio.

**Critérios de Aceitação:**
- [ ] Remover todas as tags HTML do conteúdo (`wp_strip_all_tags()`)
- [ ] Remover todos os shortcodes do conteúdo (`strip_shortcodes()`)
- [ ] Remover blocos Gutenberg que não são texto (imagens, vídeos, embeds)
- [ ] Preservar quebras de parágrafo como pausas naturais no áudio
- [ ] Converter entidades HTML para caracteres (`html_entity_decode()`)
- [ ] Remover espaços em branco excessivos (múltiplas linhas vazias)
- [ ] Remover URLs e links (manter apenas o texto do link)
- [ ] Remover código fonte/snippets de programação (blocos `<code>`, `<pre>`)
- [ ] Tratar listas (ul/ol) convertendo para texto sequencial
- [ ] Remover comentários HTML
- [ ] Normalizar caracteres especiais e emojis

---

## Requisitos Funcionais

- **FR-1:** O plugin deve adicionar uma página de configurações em Settings > Text to Audio
- **FR-2:** A página de configurações deve permitir inserir e salvar a API Key do Gemini
- **FR-3:** A página de configurações deve permitir selecionar a voz (Aoede, Charon, Fenrir, Kore, Puck)
- **FR-4:** A página de configurações deve permitir selecionar o idioma dentre os 24 suportados
- **FR-5:** A página de configurações deve permitir ajustar velocidade (0.5 a 2.0) e tom (-20.0 a 20.0)
- **FR-6:** O plugin deve adicionar um meta box na sidebar do editor para posts e páginas
- **FR-7:** O meta box deve exibir botão para gerar/regenerar áudio
- **FR-8:** Ao clicar no botão, uma requisição AJAX deve ser enviada para conversão
- **FR-9:** O conteúdo do post deve passar pelo processo de sanitização antes de enviar à API (ver Sanitização de Conteúdo)
- **FR-10:** O plugin deve usar a biblioteca `wordpress/php-ai-client` para comunicação com Gemini
- **FR-11:** O áudio retornado deve ser salvo como arquivo MP3 no diretório de uploads
- **FR-12:** Um attachment WordPress deve ser criado para o arquivo de áudio
- **FR-13:** O ID do attachment deve ser salvo como post meta (`_fellyph_audio_id`)
- **FR-14:** O meta box deve exibir o player de áudio após geração bem-sucedida
- **FR-15:** O plugin deve registrar um bloco Gutenberg "Post Audio Player"
- **FR-16:** O bloco deve renderizar um player HTML5 com o áudio vinculado ao post
- **FR-17:** O bloco deve exibir mensagem apropriada se não houver áudio disponível
- **FR-18:** O plugin deve detectar quando o texto excede 4.000 bytes e aplicar chunking
- **FR-19:** O plugin deve dividir textos longos em parágrafos/sentenças para chunking
- **FR-20:** O plugin deve concatenar múltiplos áudios em um único arquivo MP3
- **FR-21:** O meta box deve exibir contagem de bytes e estimativa de duração do áudio
- **FR-22:** O plugin deve limitar a duração total do áudio a ~10 minutos (limite da API: 655 segundos)

## Não-Objetivos (Fora do Escopo)

- Geração automática de áudio ao publicar/atualizar post
- Transcrição de áudio para texto (funcionalidade inversa)
- Suporte a múltiplos áudios por post
- Editor de áudio integrado (cortar, editar)
- Integração com outros provedores de TTS além do Gemini
- Sistema de filas para processamento em lote
- CDN ou armazenamento externo para arquivos de áudio
- Analytics de reprodução de áudio
- Suporte a custom post types além de posts e páginas
- Player de áudio customizado (usa player nativo HTML5)

## Considerações de Design

### Interface Admin (Configurações)
- Seguir padrões de UI do WordPress Admin
- Campos agrupados logicamente (API, Voz/Idioma, Ajustes de áudio)
- Descrições claras para cada campo
- Feedback visual ao salvar configurações

### Interface Admin (Editor)
- Meta box compacto na sidebar
- Estados visuais claros: sem áudio, gerando, com áudio
- Botão primário para ação principal
- Player inline para preview

### Interface Frontend (Bloco)
- Design minimalista
- Player responsivo
- Acessível (controles de teclado, labels)

## Considerações Técnicas

### Limites da API Gemini TTS

| Parâmetro | Limite | Observação |
|-----------|--------|------------|
| **Campo de texto** | 4.000 bytes | Limite por requisição |
| **Campo de prompt** | 4.000 bytes | Instruções de estilo/tom |
| **Total (texto + prompt)** | 8.000 bytes | Combinado |
| **Duração do áudio** | ~655 segundos (~11 min) | Áudio truncado se exceder |

**Nota:** Usuários reportam cutoff prático de ~5 minutos na versão preview. Monitorar atualizações da API.

**Estratégia de Chunking:**
1. Calcular tamanho do texto em bytes (UTF-8)
2. Se > 4.000 bytes, dividir em chunks por parágrafos
3. Cada chunk deve terminar em sentença completa (., !, ?)
4. Processar chunks sequencialmente
5. Concatenar áudios usando biblioteca PHP (ex: `ffmpeg` ou manipulação binária)

### Sanitização de Conteúdo

O conteúdo do post deve ser processado para remover elementos não-textuais antes de enviar à API do Gemini. O objetivo é garantir que apenas texto legível seja convertido em áudio.

**Pipeline de Sanitização:**

```php
// 1. Remover shortcodes
$content = strip_shortcodes( $content );

// 2. Processar blocos Gutenberg (remover blocos não-textuais)
$content = excerpt_remove_blocks( $content );

// 3. Remover tags HTML preservando quebras de linha
$content = wp_strip_all_tags( $content, true );

// 4. Decodificar entidades HTML
$content = html_entity_decode( $content, ENT_QUOTES, 'UTF-8' );

// 5. Remover URLs
$content = preg_replace( '/https?:\/\/[^\s]+/', '', $content );

// 6. Normalizar espaços em branco
$content = preg_replace( '/\n{3,}/', "\n\n", $content );
$content = preg_replace( '/[ \t]+/', ' ', $content );

// 7. Trim final
$content = trim( $content );
```

**Elementos a Remover:**
- Tags HTML (`<div>`, `<span>`, `<a>`, etc.)
- Shortcodes (`[gallery]`, `[embed]`, etc.)
- Blocos Gutenberg não-textuais (imagem, vídeo, áudio, embed, código)
- URLs e links
- Comentários HTML (`<!-- -->`)
- Código fonte (conteúdo de `<pre>`, `<code>`)
- Espaços em branco excessivos

**Elementos a Preservar:**
- Texto de parágrafos
- Texto de headings (h1-h6)
- Texto de listas (convertido para sequência)
- Quebras de parágrafo (como pausas naturais)
- Texto de blockquotes
- Texto de tabelas (linha por linha)

**Classe Responsável:** `ContentSanitizer.php` (novo)

### Dependências
- WordPress 6.9+
- PHP 7.2.24+
- Biblioteca `wordpress/php-ai-client` (via Composer)
- API Key válida do Google Gemini
- FFmpeg (opcional, para concatenação de áudios longos)

### Estrutura de Arquivos
```
text-to-audio/
├── text-to-audio.php          # Entry point do plugin
├── src/
│   ├── Settings.php           # Página de configurações
│   ├── TTSProvider.php        # Integração com Gemini API
│   ├── Media.php              # Gerenciamento de arquivos de áudio
│   ├── UI.php                 # Meta box no editor
│   ├── Shortcode.php          # Shortcode (legacy)
│   ├── Block.php              # Bloco Gutenberg (novo)
│   ├── Chunker.php            # Divisão e concatenação de textos longos (novo)
│   └── ContentSanitizer.php   # Limpeza e sanitização do conteúdo (novo)
├── assets/
│   ├── js/
│   │   ├── admin.js           # Scripts do admin
│   │   └── block.js           # Script do bloco Gutenberg
│   └── css/
│       ├── admin.css          # Estilos do admin
│       └── frontend.css       # Estilos do frontend
├── blocks/
│   └── post-audio-player/
│       ├── block.json         # Metadados do bloco
│       ├── edit.js            # Componente de edição
│       ├── save.js            # Componente de salvamento
│       └── style.css          # Estilos do bloco
├── vendor/                    # Dependências Composer
├── composer.json
└── blueprint.json             # Config WordPress Playground
```

### Armazenamento de Dados
- Options: `fellyph_text_to_audio_settings` (array com todas configurações)
- Post Meta: `_fellyph_audio_id` (ID do attachment de áudio)

### Segurança
- Nonce verification em todas requisições AJAX
- Capability check (`edit_post`) antes de permitir conversão
- Sanitização de inputs nas configurações
- API Key armazenada nas options (considerar encryption para produção)

### Performance
- Conversão via AJAX (não bloqueia UI)
- Lazy loading do áudio no frontend
- Cache de configurações via `get_option()`

## Métricas de Sucesso

- Editor consegue gerar áudio em menos de 30 segundos para posts de tamanho médio
- Áudio gerado reproduz corretamente em todos os browsers modernos
- Player do bloco Gutenberg funciona consistentemente no editor e frontend
- Taxa de erro na geração menor que 5% (excluindo erros de API)
- Interface de configurações intuitiva (usuário consegue configurar sem documentação)

## Questões em Aberto

1. **Formato de áudio:** MP3 é o único formato ou devemos suportar outros (WAV, OGG)?
2. **Cleanup de áudios antigos:** Ao regenerar, devemos deletar o attachment anterior ou mantê-lo?
3. **Modelo Gemini:** Usar `gemini-1.5-flash` ou versão mais recente como `gemini-2.5-flash`?
4. **Internacionalização:** O plugin deve ter traduções para outros idiomas além do inglês?
5. **Tratamento de conteúdo especial:** Como lidar com tabelas, código, listas longas no conteúdo?
6. **Dependência FFmpeg:** Exigir FFmpeg no servidor ou usar alternativa PHP pura para concatenação?

## Referências

- [Gemini TTS Documentation](https://docs.cloud.google.com/text-to-speech/docs/gemini-tts)
- [Gemini API Speech Generation](https://ai.google.dev/gemini-api/docs/speech-generation)
- [Cloud TTS Quotas & Limits](https://docs.cloud.google.com/text-to-speech/quotas)
